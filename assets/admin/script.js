//initialise all admin js scripts here
jQuery(document).ready(function($){

    $(".datatable").DataTable();
    $(".chosen").chosen();

    $(function () {
        $('[data-toggle="popover"]').popover()
    })

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    //do logic to redirect users where they filter date on dashboard
    $("#filter").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_type = $("#gt_order_type").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date
                            + "&order_type=" + order_type;

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-category").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_type = $("#gt_order_type").val();
        var categories = $("#gt_category").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date
                            + "&order_type=" + order_type;

        if(categories === null)
        {
            alert("Please select the required categories");
            return false;
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < categories.length; i++)
        {
            finalUrl += "&categories%5B%5D=" + categories[i];
        }

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-achat-client").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_statuses = $("#gt_order_status").val();
        var categories = $("#gt_category").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date;

        if(order_statuses === null)
        {
            alert("Please select the required order Status");
            return false;
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < order_statuses.length; i++)
        {
            finalUrl += "&statuses%5B%5D=" + order_statuses[i];
        }

        if(categories === null)
        {
            alert("Please select the required categories");
            return false;
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < categories.length; i++)
        {
            finalUrl += "&categories%5B%5D=" + categories[i];
        }

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-ventes").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_statuses = $("#gt_order_status").val();
        var categories = $("#gt_category").val();

        var max = $("#gt_max_amount").val();
        var min = $("#gt_min_amount").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&max=" + max + "&min=" + min
                            + "&end_date=" + end_date;

        if(order_statuses === null)
        {
            alert("Please select the required order Status");
            return false;
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < order_statuses.length; i++)
        {
            finalUrl += "&statuses%5B%5D=" + order_statuses[i];
        }

        if(categories === null)
        {
            alert("Please select the required categories");
            return false;
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < categories.length; i++)
        {
            finalUrl += "&categories%5B%5D=" + categories[i];
        }

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-operations").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_type = $("#gt_order_type").val();
        var categories = $("#gt_category").val();
        var sellers = $("#gt_seller").val();

        var finalUrl = url + "&start_date=" + start_date
                            + "&end_date=" + end_date
                            + "&order_type=" + order_type;

        if(sellers === null)
        {
            alert('Please select the sellers');
            return false;
        }

        if(categories === null)
        {
            alert("Please select the required categories");
            return false;
        }


        //form the url with the sellers and categories.
        for(var i = 0; i < categories.length; i++)
        {
            finalUrl += "&categories%5B%5D=" + categories[i];
        }

        //form the url with the sellers and categories.
        for(var i = 0; i < sellers.length; i++)
        {
            finalUrl += "&sellers%5B%5D=" + sellers[i];
        }

        //redirec the user to the new url
        window.location.href = finalUrl;
    });

    $("#filter-sellers").click(function(){
        var url = $("#url").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var order_type = $("#gt_order_type").val();
        var sellers = $("#gt_seller").val();

        if(sellers === null)
        {
            alert('Please select the sellers');
        }
        else {
            var finalUrl = url + "&start_date=" + start_date
                                + "&end_date=" + end_date
                                + "&order_type=" + order_type;

            //loop through the sellers. and add them to the url.
            for(var i = 0; i < sellers.length; i++)
            {
                finalUrl += "&sellers%5B%5D=" + sellers[i];
            }

            //redirec the user to the new url
            window.location.href = finalUrl;
        }

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
