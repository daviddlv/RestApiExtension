<?php

namespace DavidDel\RestApi\RestApiExtension;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Behat\Behat\Context\ServiceContainer\ContextExtension;

/**
 * Mink extension for MailCatcher manipulation.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class Extension implements ExtensionInterface
{
    const REST_API_ID = 'rest_api';

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/services'));
        $container->setParameter('behat.rest_api.base_url', $config['base_url']);

        $loader->load('core.xml');
        $this->loadContextInitializer($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadContextInitializer(ContainerBuilder $container)
    {
        $definition = new Definition('DavidDel\RestApi\RestApiExtension\ContextInitializer', array(
            '%behat.rest_api.base_url%'
        ));
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('rest_api.context_initializer', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode('base_url')->isRequired()->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompilerPasses()
    {
        return array();
    }

    /**
     * @return array
     */
    protected function loadEnvironmentConfiguration()
    {
        $config = array();

        if ($url = getenv('VIRTUAL_HOST')) {
            $config['base_url'] = $url;
        }

        return $config;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'rest_api';
    }
    
    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }
    
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
