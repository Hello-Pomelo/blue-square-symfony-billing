<?php

namespace Bluesquare\BillingBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BillingSrv
{
    private $container;
    private $url_prefix;

    public function __construct(ContainerInterface $container, $env)
    {
        $this->container = $container;

        $this->url_prefix = $env->isDebug() ? $this->container->getParameter('dev_ngrok_prefix')
                                            : $container->get('router')->getContext()->getBaseUrl();
    }

    private function setApiKey()
    {
        $apiKey = $this->container->getParameter('stripe_api_key_secret');

        \Stripe\Stripe::setApiKey($apiKey);
    }

    public function createPurchase($customer, $items, $cbFormatter)
    {
        $this->setApiKey();

        $line_items = [];

        foreach ($items as $item) $line_items[] = $cbFormatter($item);

        return (
            \Stripe\Checkout\Session::create([
                'customer' => $customer,
                'payment_method_types' => $this->container->getParameter('payment_method'),
                'line_items' => $line_items,
                'success_url' => $this->url_prefix . $this->container->getParameter('stripe_success_url'),
                'cancel_url' => $this->url_prefix . $this->container->getParameter('stripe_cancel_url'),
            ])
        );
    }

    public function confirmPayment($paimentIntent)
    {
        $infoIntent = $this->retrievePurchase($paimentIntent);

        return ($infoIntent['amount'] == $infoIntent['amount_received'] && $infoIntent['amount_capturable'] == 0);
    }

    public function retrievePurchase($paimentIntent)
    {
        $this->setApiKey();

        return \Stripe\PaymentIntent::retrieve($paimentIntent);
    }
}
