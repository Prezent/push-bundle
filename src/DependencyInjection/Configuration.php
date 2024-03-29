<?php

namespace Prezent\PushBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Bundle configuration
 *
 * @see ConfigurationInterface
 * @author Robert-Jan Bijl <robert-jan@prezent.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('prezent_push');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('provider')->isRequired()->end()
                ->arrayNode('pushwoosh')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('api_key')->isRequired()->end()
                        ->scalarNode('application_id')->end()
                        ->scalarNode('application_group_id')->end()
                    ->end()
                ->end()
                ->arrayNode('onesignal')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('application_id')->isRequired()->end()
                        ->scalarNode('application_auth_key')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->canBeEnabled()
                    ->children()
                        ->scalarNode('target')->defaultValue('file')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
