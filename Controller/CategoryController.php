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
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\UserRole;

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
        $source = new Entity($this->getParameter('asf_product.category.entity'));
        $tableAlias = $source->getTableAlias();
        $user = $this->getUser();
        $source->manipulateQuery(function ($query) use ($tableAlias, $user) {
            $query instanceof QueryBuilder;

            $states = array(
                CategoryModel::STATE_DRAFT,
                CategoryModel::STATE_WAITING,
                CategoryModel::STATE_PUBLISHED
            );
            
            if ( in_array(UserRole::ROLE_SUPERADMIN, $user->getRoles()) ) {
                $states[] = CategoryModel::STATE_DELETED;
            }
            
            $query->add('where', $query->expr()->in($tableAlias.'.state', $states));
            
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
        
        $source->manipulateRow(function($row) {
            if ( CategoryModel::STATE_DELETED === $row->getField('state') ) {
                $row->setClass('danger');
            } else if ( CategoryModel::STATE_WAITING === $row->getField('state') ) {
                $row->setClass('info');
            } else if ( CategoryModel::STATE_DRAFT === $row->getField('state') ) {
                $row->setClass('warning');
            }
            return $row;
        });
        
        // Columns configuration
        $editAction = new RowAction('btn_edit', 'asf_product_category_edit');
        $editAction->setRouteParameters(array('id'));
        $grid->addRowAction($editAction);

        $deleteAction = new RowAction('btn_delete', 'asf_product_category_delete', true);
        $deleteAction->setRouteParameters(array('id'))
            ->setConfirmMessage($this->get('translator')->trans('asf.product.msg.delete.confirm', array('%name%' => $this->get('translator')->trans('asf.product.default_value.this_category'))));
        $grid->addRowAction($deleteAction);

        $grid->setNoDataMessage($this->get('translator')->trans('asf.product.msg.list.no_category', array(), 'asf_product'));

        return $grid->getGridResponse('ASFProductBundle:Category:list.html.twig');
    }

    /**
     * Add or edit a category.
     * 
     * @param Request $request
     * @param int     $id      ASFProductBundle:Category Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for edit the category
     * @throws \Exception            Error on category not found  
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        $formFactory = $this->get('asf_product.form.factory.category');
        $categoryManager = $this->get('asf_product.category.manager');

        if (!is_null($id)) {
            $category = $this->getDoctrine()->getRepository($this->getParameter('asf_product.category.entty'))->findOneBy(array('id' => $id));
        } else {
            $category = $categoryManager->createInstance();
            $category->setName($this->get('translator')->trans('asf.product.default_value.category_name'));
        }

        if (is_null($category)) {
            throw new \Exception($this->get('translator')->trans('asf.product.msg.error.category_not_found'));
        }

        $form = $formFactory->createForm();
        $form->setData($category);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            try {
                if (is_null($category->getId())) {
                    $this->get('doctrine.orm.default_entity_manager')->persist($category);
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.category_created', array('%name%' => $category->getName()));
                } else {
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.category_updated', array('%name%' => $category->getName()));
                }
                $this->get('doctrine.orm.default_entity_manager')->flush();

                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->success($success_message);
                }

                return $this->redirect($this->get('router')->generate('asf_product_category_edit', array('id' => $category->getId())));
            } catch (\Exception $e) {
                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->danger($e->getMessage());
                } else {
                    return $e;
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
        $category = $this->getDoctrine()->getRepository($this->getParameter('asf_product.category.entity'))->findOneBy(array('id' => $id));

        try {
            $category->setState(CategoryModel::STATE_DELETED);
            $this->get('doctrine.orm.default_entity_manager')->flush();

            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('asf.product.msg.success.category_deleted', array('%name%' => $category->getName())));
            }
        } catch (\Exception $e) {
            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            } else {
                return $e;
            }
        }

        return $this->redirect($this->get('router')->generate('asf_product_category_list'));
    }
}
