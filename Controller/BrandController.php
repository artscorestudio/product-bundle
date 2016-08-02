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
        $source = new Entity($this->getParameter('asf_product.brand.entity'));
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
            $brand = $this->getDoctrine()->getRepository($this->getParameter('asf_product.brand.entity'))->findOneBy(array('id' => $id));
        } else {
            $brand = $brandManager->createInstance();
            $brand->setName($this->get('translator')->trans('asf.product.default_value.category_name'));
        }

        if (is_null($brand)) {
            throw new \Exception($this->get('translator')->trans('asf.product.msg.error.brand_not_found'));
        }
        
        $form = $formFactory->createForm();
        $form->setData($brand);
		$form->handleRequest($request);
		
        if ( $form->isSubmitted() && $form->isValid() ) {
            try {
                if (is_null($brand->getId())) {
                    $this->get('doctrine.orm.default_entity_manager')->persist($brand);
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.brand_created', array('%name%' => $brand->getName()));
                } else {
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.brand_updated', array('%name%' => $brand->getName()));
                }
                $this->get('doctrine.orm.default_entity_manager')->flush();

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
        $brand = $this->getDoctrine()->getRepository($this->getParameter('asf_product.brand.entity'))->findOneBy(array('id' => $id));

        try {
            $brand->setState(BrandModel::STATE_DELETED);
            $this->get('doctrine.orm.default_entity_manager')->flush();

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
