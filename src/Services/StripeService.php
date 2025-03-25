<?php

namespace App\Services;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeService
{

    private StripeClient $stripe;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->stripe = new StripeClient($_ENV['STRIPE_API_KEY']);
        $this->logger = $logger;
    }

    public function getPaymentMethod($id_stripe_session) : ?\Stripe\StripeObject
    {
        try {
            // récupérer la session stripe correspondant
            $session = $this->stripe->checkout->sessions->retrieve($id_stripe_session, []);
            // récupérer intent du paiement
            $paymentIntent = $this->stripe->paymentIntents->retrieve($session->payment_intent, []);
            //récupérer les modes de paiement utilisé
            $paymentMethod = $this->stripe->paymentMethods->retrieve($paymentIntent->payment_method, []);
            // dans notre cas que les paiements en carte
            return $paymentMethod->card;
        } catch (ApiErrorException $e) {
            $this->logger->error("Erreur Stripe : " . $e->getMessage());
            return null;
        }

    }
}