<?php

namespace ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
abstract class BaseCompilerPass implements CompilerPassInterface
{
    const REGISTRY_NAME = 'api.registry.service_name';
    const TAG_NAME      = 'api.tag';
    const ADD_METHOD    = 'addTaggedService';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(static::REGISTRY_NAME)) {
            return;
        }

        $definition = $container->getDefinition(static::REGISTRY_NAME);

        foreach ($container->findTaggedServiceIds(static::TAG_NAME) as $id => $arguments) {
            $this->addMethodCallToDefinition($definition, $id, $arguments);
        }
    }

    /**
     * @param Definition $definition
     * @param int        $id
     * @param array|null $arguments
     */
    public function addMethodCallToDefinition(Definition $definition, $id, $arguments = null)
    {
        $definition->addMethodCall(static::ADD_METHOD, [new Reference($id)]);
    }
}
