<?php
namespace App\Reports;


class OrderStatus
{
    const PENDING = 'wc-pending';
    const PROCESSING = 'wc-processing';
    const UNREACHABLE1 = 'wc-unreachable-1';
    const UNREACHABLE2 = 'wc-unreachable-2';
    const ON_HOLD = 'wc-on-hold';
    const COMFIRMED = 'wc-on-hold';
    const COMPLETED = 'wc-completed';
    const FAILED = 'wc-failed';
    const CANCELLED = 'wc-cancelled';
    const DRAFT = 'auto-draft';
    const PLANNING = 'wc-planning';
    const SHIPPED = 'wc-shipped';
    const TRASHED = 'trash';
    const REFUNDED = 'wc-refund';

    const PENDING_DELIVERY = 'wc-pending-delivery';
    const PENDING_IMPORT = 'wc-import';
    const PENDING_ADVANCE = 'WC-pending-advance';

    public static function allClasses()
    {
        return [
            self::PENDING => 'label label-en-cours',
            self::PROCESSING => 'label label-en-cours',

            self::UNREACHABLE1 => 'label label-unreachable-1',
            self::UNREACHABLE2 => 'label label-unreachable-2',
            self::ON_HOLD => 'label label-confirmed',
            self::PLANNING => 'label label-planning',
            self::SHIPPED => 'label label-shipped',

            self::COMPLETED => 'label label-success',

            self::CANCELLED => 'label label-danger',
            self::FAILED => 'label label-danger',

            self::REFUNDED => 'label label-refund',

            self::PENDING_IMPORT => 'label label-pending-import',
            self::PENDING_ADVANCE => 'label label-pending-advance',
            self::PENDING_DELIVERY => 'label label-pending-delivery'
        ];

    }


    public static function allNames()
    {
        return [
            self::PENDING => 'Attente Paiement',
            self::PROCESSING => 'En Cours',

            self::UNREACHABLE1 => 'Injoignable 1',
            self::UNREACHABLE2 => 'Injoignable 2',
            self::ON_HOLD => 'Commande Confirmée',
            self::PLANNING => 'Planification',
            self::SHIPPED => 'Commande Expédié',

            self::COMPLETED => 'Commande Livrée',

            self::CANCELLED => 'Commande Annulée',
            self::FAILED => 'Échouée',

            self::REFUNDED => 'Remboursée',

            self::PENDING_IMPORT => 'Produit à l’Import',
            self::PENDING_ADVANCE => 'Avance en Attente',
            self::PENDING_DELIVERY => 'En Cours de Livraison'
        ];
    }

    public function getName($status)
    {
        $names = self::allNames();

        if(array_key_exists($status, $names))
        {
            return $names[$status];
        }

        return ' ';
    }

    public function getClass($status)
    {
        $classes = self::allClasses();

        if(array_key_exists($status, $classes))
        {
            return $classes[$status];
        }

        return ' ';
    }

    public static function validStatuses()
    {
        return self::allNames();
    }
}

 ?>
