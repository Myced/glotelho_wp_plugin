<?php
if($_GET['download'] == true)
{
    ob_end_clean();

    $full_data = [];

    //set the document properties.
    // Set document properties
    $spreadsheet->getProperties()->setCreator('TN CEDRIC')
        ->setLastModifiedBy('TN CEDRIC')
        ->setTitle('Office 2007 XLSX Glotelho Report')
        ->setSubject('Office 2007 XLSX Sales Report')
        ->setDescription('Glotelho Ecommerce Report')
        ->setKeywords('office 2007 openxml php')
        ->setCategory('Excel Document');

    //get the active sheet
    $sheet = $spreadsheet->getActiveSheet();

    if(isset($_GET['start_date']))
    {
        $date_period = $start_date . ' - ' . $end_date;
    }
    else {
        $date_period = "Today (" . date("d/M/Y") . ")";
    }

    //push the headings
    $h1 = [ "Glotelho Sales Report" ];
    array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //set the headers
    $row = [
        "Date", "Order #", "Status", "Product", "Quantity",
        "Unit Price (PU)", "Cost Price (PR)", "Total Price (PT)", "Profit",
        "Seller", "Town", "Comment"
    ];
    array_push($full_data, $row);

    //intialise the grand totals
    $grandQuantity = 0;
    $grandCostPrice = 0;
    $grandTotalCost = 0;
    $grandSellingPrice = 0;
    $grandTotalTotal = 0;
    $grandProfit = 0;

    //go through the results and put them into the array
    //get the data and loop through it.
    foreach($data as $date => $dates)
    {
        $dateUsed = false;
        $dateTotal = ["", "", "Date Total", "", 0, 0, 0, 0, 0];
        $currentDate = $date;
        $currentOrder = "";

        foreach($dates as $order => $orders)
        {
            $orderUsed = false;

            foreach($orders as $product)
            {
                if($dateUsed == false)
                {
                    $dateUsed = true;
                    $mydate = $date;
                }
                else {
                    $mydate = "";
                }

                if($orderUsed == false)
                {
                    $orderUsed = true;
                    $myorder = $order;
                    $myorderstatus = get_order_status($product['order_status']);
                }
                else {
                    $myorder = "";
                    $myorderstatus = "";
                }

                $dateTotal[4] += $product['quantity'];
                $dateTotal[5] += $product['cost_price'];
                $dateTotal[6] += $product['cost_price'] * $product['quantity'];
                $dateTotal[7] += $product['product_total'];
                $dateTotal[8] += $product['profit'];

                $grandQuantity += $product['quantity'];
                $grandCostPrice += $product['cost_price'];
                $grandTotalCost += $product['quantity'] * $product['cost_price'];
                $grandTotalTotal += $product['product_total'];
                $grandProfit += $product['profit'];

                $row = [
                    $mydate,
                    $myorder,
                    $myorderstatus,
                    $product['name'],
                    $product['quantity'],
                    $product['cost_price'],
                    $product['quantity'] * $product['cost_price'],
                    $product['product_total'],
                    $product['profit'],
                    $product['seller'],
                    $product['town'],
                    $product['order_note']
                ];

                array_push($full_data, $row);
            }
        }

        //load the date total here
        array_push($full_data, $dateTotal);
        $row = ["", "", "", "", "", ""];
        array_push($full_data, $row);
        $row = ["", "", "", "", "", ""];
        array_push($full_data, $row);
    }

    $row = [ "", "GRAND TOTAL", "", "",
        $grandQuantity, $grandCostPrice, $grandTotalCost,
         $grandTotalTotal, $grandProfit
    ];

    array_push($full_data, $row);

    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="SalesReport.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $writer = self::getIOFactory($spreadsheet);
    $writer->save('php://output');
    exit;
}

 ?>
