<?php
namespace App\Base;


class PaymentMethodBox
{

    private $order_payment_method_key = "_gt_order_payment_method";

    private $methods = [
        "MOMO" => "MTN Mobile Money",
        "ORANGE" => "Orange Money",
        "CASH" => "CASH"
    ];


    public function register()
    {
        add_action( 'add_meta_boxes', [$this, 'add_meta_box'] );

        add_action( 'save_post', [$this, 'save_meta_data'], 10, 1 );
    }

    public function add_meta_box()
    {
        add_meta_box( 'gt_order_payment_method', __('Order Payment Method','woocommerce'),
            [$this,'add_order_box_fields'], 'shop_order', 'side', 'core' );
    }

    public function add_order_box_fields()
    {
        global $post;

        //get the payment method
        $method = get_post_meta($post->ID, $this->order_payment_method_key, true);


        echo '<input type="hidden" name="gt_payment_method_nonce" value="' . wp_create_nonce() . '">';
        ?>
        <p>
			<label class="meta-label" for="gt_order_payment_method">Mode de Paiement:</label>

            <select class="form-controll" name="gt_order_payment_method" id="gt_order_payment_method"
                style="width: 200px; ">
                <option value="-1">Sélectionnez le mode de paiement</option>
                <?php foreach ($this->methods as $key => $value): ?>
                    <option value="<?php echo $key ?>"
                        <?php if($key == $method) { echo "selected"; } ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
		</p>

        <?php

    }

    public function save_meta_data( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'gt_payment_method_nonce' ] ) ) {
            return $post_id;
        }

        $nonce = $_REQUEST[ 'gt_payment_method_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        $method = $_POST['gt_order_payment_method'];


        if( $method != '-1')
        {
            if( ! empty( $method ) )
            {
                update_post_meta( $post_id, $this->order_payment_method_key, $method, false );
            }

        }

    }


}

 ?>
