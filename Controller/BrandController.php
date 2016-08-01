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
use Doctrine\ORM\QueryBuilder;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use ASF\ProductBundle\Model\Brand\BrandModel;
use ASF\ProductBundle\Form\Handler\BrandFormHandler;

/**
 * Artscore Studio Product Controller.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class BrandController extends Controller
{
    /**
     * List all brands.
     *
     * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        // Set Datagrid source
        $source = new Entity($this->get('asf_product.brand.manager')->getClassName());
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
        $grid->setId('asf_brands_list');

        // Columns configuration
        $grid->hideColumns(array('id', 'content'));

        $grid->getColumn('name')->setTitle($this->get('translator')->trans('asf.product.brand_name'))
            ->setDefaultOperator('like')
            ->setOperatorsVisible(false);

        $grid->getColumn('state')->setTitle($this->get('translator')->trans('asf.product.state'))
            ->setFilterType('select')->setSelectFrom('values')->setOperatorsVisible(false)
            ->setDefaultOperator('eq')->setValues(array(
                BrandModel::STATE_DRAFT => $this->get('translator')->trans('asf.product.state.draft'),
                BrandModel::STATE_WAITING => $this->get('translator')->trans('asf.product.state.waiting'),
                BrandModel::STATE_PUBLISHED => $this->get('translator')->trans('asf.product.state.published'),
                BrandModel::STATE_DELETED => $this->get('translator')->trans('asf.product.state.deleted'),
            ));

        $grid->getColumn('createdAt')->setSize(100)->setTitle($this->get('translator')->trans('asf.product.created_at'));
        $grid->getColumn('updatedAt')->setSize(100)->setTitle($this->get('translator')->trans('asf.product.updated_at'));
        $grid->getColumn('deletedAt')->setSize(100)->setTitle($this->get('translator')->trans('asf.product.deleted_at'));

        $editAction = new RowAction('btn_edit', 'asf_product_brand_edit');
        $editAction->setRouteParameters(array('id'));
        $grid->addRowAction($editAction);

        $deleteAction = new RowAction('btn_delete', 'asf_product_brand_delete', true);
        $deleteAction->setRouteParameters(array('id'))
            ->setConfirmMessage($this->get('translator')->trans('asf.product.msg.delete.confirm', array('%name%' => $this->get('translator')->trans('asf.product.default_value.this_brand'))));
        $grid->addRowAction($deleteAction);

        $grid->setNoDataMessage($this->get('translator')->trans('asf.product.msg.list.no_brand'));

        return $grid->getGridResponse('ASFProductBundle:Brand:list.html.twig');
    }

    /**
     * Add or edit a brand.
     * 
     * @param Request $request
     * @param int     $id      ASFProductBundle:Brand Entity ID
     * 
     * @throws AccessDeniedException If user does not have ACL's rights for edit the brand
     * @throws \Exception            Error on brand not found  
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        $formFactory = $this->get('asf_product.form.factory.brand');
        $brandManager = $this->get('asf_product.brand.manager');

        if (!is_null($id)) {
            $brand = $brandManager->getRepository()->findOneBy(array('id' => $id));
            $success_message = $this->get('translator')->trans('asf.product.msg.success.brand_updated', array('%name%' => $brand->getName()));
        } else {
            $brand = $brandManager->createInstance();
            $brand->setName($this->get('translator')->trans('asf.product.default_value.category_name'));
            $success_message = $this->get('translator')->trans('asf.product.msg.success.brand_created', array('%name%' => $brand->getName()));
        }

        if (is_null($brand)) {
            throw new \Exception($this->get('translator')->trans('asf.product.msg.error.brand_not_found'));
        }

        $form = $formFactory->createForm();
        $form->setData($brand);

        $formHandler = new BrandFormHandler($form, $this->container);

        if (true === $formHandler->process()) {
            try {
                if (is_null($brand->getId())) {
                    $brandManager->getEntityManager()->persist($brand);
                }
                $brandManager->getEntityManager()->flush();

                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->success($success_message);
                }

                return $this->redirect($this->get('router')->generate('asf_product_brand_edit', array('id' => $brand->getId())));
            } catch (\Exception $e) {
                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->danger($e->getMessage());
                } else {
                    return $e;
                }
            }
        }

        return $this->render('ASFProductBundle:Brand:edit.html.twig', array(
            'brand' => $brand,
            'form' => $form->createView(),
        ));
    }

    /**
     * Delete a brand.
     *
     * @param int $id ASFProductBundle:Brand Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for delete the brand
     * @throws \Exception            Error on brand not found or on removing element from DB
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $brand = $this->get('asf_product.brand.manager')->getRepository()->findOneBy(array('id' => $id));

        try {
            $brand->setState(BrandModel::STATE_DELETED);
            $this->get('asf_product.brand.manager')->getEntityManager()->flush();

            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('asf.product.msg.success.brand_deleted', array('%name%' => $brand->getName())));
            }
        } catch (\Exception $e) {
            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            } else {
                return $e;
            }
        }

        return $this->redirect($this->get('router')->generate('asf_product_brand_list'));
    }
}
