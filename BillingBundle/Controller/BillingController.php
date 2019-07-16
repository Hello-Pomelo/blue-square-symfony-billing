<?php

namespace Bluesquare\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillingController extends AbstractController
{
    public function webhookAction()
    {
        \Stripe\Stripe::setApiKey($this->container->get('stripe_api_key_secret'));

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $this->container->get('stripe_webhook_key');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            file_put_contents("/tmp/test_stripe", json_encode($e->getMessage()));
            exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            file_put_contents("/tmp/test_stripe", json_encode($e->getMessage()));
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Fulfill the purchase...
            file_put_contents("/tmp/test_stripe", json_encode($session));
            //handle_checkout_session($session);
        }

        return $this->json("OK", 200);
    }
}
