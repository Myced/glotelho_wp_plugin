<?php
namespace App\Google;

use App\Traits\GoogleAnalyticsTrait;

class PurchaseEvent
{
    use GoogleAnalyticsTrait;

    public function register()
    {
        add_action('woocommerce_thankyou', [$this, 'send_ga_purchase_event'], 10);
    }

    public function send_ga_purchase_event($order_id)
    {
        $order = wc_get_order($order_id);

        $items = [];
        $analytics = [];

        $analytics['transaction_id'] = $order_id;
        $analytics['affiliation'] = "Glotelho Online Store";
        $analytics['value'] = $order->get_total();
        $analytics['currency'] = "USD";
        $analytics['tax'] = 0;
        $analytics['shipping'] = $order->get_shipping_total();

        //now load the
        $list_position = 1;
        foreach($order->get_items() as $item)
        {
            $product = [];

            $category_name = '';

            $category_ids = $item->get_product()->get_category_ids();
            $brand_id = $category_ids[count($category_ids) - 1];

            $category_name = GoogleAnalyticsTrait::get_product_category($category_ids);

            $brand = GoogleAnalyticsTrait::get_brand($brand_id);

            $product['id'] = '' . $item->get_product_id() . '';
            $product['name'] = $item->get_name();
            $product['list_name'] = "Cart";
            $product['brand'] = $brand;
            $product['category'] = $category_name;
            $product['variant'] = "None";
            $product['list_position'] = $list_position++;
            $product['quantity'] = $item->get_quantity();
            $product['price'] = $item->get_total();

            //add to the array of items
            array_push($items, $product);
        }

        //add the items to the analytics array
        $analytics['items'] = $items;

        $this->send_analytics($analytics);
    }

    public function send_analytics($analytics)
    {
        ?>
        <script type="text/javascript">
            gtag('event', 'purchase', <?php echo json_encode($analytics); ?>)
        </script>

        <?php
    }
}

 ?>
