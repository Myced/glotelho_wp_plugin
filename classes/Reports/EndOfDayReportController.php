<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\EndOfDayReportManager;

class EndOfDayReportController
{
    use ExcelTrait;

    public static function show_report()
    {
        $manager = new EndOfDayReportManager;

        $data = $manager->get_data();

        //create a new spread sheet if we need to download.
        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/end_of_day_report.php';
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
