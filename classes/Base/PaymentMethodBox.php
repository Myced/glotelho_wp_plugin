<?php
namespace App\Base;

use App\Base\Users;

class PaymentMethodBox
{

    private $order_payment_method_key = "_gt_order_payment_method";
    public $date_key = "_gt_payment_date";

    private $methods = [
        "MOMO" => "MTN Mobile Money",
        "ORANGE" => "Orange Money",
        "CASH" => "CASH",
        "YDE" => "YAOUNDE",
        "CHEQUE" => "CHEQUE",
        "CARD" => "CARD",
        "SHOWROOM" => "SHOWROOM"
    ];


    public function register()
    {
        add_action( 'add_meta_boxes', [$this, 'add_meta_box'] );

        add_action( 'save_post', [$this, 'save_meta_data'], 10, 1 );
    }

    private function get_user()
    {
        return wp_get_current_user();
    }

    public function accepted_users()
    {
        return Users::authorized();
    }

    public function add_meta_box()
    {
        if($this->user_authorized())
        {
            add_meta_box( 'gt_order_payment_method', __('Order Payment Method','woocommerce'),
                [$this,'add_order_box_fields'], 'shop_order', 'side', 'core' );
        }

    }

    public function add_order_box_fields()
    {
        global $post;

        $user = wp_get_current_user();

        //get the payment method
        $method = get_post_meta($post->ID, $this->order_payment_method_key, true);

        //get the date encaisse
        $date  = get_post_meta($post->ID, $this->date_key, true);

        if(empty($date))
        {
            $date = date("Y-m-d H:i:s");
        }

        echo '<input type="hidden" name="gt_payment_method_nonce" value="' . wp_create_nonce() . '">';
        ?>
        <p>
			<label class="meta-label" for="gt_pay_date">Date Encaisse:</label>

            <input type="text" name="gt_payment_date" value="<?php echo $date; ?>"
                id="gt_pay_date" placeholder="The Encaisse date">
		</p>
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
        if(! $this->user_authorized() )
        {
            return;
        }

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

        $payment_date = $_POST['gt_payment_date'];

        $date  = get_post_meta($post_id, $this->date_key, true);

        //update the payment date
        if(! empty($date))
        {
            update_post_meta( $post_id, $this->date_key, $payment_date );
        }

        if( $method != '-1')
        {
            if( ! empty( $method ) )
            {
                update_post_meta( $post_id, $this->order_payment_method_key, $method);
            }

        }

    }

    public function user_authorized()
    {
        //ge the user
        $user = $this->get_user();

        if(in_array($user->user_login, $this->accepted_users()))
        {
            return true;
        }

        return false;
    }


}

 ?>
