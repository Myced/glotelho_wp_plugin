<?php
namespace App\Reports;


class UserStatsController
{
    public static function show_report()
    {
        return require_once GT_BASE_DIRECTORY . '/templates/user_stats.php';
    }


}

 ?>
