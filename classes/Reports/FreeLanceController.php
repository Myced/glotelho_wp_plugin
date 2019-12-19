<?php
namespace App\Reports;

use App\Traits\ExcelTrait;
use App\Reports\OrderStatus;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Reports\Managers\FreeLanceManager;

class FreeLanceController
{
    use ExcelTrait;

    public static function show_report()
    {

        $sellers = self::getNames();
        $manager = new FreeLanceManager;

        if(isset($_GET['download']))
        {
            $spreadsheet = new Spreadsheet;
        }

        return require_once GT_BASE_DIRECTORY . '/templates/freelance_report.php';
    }

    public static function getRegions()
    {
        return get_terms('zone_region', ['hide_empty' => false ]);
    }

    public static function getSellers()
    {
        return get_terms('seller', ['hide_empty' => false ]);
    }

    public static function getTowns()
    {
        return get_terms('zone_town', ['hide_empty' => false ]);
    }

    public static function showStatus($status)
    {
        $name = OrderStatus::getName($status);
        $class = OrderStatus::getClass($status);

        $status = "<label class=\"$class\">$name </label>";
        return $status;
    }

    public static function getNames()
    {
        return [
            "2525" => [
                "name" => "Ebode ",
                "email" => ""
            ],

            "3030" => [
                "name" => "Jehu Djiokou",
                "email" => ""
            ],

            "2857" => [
                "name" => "Kengne",
                "email" => ""
            ],

            "3118" => [
                "name" => "Lukong",
                "email" => ""
            ],

            "4812" => [
                "name" => "Manjombe",
                "email" => ""
            ],

            "2992" => [
                "name" => "Minoue",
                "email" => ""
            ],

            "1881" => [
                "name" => "Nanawa",
                "email" => ""
            ],

            "1719" => [
                "name" => "Ngo Eone",
                "email" => ""
            ],

            "2831" => [
                "name" => "Ntuente",
                "email" => ""
            ],

            "1219" => [
                "name" => "Saa Josias",
                "email" => ""
            ],

            "9018" => [
                "name" => "Tita",
                "email" => ""
            ],

            "1010" => [
                "name" => "Franck Ngueti",
                "email" => ""
            ],

            "5115" => [
                "name" => "Eyada Romaric",
                "email" => ""
            ],

            "8823" => [
                "name" => "Soh Kengni",
                "email" => ""
            ],

            "19181" => [
                "name" => "Rostand Kouteu",
                "email" => ""
            ],

            "2306" => [
                "name" => "Lucress",
                "email" => ""
            ],

            "2811" => [
                "name" => "Mabounan Chedril Doura",
                "email" => ""
            ],

            "14131" => [
                "name" => "Arnaud Kakam",
                "email" => ""
            ],

            "2002" => [
                "name" => "Octavie",
                "email" => ""
            ],

            "17219" => [
                "name" => "Komguem Fotso",
                "email" => ""
            ],

            "20159" => [
                "name" => "Lydy Tchuemkam",
                "email" => ""
            ],

            "2238" => [
                "name" => "Roland Ndigui",
                "email" => ""
            ],

            "888" => [
                "name" => "Doriane Tifuh",
                "email" => "tifuhdoriane@gmail.com"
            ]

        ];
    }
}
 ?>
