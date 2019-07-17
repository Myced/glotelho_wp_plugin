<div class="row">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">
                    Grand Total
                </h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Total Cost Price</th>
                            <th>Selling Price</th>
                            <th>Total</th>
                            <th>Profit</th>
                        </tr>
                        <tr>
                            <th> <?php echo $grandQuantity; ?> </th>
                            <th> <?php echo number_format($grandCostPrice); ?> </th>
                            <th> <?php echo number_format($grandTotalCost); ?> </th>
                            <th> <?php echo number_format($grandSellingPrice); ?> </th>
                            <th> <?php echo number_format($grandTotal); ?> </th>
                            <th> <?php echo number_format($grandProfit); ?> </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
