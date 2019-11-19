<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_top_sellers";

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
        Top Produit
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
             <input type="text" name="quantity" value="<?php echo $manager->default_quantity; ?>"
                class="form-control" id="quantity" placeholder="Quantite de default" required>
         </div>

         <div class="col-md-5">
             <input type="submit" id="filter-top-sellers" class="btn btn-primary" value="Filter">
             <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                 Reset to Today
             </a>
         </div>
     </div>

     <br><br>
     <div class="row">
         <div class="col-md-12">

             <div class="box box-info">
                 <div class="box-header with-border">
                     <h3 class="box-title">
                         Les top Produit
                     </h3>
                 </div>

                 <div class="box-body">
                     <div class="table-responsive">

                         <table class="table table-hover">
                             <tr>
                                 <th>S/N</th>
                                 <th style="min-width: 300px;">Produit</th>
                                 <th>No des Commandes</th>
                                 <th>Quantite Vendu</th>
                                 <th>Chiffre d'Affaire</th>
                             </tr>
                             <tr>
                                 <td>1</td>
                                 <td>Huawei Y9 blue</td>
                                 <td>3</td>
                                 <td>5</td>
                                 <td>1,200,000</td>
                             </tr>
                         </table>

                     </div>
                 </div>

             </div>

         </div>
     </div>

 </div>
