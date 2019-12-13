<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\WooCommerceOrderQuery;


class IncomeReportManager
{

    use WooCommerceOrderQuery;

    public $wpdb;

    private $start_date;
    private $end_date;

    private $products = [];

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->init_dates();

    }

    public function get_product($id)
    {
        if(array_key_exists($id, $this->products))
        {
            return $this->products[$id];
        }

        $product = $this->get_product_info($id);

        if($product != false)
        {
            $this->products[$id] = $product;

            return $product;
        }

        return false;

    }

    private function get_product_info($id)
    {
        $result = $this->wpdb->get_results($this->getProductQuery($id));

        if(count($result) == 0)
            return false;

        return [
            'name' => $result[0]->post_title,
            'cost_price' => $result[0]->cost_price
        ];

    }

    private function getProductQuery($id)
    {
        $sql = "SELECT
                    `id`, `post_title`,
                    MAX(CASE WHEN (wp_postmeta.meta_key = '_gt_cost_price')
                        THEN wp_postmeta.meta_value ELSE NULL END) AS cost_price
                FROM `wp_posts`
                LEFT JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                WHERE `ID` = '$id'
        ";

        return $sql;
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
            $this->end_date = date("Y-m-d 23:59:59");
        }

    }

    public function get_data()
    {
        $data = [];

        // $results = $this->get_order_report_data($this->get_args());
        $results = $this->get_order_data($this->get_sql());

        foreach($results as $result)
        {
            $date = date("d/M/Y", strtotime($result->post_date));
            if(!array_key_exists($date, $data))
            {
                $data[$date] = [];
            }

            //now get the order
            $order = $result->order_id;
            if(!array_key_exists($order, $data[$date]))
            {
                $data[$date][$order] = [];
            }

            //now get the product details.
            $product_info = $this->get_product_info($result->product_id);

            $profit = ($result->item_total) - ($result->cost_price * $result->quantity);

            //if the product name is empty and cost is null,
            //then do not add the product.
            if($result->quantity == null || $result->item_total == null)
            {
                continue;
            }

            $productDetails = [
                "order_status" => $result->order_status,
                'id' => $result->product_id,
                'name' => $product_info['name'],
                'cost_price' => $result->cost_price,
                'selling_price' => $result->item_total / $result->quantity,
                'quantity' => $result->quantity,
                'profit' => $profit
            ];

            //push it into the order
            array_push($data[$date][$order], $productDetails);

        }

        return $data;

        //now lets process the order
    }

    private function get_sql()
    {
        return $sql = "SELECT
                            order_item_meta__product_id.meta_value AS product_id,
                            order_item_meta__qty.meta_value AS quantity,
                            order_item_meta__line_subtotal.meta_value AS item_total,
                            order_item_meta__gt_cost_price.meta_value AS cost_price,
                            posts.post_date AS post_date,
                            posts.id AS order_id,
                            posts.post_status as order_status
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
                            posts.post_type = 'shop_order'

                            AND posts.post_status NOT IN ('auto-draft', 'trash')

                            AND
                                posts.post_date >= '$this->start_date'
                            AND
                                posts.post_date <= '$this->end_date'
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
