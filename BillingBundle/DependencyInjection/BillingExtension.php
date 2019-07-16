<?php

namespace Bluesquare\BillingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BillingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $this->addAnnotatedClassesToCompile([
            'Bluesquare\\BillingBundle\\Controller\\'
        ]);

        $config = $this->processConfiguration($configuration, $configs);
    }
}
