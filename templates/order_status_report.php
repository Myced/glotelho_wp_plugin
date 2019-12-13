<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_order_statuses";

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

$orders = $manager->get_orders();

$withShipping = [];
$withoutShipping = [];

//initialise the status counts and totals
$statuses = \App\Reports\OrderStatus::allNames();

$withShipping['total'] = [
    'count' => 0,
    'total' => 0
];

$withoutShipping['total'] = [
    'count' => 0,
    'total' => 0
];

foreach($statuses as $status => $value)
{
    if(! array_key_exists($status, $withShipping))
    {
        $withShipping[$status] = [
            'count' => 0,
            'total' => 0
        ];
    }

    if(! array_key_exists($status, $withoutShipping))
    {
        $withoutShipping[$status] = [
            'count' => 0,
            'total' => 0
        ];
    }
}


//loop through the data and filter/
foreach($orders as $order)
{
    $orderStatus = $order->status;
    $total = $order->total;
    $shipping = $order->shipping;

    $subTotal = $total - $shipping;

    //add all totals
    ++$withShipping['total']['count'];
    $withShipping['total']['total'] += $total;

    ++$withoutShipping['total']['count'];
    $withoutShipping['total']['total'] += $total;

    //now save the values
    ++$withShipping[$orderStatus]['count'];
    $withShipping[$orderStatus]['total'] += $total;

    ++$withoutShipping[$orderStatus]['count'];
    $withoutShipping[$orderStatus]['total'] += $total;

}

?>

