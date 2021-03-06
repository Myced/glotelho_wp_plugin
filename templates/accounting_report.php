<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_accounting";

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


function get_order_status($status)
{
    foreach(\App\Reports\OrderStatus::allNames() as $key => $value)
    {
        if($key == $status)
            return $value;
    }

    return "";
}

//now get the products in each categories
$category_products = [];
$cat_ids = [];
$product_cats = [];

$payment_methods = \App\Base\WhiteList::payment_methods();

//since there are almost 46 categories and we need only a few.
//let me whitelist categories to be shown.
$whiteList = \App\Base\WhiteList::categories();

foreach($categories as $category)
{
    $term_ids    = get_term_children( $category->term_id, 'product_cat' );
    $term_ids[]  = $category->term_id;
    $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

    array_push($cat_ids, $category->term_id);

    $category_products[$category->term_id] = $product_ids;
}


function get_product_categories($product_id, $categories, $category_products, $whiteList)
{
    $name = "";
    $all = [];


    foreach ($categories as $category)
    {

        $ids = $category_products[$category->term_id];

        if(in_array($product_id, $ids))
        {
            //if its a whitelisted category
            if(in_array($category->term_id, $whiteList))
            {
                array_push($all, $category->name);
            }
        }
    }

    //form the categories to one
    if(count($all) == 0)
    {
        $name = "";
    }
    else {
        if(count($all) == 1)
        {
            $name = $all[0];
        }
        else {

            $num = count($all);

            for($i = 0; $i < count($all); $i++)
            {
                $name .= $all[$i];

                if($i < $num -1)
                {
                    $name .= " / ";
                }
            }
        }
    }

    return $name;
}

//set up the category data
$category_data = [];

foreach($categories as $category)
{
    $category_data[$category->term_id] = [
        'name' => $category->name,
        'qty' => 0,
        'orders' => 0,
        'cost' => 0,
        'total' => 0,
        'marge' => 0
    ];
}

//get orders for this statistics
$paidOrders = $manager->getPaidOrders();

$sellers = self::getSellers();
$towns = self::getTowns();

//if the request is to download the document
if(isset($_GET['download']))
{
    if($_GET['download'] == true)
    {
        require_once GT_BASE_DIRECTORY . '/templates/accounting_download.php';
    }
}
?>

