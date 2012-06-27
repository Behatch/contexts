<?php

namespace Sanpi\Behatch;

use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Extension implements ExtensionInterface
{
    public function load(array $config, ContainerBuilder $container)
    {
    }

    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                arrayNode('filesystem')->
                    children()->
                        scalarNode('root')->
                            defaultNull()->
                        end()->
                    end()->
                end()->
            end()->
            children()->
                arrayNode('json')->
                    children()->
                        scalarNode('evaluation_mode')->
                            defaultNull()->
                        end()->
                    end()->
                end()->
            end()->
            children()->
                arrayNode('debug')->
                    children()->
                        scalarNode('screenshot_dir')->
                            defaultNull()->
                        end()->
                        scalarNode('screen_id')->
                            defaultNull()->
                        end()->
                    end()->
                end()->
            end()->
        end();

    }

    public function getCompilerPasses()
    {
        return array();
    }
}
