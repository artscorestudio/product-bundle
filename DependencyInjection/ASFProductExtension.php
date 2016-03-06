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

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ASFProductExtension extends ASFExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
	    $config = $this->processConfiguration($configuration, $configs);
	    
	    $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
	    
	    if ( isset($config['enable_core_support']) && true === $config['enable_core_support'] ) {
	    	$loader->load('services/enable_core_support/services.xml');
	    }
    }
}
