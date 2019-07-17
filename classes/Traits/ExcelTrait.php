<?php
namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait ExcelTrait
{
    public static function getExcel($spreadsheet)
    {
        return new Xlsx($spreadsheet);
    }

    public static function getIOFactory($spreadsheet)
    {
        return IOFactory::createWriter($spreadsheet, 'Xlsx');
    }
}

 ?>
