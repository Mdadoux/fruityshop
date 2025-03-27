<?php

namespace App\Services;

class OrderStatesService
{
    public const STATES = [

        '1' => [
            'label' => 'En attente de paiement',
            'email_subject' => 'Commande en attente de paiement',
            'email_template' => 'payment_inprogress.html',
            'class'=>'bg-secondary'
        ],
        '2' => [
            'label' => 'Paiement accepté',
            'email_subject' => 'Commande payée',
            'email_template' => 'payment.html',
            'class'=>'bg-primary'
        ],
        '3' => [
            'label' => 'En cours de préparation',
            'email_subject' => 'Commande en cours de préparation',
            'email_template' => 'preparation.html',
            'class'=>'bg-warning'

        ],
        '4' => [
            'label' => 'Expédiée',
            'email_subject' => 'Commande expédié',
            'email_template' => 'shipped.html',
            'class'=>'bg-success'
        ],
        '5' => [
            'label' => 'Annulée',
            'email_subject' =>'Commande annulée',
            'email_template' => 'order_cancelled.html',
            'class'=>'bg-danger'
        ],
    ];


}