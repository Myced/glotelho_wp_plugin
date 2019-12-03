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
    $h1 = [ "Glotelho Categories Report" ];
    array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //go through the results and put them into the array
    //get the data and loop through it.
    if(in_array('-1', $selectedCategories))
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

            $data = $manager->get_data($category->term_id);

            require GT_BASE_DIRECTORY . '/templates/category_download_row.php';
        }

        //now insert the Grand Total for all categories
        $row = ["", "", ""];
        array_push($full_data, $row);

        $row = ["Grand Total", "", ""];
        array_push($full_data, $row);

        $row = [
            "Quantity", "Cost Price", "Total Cost Price",
            "Selling Price",  "Profits"
        ];
        array_push($full_data, $row);

        $row = [
            $grandQuantity, $grandCostPrice, $grandTotalCost,
            $grandSellingPrice,  $grandProfit
        ];

        array_push($full_data, $row);


    }
    else {

        //do it for the selected categories
        foreach($selectedCategories as $cat_id)
        {
            //get the category name
            $category = get_term_by( 'id', $cat_id, "product_cat");

            $row = [
                "Category",
                $category->name
            ];

            array_push($full_data, $row);

            $data = $manager->get_data($category->term_id);

            require GT_BASE_DIRECTORY . '/templates/category_download_row.php';
        }

        //now insert the Grand Total for all categories
        $row = ["", "", ""];
        array_push($full_data, $row);



    }


    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="CategoriesReport.xlsx"');
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
