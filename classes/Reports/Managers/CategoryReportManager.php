<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;
use App\Traits\WooCommerceOrderQuery;


class CategoryReportManager
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
            'cost_price' => 1000
        ];

    }

    private function getProductQuery($id)
    {
        $sql = "SELECT `id`, `post_title` FROM `wp_posts`
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
            $this->end_date = $_GET['end_date'];
        }
        else {
            $this->end_date = date("Y-m-d h:i:s");
        }

    }

    public function get_data($category)
    {
        $data = [];

        //get the products in this category
        //do all the processing
        //get all the products in this category
        $term_ids    = get_term_children( $category, 'product_cat' );
        $term_ids[]  = $category;
        $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

        $results = $this->get_order_report_data($this->get_args());

        foreach($results as $result)
        {
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

                $profit = ($result->item_total) - ($product_info['cost_price'] * $result->quantity);


                $productDetails = [
                    'id' => $result->product_id,
                    'name' => $product_info['name'],
                    'cost_price' => $product_info['cost_price'],
                    'selling_price' => $result->item_total / $result->quantity,
                    'quantity' => $result->quantity,
                    'profit' => $profit
                ];

                //push it into the order
                array_push($data[$date][$order], $productDetails);
            }


        }

        return $data;

        //now lets process the order
    }

    private function get_args()
    {
        return $args = array(
            'data'  => array(
                '_product_id' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => '',
                    'name'            => 'product_id',
                ),

                '_qty' => array(
                    'type'            => 'order_item_meta',
                    'function'        => '',
                    'name'            => 'quantity',
                ),
                '_line_subtotal' => array(
                    'type'            => 'order_item_meta',
                    'function'        => '',
                    'name'            => 'item_total',
                ),
                'post_date'   => array(
                    'type'     => 'post_data',
                    'function' => '',
                    'name'     => 'post_date',
                ),
                'ID'   => array(
                    'type'     => 'post_data',
                    'function' => '',
                    'name'     => 'order_id',
                )
            ),
            'group_by'     => 'ID, product_id, post_date',
            'query_type'   => 'get_results',
            'filter_range' => true,
        );
    }

}

?>