<div class="wrap">
    <h3>
        Rapport des commandes par statut
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
                >Traité aujourd'hui</option>
                <option value="1"
                <?php echo isset($_GET['order_type']) && ($_GET['order_type'] == '1') ? 'selected' : '' ?>
                >Passé aujourd'hui</option>
            </select>
        </div>

        <div class="col-md-5">
            <input type="submit" id="filter" class="btn btn-primary" value="Filtre">
            <a href="<?php echo $defaultUrl; ?>" class="btn btn-success">
                Réinitialiser à aujourd'hui
            </a>
        </div>
    </div>

    <!-- //now starts the real reports -->
    <br>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-header" style="border-bottom-color: #fff;">Sans Les Frais de Livraison</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-aqua">
                <span class="info-box-icon">
                    <i class="gi gi-euro"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Chiffre D'Affaire (Total)</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping['total']['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping['total']['count']; ?>
                        Commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>

        <!-- awaiting payment -->
        <div class="col-md-4">
            <div class="info-box bg-en-cours" >
                <span class="info-box-icon">
                    <i class="gi gi-clock"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">En Attente De Paiment</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::PENDING]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::PENDING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- awaiting payment -->

        <!-- en cours (processing) -->
        <div class="col-md-4">
            <div class="info-box bg-en-cours">
                <span class="info-box-icon">
                    <i class="gi gi-restart"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">En Cours</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::PROCESSING]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::PROCESSING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of en cours (processing) -->

        <!-- unreachable 1 -->
        <div class="col-md-4">
            <div class="info-box bg-unreachable-1">
                <span class="info-box-icon">
                    <i class="fa fa-phone"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Injoignable 1</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::UNREACHABLE1]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::UNREACHABLE1]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- unreachable 1 -->

        <!-- unreachable 2 -->
        <div class="col-md-4">
            <div class="info-box bg-unreachable-2">
                <span class="info-box-icon">
                    <i class="fa fa-headphones"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Injoignable 2</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::UNREACHABLE2]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::UNREACHABLE2]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of unreachable 2 -->

        <!-- order confirmed -->
        <div class="col-md-4">
            <div class="info-box bg-confirmed">
                <span class="info-box-icon">
                    <i class="gi gi-circle_minus"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Confirmée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::ON_HOLD]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of order confirmed -->

        <!-- planification -->
        <div class="col-md-4">
            <div class="info-box bg-planning">
                <span class="info-box-icon">
                    <i class="fa fa-file"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Planification</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::PLANNING]['total']); ?>
                        FCFA</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::PLANNING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of planification -->

        <!-- commande shipped -->
        <div class="col-md-4">
            <div class="info-box bg-shipped">
                <span class="info-box-icon">
                    <i class="fa fa-truck"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Expédié</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::SHIPPED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of commande shipped -->

        <!-- delivered -->
        <div class="col-md-4">
            <div class="info-box bg-green">
                <span class="info-box-icon">
                    <i class="gi gi-ok_2"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Livrée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::COMPLETED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of delivered orders -->

        <!-- cancelled orders -->
        <div class="col-md-4">
            <div class="info-box bg-red">
                <span class="info-box-icon">
                    <i class="gi gi-circle_remove"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Annulée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::CANCELLED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::CANCELLED]['count']); ?>
                         commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of cancelled orders -->

        <!-- refunds -->
        <div class="col-md-4">
            <div class="info-box bg-refund">
                <span class="info-box-icon">
                    <i class="gi gi-retweet"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Remboursée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::REFUNDED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::REFUNDED]['count']; ?>
                         commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of refunds -->

        <!-- order failed -->
        <div class="col-md-4">
            <div class="info-box bg-red">
                <span class="info-box-icon">
                    <i class="fa fa-exclamation-circle"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Echouée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withoutShipping[\App\Reports\OrderStatus::FAILED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withoutShipping[\App\Reports\OrderStatus::FAILED]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of order failed -->
    </div>

    <br>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-header" style="border-bottom-color: #fff;">Avec Les Frais de Livraison</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="info-box bg-aqua">
                <span class="info-box-icon">
                    <i class="gi gi-euro"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Chiffre D'Affaire (Total)</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping['total']['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping['total']['count']; ?>
                        Commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>

        <!-- awaiting payment -->
        <div class="col-md-4">
            <div class="info-box bg-en-cours" >
                <span class="info-box-icon">
                    <i class="gi gi-clock"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">En Attente De Paiment</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::PENDING]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::PENDING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- awaiting payment -->

        <!-- en cours (processing) -->
        <div class="col-md-4">
            <div class="info-box bg-en-cours">
                <span class="info-box-icon">
                    <i class="gi gi-restart"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">En Cours</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::PROCESSING]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::PROCESSING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of en cours (processing) -->

        <!-- unreachable 1 -->
        <div class="col-md-4">
            <div class="info-box bg-unreachable-1">
                <span class="info-box-icon">
                    <i class="fa fa-phone"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Injoignable 1</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::UNREACHABLE1]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::UNREACHABLE1]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- unreachable 1 -->

        <!-- unreachable 2 -->
        <div class="col-md-4">
            <div class="info-box bg-unreachable-2">
                <span class="info-box-icon">
                    <i class="fa fa-headphones"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Injoignable 2</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::UNREACHABLE2]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::UNREACHABLE2]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of unreachable 2 -->

        <!-- order confirmed -->
        <div class="col-md-4">
            <div class="info-box bg-confirmed">
                <span class="info-box-icon">
                    <i class="gi gi-circle_minus"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Confirmée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::ON_HOLD]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of order confirmed -->

        <!-- planification -->
        <div class="col-md-4">
            <div class="info-box bg-planning">
                <span class="info-box-icon">
                    <i class="fa fa-file"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Planification</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::PLANNING]['total']); ?>
                        FCFA</span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::PLANNING]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of planification -->

        <!-- commande shipped -->
        <div class="col-md-4">
            <div class="info-box bg-shipped">
                <span class="info-box-icon">
                    <i class="fa fa-truck"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Expédié</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::SHIPPED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of commande shipped -->

        <!-- delivered -->
        <div class="col-md-4">
            <div class="info-box bg-green">
                <span class="info-box-icon">
                    <i class="gi gi-ok_2"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Livrée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::COMPLETED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::ON_HOLD]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of delivered orders -->

        <!-- cancelled orders -->
        <div class="col-md-4">
            <div class="info-box bg-red">
                <span class="info-box-icon">
                    <i class="gi gi-circle_remove"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Annulée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::CANCELLED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::CANCELLED]['count']); ?>
                         commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of cancelled orders -->

        <!-- refunds -->
        <div class="col-md-4">
            <div class="info-box bg-refund">
                <span class="info-box-icon">
                    <i class="gi gi-retweet"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Remboursée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::REFUNDED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::REFUNDED]['count']; ?>
                         commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of refunds -->

        <!-- order failed -->
        <div class="col-md-4">
            <div class="info-box bg-red">
                <span class="info-box-icon">
                    <i class="fa fa-exclamation-circle"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Commandes Echouée</span>
                    <span class="info-box-number">
                        <?php echo number_format($withShipping[\App\Reports\OrderStatus::FAILED]['total']); ?>
                        FCFA
                    </span>

                    <div class="progress">
                        <div class="progress-bar" style="width: 80%"></div>
                    </div>

                    <span class="progress-description">
                        <?php echo $withShipping[\App\Reports\OrderStatus::FAILED]['count']; ?>
                        commandes
                    </span>
                </div>

            <!-- /.info-box-content -->
            </div>
        </div>
        <!-- end of order failed -->
    </div>

    <br><br>
    <!-- //row to show the sellers Commandes -->
  <div class="row">
      <div class="col-md-12">

          <div class="box box-info">
              <div class="box-header with-border">
                  <h3 class="box-title">Les Commandes</h3>
              </div>

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
                                  <th>Vendeur</th>
                                  <th>Ville</th>
                                  <th>Commentaire</th>
                                  <th>Action</th>
                              </tr>
                          </thead>

                          <tbody>
                              <?php
                                    $count = 1;
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
                                      <td> <?php echo self::showStatus($order->status); ?> </td>
                                      <td> <?php echo number_format($order->total) . ' FCFA'; ?> </td>
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
              </div>
          <!-- /.box-body -->
          </div>
      </div>
</div>

</div>
