<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\ReportsTrait;
use App\Traits\WooCommerceOrderQuery;


class ReportsManager
{
    use ReportsTrait;
    use WooCommerceOrderQuery;

    public $wpdb;
    public $woocommerce;

    private $start_date;
    private $end_date;

    private $post_date_field;

    private $order_items = [];

    private $results;

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;
        global $woocommerce;

        $this->wpdb = $wpdb;
        $this->woocommerce = $woocommerce;

        $this->init_dates();
        $this->init_post_date_field();
    }

    private function init_dates()
    {
        if(isset($_GET['start_date']))
        {
            $this->start_date = $_GET['start_date'];
        }
        else {
            $this->start_date = date("Y-m-d");
        }

        if(isset($_GET['end_date']))
        {
            $this->end_date = $_GET['end_date'] . ' 23:59:59';
        }
        else {
            $this->end_date = date("Y-m-d H:i:s");
        }

    }

    private function init_post_date_field()
    {
        if(isset($_GET['order_type']))
        {
            if($_GET['order_type'] == '-1')
            {
                //the date the post was modified
                $this->post_date_field = 'post_modified';
            }
            else {
                $this->post_date_field = "post_date";
            }
        }
        else {

            //by default get the post modified date
            $this->post_date_field = "post_modified";
        }
    }

    public function getOrderStats()
    {
        $query = $this->getStatsQuery();
        $shippingQuery = $this->getStatsShippingQuery();

        $results = $this->wpdb->get_results($query);
        $shippingStats = $this->wpdb->get_results($shippingQuery);

        $response = [
            'total' => count($results),
            'total_cost' => 0,

            'completed' => [
                    'count' => 0,
                    'total' => 0
                ],

            'pending' => [
                'count' => 0,
                'total' => 0
            ],
            'cancelled' =>[
                'count' => 0,
                'total' => 0
            ]
        ];

        $withoutShipping = [
            'total' => count($shippingStats),
            'total_cost' => 0,

            'completed' => [
                    'count' => 0,
                    'total' => 0
                ],

            'pending' => [
                'count' => 0,
                'total' => 0
            ],
            'cancelled' =>[
                'count' => 0,
                'total' => 0
            ]
        ];

        $i = 0;
        foreach($results as $post)
        {
            $shipping = $shippingStats[$i];

            $response['total_cost'] += $post->order_total;
            $withoutShipping['total_cost'] += ($post->order_total - $shipping->shipping);

            if($post->post_status == OrderStatus::COMPLETED)
            {
                $response['completed']['count'] += 1;
                $response['completed']['total'] += $post->order_total;

                $withoutShipping['completed']['total'] += ($post->order_total - $shipping->shipping);
            }

            if($post->post_status == OrderStatus::ON_HOLD || $post->post_status == OrderStatus::PROCESSING)
            {
                $response['pending']['count'] += 1;
                $response['pending']['total'] += $post->order_total;

                $withoutShipping['pending']['total'] += ($post->order_total - $shipping->shipping);
            }

            if($post->post_status == OrderStatus::FAILED
                || $post->post_status == OrderStatus::CANCELLED
                || $post->post_status == OrderStatus::TRASHED)
            {
                $response['cancelled']['count'] += 1;
                $response['cancelled']['total'] += $post->order_total;

                $withoutShipping['cancelled']['total'] += ($post->order_total - $shipping->shipping);
            }

            ++$i;
        }



        return [
            'with_shipping' => $response,
            'without_shipping' => $withoutShipping
        ];
    }

    public function userRegionReport()
    {
        $data = $this->getUsersAndRegionOrders();

        $report = [
            'sellers' => [],
            'regions' => [],
            'towns' => [],
            'payment_methods' => []
        ];

        //initialise the users and the regions
        $regions = get_terms("zone_region", ['hide_empty' => false ]);
        $sellers = get_terms("seller", ['hide_empty' => false ]);
        $towns = get_terms("zone_town", ['hide_empty' => false ]);
        $payment_methods = $this->get_payment_methods();

        foreach ($sellers as $seller) {
            $report['sellers'][$seller->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($regions as $region) {
            $report['regions'][$region->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($towns as $town) {
            $report['towns'][$town->term_id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        foreach ($payment_methods as $method) {
            $report['payment_methods'][$method->id] = [
                'count' => 0,
                'total' => 0
            ];
        }

        //for now create a non existent key
        $report['towns']['-1'] = [
            'count' => 0,
            'total' => 0
        ];

        //also for sellers
        //for now create a non existent key
        $report['sellers']['-1'] = [
            'count' => 0,
            'total' => 0
        ];

        //for now create a non existent key
        $report['regions']['-1'] = [
            'count' => 0,
            'total' => 0
        ];


        //now loop through the results and save the data
        foreach ($data as $key ) {

            $p_method = $key->payment_method;

            $order_amount = $key->total - $key->shipping;

            if($key->order_data != null)
            {
                $order_data = unserialize($key->order_data);

                $region = $order_data['gt_region'];
                $seller = $order_data['gt_seller'];
                $town = isset($order_data['gt_town']) ? $order_data['gt_town'] : '-1';

                $report['sellers'][$seller]['count'] += 1;
                $report['sellers'][$seller]['total'] += $order_amount;

                $report['regions'][$region]['count'] += 1;
                $report['regions'][$region]['total'] += $order_amount;

                $report['towns'][$town]['count'] += 1;
                $report['towns'][$town]['total'] += $order_amount;

            }

            //count the shipping method
            if(array_key_exists($p_method, $report['payment_methods']))
            {
                $report['payment_methods'][$p_method]['count'] += 1;
                $report['payment_methods'][$p_method]['total'] += $order_amount;
            }

        }

        return $report;
    }

    private function getStatsQuery()
    {
        $sql = "SELECT {$this->wpdb->posts}.*,
                    wp_postmeta.meta_value as order_total
                from {$this->wpdb->posts}
                INNER JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                    AND wp_postmeta.meta_key = '_order_total'
                where
                    `$this->post_date_field` >= '$this->start_date'
                    AND `post_status` NOT IN ('auto-draft', 'trash')
                    AND `$this->post_date_field` <= '$this->end_date'
                    and `post_type` = 'shop_order'
                ";

        return $sql;
    }

    private function getStatsShippingQuery()
    {
        $sql = "SELECT {$this->wpdb->posts}.*,
                    wp_postmeta.meta_value as shipping
                from {$this->wpdb->posts}
                INNER JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                    AND wp_postmeta.meta_key = '_order_shipping'
                where
                    `$this->post_date_field` >= '$this->start_date'
                    AND `post_status` NOT IN ('auto-draft', 'trash')
                    AND `$this->post_date_field` <= '$this->end_date'
                    and `post_type` = 'shop_order'
                ";

        return $sql;
    }

    public function getCategoryData($category_id)
    {
        $term_ids    = get_term_children( $category_id, 'product_cat' );
		$term_ids[]  = $category_id;
		$product_ids = get_objects_in_term( $term_ids, 'product_cat' );

        //if the data has not been gotten
        //then fetch it and save it.
        //to reduce the number of queries.
        if($this->items_gotten == false)
        {
            $results = $this->get_order_data($this->get_sql());

            $this->results = $results;
            $this->items_gotten = true;
        }

        $quantityCount = 0;
        $totalAmount = 0;

        foreach($this->results as $result)
        {
            //check that the product is in the product ids of this category
            if(in_array($result->product_id, $product_ids))
            {

                $quantityCount += $result->quantity;
                $totalAmount += $result->item_total;
            }


        }


        return [
            'order_count' => $quantityCount,
            'order_total' => $totalAmount
        ];
    }

    private function get_sql()
    {
        return $sql = "SELECT
                            order_item_meta__product_id.meta_value AS product_id,
                            order_item_meta__qty.meta_value AS quantity,
                            order_item_meta__line_subtotal.meta_value AS item_total,
                            order_item_meta__gt_cost_price.meta_value AS cost_price,
                            posts.post_date AS post_date,
                            posts.post_status AS post_status,
                            posts.id AS order_id
                        FROM
                            wp_posts AS posts
                        INNER JOIN wp_woocommerce_order_items AS order_items
                        ON
                            (
                                posts.ID = order_items.order_id
                            )
                        LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__product_id
                        ON
                            (
                                order_items.order_item_id = order_item_meta__product_id.order_item_id
                            ) AND(
                                order_item_meta__product_id.meta_key = '_product_id'
                            )
                        LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__qty
                        ON
                            (
                                order_items.order_item_id = order_item_meta__qty.order_item_id
                            ) AND(
                                order_item_meta__qty.meta_key = '_qty'
                            )
                        LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__line_subtotal
                        ON
                            (
                                order_items.order_item_id = order_item_meta__line_subtotal.order_item_id
                            ) AND(
                                order_item_meta__line_subtotal.meta_key = '_line_subtotal'
                            )
                        LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__gt_cost_price
                        ON
                            (
                                order_items.order_item_id = order_item_meta__gt_cost_price.order_item_id
                            ) AND(
                                order_item_meta__gt_cost_price.meta_key = '_gt_cost_price'
                            )
                        WHERE
                            posts.post_type IN('shop_order') AND posts.post_status IN(
                                'wc-completed',
                                'wc-processing',
                                'wc-on-hold',
                                'wc-pending'
                            )
                            AND
                                posts.$this->post_date_field >= '$this->start_date'
                            AND
                                posts.$this->post_date_field <= '$this->end_date'
                            AND
                                order_items.order_item_type <> 'shipping'
                        GROUP BY
                            ID,
                            product_id,
                            post_date
                        ";
    }

}

?>
