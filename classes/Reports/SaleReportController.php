<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\SaleReportManager;

class SaleReportController
{
    use ExcelTrait;

    public static function show_report()
    {
        $manager = new SaleReportManager;

        $data = $manager->get_data();

        //create a new spread sheet if we need to download.
        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/sale_report.php';
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
