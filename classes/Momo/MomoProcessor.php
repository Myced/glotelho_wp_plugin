<?php
namespace App\Momo;

ini_set('max_execution_time', 180); // 120 (seconds) = 2 Minutes
ini_set('default_socket_timeout', 180);

class MomoProcessor
{
    protected $amount;
    protected $number;

    private $momoEmail = 'tncedric@yahoo.com';

    private $url;

    public function __construct($number, $amount, $momoEmail)
    {
        $this->number = $number;
        $this->amount = $amount;
        $this->momoEmail = $momoEmail;

        $this->prepareURL();
    }

    private function prepareURL()
    {
        $url  = "https://developer.mtn.cm/OnlineMomoWeb/faces/transaction/transactionRequest.xhtml"
                . "?idbouton=2&typebouton=PAIE&_amount="
                . $this->amount . "&_tel="
                . $this->number ."&_clP=Cedric@2017"."&_email="
                . $this->momoEmail . "&submit.x=104&submit.y=70";

        $this->url = $url;
    }

    public function pay()
    {
        global $wp_version;
        $args = array(
            'timeout'     => 185,
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

        $response = $raw_response['body'];



        // $response = '{"ReceiverNumber":"237673901939","StatusCode":"100",
        //             "Amount":"80500","TransactionID":"12343433",
        //             "ProcessingNumber":"152951799903033605854680843",
        //             "OpComment":"Transaction failed",
        //             "StatusDesc":"TARGET_AUTHORIZATION_ERROR",
        //             "SenderNumber":"237673901939",
        //             "OperationType":"RequestPaymentIndividual"}';

        $this->response = $response;

        return $response;

    }

    protected function makeRequest()
    {
        $ch = curl_init($this->url); // such as http://example.com/example.xml
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
}
?>
