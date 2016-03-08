<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Model\Product;

/**
 * Product Pack Interface
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
interface ProductPackInterface
{
    /**
     * @return ArrayCollection
     */
    public function getProducts();

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function addProduct(ProductInterface $product);

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function removeProduct(ProductInterface $product);
}