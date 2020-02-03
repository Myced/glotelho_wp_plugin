<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\ChristianManager;

class ChristianController
{
    use ExcelTrait;

    public static function show_report()
    {
        $manager = new ChristianManager;

        if(isset($_GET['start_date']))
        {
            $data = $manager->get_data();
        }
        else {
            $data = [];
        }

        //create a new spread sheet if we need to download.
        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/christian_report.php';
    }

    public static function order_status($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }

    public static function getStatuses()
    {
        return OrderStatus::validStatuses();
    }
}
 ?>
