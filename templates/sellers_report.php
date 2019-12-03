<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_sellers_report";

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

$sellers  = self::getSellers();

?>

<div class="wrap">
    <h3>
        Sellers Report
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

        <div class="col-md-3">
            <select class="form-control chosen" multiple id="gt_seller"
                data-placeholder="Select The Sellers..." >
                <option value="-1"
                    <?php echo isset($_GET['sellers'])  && in_array('-1', $_GET['sellers']) ? 'selected' : '' ?> >All Sellers</option>
                <?php foreach (self::getSellers() as $seller): ?>
                    <option value="<?php echo $seller->term_id; ?>"
                        <?php echo isset($_GET['sellers'])  && in_array($seller->term_id, $_GET['sellers']) ? 'selected' : '' ?>
                        >
                        <?php echo $seller->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <input type="submit" id="filter-sellers" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php
    //get the data for the seller
    if(!isset($_GET['sellers']))
    {
        //show for all sellers, since its not been filtered

        foreach ($sellers as $seller) {

            $data = $manager->get_user_data($seller->term_id);
            $seller_name = $seller->name;

            require GT_BASE_DIRECTORY . '/templates/sellers_report_row.php';
        }
    }
    else {
        //filter is on.
        //make sure all users is not included in the list
        if(in_array('-1', $_GET['sellers']))
        {
            foreach ($sellers as $seller) {

                $data = $manager->get_user_data($seller->term_id);
                $seller_name = $seller->name;

                require GT_BASE_DIRECTORY . '/templates/sellers_report_row.php';
            }
        }
        else {

            foreach ($sellers as $seller) {

                if(in_array($seller->term_id, $_GET['sellers']))
                {
                    $data = $manager->get_user_data($seller->term_id);
                    $seller_name = $seller->name;

                    require GT_BASE_DIRECTORY . '/templates/sellers_report_row.php';
                }
            }
        }
    }
     ?>

</div>
