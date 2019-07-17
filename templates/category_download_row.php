<?php

//add the table headings
$headings = [
    "Date", "Order", "Product", "Quantity",
    "Unit Cost", 'Selling Price', 'Total', "Profit"
];

array_push($full_data, $headings);


//now loop throug the results and put them into the array.
$categoryTotal = ["", "", "Category Total", 0, 0, 0, 0, 0];



foreach($data as $date => $dates)
{
    $dateUsed = false;
    $dateTotal = ["", "", "Date Total", 0, 0, 0, 0, 0];
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
            }
            else {
                $myorder = "";
            }

            $categoryTotal[3] += $product['quantity'];
            $categoryTotal[4] += $product['cost_price'];
            $categoryTotal[5] += $product['selling_price'];
            $categoryTotal[6] += $product['quantity'] * $product['selling_price'];
            $categoryTotal[7] += $product['profit'];

            $dateTotal[3] += $product['quantity'];
            $dateTotal[4] += $product['cost_price'];
            $dateTotal[5] += $product['selling_price'];
            $dateTotal[6] += $product['quantity'] * $product['selling_price'];
            $dateTotal[7] += $product['profit'];

            //calculate the grand total only for multiple categories.
            if($cat == '-1')
            {
                $grandQuantity += $product['quantity'];
                $grandCostPrice += $product['cost_price'];
                $grandTotalCost += $product['quantity'] * $product['cost_price'];
                $grandSellingPrice += $product['selling_price'];
                $grandTotalTotal += $product['selling_price'] * $product['quantity'];
                $grandProfit += $product['profit'];
            }

            $row = [
                $mydate,
                $myorder,
                $product['name'],
                $product['quantity'],
                $product['cost_price'],
                $product['selling_price'],
                $product['quantity'] * $product['selling_price'],
                $product['profit']
            ];

            array_push($full_data, $row);
        }
    }

    //load the date total here
    array_push($full_data, $dateTotal);
    $row = ["", "", "", "", "", ""];
    array_push($full_data, $row);
}

$row = ["", "", "", "", "", ""];
array_push($full_data, $row);
array_push($full_data, $categoryTotal);

//insert two rows after each category
$row = ["", "", "", "", "", ""];
array_push($full_data, $row);

$row = ["", "", "", "", "", ""];
array_push($full_data, $row);

?>
