<?php

namespace App\Base;


class Users
{
    const ADMIN = 'ADMIN';
    const CUSTOMER_SERVICE = 'CUSTOMER_SERVICE';

    public static function authorized()
    {
        return [
            'cedrickt',
            'eliza', 'stephane'
        ];
    }

    public static function groups()
    {
        return [
            self::ADMIN,
            self::CUSTOMER_SERVICE
        ];
    }

    public static function group_users()
    {
        return [
            self::ADMIN => [
                'cedrickt',
                'eliza',
                'stephane'
            ],

            self::CUSTOMER_SERVICE => [
                'beryl',
                'helen',
                'lysa'
            ]
        ];
    }
}

 ?>
