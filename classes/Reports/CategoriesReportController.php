<?php
namespace App\Reports;

use App\Reports\Managers\CategoryReportManager;

class CategoriesReportController
{
    public static function show_report()
    {
        $manager = new CategoryReportManager;
        

        return require_once BASE_DIRECTORY . '/templates/categories_report.php';
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


}
 ?>
