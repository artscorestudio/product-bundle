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
     * Return a list of suggested product according to a search
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function suggestProductAjaxRequestAction(Request $request)
    {
        $terms = $request->get('term'); $result = array(); $productManager = $this->get('asf_product.product.manager');
        $products = $productManager->getProductsByKeywords($terms);
    
        foreach($products as $product) {
            $result[$product->getId()] = $productManager->getFormattedProductName($product);
        }
    
        $explterms = explode(' ', $terms); $brand_name = null;
        $brand_names = $productManager->findBrandNameInString($terms, true);
        foreach($explterms as $term) {
            if ( $term == in_array($term, $brand_names) ) {
                $brand_name = $term;
            }
        }
    
        $weight = $productManager->findWeightPropertyInString($terms);
        $capacity = $productManager->findCapacityPropertyInString($terms);
    
        $weight = is_null($weight) ? null : $weight . 'kg';
        $capacity = is_null($capacity) ? null : $capacity . 'L';
    
        $product_name = '';
        foreach($explterms as $term) {
            $is_weight = $productManager->findWeightPropertyInString($term);
            $is_capacity = $productManager->findCapacityPropertyInString($term);
            if ( $term != $brand_name && is_null($is_weight) && is_null($is_capacity) ) {
                $product_name .= ' ' . $term;
            }
        }
    
        return $this->render('ASFProductBundle:Product:suggest-product.html.twig', array(
            'products' => $result,
            'product_name' => trim($product_name),
            'brand_name' => $brand_name,
            'weight' => $weight,
            'capacity' => $capacity
        ));
    }
    
    /**
     * Create product according to a search
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createProductAjaxRequestAction(Request $request)
    {
        try {
            $response = new Response();
            $product_name = $request->get('productName');
            $brand_name = $request->get('brandName');
            $weight = $this->get('asf_product.product.manager')->findWeightPropertyInString($request->get('weight'));
            $capacity = $this->get('asf_product.product.manager')->findCapacityPropertyInString($request->get('capacity'));
            $productManager = $this->get('asf_product.product.manager');
            	
            $weight = is_null($weight) || $weight == '' ? null : $weight;
            $capacity = is_null($capacity) || $capacity == '' ? null : $capacity;
            	
            $product = $productManager->createInstance();
            $product->setName($product_name)->setState(ProductModel::STATE_PUBLISHED)->setWeight($weight)->setCapacity($capacity);
            	
            if ( $brand_name != '' ) {
                $brand = $this->get('asf_product.brand.manager')->getRepository()->findOneBy(array('name' => $brand_name));
                if ( is_null($brand) ) {
                    $update_brand_acl = true;
                    $brand = $this->get('asf_product.brand.manager')->createInstance();
                    $brand->setName($brand_name)->setState(BrandModel::STATE_PUBLISHED);
                }
                $product->setBrand($brand);
            }
            	
            $productManager->getEntityManager()->persist($product);
            $productManager->getEntityManager()->flush();
            	
            $object_identity = ObjectIdentity::fromDomainObject($product);
            $acl = $this->get('security.acl.provider')->createAcl($object_identity);
            	
            $security_identity = UserSecurityIdentity::fromAccount($this->get('security.context')->getToken()->getUser());
    
            $acl->insertObjectAce($security_identity, MaskBuilder::MASK_OWNER);
            $this->get('security.acl.provider')->updateAcl($acl);
            	
            if ( isset($update_brand_acl) ) {
                $object_identity = ObjectIdentity::fromDomainObject($brand);
                $acl = $this->get('security.acl.provider')->createAcl($object_identity);
                	
                $security_identity = UserSecurityIdentity::fromAccount($this->get('security.context')->getToken()->getUser());
    
                $acl->insertObjectAce($security_identity, MaskBuilder::MASK_OWNER);
                $this->get('security.acl.provider')->updateAcl($acl);
            }
            	
            $response->setContent(json_encode(array('name' => $productManager->getFormattedProductName($product))));
        } catch (\Exception $e) {
            $response->setContent(json_encode(array('error' => $e->getMessage())));
        }
        return $response;
    }
    
    /**
     * Return a list of product according to a search
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRequestAction(Request $request)
    {
        $terms = $request->get('term'); $result = array();
        $products = $this->get('asf_product.product.manager')->getProductsByKeywords($terms);
    
        foreach($products as $product) {
            $result[$product->getId()] = $this->get('asf_product.product.manager')->getFormattedProductName($product);
        }
    
        $response = new Response();
        $response->setContent(json_encode($result));
    
        return $response;
    }
    
    /**
     * Return list of products via an ajax request for search on exactly term
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRequestNameAction(Request $request)
    {
        $term = $request->get('term'); $result = array();
        $products = $this->get('asf_product.product.manager')->getRepository()->findBy(array('name' => $term));
    
        foreach($products as $product) {
            $result[$product->getId()] = $this->get('asf_product.product.manager')->getFormattedProductName($product);
        }
    
        $response = new Response();
        $response->setContent(json_encode($result));
    
        return $response;
    }
    
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