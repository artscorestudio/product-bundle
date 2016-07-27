<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Model\Brand;

use ASF\ProductBundle\Model\Product\ProductInterface;

/**
 * Brand Interface.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
interface BrandInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setState($state);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts();

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @see \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function addProduct(ProductInterface $product);

    /**
     * @param \ASF\ProductBundle\Model\Product\ProductInterface $product
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function removeProduct(ProductInterface $product);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $created_at
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setCreatedAt(\DateTime $created_at);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updated_at
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setUpdatedAt(\DateTime $updated_at);

    /**
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * @param \DateTime $deleted_at
     *
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function setDeletedAt(\DateTime $deleted_at);
}
