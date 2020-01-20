<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\AccountingReportManager;

class GTAccountingReportController
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

        return require_once GT_BASE_DIRECTORY . '/templates/accounting_report_custom.php';
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

    public static function getSellers()
    {
        $sellers = [];
        $key = 'gt_seller_code';

        $sell = get_terms("seller", ['hide_empty' => false ]);

        foreach($sell as $seller)
        {
            //get the seller code
            $code = get_term_meta( $seller->term_id, $key, $single = true );

            $group =  substr($code, 0, 2);

            $sellers[$seller->term_id] = [
                "name" => $seller->name,
                "code" => $code,
                "group" => $group
            ];
        }

        return $sellers;
    }

    public static function getTowns()
    {
        $towns = [];

        $ts = get_terms("zone_town", ['hide_empty' => false ]);

        foreach ($ts as $town)
        {
            //save the towns
            $towns[$town->term_id] = $town->name;
        }

        return $towns;
    }

    public static function show_status($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }
}
 ?>
