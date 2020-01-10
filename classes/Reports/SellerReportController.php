<?php
namespace App\Reports;

use App\Reports\OrderStatus;
use App\Reports\Managers\SellerReportManager;

class SellerReportController
{

    public static function show_report()
    {
        $manager = new SellerReportManager;

        return require_once GT_BASE_DIRECTORY . '/templates/sellers_report.php';
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
        return get_terms("seller", ['hide_empty' => false ]);
    }
}
 ?>
