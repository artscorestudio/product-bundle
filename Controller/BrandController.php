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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use Asf\Bundle\ProductBundle\Entity\BrandModel;

/**
 * Artscore Studio Product Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class BrandController extends AsfController
{
	/**
	 * List all brands
	 *
	 * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		if ( !$this->get('security.context')->isGranted('ROLE_ADMIN') )
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
	
		$grid->getColumn('name')->setTitle($this->getTranslator()->trans('Brand name', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false);
		
		$grid->getColumn('state')->setTitle($this->getTranslator()->trans('State', array(), 'asf_product'))
			->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
			->setDefaultOperator('eq')->setValues(array(
				BrandModel::STATE_DRAFT => $this->getTranslator()->trans('Draft', array(), 'asf_product'),
				BrandModel::STATE_WAITING => $this->getTranslator()->trans('Waiting', array(), 'asf_product'),
				BrandModel::STATE_PUBLISHED => $this->getTranslator()->trans('Published', array(), 'asf_product'),
				BrandModel::STATE_DELETED => $this->getTranslator()->trans('Deleted', array(), 'asf_product')
			));
			
		$grid->getColumn('createdAt')->setSize(100)->setTitle($this->getTranslator()->trans('Created at', array(), 'asf_product'));
		$grid->getColumn('updatedAt')->setSize(100)->setTitle($this->getTranslator()->trans('Updated at', array(), 'asf_product'));
		$grid->getColumn('deletedAt')->setSize(100)->setTitle($this->getTranslator()->trans('Deleted at', array(), 'asf_product'));
	
		$editAction = new RowAction('btn_edit', 'asf_product_brand_edit');
		$editAction->setRouteParameters(array('id'));
		$grid->addRowAction($editAction);
	
		$deleteAction = new RowAction('btn_delete', 'asf_product_brand_delete', true);
		$deleteAction->setRouteParameters(array('id'))
			->setConfirmMessage($this->get('translator')->trans('Do you want to delete this brand ?', array(), 'asf_product'));
		$grid->addRowAction($deleteAction);
	
		$grid->setNoDataMessage($this->getTranslator()->trans('No brands were found.', array(), 'asf_product'));
		
		return $grid->getGridResponse('AsfProductBundle:Brand:list.html.twig');
	}
	
	/**
	 * Add or edit a brand
	 * 
	 * @param  integer $id           AsfProductBundle:Brand Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for edit the brand
	 * @throws \Exception            Error on brand not found  
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction($id = null)
	{
		$securityContext = $this->get('security.context');
		
		if ( !is_null($id) ) {
			$brand = $this->get('asf_product.brand.manager')->getRepository()->findOneBy(array('id' => $id));
			if (false === $securityContext->isGranted('EDIT', $brand))
				throw new AccessDeniedException();
			
			$success_message = $this->getTranslator()->trans('Updated successfully', array(), 'asf_product');
			
		} else {
			$brand = $this->get('asf_product.brand.manager')->createInstance();

			$brand->setName($this->getTranslator()->trans('New brand', array(), 'asf_product'));
			$success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
		}
		
		if ( is_null($brand) )
			throw new \Exception($this->getTranslator()->trans('An error occurs when generating or getting the brand', array(), 'asf_product'));

		$form = $this->get('asf_product.form.brand')->setData($brand);
		$formHandler = $this->get('asf_product.form.handler.brand');
		
		if ( true === $formHandler->process() ) {
			try {
				$this->get('asf_ui.flash_message')->success($success_message);
				return $this->redirect($this->get('router')->generate('asf_product_brand_edit', array('id' => $brand->getId())));
				
			} catch(\Exception $e) {
				$this->get('asf_ui.flash_message')->danger($e->getMessage());
			}
		}
		
		return $this->render('AsfProductBundle:Brand:edit.html.twig', array(
			'brand' => $brand, 
			'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a brand
	 *
	 * @param  integer $id           AsfProductBundle:Brand Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for delete the brand
	 * @throws \Exception            Error on brand not found or on removing element from DB
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
		$securityContext = $this->get('security.context');
		$brand = $this->get('asf_product.brand.manager')->getRepository()->findOneBy(array('id' => $id));
		if (false === $securityContext->isGranted('DELETE', $brand))
			throw new AccessDeniedException();
	
		try {
			$this->get('asf_product.brand.manager')->getEntityManager()->remove($brand);
			$this->get('asf_product.brand.manager')->getEntityManager()->flush();
				
			$this->get('asf_ui.flash_message')->success($this->getTranslator()->trans('The brand "%name%" successfully deleted.', array('%name%' => $brand->getName()), 'asf_product'));
				
		} catch (\Exception $e) {
			$this->get('asf_ui.flash_message')->danger($e->getMessage());
		}
	
		return $this->redirect($this->get('router')->generate('asf_product_brand_list'));
	}

	/**
	 * Return a list of brand according to a search
	 * 
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxRequestAction(Request $request)
	{
		$term = $request->get('term');
		$brands = $this->get('asf_product.brand.manager')->getRepository()->findByNameContains($term);
		$search = array();
		
		foreach($brands as $brand) {
			$search[] = $brand->getName();
		}
		
		$response = new Response();
		$response->setContent(json_encode($search));
		
		return $response;
	}
	
	/**
	 * Return list of brand via an ajax request for search on exactly term
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxRequestNameAction(Request $request)
	{
		$term = $request->get('term');
		$brands = $this->get('asf_product.brand.manager')->getRepository()->findBy(array('name' => $term));
		$search = array();
		
		foreach($brands as $brand) {
			$search[] = $brand->getName();
		}
		
		$response = new Response();
		$response->setContent(json_encode($search));
		
		return $response;
	}
}