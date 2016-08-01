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
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Doctrine\ORM\QueryBuilder;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Source\Entity;
use ASF\ProductBundle\Model\Product\ProductModel;
use ASF\ProductBundle\Form\Handler\ProductFormHandler;
use AppBundle\Entity\UserRole;

/**
 * Artscore Studio Product Controller.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class ProductController extends Controller
{
    /**
     * List all products.
     *
     * @throws AccessDeniedException If authenticate user is not allowed to access to this resource (minimum ROLE_ADMIN)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        // Set Datagrid source
        $source = new Entity($this->get('asf_product.product.manager')->getClassName());
        $tableAlias = $source->getTableAlias();
        $user = $this->getUser();
        $source->manipulateQuery(function ($query) use ($tableAlias, $user) {
            $query instanceof QueryBuilder;

            $states = array(
                ProductModel::STATE_DRAFT,
                ProductModel::STATE_WAITING,
                ProductModel::STATE_PUBLISHED
            );
            
            if ( in_array(UserRole::ROLE_SUPERADMIN, $user->getRoles()) ) {
                $states[] = ProductModel::STATE_DELETED;
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
        $grid->setId('asf_products_list');

        // Columns configuration
        $editAction = new RowAction('btn_edit', 'asf_product_product_edit');
        $editAction->setRouteParameters(array('id'));
        $grid->addRowAction($editAction);

        $deleteAction = new RowAction('btn_delete', 'asf_product_product_delete', true);
        $deleteAction->setRouteParameters(array('id'))
            ->setConfirmMessage($this->get('translator')->trans('asf.product.msg.delete.confirm', array('%name%' => $this->get('translator')->trans('asf.product.default_value.this_product'))));
        $grid->addRowAction($deleteAction);

        $grid->setNoDataMessage($this->get('translator')->trans('asf.product.msg.list.no_product'));

        return $grid->getGridResponse('ASFProductBundle:Product:list.html.twig');
    }

    /**
     * Add or edit a product.
     * 
     * @param Request $request
     * @param int     $id      ASFProductBundle:Product Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for edit the product
     * @throws \Exception            Error on product not found  
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id = null)
    {
        $formFactory = $this->get('asf_product.form.factory.product');
        $productManager = $this->get('asf_product.product.manager');

        if (!is_null($id)) {
            $product = $productManager->getRepository()->findOneBy(array('id' => $id));
        } else {
            $product = $productManager->createInstance();

            $product->setName($this->get('translator')->trans('asf.product.default_value.product_name'))
                ->setState(ProductModel::STATE_PUBLISHED)
                ->setType(ProductModel::TYPE_PRODUCT);
        }

        if (is_null($product)) {
            throw new \Exception($this->get('translator')->trans('asf.product.msg.error.product_not_found'));
        }

        $form = $formFactory->createForm();
        $form->setData($product);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            try {
                $product = $form->getData();
                if (is_null($product->getId())) {
                    $this->get('asf_product.product.manager')->getEntityManager()->persist($product);
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.product_created', array('%name%' => $product->getName()));
                } else {
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.product_updated', array('%name%' => $product->getName()));
                }

                $this->get('asf_product.product.manager')->getEntityManager()->flush();

                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->success($success_message);
                }

                return $this->redirect($this->get('router')->generate('asf_product_product_edit', array('id' => $product->getId())));
            } catch (\Exception $e) {
                if ($this->has('asf_layout.flash_message')) {
                    $this->get('asf_layout.flash_message')->danger($e->getMessage());
                } else {
                    return $e;
                }
            }
        }

        return $this->render('ASFProductBundle:Product:edit.html.twig', array(
            'product' => $product,
            'form' => $form->createView(),
        ));
    }

    /**
     * Delete a product.
     *
     * @param int $id ASFProductBundle:Product Entity ID
     *
     * @throws AccessDeniedException If user does not have ACL's rights for delete the product
     * @throws \Exception            Error on product not found or on removing element from DB
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $product = $this->get('asf_product.product.manager')->getRepository()->findOneBy(array('id' => $id));

        try {
            $product->setState(ProductModel::STATE_DELETED);
            $this->get('doctrine.orm.default_entity_manager')->flush();

            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->success($this->get('translator')->trans('asf.product.msg.success.product_deleted', array('%name%' => $product->getName())));
            }
        } catch (\Exception $e) {
            if ($this->has('asf_layout.flash_message')) {
                $this->get('asf_layout.flash_message')->danger($e->getMessage());
            } else {
                return $e;
            }
        }

        return $this->redirect($this->get('router')->generate('asf_product_product_list'));
    }
}
