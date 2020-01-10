<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_seller_orders";

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

//get the sellers and the towns
$towns = SELF::getTowns();
$sellers = self::getSellers();

$selected_sellers = [];

if(isset($_GET['sellers']))
{
    $selected_sellers = $_GET['sellers'];
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
                 <option value="1"
                     <?php echo isset($_GET['order_type']) && ($_GET['order_type'] == '1') ? 'selected' : '' ?>
                     >
                     Commandé en Period
                 </option>
                 <option value="-1"
                     <?php echo isset($_GET['order_type']) && ($_GET['order_type'] == '-1') ? 'selected' : '' ?>
                     >
                     Traité en Period
                 </option>
             </select>
         </div>

         <div class="col-md-3">
             <select class="form-control chosen" multiple id="gt_seller"
                 data-placeholder="Select The Sellers..." >

                     <?php foreach ($sellers as $key => $seller): ?>
                         <option value="<?php echo $key; ?>"
                             <?php echo isset($_GET['sellers'])  && in_array($key, $_GET['sellers']) ? 'selected' : '' ?>
                             >
                             <?php echo $seller; ?>
                         </option>
                     <?php endforeach; ?>
             </select>
         </div>

         <div class="col-md-5">
             <input type="submit" id="filter-sellers" class="btn btn-primary" value="Filter">
             <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                 Réinitialiser à aujourd'hui
             </a>
         </div>
     </div>

     <?php foreach ($selected_sellers as $seller_id): ?>
         <br>
         <br>
         <div class="row">
             <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <strong>
                                <?php echo $sellers[$seller_id]; ?>
                                (<?php
                                //get the seller code
                                $code = get_term_meta($seller_id, 'gt_seller_code', true);
                                echo $code;
                                 ?>)
                            </strong>
                        </h3>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">

                        <div class="table-responsive">

                            <table class="table-bordered table">
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

                                        //initialise the order statuses
                                        $statuses = self::getOrderStatuses();

                                        //calculate the user orders for each status
                                        $user_orders = [];

                                        foreach ($statuses as $status => $name) {
                                            $user_orders[$status] = [
                                                "count" => 0,
                                                "amount" => 0
                                            ];
                                        }

                                    ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php

                                        //process order data.
                                        $town = '';
                                        $seller = '';

                                        $order_data = $order->order_data;

                                        if($order_data != null)
                                        {
                                            $data = unserialize($order_data);

                                            $town_sel = $data['gt_town'];
                                            $seller_sel = $data['gt_seller'];

                                            if($seller_sel == '-1')
                                            {
                                                continue;
                                            }
                                            elseif ($seller_sel != $seller_id)
                                            {
                                                continue;
                                            }
                                            else {

                                                //do all processing.
                                                $seller = $sellers[$seller_sel];
                                            }

                                            if($town_sel != '-1')
                                            {
                                                if(array_key_exists($town_sel, $towns))
                                                {
                                                    $town = $towns[$town_sel];
                                                }
                                            }


                                        }
                                        else {
                                            //do not process this order
                                            continue;
                                        }

                                        //process the order number
                                        $order_no = $order->ID;

                                        if($order->invoice_no != null)
                                        {
                                            $order_no = $order->invoice_no;
                                        }

                                        $amount = $order->total;

                                        $total += $amount;
                                        $ustatus = $order->post_status;

                                        //sum the order details
                                        ++$user_orders[$ustatus]['count'];
                                        $user_orders[$ustatus]['amount'] += $amount;

                                        if($order->post_status != \App\Reports\OrderStatus::CANCELLED
                                                && $order->post_status != \App\Reports\OrderStatus::FAILED
                                                && $order->post_status != \App\Reports\OrderStatus::DRAFT )
                                        {
                                            $realTotal += $amount;
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
                                            <td> #<?php echo $order_no; ?> </td>
                                            <td> <?php echo $order->first_name . ' ' . $order->last_name; ?> </td>
                                            <td> <?php echo $order->tel; ?> </td>
                                            <td> <?php echo self::order_status($order->post_status); ?> </td>
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
                                                        class="btn btn-info btn-xs" target="_blank">
                                                    View
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>

                                    <!-- //show the order status reports  -->
                                    <?php foreach ($user_orders as $key => $value): ?>
                                        <?php
                                        if($value['count'] == 0)
                                            continue;
                                         ?>
                                        <tr>
                                            <th colspan="3" class="text-center">
                                                <?php echo $statuses[$key]; ?>
                                            </th>
                                            <td>
                                                <?php echo $value['count']; ?>
                                            </td>
                                            <td colspan="2">
                                                <strong>
                                                    <?php echo number_format($value['amount']); ?>
                                                </strong>
                                            </td>
                                            <td colspan="5"></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>

                        </div>

                        <div class="box-footer">

                        </div>

                    </div>
                <!-- /.box-body -->
                </div>
             </div>
         </div>
     <?php endforeach; ?>


 </div>
