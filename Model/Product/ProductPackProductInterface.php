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
 * Product Pack Product Interface.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
interface ProductPackProductInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function getProductPack();

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductPackInterface
     */
    public function setProductPack(ProductPackInterface $product);

    /**
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function getProduct();

    /**
     * @param \ASF\ProductBundle\Model\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return numeric
     */
    public function getOrder();

    /**
     * @param numeric $order
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setOrder($order);
}
