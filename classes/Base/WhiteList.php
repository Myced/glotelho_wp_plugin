<?php
namespace App\Base;


class WhiteList
{
    public static function categories()
    {
        return [
            '3210', //PRODUITS POUR BÉBÉS
            '3202', //Sécurité/Télécom
            '1426', //BUREAUX & MAISON
            '1483', //ELECTROMENAGER
            '1393', //TELECOMS
            '1394', //INFORMATIQUE
            '1523', //PRODUITS NATURELS
            '1390', //SÉCURITÉ ELECTRONIQUE
            '1387', //TELEPHONES & TABLETTES
            '3347', //SAV
            '3361', //SUPER MARCHE
            '16' //clothing
        ];
    }

    public static function payment_methods()
    {

    }
}

 ?>
