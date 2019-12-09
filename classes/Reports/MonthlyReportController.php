<?php
namespace App\Reports;

use App\Reports\Managers\MonthlyReportManager;

class MonthlyReportController
{
    public static function show_report()
    {

        $manager = new MonthlyReportManager();

        $data = $manager->get_data();

        return require_once GT_BASE_DIRECTORY . '/templates/monthly_report.php';
    }


}
 ?>
