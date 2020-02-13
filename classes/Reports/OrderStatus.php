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

    const ON_DELIVERY = 'wc-pending-delivery';
    const PENDING_IMPORT = 'wc-import';
    const PENDING_ADVANCE = 'wc-pending-advance';

    const WAITING_SHIPPING_FEE = 'wc-waiting-shippx-fe';
    const PAYMENT_RECEIVED = "wc-payment-received";
    const TO_BE_VERIFIED = "wc-to-be-verified";
    const DELIVERY_FORWARDED = "wc-delivery-forwarde";
    const ORDER_PLANNED = "wc-order-planned";
    const ON_REAPPRO = "wc-on-reappro";
    const ON_REAPPRO_2 = "wc-on-purchase";

    const PLANNED_PUS = 'wc-planned_pus';
    const ADVANCED_PAID = 'wc-advance_paid';
    const UNFINISHED = "wc-wc-unfinished";
    const SHIPPED_OTHER_TOWN = 'wc-shipped_other_tow';

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
            self::ON_REAPPRO_2 => 'label label-on-purchase',

            self::PLANNED_PUS => 'label label-planned-pus',
            self::ADVANCED_PAID => 'label label-advance-paid',
            self::UNFINISHED => 'label label-unfinished',
            self::SHIPPED_OTHER_TOWN => "label label-shipped-other-town"
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
            self::SHIPPED => 'Expédié Yaoounde',

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
            self::ORDER_PLANNED => 'Planifiée Fleet',
            self::ON_REAPPRO => 'En Cour d’Approvisionement',
            self::ON_REAPPRO_2 => 'En Cour d’Approvisionement',

            self::PLANNED_PUS => "Planifiée PUS",
            self::ADVANCED_PAID => "Avance Payé",
            self::UNFINISHED => "Commande Non Terminée",
            self::SHIPPED_OTHER_TOWN => "Expédié Autre Ville"
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
