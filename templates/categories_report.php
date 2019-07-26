<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_categories_report";

if(isset($_GET['start_date']))
{
    $start_date = $_GET['start_date'];
}
else {
    $start_date = date("Y-m-d");
}

if(isset($_GET['end_date']))
{
    $end_date = $_GET['end_date'];
}
else {
    $end_date = date("Y-m-d");
}

if(isset($_GET['categories']))
{
    $selectedCategories = $_GET['categories'];
}
else {
    $selectedCategories = [];
}

$categories = self::getCategories();

if(isset($_GET['download']))
{
    require_once BASE_DIRECTORY . '/templates/category_download.php';
}
?>

<div class="wrap">
    <h3>
        Categories Report
        (<?php
            if(isset($_GET['start_date']))
                echo $start_date . ' - ' . $end_date;
            else {
                echo "Today";
            }
        ?>)

    </h3>
</div>

<div class="content">
    <input type="hidden" id="url" value="<?php echo $defaultUrl; ?>">

    <div class="row">
        <div class="col-md-2">
            <input type="text" name="start_date" value="<?php echo $start_date; ?>"
                class="form-control datepicker" id="start_date"
                placeholder="Start Date">
        </div>

        <div class="col-md-2">
            <input type="text" name="end_date" value="<?php echo $end_date; ?>"
                class="form-control datepicker" id="end_date"
                placeholder="End Date">
        </div>

        <div class="col-md-2">
            <select class="form-control" id="gt_order_type" >
                <option value="-1"
                <?php echo isset($_GET['order_type']) && ($_GET['order_type'] == '-1') ? 'selected' : '' ?>
                >Treated Today</option>
                <option value="1"
                <?php echo isset($_GET['order_type']) && ($_GET['order_type'] == '1') ? 'selected' : '' ?>
                >Ordered Today</option>
            </select>
        </div>

        <div class="col-md-4">
            <select class="form-control chosen" multiple id="gt_category"
                data-placeholder="Choose the categories needed">
                <option value="-1"
                    <?php echo isset($_GET['categories']) && in_array('-1', $_GET['categories']) ? "selected" : '' ?>
                    >All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category->term_id ?>"
                        <?php
                        if(isset($_GET['categories']))
                        {
                            if(in_array($category->term_id, $_GET['categories']))
                                echo 'selected';
                        }  ?>>
                        <?php echo $category->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <input type="submit" id="filter-category" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php
    if(isset($_GET['categories']))
    {
        //include download button
        require BASE_DIRECTORY . '/templates/excel_download_btn.php';
    }
    ?>

    <br>
    <div class="row">
        <div class="col-md-12">

            <?php

            if(isset($_GET['categories']))
            {

                if(in_array('-1', $selectedCategories))
                {
                    //show the data for all categories
                    $grandQuantity = 0;
                    $grandCostPrice = 0;
                    $grandTotalCost = 0;
                    $grandSellingPrice = 0;
                    $grandProfit = 0;

                    foreach ($categories as $cat)
                    {
                        $cat_name = $cat->name;

                        $data = $manager->get_data($cat->term_id);

                        require BASE_DIRECTORY . '/templates/category_report_row.php';
                    }

                    //now show the grand total space
                    require_once BASE_DIRECTORY . '/templates/grand_total.php';
                }
                else {

                    foreach ($categories as $cat)
                    {
                        //if not a selected category then continue;
                        if(! in_array($cat->term_id, $selectedCategories))
                            continue;


                        $cat_name = $cat->name;

                        $data = $manager->get_data($cat->term_id);

                        require BASE_DIRECTORY . '/templates/category_report_row.php';
                    }
                }
            }

            else {
                ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Please Select the Category and date period</h3>

                    </div>

                <!-- /.box-body -->
                </div>
                <?php
            }

             ?>
        </div>
    </div>
</div>
