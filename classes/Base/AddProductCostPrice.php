<?php
namespace App\Base;

class AddProductCostPrice
{
    public $key = "_gt_cost_price";

    public function register()
    {
        add_action('woocommerce_product_options_pricing', [$this, 'show_field']);
        add_action( 'save_post', array( $this, 'save_cost_price' ) );
    }

    public function show_field()
    {
        $value = $this->get_old_value();
        ?>

        <p class="form-field _regular_price_field ">
    		<label for="_gt_cost_price">Cost price (CFA)</label>

            <input type="text" class="short wc_input_price"
            style="" name="_gt_cost_price" id="_gt_cost_price"
            value="<?php echo $value; ?>" placeholder="">
        </p>

        <?php
    }

    public function save_cost_price($post_id)
    {
        if(isset($_POST['_gt_cost_price']))
        {
            $cost_price = $_POST['_gt_cost_price'];

            update_post_meta( $post_id, "_gt_cost_price", $cost_price );
        }
    }

    public function get_old_value()
    {
        global $post;
        $product_id = $post->ID;

        $cost_price = get_post_meta( $product_id, '_gt_cost_price', true );

        return $cost_price;
    }
}

?>
