<?php
/**
 * This file is part of Artscore Studio Framework Package
 *
 * (c) 2012-2015 Artscore Studio <info@artscore-studio.fr>
 *
 * This source file is subject to the MIT Licence that is bundled
 * with this source code in the file LICENSE.
 */
namespace Asf\Bundle\ProductBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;

/**
 * Data Fixture loader
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class loader extends DataFixtureLoader
{
	/**
	 * (non-PHPdoc)
	 * @see \Hautelook\AliceBundle\Alice\DataFixtureLoader::getFixtures()
	 */
	protected function getFixtures()
	{
		return array(
			//__DIR__ . '/Categories.yml'
		);
	}
}