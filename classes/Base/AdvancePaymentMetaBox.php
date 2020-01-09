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

    public function add_meta_box()
    {
        add_meta_box( 'gt_payment_advance', __('Advance Payments','woocommerce'),
            [$this,'add_order_box_fields'], 'shop_order', 'side', 'core' );
    }

    public function add_order_box_fields()
    {
        global $post;
        //get previous payments and show them.
        $payments = get_post_meta($post->ID, $this->payment_method_key, true);

        $amount = "";
        $method = "";

        if(! empty($payments))
        {
            $amount = $payments['amount'];
            $method = $payments['method'];

            //show the payments
            echo "<h4> Les Paiements </h4>";

            //echo a form field to indicate that the advance had been added earlier
            ?>
            <input type="hidden" name="gt_old_method" value="<?php echo $method; ?>">
            <input type="hidden" name="gt_old_amount" value="<?php echo $amount; ?>">
            <?php

            ?>
            <table class="widefat fixed" cellspacing="0">
                <tr>
                    <th> <strong>S/N</strong> </th>
                    <th> <strong>Montant</strong> </th>
                    <th> <strong>Method</strong> </th>
                </tr>

            <?php

            $count = 1;

            ?>
                <tr>
                    <td> <?php echo $count++; ?> </td>
                    <td> <?php echo number_format($payments['amount']); ?> </td>
                    <td> <?php echo $this->methods[$payments['method']]; ?> </td>
                </tr>
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
                    <option value="<?php echo $key ?>"
                        <?php
                        if(! empty($method)) {
                            if($method == $key)
                                echo 'selected';
                        }
                         ?>>
                        <?php echo $value; ?>
                    </option>
                <?php endforeach; ?>
            </select>
		</p>

        <p>
			<label class="meta-label" for="gt_plugin_region">Montant:</label>

            <input type="text" name="gt_advance_amount"
                value="<?php if(! empty($amount)) echo number_format($amount); ?>"
                placeholder="Montant">
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

        $old_method = isset($_POST['gt_old_method']) ? $_POST['gt_old_method'] : '-1' ;
        $old_amount = isset($_POST['gt_old_amount']) ? $this->get_money($_POST['gt_old_amount']) : '-1';
        $note = '';

        $data = [
            'method' => $_POST['gt_payment_mode'],
            'amount' => $this->get_money($_POST['gt_advance_amount'])
        ];

        $date = date("Y-m-d H:i:s");

        if($data['method'] != '-1')
        {
            if( ! empty($data['amount']))
            {
                //save the meta data to the database

                //the advance has not bee saved before
                if($old_method == '-1')
                {
                    update_post_meta( $post_id, $this->payment_method_key, $data );
                    update_post_meta( $post_id, $this->advance_date_key, $date );

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

                else {
                    ///check if the old values are equal to the new
                    if($old_amount == $data['amount'] && $old_method == $data['method'])
                    {
                        //do nothing
                    }
                    else {
                        //update the values and also make a comment.
                        update_post_meta( $post_id, $this->payment_method_key, $data );
                        update_post_meta( $post_id, $this->advance_date_key, $date );

                        //update the comment depending on what changed.
                        if($old_amount != $data['amount'] )
                        {
                            $note = "Le montant de l'avance  a èté changé"
                                    . " de " . $old_amount . "  à "
                                    . number_format($data['amount']);
                        }

                        if($old_method != $data['method'])
                        {
                            $note = "Le Method de Paiement de l'avance  a èté changé"
                                    . " de " . $this->methods[$old_method] . "  à "
                                    . $this->methods[$data['method']] ;
                        }

                        //make a comment to indicate that an avance has been paid.
                        // If you don't have the WC_Order object (from a dynamic $order_id)
                        $order = wc_get_order(  $post_id );

                        // Add the note
                        $order->add_order_note( $note );
                    }
                }


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
