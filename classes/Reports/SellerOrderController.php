<?php
namespace App\Reports;

use App\Reports\OrderStatus;
use App\Reports\Managers\SellerOrderManager;

class SellerOrderController
{

    public static function show_report()
    {
        $manager = new SellerOrderManager;
        $orders = $manager->get_orders();

        return require_once GT_BASE_DIRECTORY . '/templates/seller_orders.php';
    }

    public static function order_status($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }

    public static function getSellers()
    {
        $sellers = [];
        $ss = get_terms("seller", ['hide_empty' => false ]);

        foreach ($ss as $seller) {
            $sellers[$seller->term_id] = $seller->name;
        }

        return $sellers;
    }

    public static function getTowns()
    {
        $towns = [];
        $tt = get_terms("zone_town", ['hide_empty' => false ]);

        foreach ($tt as $town) {
            $towns[$town->term_id] = $town->name;
        }

        return $towns;
    }

    public static function getOrderStatuses()
    {
        return OrderStatus::allNames();
    }
}
 ?>
