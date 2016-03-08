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
use Asf\Bundle\ProductBundle\Entity\CategoryModel;

/**
 * Artscore Studio Product Category Controller
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class CategoryController extends AsfController
{
	/**
	 * List all categories
	 *
	 * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function listAction()
	{
		if ( !$this->get('security.context')->isGranted('ROLE_ADMIN') )
			throw new AccessDeniedException();
		
		// Set Datagrid source
		$source = new Entity($this->get('asf_product.category.manager')->getClassName());
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
		$grid->setId('asf_categories_list');
	
		// Columns configuration
		$grid->hideColumns(array('id'));
	
		$nameColumn = $grid->getColumn('name');
		$nameColumn->setTitle($this->get('translator')->trans('Category name', array(), 'asf_product'))
			->setDefaultOperator('like')
			->setOperatorsVisible(false);
		
		$stateColumn = $grid->getColumn('state');
		$stateColumn->setTitle($this->get('translator')->trans('State', array(), 'asf_product'))
			->setFilterType('select')
			->setDefaultOperator('like')
			->setOperatorsVisible(false)
			->setSelectFrom('values')
			->setValues(array(
				CategoryModel::STATE_DRAFT => $this->getTranslator()->trans('Draft', array(), 'asf_product'), 
				CategoryModel::STATE_WAITING => $this->getTranslator()->trans('Waiting', array(), 'asf_product'),
				CategoryModel::STATE_PUBLISHED => $this->getTranslator()->trans('Published', array(), 'asf_product'),
				CategoryModel::STATE_DELETED => $this->getTranslator()->trans('Deleted', array(), 'asf_product')
			));
	
		$editAction = new RowAction('btn_edit', 'asf_product_category_edit');
		$editAction->setRouteParameters(array('id'));
		$grid->addRowAction($editAction);
	
		$deleteAction = new RowAction('btn_delete', 'asf_product_category_delete', true);
		$deleteAction->setRouteParameters(array('id'))
			->setConfirmMessage($this->get('translator')->trans('Do you want to delete this category ?', array(), 'asf_product'));
		$grid->addRowAction($deleteAction);
	
		$grid->setNoDataMessage($this->getTranslator()->trans('No category was found.', array(), 'asf_product'));
		
		return $grid->getGridResponse('AsfProductBundle:Category:list.html.twig');
	}
	
	/**
	 * Add or edit a category
	 * 
	 * @param  integer $id           AsfProductBundle:Category Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for edit the category
	 * @throws \Exception            Error on category not found  
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function editAction($id = null)
	{
		$securityContext = $this->get('security.context');
		
		if ( !is_null($id) ) {
			$category = $this->get('asf_product.category.manager')->getRepository()->findOneBy(array('id' => $id));
			if (false === $securityContext->isGranted('EDIT', $category))
				throw new AccessDeniedException();
			
			$success_message = $this->getTranslator()->trans('Updated successfully', array(), 'asf_product');
			
		} else {
			$category = $this->get('asf_product.category.manager')->createInstance();

			$category->setName($this->getTranslator()->trans('New category', array(), 'asf_product'));
			$success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
		}
		
		if ( is_null($category) )
			throw new \Exception($this->getTranslator()->trans('An error occurs when generating or getting the category', array(), 'asf_product'));

		$form = $this->get('asf_product.form.category')->setData($category);
		$formHandler = $this->get('asf_product.form.handler.category');
		
		if ( true === $formHandler->process() ) {
			try {
				$this->get('asf_ui.flash_message')->success($success_message);
				return $this->redirect($this->get('router')->generate('asf_product_category_edit', array('id' => $category->getId())));
				
			} catch(\Exception $e) {
				$this->get('asf_ui.flash_message')->danger($e->getMessage());
			}
		}
		
		return $this->render('AsfProductBundle:Category:edit.html.twig', array(
			'category' => $category, 
			'form' => $form->createView()
		));
	}
	
	/**
	 * Delete a category
	 *
	 * @param  integer $id           AsfProductBundle:Category Entity ID
	 * @throws AccessDeniedException If user does not have ACL's rights for delete the category
	 * @throws \Exception            Error on category not found or on removing element from DB
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteAction($id)
	{
		$securityContext = $this->get('security.context');
		$category = $this->get('asf_product.category.manager')->getRepository()->findOneBy(array('id' => $id));
		if (false === $securityContext->isGranted('DELETE', $category))
			throw new AccessDeniedException();

		try {
			$this->get('asf_product.category.manager')->removeCategory($category);
			$this->get('asf_product.category.manager')->getEntityManager()->flush();
				
			$this->get('asf_ui.flash_message')->success($this->getTranslator()->trans('The category "%name%" successfully deleted', array('%name%' => $category->getName()), 'asf_product'));
				
		} catch (\Exception $e) {
			$this->get('asf_ui.flash_message')->danger($e->getMessage());
		}
	
		return $this->redirect($this->get('router')->generate('asf_product_category_list'));
	}
}