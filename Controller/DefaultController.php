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
        return $this->render('ASFProductBundle:Default:index.html.twig');
    }

    /**
     * Show database statistics
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showStatsAction($showProducts = true, $showCategories = true, $showBrands = true)
    {
        if ( true === $showProducts ) {
            $productsNb = $this->getDoctrine()->getRepository($this->getParameter('asf_product.product.entity'))->countProducts();
        }
        
        if ( true === $showCategories ) {
            $categoriesNb = $this->getDoctrine()->getRepository($this->getParameter('asf_product.category.entity'))->countCategories();
        }
        
        if ( true === $this->getParameter(('asf_product.enable_brand_entity')) && true === $showBrands ) {
            $brandsNb = $this->getDoctrine()->getRepository($this->getParameter('asf_product.brand.entity'))->countBrands();
        }
        
        return $this->render('ASFProductBundle:Default:show-stats.html.twig', array(
            'productsNb' => true === $showProducts ? $productsNb : null,
            'categoriesNb' => true === $showCategories ? $categoriesNb : null,
            'brandsNb' => true === $showBrands ? $brandsNb : null,
        ));
    }
}
