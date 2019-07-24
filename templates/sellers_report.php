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

$sellers  = self::getSellers();

?>

<div class="wrap">
    <h3>
        Income Report
        (<?php
            if(isset($_GET['start_date']))
                echo $start_date . ' - ' . $end_date;
            else {
                echo "Today";
            }
        ?>)

    </h3>

    <?php
    if(isset($_GET['seller']))
    {
        ?>
        <h3><?php echo $seller_name; ?></h3>
        <?php
    }
     ?>
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

        <div class="col-md-5">
            <input type="submit" id="filter-sellers" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php
    //get the data for the seller
    if($selected_seller == '-1')
    {
        foreach ($sellers as $seller) {

            $data = $manager->get_user_data($seller->term_id);
            $seller_name = $seller->name;

            require BASE_DIRECTORY . '/templates/sellers_report_row.php';
        }
    }
    else {
        $data = $manager->get_user_data($selected_seller);

        require_once BASE_DIRECTORY . '/templates/sellers_report_row.php';
    }
     ?>

</div>
