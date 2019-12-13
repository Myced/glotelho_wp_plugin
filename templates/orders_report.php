<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_orders_report";

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
        Les Commandes
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
                 Reset to Today
             </a>
         </div>
     </div>

     <br>
     <br>
     <div class="row">
         <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        Order List
                        <small>(Total amount without shipping cost)</small>
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
                                    <th>Client</th>
                                    <th>Telephone</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Seller</th>
                                    <th>Ville</th>
                                    <th>Commentaire</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                    $count = 1;
                                    $total = 0;
                                    $realTotal = 0;

                                      $townss = self::getTowns();
                                      $sellerss = self::getSellers();

                                      $towns = [];
                                      $sellers = [];

                                      foreach($sellerss as $seller)
                                      {
                                          $sellers[$seller->term_id] = $seller;
                                      }

                                      foreach($townss as $town)
                                      {
                                          $towns[$town->term_id] = $town;
                                      }


                                ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    $amount = $order->total - $order->shipping;
                                    $total += $amount;

                                    if($order->post_status != \App\Reports\OrderStatus::CANCELLED
                                            && $order->post_status != \App\Reports\OrderStatus::FAILED
                                            && $order->post_status != \App\Reports\OrderStatus::DRAFT )
                                    {
                                        $realTotal += $amount;
                                    }

                                    //process order data.
                                    $town = '';
                                    $seller = '';

                                    $order_data = $order->order_data;

                                    if($order_data != null)
                                    {
                                        $data = unserialize($order_data);

                                        $town_sel = $data['gt_town'];
                                        $seller_sel = $data['gt_seller'];

                                        if($town_sel != '-1')
                                        {
                                            if(array_key_exists($town_sel, $towns))
                                            {
                                                $town = $towns[$town_sel]->name;
                                            }
                                        }

                                        if($seller_sel != '-1')
                                        {
                                            if(array_key_exists($seller_sel, $sellers))
                                            {
                                                $seller = $sellers[$seller_sel]->name;
                                            }
                                        }
                                    }

                                    $time = date('d, M Y', strtotime($order->post_date)). ' @ '. date('H:i', strtotime($order->post_date));

                                     ?>
                                    <tr>
                                        <td> <?php echo $count++; ?> </td>
                                        <td>
                                            <a href="javascript:void(0)"
                                              data-toggle="tooltip"
                                              title="<?php echo $time; ?>"
                                              style="color: #333;">
                                              <?php echo date("d, M Y", strtotime($order->post_date)); ?>
                                          </a>
                                        </td>
                                        <td> Ord #<?php echo $order->ID; ?> </td>
                                        <td> <?php echo $order->first_name . ' ' . $order->last_name; ?> </td>
                                        <td> <?php echo $order->tel; ?> </td>
                                        <td> <?php echo self::showStatus($order->post_status); ?> </td>
                                        <td> <?php echo number_format($amount) . ' FCFA'; ?> </td>
                                        <td> <?php echo $seller; ?> </td>
                                        <td> <?php echo $town; ?> </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs" data-toggle="popover"
                                              title="Commentaire du Commande"
                                              data-content="<?php echo $order->comment; ?>"
                                              data-placement="top">
                                                Commentaire
                                            </button>
                                        </td>
                                        <td>
                                            <a href="post.php?post=<?php echo $order->ID; ?>&action=edit"
                                                    class="btn btn-info btn-xs">
                                                View
                                            </a>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>

                    </div>

                    <div class="box-footer">
                        <h4 class="box-title">
                            Total : <strong> <?php echo number_format($total); ?>  FCFA</strong>

                            &nbsp;  &nbsp; &nbsp; &nbsp;
                            Total (- Cancelled) :
                                <strong> <?php echo number_format($realTotal); ?>  FCFA</strong>
                        </h4>
                    </div>

                </div>
            <!-- /.box-body -->
            </div>
         </div>
     </div>

 </div>
