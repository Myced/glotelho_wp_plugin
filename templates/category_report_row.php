<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?php if(isset($_GET['category'])) echo $cat_name; ?>
            (<?php if(isset($_GET['start_date'])) echo $start_date . ' - ' . $end_date; ?>)
        </h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th>Date</th>
                    <th>Order</th>
                    <th style="width: 300px;">Product</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Selling Price</th>
                    <th>Profit <small>(Total)</small> </th>
                </tr>

                <?php

                $periodQuantity = 0;
                $periodCostPrice = 0;
                $periodSellingPrice = 0;
                $periodProfits = 0;

                 ?>

                <?php foreach ($data as $currentDate => $date): ?>

                    <?php
                        $dateCount = count($date) + 1;
                        $isDateRow = true;

                        //get the totals
                        $quantityTotal = 0;
                        $cost_price_total = 0;
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
                            $periodSellingPrice += $product['selling_price'];
                            $periodProfits += $product['profit'];

                            $quantityTotal += $product['quantity'];
                            $cost_price_total += $product['cost_price'];
                            $selling_price_total += $product['selling_price'];
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
                                     </td>
                                     <?php
                                 }
                                  ?>

                                <td> <?php echo $product['name']; ?> </td>
                                <td> <?php echo $product['quantity']; ?> </td>
                                <td> <?php echo number_format($product['cost_price']); ?> </td>
                                <td> <?php echo number_format($product['selling_price']); ?> </td>
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
                        <th> <?php echo number_format($total_profits); ?> </th>
                    </tr>
                <?php endforeach; ?>

                <!-- //show the details for the date -->
                <tr>
                    <th style="text-align: center; font-size: 18px;" colspan="3">Totals</th>
                    <th style="font-size: 18px;"> <?php echo $periodQuantity; ?> </th>
                    <th style="font-size: 18px;"> <?php echo number_format($periodCostPrice); ?> </th>
                    <th style="font-size: 18px;"> <?php echo number_format($periodSellingPrice); ?> </th>
                    <th style="font-size: 18px;"> <?php echo number_format($periodProfits); ?> </th>
                </tr>

            </table>
        </div>
    </div>
<!-- /.box-body -->
</div>