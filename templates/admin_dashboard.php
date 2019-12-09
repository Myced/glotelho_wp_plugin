<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_plugin_settings";

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
        Glotelho Report Dashboard
        (<?php
            if(isset($_GET['start_date']))
                echo $start_date . ' - ' . $end_date;
            else {
                echo "Today";
            }
        ?>)
    </h3>
</div>

<!-- //hidden form fields for chart data  -->
<input type="hidden" id="pie-data" value='<?php echo json_encode($pieChart); ?>'>
<input type="hidden" id="bar-data" value='<?php echo json_encode($barChart); ?>'>

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
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua">
                <i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Orders</span>
              <span class="info-box-number">
                  <?php echo $header_data['total']; ?>
                  <br>
                  <?php echo number_format($header_data['total_cost']) . ' FCFA'; ?>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green">
                <i class="fa fa-check"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">Delivered</span>
              <span class="info-box-number">
                  <?php echo $header_data['completed']['count']; ?>
                  <br>
                  <?php echo number_format($header_data['completed']['total']) . ' FCFA'; ?>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow">
                <i class="fa fa-clock"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">Pending</span>
              <span class="info-box-number">
                  <?php echo $header_data['pending']['count']; ?>
                  <br>
                  <?php echo number_format($header_data['pending']['total']) . ' FCFA'; ?>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red">
                <i class="fa fa-times"></i>
            </span>

            <div class="info-box-content">
              <span class="info-box-text">Cancelled</span>
              <span class="info-box-number">
                  <?php echo $header_data['cancelled']['count']; ?>
                  <br>
                  <?php echo number_format($header_data['cancelled']['total']) . ' FCFA'; ?>
              </span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- end of row -->

      <!-- row for stats without shipping -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>
                  <?php echo number_format($header_san_shipping['total_cost']) . ' F'; ?>
              </h3>

              <p>Total Orders Value</p>
            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>
                  <?php echo number_format($header_san_shipping['completed']['total']) . ' F'; ?>
              </h3>

              <p>Delivered Orders Value</p>
            </div>
            <div class="icon">
              <i class="fa fa-check"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>
                  <?php echo number_format($header_san_shipping['pending']['total']) . ' F'; ?>
              </h3>

              <p>Pending Order Value</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>
                  <?php echo number_format($header_san_shipping['cancelled']['total']) . ' F'; ?>
              </h3>

              <p>Cancelled Orders</p>
            </div>
            <div class="icon">
              <i class="fa fa-times"></i>
            </div>
            <a href="javascript:void(0)" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- end of row for stats without shipping -->

      <!-- start the row -->
        <div class="row">
            <div class="col-md-6">
            <!-- AREA CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Orders Count</h3>

                    </div>

                    <div class="box-body chart-responsive">
                        <div class="chart" id="orders-pie" style="height: 300px;"></div>
                    </div>
                <!-- /.box-body -->
                </div>
            <!-- /.box -->
            </div>

            <div class="col-md-6">
            <!-- AREA CHART -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Orders Income Chart</h3>
                    </div>

                    <div class="box-body chart-responsive">
                        <div class="chart" id="sales-bar-chart" style="height: 300px;"></div>
                    </div>
                <!-- /.box-body -->
                </div>
            <!-- /.box -->
            </div>
        </div>
      <!-- end of row -->

      <!-- row for category stats -->
      <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">
                  Category Report
                  <small>(Without Shipping and excluding cancelled orders)</small>
              </h3>


            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>S/N</th>
                    <th>Category</th>
                    <th>N<sup>o</sup> of Orders  </th>
                    <th>Qty Bought</th>
                    <th>Total</th>
                  </tr>
                  </thead>
                  <tbody>

                      <?php
                      //prendre tous les category
                      $categoriesMixed = get_terms('product_cat', ['hide_empty' => false ]);

                      $categories = [];

                      foreach($categoriesMixed as $cat)
                      {
                          if($cat->parent == 0 && $cat->slug != 'uncategorized')
                          {
                              //this category is a top level category
                              array_push($categories, $cat);
                          }
                      }



                      $count = 1;
                      foreach ($categories as $category) {

                          $catData = $manager->getCategoryData($category->term_id);

                          ?>
                          <tr>
                            <td> <?php echo $count++; ?> </td>
                            <td> <?= $category->name; ?> </td>
                            <td>
                                <?php echo $catData['order_count']; ?>
                            </td>
                            <td>
                              <?= number_format($catData['order_total']) . ' FCFA'; ?>
                            </td>
                          </tr>
                          <?php
                      }
                      ?>


                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
            </div>
            <!-- /.box-footer -->
          </div>
        </div>
      </div>
      <!-- end of row for category stats -->

      <!-- next row for stats by user / ambassadors -->
      <div class="row">
          <div class="col-md-6">
              <div class="box box-primary">
                  <div class="box-header with-border">
                      <h3 class="box-title">Orders By Users</h3>
                  </div>

                  <div class="box-body chart-responsive">
                      <table class="table table-bordered">
                          <tr>
                              <th>S/N</th>
                              <th>User</th>
                              <th>Total Orders</th>
                              <th>Amount</th>
                          </tr>

                          <?php $count = 1; ?>
                          <?php foreach (self::getSellers() as $seller): ?>
                              <tr>
                                  <td> <?php echo $count++; ?> </td>
                                  <td> <?php echo $seller->name; ?> </td>
                                  <td> <?php echo $sellerData[$seller->term_id]['count']; ?> </td>
                                  <td> <?php echo number_format($sellerData[$seller->term_id]['total']) . ' FCFA'; ?> </td>
                              </tr>
                          <?php endforeach; ?>
                      </table>
                  </div>
              <!-- /.box-body -->
              </div>

              <!-- order by region -->
              <div class="box box-warning">
                  <div class="box-header with-border">
                      <h3 class="box-title">Orders By Region</h3>

                  </div>

                  <div class="box-body chart-responsive">
                      <table class="table table-bordered">
                          <tr>
                              <th>S/N</th>
                              <th>Region</th>
                              <th>Total Orders</th>
                              <th>Amount</th>
                          </tr>

                          <?php $count = 1; ?>
                          <?php foreach (self::getRegions() as $region): ?>
                              <tr>
                                  <td> <?php echo $count++; ?> </td>
                                  <td> <?php echo $region->name; ?> </td>
                                  <td> <?php echo $regionData[$region->term_id]['count']; ?> </td>
                                  <td> <?php echo number_format($regionData[$region->term_id]['total']) . ' FCFA'; ?> </td>
                              </tr>
                          <?php endforeach; ?>

                      </table>
                  </div>
              <!-- /.box-body -->
              </div>
              <!-- order by region -->
          </div>

          <div class="col-md-6">
              <div class="box box-primary">
                  <div class="box-header with-border">
                      <h3 class="box-title">Orders By Status</h3>
                  </div>

                  <div class="box-body chart-responsive">
                      <table class="table table-bordered">
                          <tr>
                              <th>S/N</th>
                              <th>Status</th>
                              <th>Total Orders</th>
                              <th>Amount</th>
                          </tr>

                          <tr>
                              <td>1</td>
                              <td>Completed</td>
                              <td><?php echo $header_data['completed']['count']; ?></td>
                              <td> <?php echo $header_san_shipping['completed']['total'] . ' FCFA'; ?> </td>
                          </tr>
                          <tr>
                              <td>2</td>
                              <td>Pending</td>
                              <td> <?php echo $header_data['pending']['count']; ?> </td>
                              <td> <?php echo $header_san_shipping['pending']['total'] . ' FCFA'; ?> </td>
                          </tr>
                          <tr>
                              <td>3</td>
                              <td>Cancelled</td>
                              <td> <?php echo $header_data['cancelled']['count']; ?> </td>
                              <td> <?php echo $header_san_shipping['cancelled']['total'] . ' FCFA'; ?> </td>
                          </tr>
                      </table>
                  </div>
              <!-- /.box-body -->
              </div>

              <!-- //order by towns  -->
              <div class="box box-warning">
                  <div class="box-header with-border">
                      <h3 class="box-title">Orders By Towns</h3>

                  </div>

                  <div class="box-body chart-responsive">
                      <table class="table table-bordered">
                          <tr>
                              <th>S/N</th>
                              <th>Town</th>
                              <th>Total Orders</th>
                              <th>Amount</th>
                          </tr>

                          <?php $count = 1; ?>
                          <?php foreach (self::getTowns() as $town): ?>
                              <tr>
                                  <td> <?php echo $count++; ?> </td>
                                  <td> <?php echo $town->name; ?> </td>
                                  <td> <?php echo $townData[$town->term_id]['count']; ?> </td>
                                  <td> <?php echo number_format($townData[$town->term_id]['total']) . ' FCFA'; ?> </td>
                              </tr>
                          <?php endforeach; ?>

                      </table>
                  </div>
              <!-- /.box-body -->
              </div>
              <!-- end of orders by towns  -->
          </div>
      </div>
      <!-- end of row for stats by user  -->

      <!-- row for the payment methods -->
      <div class="row">
          <div class="col-md-6">
              <div class="box box-warning">
                  <div class="box-header with-border">
                      <h3 class="box-title">Payment Methods</h3>

                  </div>

                  <div class="box-body chart-responsive">
                      <table class="table table-bordered">
                          <tr>
                              <th>S/N</th>
                              <th>Method</th>
                              <th>Total Orders</th>
                              <th>Amount</th>
                          </tr>

                          <?php $count = 1; ?>
                          <?php foreach (self::getPaymentMethods() as $method): ?>
                              <tr>
                                  <td> <?php echo $count++; ?> </td>
                                  <td> <?php echo $method->method_title; ?> </td>
                                  <td>
                                      <?php
                                      if(array_key_exists($method->id, $paymentData))
                                      {
                                          echo $paymentData[$method->id]['count'];
                                      }
                                      else {
                                          echo '0';
                                      }
                                      ?>
                                  </td>
                                  <td>
                                      <?php
                                      if(array_key_exists($method->id, $paymentData))
                                      {
                                          echo number_format($paymentData[$method->id]['total']) . ' FCFA';
                                      }
                                      else {
                                          echo '0 FCFA';
                                      }
                                      ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>

                      </table>
                  </div>
              <!-- /.box-body -->
              </div>
          </div>
      </div>
      <!-- end of payment by method -->
</div>
