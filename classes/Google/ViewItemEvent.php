<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class ViewItemEvent
{
    use GoogleAnalyticsTrait;

    public function register()
    {
        //register the event after the product details has been show
        add_action('woocommerce_after_single_product_summary', [$this, 'register_product_view']);
    }

    public function register_product_view()
    {
        global $post;
        $product_id = $post->ID;

        //array containing the item
        $items = [];

        $product = wc_get_product($product_id);

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
        $item['quantity'] = 1;

        array_push($items, $item);

        $this->send_analytics($items);

    }

    public function send_analytics($items)
    {
        $analytics = [];
        $analytics['items'] = $items;

        ?>
        <script type="text/javascript">
            gtag('event', 'view_item', <?php echo json_encode($analytics); ?>)
        </script>

        <?php
    }
}

 ?>
