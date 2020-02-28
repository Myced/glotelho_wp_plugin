<?php
namespace App\Ajax;

use App\Reports\OrderStatus;

class InfoBipOrderStatus
{
    public function register()
    {
        //add a header for CORS
        add_action('init', [$this,'add_cors_http_header']);

        //get the towns from a particular region
        add_action( 'wp_ajax_gt_get_order_status',[$this, 'getOrderStatus'] );
        add_action( 'wp_ajax_nopriv_gt_get_order_status', [$this, 'getOrderStatus']);
    }

    public function add_cors_http_header(){
        header("Access-Control-Allow-Origin: *");
    }

    public function getOrderStatus(){
        if(!isset($_POST['order_number']))
        {
            die("INVALID");
        }

        $order_number = sanitize_text_field($_POST['order_number']);

        //get the database query object
        global $wpdb;

        //validate the order number..
        //make sure its numeric.
        if(!is_numeric($order_number))
        {
            echo "INVALID";
            die(); //end the request here
        }
        else {
            //check if the order exists in the database
            //get the order details from the database
            $query = "SELECT
                        `ID`,
                        `post_status`,
                        MAX(CASE WHEN (wp_postmeta.meta_key = '_order_number')
                            THEN wp_postmeta.meta_value ELSE NULL END) AS order_number
                    FROM
                        `wp_posts`
                        LEFT JOIN `wp_postmeta`
                            ON wp_posts.ID = wp_postmeta.post_id
                    WHERE wp_postmeta.meta_value = '$order_number'
                        AND wp_postmeta.meta_key = '_order_number'
                    ";

            //perform the query
            $result = $wpdb->get_results($query);

            if(count($result) == 0)
                die("FALSE");
            else {

                $order_details = $result[0];

                if($order_details->order_number == NULL)
                {
                    die("FALSE");
                }
                else {
                    $status = OrderStatus::getName($order_details->post_status);

                    echo $status;
                    die();
                }

            }

        }
    }
}

 ?>
