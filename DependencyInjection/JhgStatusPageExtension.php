<?php

namespace Jhg\StatusPageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class JhgStatusPageExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('jhg_status_page.predis_client_id', $config['predis_client_id']);
        $container->setParameter('jhg_status_page.auto_register_guzzle_middleware', $config['auto_register_guzzle_middleware']);
        $container->setParameter('jhg_status_page.watchdogs', $config['watchdogs']);
        $container->setParameter('jhg_status_page.views', $config['views']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $metrics = [];
        $loadGuzzleMiddleware = false;
        foreach ($config['metrics'] as $metricId => $metric) {
            $loadGuzzleMiddleware |= in_array($metric['type'], ['guzzle_request_count', 'guzzle_response_count', 'guzzle_response_time']);
            $this->loadMetric($metric + ['id' => $metricId], $container);
            $metrics[$metricId] = $metric + ['id' => $metricId];
        }

        $container->setParameter('jhg_status_page.metrics', $metrics);

        if ($loadGuzzleMiddleware) {
            $loader->load('services/guzzle-middleware.yml');
        }

        if (!empty($config['watchdogs'])) {
            $loader->load('services/watchdogs.yml');

            $statusFlushEventListener = $container->getDefinition('jhg_status_page.status_flush.event_listener');
            $statusFlushEventListener->setArgument(1, $container->getDefinition('jhg_status_page.watchdogs_processor'));
        }
    }

    /**
     * @param array            $metric
     * @param ContainerBuilder $container
     */
    protected function loadMetric(array $metric, ContainerBuilder $container)
    {
        switch ($metric['type']) {
            case 'request_count':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\RequestCountListener');
                break;

            case 'response_count':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\ResponseCountListener');
                break;

            case 'response_time':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\ResponseTimeListener');
                break;

            case 'exception':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\ExceptionListener');
                break;

            case 'guzzle_request_count':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\GuzzleRequestCountListener');
                break;

            case 'guzzle_response_count':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\GuzzleResponseCountListener');
                break;

            case 'guzzle_response_time':
                $listenerDefinition = new Definition('Jhg\StatusPageBundle\StatusListener\GuzzleResponseTimeListener');
                break;

            case 'custom':
                if ($metric['service'] && $metric['class']) {
                    throw new InvalidConfigurationException('Custom listener provides both service and class name. Just one please!');
                } elseif (!$metric['service'] && !$metric['class']) {
                    throw new InvalidConfigurationException('Custom listener requires service or class name.');
                } elseif ($metric['service']) {
                    $listenerDefinition = $container->getDefinition($metric['service']);
                } else /*if ($metric['class'])*/ {
                    $listenerDefinition = new Definition($metric['class']);
                }
                break;

            default:
                throw new InvalidConfigurationException(sprintf('Listener for %s type is not yet implemented', $metric['type']));
        }

        $listenerDefinitionReflection = new \ReflectionClass($listenerDefinition->getClass());

        if ($listenerDefinitionReflection->implementsInterface('Symfony\Component\EventDispatcher\EventSubscriberInterface')) {
            $listenerDefinition->addTag('kernel.event_subscriber');
            $listenerDefinition->setPublic(true);
        }

        if ($listenerDefinitionReflection->implementsInterface('Jhg\StatusPageBundle\StatusListener\StatusListenerInterface')) {
            $listenerDefinition->addMethodCall('setEventKey', [$metric['id']]);
            $listenerDefinition->addMethodCall('setEventPeriod', [$metric['period']]);
            $listenerDefinition->addMethodCall('setEventExpire', [$metric['expire']]);
            $listenerDefinition->addMethodCall('setCondition', [$metric['condition']]);
        }

        if ($listenerDefinitionReflection->implementsInterface('Jhg\StatusPageBundle\Status\StatusStackAwareInterface')) {
            $listenerDefinition->addMethodCall('setStatusStack', [new Reference('jhg_status_page.status_stack')]);
        }

        $listenerId = sprintf('status_page.%s.%s_listener', $metric['id'], $metric['type']);
        $container->setDefinition($listenerId, $listenerDefinition);
    }
}