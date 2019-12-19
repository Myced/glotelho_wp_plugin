<?php

$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_freelance_report";

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

if(isset($_GET['download']))
{
    if($_GET['download'] == true)
    {
        require_once GT_BASE_DIRECTORY . '/templates/freelance_report_download.php';
    }
}
?>

<div class="wrap">
    <h3>
        Commissions FreeLance
    </h3>
</div>

 <div class="content">

     <input type="hidden" id="url" value="<?php echo $defaultUrl; ?>">

     <?php require_once GT_BASE_DIRECTORY . '/templates/excel_download_btn.php'; ?>


     <br>


     <?php foreach ($sellers as $key => $value): ?>

         <?php
             $order_total = 0;
             $commission = 0;
          ?>
         <br>
         <div class="row">
             <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <?php echo $value['name']; ?>
                            <strong>(<?php echo $key; ?>)</strong>
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
                                        <th>Commission.</th>
                                        <th>Commentaire</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    //ge the sellers orders
                                    $orders = $manager->get_orders($key);
                                    $count = 1;
                                    ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php

                                        $amount = $order->total - $order->shipping;
                                        $comm  = (5/100) * $amount;


                                        if($order->post_status != \App\Reports\OrderStatus::CANCELLED
                                                && $order->post_status != \App\Reports\OrderStatus::FAILED
                                                && $order->post_status != \App\Reports\OrderStatus::DRAFT )
                                        {
                                            $order_total += $amount;
                                            $commission += $comm;
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
                                            <td> <?php echo number_format($comm) . 'FCFA'; ?> </td>
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
                                Total : <strong> <?php echo number_format($order_total); ?>  FCFA</strong>

                                &nbsp;  &nbsp; &nbsp; &nbsp;
                                Total Commission :
                                    <strong> <?php echo number_format($commission); ?>  FCFA</strong>
                            </h4>
                        </div>

                    </div>
                <!-- /.box-body -->
                </div>
             </div>
         </div>
     <?php endforeach; ?>

 </div>
