<?php

namespace Prezent\PushwooshBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Load the bundle configuration
 *
 * @see Extension
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class PrezentPushwooshExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // check if the application ID, or the application group ID is set
        if (!isset($config['application_id']) && !isset($config['application_group_id'])) {
            throw new InvalidConfigurationException(
                'Either the child node "application_id" or the child node "application_group_id" at path "prezent_pushwoosh" must be configured.'
            );
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('prezent_pushwoosh.api_key', $config['api_key']);

        if (isset($config['application_id'])) {
            $container->setParameter('prezent_pushwoosh.application_id', $config['application_id']);
        }
        if (isset($config['application_group_id'])) {
            $container->setParameter('prezent_pushwoosh.application_group_id', $config['application_group_id']);
        }
        if (isset($config['client_class'])) {
            $container->setParameter('prezent_pushwoosh.pushwoosh_client_class', $config['client_class']);
        }

        if ($config['logging']['enabled']) {
            $container->setParameter('prezent_pushwoosh.logging', $config['logging']['target']);

            switch ($config['logging']['target']) {
                case 'file':
                    // if we are logging to file, add monolog to the manager, and create a specific channel
                    $definition = $container->getDefinition('prezent_pushwoosh.pushwoosh_manager');
                    $definition->addMethodCall('setLogger', [new Reference('logger')]);
                    $definition->addTag('monolog.logger', ['channel' => 'prezent_pushwoosh']);

                    $definition = $container->getDefinition('prezent_pushwoosh.onesignal_manager');
                    $definition->addMethodCall('setLogger', [new Reference('logger')]);
                    $definition->addTag('monolog.logger', ['channel' => 'prezent_pushwoosh']);
                    break;
                default:
                    break;
            }
        }
    }
}
