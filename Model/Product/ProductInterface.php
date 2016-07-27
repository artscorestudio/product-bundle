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

use ASF\ProductBundle\Model\Category\CategoryInterface;

/**
 * Product Interface.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
interface ProductInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setState($state);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategories();

    /**
     * @param \ASF\ProductBundle\Model\Category\CategoryInterface $category
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function addCategory(CategoryInterface $category);

    /**
     * @param \ASF\ProductBundle\Model\Category\CategoryInterface $category
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function removeCategory(CategoryInterface $category);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $created_at
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setCreatedAt(\DateTime $created_at);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updated_at
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setUpdatedAt(\DateTime $updated_at);

    /**
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * @param \DateTime $deleted_at
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setDeletedAt(\DateTime $deleted_at);
}
