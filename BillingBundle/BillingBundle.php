<?php
/**
 * Created by PhpStorm.
 * User: loann
 * Date: 28/02/19
 * Time: 15:27
 */


namespace Bluesquare\BillingBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BillingBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
