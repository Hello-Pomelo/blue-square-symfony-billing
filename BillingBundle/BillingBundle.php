<?php
/**
 * Created by PhpStorm.
 * User: loann
 * Date: 28/02/19
 * Time: 15:27
 */


namespace Bluesquare\BillingBundle;

use Bluesquare\BillingBundle\DependencyInjection\BillingExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BillingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function getContainerExtension()
    {
        if (null === $this->extension)
            $this->extension = new BillingExtension();
        return $this->extension;
    }
}
