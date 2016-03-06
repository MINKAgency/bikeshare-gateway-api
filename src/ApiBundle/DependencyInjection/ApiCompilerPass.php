<?php

namespace ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Anael Chardan <anael.chardan@gmail.com>
 */
class ApiCompilerPass extends BaseCompilerPass
{
    const REGISTRY_NAME = 'api.api_registry';
    const TAG_NAME      = 'api.api_wrapper';
    const ADD_METHOD    = 'addCityWrapper';

    /**
     * @param Definition $definition
     * @param int        $id
     * @param null|array $arguments
     */
    public function addMethodCallToDefinition(Definition $definition, $id, $arguments = null)
    {
        foreach ($arguments as $attributes) {
            $definition->addMethodCall(static::ADD_METHOD, [new Reference($id), $attributes['alias']]);
        }
    }
}
