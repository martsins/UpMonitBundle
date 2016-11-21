<?php

namespace Martsins\UpMonitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('martsins_up_monit');

        $rootNode
          ->children()
          ->scalarNode('token')->cannotBeEmpty()
          ->end()
          ->scalarNode('project_id')->cannotBeEmpty()
          ->end()
          ->scalarNode('url')->cannotBeEmpty()
          ->end()
          ->end()
        ;

        return $treeBuilder;
    }
}
