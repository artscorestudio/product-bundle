<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('asf_product');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
	        ->children()
		        ->booleanNode('enable_core_support')
		        	->defaultFalse()
		        ->end()
		        ->booleanNode('enable_select2_support')
		          ->defaultFalse()
		        ->end()
		        ->booleanNode('enable_brand_entity')
		        	->defaultFalse()
		        ->end()
		        ->booleanNode('enable_productPack_entity')
		          ->defaultFalse()
		        ->end()
		        ->scalarNode('form_theme')
		          ->defaultValue('ASFProductBundle:Form:fields.html.twig')
		        ->end()
		        
		        ->append($this->addProductParameterNode())
		        ->append($this->addCategoryParameterNode())
		        ->append($this->addBrandParameterNode())
		    ->end();
        
        return $treeBuilder;
    }
    
    /**
     * Add Product Entity Configuration
     */
    protected function addProductParameterNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('product');
        
        $node
            ->treatTrueLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\ProductType")))
            ->treatFalseLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\ProductType")))
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue('ASF\ProductBundle\Form\Type\ProductFormType')
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('product_type')
                        ->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array("Default"))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        
        return $node;
    }
    
    /**
     * Add Category Entity Configuration
     */
    protected function addCategoryParameterNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('category');
    
        $node
            ->treatTrueLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\CategoryType")))
            ->treatFalseLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\CategoryType")))
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue('ASF\ProductBundle\Form\Type\CategoryType')
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('category_type')
                        ->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array("Default"))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    
        return $node;
    }
    
    /**
     * Add Brand Entity Configuration
     */
    protected function addBrandParameterNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('brand');
    
        $node
            ->treatTrueLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\BrandType")))
            ->treatFalseLike(array('form' => array('type' => "ASF\ProductBundle\Form\Type\BrandType")))
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue('ASF\ProductBundle\Form\Type\BrandType')
                        ->end()
                        ->scalarNode('name')
                            ->defaultValue('brand_type')
                        ->end()
                        ->arrayNode('validation_groups')
                            ->prototype('scalar')->end()
                            ->defaultValue(array("Default"))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    
        return $node;
    }
}
