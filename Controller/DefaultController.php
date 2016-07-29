<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default Controller gather generic app views.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class DefaultController extends Controller
{
    /**
     * Product Homepage.
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('ASFProductBundle:Default:index.html.twig', array(
        	'brand_enabled' => $this->getParameter('asf_product.enable_brand_entity'),
        	'productPack_enabled' => $this->getParameter('asf_product.enable_productPack_entity')
        ));
    }
}
