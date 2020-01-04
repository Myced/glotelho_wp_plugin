<?php
namespace App\Base;

use App\Traits\ZoneTrait;

class AdvancePaymentMetaBox
{
    use ZoneTrait;

    private $payment_method_key = "_gt_advance_payment";
    private $advance_date_key = "_gt_advance_date";

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
        add_meta_box( 'gt_payment_advance', __('Advance Payments','woocommerce'),
            [$this,'add_order_box_fields'], 'shop_order', 'side', 'core' );
    }

    public function add_order_box_fields()
    {
        global $post;
        //get previous payments and show them.
        $payments = get_post_meta($post->ID, $this->payment_method_key);

        if(count($payments) > 0)
        {
            //show the payments
            echo "<h4> Les Paiements </h4>";

            ?>
            <table class="widefat fixed" cellspacing="0">
                <tr>
                    <th>S/N</th>
                    <th>Montant</th>
                    <th>Method</th>
                </tr>

            <?php

            $count = 1;
            foreach ($payments as $payment) {

                $method = $payment['method'];
                $amount = $payment['amount'];

                ?>
                <tr>
                    <td> <?php echo $count++; ?> </td>
                    <td> <?php echo number_format($amount); ?> </td>
                    <td> <?php echo $this->methods[$method] ?> </td>
                </tr>
                <?php
            }

            ?>
            </table>
            <?php
        }

        echo '<input type="hidden" name="gt_advance_nonce" value="' . wp_create_nonce() . '">';
        ?>
        <p>
			<label class="meta-label" for="gt_payment_mode">Mode de Paiement:</label>

            <select class="form-controll" name="gt_payment_mode" id="gt_payment_mode"
                style="width: 200px; ">
                <option value="-1">Sélectionnez le mode de paiement</option>
                <?php foreach ($this->methods as $key => $value): ?>
                    <option value="<?php echo $key ?>">
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
		</p>

        <p>
			<label class="meta-label" for="gt_plugin_region">Montant:</label>

            <input type="text" name="gt_advance_amount" value="" placeholder="Montant">
		</p>

        <?php

    }

    public function save_meta_data( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'gt_advance_nonce' ] ) ) {
            return $post_id;
        }

        $nonce = $_REQUEST[ 'gt_advance_nonce' ];

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

        $data = [
            'method' => $_POST['gt_payment_mode'],
            'amount' => $this->get_money($_POST['gt_advance_amount'])
        ];

        $date = date("Y-m-d H:i:s");

        if($data['method'] != '-1')
        {
            if( ! empty($data['amount']))
            {
                // Sanitize user input  and update the meta field in the database.
                add_post_meta( $post_id, $this->payment_method_key, $data, false);
                add_post_meta( $post_id, $this->advance_date_key, $date, true);

                //make a comment to indicate that an avance has been paid.
                // If you don't have the WC_Order object (from a dynamic $order_id)
                $order = wc_get_order(  $post_id );

                // The text for the note
                $note = "Une avance de "
                        . number_format($data['amount'])
                        . " a été payée par "
                        . $this->methods[$data['method']];

                // Add the note
                $order->add_order_note( $note );
            }

        }

    }

    public static function get_money($money)
    {
        $regex = '/[\s\,\.\-]/';
        if(preg_match($regex, $money))
        {
            $filter = preg_filter($regex, '', $money);
        }
        else
        {
            $filter = $money;
        }

        return $filter;
    }


}

 ?>
