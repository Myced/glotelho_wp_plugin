<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\WooCommerceOrderQuery;

class ClientAchatManager
{
    use WooCommerceOrderQuery;

    public $wpdb;

    private $start_date;
    private $end_date;

    private $post_date_field;

    private $order_statuses;

    private $products = [];

    //initialise the towns
    private $towns;
    private $sellers;

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->init_dates();
        $this->init_post_date_field();
        $this->set_order_statuses();
        // $this->init_sellers();
        // $this->init_towns();

        $this->set_product_ids();

    }

    private function init_post_date_field()
    {
        //we will uniquely take items ordered within
        //the selected period
        $this->post_date_field = "post_date";
    }

    private function set_product_ids()
    {
        if(isset($_GET['categories']))
        {
            $categories = $_GET['categories'];

            $products = [];

            //for each category take the product ids and save
            foreach($categories as $category)
            {
                $term_ids    = get_term_children( $category, 'product_cat' );
                $term_ids[]  = $category;
                $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

                $products = array_merge($products, $product_ids);
            }

            //make the array unique
            $this->products = array_unique($products);
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

            //if the product is not in the list of
            // eligible products then continue
            if(!in_array($result->product_id, $this->products))
            {
                continue;
            }

            $date = date("d/M/Y", strtotime($result->post_date));

            $full_name = $result->first_name . ' ' . $result->last_name;

            $productDetails = [
                'date' => $result->post_date,
                'order_no' => $result->order_id,
                'order_status' => $result->order_status,
                'client_name' => $full_name,
                'client_tel' => $result->client_tel,
                "order_status" => $result->order_status,
                'product_name' => $result->product_name,

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
                            posts.post_date AS post_date,
                            posts.id AS order_id,
                            posts.post_status as order_status,
                            order_items.order_item_name as product_name,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_first_name')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS first_name,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_last_name')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS last_name,
                            MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_phone')
                                THEN wp_postmeta.meta_value ELSE NULL END) AS client_tel
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
                            posts.post_type
                            IN
                                ('shop_order', 'shop_order_refund')
                            AND posts.post_status
                            IN
                                $this->order_statuses
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
