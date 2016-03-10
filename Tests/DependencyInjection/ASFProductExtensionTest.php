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

use ASF\ProductBundle\DependencyInjection\ASFProductExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;

/**
 * Bundle's Extension Test Suites
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ASFProductExtensionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \ASF\ProductBundle\DependencyInjection\ASFProductExtension
	 */
	protected $extension;
	
	/**
	 * {@inheritDoc}
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp()
	{
		parent::setUp();

		$this->extension = new ASFProductExtension();
	}
	
	/**
	 * @covers ASF\ProductBundle\DependencyInjection\ASFProductExtension::load
	 */
	public function testLoadExtension()
	{
	    $container = new ContainerBuilder();
		$this->extension->load(array(), $container);
	}
	
	/**
	 * @covers ASF\ProductBundle\DependencyInjection\ASFProductExtension::prepend
	 */
	public function testPrependExtension()
	{
	    $this->extension->prepend($this->getContainer());
	}
	
	/**
	 * Return a mock object of ContainerBuilder
	 *
	 * @return \Symfony\Component\DependencyInjection\ContainerBuilder
	 */
	protected function getContainer($bundles = null, $extensions = null)
	{
	    $bag = m::mock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBag');
	    $bag->shouldReceive('add');
	     
	    if ( is_null($bundles) ) {
	        $bundles = $bundles = array(
	            'TwigBundle' => 'Symfony\Bundle\TwigBundle\TwigBundle',
	        );
	    }
	     
	    if ( is_null($extensions) ) {
	        $extensions = array(
	            'twig' => new TwigExtension()
	        );
	    }
	     
	    $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
	    $container->shouldReceive('getParameter')->with('kernel.bundles')->andReturn($bundles);
	    $container->shouldReceive('getExtensions')->andReturn($extensions);
	    $container->shouldReceive('getExtensionConfig')->andReturn(array());
	    $container->shouldReceive('prependExtensionConfig');
	    $container->shouldReceive('setAlias');
	     
	    $container->shouldReceive('addResource');
	    $container->shouldReceive('setParameter');
	    $container->shouldReceive('hasExtension')->andReturn(false);
	    $container->shouldReceive('getParameterBag')->andReturn($bag);
	    $container->shouldReceive('setDefinition');
	    $container->shouldReceive('setParameter');
	     
	    return $container;
	}
	
	/**
	 * Return bundle's default configuration
	 *
	 * @return array
	 */
	protected function getDefaultConfig()
	{
	    return array(
            'enable_core_support' => false,
	        'enable_brand_entity' => false,
	        'enable_productPackEntity' => false,
	        'form_theme' => 'ASFProductBundle:Form:fields.html.twig'
	    );
	}
}