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
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
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
	    
	    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
	    
	    $container->setParameter('asf_product.enable_brand_entity', $config['enable_brand_entity']);
	    
    	$loader->load('services/services.xml');
    	
    	if ( isset($config['enable_brand_entity']) && true === $config['enable_brand_entity'] ) {
    		$loader->load('services/brand.xml');
    	}
    	
    	if ( isset($config['enable_productPack_entity']) && true === $config['enable_productPack_entity'] ) {
    	    $loader->load('services/product_pack.xml');
    	}
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface::prepend()
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');
    
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);
    
		$this->configureTwigBundle($container, $config);
    }
}
