<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_ventes_report";


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



//required variables
$categories = self::getCategories();
$statuses = self::getStatuses();


if(isset($_GET['download']))
{
    require_once GT_BASE_DIRECTORY . '/templates/ventes_reports_download.php';
}

?>

<div class="wrap">
    <h3>
        Ventes
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
             <select class="form-control chosen" multiple id="gt_category"
                 data-placeholder="Choose the categories needed">
                 <option value="-1"
                 <?php
                    if(isset($_GET['categories']))
                    {
                        if(in_array('-1', $_GET['categories']))
                            echo 'selected';
                    }
                    ?>>
                     Tous Les Categories
                 </option>

                 <?php foreach ($categories as $category): ?>
                     <option value="<?php echo $category->term_id ?>"
                         <?php
                         if(isset($_GET['categories']))
                         {
                             if(in_array($category->term_id, $_GET['categories']))
                                 echo 'selected';
                         }  ?>>
                         <?php echo $category->name; ?>
                     </option>
                 <?php endforeach; ?>
             </select>
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

     </div>

     <br>
     <div class="row">
         <div class="col-md-2">
             <input type="text" name="start_date" value="<?php echo $min; ?>"
                 class="form-control" id="gt_min_amount"
                 placeholder="Prix Minimum">
         </div>

         <div class="col-md-2">
             <input type="text" name="end_date" value="<?php echo $max; ?>"
                 class="form-control" id="gt_max_amount"
                 placeholder="Prix Maximum">
         </div>
     </div>

     <br>
     <div class="row">
         <div class="col-md-12 text-center">
             <input type="submit" id="filter-ventes" class="btn btn-primary" value="Filter">
             <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                 Reset to Today
             </a>
         </div>
     </div>

     <br>
     <?php
     if(isset($_GET['categories']))
     {
         //include download button
         require GT_BASE_DIRECTORY . '/templates/excel_download_btn.php';
     }
     ?>

     <br>
     <br>
     <div class="row">
         <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Liste des Clients et Achats
                    </h3>
                </div>

            <!-- /.box-header -->
                <div class="box-body">

                    <div class="table-responsive">

                        <table class="table-bordered table datatable">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Date</th>
                                    <th>Order No</th>
                                    <th>Status</th>
                                    <th>Client</th>
                                    <th>Telephone</th>
                                    <th>Produit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php $count = 1; ?>
                                <?php foreach ($data as $order): ?>
                                    <tr>
                                        <td> <?php echo $count++; ?> </td>
                                        <td> <?php echo date("d, M Y", strtotime($order['date'])); ?> </td>
                                        <td> Ord #<?php echo $order['order_no']; ?> </td>
                                        <td> <?php echo self::showStatus($order['order_status']); ?> </td>
                                        <td> <?php echo $order['client_name']; ?> </td>
                                        <td> <?php echo $order['client_tel']; ?> </td>
                                        <td> <?php echo $order['product_name']; ?> </td>
                                        <td> <?php echo number_format($order['total']); ?> </td>
                                    </tr>
                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                </div>
            <!-- /.box-body -->
            </div>
         </div>
     </div>

 </div>