<div class="wrap">
    <h3>
        COMPTABILITE
        (<?php
            if(isset($_GET['start_date']))
                echo $start_date . ' - ' . $end_date;
            else {
                echo "Aujourd'hui";
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
            <input type="submit" id="filter-acc" class="btn btn-primary" value="Filter">
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
                        Les Ventes Encaissé
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="width: 2000px;">
                            <tr>
                                <th style="min-width: 150px;">Date Encaissé</th>
                                <th style="min-width: 150px;">Date Commandé</th>
                                <th style="min-width: 150px;">No Commande</th>
                                <th style="min-width: 300px;">Produit</th>
                                <th style="min-width: 40px">Qte</th>
                                <th style="min-width: 120px;">Prix Unitaire (PU)</th>
                                <th style="min-width: 120px;">Prix Reviendre (PR)</th>
                                <th style="min-width: 120px;">Prix Total (PT)</th>
                                <th style="min-width: 100px;">Marge</th>
                                <th style="min-width: 150px;">Vendeur</th>
                                <th style="min-width: 120px;">Ville</th>
                                <th style="min-width: 100px;">Paiement</th>
                                <th style="min-width: 200px;"> Categorie </th>
                                <th style="min-width: 200px;">Sub Category </th>
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

                                <?php $gt_current_order = ''; ?>
                                <?php foreach ($date as $currentOrder => $order): ?>

                                    <?php
                                        $gt_current_order = $currentOrder;

                                        $isOrderRow = true;
                                        $orderCount = count($order);

                                        $sub_current_order = '';
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

                                        $current_product_id = $product['product_id'];

                                        //the categories that need to be affected by this product
                                        $affected_categories = [];

                                        //check if this product already has it categories
                                        if(array_key_exists($current_product_id, $product_cats) )
                                        {
                                            //then just update the categories total info
                                            $affected_categories = $product_cats[$current_product_id];
                                        }
                                        else {
                                            //get the product categories and save them

                                            //loop through the categories
                                            $prod_cats = [];

                                            foreach($category_products as $cur_cat_id => $cat_products)
                                            {
                                                //check if the item is in the list
                                                if(in_array($current_product_id, $cat_products))
                                                {
                                                    array_push($prod_cats, $cur_cat_id);
                                                }
                                            }

                                            $product_cats[$product['product_id']] = $prod_cats;

                                            $affected_categories = $prod_cats;
                                        }

                                        //get the product category
                                        $sub_cat = wc_get_product_category_list($current_product_id);

                                        //now affect the required categories
                                        $p_cost_price = $product['cost_price'] * $product['quantity'];
                                        $p_total = $product['product_total'];
                                        $marge = $product['profit'];

                                        foreach($affected_categories as $aff_cat)
                                        {
                                            $category_data[$aff_cat]['qty'] += $product['quantity'];
                                            $category_data[$aff_cat]['cost'] += $p_cost_price;
                                            $category_data[$aff_cat]['total'] += $p_total;
                                            $category_data[$aff_cat]['marge'] += $marge;

                                            if($sub_current_order == '')
                                            {
                                                ++$category_data[$aff_cat]['orders'];
                                            }
                                            else {
                                                if($sub_current_order != $gt_current_order)
                                                {
                                                    ++$category_data[$aff_cat]['orders'];
                                                }
                                            }

                                        }

                                        $sub_current_order = $gt_current_order;

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
                                                     <?php
                                                     if($product['order_date'] != null)
                                                     {
                                                         $edate = date("d/M/Y", strtotime($product['order_date']));
                                                         echo $edate;
                                                     }
                                                      ?>
                                                 </td>
                                                 <td rowspan="<?php echo $orderCount; ?>">
                                                    Ord #<?php echo $currentOrder; ?>
                                                    <?php echo self::order_status($product['order_status']); ?>
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
                                            <td> <?php echo $product['payment_method']; ?> </td>
                                            <td>
                                                <?php
                                                    $cat = get_product_categories($product['product_id'], $categories, $category_products, $whiteList);
                                                    echo $cat;
                                                ?>
                                            </td>

                                            <td> <?php echo $sub_cat; ?> </td>
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
                                    <th>  </th>
                                    <th>  </th>
                                    <th></th>
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
                                <th> </th>
                                <th> </th>
                                <th> </th>
                                <th></th>
                            </tr>

                        </table>
                    </div>
                </div>
            <!-- /.box-body -->
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Les Commandes Encaissé
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">

                        <table class="table-bordered table "  style="width: 1500px;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Date Cmde</th>
                                    <th>Date Encaissé</th>
                                    <th>No Commde</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Advance</th>
                                    <th>Advance Method</th>
                                    <th>Total Cmde</th>
                                    <th>Net Encaissé</th>
                                    <th>Mtd de Paiement</th>
                                    <th>Seller</th>
                                    <th>Ville</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    $count = 1;

                                    //set the totals;
                                    $total_income = 0;
                                    $total_advances = 0;
                                    $total_cashed = [];
                                    $total_cashed['ALL'] = 0;

                                    foreach ($payment_methods as $key => $value) {
                                        $total_cashed[$key] = 0;
                                    }
                                ?>
                                <?php foreach ($paidOrders as $order): ?>
                                    <?php
                                    $client  = $order->first_name . ' ' . $order->last_name;

                                    $advance = '';
                                    $advance_method = '';
                                    $total = $order->total;
                                    $net_income = 0;


                                    //order data
                                    $order_town = "";
                                    $order_seller = "";

                                    if($order->advance_data != null)
                                    {
                                        //the advance is not null
                                        $advance_data  = unserialize($order->advance_data);

                                        $advance = $advance_data['amount'];
                                        $advance_method = $advance_data['method'];

                                        $total_advances += $advance;

                                    }

                                    if($order->order_data != null)
                                    {
                                        $order_data = unserialize($order->order_data);

                                        $order_town = $towns[$order_data['gt_town']];

                                        $s_data = $sellers[$order_data['gt_seller']];
                                        $order_seller = $s_data['name'];
                                    }

                                    $net_income = $total - $advance;

                                    //calculate the totals
                                    $total_cashed['ALL'] += $net_income;


                                    if(array_key_exists($order->payment_method, $payment_methods))
                                    {
                                        //sum that payment method
                                        $total_cashed[$order->payment_method] += $net_income;
                                    }

                                     ?>
                                    <tr>
                                        <td> <?php echo $count++; ?> </td>
                                        <td> <?php echo date("d, M Y", strtotime($order->post_date)); ?> </td>
                                        <td> <?php echo date("d, M Y", strtotime($order->payment_date)); ?> </td>
                                        <td>
                                            Ord
                                            #<?php
                                            if(! is_null($order->invoice_no))
                                            {
                                                echo $order->invoice_no;
                                            }
                                            else {
                                                echo $order->ID;
                                            }
                                             ?>
                                        </td>
                                        <td> <?php echo $client; ?> </td>
                                        <td> <strong> <i class="fa fa-check text-success"></i> </strong> </td>
                                        <td> <?php echo number_format($advance); ?> </td>
                                        <td> <?php echo $payment_methods[$advance_method]; ?> </td>
                                        <td> <?php echo number_format($total); ?> </td>
                                        <td> <?php echo number_format($net_income); ?> </td>
                                        <td> <?php echo $payment_methods[$order->payment_method]; ?> </td>
                                        <td> <?php echo $order_seller; ?> </td>
                                        <td> <?php echo $order_town; ?> </td>

                                    </tr>

                                <?php endforeach; ?>
                                <tr>
                                    <th colspan="2">Total Encaissé</th>
                                    <th colspan="2"> <?php echo number_format($total_cashed['ALL']); ?> FCFA </th>
                                </tr>

                                <?php foreach ($total_cashed as $key => $value): ?>

                                    <?php if ($key != 'ALL'): ?>
                                        <tr>
                                            <th colspan="2">
                                                Total Encaissé ( <?php echo $payment_methods[$key]; ?> )
                                            </th>
                                            <th colspan="2">
                                                <?php echo number_format($total_cashed[$key]); ?> FCFA
                                            </th>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- advances received 0 -->
    <?php $advance_orders = $manager->get_adavance_orders(); ?>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Les Avances Encaissé
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">

                        <table class="table-bordered table "  style="width: 1500px;">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Date Cmde</th>
                                    <th>No Commde</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Advance</th>
                                    <th>Advance Method</th>
                                    <th>Date D'Advance</th>
                                    <th>Total Cmde</th>
                                    <th>Seller</th>
                                    <th>Ville</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    $count = 1;
                                    $total_advances = 0;
                                ?>
                                <?php foreach ($advance_orders as $order): ?>
                                    <?php
                                    //DO NOT INCLUDES COMMANDESA ALREADY ENCAISSER
                                    // if($order->post_status == \App\Reports\OrderStatus::PAYMENT_RECEIVED)
                                    // {
                                    //     continue;
                                    // }

                                    $client  = $order->first_name . ' ' . $order->last_name;

                                    $advance = '';
                                    $advance_method = '';


                                    //order data
                                    $order_town = "";
                                    $order_seller = "";

                                    if($order->advance_data != null)
                                    {
                                        //the advance is not null
                                        $advance_data  = unserialize($order->advance_data);

                                        $advance = $advance_data['amount'];
                                        $advance_method = $advance_data['method'];

                                        $total_advances += $advance;

                                    }

                                    if($order->order_data != null)
                                    {
                                        $order_data = unserialize($order->order_data);

                                        $order_town = $towns[$order_data['gt_town']];

                                        $s_data = $sellers[$order_data['gt_seller']];
                                        $order_seller = $s_data['name'];
                                    }

                                     ?>
                                    <tr>
                                        <td> <?php echo $count++; ?> </td>
                                        <td> <?php echo date("d, M Y", strtotime($order->post_date)); ?> </td>
                                        <td>
                                            #<?php
                                            if(! is_null($order->invoice_no))
                                            {
                                                echo $order->invoice_no;
                                            }
                                            else {
                                                echo $order->ID;
                                            }
                                             ?>
                                        </td>
                                        <td> <?php echo $client; ?> </td>
                                        <td> <?php echo self::show_status($order->post_status); ?> </strong> </td>
                                        <td> <?php echo number_format($advance); ?> </td>
                                        <td> <?php echo $payment_methods[$advance_method]; ?> </td>
                                        <td> <?php echo date("d, M Y", strtotime($order->advance_date)); ?> </td>
                                        <td> <?php echo number_format($order->total); ?> </td>
                                        <td> <?php echo $order_seller; ?> </td>
                                        <td> <?php echo $order_town; ?> </td>

                                    </tr>

                                <?php endforeach; ?>

                                <tr>
                                    <th colspan="3" class="text-center">Total Advances</th>
                                    <td colspan="2"> <?php echo number_format($total_advances); ?> </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <!-- end of advances -->

    <!-- category report row  -->
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Les Categories
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Categorie</th>
                                <th>Qte Vendu</th>
                                <th>No Commandes</th>
                                <th>Coût</th>
                                <th>Total</th>
                                <th>Marge</th>
                            </tr>

                            <?php $count = 1; ?>
                            <?php foreach ($category_data as $key => $value): ?>
                                <?php
                                //only show whitelisted categories
                                if(! in_array($key, $whiteList))
                                    continue;

                                 ?>
                                <tr>
                                    <th> <?php echo $count++; ?> </th>
                                    <th> <?php echo $value['name']; ?> </th>
                                    <td> <?php echo $value['qty']; ?> </td>
                                    <td> <?php echo $value['orders']; ?> </td>
                                    <td> <?php echo number_format($value['cost']); ?> </td>
                                    <td> <?php echo number_format($value['total']); ?> </td>
                                    <td> <?php echo number_format($value['marge']); ?> </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end of category report row -->

    <?php

    $seller_data['SC'] = [];
    $seller_data['FL'] = [];
    $seller_data['AM'] = [];
    $seller_data['EM'] = [];

    foreach($sellers as $seller_id => $seller)
    {
            // add the seller to the list
            $array = [];
            $seller_group = $seller['group'];

            // array_push($seller_data[$seller_group], $array);
            $seller_data[$seller_group][$seller_id] = [
                            "name" => $seller['name'],
                            "orders_count" => 0,
                            "amount" => 0
                        ];

    }

    //loop through the data and calculate
    foreach($paidOrders as $order)
    {
        if($order->order_data != null)
        {
            $order_data = unserialize($order->order_data);

            $region = $order_data['gt_region'];
            $seller_id = $order_data['gt_seller'];
            $town = isset($order_data['gt_town']) ? $order_data['gt_town'] : '-1';

            //get the user type
            $seller = $sellers[$seller_id];
            $seller_group = $seller['group'];

            //now add the sales for the seller
            ++$seller_data[$seller_group][$seller_id]['orders_count'];
            $seller_data[$seller_group][$seller_id]['amount'] += $order->total;

            // $report['sellers'][$seller]['count'] += 1;
            // $report['sellers'][$seller]['total'] += $order_amount;

            //
            // $report['regions'][$region]['count'] += 1;
            // $report['regions'][$region]['total'] += $order_amount;
            //
            // $report['towns'][$town]['count'] += 1;
            // $report['towns'][$town]['total'] += $order_amount;

        }
    }

    //loop through and make the reports
    ?>

    <!-- start row to show sales by sellers  -->
    <div class="row">
        <div class="col-md-6">

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Service Client
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Vendeur</th>
                                <th>No Commandes</th>
                                <th>Chiffre</th>
                            </tr>

                            <?php
                            $sc_data = $seller_data['SC'];

                            $count = 1;
                             ?>

                             <?php foreach ($sc_data as $key => $value): ?>
                                 <tr>
                                     <td> <?php echo $count++; ?> </td>
                                     <td> <?php echo $value['name']; ?> </td>
                                     <td> <?php echo $value['orders_count']; ?> </td>
                                     <td> <?php echo number_format($value['amount']); ?> </td>
                                 </tr>
                             <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Ambassadors
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Vendeur</th>
                                <th>No Commandes</th>
                                <th>Chiffre</th>
                            </tr>

                            <?php
                            $sc_data = $seller_data['AM'];

                            $count = 1;
                             ?>

                             <?php foreach ($sc_data as $key => $value): ?>
                                 <tr>
                                     <td> <?php echo $count++; ?> </td>
                                     <td> <?php echo $value['name']; ?> </td>
                                     <td> <?php echo $value['orders_count']; ?> </td>
                                     <td> <?php echo number_format($value['amount']); ?> </td>
                                 </tr>
                             <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            </div>

        </div>


    </div>
    <!-- end of row of sales by sellers  -->

    <div class="row">
        <div class="col-md-6">

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        FreeLance
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Vendeur</th>
                                <th>No Commandes</th>
                                <th>Chiffre</th>
                            </tr>

                            <?php
                            $sc_data = $seller_data['FL'];

                            $count = 1;
                             ?>

                             <?php foreach ($sc_data as $key => $value): ?>
                                 <tr>
                                     <td> <?php echo $count++; ?> </td>
                                     <td> <?php echo $value['name']; ?> </td>
                                     <td> <?php echo $value['orders_count']; ?> </td>
                                     <td> <?php echo number_format($value['amount']); ?> </td>
                                 </tr>
                             <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Employees
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Vendeur</th>
                                <th>No Commandes</th>
                                <th>Chiffre</th>
                            </tr>

                            <?php
                            $sc_data = $seller_data['EM'];

                            $count = 1;
                             ?>

                             <?php foreach ($sc_data as $key => $value): ?>
                                 <tr>
                                     <td> <?php echo $count++; ?> </td>
                                     <td> <?php echo $value['name']; ?> </td>
                                     <td> <?php echo $value['orders_count']; ?> </td>
                                     <td> <?php echo number_format($value['amount']); ?> </td>
                                 </tr>
                             <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
