<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;


class OperationsReportManager
{


    public $wpdb;

    private $start_date;
    private $end_date;

    private $post_date_field;

    private $towns = [];

    private $products = [];

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->init_dates();
        $this->init_post_date_field();
        $this->init_towns();

    }

    private function init_towns()
    {
        $towns = get_terms("zone_town", ['hide_empty' => false ]);

        foreach($towns as $town)
        {
            $this->towns[$town->term_id] = $town->name;
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

    private function getTown($id)
    {
        if(array_key_exists($id, $this->towns))
        {
            return $this->towns[$id];
        }

        return false;
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

    public function get_data($category, $seller)
    {
        $data = [];

        //get the products in this category
        //do all the processing
        //get all the products in this category
        $term_ids    = get_term_children( $category, 'product_cat' );
        $term_ids[]  = $category;
        $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

        $results = $this->get_result($this->get_sql());

        foreach($results as $result)
        {
            if($result->order_data == null)
            {
                continue;
            }

            $order_data = unserialize($result->order_data);

            $town = $this->getTown($order_data['gt_town']);
            $gt_seller = $order_data['gt_seller'];

            if($gt_seller != $seller)
            {
                continue;
            }

            //check that the product is in the product ids of this category
            if(in_array($result->product_id, $product_ids))
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


                $productDetails = [
                    'order_status' => $result->post_status,
                    'id' => $result->product_id,
                    'name' => $product_info['name'],
                    'cost_price' => $result->cost_price,
                    'selling_price' => $result->item_total / $result->quantity,
                    'quantity' => $result->quantity,
                    'profit' => $profit,
                    'town' => $town
                ];

                //push it into the order
                array_push($data[$date][$order], $productDetails);
            }


        }

        return $data;

        //now lets process the order
    }

    private function get_result($sql)
    {
        return $this->wpdb->get_results($sql);
    }

    private function get_sql()
    {
        $sql = "SELECT
                    order_item_meta__product_id.meta_value AS product_id,
                    order_item_meta__qty.meta_value AS quantity,
                    order_item_meta__gt_cost_price.meta_value AS cost_price,
                    order_item_meta__line_subtotal.meta_value AS item_total,
                    posts.post_date AS post_date,
                    posts.post_status AS post_status,
                    posts.id AS order_id,
                    MAX(CASE WHEN (wp_postmeta.meta_key = '_gt_order_data')
                        THEN wp_postmeta.meta_value ELSE NULL END) AS order_data
                FROM
                    wp_posts AS posts
                LEFT JOIN `wp_postmeta`
                    ON posts.ID = wp_postmeta.post_id
                INNER JOIN wp_woocommerce_order_items AS order_items
                ON
                    (
                        posts.ID = order_items.order_id
                    )
                INNER JOIN wp_woocommerce_order_itemmeta AS order_item_meta__product_id
                ON
                    (
                        order_items.order_item_id = order_item_meta__product_id.order_item_id
                    ) AND(
                        order_item_meta__product_id.meta_key = '_product_id'
                    )
                INNER JOIN wp_woocommerce_order_itemmeta AS order_item_meta__qty
                ON
                    (
                        order_items.order_item_id = order_item_meta__qty.order_item_id
                    ) AND(
                        order_item_meta__qty.meta_key = '_qty'
                    )
                INNER JOIN wp_woocommerce_order_itemmeta AS order_item_meta__line_subtotal
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
                    wp_posts.post_type = 'shop_order'

                    AND wp_posts.post_status NOT IN ('auto-draft', 'trash')

                    AND
                        posts.$this->post_date_field >= '$this->start_date'
                    AND
                        posts.$this->post_date_field < '$this->end_date'
                    AND
                        order_items.order_item_type <> 'shipping'
                GROUP BY
                    ID,
                    product_id,
                    post_date
                    ";

        return $sql;

    }

}

?>
