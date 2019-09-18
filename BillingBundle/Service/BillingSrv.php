<?php

namespace Bluesquare\BillingBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BillingSrv
{
    private $container;
    private $manager;
    private $url_prefix;

    public function __construct(ContainerInterface $container, EntityManager $manager, $env)
    {
        $this->container = $container;

        $this->manager = $manager;

        $ngrok_prefix = $this->container->getParameter('dev_ngrok_prefix');

        $base_url = $this->container->getParameter('prod_prefix');

        $this->url_prefix = ($env == "dev" && !empty($ngrok_prefix)) ? $ngrok_prefix : $base_url;
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

        if (empty($customer->getStripeCustomerId()))
        {
            $stripeCustomer = \Stripe\Customer::create(array(
                "description" => "Customer for ".$customer->getUsername(),
                "email" => $customer->getUsername(),
                "metadata" => [
                    "Prénom" => $customer->getFirstname(),
                    "Nom" => $customer->getLastname(),
                    "Entreprise" => $customer->getCompany()
                ]
            ));

            $customerId = $stripeCustomer["id"];
            $customer->setStripeCustomerId($customerId);
            $this->manager->persist($customer);
            $this->manager->flush();

            $stripeCustomerId = $customerId;
        }
        else
            $stripeCustomerId = $customer->getStripeCustomerId();

        foreach ($items as $item) $line_items[] = $cbFormatter($item);

        return (
            \Stripe\Checkout\Session::create([
                'customer' => $stripeCustomerId,
                'payment_method_types' => $this->container->getParameter('payment_method'),
                'line_items' => $line_items,
                'success_url' => $this->url_prefix . $this->container->getParameter('stripe_success_url'),
                'cancel_url' => $this->url_prefix . $this->container->getParameter('stripe_cancel_url'),
            ])
        );
    }

    public function confirmPurchase($paimentIntent)
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
