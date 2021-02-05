<?php

namespace Prezent\PushBundle\DependencyInjection;

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
class PrezentPushExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        switch ($config['provider']) {
            case 'onesignal':
                $this->configureOneSignal($config['onesignal'], $container);
                break;
            case 'pushwoosh':
                $this->configurePushwoosh($config['pushwoosh'], $container);
                break;
            default:
                throw new InvalidConfigurationException(
                    'The child node "provider" at path "prezent_push" must be one of "onesignal", "pushwoosh".'
                );
        }
        if ($config['logging']['enabled']) {
            $this->configureLogging($config['logging'], $container, $config['provider']);
        }
    }

    /**
     * Configure the OneSignal manager
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureOneSignal(array $config, ContainerBuilder $container)
    {
        if (!$config['enabled']) {
            throw new InvalidConfigurationException(
                'The configuration "onesignal" at path "prezent_push" must be enabled.'
            );
        }

        $container->setParameter('prezent_push.onesignal.application_id', $config['application_id']);
        $container->setParameter('prezent_push.onesignal.application_auth_key', $config['application_auth_key']);

        $container->setAlias('prezent_push.manager', 'prezent_push.onesignal_manager');
    }

    private function configurePushwoosh(array $config, ContainerBuilder $container)
    {
        if (!$config['enabled']) {
            throw new InvalidConfigurationException(
                'The configuration "pushwoosh" at path "prezent_push" must be enabled.'
            );
        }

        // check if the application ID, or the application group ID is set
        if (!isset($config['application_id']) && !isset($config['application_group_id'])) {
            throw new InvalidConfigurationException(
                'Either the child node "application_id" or the child node "application_group_id" at path "prezent_push" must be configured.'
            );
        }

        $container->setParameter('prezent_push.pushwoosh.api_key', $config['api_key']);

        if (isset($config['application_id'])) {
            $container->setParameter('prezent_push.pushwoosh.application_id', $config['application_id']);
        }
        if (isset($config['application_group_id'])) {
            $container->setParameter('prezent_push.pushwoosh.application_group_id', $config['application_group_id']);
        }

        $container->setAlias('prezent_push.manager', 'Prezent\PushBundle\Manager\PushwooshManager');
    }

    /**
     * Configure logging
     *
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureLogging(array $config, ContainerBuilder $container, $provider)
    {
        $container->setParameter('prezent_push.logging', $config['target']);

        switch ($config['target']) {
            case 'file':
                // if we are logging to file, add monolog to the manager, and create a specific channel
                $definition = $container->getDefinition(sprintf('Prezent\PushBundle\Manager\%sManager', ucfirst($provider)));
                $definition->addMethodCall('setLogger', [new Reference('logger')]);
                $definition->addTag('monolog.logger', ['channel' => 'prezent_push']);
                break;
            default:
                break;
        }
    }
}
