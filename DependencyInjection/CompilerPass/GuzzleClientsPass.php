<?php

namespace Jhg\StatusPageBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GuzzleClientsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('jhg_status_page.auto_register_guzzle_middleware')){
            return;
        }

        if (!$container->hasDefinition('jhg_status_page.guzzle_status.middleware')){
            return;
        }

        foreach ($container->findTaggedServiceIds('csa_guzzle.client') as $id => $attr) {
            $guzzleClientDefinition = $container->getDefinition($id);
            $guzzleClientDefinition->clearTag('csa_guzzle.client');
            $guzzleClientDefinition->addTag('csa_guzzle.client', [ 'middleware' => 'jhg_status_page.guzzle_middleware' ]);
        }
    }
}