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

        foreach ($config['contexts'] as $name => $values) {
            $this->validateContextsConfig($name, $values);
        }

        $container->setParameter('behatch.parameters', $config);
    }

    private function validateContextsConfig($name, $values)
    {
        $validate = 'validateContexts' . ucfirst($name) . 'Config';
        if (is_callable(array($this, $validate))) {
            $this->$validate($values);
        }
        else {
            throw new \RuntimeException("Invalid config section '$name'.");
        }
    }

    private function validateContextsBrowserConfig($values)
    {
    }

    private function validateContextsDebugConfig($values)
    {
        if ($values['enable']) {
            if (isset($values['screenshot_dir'])) {
                if (!is_dir($values['screenshot_dir'])) {
                    throw new \RuntimeException(
                        'The screenshot directory doesn\'t exists.'
                    );
                }
                if (!is_writable($values['screenshot_dir'])) {
                    throw new \RuntimeException(
                        'The screenshot directory is not writable.'
                    );
                }
            }
        }
    }

    private function validateContextsJsonConfig($values)
    {
        if ($values['enable']) {
            if (isset($values['evaluation_mode'])) {
                if(!in_array($values['evaluation_mode'], array('php', 'javascript'))) {
                    throw new \RuntimeException(
                        'Unknown JSON evaluation mode.'
                    );
                }
            }
            else {
                throw new \Exception(
                    'You must provide a json evaluation mode.'
                );
            }
        }
    }

    private function validateContextsRestConfig($values)
    {
    }

    private function validateContextsSystemConfig($values)
    {
        if ($values['enable']) {
            if (isset($values['root'])) {
                if (!is_dir($values['root'])) {
                    throw new \RuntimeException(
                        'The system root directory doesn\'t exists.'
                    );
                }
                if (!is_writable($values['root'])) {
                    throw new \RuntimeException(
                        'The system root directory is not writable.'
                    );
                }
            }
        }
    }

    private function validateContextsTableConfig($values)
    {
    }

    private function validateContextsXmlConfig($values)
    {
    }

    public function getConfig(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                arrayNode('contexts')->
                    isRequired()->
                    children()->
                        arrayNode('browser')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                                scalarNode('timeout')->
                                    defaultValue('10')->
                                end()->
                            end()->
                        end()->
                        arrayNode('debug')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                                scalarNode('screenshot_dir')->
                                    defaultValue('.')->
                                end()->
                            end()->
                        end()->
                        arrayNode('json')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                                scalarNode('evaluation_mode')->
                                    defaultValue('javascript')->
                                end()->
                            end()->
                        end()->
                        arrayNode('rest')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                            end()->
                        end()->
                        arrayNode('system')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                                scalarNode('root')->
                                    defaultValue('.')->
                                end()->
                            end()->
                        end()->
                        arrayNode('table')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                            end()->
                        end()->
                        arrayNode('xml')->
                            children()->
                                scalarNode('enable')->
                                    defaultTrue()->
                                end()->
                            end()->
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
