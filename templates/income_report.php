<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_income_report";

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



        <div class="col-md-5">
            <input type="submit" id="filter" class="btn btn-primary" value="Filter">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Reset
            </a>
        </div>
    </div>

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
                        <table class="table table-bordered">
                            <tr>
                                <th>Date</th>
                                <th>Order</th>
                                <th style="width: 300px;">Product</th>
                                <th style="width: 10px">Qty</th>
                                <th>Unit Cost</th>
                                <th>Selling Price</th>
                                <th>Total</th>
                                <th>Profit <small>(Total)</small> </th>
                            </tr>

                            <?php

                            $periodQuantity = 0;
                            $periodCostPrice = 0;
                            $periodSellingPrice = 0;
                            $periodTotal = 0;
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
                                    $selling_price_total = 0;
                                    $total_total= 0;
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
                                        $periodSellingPrice += $product['selling_price'];
                                        $periodProfits += $product['profit'];
                                        $periodTotal += $product['selling_price'] * $product['quantity'];

                                        $quantityTotal += $product['quantity'];
                                        $cost_price_total += $product['cost_price'];
                                        $selling_price_total += $product['selling_price'];
                                        $total_total += $product['selling_price'] * $product['quantity'];
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
                                                 <?php
                                             }
                                              ?>

                                            <td> <?php echo $product['name']; ?> </td>
                                            <td> <?php echo $product['quantity']; ?> </td>
                                            <td> <?php echo number_format($product['cost_price']); ?> </td>
                                            <td> <?php echo number_format($product['selling_price']); ?> </td>
                                            <td> <?php echo number_format($product['quantity'] * $product['selling_price']); ?> </td>
                                            <td> <?php echo number_format($product['profit']); ?> </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php endforeach; ?>

                                <!-- //show the details for the date -->
                                <tr>
                                    <th style="text-align: center" colspan="2">Totals</th>
                                    <th> <?php echo $quantityTotal; ?> </th>
                                    <th> <?php echo number_format($cost_price_total); ?> </th>
                                    <th> <?php echo number_format($selling_price_total); ?> </th>
                                    <th> <?php echo number_format($total_total); ?> </th>
                                    <th> <?php echo number_format($total_profits); ?> </th>
                                </tr>
                            <?php endforeach; ?>

                            <!-- //show the details for the date -->
                            <tr>
                                <th style="text-align: center; font-size: 18px;" colspan="3">Totals</th>
                                <th style="font-size: 18px;"> <?php echo $periodQuantity; ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodCostPrice); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodSellingPrice); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodTotal); ?> </th>
                                <th style="font-size: 18px;"> <?php echo number_format($periodProfits); ?> </th>
                            </tr>

                        </table>
                    </div>
                </div>
            <!-- /.box-body -->
            </div>

        </div>
    </div>
</div>
