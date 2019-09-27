<?php
namespace App\Reports;

use App\Reports\OrderStatus;
use App\Reports\Managers\ClientAchatManager;

class ClientAchatController
{
    public static function show_report()
    {
        $manager = new ClientAchatManager;

        $data = $manager->get_data();

        return require_once BASE_DIRECTORY . '/templates/client_achat_category.php';
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
        return OrderStatus::validStatuses();
    }

    public static function showStatus($status)
    {
        $statuses = OrderStatus::allClasses();
        $class = $statuses[$status];
        $name = OrderStatus::allNames()[$status];

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }
}

 ?>
