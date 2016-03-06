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
}