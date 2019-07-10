<?php
namespace App\Reports;


class OrderStatus
{
    const COMPLETED = 'wc-completed';
    const FAILED = 'wc-failed';
    const CANCELLED = 'wc-cancelled';
    const PROCESSING = 'wc-processing';
    const ON_HOLD = 'wc-on-hold';
    const PENDING = 'wc-pending';
    const DRAFT = 'auto-draft';

    public static function allClasses()
    {
        return [
            self::COMPLETED => 'label label-success',
            self::FAILED => 'label label-danger',
            self::CANCELLED => 'label label-danger',
            self::PROCESSING => 'label label-info',
            self::ON_HOLD => 'label label-primary',
            self::PENDING => 'label label-primary',
            self::DRAFT => ''
        ];
    }

    public static function allNames()
    {
        return [
            self::COMPLETED => 'COMPLETED',
            self::FAILED => 'FAILED',
            self::CANCELLED => 'CANCELLED',
            self::PROCESSING => 'PROCESSING',
            self::ON_HOLD => 'ON HOLD',
            self::PENDING => 'PENDING',
            self::DRAFT => ''
        ];
    }
}

 ?>
