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



$categories = self::getCategories();
$sellers = self::getSellers();

//now if it is the download
if(isset($_GET['download']))
{
    require_once GT_BASE_DIRECTORY . '/templates/operations_download.php';
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

    </h3>
</div>

<div class="content">
    <input type="hidden" id="url" value="<?php echo $defaultUrl; ?>">

    <div class="row">
        <div class="col-md-3">
            <input type="text" name="start_date" value="<?php echo $start_date; ?>"
                class="form-control datepicker" id="start_date"
                placeholder="Start Date">
        </div>

        <div class="col-md-3">
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

    </div>

    <br>
    <div class="row">
        <div class="col-md-5">
            <select class="form-control chosen" multiple name="" id="gt_seller"
                data-placeholder="Select the Sellers you want">
                <option value="-1"
                    <?php echo isset($_GET['sellers']) && in_array('-1', $_GET['sellers']) ? "selected" : '' ?>
                    >All Sellers</option>
                <?php foreach (self::getSellers() as $seller): ?>
                    <option value="<?php echo $seller->term_id; ?>"
                        <?php
                        if(isset($_GET['sellers']))
                        {
                            if(in_array($seller->term_id, $_GET['sellers']))
                                echo 'selected';
                        }  ?>
                        >
                        <?php echo $seller->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-5">
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
            <input type="submit" id="filter-operations" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php
    if(isset($_GET['categories']))
    {
        //include download button
        require GT_BASE_DIRECTORY . '/templates/excel_download_btn.php';
    }
    ?>

    <br>
    <div class="row">
        <div class="col-md-12">

            <?php

            if(isset($_GET['categories']))
            {
                $urlcategories = $_GET['categories'];
                $urlsellers = $_GET['sellers'];


                if(in_array('-1', $urlsellers))
                {
                    //show results for each seller
                    foreach($sellers as $seller)
                    {
                        ?>
                        <br>
                        <br>
                        <h3>Seller - <?php echo $seller->name; ?></h3>

                        <?php
                        if(in_array('-1', $urlcategories))
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
                                $cat_name = $cat->name;

                                $data = $manager->get_data($ct->term_id, $seller->term_id);

                                require GT_BASE_DIRECTORY . '/templates/operations_report_row.php';
                            }

                            //now show the grand total space
                            require GT_BASE_DIRECTORY . '/templates/grand_total.php';
                        }
                        else {

                            //show the data only for selected categories

                            foreach ($categories as $cat)
                            {
                                if(! in_array($cat->term_id, $urlcategories))
                                    continue;

                                $ct = get_term_by("id", $cat->term_id, "product_cat");

                                $cat_name = $ct->name;

                                $data = $manager->get_data($ct->term_id, $seller->term_id);

                                require GT_BASE_DIRECTORY . '/templates/operations_report_row.php';
                            }

                        }
                    }
                }
                else {

                    //show only for selected sellers
                    foreach($sellers as $seller)
                    {
                        if(! in_array($seller->term_id, $urlsellers))
                            continue;


                        ?>
                        <br>
                        <br>
                        <h3>Seller - <?php echo $seller->name; ?></h3>

                        <?php
                        if(in_array('-1', $urlcategories))
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

                                require GT_BASE_DIRECTORY . '/templates/operations_report_row.php';
                            }

                            //now show the grand total space
                            require GT_BASE_DIRECTORY . '/templates/grand_total.php';
                        }
                        else {

                            //show the data for all categories
                            foreach ($categories as $cat)
                            {
                                if(! in_array($cat->term_id, $urlcategories))
                                    continue;

                                $cat_name = $cat->name;


                                $data = $manager->get_data($cat->term_id, $seller->term_id);

                                require GT_BASE_DIRECTORY . '/templates/operations_report_row.php';
                            }

                        }
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
