<?php

namespace Bluesquare\BillingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('billing');

        $treeBuilder->getRootNode()->children()
            ->arrayNode('stripe')->children()
                ->scalarNode('stripe_api_key_pub')->end()
                ->scalarNode('stripe_api_key')->end()
                ->scalarNode('stripe_webhook_key')->end()
            ->end()
        ->end();


        return $treeBuilder;
    }
}
