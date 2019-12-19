<?php
function achat_get_order_status($status)
{
    $astatuses = \App\Reports\OrderStatus::allNames();

    return $astatuses[$status];
}


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
        ->setDescription('Glotelho Ecommerce Report Client Achat')
        ->setKeywords('office 2007 openxml php')
        ->setCategory('Excel Document');


    //get the active sheet
    $sheet = $spreadsheet->getActiveSheet();

    $date_period = "November - December 19";

    //push the headings
    $h1 = [ "FreeLance Reports" ];
    array_push($full_data, $h1);

    $h1 = ["Date Period:", $date_period];
    array_push($full_data, $h1);

    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);

    //go through the results and put them into the array
    //get the data and loop through it.

    foreach ($sellers as $key => $value) {

        //make a row the seller and code
        $row = [$value['name'], $key, ""];

        array_push($full_data, $row);

        //make the headings
        $row = ['S/N', 'Date', 'Order No', 'Client', 'Telephone', 'Statut', 'Total', 'Commission'];
        array_push($full_data, $row);

        //manage ther orders now
        $orders = $manager->get_orders($key);
        $count = 1;
        $seller_total = 0;
        $seller_commission = 0;

        foreach ($orders as $order) {

            $amount = $order->total - $order->shipping;
            $comm  = (5/100) * $amount;

            if($order->post_status != \App\Reports\OrderStatus::CANCELLED
                    && $order->post_status != \App\Reports\OrderStatus::FAILED
                    && $order->post_status != \App\Reports\OrderStatus::DRAFT )
            {
                $seller_total += $amount;
                $seller_commission += $comm;
            }

            $row = [];

            //put everthing in the data row
            $row[0] = $count++;
            $row[1] = date("d, M Y", strtotime($order->post_date));
            $row[2] = $order->ID;
            $row[3] = $order->first_name . ' ' . $order->last_name;
            $row[4] = $order->tel;
            $row[5] = achat_get_order_status($order->post_status);
            $row[6] = number_format($amount) . ' FCFA';
            $row[7] = number_format($comm) . 'FCFA';


            //push the row
            array_push($full_data, $row);

        }

        $row = [];
        $row[0] = "Total";
        $row[1] = number_format($seller_total);
        array_push($full_data, $row);

        $row = [];
        $row[0] = "Commission";
        $row[1] = number_format($seller_commission);
        array_push($full_data, $row);

        $row = [""];
        array_push($full_data, $row);

        $row = [""];
        array_push($full_data, $row);

    }


    //Load the data from an array
    $sheet->fromArray($full_data, null, 'A1');

    // Redirect output to a client’s web browser (Xlsx)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="RapportFreelancce.xlsx"');
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
