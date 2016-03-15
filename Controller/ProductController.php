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
use Symfony\Component\Finder\Exception\AccessDeniedException;

use Doctrine\ORM\QueryBuilder;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;

use ASF\ProductBundle\Entity\ProductModel;
use ASF\ProductBundle\Form\Handler\ProductFormHandler;

/**
 * Artscore Studio Product Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductController extends Controller
{
	/**
	 * List all products
	 *
	 * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
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
	
		$grid->getColumn('name')->setTitle($this->get('translator')->trans('Product name', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false);
		
		$grid->getColumn('state')->setTitle($this->get('translator')->trans('State', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				ProductModel::STATE_DRAFT => $this->get('translator')->trans('Draft', array(), 'asf_product'),
				ProductModel::STATE_WAITING => $this->get('translator')->trans('Waiting', array(), 'asf_product'),
				ProductModel::STATE_PUBLISHED => $this->get('translator')->trans('Published', array(), 'asf_product'),
				ProductModel::STATE_DELETED => $this->get('translator')->trans('Deleted', array(), 'asf_product')
			));
			
		$grid->getColumn('type')->setTitle($this->get('translator')->trans('Type', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				ProductModel::TYPE_PRODUCT => $this->get('translator')->trans('Product', array(), 'asf_product'),
				ProductModel::TYPE_PRODUCT_PACK => $this->get('translator')->trans('ProductPack', array(), 'asf_product')
			));
			
		$grid->getColumn('weight')->setTitle($this->get('translator')->trans('Weight (Kg)', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false)
			->setSize(100);
		
		$grid->getColumn('capacity')->setTitle($this->get('translator')->trans('Capacity (Liter)', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false)
			->setSize(100);
			
		$grid->getColumn('createdAt')->setSize(100)->setTitle($this->get('translator')->trans('Created at', array(), 'asf_product'));
		$grid->getColumn('updatedAt')->setSize(100)->setTitle($this->get('translator')->trans('Updated at', array(), 'asf_product'));
		$grid->getColumn('deletedAt')->setSize(100)->setTitle($this->get('translator')->trans('Deleted at', array(), 'asf_product'));
        
		$editAction = new RowAction('btn_edit', 'asf_product_product_edit');
		$editAction->setRouteParameters(array('id'));
		$grid->addRowAction($editAction);
	
		$deleteAction = new RowAction('btn_delete', 'asf_product_product_delete', true);
		$deleteAction->setRouteParameters(array('id'))
			->setConfirmMessage($this->get('translator')->trans('Do you want to delete this product ?', array(), 'asf_product'));
		$grid->addRowAction($deleteAction);
	
		$grid->setNoDataMessage($this->get('translator')->trans('No product was found.', array(), 'asf_product'));
		
		return $grid->getGridResponse('ASFProductBundle:Product:list.html.twig');
	}
	
	/**
	 * Add or edit a product
	 * 
	 * @param  Request $request
	 * @param  integer $id           ASFProductBundle:Product Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for edit the product
	 * @throws \Exception            Error on product not found  
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id = null)
	{
		if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
			throw new AccessDeniedException();
		
		$formFactory = $this->get('asf_product.form.factory.product');
		$productManager = $this->get('asf_product.product.manager');
		
		if ( !is_null($id) ) {
			$product = $productManager->getRepository()->findOneBy(array('id' => $id));
			$success_message = $this->get('translator')->trans('Updated successfully', array(), 'asf_product');
			
		} else {
			$product = $productManager->createInstance();
			
			$product->setName($this->get('translator')->trans('New product', array(), 'asf_product'))->setState(ProductModel::STATE_PUBLISHED);
			$success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
		}
		
		if ( is_null($product) )
			throw new \Exception($this->get('translator')->trans('An error occurs when generating or getting the product', array(), 'asf_product'));

		$form = $formFactory->createForm();
		$form->setData($product);
		$formHandler = new ProductFormHandler($form, $this->get('request_stack')->getCurrentRequest(), $this->get('asf_product.product.manager'));
		
		if ( true === $formHandler->process() ) {
			try {
			    if ( is_null($product->getId()) ) {
			        $this->get('asf_product.product.manager')->getEntityManager()->persist($product);
			    }
			     
			    $this->get('asf_product.product.manager')->getEntityManager()->flush();
			     
			    if ( $this->has('asf_layout.flash_message') ) {
			        $this->get('asf_layout.flash_message')->success($success_message);
			    }
				return $this->redirect($this->get('router')->generate('asf_product_product_edit', array('id' => $product->getId())));
				
			} catch(\Exception $e) {
                if ( $this->has('asf_layout.flash_message') ) {
			        $this->get('asf_layout.flash_message')->danger($e->getMessage());
			    }
			}
		}
		
		return $this->render('ASFProductBundle:Product:edit.html.twig', array(
			'product' => $product, 
			'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a product
	 *
	 * @param  integer $id           ASFProductBundle:Product Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for delete the product
	 * @throws \Exception            Error on product not found or on removing element from DB
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
		if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
			throw new AccessDeniedException();
		
		$product = $this->get('asf_product.product.manager')->getRepository()->findOneBy(array('id' => $id));

		try {
			$this->get('asf_product.product.manager')->getEntityManager()->remove($product);
			$this->get('asf_product.product.manager')->getEntityManager()->flush();
			
			if ( $this->has('asf_layout.flash_message') ) {
			    $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('The product "%name%" successfully deleted', array('%name%' => $product->getName()), 'asf_product'));
			}
				
		} catch (\Exception $e) {
            if ( $this->has('asf_layout.flash_message') ) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            }
		}
	
		return $this->redirect($this->get('router')->generate('asf_product_product_list'));
	}
}