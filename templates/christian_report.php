<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_christian_report";

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

//if the request is to download the document
if(isset($_GET['download']))
{
    if($_GET['download'] == true)
    {
        require_once GT_BASE_DIRECTORY . '/templates/sales_download.php';
    }
}

$statuses = self::getStatuses();


?>

<div class="wrap">
    <h3>
        Rapport Pour Achat
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



        <div class="col-md-3">
            <select class="form-control chosen" multiple id="gt_order_status"
                data-placeholder="Choose the order status">

                <?php foreach ($statuses as $key => $status): ?>
                    <option value="<?php echo $key ?>"
                        <?php
                        if(isset($_GET['statuses']))
                        {
                            if(in_array($key, $_GET['statuses']))
                                echo 'selected';
                        }  ?>>
                        <?php echo $status; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <input type="submit" id="filter-christian" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

    <?php require_once GT_BASE_DIRECTORY . '/templates/excel_download_btn.php'; ?>

    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Les Produit à Acheter
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" >
                            <tr>
                                <th style="min-width: 100px;">Date</th>
                                <th style="min-width: 80px;">Order</th>
                                <th style="min-width: 100px;">Status</th>
                                <th style="min-width: 200px;">Client</th>
                                <th style="min-width: 300px;">Product</th>
                                <th style="min-width: 40px">Qty</th>
                            </tr>


                            <?php foreach ($data as  $item): ?>

                                <tr>

                                      <td>
                                          <?php echo $item['date']; ?>
                                      </td>
                                      <td>
                                         <?php echo $item['order_number']; ?>
                                      </td>

                                      <td>
                                          <?php echo self::order_status($item['order_status']); ?>
                                      </td>

                                      <td>
                                          <?php echo $item['full_name']; ?>
                                      </td>
                                        <td> <?php echo $item['product_name']; ?> </td>
                                        <td> <?php echo $item['quantity']; ?> </td>

                                </tr>


                            <?php endforeach; ?>

                            <!-- //show the details for the date -->


                        </table>
                    </div>
                </div>
            <!-- /.box-body -->
            </div>

        </div>
    </div>
</div>
