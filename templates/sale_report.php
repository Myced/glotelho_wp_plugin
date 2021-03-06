<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_sales_report";

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

function get_order_status($status)
{
    foreach(\App\Reports\OrderStatus::allNames() as $key => $value)
    {
        if($key == $status)
            return $value;
    }

    return "";
}


?>

<div class="wrap">
    <h3>
        Sales Report
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

        <div class="col-md-5">
            <input type="submit" id="filter" class="btn btn-primary" value="Filter">
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
                        Sales
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="width: 2000px;">
                            <tr>
                                <th style="min-width: 100px;">Date</th>
                                <th style="min-width: 150px;">Order</th>
                                <th style="min-width: 200px;">Client</th>
                                <th style="min-width: 300px;">Product</th>
                                <th style="min-width: 40px">Qty</th>
                                <th style="min-width: 120px;">Unit Price (PU)</th>
                                <th style="min-width: 120px;">Cost Price (PR)</th>
                                <th style="min-width: 120px;">Total Price (PT)</th>
                                <th style="min-width: 100px;">Profit</th>
                                <th style="min-width: 150px;">Seller</th>
                                <th style="min-width: 120px;">Town</th>
                                <th style="min-width: 600px;">Comment</th>
                            </tr>

                            <?php

                            $periodQuantity = 0;
                            $periodCostPrice = 0;
                            $periodTotalCost = 0;
                            $periodSellingPrice = 0;
                            $periodProfits = 0;

                             ?>

                            <?php foreach ($data as $currentDate => $date): ?>

                                <?php
                                    $dateCount = 0;
                                    $isDateRow = true;

                                    //set the order count
                                    foreach ($date as $order) {
                                        foreach ($order as $key => $product) {
                                            $dateCount += 1;
                                        }
                                    }
                                    ++$dateCount; // the column for totals

                                    //get the totals
                                    $quantityTotal = 0;
                                    $cost_price_total = 0;
                                    $total_cost_price_total = 0;
                                    $selling_price_total = 0;
                                    $total_profits = 0;
                                ?>

                                <?php foreach ($date as $currentOrder => $order): ?>

                                    <?php
                                        $isOrderRow = true;
                                        $orderCount = count($order);


                                    ?>
                                    <?php foreach ($order as $product): ?>
                                        <?php

                                        $periodQuantity += $product['quantity'];
                                        $periodCostPrice += $product['cost_price'];
                                        $periodTotalCost += $product['quantity'] * $product['cost_price'];
                                        $periodSellingPrice += $product['product_total'];
                                        $periodProfits += $product['profit'];

                                        $quantityTotal += $product['quantity'];
                                        $cost_price_total += $product['cost_price'];
                                        $total_cost_price_total += $product['quantity'] * $product['cost_price'];
                                        $selling_price_total += $product['product_total'];
                                        $total_profits += $product['profit'];



                                         ?>
                                        <tr>
                                            <?php
                                            if($isDateRow == true)
                                            {
                                                $isDateRow = false;
                                                ?>
                                                <td rowspan="<?php echo $dateCount; ?>">
                                                    <?php echo $currentDate; ?>
                                                </td>
                                                <?php
                                            }
                                             ?>

                                             <?php
                                             if($isOrderRow == true)
                                             {
                                                 $isOrderRow = false;
                                                 ?>
                                                 <td rowspan="<?php echo $orderCount; ?>">
                                                    Ord #<?php echo $currentOrder; ?>
                                                    <?php echo self::order_status($product['order_status']); ?>
                                                 </td>

                                                 <td rowspan="<?php echo $orderCount; ?>">
                                                     <?php echo $product['full_name']; ?>
                                                 </td>
                                                 <?php
                                             }
                                              ?>

                                            <td> <?php echo $product['name']; ?> </td>
                                            <td> <?php echo $product['quantity']; ?> </td>
                                            <td> <?php echo $product['cost_price']; ?> </td>
                                            <td> <?php echo number_format($product['quantity'] * $product['cost_price']); ?> </td>
                                            <td> <?php echo number_format($product['product_total']); ?> </td>
                                            <td> <?php echo number_format($product['profit']); ?> </td>
                                            <td> <?php echo $product['seller']; ?>  </td>
                                            <td> <?php echo $product['town']; ?> </td>
                                            <td> <?php echo $product['order_note']; ?> </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php endforeach; ?>

                                <!-- //show the details for the date -->
                                <tr>
                                    <th style="text-align: center" colspan="3">Totals</th>
                                    <th> <?php echo $quantityTotal; ?> </th>
                                    <th> <?php echo number_format($cost_price_total); ?> </th>
                                    <th> <?php echo number_format($total_cost_price_total); ?> </th>
                                    <th> <?php echo number_format($selling_price_total); ?> </th>
                                    <th> <?php echo number_format($total_profits); ?> </th>
                                    <th>  </th>
                                </tr>
                            <?php endforeach; ?>

                            <!-- //show the details for the date -->
                            <tr>
                                <th style="text-align: center; font-size: 18px;" colspan="4">Totals</th>
                                <th style="font-size: 18px;"> <?php echo $periodQuantity; ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodCostPrice); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodTotalCost); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodSellingPrice); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodProfits); ?> </th>
                                <th </th>
                            </tr>

                        </table>
                    </div>
                </div>
            <!-- /.box-body -->
            </div>

        </div>
    </div>
</div>
