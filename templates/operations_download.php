<?php
if($_GET['download'] == true)
{
    ob_end_clean();

    //get the selected categories
    $selectedCategoreis = $_GET['categories'];
    $selectedSellers = $_GET['sellers'];

    $full_data = [];

    //set the document properties.
    // Set document properties
    $spreadsheet->getProperties()->setCreator('TN CEDRIC')
        ->setLastModifiedBy('TN CEDRIC')
        ->setTitle('Office 2007 XLSX Glotelho Report')
        ->setSubject('Office 2007 XLSX Categories Report')
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
    $h1 = [ "Glotelho Operations Report" ];
    array_push($full_data, $h1);

    // $h1 = [ "Seller:", $seller_name];
    // array_push($full_data, $h1);
    //
    // $h1 = [ "Category:", $cat_name];
    // array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //go through the results and put them into the array
    //get the data and loop through it.

    if(in_array('-1', $selectedSellers))
    {
        //loop through all the sellers.
        foreach($sellers as $seller)
        {
            //insert space before the seller
            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["Seller:", $seller->name, "", "", "", ""];
            array_push($full_data, $row);

            if(in_array('-1', $selectedCategoreis))
            {
                //do this for all categories
                $grandQuantity = 0;
                $grandCostPrice = 0;
                $grandTotalCost = 0;
                $grandSellingPrice = 0;
                $grandTotalTotal = 0;
                $grandProfit = 0;

                foreach($categories as $category)
                {
                    $row = [
                        "Category",
                        $category->name
                    ];

                    array_push($full_data, $row);

                    $data = $manager->get_data($category->term_id, $seller->term_id);

                    require GT_BASE_DIRECTORY . '/templates/operations_download_row.php';
                }

                //now insert the Grand Total for all categories
                $row = ["", "", ""];
                array_push($full_data, $row);

                $row = ["Grand Total", "", ""];
                array_push($full_data, $row);

                $row = [
                    "Quantity", "Cost Price", "Total Cost Price",
                    "Selling Price", "Total Income", "Profits"
                ];
                array_push($full_data, $row);

                $row = [
                    $grandQuantity, $grandCostPrice, $grandTotalCost,
                    $grandSellingPrice, $grandTotalTotal, $grandProfit
                ];

                array_push($full_data, $row);


            }
            else {
                foreach($categories as $category)
                {
                    if(!in_array($category->term_id, $selectedCategoreis))
                        continue;


                    $row = [
                        "Category",
                        $category->name
                    ];

                    array_push($full_data, $row);

                    $data = $manager->get_data($category->term_id, $seller->term_id);

                    require GT_BASE_DIRECTORY . '/templates/operations_download_row.php';
                }
            }
        }
    }
    else {

        //loop through and show only selected sellers
        foreach($sellers as $seller)
        {
            if(! in_array($seller->term_id, $selectedSellers))
                continue;


            //insert space before the seller
            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["", "", "", "", "", ""];
            array_push($full_data, $row);

            $row = ["Seller:", $seller->name, "", "", "", ""];
            array_push($full_data, $row);

            if(in_array('-1', $selectedCategoreis))
            {
                //do this for all categories
                $grandQuantity = 0;
                $grandCostPrice = 0;
                $grandTotalCost = 0;
                $grandSellingPrice = 0;
                $grandTotalTotal = 0;
                $grandProfit = 0;

                foreach($categories as $category)
                {
                    $row = [
                        "Category",
                        $category->name
                    ];

                    array_push($full_data, $row);

                    $data = $manager->get_data($category->term_id, $seller->term_id);

                    require GT_BASE_DIRECTORY . '/templates/operations_download_row.php';
                }

                //now insert the Grand Total for all categories
                $row = ["", "", ""];
                array_push($full_data, $row);

                $row = ["Grand Total", "", ""];
                array_push($full_data, $row);

                $row = [
                    "Quantity", "Cost Price", "Total Cost Price",
                    "Selling Price", "Total Income", "Profits"
                ];
                array_push($full_data, $row);

                $row = [
                    $grandQuantity, $grandCostPrice, $grandTotalCost,
                    $grandSellingPrice, $grandTotalTotal, $grandProfit
                ];

                array_push($full_data, $row);


            }
            else {
                foreach($categories as $category)
                {
                    if(!in_array($category->term_id, $selectedCategoreis))
                        continue;


                    $row = [
                        "Category",
                        $category->name
                    ];

                    array_push($full_data, $row);

                    $data = $manager->get_data($category->term_id, $seller->term_id);

                    require GT_BASE_DIRECTORY . '/templates/operations_download_row.php';
                }
            }
        }

    }



    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="OperationsReport.xlsx"');
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
