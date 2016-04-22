<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Ajax Request Controller Test
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class AjaxRequestControllerTest extends WebTestCase
{
	/**
	 * @covers ASF\ProductBundle\Controller\AjaxRequestController::suggestProductAjaxRequestAction
	 */
	public function testSuggestProductAjaxRequestAction()
	{
		$client = static::createClient();
		
		$crawler = $client->request('GET', '/suggest/ajax/request');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Default Product template")')->count());
	}
}