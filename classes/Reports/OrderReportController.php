<?php
namespace App\Reports;

use App\Reports\OrderStatus;
use App\Reports\Managers\OrderReportManager;

class OrderReportController
{
    public static function show_orders()
    {
        $manager = new OrderReportManager;

        $orders = $manager->get_orders();

        return require_once GT_BASE_DIRECTORY . '/templates/orders_report.php';
    }

    public static function getRegions()
    {
        return get_terms('zone_region', ['hide_empty' => false ]);
    }

    public static function getSellers()
    {
        return get_terms('seller', ['hide_empty' => false ]);
    }

    public static function getTowns()
    {
        return get_terms('zone_town', ['hide_empty' => false ]);
    }

    public static function showStatus($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }
}
 ?>
