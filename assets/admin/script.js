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

    //initialise datepicker
    //Date picker
    $('.datepicker').cdatepicker({
        format: 'yyyy-mm-dd',
        autoclose: true
    })

    var barData = $.parseJSON($("#bar-data").val());
    var pieData = $.parseJSON($("#pie-data").val());

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
