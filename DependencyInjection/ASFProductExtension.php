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

use ASF\CoreBundle\DependencyInjection\ASFExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ASFProductExtension extends ASFExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->mapsParameters($container, $this->getAlias(), $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $container->setParameter('asf_product.enable_brand_entity', $config['enable_brand_entity']);
        $container->setParameter('asf_product.enable_productPack_entity', $config['enable_productPack_entity']);
        
        if ( $config['product']['entity'] === null ) {
        	throw new InvalidConfigurationException('The asf_product.product.entity parameter must be defined.');
        }
        
        if ( $config['category']['entity'] === null ) {
        	throw new InvalidConfigurationException('The asf_product.category.entity parameter must be defined.');
        }
        
        $container->setParameter('asf_product.product.entity', $config['product']['entity']);
        $container->setParameter('asf_product.category.entity', $config['category']['entity']);
        
        $loader->load('services/services.yml');

        if (isset($config['enable_brand_entity']) && true === $config['enable_brand_entity']) {
        	if ( $config['brand']['entity'] === null ) {
        		throw new InvalidConfigurationException('The asf_product.brand.entity parameter must be defined.');
        	}
        	$container->setParameter('asf_product.brand.entity', $config['brand']['entity']);
            $loader->load('services/brand.yml');
        }

        if (isset($config['enable_productPack_entity']) && true === $config['enable_productPack_entity']) {
        	if ( $config['product_pack']['entity'] === null ) {
        		throw new InvalidConfigurationException('The asf_product.product_pack.entity parameter must be defined.');
        	}
            $loader->load('services/product_pack.yml');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface::prepend()
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->configureTwigBundle($container, $config);
    }
    
	/**
     * Configure twig bundle.
     *
     * @param ContainerBuilder $container
     * @param array            $config
     */
    public function configureTwigBundle(ContainerBuilder $container, array $config)
    {
    	parent::configureTwigBundle($container, $config);
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    if (isset($config['enable_brand_entity']) && true === $config['enable_brand_entity']) {
                    	$brandEnabled = true;
                    } else {
                    	$brandEnabled = false;
                    }
                    if (isset($config['enable_productPack_entity']) && true === $config['enable_productPack_entity']) {
                    	$productPackEnabled = true;
                    } else {
                    	$productPackEnabled = false;
                    }
                    
                    $container->prependExtensionConfig($name, array(
                    	'globals' => array(
                    		'brandEnabled' => $brandEnabled,
                    		'productPackEnabled' => $productPackEnabled
                    	)
                    ));
                    break;
            }
        }
    }
}
