<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_accounting_report";

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

$payment_methods = [
    "MOMO" => "MTN Mobile Money",
    "ORANGE" => "Orange Money",
    "CASH" => "CASH",
    "YDE" => "YAOUNDE",
    "CHEQUE" => "CHEQUE",
    "CARD" => "CARD",
    "SHOWROOM" => "SHOWROOM"
];

//since there are almost 46 categories and we need only a few.
//let me whitelist categories to be shown.
$whiteList = [
    '3210', //PRODUITS POUR BÉBÉS
    '3202', //Sécurité/Télécom
    '1426', //BUREAUX & MAISON
    '1483', //ELECTROMENAGER
    '1393', //TELECOMS
    '1394', //INFORMATIQUE
    '1523', //PRODUITS NATURELS
    '1390', //SÉCURITÉ ELECTRONIQUE
    '1387', //TELEPHONES & TABLETTES
    '16', //clothing
    '77' //laptops
];

foreach($categories as $category)
{
    $term_ids    = get_term_children( $category->term_id, 'product_cat' );
    $term_ids[]  = $category->term_id;
    $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

    array_push($cat_ids, $category->term_id);

    $category_products[$category->term_id] = $product_ids;
}


function get_product_categories($product_id, $categories, $category_products)
{
    $name = "";
    $all = [];

    foreach ($categories as $category)
    {

        $ids = $category_products[$category->term_id];

        if(in_array($product_id, $ids))
        {
            array_push($all, $category->name);
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
$cat_branch_data = [];
$cat_branch_data['DLA'] = [];
$cat_branch_data['YDE'] = [];

foreach($categories as $category)
{
    $category_data[$category->term_id] = [
        'name' => $category->name,
        'cost' => 0,
        'total' => 0,
        'marge' => 0
    ];

    $cat_branch_data['DLA'][$category->term_id] = [
        'name' => $category->name,
        'cost' => 0,
        'total' => 0,
        'marge' => 0
    ];

    $cat_branch_data['YDE'][$category->term_id] = [
        'name' => $category->name,
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


                //now affect the required categories
                $p_cost_price = $product['cost_price'] * $product['quantity'];
                $p_total = $product['product_total'];
                $marge = $product['profit'];

                $town = $product['town'];

                foreach($affected_categories as $aff_cat)
                {
                    $category_data[$aff_cat]['cost'] += $p_cost_price;
                    $category_data[$aff_cat]['total'] += $p_total;
                    $category_data[$aff_cat]['marge'] += $marge;

                    //calculate by branch
                    //check the order region and calculate acordingly

                    //if the town is yaounde
                    if($town == "Yaounde")
                    {
                        $cat_branch_data['YDE'][$aff_cat]['cost'] += $p_cost_price;
                        $cat_branch_data['YDE'][$aff_cat]['total'] += $p_total;
                        $cat_branch_data['YDE'][$aff_cat]['marge'] += $marge;
                    }
                    else {
                        $cat_branch_data['DLA'][$aff_cat]['cost'] += $p_cost_price;
                        $cat_branch_data['DLA'][$aff_cat]['total'] += $p_total;
                        $cat_branch_data['DLA'][$aff_cat]['marge'] += $marge;
                    }

                }

                $sub_current_order = $gt_current_order;

                 ?>
                    <?php
                    if($isDateRow == true)
                    {
                        $isDateRow = false;

                    }
                     ?>

                     <?php
                     if($isOrderRow == true)
                     {
                         $isOrderRow = false;
                         ?>

                         <?php
                     }
                      ?>

            <?php endforeach; ?>

        <?php endforeach; ?>

        <!-- //show the details for the date -->

    <?php endforeach; ?>

    <!-- category sales Yaounde branch report row  -->
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <strong>
                            Statistics Yaounde Branch
                        </strong>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Categorie</th>
                                <th>Coût</th>
                                <th>Total</th>
                                <th>Marge</th>
                            </tr>

                            <?php
                                $count = 1;
                                $cat_cost = 0;
                                $cat_sales = 0;
                                $cat_marge = 0;
                            ?>
                            <?php foreach ($cat_branch_data['YDE'] as $key => $value): ?>
                                <?php
                                //only show whitelisted categories
                                if(! in_array($key, $whiteList))
                                    continue;

                                $cat_cost += $value['cost'];
                                $cat_sales += $value['total'];
                                $cat_marge += $value['marge'];
                                 ?>
                                <tr>
                                    <th> <?php echo $count++; ?> </th>
                                    <th> <?php echo $value['name']; ?> </th>
                                    <td> <?php echo number_format($value['cost']); ?> </td>
                                    <td> <?php echo number_format($value['total']); ?> </td>
                                    <td> <?php echo number_format($value['marge']); ?> </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="2" class="text-center">
                                    TOTAL/GROSS PROFIT
                                </th>
                                <th>
                                    <?php echo number_format($cat_cost); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($cat_sales); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($cat_marge); ?> FCFA
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end of category report row -->


    <!-- sales Douala and other branches -->
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <strong>
                            Sales Douala and Other Branches
                        </strong>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Categorie</th>
                                <th>Coût</th>
                                <th>Total</th>
                                <th>Marge</th>
                            </tr>

                            <?php
                                $count = 1;
                                $cat_cost = 0;
                                $cat_sales = 0;
                                $cat_marge = 0;
                            ?>
                            <?php foreach ($cat_branch_data['DLA'] as $key => $value): ?>
                                <?php
                                //only show whitelisted categories
                                if(! in_array($key, $whiteList))
                                    continue;

                                $cat_cost += $value['cost'];
                                $cat_sales += $value['total'];
                                $cat_marge += $value['marge'];

                                 ?>
                                <tr>
                                    <th> <?php echo $count++; ?> </th>
                                    <th> <?php echo $value['name']; ?> </th>
                                    <td> <?php echo number_format($value['cost']); ?> </td>
                                    <td> <?php echo number_format($value['total']); ?> </td>
                                    <td> <?php echo number_format($value['marge']); ?> </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="2" class="text-center">
                                    TOTAL/GROSS PROFIT
                                </th>
                                <th>
                                    <?php echo number_format($cat_cost); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($cat_sales); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($cat_marge); ?> FCFA
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end of sales Douala and other branches -->

    <!-- General Sales Ecommerce -->
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <strong>
                            General Sales Ecommerce
                        </strong>
                    </h3>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>S/N</th>
                                <th>Categorie</th>
                                <th>Coût</th>
                                <th>Total</th>
                                <th>Marge</th>
                            </tr>

                            <?php
                                $count = 1;
                                $gen_costs = 0;
                                $gen_sales = 0;
                                $gen_marge = 0;
                            ?>
                            <?php foreach ($category_data as $key => $value): ?>
                                <?php
                                //only show whitelisted categories
                                if(! in_array($key, $whiteList))
                                    continue;

                                $gen_costs += $value['cost'];
                                $gen_sales += $value['total'];
                                $gen_marge += $value['marge'];

                                 ?>
                                <tr>
                                    <th> <?php echo $count++; ?> </th>
                                    <th> <?php echo $value['name']; ?> </th>
                                    <td> <?php echo number_format($value['cost']); ?> </td>
                                    <td> <?php echo number_format($value['total']); ?> </td>
                                    <td> <?php echo number_format($value['marge']); ?> </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="2" class="text-center">
                                    TOTAL/GROSS PROFIT
                                </th>
                                <th>
                                    <?php echo number_format($gen_costs); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($gen_sales); ?> FCFA
                                </th>
                                <th>
                                    <?php echo number_format($gen_marge); ?> FCFA
                                </th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- End of General Sales Ecommerce -->

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


</div>
