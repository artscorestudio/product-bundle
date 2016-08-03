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

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ASFProductExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        
        $container->setParameter('asf_product.enable_brand_entity', $config['enable_brand_entity']);
        $container->setParameter('asf_product.enable_productPack_entity', $config['enable_productPack_entity']);
        
        $loader->load('services/services.yml');
        
        $this->setProductParameters($container, $loader, $config);
        $this->setCategoryParameters($container, $loader, $config);
        $this->setBrandParameters($container, $loader, $config);
        $this->setProductPackParameters($container, $loader, $config);
    }

    /**
     * Set Product Entity Parameters in Container
     * 
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     * @param array            $config
     * 
     * @throws InvalidConfigurationException
     * 
     * @return void
     */
    protected function setProductParameters(ContainerBuilder $container, YamlFileLoader $loader, array $config)
    {
        if ( null === $config['product']['entity'] ) {
            throw new InvalidConfigurationException('The asf_product.product.entity parameter must be defined.');
        }
        
        $container->setParameter('asf_product.product.entity', $config['product']['entity']);
        $container->setParameter('asf_product.product.form.name', $config['product']['form']['name']);
        $container->setParameter('asf_product.product.form.type', $config['product']['form']['type']);
        $loader->load('services/product.yml');
    }
    
    /**
     * Set Category Entity Parameters in Container
     *
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     * @param array            $config
     *
     * @throws InvalidConfigurationException
     *
     * @return void
     */
    protected function setCategoryParameters(ContainerBuilder $container, YamlFileLoader $loader, array $config)
    {
        if ( null === $config['category']['entity'] ) {
            throw new InvalidConfigurationException('The asf_product.category.entity parameter must be defined.');
        }
        
        $container->setParameter('asf_product.category.entity', $config['category']['entity']);
        $container->setParameter('asf_product.category.form.name', $config['category']['form']['name']);
        $container->setParameter('asf_product.category.form.type', $config['category']['form']['type']);
        $loader->load('services/category.yml');
    }
    
    /**
     * Set Brand Entity Parameters in Container
     *
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     * @param array            $config
     *
     * @throws InvalidConfigurationException
     *
     * @return void
     */
    protected function setBrandParameters(ContainerBuilder $container, YamlFileLoader $loader, array $config)
    {
        if ( false === $config['enable_brand_entity'] ) {
            $container->setParameter('asf_product.brand.entity', $config['brand']['entity']);
            return;
        }
        
        if ( null === $config['brand']['entity'] ) {
            throw new InvalidConfigurationException('The asf_product.brand.entity parameter must be defined.');
        }
        
        $container->setParameter('asf_product.brand.entity', $config['brand']['entity']);
        $container->setParameter('asf_product.brand.form.name', $config['brand']['form']['name']);
        $container->setParameter('asf_product.brand.form.type', $config['brand']['form']['type']);
        $loader->load('services/brand.yml');
    }
    
    /**
     * Set ProductPack Entity Parameters in Container
     *
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     * @param array            $config
     *
     * @throws InvalidConfigurationException
     *
     * @return void
     */
    protected function setProductPackParameters(ContainerBuilder $container, YamlFileLoader $loader, array $config)
    {
        if ( false === $config['enable_productPack_entity'] ) {
            return;
        }
    
        if ( null === $config['product_pack']['entity'] ) {
            throw new InvalidConfigurationException('The asf_product.product_pack.entity parameter must be defined.');
        }
    
        $container->setParameter('asf_product.product_pack.entity', $config['product_pack']['entity']);
        $container->setParameter('asf_product.product_pack.form.name', $config['product_pack']['form']['name']);
        $container->setParameter('asf_product.product_pack.form.type', $config['product_pack']['form']['type']);
        $loader->load('services/product_pack.yml');
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface::prepend()
     */
    public function prepend(ContainerBuilder $container)
    {
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
        foreach (array_keys($container->getExtensions()) as $name) {
            switch ($name) {
                case 'twig':
                    // Add Form Theme
                    $container->prependExtensionConfig($name, array(
                        'form_themes' => array($config['form_theme']),
                    ));
                    
                    // Add globals
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
