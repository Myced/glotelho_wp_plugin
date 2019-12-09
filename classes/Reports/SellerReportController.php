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
        if($status == OrderStatus::COMPLETED)
        {
            $statuss = '<i class="fa fa-check text-success"></i>';
        }
        else {
            $statuss = '<i class="fa fa-clock text-warning"></i>';
        }

        return $statuss;
    }

    public static function getSellers()
    {
        return get_terms("seller", ['hide_empty' => false ]);
    }
}
 ?>
