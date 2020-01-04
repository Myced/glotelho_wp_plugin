<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\AccountingReportManager;

class AccountingReportController
{
    use ExcelTrait;

    public static function show_report()
    {
        $manager = new AccountingReportManager;

        $data = $manager->get_data();
        $categories = self::getCategories();

        //create a new spread sheet if we need to download.
        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/accounting_report.php';
    }

    public static function getCategories()
    {
        //prendre tous les category
        $categoriesMixed = get_terms('product_cat', ['hide_empty' => false ]);

        $categories = [];

        foreach($categoriesMixed as $cat)
        {
            if($cat->parent == 0 && $cat->slug != 'uncategorized')
            {
                //this category is a top level category
                array_push($categories, $cat);
            }
        }

        return $categories;
    }

    public static function order_status($status)
    {
        $statuss = '<i class="fa fa-check text-success"></i>';

        return $statuss;
    }
}
 ?>
