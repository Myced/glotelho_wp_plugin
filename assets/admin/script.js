//initialise all admin js scripts here
jQuery(document).ready(function($){

    $(".datatable").DataTable();

    //do logic to redirect users where they filter date on dashboard
    $("#filter").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date;

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-category").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var category = $("#gt_category option:selected").val();

        console.log(category);

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date
                            + "&category=" + category;

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    //initialise datepicker
    //Date picker
    $('.datepicker').cdatepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    })

    var barData = $.parseJSON($("#bar-data").val());
    var pieData = $.parseJSON($("#pie-data").val());

    if(jQuery("#gt_monthly_data").val() != undefined)
    {
        //initaalise monthly data
        $data = jQuery.parseJSON(jQuery("#gt_monthly_data").val());

        var monthlyCount = [];
        var monthlyIncome = [];

        for(var i = 1; i <= 12; i++ )
        {
            var current = $data[i];

            var currentMonthIncome = {
                y: current.name,
                a: current.total
            }

            var currentMonthCount = {
                y: current.name,
                a: current.count
            }

            monthlyCount.push(currentMonthCount);
            monthlyIncome.push(currentMonthIncome);

        }

        //initialise the graphs.
        //start with the count
        new Morris.Bar({
          element: 'sales-count-chart',
          resize: true,
          data: monthlyCount,
          barColors: ['#3c8dbc'],
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Sales Count'],
          hideHover: 'auto'
        });


        //intialise for income
        new Morris.Bar({
          element: 'sales-income-chart',
          resize: true,
          data: monthlyIncome,
          barColors: ['#3c8dbc'],
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Sales Income'],
          hideHover: 'auto'
        });
    }

    //DONUT CHART
    if(barData !== null)
    {
        var donut = new Morris.Donut({
          element: 'orders-pie',
          resize: true,
          colors: ["#3c8dbc", "#f56954", "#00a65a"],
          data: [
            {label: "Pending Orders", value: pieData.pending},
            {label: "Cancelled Orders", value: pieData.cancelled},
            {label: "Completed Orders", value: pieData.completed}
          ],
          hideHover: 'auto'
        });

        //BAR CHART
        var bar = new Morris.Bar({
          element: 'sales-bar-chart',
          resize: true,
          data: [
            {y: 'Pending', a: barData.pending },
            {y: 'Completed', a: barData.completed },
            {y: 'Cancelled', a: barData.cancelled },
          ],
          barColors: ['#3c8dbc'],
          xkey: 'y',
          ykeys: ['a'],
          labels: ['Sales'],
          hideHover: 'auto'
        });
    }


});
