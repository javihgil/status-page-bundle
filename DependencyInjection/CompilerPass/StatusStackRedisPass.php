<?php

namespace Jhg\StatusPageBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class StatusStackRedisPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $statusStackDefinition = $container->getDefinition('jhg_status_page.status_stack');
        $predisClientId = $container->getParameter('jhg_status_page.predis_client_id');
        $statusStackDefinition->replaceArgument(0, new Reference($predisClientId));
    }
}