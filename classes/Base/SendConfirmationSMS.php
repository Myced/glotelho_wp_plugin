<?php
namespace App\Base;


class SendConfirmationSMS
{
    private $username = "Glotelho";
    private $password = "Glotelho@2020";

    private $base_url = "http://api.foseintsms.com/api_v3.php?";

    private $url = "";

    private $order;

    public function register()
    {
        add_action('woocommerce_thankyou', [$this, 'send_sms_notification'], 10);
    }

    public function send_sms_notification($order_id)
    {
        $order = wc_get_order($order_id);

        $this->order = $order;

        $sms = $this->build_sms();

        $this->send_sms($sms);
    }

    private function send_sms($sms)
    {
        $tel = $this->get_user_tel();

        if($tel == false)
        {
            //don't send an sms. the number is not valid
            return;
        }

        //build the url
        $url = $this->base_url;
        $url .= "username=$this->username&";
        $url .= "password=$this->password&";
        $url .= "message=" . urlencode($sms) . "&";
        $url .= "telephone=$tel";

        $this->url = $url;

        //make the http request.
        $this->make_get_request();
    }

    private function make_get_request()
    {
        global $wp_version;
        $args = array(
            'timeout'     => 30,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
            'body'        => null,
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => false,
            'stream'      => false,
            'filename'    => null
        );

        $raw_response = wp_remote_get( $this->url, $args );
    }

    private function build_sms()
    {
        $name = $this->get_client_name();
        $order_number = $this->order->get_order_number();

        $sms = "";

        $sms .= "Bonjour Monsieur/Madame $name.";
        $sms .= "\n";
        $sms .= "Votre commande No. " . $order_number;
        $sms .= " ";
        $sms .= "a bien été validée. ";
        $sms .= "Vous serez contacté par le Service Client pour confirmer votre commande.";

        // var_dump(urlencode($sms));

        return $sms;
    }

    private function get_client_name()
    {
        $billing_data = $this->order->get_data()['billing'];

        //get the names
        $first_name = isset($billing_data['first_name']) ? $billing_data['first_name'] : "";
        $last_name = isset($billing_data['last_name']) ? $billing_data['last_name'] : "";

        $name = $first_name . ' ' . $last_name;

        return $name;

    }

    private function get_user_tel()
    {
        $billing_data = $this->order->get_data()['billing'];

        //get the names
        $tel = isset($billing_data['phone']) ? $billing_data['phone'] : "";

        return $this->format_tel($tel);
    }

    private function format_tel($num)
    {
        $tel = $this->clean_tel($num);

        if(strlen($tel) == 9)
        {
            return $tel;
        }
        elseif (strlen($tel) == 8) {
            return '6' . $tel;
        }
        else {
            //the number is not 9 digits
            if(strlen($tel) == 12)
            {
                return substr($tel, 2, 9);
            }
            elseif (strlen($tel) == 11)
            {
                //the tel number is
                return '6' . substr($tel, 2, 8);
            }
            else {
                return false;
            }
        }
    }

    public static function clean_tel($number)
    {
        $regex = '/[\s\,\.\-\+\_]/';
        if(preg_match($regex, $number))
        {
            $filter = preg_filter($regex, '', $number);
        }
        else
        {
            $filter = $number;
        }

        return $filter;
    }
}

?>
