<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class RemoveFromCartEvent
{
    use GoogleAnalyticsTrait;

    private $items;

    public function register()
    {
        //register the event after the product details has been show
        add_action('woocommerce_remove_cart_item', [$this, 'register_remove_item']);

        add_filter('woocommerce_add_to_cart_fragments', [$this, 'add_analytics_code']);
    }

    public function register_remove_item($key)
    {
        $cart_item = $this->get_cart_item($key);

        //array containing the item
        $items = [];

        $product_id = $cart_item['product_id'];
        $product = $cart_item['data'];

        //get the product category ids
        $category_ids = $product->get_category_ids();
        $brand_id = $category_ids[count($category_ids) - 1];

        $category_name = GoogleAnalyticsTrait::get_product_category($category_ids);
        $brand = GoogleAnalyticsTrait::get_brand($brand_id);

        //prepare a item to send to google analytics
        $item = [];

        //fill in the item information
        $item['id'] = '' . $product_id . '';
        $item['name'] = $product->get_name();
        $item['list_name'] = "Product List";
        $item['brand'] = $brand;
        $item['category'] = $category_name;
        $item['variant'] = "None";
        $item['list_position'] = 1;
        $item['price'] = $product->get_price();
        $item['quantity'] = $cart_item['quantity'];

        array_push($items, $item);

        $this->items = $items;

        //set the session to initidate the items removed.
        $_SESSION['removed'] = true;
        $_SESSION['items'] = $items;

    }

    public function send_analytics($items)
    {

    }

    public function add_analytics_code($fragments)
    {
        if(isset($_SESSION['removed']))
        {
            if($_SESSION['removed'] == true)
            {
                // $items = $_SESSION['items'];
                //
                // $_SESSION['removed'] = false;
                //
                // $analytics = [];
                // $analytics['items'] = $items;
                //
                // $script =  '<script type="text/javascript">'
                //             . 'alert("Remove  cart");'
                //             . 'gtag("event", "add_to_cart", ' . json_encode($analytics) . ')'
                //             . '</script>';
                //
                // // $fragments['script']  = $script;
                //
                // return $fragments;
            }
        }
    }

    private function get_cart_item($key)
    {
        $item = WC()->cart->get_cart_item($key);

        return $item;
    }
}

 ?>
