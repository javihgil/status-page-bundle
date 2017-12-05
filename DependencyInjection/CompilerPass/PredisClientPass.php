<?php

namespace Jhg\StatusPageBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class PredisClientPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $predisClientId = $container->getParameter('jhg_status_page.predis_client_id');

        $statusStackDefinition = $container->getDefinition('jhg_status_page.status_stack');
        $statusStackDefinition->replaceArgument(0, new Reference($predisClientId));

        if ($container->hasDefinition('jhg_status_page.watchdogs_processor')) {
            $watchdogsProcessorDefinition = $container->getDefinition('jhg_status_page.watchdogs_processor');
            $watchdogsProcessorDefinition->replaceArgument(2, new Reference($predisClientId));
        }

        if ($container->hasDefinition('jhg_status_page.watchdogs_reader')) {
            $watchdogsReaderDefinition = $container->getDefinition('jhg_status_page.watchdogs_reader');
            $watchdogsReaderDefinition->replaceArgument(1, new Reference($predisClientId));
        }

        if ($container->hasDefinition('jhg_status_page.metric_reader')) {
            $watchdogsReaderDefinition = $container->getDefinition('jhg_status_page.metric_reader');
            $watchdogsReaderDefinition->replaceArgument(0, new Reference($predisClientId));
        }
    }
}