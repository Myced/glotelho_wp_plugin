<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_operations_report";

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

$cat = "";
if(isset($_GET['category']))
{
    $cat = $_GET['category'];
    if($cat == '-1')
    {
        $cat_name = "All Categories";
    }
    else {
        $ct = get_term_by("id", $cat, "product_cat");
        $cat_name = $ct->name;
    }
}


$selected_seller = '-1';
$seller_name = "";
if(isset($_GET['seller']))
{
    $selected_seller = $_GET['seller'];

    if($_GET['seller'] == '-1')
    {
        $seller_name = "All Sellers";
    }
    else {
        $seller = get_term_by('id', $_GET['seller'], "seller");

        $seller_name = $seller->name;
    }
}

$categories = self::getCategories();
$sellers = self::getSellers();

//now if it is the download
if(isset($_GET['download']))
{
    require_once BASE_DIRECTORY . '/templates/operations_download.php';
}
?>

<div class="wrap">
    <h3>
        Operations Report
        (<?php
            if(isset($_GET['start_date']))
                echo $start_date . ' - ' . $end_date;
            else {
                echo "Today";
            }
        ?>)

        <br><br>
        <?php
            echo $seller_name;
         ?>

        <?php
        if(isset($_GET['category']))
        {
            echo ' - ' . $cat_name;
        }
         ?>
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
            <select class="form-control" name="" id="gt_seller">
                <option value="-1">All Sellers</option>
                <?php foreach (self::getSellers() as $seller): ?>
                    <option value="<?php echo $seller->term_id; ?>"
                        <?php if($selected_seller == $seller->term_id) echo 'selected'; ?>
                        >
                        <?php echo $seller->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <select class="form-control" id="gt_category">
                <option value="-1">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category->term_id ?>"
                        <?php
                        if(isset($_GET['category']))
                        {
                            if($_GET['category'] == $category->term_id)
                                echo 'selected';
                        }  ?>>
                        <?php echo $category->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <input type="submit" id="filter-operations" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php
    if(isset($_GET['category']))
    {
        //include download button
        require BASE_DIRECTORY . '/templates/excel_download_btn.php';
    }
    ?>

    <br>
    <div class="row">
        <div class="col-md-12">

            <?php

            if(isset($_GET['category']))
            {
                $category = $_GET['category'];
                if($selected_seller == '-1')
                {
                    //show results for each seller
                    foreach($sellers as $seller)
                    {
                        ?>
                        <br>
                        <br>
                        <h3>Seller - <?php echo $seller->name; ?></h3>

                        <?php
                        if($category == '-1')
                        {
                            //show the data for all categories
                            $grandQuantity = 0;
                            $grandCostPrice = 0;
                            $grandTotalCost = 0;
                            $grandSellingPrice = 0;
                            $grandTotal = 0;
                            $grandProfit = 0;

                            //show the data for all categories
                            foreach ($categories as $cat)
                            {
                                $ct = get_term_by("id", $cat->term_id, "product_cat");
                                $cat_name = $ct->name;

                                $data = $manager->get_data($ct->term_id, $seller->term_id);

                                require BASE_DIRECTORY . '/templates/operations_report_row.php';
                            }

                            //now show the grand total space
                            require BASE_DIRECTORY . '/templates/grand_total.php';
                        }
                        else {
                            $data = $manager->get_data($category, $seller->term_id);
                            require BASE_DIRECTORY . '/templates/operations_report_row.php';
                        }
                    }
                }
                else {
                    if($category == '-1')
                    {
                        //show the data for all categories
                        //show the data for all categories
                        $grandQuantity = 0;
                        $grandCostPrice = 0;
                        $grandTotalCost = 0;
                        $grandSellingPrice = 0;
                        $grandTotal = 0;
                        $grandProfit = 0;

                        foreach ($categories as $cat)
                        {
                            $ct = get_term_by("id", $cat->term_id, "product_cat");
                            $cat_name = $ct->name;

                            $data = $manager->get_data($ct->term_id, $selected_seller);

                            require BASE_DIRECTORY . '/templates/operations_report_row.php';
                        }

                        //now show the grand total space
                        require BASE_DIRECTORY . '/templates/grand_total.php';
                    }
                    else {
                        $data = $manager->get_data($category, $selected_seller);
                        require BASE_DIRECTORY . '/templates/operations_report_row.php';
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
