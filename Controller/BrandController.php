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

use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\QueryBuilder;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;

use ASF\ProductBundle\Entity\BrandModel;
use ASF\ProductBundle\Form\Handler\BrandFormHandler;

/**
 * Artscore Studio Product Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class BrandController extends Controller
{
	/**
	 * List all brands
	 *
	 * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
			throw new AccessDeniedException();
		
		// Set Datagrid source
		$source = new Entity($this->get('asf_product.brand.manager')->getClassName());
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
		$grid->setId('asf_brands_list');
	
		// Columns configuration
		$grid->hideColumns(array('id', 'content'));
	
		$grid->getColumn('name')->setTitle($this->get('translator')->trans('Brand name', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false);
		
		$grid->getColumn('state')->setTitle($this->get('translator')->trans('State', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				BrandModel::STATE_DRAFT => $this->get('translator')->trans('Draft', array(), 'asf_product'),
				BrandModel::STATE_WAITING => $this->get('translator')->trans('Waiting', array(), 'asf_product'),
				BrandModel::STATE_PUBLISHED => $this->get('translator')->trans('Published', array(), 'asf_product'),
				BrandModel::STATE_DELETED => $this->get('translator')->trans('Deleted', array(), 'asf_product')
			));
			
		$grid->getColumn('createdAt')->setSize(100)->setTitle($this->get('translator')->trans('Created at', array(), 'asf_product'));
		$grid->getColumn('updatedAt')->setSize(100)->setTitle($this->get('translator')->trans('Updated at', array(), 'asf_product'));
		$grid->getColumn('deletedAt')->setSize(100)->setTitle($this->get('translator')->trans('Deleted at', array(), 'asf_product'));
	
		$editAction = new RowAction('btn_edit', 'asf_product_brand_edit');
		$editAction->setRouteParameters(array('id'));
		$grid->addRowAction($editAction);
	
		$deleteAction = new RowAction('btn_delete', 'asf_product_brand_delete', true);
		$deleteAction->setRouteParameters(array('id'))
			->setConfirmMessage($this->get('translator')->trans('Do you want to delete this brand ?', array(), 'asf_product'));
		$grid->addRowAction($deleteAction);
	
		$grid->setNoDataMessage($this->get('translator')->trans('No brands were found.', array(), 'asf_product'));
		
		return $grid->getGridResponse('ASFProductBundle:Brand:list.html.twig');
	}
	
	/**
	 * Add or edit a brand
	 * 
	 * @param Request $request
	 * @param integer $id           ASFProductBundle:Brand Entity ID
	 * 
	 * @throws AccessDeniedException If user does not have ACL's rights for edit the brand
	 * @throws \Exception            Error on brand not found  
	 * 
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction(Request $request, $id = null)
	{
	    if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
	        throw new AccessDeniedException();
	    
	    $formFactory = $this->get('asf_product.form.factory.brand');
	    $brandManager = $this->get('asf_product.brand.manager');
	    
		if ( !is_null($id) ) {
			$brand = $brandManager->getRepository()->findOneBy(array('id' => $id));
			$success_message = $this->get('translator')->trans('Updated successfully', array(), 'asf_product');
			
		} else {
			$brand = $brandManager->createInstance();
			$brand->setName($this->get('translator')->trans('New brand', array(), 'asf_product'));
			$success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
		}
		
		if ( is_null($brand) )
			throw new \Exception($this->get('translator')->trans('An error occurs when generating or getting the brand', array(), 'asf_product'));

		$form = $formFactory->createForm();
		$form->setData($brand);
		
		$formHandler = new BrandFormHandler($form, $this->container);
		
		if ( true === $formHandler->process() ) {
			try {
			    if ( is_null($brand->getId()) ) {
                    $brandManager->getEntityManager()->persist($brand);
			    }
			    $brandManager->getEntityManager()->flush();
			    
			    if ( $this->has('asf_layout.flash_message') ) {
			        $this->get('asf_layout.flash_message')->success($success_message);
			    }
				
				return $this->redirect($this->get('router')->generate('asf_product_brand_edit', array('id' => $brand->getId())));
				
			} catch(\Exception $e) {
			    if ( $this->has('asf_layout.flash_message') ) {
			        $this->get('asf_layout.flash_message')->danger($e->getMessage());
			    }
			}
		}
		
		return $this->render('ASFProductBundle:Brand:edit.html.twig', array(
			'brand' => $brand, 
			'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a brand
	 *
	 * @param  integer $id           ASFProductBundle:Brand Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for delete the brand
	 * @throws \Exception            Error on brand not found or on removing element from DB
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
	    if ( false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') )
	        throw new AccessDeniedException();
	    
		$brand = $this->get('asf_product.brand.manager')->getRepository()->findOneBy(array('id' => $id));
	
		try {
			$this->get('asf_product.brand.manager')->getEntityManager()->remove($brand);
			$this->get('asf_product.brand.manager')->getEntityManager()->flush();
			
			if ( $this->has('asf_layout.flash_message') ) {
                $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('The brand "%name%" successfully deleted.', array('%name%' => $brand->getName()), 'asf_product'));
			}
			
		} catch (\Exception $e) {
            if ( $this->has('asf_layout.flash_message') ) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            }
		}
	
		return $this->redirect($this->get('router')->generate('asf_product_brand_list'));
	}
}