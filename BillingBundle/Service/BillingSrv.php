<?php

namespace Bluesquare\BillingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BillingSrv
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createPurchase($user_email, $items, $cbFormatter)
    {
        $apiKey = $this->container->getParameter('stripe_api_key_secret');

        \Stripe\Stripe::setApiKey($apiKey);

        $line_items = [];

        foreach ($items as $item) $line_items[] = $cbFormatter($item);

        return (
            \Stripe\Checkout\Session::create([
                'customer_email' => $user_email,
                'payment_method_types' => $this->container->getParameter('payment_method'),
                'line_items' => $line_items,
                'success_url' => 'http://18d049f8.ngrok.io' . $this->container->getParameter('stripe_success_url'),
                'cancel_url' => 'http://18d049f8.ngrok.io' . $this->container->getParameter('stripe_cancel_url'),
            ])
        );
    }
}
