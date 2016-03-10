<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use ASF\ProductBundle\DependencyInjection\Configuration;

/**
 * This test case check if the default bundle's configuration from bundle's Configuration class is OK
 *  
 * @author Nicolas Claverie <info@artscore-studio.fr
 *
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ASF\ProductBundle\DependencyInjection\Configuration
     */
    public function testEnableASFCoreSupportParameterInDefaultConfiguration()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());
        $this->assertFalse($config['enable_core_support']);
    }
    
    /**
     * @covers ASF\ProductBundle\DependencyInjection\Configuration
     */
    public function testEnableBrandEntityParameterInDefaultConfiguration()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());
        $this->assertFalse($config['enable_brand_entity']);
    }
    
    /**
     * @covers ASF\ProductBundle\DependencyInjection\Configuration
     */
    public function testEnableProductPackEntityParameterInDefaultConfiguration()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());
        $this->assertFalse($config['enable_productPack_entity']);
    }
    
    /**
     * @covers ASF\ProductBundle\DependencyInjection\Configuration
     */
    public function testTwigFormThemeParameterInDefaultConfiguration()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array());
        $this->assertArrayHasKey('form_theme', $config);
    }
}