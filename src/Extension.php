<?php

namespace Behatch;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;

class Extension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'behatch';
    }

    public function initialize(ExtensionManager $extensionManager)
    {
        if (PHP_MAJOR_VERSION === 5) {
            @trigger_error('The behatch context extension will drop support for PHP 5 in version 4.0', E_USER_DEPRECATED);
        }
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/services'));
        $loader->load('http_call.yml');

        $this->loadClassResolver($container);
        $this->loadHttpCallListener($container);
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    private function loadClassResolver(ContainerBuilder $container)
    {
        $definition = new Definition('Behatch\Context\ContextClass\ClassResolver');
        $definition->addTag(ContextExtension::CLASS_RESOLVER_TAG);
        $container->setDefinition('behatch.class_resolver', $definition);
    }

    private function loadHttpCallListener(ContainerBuilder $container)
    {
        $processor = new \Behat\Testwork\ServiceContainer\ServiceProcessor;
        $references = $processor->findAndSortTaggedServices($container, 'behatch.context_voter');
        $definition = $container->getDefinition('behatch.context_supported.voter');

        foreach ($references as $reference) {
            $definition->addMethodCall('register', [$reference]);
        }
    }

    public function getCompilerPasses()
    {
        return [];
    }
}
