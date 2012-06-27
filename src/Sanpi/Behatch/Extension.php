<?php

namespace Sanpi\Behatch;

use Symfony\Component\Config\FileLocator;
use Behat\Behat\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Extension implements ExtensionInterface
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/services'));
        $loader->load('core.xml');

        if (isset($config['filesystem']['root'])) {
            if (!is_dir($config['filesystem']['root'])) {
                throw new \RuntimeException(
                    'The filesystem root directory doesn\'t exists.'
                );
            }
            if (!is_writable($config['filesystem']['root'])) {
                throw new \RuntimeException(
                    'The screenshot directory is not writable.'
                );
            }
        }

        if (isset($config['json']['evaluation_mode'])) {
            if(!in_array($config['json']['evaluation_mode'], array('php', 'javascript'))) {
                throw new \RuntimeException(
                    'Unknown JSON evaluation mode.'
                );
            }
        }
        else {
            throw new \Exception(
                'You must provide a a json evaluation mode.'
            );
        }

        if (isset($config['debug']['screenshot_dir'])) {
            if (!is_dir($config['debug']['screenshot_dir'])) {
                throw new \RuntimeException(
                    'The screenshot directory doesn\'t exists.'
                );
            }
            if (!is_writable($config['debug']['screenshot_dir'])) {
                throw new \RuntimeException(
                    'The screenshot directory is not writable.'
                );
            }
        }
        if (isset($config['debug']['screen_id'])) {
            exec(sprintf("xdpyinfo -display %s >/dev/null 2>&1 && echo OK || echo KO", $config['debug']['screen_id']), $output);
            if (sizeof($output) != 1 || $output[0] != "OK") {
                throw new \RuntimeException(
                    'Screen id is not available.'
                );
            }
        }
        else {
            throw new \Exception(
                'You must provide a screen id.'
            );
        }

        $parameters = array();
        foreach ($config as $ns => $tlValue) {
            foreach ($tlValue as $name => $value) {
                $parameters["behatch.$ns.$name"] = $value;
            }
        }
        $container->setParameter('behatch.parameters', $parameters);
    }

    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                arrayNode('filesystem')->
                    children()->
                        scalarNode('root')->
                            defaultValue('.')->
                        end()->
                    end()->
                end()->
            end()->
            children()->
                arrayNode('json')->
                    children()->
                        scalarNode('evaluation_mode')->
                            defaultValue('javascript')->
                        end()->
                    end()->
                end()->
            end()->
            children()->
                arrayNode('debug')->
                    children()->
                        scalarNode('screenshot_dir')->
                            defaultValue('.')->
                        end()->
                        scalarNode('screen_id')->
                            defaultValue(':0')->
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
