<?php
namespace App\Reports\Managers;

use App\Traits\WooCommerceOrderQuery;

class TopSellersManager
{
    use WooCommerceOrderQuery;

    public $wpdb;
    public $default_quantity = 20;

    private $start_date;
    private $end_date;

    private $post_date_field;

    private $products = [];

    private $items_gotten = false;

    public function __construct()
    {

    }
}

 ?>
