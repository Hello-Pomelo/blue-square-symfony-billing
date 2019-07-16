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

    public function createPurchase($items)
    {
        $apiKey = $this->container->getParameter('stripe_api_key_secret');

        \Stripe\Stripe::setApiKey($apiKey);

        return (
            \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => 'T-shirt',
                    'description' => 'Comfortable cotton t-shirt',
                    'images' => ['https://example.com/t-shirt.png'],
                    'amount' => 500,
                    'currency' => 'eur',
                    'quantity' => 1,
                ]],
                'success_url' => 'http://18d049f8.ngrok.io/redirected',
                'cancel_url' => 'http://18d049f8.ngrok.io/cancel',
            ])
        );
    }
}
