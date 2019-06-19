<?php
namespace App\Momo;


class MomoParser
{
    private $response;

    public $receiverNumber;
    public $senderNumber;
    public $statusCode;
    public $amount;
    public $transactionID;
    public $processingNumber;

    public $success;
    public $message; // will contain the message for the result

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function parse()
    {
        $decoded = json_decode($this->response);

        $this->extractData($decoded);
        $this->parseResult();
    }

    private function extractData($decoded)
    {
        //now get the fields from the momo response
        $amount = $decoded->{'Amount'};
        $receiverNumber = $decoded->{'ReceiverNumber'};
        $senderNumber = $decoded->{'SenderNumber'};
        $statusCode = $decoded->{'StatusCode'};
        $transactionID = $decoded->{'TransactionID'};
        $processingNumber = $decoded->{'ProcessingNumber'};

        //now put them in the variable
        $this->amount = $amount;
        $this->receiverNumber = $receiverNumber;
        $this->senderNumber = $senderNumber;
        $this->statusCode = $statusCode;
        $this->transactionID = $transactionID;
        $this->processingNumber = $processingNumber;
    }

    private function parseResult()
    {
        //get the status code
        $code = $this->statusCode;

        if($code == "01")
        {
            $this->success = true;
            $this->message = "Payment Successful";
        }
        elseif($code == "515")
        {
            $this->success = false;
            $this->message = "This number does not have a mobile money account";
        }
        elseif($code == "529")
        {
            $this->success = false;
            $this->message = "You don't have enough money in your account. Please Recharge";
        }
        elseif($code == "100")
        {
            $this->success = false;
            $this->message = "Transaction Failed. Declined by user.";
        }
        elseif($code == "103")
        {
            $this->success = false;
            $this->message = "You did not approve the payment. Payment Timed out";
        }
        else{
            $this->success = false;
            $this->message = "Unknown Response";
        }
    }

    public function logPayout($order_id, $user_id, $user_name, $momoEmail)
    {
        date_default_timezone_set ( "Africa/Douala" );

        $created_at = date("Y-m-d h:i:s");

        //prepare a key value pair for the wp insert
        $values = [
            "order_id" => $order_id,
            "user_id" => $user_id,
            "client_name" => $user_name,
            "amount" => $this->amount,
            "user_number" => $this->senderNumber,
            "receiver_number" => $this->receiverNumber,
            "momo_email" => $momoEmail,
            "status_code" => $this->statusCode,
            "transaction_id" => $this->transactionID,
            "processing_number" => $this->processingNumber,
            "raw_response" => $this->response,
            "created_at" => $created_at
        ];

        $table = "momo_logs";

        global $wpdb;
        $wpdb->insert($table, $values);
    }
}
?>
