<?php
namespace App\Base;


class SavePaymentDate
{
    public $date_key = "_gt_payment_date";

    public function register()
    {
        add_action( 'save_post', [$this, 'register_payment_date'], 10, 1 );
    }

    public function register_payment_date($post_id)
    {
        $post = get_post($post_id);

        //get the status of the order
        $status = $_POST['order_status'];

        if($post->post_type == "shop_order")
        {
            if($status == "wc-payment-received")
            {
                $this->save_payment_date($post_id);
            }
        }

    }

    public function save_payment_date($post_id)
    {
        $payment_date = $_POST['gt_payment_date'];

        if(empty($payment_date))
        {
            $payment_date = date("Y-m-d H:i:s");
        }

        $date = $payment_date;

        $single = true;

        //check if the date has been set before
        $old_value = get_post_meta($post_id, $this->date_key, $single );

        if(empty($old_value))
        {
            //update the post meta
            update_post_meta($post_id, $this->date_key, $date);

            //make a comment that the command has been encaisser
            $this->add_comment($post_id);
        }

    }

    private function add_comment($post_id)
    {
        $order = wc_get_order(  $post_id );

        // The text for the note
        $note = "Commande à étè encaisser";

        // Add the note
        $order->add_order_note( $note );
    }
}

 ?>
