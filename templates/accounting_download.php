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
    $h1 = [ "Accounting Report" ];
    array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //set the headers
    $row = [
        "Date Commandé", "Date Encaissé", "No Commande", "Produit", "Qte",
        "Prix Unitaire (PU)", "Prix Reviendre (PR)", "Prix Total (PT)",
        "Marge", "Vendeur", "Ville", "Paiement",
        "Categorie"
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
        $dateTotal = ["", "", "", "Date Total", 0, 0, 0, 0, 0];
        $currentDate = $date;
        $currentOrder = "";

        foreach($dates as $order => $orders)
        {
            $orderUsed = false;

            foreach($orders as $product)
            {
                //////////process the order info

                $periodQuantity += $product['quantity'];
                $periodCostPrice += $product['cost_price'];
                $periodTotalCost += $product['quantity'] * $product['cost_price'];
                $periodSellingPrice += $product['product_total'];
                $periodProfits += $product['profit'];

                $quantityTotal += $product['quantity'];
                $cost_price_total += $product['cost_price'];
                $total_cost_price_total += $product['quantity'] * $product['cost_price'];
                $selling_price_total += $product['product_total'];
                $total_profits += $product['profit'];

                $current_product_id = $product['product_id'];

                //the categories that need to be affected by this product
                $affected_categories = [];

                //check if this product already has it categories
                if(array_key_exists($current_product_id, $product_cats) )
                {
                    //then just update the categories total info
                    $affected_categories = $product_cats[$current_product_id];
                }
                else {
                    //get the product categories and save them

                    //loop through the categories
                    $prod_cats = [];

                    foreach($category_products as $cur_cat_id => $cat_products)
                    {
                        //check if the item is in the list
                        if(in_array($current_product_id, $cat_products))
                        {
                            array_push($prod_cats, $cur_cat_id);
                        }
                    }

                    $product_cats[$product['product_id']] = $prod_cats;

                    $affected_categories = $prod_cats;
                }


                //now affect the required categories
                $p_cost_price = $product['cost_price'] * $product['quantity'];
                $p_total = $product['product_total'];
                $marge = $product['profit'];

                foreach($affected_categories as $aff_cat)
                {
                    $category_data[$aff_cat]['qty'] += $product['quantity'];
                    $category_data[$aff_cat]['cost'] += $p_cost_price;
                    $category_data[$aff_cat]['total'] += $p_total;
                    $category_data[$aff_cat]['marge'] += $marge;

                    if($sub_current_order == '')
                    {
                        ++$category_data[$aff_cat]['orders'];
                    }
                    else {
                        if($sub_current_order != $gt_current_order)
                        {
                            ++$category_data[$aff_cat]['orders'];
                        }
                    }

                }

                $sub_current_order = $gt_current_order;
                $edate  = date("d/M/Y", strtotime($product['payment_date']));

                //end of order processing info
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
                    $full_name = $product['full_name'];
                    $myorderstatus = get_order_status($product['order_status']);
                }
                else {
                    $myorder = "";
                    $full_name = "";
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
                    $edate,
                    $order,
                    $product['name'],
                    $product['quantity'],
                    $product['cost_price'],
                    number_format($product['quantity'] * $product['cost_price']),
                    number_format($product['product_total']),
                    number_format($product['profit']),
                    $product['seller'],
                    $product['town'],
                    $product['payment_method'],
                    get_product_categories($product['product_id'], $categories, $category_products)
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

    $row = [ "", "", "GRAND TOTAL", "", "",
        $grandQuantity, $grandCostPrice, $grandTotalCost,
         $grandTotalTotal, $grandProfit
    ];

    array_push($full_data, $row);

    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="AccountingReport.xlsx"');
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
