<?php declare(strict_types=1);

namespace Becklyn\DomainChecker;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class YamlStructureConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder ()
    {
        $treeBuilder = new TreeBuilder("root");

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode("domains")
                    ->scalarPrototype()->end()
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode("ip")
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(
                            function ($value)
                            {
                                return false === \ip2long($value);
                            }
                        )
                        ->thenInvalid("Invalid IP address given in `ip`. Must be a valid IPv4.")
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
