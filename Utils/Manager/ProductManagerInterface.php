<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Utils\Manager;

use ASF\ProductBundle\Model\Product\ProductInterface;

/**
 * Product Bundle Manager.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
interface ProductManagerInterface
{
    /**
     * Create a Product Instance.
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function createProductInstance();

    /**
     * Create a Category Instance.
     *
     * @return \ASF\ProductBundle\Model\Category\CategoryInterface
     */
    public function createCategoryInstance();

    /**
     * Create a Brand Instance.
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function createBrandInstance();

    /**
     * Populate a new product.
     *
     * @param ProductInterface $product
     */
    public function populateProduct(ProductInterface $product);

    /**
     * Return a product based on keywords.
     *
     * @param string $keywords
     */
    public function getProductWithFormattedKeywords($keywords);

    /**
     * Return a list of products form list of keywords.
     *
     * @param string $keywords
     *
     * @return array
     */
    public function getProductsByKeywords($keywords);

    /**
     * Clean keywords for search in repository.
     *
     * @param string $keywords
     *
     * @return string
     */
    public function cleanKeywords($keywords);

    /**
     * Find a product name from a list of keywords.
     *
     * @param string $string
     * @param bool   $is_flat
     *
     * @return array
     */
    public function findProductNameInString($string, $is_flat = false);

    /**
     * Join keywords.
     *
     * @param array $keywords
     */
    public function joinKeywords($keywords);

    /**
     * Find a brand name from a list of keywords.
     *
     * @param string $string
     * @param bool   $is_flat
     *
     * @return array
     */
    public function findBrandNameInString($string, $is_flat = false);

    /**
     * Find weight property in string.
     *
     * @param string $string
     */
    public function findWeightPropertyInString($string);

    /**
     * Find capacity property in string.
     *
     * @param string $string
     */
    public function findCapacityPropertyInString($string);

    /**
     * Return formatted product name.
     *
     * @param ProductInterface $product
     *
     * @return string
     */
    public function getFormattedProductName(ProductInterface $product);

    /**
     * Find a product brand name from a list of keywords.
     *
     * @param string $string
     *
     * @return array
     */
    public function findProductBrandNameInString($string);
}
