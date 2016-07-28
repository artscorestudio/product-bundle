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
use Doctrine\ORM\QueryBuilder;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use ASF\ProductBundle\Model\Category\CategoryModel;
use ASF\ProductBundle\Form\Handler\CategoryFormHandler;

/**
 * Artscore Studio Product Category Controller.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class CategoryController extends Controller
{
    /**
     * List all categories.
     *
     * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        // Set Datagrid source
        $source = new Entity($this->get('asf_product.category.manager')->getClassName());
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(function ($query) use ($tableAlias) {
            $query instanceof QueryBuilder;

            if (count($query->getDQLPart('orderBy')) == 0) {
                $query->orderBy($tableAlias.'.name', 'ASC');
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
        $nameColumn->setTitle($this->get('translator')->trans('asf.product.category_name'))
            ->setDefaultOperator('like')
            ->setOperatorsVisible(false);

        $stateColumn = $grid->getColumn('state');
        $stateColumn->setTitle($this->get('translator')->trans('asf.product.state'))
            ->setFilterType('select')
            ->setDefaultOperator('like')
            ->setOperatorsVisible(false)
            ->setSelectFrom('values')
            ->setValues(array(
                CategoryModel::STATE_DRAFT => $this->get('translator')->trans('asf.product.state.draft'),
                CategoryModel::STATE_WAITING => $this->get('translator')->trans('asf.product.state.waiting'),
                CategoryModel::STATE_PUBLISHED => $this->get('translator')->trans('asf.product.state.published'),
                CategoryModel::STATE_DELETED => $this->get('translator')->trans('asf.product.state.deleted'),
            ));

        $editAction = new RowAction('btn_edit', 'asf_product_category_edit');
        $editAction->setRouteParameters(array('id'));
        $grid->addRowAction($editAction);

        $deleteAction = new RowAction('btn_delete', 'asf_product_category_delete', true);
        $deleteAction->setRouteParameters(array('id'))
            ->setConfirmMessage($this->get('translator')->trans('asf.product.msg.delete.confirm', array('%name%' => $this->get('translator')->trans('asf.product.default_value.this_category'))));
        $grid->addRowAction($deleteAction);

        $grid->setNoDataMessage($this->get('translator')->trans('No category was found.', array(), 'asf_product'));

        return $grid->getGridResponse('ASFProductBundle:Category:list.html.twig');
    }

    /**
     * Add or edit a category.
     * 
     * @param int $id ASFProductBundle:Category Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for edit the category
     * @throws \Exception            Error on category not found  
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id = null)
    {
        $formFactory = $this->get('asf_product.form.factory.category');
        $categoryManager = $this->get('asf_product.category.manager');

        if (!is_null($id)) {
            $category = $categoryManager->getRepository()->findOneBy(array('id' => $id));
            $success_message = $this->get('translator')->trans('Updated successfully', array(), 'asf_product');
        } else {
            $category = $categoryManager->createInstance();
            $category->setName($this->get('translator')->trans('New category', array(), 'asf_product'));
            $success_message = $this->get('translator')->trans('Created successfully', array(), 'asf_product');
        }

        if (is_null($category)) {
            throw new \Exception($this->get('translator')->trans('An error occurs when generating or getting the category', array(), 'asf_product'));
        }

        $form = $formFactory->createForm();
        $form->setData($category);
        $formHandler = new CategoryFormHandler($form, $this->get('request_stack')->getCurrentRequest(), $this->get('asf_product.category.manager'));

        if (true === $formHandler->process()) {
            try {
                if (is_null($category->getId())) {
                    $categoryManager->getEntityManager()->persist($category);
                }
                $categoryManager->getEntityManager()->flush();

                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->success($success_message);
                }

                return $this->redirect($this->get('router')->generate('asf_product_category_edit', array('id' => $category->getId())));
            } catch (\Exception $e) {
                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->danger($e->getMessage());
                }
            }
        }

        return $this->render('ASFProductBundle:Category:edit.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Delete a category.
     *
     * @param int $id ASFProductBundle:Category Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for delete the category
     * @throws \Exception            Error on category not found or on removing element from DB
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $category = $this->get('asf_product.category.manager')->getRepository()->findOneBy(array('id' => $id));

        try {
            $this->get('asf_product.category.manager')->removeCategory($category);
            $this->get('asf_product.category.manager')->getEntityManager()->flush();

            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('The category "%name%" successfully deleted', array('%name%' => $category->getName()), 'asf_product'));
            }
        } catch (\Exception $e) {
            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            }
        }

        return $this->redirect($this->get('router')->generate('asf_product_category_list'));
    }
}
