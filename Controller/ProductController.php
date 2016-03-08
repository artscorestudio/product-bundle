<?php
/**
 * This file is part of Artscore Studio Framework package
 *
 * (c) 2012-2015 Nicolas Claverie <info@artscore-studio.fr>
 *
 * This dource file is subject to the MIT Licence that is bundled
 * with this source code in the file LICENSE.
 */
namespace Asf\Bundle\ProductBundle\Controller;

use Asf\Bundle\ApplicationBundle\Controller\AsfController;

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Asf\Bundle\ProductBundle\Entity\ProductModel;
use Asf\Bundle\ProductBundle\Model\Brand\BrandModel;

/**
 * Artscore Studio Product Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductController extends AsfController
{
	/**
	 * List all products
	 *
	 * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		if ( !$this->get('security.context')->isGranted('ROLE_ADMIN') )
			throw new AccessDeniedException();
		
		// Set Datagrid source
		$source = new Entity($this->get('asf_product.product.manager')->getClassName());
		$tableAlias = $source->getTableAlias();
		$source->manipulateQuery(function($query) use ($tableAlias){
			$query instanceof QueryBuilder;
			
			if ( count($query->getDQLPart('orderBy')) == 0) {
				$query->orderBy($tableAlias . '.name', 'ASC');
			}
		});
		
		// Get Grid instance
		$grid = $this->get('grid');
		$grid instanceof Grid;
	
		// Attach the source to the grid
		$grid->setSource($source);
		$grid->setId('asf_products_list');
	
		// Columns configuration
		$grid->hideColumns(array('id', 'content', 'createdAt', 'updatedAt', 'deletedAt'));
	
		$grid->getColumn('name')->setTitle($this->getTranslator()->trans('Product name', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false);
		
		$grid->getColumn('state')->setTitle($this->getTranslator()->trans('State', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				ProductModel::STATE_DRAFT => $this->getTranslator()->trans('Draft', array(), 'asf_product'),
				ProductModel::STATE_WAITING => $this->getTranslator()->trans('Waiting', array(), 'asf_product'),
				ProductModel::STATE_PUBLISHED => $this->getTranslator()->trans('Published', array(), 'asf_product'),
				ProductModel::STATE_DELETED => $this->getTranslator()->trans('Deleted', array(), 'asf_product')
			));
			
		$grid->getColumn('type')->setTitle($this->getTranslator()->trans('Type', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				ProductModel::TYPE_PRODUCT => $this->getTranslator()->trans('Product', array(), 'asf_product'),
				ProductModel::TYPE_PRODUCT_PACK => $this->getTranslator()->trans('ProductPack', array(), 'asf_product')
			));
			
		$grid->getColumn('weight')->setTitle($this->getTranslator()->trans('Weight (Kg)', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false)
			->setSize(100);
		
		$grid->getColumn('capacity')->setTitle($this->getTranslator()->trans('Capacity (Liter)', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false)
			->setSize(100);
			
		$grid->getColumn('createdAt')->setSize(100)->setTitle($this->getTranslator()->trans('Created at', array(), 'asf_product'));
		$grid->getColumn('updatedAt')->setSize(100)->setTitle($this->getTranslator()->trans('Updated at', array(), 'asf_product'));
		$grid->getColumn('deletedAt')->setSize(100)->setTitle($this->getTranslator()->trans('Deleted at', array(), 'asf_product'));
	
		$editAction = new RowAction('btn_edit', 'asf_product_product_edit');
		$editAction->setRouteParameters(array('id'));
		$grid->addRowAction($editAction);
	
		$deleteAction = new RowAction('btn_delete', 'asf_product_product_delete', true);
		$deleteAction->setRouteParameters(array('id'))
			->setConfirmMessage($this->get('translator')->trans('Do you want to delete this product ?', array(), 'asf_product'));
		$grid->addRowAction($deleteAction);
	
		$grid->setNoDataMessage($this->getTranslator()->trans('No product was found.', array(), 'asf_product'));
		
		return $grid->getGridResponse('AsfProductBundle:Product:list.html.twig');
	}
	
	/**
	 * Add or edit a product
	 * 
	 * @param  integer $id           AsfProductBundle:Product Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for edit the product
	 * @throws \Exception            Error on product not found  
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction($id = null)
	{
		$securityContext = $this->get('security.context');
		
		if ( !is_null($id) ) {
			$product = $this->get('asf_product.product.manager')->getRepository()->findOneBy(array('id' => $id));
			if (false === $securityContext->isGranted('EDIT', $product))
				throw new AccessDeniedException();
			$success_message = $this->getTranslator()->trans('Updated successfully', array(), 'asf_product');
			
		} else {
			$product = $this->get('asf_product.product.manager')->createInstance();
			$product->setName($this->getTranslator()->trans('New product', array(), 'asf_product'))->setState(ProductModel::STATE_PUBLISHED);
			$success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
		}
		
		if ( is_null($product) )
			throw new \Exception($this->getTranslator()->trans('An error occurs when generating or getting the product', array(), 'asf_product'));

		$form = $this->get('asf_product.form.product')->setData($product);
		$formHandler = $this->get('asf_product.form.handler.product');
		
		if ( true === $formHandler->process() ) {
			try {
				$this->get('asf_ui.flash_message')->success($success_message);
				return $this->redirect($this->get('router')->generate('asf_product_product_edit', array('id' => $product->getId())));
				
			} catch(\Exception $e) {
				$this->get('asf_ui.flash_message')->danger($e->getMessage());
			}
		}
		
		return $this->render('AsfProductBundle:Product:edit.html.twig', array(
			'product' => $product, 
			'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a product
	 *
	 * @param  integer $id           AsfProductBundle:Product Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for delete the product
	 * @throws \Exception            Error on product not found or on removing element from DB
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
		$securityContext = $this->get('security.context');
		$product = $this->get('asf_product.product.manager')->getRepository()->findOneBy(array('id' => $id));
		if (false === $securityContext->isGranted('DELETE', $product))
			throw new AccessDeniedException();
	
		try {
			$this->get('asf_product.product.manager')->getEntityManager()->remove($product);
			$this->get('asf_product.product.manager')->getEntityManager()->flush();
				
			$this->get('asf_ui.flash_message')->success($this->getTranslator()->trans('The product "%name%" successfully deleted', array('%name%' => $product->getName()), 'asf_product'));
				
		} catch (\Exception $e) {
			$this->get('asf_ui.flash_message')->danger($e->getMessage());
		}
	
		return $this->redirect($this->get('router')->generate('asf_product_product_list'));
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
		
		return $this->render('AsfProductBundle:Product:suggest-product.html.twig', array(
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
}