<?php

namespace Jhg\StatusPageBundle;

use Jhg\StatusPageBundle\DependencyInjection\CompilerPass\GuzzleClientsPass;
use Jhg\StatusPageBundle\DependencyInjection\CompilerPass\PredisClientPass;
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
        $container->addCompilerPass(new PredisClientPass());
        $container->addCompilerPass(new GuzzleClientsPass());
    }

}