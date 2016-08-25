<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Event;

/**
 * Contains all events thrown in the Product Bundle.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
final class ProductEvents
{
    /**
     * The LIST_PRODUCTS event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     * 
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const LIST_PRODUCTS = 'asf.product.event.list_products';
    
    /**
     * The EDIT_PRODUCT event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const EDIT_PRODUCT = 'asf.product.event.edit_product';
    
    /**
     * The DELETE_PRODUCT event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const DELETE_PRODUCT = 'asf.product.event.delete_product';
    
    /**
     * The LIST_CATEGORIES event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const LIST_CATEGORIES = 'asf.product.event.list_categories';
    
    /**
     * The EDIT_CATEGORY event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const EDIT_CATEGORY = 'asf.product.event.edit_category';
    
    /**
     * The DELETE_CATEGORY event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const DELETE_CATEGORY = 'asf.product.event.delete_category';
    
    /**
     * The LIST_BRANDS event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const LIST_BRANDS = 'asf.product.event.list_brands';
    
    /**
     * The EDIT_BRAND event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const EDIT_BRAND = 'asf.product.event.edit_brand';
    
    /**
     * The DELETE_BRAND event occurs at the very beginning of a controller action
     *
     * This event allows you to create custom controls like ACLs, etc. before
     * the execution of the logic of the controller action.
     *
     * @Event("Symfony\Component\HttpKernel\Event\GetResponseEvent")
     *
     * @var string
     */
    const DELETE_BRAND = 'asf.product.event.delete_brand';
}