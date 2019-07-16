<?php

namespace Bluesquare\BillingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

class BillingController extends AbstractController
{
    protected $logger;
    protected $container;

    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->container = $container;
    }

    public function webhook()
    {
        \Stripe\Stripe::setApiKey($this->getParameter('stripe_api_key_secret'));

        $endpoint_secret = $this->getParameter('stripe_webhook_key');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

        } catch(\UnexpectedValueException $e) {
            $logger->error($e->getMessage());
            exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
            $logger->error($e->getMessage());
            exit();
        }

        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;

            // Fulfill the purchase...
            file_put_contents("/tmp/test_stripe", json_encode($session));
            //handle_checkout_session($session);
        }

        return $this->json("OK", 200);
    }
}
