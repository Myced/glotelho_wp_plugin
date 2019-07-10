<?php
$defaultUrl = basename($_SERVER['PHP_SELF']) . "?page=gt_plugin_settings";

?>
<input type="hidden" id="gt_monthly_data" value='<?php echo json_encode($data) ?>'>

<div class="wrap">
    <h3>
        Monthly Orders Report
    </h3>
</div>


<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Orders Count Chart</h3>
                </div>

                <div class="box-body chart-responsive">
                    <div class="chart" id="sales-count-chart" style="height: 400px;"></div>
                </div>
            <!-- /.box-body -->
            </div>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Orders Income Chart</h3>
                </div>

                <div class="box-body chart-responsive">
                    <div class="chart" id="sales-income-chart" style="height: 400px;"></div>
                </div>
            <!-- /.box-body -->
            </div>
        </div>
    </div>
</div>
