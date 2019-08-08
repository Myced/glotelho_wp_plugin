<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class RemoveFromCartEvent
{
    use GoogleAnalyticsTrait;

    public function register()
    {
        //register the event after the product details has been show
        add_action('woocommerce_remove_cart_item', [$this, 'register_add_to_cart']);
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

        $this->send_analytics($items);

    }

    public function send_analytics($items)
    {
        $analytics = [];
        $analytics['items'] = $items;

        ?>
        <script type="text/javascript">
            gtag('event', 'remove_from_cart', <?php echo json_encode($analytics); ?>)
        </script>

        <?php
    }

    private function get_cart_item($key)
    {
        $item = WC()->cart->get_cart_item($key);

        return $item;
    }
}

 ?>
