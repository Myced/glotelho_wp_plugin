<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_products_report";

//get all the categories
global $gt_product_categories;
$gt_product_categories = [];
$categories = get_terms("product_cat", ['hide_empty' => false ]);


foreach($categories as $category)
{
    $gt_product_categories[$category->term_id] = $category;
}

function gt_get_cat_name($cat_id)
{
    global $gt_product_categories;

    $category = $gt_product_categories[$cat_id];

    return $category->name;
}

?>

<div class="wrap">
    <h3>
        Products Report
    </h3>
</div>

<div class="content">

    <input type="hidden" id="url" value="<?php echo $defaultUrl; ?>">

    <div class="row">
        <div class="col-md-5">
            <select class="form-control chosen" id="category"
                multiple data-placeholder="Choose your categories">

                <option value="">My cat</option>
                <option value="">Cat 2</option>

            </select>
        </div>

        <div class="col-md-5">
            <input type="submit" id="filter-products" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Products Report
                    </h3>
                </div>

                <div class="box-body">

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered datatable" >
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Product Name</th>
                                    <th>Date Created</th>
                                    <th>On Promo</th>
                                    <th>% Off</th>
                                    <th>Cost Price</th>
                                    <th>Selling Price</th>
                                    <th>Marge</th>
                                    <th style="min-width: 200px;">Category</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $products = $manager->get_all_products();

                                $count = 1;
                                ?>

                                <?php foreach ($products as $product): ?>
                                    <?php
                                    $category_ids = $product->get_category_ids();

                                    $onPromo = false;
                                    $percentage = '';

                                    $regular_price = $product->get_regular_price();
                                    $active_price = $product->get_price();

                                    if($regular_price !== $active_price)
                                    {
                                        $onPromo = true;
                                        $percent = ($regular_price - $active_price) / $regular_price;
                                        $percentage = round($percent * 100);
                                    }

                                    $created_on = $product->get_date_created();
                                    $strtime = strtotime($created_on);

                                    $cost_price = get_post_meta($product->get_id(), "_gt_cost_price", true);

                                     ?>
                                    <tr>
                                        <td> <?php echo $count++; ?> </td>
                                        <th> <?php echo $product->get_name(); ?> </th>
                                        <td> <?php echo date("d/M/Y", $strtime); ?> </td>
                                        <td>
                                            <?php if ($onPromo == true): ?>
                                                <span class="label label-danger">On Promo</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($onPromo == true): ?>
                                                <span class="label label-danger">-<?php echo $percentage; ?>%</span>
                                            <?php endif; ?>
                                        </td>
                                        <td> <?php if(is_numeric($cost_price)) { echo number_format($cost_price);  } ?> </td>
                                        <td>
                                            <?php
                                                if($onPromo)
                                                {
                                                    ?>
                                                    <span style="text-decoration: line-through;"><?php echo number_format($regular_price); ?></span>
                                                    <br>
                                                    <span class="text-danger">
                                                        <strong><?php echo number_format($active_price); ?></strong>
                                                    </span>
                                                    <?php
                                                }
                                                else {
                                                    echo number_format($active_price);
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                if(is_numeric($cost_price))
                                                {
                                                    $profit = $active_price - $cost_price;
                                                    echo number_format($profit);
                                                }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            for($i = 0; $i < count($category_ids); $i++)
                                            {
                                                $cat_id = $category_ids[$i];

                                                echo gt_get_cat_name($cat_id);

                                                if($i < count($category_ids) -1)
                                                {
                                                    echo "/";
                                                }
                                            }
                                             ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>


                        </table>
                    </div>

                </div>
            </div>


        </div>
    </div>

</div>
