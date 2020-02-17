<?php

namespace App\Base;

use App\Reports\OrderStatus;

class Users
{
    const ADMIN = 'ADMIN';
    const CUSTOMER_SERVICE = 'CUSTOMER_SERVICE';
    const DELIVERY_AGENT = "DELIVERY_AGENT";
    const ACCOUNTING = "ACCOUNTING";

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
            self::CUSTOMER_SERVICE,
            self::DELIVERY_AGENT,
            self::ACCOUNTING
        ];
    }

    public static function group_users()
    {
        return [
            self::ADMIN => [
                'cedrickt',
                'stephane'
            ],

            self::CUSTOMER_SERVICE => [
                'beryl',
                'helen',
                'lysa',
            ],

            self::DELIVERY_AGENT => [
                'martin',
                'reine'
            ],

            self::ACCOUNTING => [
                'eliza'
            ]
        ];
    }

    public static function group_access()
    {
        return [
            self::ADMIN => [
                'ALL'
            ],
            self::CUSTOMER_SERVICE => [
                OrderStatus::PENDING,
                OrderStatus::PROCESSING,
                OrderStatus::UNREACHABLE1,
                OrderStatus::UNREACHABLE2,
                OrderStatus::ON_HOLD,
                OrderStatus::CANCELLED,
                OrderStatus::FAILED,
                OrderStatus::WAITING_SHIPPING_FEE,
                OrderStatus::PENDING_ADVANCE,
                OrderStatus::TO_BE_VERIFIED
            ],

            self::DELIVERY_AGENT => [
                OrderStatus::ORDER_PLANNED,
                OrderStatus::ON_DELIVERY,
                OrderStatus::COMPLETED,
                OrderStatus::FAILED,
                OrderStatus::CANCELLED
            ],

            self::ACCOUNTING => [
                OrderStatus::COMPLETED,
                OrderStatus::PAYMENT_RECEIVED
            ]
        ];
    }

    public static function get_group_access($group)
    {
        return self::group_access()[$group];
    }

    public static function user_access()
    {
        $users = [
            'perdita' => [
                OrderStatus::ON_HOLD,
                OrderStatus::DELIVERY_FORWARDED_DLA
            ],

            'christian2' => [
                OrderStatus::ON_REAPPRO,
                OrderStatus::ON_REAPPRO_2,
                OrderStatus::ORDER_PLANNED,
                OrderStatus::PENDING_IMPORT
            ],


        ];
    }

    public static function get_user_access( $user_login = '')
    {
        //check if the user belongs to a group
        $in_group = false;
        $group_access = self::group_users();
        $user_group = '';

        //the final statusses for the user
        $statusses = [];

        foreach ($group_access as $group => $users) {
            if(in_array($user_login, $users))
            {
                $user_group = $group;
                $in_group = true;
            }
        }

        if($in_group)
        {
            //get the access status for that group
            $statusses = self::get_group_access($user_group);
        }
        else {
            //check if there is access kept for this particular user
            $user_access = self::user_access();

            foreach($user_access as $user_l => $access)
            {
                if($user_l == $user_login)
                {
                    $statusses = $access;
                }

            }
        }

        //return the access statusses for the user
        return $statusses;
    }
}

 ?>
