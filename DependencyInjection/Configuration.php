<?php

namespace Jhg\StatusPageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jhg_status_page');

        $rootNode
            ->children()
                ->scalarNode('predis_client_id')->isRequired()->end()
                ->booleanNode('auto_register_guzzle_middleware')->defaultTrue()->end()
                ->arrayNode('metrics')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->enumNode('type')->values(['request_count', 'response_count', 'response_time', 'custom', 'guzzle_request_count', 'guzzle_response_count', 'guzzle_response_time', 'exception', 'readonly'])->isRequired()->end()
                            ->enumNode('period')->values(['second', 'minute', 'hour', 'day', 'total'])->isRequired()->end()
                            ->scalarNode('class')->info('Listener class name for custom type')->defaultNull()->end()
                            ->scalarNode('service')->info('Listener service name for custom type')->defaultNull()->end()
                            ->scalarNode('expire')->defaultNull()->end()
                            ->scalarNode('condition')->defaultNull()->end()
                        ->end() // prototype-children
                    ->end() // prototype
                ->end() // array

                ->arrayNode('watchdogs')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('expire')->isRequired()->end()
                            ->scalarNode('condition')->isRequired()->end()
                            ->integerNode('threshold')->defaultValue(1)->end()
                        ->end() // prototype-children
                    ->end() // prototype
                ->end() // array

                ->arrayNode('views')
                    ->useAttributeAsKey('id')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('template')->defaultValue('JhgStatusPageBundle:StatusPage:status.html.twig')->end()
                            ->arrayNode('metrics')
                                ->useAttributeAsKey('id')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('metric_id')->isRequired()->end()
                                        ->scalarNode('title')->isRequired()->end()
                                        ->scalarNode('period')->isRequired()->end()
                                        ->scalarNode('average_by')->end()
                                        ->scalarNode('percentage_by')->end()
                                    ->end() // prototype-children
                                ->end() // prototype
                            ->end() // array
                        ->end() // prototype-children
                    ->end() // prototype
                ->end() // array
            ->end() // root->children
        ;

        return $treeBuilder;
    }
}