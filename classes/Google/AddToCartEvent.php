<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class AddToCartEvent
{
    use GoogleAnalyticsTrait;

    public function register()
    {
        //register the event after the product details has been show
        // add_action('woocommerce_add_to_cart', [$this, 'register_add_to_cart']);

        //filter for refreshing fragment
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'add_analytics_code'], 10, 1);
    }

    public function register_add_to_cart($key)
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

        //set the session to initidate the items removed.
        $_SESSION['add_item'] = true;
        $_SESSION['items'] = $items;

        $this->send_analytics($items);

    }

    public function send_analytics($items)
    {
        $analytics = [];
        $analytics['items'] = $items;

        $script =  '<script type="text/javascript">'
                    . 'alert("javascript adding to cart");'
                    . 'gtag("event", "add_to_cart", ' . json_encode($analytics) . ')'
                    . '</script>';

        echo $script;
    }

    public function add_analytics_code($fragments)
    {

        if(isset($_SESSION['add_item']))
        {
            if($_SESSION['add_item'] == true)
            {
                $items = $_SESSION['items'];

                $_SESSION['add_item'] = false;

                $analytics = [];
                $analytics['items'] = $items;

                $script =  '<script type="text/javascript">'
                            . 'alert("javascript adding to cart");'
                            . 'gtag("event", "add_to_cart", ' . json_encode($analytics) . ')'
                            . '</script>';

                $fragments['script']  = $script;
                $fragments['me'] = "4";

                return $fragments;
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
