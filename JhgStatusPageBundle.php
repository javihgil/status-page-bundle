<?php

namespace Jhg\StatusPageBundle;

use Jhg\StatusPageBundle\DependencyInjection\CompilerPass\GuzzleClientsPass;
use Jhg\StatusPageBundle\DependencyInjection\CompilerPass\StatusStackRedisPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JhgStatusPageBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new StatusStackRedisPass());
        $container->addCompilerPass(new GuzzleClientsPass());
    }

}