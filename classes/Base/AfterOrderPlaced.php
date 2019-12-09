<?php
namespace App\Base;

class AfterOrderPlaced
{

    public function register()
    {
        add_action('woocommerce_thankyou', [$this, 'add_cost_prices'], 10, 1);
        // add_action('woocommerce_new_order_item', 'saveMetaData', 10, 3);
        // add_action('woocommerce_add_order_item_meta', [$this, 'add_order_item_meta'], 10, 2);
        add_filter('woocommerce_hidden_order_itemmeta',
                array($this, 'hidden_order_itemmeta'), 50);
    }

    public function hidden_order_itemmeta($args)
    {
        // $args[] = '_gt_cost_price';
        return $args;
    }

    public function add_order_item_meta($item_id, $values)
    {
        woocommerce_add_order_item_meta($item_id, $key, $value);
    }

    public function add_cost_prices($order_id)
    {
        $order = new \WC_Order($order_id);

        foreach($order->get_items() as $item_id => $item)
        {
            // Get the product object
            $product = $item->get_product();

            $product_id = $product->get_id();

            //now get the cost price
            $cost_price = $this->get_cost_price($product_id);

            $meta_key = "_gt_cost_price";

            if(wc_get_order_item_meta( $item_id, $meta_key, $single = true ) == "")
            {
                //save the item meta
                wc_add_order_item_meta($item_id, $meta_key, $cost_price, false);
            }
            else {
                wc_update_order_item_meta( $item_id, $meta_key, $cost_price, '' );
            }

        }
    }

    private function get_cost_price($product_id)
    {
        global $wpdb;
        $sql = $this->get_sql($product_id);

        $result = $wpdb->get_results($sql);

        if(count($result) > 0)
        {
            if($result[0]->cost_price != null)
            {
                return $result[0]->cost_price;
            }

            return 0;
        }

        return 0;
    }

    private function get_sql($product_id)
    {
        $sql = "SELECT `ID`,
                    MAX(CASE WHEN (wp_postmeta.meta_key = '_gt_cost_price')
                        THEN wp_postmeta.meta_value ELSE NULL END) AS cost_price
                    FROM `wp_posts`
                    LEFT JOIN `wp_postmeta`
                        ON wp_posts.ID = wp_postmeta.post_id
                    WHERE `ID` = '$product_id'
        ";

        return $sql;
    }
}

?>
