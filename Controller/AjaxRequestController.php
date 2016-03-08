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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajax Request Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class AjaxRequestController extends Controller
{
    /**
     * Return list of products according to the search by name
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
   public function searchProductByNameAction(Request $request)
   {
       $term = $request->get('name');
       $products = $this->get('asf_product.product.manager')->getRepository()->findProductsByNameContains($term);
       $search = array();
       	
       foreach($products as $product) {
           $search[] = array(
               'id' => $product->getId(),
               'name' => $product->getName()
           );
       }
       
       $response = new Response();
       $response->setContent(json_encode(array(
           'total_count' => count($search),
           'items' => $search
       )));
       	
       return $response;
   }
   
   /**
    * Return list of category according to the search by name
    *
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function searchCategoryByNameAction(Request $request)
   {
       $term = $request->get('name');
       $categories = $this->get('asf_product.category.manager')->getRepository()->findByNameContains($term);
       $search = array();
   
       foreach($categories as $category) {
           $search[] = array(
               'id' => $category->getId(),
               'name' => $category->getName()
           );
       }
        
       $response = new Response();
       $response->setContent(json_encode(array(
           'total_count' => count($search),
           'items' => $search
       )));
   
       return $response;
   }
   
   /**
    * Return list of brands according to the search by name
    *
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function searchBrandByNameAction(Request $request)
   {
       $term = $request->get('name');
       $brands = $this->get('asf_product.brand.manager')->getRepository()->findByNameContains($term);
       $search = array();
   
       foreach($brands as $brand) {
           $search[] = array(
               'id' => $brand->getId(),
               'name' => $brand->getName()
           );
       }
        
       $response = new Response();
       $response->setContent(json_encode(array(
           'total_count' => count($search),
           'items' => $search
       )));
   
       return $response;
   }
}