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
use AppBundle\Entity\UserRole;
use Symfony\Component\EventDispatcher\Event;
use ASF\ProductBundle\Event\ProductEvents;
use Symfony\Component\HttpFoundation\Response;
use ASF\ProductBundle\Model\Brand\BrandModel;

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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $this->get('event_dispatcher')->dispatch(ProductEvents::LIST_PRODUCTS, new Event());
        
        // Set Datagrid source
        $source = new Entity($this->getParameter('asf_product.product.entity'));
        $tableAlias = $source->getTableAlias();
        $user = $this->getUser();
        $source->manipulateQuery(function ($query) use ($tableAlias, $user) {
            $query instanceof QueryBuilder;

            $states = array(
                ProductModel::STATE_DRAFT,
                ProductModel::STATE_WAITING,
                ProductModel::STATE_PUBLISHED,
            );

            if (in_array(UserRole::ROLE_SUPERADMIN, $user->getRoles())) {
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

        $source->manipulateRow(function ($row) {
            if (ProductModel::STATE_DELETED === $row->getField('state')) {
                $row->setClass('danger');
            } elseif (ProductModel::STATE_WAITING === $row->getField('state')) {
                $row->setClass('info');
            } elseif (ProductModel::STATE_DRAFT === $row->getField('state')) {
                $row->setClass('warning');
            }

            return $row;
        });

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
        $this->get('event_dispatcher')->dispatch(ProductEvents::EDIT_PRODUCT, new Event());
        
        $formFactory = $this->get('asf_product.form.factory.product');

        if (!is_null($id)) {
            $product = $this->getDoctrine()->getRepository($this->getParameter('asf_product.product.entity'))->findOneBy(array('id' => $id));
        } else {
            $product = $this->get('asf_product.manager')->createProductInstance();
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

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $product = $form->getData();
                if (is_null($product->getId())) {
                    $this->get('doctrine.orm.default_entity_manager')->persist($product);
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.product_created', array('%name%' => $product->getName()));
                } else {
                    $success_message = $this->get('translator')->trans('asf.product.msg.success.product_updated', array('%name%' => $product->getName()));
                }

                $this->get('doctrine.orm.default_entity_manager')->flush();

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
        $this->get('event_dispatcher')->dispatch(ProductEvents::DELETE_PRODUCT, new Event());
        
        $product = $this->getDoctrine()->getRepository($this->getParameter('asf_product.product.entity'))->findOneBy(array('id' => $id));

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
    
    /**
     * Return a list of product according to a search
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRequestAction(Request $request)
    {
        $terms = $request->get('q'); $result = array();
        $products = $this->get('asf_product.manager')->getProductsByKeywords($terms);
    
        foreach($products as $product) {
            $result[] = array(
                'id' => $product->getId(),
                'name' => $this->get('asf_product.manager')->getFormattedProductName($product)
            );
        }
    
        $response = new Response();
        $response->setContent(json_encode(array(
            'total_count' => count($result),
            'items' => $result,
        )));
    
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
        $term = $request->get('q'); $result = array();
        $products = $this->getDoctrine()->getRepository($this->getParameter('asf_product.product.entity'))->findBy(array('name' => $term));
    
        foreach($products as $product) {
            $result[$product->getId()] = $this->get('asf_product.manager')->getFormattedProductName($product);
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
        $terms = $request->get('term'); $result = array(); $productManager = $this->get('asf_product.manager');
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
    
        return $this->render('ASFProductBundle:Product:suggest-product.html.twig', array(
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
            $weight = $this->get('asf_product.manager')->findWeightPropertyInString($request->get('weight'));
            $capacity = $this->get('asf_product.manager')->findCapacityPropertyInString($request->get('capacity'));
            $productManager = $this->get('asf_product.manager');
            	
            $weight = is_null($weight) || $weight == '' ? null : $weight;
            $capacity = is_null($capacity) || $capacity == '' ? null : $capacity;
            	
            $product = $productManager->createProductInstance();
            $product->setName($product_name)->setState(ProductModel::STATE_PUBLISHED)
                ->setWeight($weight)->setCapacity($capacity)->setType(ProductModel::TYPE_PRODUCT);
            	
            if ( $brand_name != '' ) {
                $brand = $this->getDoctrine()->getRepository($this->getParameter('asf_product.brand.entity'))->findOneBy(array('name' => $brand_name));
                if ( is_null($brand) ) {
                    $brand = $this->get('asf_product.manager')->createBrandInstance();
                    $brand->setName($brand_name)->setState(BrandModel::STATE_PUBLISHED);
                }
                $product->setBrand($brand);
            }
            	
            $this->get('doctrine.orm.default_entity_manager')->persist($product);
            $this->get('doctrine.orm.default_entity_manager')->flush();
            
            $response->setContent(json_encode(array('name' => $productManager->getFormattedProductName($product))));
        } catch (\Exception $e) {
            $response->setContent(json_encode(array('error' => $e->getMessage())));
        }
        return $response;
    }
}
