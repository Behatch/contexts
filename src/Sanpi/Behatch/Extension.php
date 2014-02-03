<?php

namespace Sanpi\Behatch;

use Symfony\Component\DependencyInjection\Reference;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\DependencyInjection\Definition;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;

class Extension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'behatch';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadClassResolver($container);
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    private function loadClassResolver(ContainerBuilder $container)
    {
        $definition = new Definition('Sanpi\Behatch\Context\ContextClass\ClassResolver');
        $definition->addTag(ContextExtension::CLASS_RESOLVER_TAG);
        $container->setDefinition('behatch.class_resolver', $definition);
    }

    public function getCompilerPasses()
    {
        return array();
    }
}
