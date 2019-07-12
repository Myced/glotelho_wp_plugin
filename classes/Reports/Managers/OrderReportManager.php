<?php
namespace App\Reports\Managers;

use App\Reports\OrderStatus;


class OrderReportManager
{

    public $wpdb;

    private $start_date;
    private $end_date;

    private $order_items = [];

    private $items_gotten = false;

    function __construct()
    {
        //initialise the application
        global $wpdb;

        $this->wpdb = $wpdb;

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

    public function get_orders()
    {
        return $this->wpdb->get_results($this->getOrdersQuery());
    }

    private function getOrdersQuery()
    {
        $sql = " SELECT
                    wp_posts.ID,
                    wp_posts.post_title,
                    wp_posts.post_status,
                    wp_posts.post_date,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_total')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS total,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_shipping')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS shipping,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_first_name')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS first_name,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_last_name')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS last_name,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_billing_phone')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS tel,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_gt_order_data')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS order_data

                FROM `wp_posts`
                LEFT JOIN `wp_postmeta`
                    ON wp_posts.ID = wp_postmeta.post_id
                WHERE
                    wp_posts.post_type = 'shop_order'
                    AND wp_posts.post_status <> 'auto-draft'
                    AND wp_posts.post_date >= '$this->start_date'
                    AND wp_posts.post_date <= '$this->end_date'
                GROUP BY wp_posts.ID
                ORDER BY wp_posts.ID DESC
        ";

        return $sql;
    }


}

?>
