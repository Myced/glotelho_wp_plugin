<?php
namespace App\Reports;

use App\Reports\OrderStatus;
use App\Reports\Managers\IncomeReportManager;

class IncomeReportController
{

    public static function show_report()
    {
        $manager = new IncomeReportManager;

        $data = $manager->get_data();

        return require_once GT_BASE_DIRECTORY . '/templates/income_report.php';
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
}
 ?>
