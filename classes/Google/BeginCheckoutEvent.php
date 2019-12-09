<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class BeginCheckoutEvent
{
    use GoogleAnalyticsTrait;

    public function register()
    {
        //register the event after the product details has been show
        add_action('woocommerce_after_checkout_form', [$this, 'register_begin_checkout']);
    }

    public function register_begin_checkout()
    {
        $cart_items = $this->get_cart_items();


        //array containing the item
        $items = [];

        //loop through the items and set the parameters
        foreach($cart_items as $cart_item)
        {
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
        }



        $this->send_analytics($items);

    }

    public function send_analytics($items)
    {
        $analytics = [];
        $analytics['items'] = $items;

        ?>
        <script type="text/javascript">
            gtag('event', 'begin_checkout', <?php echo json_encode($analytics); ?>)
        </script>

        <?php
    }

    private function get_cart_items()
    {
        $items = WC()->cart->get_cart();

        return $items;
    }
}

 ?>
