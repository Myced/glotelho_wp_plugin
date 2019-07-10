<?php
namespace App\Traits;


trait ReportsTrait
{
    public function getUsersAndRegionOrders()
    {
        global $wpdb;

        $sql = " SELECT
                    wp_posts.ID,
                    wp_posts.post_title,
                    wp_posts.post_status,
                    wp_posts.post_date,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_total')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS total,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_order_shipping')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS shipping,
                MAX(CASE WHEN (wp_postmeta.meta_key = '_payment_method')
                    THEN wp_postmeta.meta_value ELSE NULL END) AS payment_method,
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

        $result = $wpdb->get_results($sql);

        return $result;
    }

    public function get_payment_methods()
    {
        return WC()->payment_gateways->get_available_payment_gateways();
    }
}



 ?>
