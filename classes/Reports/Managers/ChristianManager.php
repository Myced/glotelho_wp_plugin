<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\WooCommerceOrderQuery;


class ChristianManager
{

    use WooCommerceOrderQuery;

    public $wpdb;

    private $start_date;
    private $end_date;

    private $post_date_field;

    private $order_statuses;

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->init_dates();
        $this->init_post_date_field();
        $this->set_order_statuses();
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
            $this->post_date_field = "post_date";
        }
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

            $full_name = $result->first_name . ' ' . $result->last_name;

            $order_number = $result->order_number;

            if(empty($order_number))
            {
                $order_number = $result->order_id;
            }

            $productDetails = [
                'date' => $date,
                'order_number' => $order_number,
                'full_name' => $full_name,
                'town' => $result->town,
                "order_status" => $result->order_status,
                'product_name' => $result->item_name,
                'quantity' => $result->quantity,
                'comment' => $result->order_note,
                'selling_price' => $result->selling_price
            ];

            //push it into the order
            array_push($data, $productDetails);

        }

        return $data;

        //now lets process the order
    }

    private function get_sql()
    {
        return $sql = "SELECT
                            order_item_meta__product_id.meta_value AS product_id,
                            order_item_meta__qty.meta_value AS quantity,
                            order_item_meta__line_total.meta_value as selling_price,
                            order_items.order_item_name as item_name,
                            posts.post_date AS post_date,
                            posts.id AS order_id,
                            posts.post_status as order_status,
                            posts.post_excerpt as order_note,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_order_number')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS order_number,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_first_name')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS first_name,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_last_name')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS last_name,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_city')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS town


                        FROM
                            wp_posts AS posts
                        INNER JOIN `wp_postmeta`
                            ON posts.ID = wp_postmeta.post_id
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

                        LEFT JOIN wp_woocommerce_order_itemmeta AS order_item_meta__line_total
                        ON
                            (
                                order_items.order_item_id = order_item_meta__line_total.order_item_id
                            ) AND(
                                order_item_meta__line_total.meta_key = '_line_total'
                            )

                        WHERE
                            posts.post_type = 'shop_order'

                            AND posts.post_status  IN $this->order_statuses

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

    private function set_order_statuses()
    {
        if(isset($_GET['statuses']))
        {
            $statuses = $_GET['statuses'];
            $in = '(';

            //put in all the statuses here.
            $i = 0;
            foreach($statuses as $status)
            {
                $in .= " '$status'";

                if($i < count($statuses) -1 )
                {
                    $in .= ', ';
                }

                $i++;
            }

            $in .= ')';

            $this->order_statuses = $in;
        }
    }

}

?>
