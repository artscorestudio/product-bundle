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
 * Default Controller Tests
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class TestDefaultControllerTest extends WebTestCase
{
    /**
     * @covers ASF\ProductBundle\Controller\DefaultController::indexAction
     */
    public function testProductHomepage()
    {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Default Product template")')->count());
    }
}