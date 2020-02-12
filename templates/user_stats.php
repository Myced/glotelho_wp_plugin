<?php

//prepare the data here
$data = [
    "2019" => [
        "10" => [
            "name" => "October",
            "id" => "10"
        ],
        "11" => [
            "name" => "November",
            "id" => "11"
        ],
        "12" => [
            "name" => "December",
            "id" => "12"
        ]
    ],
    "2020" => [
        "01" => [
            "name" => "January",
            "id" => "01"
        ]
    ]
];

 ?>

 <div class="content">
     <div class="row">
         <div class="col-md-12">
             <h3>User Stats</h3>
         </div>
     </div>

     <br><br>
     <div class="row">
         <div class="col-md-12">

             <div class="box box-info">
                 <div class="box-header with-border">
                     <h3 class="box-title">
                         Statistiques d'enregistrement des utilisateurs
                     </h3>
                 </div>

                 <div class="box-body">
                     <div class="table-responsive">
                         <table class="table table-bordered">
                             <tr>
                                 <th>S/N</th>
                                 <th>Année</th>
                                 <th>Mois</th>
                                 <th>Anciens utilisateurs</th>
                                 <th>Nouveaux utilisateurs</th>
                             </tr>

                            <?php $count = 1; ?>
                             <?php foreach ($data as $year => $months): ?>

                                 <?php foreach ($months as $month_id => $month): ?>

                                     <tr>
                                         <td> <?php echo $count++; ?> </td>
                                         <td> <?php echo $year ?> </td>
                                         <td> <?php echo $month['name']; ?> </td>
                                         <td>400</td>
                                         <td>21</td>
                                     </tr>

                                 <?php endforeach; ?>

                             <?php endforeach; ?>
                         </table>
                     </div>
                 </div>

             </div>

         </div>
     </div>
 </div>
