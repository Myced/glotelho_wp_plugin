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
    $h1 = [ "Rapport Christian" ];
    array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //go through the results and put them into the array
    //get the data and loop through it.

    foreach ($data as $item) {

        $row = [
            0 => $item['date'],
            1 => $item['order_number'],
            2 => self::get_name($item['order_status']),
            3 => $item['full_name'],
            4 => $item['town'],
            5 => $item['product_name'],
            6 => $item['quantity']
        ];



        array_push($full_data, $row);

    }



    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="RapportChristian.xlsx"');
    header('Content-Type: text/html; charset=utf-8');
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
