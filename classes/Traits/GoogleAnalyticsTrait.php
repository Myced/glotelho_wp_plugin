<?php
namespace App\Traits;

trait GoogleAnalyticsTrait
{
    public static function get_brand($brand_id)
    {
        $brand = get_term_by('id', $brand_id, 'product_cat')->name;

        return $brand;
    }

    public static function get_product_category($category_ids)
    {
        $category_name = '';

        //loop through all categories and get the name
        $count = 0;
        $category_count = count($category_ids);

        foreach($category_ids as $id)
        {

            $name = get_term_by('id', $id, 'product_cat')->name;
            $category_name .= $name;

            if($count < $category_count - 1)
                $category_name .= '/';

            $count++;
        }

        return $category_name;
    }
}


 ?>
