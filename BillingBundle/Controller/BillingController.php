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
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_api_key_secret'));

        $endpoint_secret = $this->container->getParameter('stripe_webhook_key');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);

            if ($event->type == 'checkout.session.completed') {
                $session = $event->data->object;

                $serviceToCall = $this->container->getParameter('payment_confirmation_service');

                list($service, $function) = explode('::', $serviceToCall);

                $srv = new $service();

                $srv->{$function}($session);

                return $this->json("OK", 200);
            }

        } catch(\UnexpectedValueException $e) {
            $this->logger->error($e->getMessage());
            exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
            $this->logger->error($e->getMessage());
            exit();
        }

        return $this->json("KO", 500);
    }
}
