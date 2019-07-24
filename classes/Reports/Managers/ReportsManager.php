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

    private $order_items = [];

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;
        global $woocommerce;

        $this->wpdb = $wpdb;
        $this->woocommerce = $woocommerce;

        $this->init_dates();
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

            if($post->post_status == OrderStatus::FAILED || $post->post_status == OrderStatus::CANCELLED)
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
                    `post_date` >= '$this->start_date'
                    AND `post_status` <> 'auto-draft'
                    AND `post_date` <= '$this->end_date'
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
                    `post_date` >= '$this->start_date'
                    AND `post_status` <> 'auto-draft'
                    AND `post_date` <= '$this->end_date'
                    and `post_type` = 'shop_order'
                ";

        return $sql;
    }

    public function getCategoryData($category_id)
    {
        $term_ids    = get_term_children( $category_id, 'product_cat' );
		$term_ids[]  = $category_id;
		$product_ids = get_objects_in_term( $term_ids, 'product_cat' );

        if($this->items_gotten == false)
        {
            $order_items = $this->get_order_report_data(
                array(
                    'data'         => array(
                        '_product_id' => array(
                            'type'            => 'order_item_meta',
                            'order_item_type' => 'line_item',
                            'function'        => '',
                            'name'            => 'product_id',
                        ),
                        '_line_total' => array(
                            'type'            => 'order_item_meta',
                            'order_item_type' => 'line_item',
                            'function'        => 'SUM',
                            'name'            => 'order_item_amount',
                        ),
                        'post_date'   => array(
                            'type'     => 'post_data',
                            'function' => '',
                            'name'     => 'post_date',
                        ),
                    ),
                    'group_by'     => 'ID, product_id, post_date',
                    'query_type'   => 'get_results',
                    'filter_range' => true,
                )
            );

            foreach($order_items as $order_item)
            {
                if(! array_key_exists($order_item->product_id, $this->order_items))
                {
                    $this->order_items[$order_item->product_id] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }

                //now add the items
                $this->order_items[$order_item->product_id]['count'] += 1;
                $this->order_items[$order_item->product_id]['total'] += $order_item->order_item_amount;
            }

            $this->items_gotten = true;
        }



        $total = 0;
        $count = 0;

        //now filter only the products in the required category
        foreach ( $product_ids as $id ) {
            // var_dump($id); die();

            if ( isset( $this->order_items[ $id ] ) ) {
                $count += $this->order_items[ $id ]['count'];
                $total += $this->order_items[ $id ]['total'];
            }
        }

        return [
            'order_count' => $count,
            'order_total' => $total
        ];
    }



}

?>
