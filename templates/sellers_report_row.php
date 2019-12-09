<br>
<div class="row">
    <div class="col-md-12">

        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Sales - <?php echo $seller_name; ?>
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
                            <th>Unit Price (PU)</th>
                            <th>Cost Price (PR)</th>
                            <th>Selling Price (PT)</th>
                            <th>Profit</th>
                        </tr>

                        <?php

                        $periodQuantity = 0;
                        $periodUnitPrice = 0;
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
                                $unit_price_total = 0;
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
                                    $periodUnitPrice += $product['cost_price'];
                                    $periodCostPrice += $product['quantity'] * $product['cost_price'];
                                    // $periodSellingPrice += $product['selling_price'];
                                    $periodProfits += $product['profit'];
                                    $periodSellingPrice += $product['selling_price'] * $product['quantity'];

                                    $quantityTotal += $product['quantity'];
                                    $unit_price_total += $product['cost_price'];
                                    $cost_price_total += $product['cost_price'] * $product['quantity'];
                                    // $selling_price_total += $product['selling_price'];
                                    $selling_price_total += $product['selling_price'] * $product['quantity'];
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
                                        <td> <?php echo number_format($product['cost_price'] * $product['quantity']); ?> </td>
                                        <td> <?php echo number_format($product['quantity'] * $product['selling_price']); ?> </td>
                                        <td> <?php echo number_format($product['profit']); ?> </td>
                                    </tr>
                                <?php endforeach; ?>

                            <?php endforeach; ?>

                            <!-- //show the details for the date -->
                            <tr>
                                <th style="text-align: center" colspan="2">Totals</th>
                                <th> <?php echo $quantityTotal; ?> </th>
                                <th> <?php echo number_format($unit_price_total); ?> </th>
                                <th> <?php echo number_format($cost_price_total); ?> </th>
                                <th> <?php echo number_format($selling_price_total); ?> </th>
                                <th> <?php echo number_format($total_profits); ?> </th>
                            </tr>
                        <?php endforeach; ?>

                        <!-- //show the details for the date -->
                        <tr>
                            <th style="text-align: center; font-size: 18px;" colspan="3">Totals</th>
                            <th style="font-size: 18px;"> <?php echo $periodQuantity; ?> </th>
                            <th style="font-size: 18px;"> <?php echo number_format($periodUnitPrice); ?> </th>
                            <th style="font-size: 18px;"> <?php echo number_format($periodCostPrice); ?> </th>
                            <th style="font-size: 18px;"> <?php echo number_format($periodSellingPrice); ?> </th>
                            <th style="font-size: 18px;"> <?php echo number_format($periodProfits); ?> </th>
                        </tr>

                    </table>
                </div>
            </div>
        <!-- /.box-body -->
        </div>

    </div>
</div>
