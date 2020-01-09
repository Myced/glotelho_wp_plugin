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

    const WAITING_SHIPPING_FEE = 'waiting-shippx-fe';
    const PAYMENT_RECEIVED = "payment-received";
    const TO_BE_VERIFIED = "to-be-verified";
    const DELIVERY_FORWARDED = "delivery-forwarde";
    const ORDER_PLANNED = "order-planned";
    const ON_REAPPRO = "on-reappro";
    const ON_REAPPRO_2 = "on-purchase";

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
            self::ON_DELIVERY => 'label label-pending-delivery',

            self::WAITING_SHIPPING_FEE => 'label label-waiting-shipping-fee',
            self::PAYMENT_RECEIVED => 'label label-payment-received',
            self::TO_BE_VERIFIED => 'label label-to-be-verified',
            self::DELIVERY_FORWARDED => "label label-delivery-forwarded",
            self::ORDER_PLANNED => 'label label-order-planned',
            self::ON_REAPPRO => 'label label-on-reappro',
            self::ON_REAPPRO_2 => 'label label-on-purchase'
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
            self::ON_DELIVERY => 'En Cours de Livraison',

            self::WAITING_SHIPPING_FEE => "En Attente De Frais De Livraison",
            self::PAYMENT_RECEIVED => "Encaissé",
            self::TO_BE_VERIFIED => 'A Vérifier Par Le Service Client',
            self::DELIVERY_FORWARDED => "Livraison Reportée",
            self::ORDER_PLANNED => 'Commande Planifiée',
            self::ON_REAPPRO => 'En Cour d’Approvisionement',
            self::ON_REAPPRO_2 => 'En Cour d’Approvisionement'
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
