<?php
// we are here for the accounting part;

$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_accounting_cat";

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

$cat_products_items = [];

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
$whiteList = \App\Base\WhiteList::categories();

foreach($categories as $category)
{
    $term_ids    = get_term_children( $category->term_id, 'product_cat' );
    $term_ids[]  = $category->term_id;
    $product_ids = get_objects_in_term( $term_ids, 'product_cat' );

    array_push($cat_ids, $category->term_id);

    $category_products[$category->term_id] = $product_ids;

    if(in_array($category->term_id, $whiteList))
    {
        $cat_products_items[$category->term_id] = [];

        $cat_products_items[$category->term_id]['name'] = $category->name;
        $cat_products_items[$category->term_id]['items'] = [];
    }
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

 <?php
 ////////////////////////////////////////////
 // DATA PROCESSING /////////////////////////
 ////////////////////////////////////////////
 foreach ($data as $currentDate => $date) {

     foreach ($date as $currentOrder => $order){

         foreach ($order as $product) {

             $item = [];

             $item['name'] = $product['name'];
             $item['quantity'] = $product['quantity'];
             $item['unit_cost'] = $product['cost_price'];
             $item['total_cost'] = $product['quantity'] * $product['cost_price'];
             $item['product_total']= $product['product_total'];
             $item['profit'] = $product['profit'];

             //get the product category and add it to the right category
             $product_id = $product['product_id'];

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
                     if(in_array($product_id, $cat_products))
                     {
                         array_push($prod_cats, $cur_cat_id);
                     }
                 }

                 $product_cats[$product['product_id']] = $prod_cats;

                 $affected_categories = $prod_cats;
             }

             //for all the affected categories..
             //add the product to their list
            foreach ($affected_categories as $acat)
            {
                if(in_array($acat, $whiteList))
                {
                    array_push($cat_products_items[$acat]['items'], $item);
                }
            }

         }

     }

 }
  ?>

 <div class="wrap">
     <h3>
         COMPTABILITE CATEGORIE
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

             <?php foreach ($cat_products_items as $key => $pcat): ?>
                 <div class="box box-info">
                     <div class="box-header with-border">
                         <h3 class="box-title">
                             <?php echo $pcat['name']; ?>
                         </h3>
                     </div>

                     <div class="box-body">
                         <div class="table-responsive">
                             <table class="table table-bordered" style="width: 1000px;">
                                 <tr>
                                     <th style="min-width: 10px"> S/N </th>
                                     <th style="min-width: 300px;">Produit</th>
                                     <th style="min-width: 40px">Qte</th>
                                     <th style="min-width: 120px;">Prix Unitaire (PU)</th>
                                     <th style="min-width: 120px;">Prix Reviendre (PR)</th>
                                     <th style="min-width: 120px;">Prix Total (PT)</th>
                                     <th style="min-width: 100px;">Marge</th>
                                 </tr>

                                 <?php
                                 $count = 1;
                                 $quantity = 0;
                                 $unit_cost = 0;
                                 $cost_price = 0;
                                 $selling_price =0;
                                 $marge = 0;
                                  ?>
                                 <?php foreach ($pcat['items'] as $item): ?>
                                     <?php
                                     $quantity += $item['quantity'];
                                     $unit_cost += $item['unit_cost'];
                                     $cost_price += $item['total_cost'];
                                     $selling_price += $item['product_total'];
                                     $marge += $item['profit'];

                                      ?>
                                     <tr>
                                         <td> <?php echo $count++; ?> </td>
                                         <td> <?php echo $item['name']; ?> </td>
                                         <td> <?php echo $item['quantity']; ?> </td>
                                         <td> <?php echo number_format($item['unit_cost']); ?> </td>
                                         <td> <?php echo number_format($item['total_cost']); ?> </td>
                                         <td> <?php echo number_format($item['product_total']); ?> </td>
                                         <td> <?php echo number_format($item['profit']); ?> </td>
                                     </tr>
                                 <?php endforeach; ?>

                                 <tr>
                                     <th style="text-align: center" colspan="2">Totals</th>
                                     <th> <?php echo number_format($quantity); ?> </th>
                                     <th> <?php echo number_format($unit_cost); ?> </th>
                                     <th> <?php echo number_format($cost_price); ?> </th>
                                     <th> <?php echo number_format($selling_price); ?> </th>
                                     <th> <?php echo number_format($marge); ?> </th>
                                 </tr>

                                 <!-- //show the details for the date -->


                             </table>
                         </div>
                     </div>
                 <!-- /.box-body -->
                 </div>
             <?php endforeach; ?>

         </div>
     </div>


 </div>
