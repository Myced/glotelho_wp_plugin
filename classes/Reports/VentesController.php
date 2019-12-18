<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\VentesManager;

class VentesController
{
    use ExcelTrait;

    public static function show_report()
    {
        $manager = new VentesManager;

        //initialise the min and max prices
        if(isset($_GET['max']))
        {
            $max = number_format(self::gt_get_money($_GET['max']));
        }
        else {
            $max = number_format("10000000");
        }

        if(isset($_GET['min']))
        {
            $min = number_format(self::gt_get_money($_GET['min']));
        }
        else {
            $min = number_format('100000');
        }

        $max_value = self::gt_get_money($max);
        $min_value = self::gt_get_money($min);


        if(isset($_GET['categories']))
        {
            $data = $manager->get_data($min_value, $max_value);
        }
        else {
            $data = [];
        }

        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/ventes_report.php';
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

    public static function getStatuses()
    {
        return OrderStatus::allNames();
    }

    public static function showStatus($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }

    public static function gt_get_money($money)
    {
        $regex = '/[\s\,\.\-]/';
        if(preg_match($regex, $money))
        {
            $filter = preg_filter($regex, '', $money);
        }
        else
        {
            $filter = $money;
        }

        return $filter;
    }
}

 ?>
