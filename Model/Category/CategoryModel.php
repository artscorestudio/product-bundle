<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Model\Category;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product Category.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
abstract class CategoryModel implements CategoryInterface
{
    /**
     * All product category' states are hardcoded in constantes.
     * For historical features reasons, products are not completelly removed form the DB.
     */
    const STATE_DRAFT = 'draft';
    const STATE_WAITING = 'waiting';
    const STATE_PUBLISHED = 'published';
    const STATE_DELETED = 'deleted';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var \ASF\ProductBundle\Model\Category\CategoryInterface
     */
    protected $parent;

    /**
     * @var ArrayCollection
     */
    protected $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\Category::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\Category::setName()
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\Category::getState()
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\Category::setState()
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\Category::getParent()
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::setParent()
     */
    public function setParent(CategoryInterface $category)
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::getChildren()
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::addChild()
     */
    public function addChild(CategoryInterface $category)
    {
        $this->children->add($category);

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::removeChild()
     */
    public function removeChild(CategoryInterface $category)
    {
        $this->children->removeElement($category);

        return $this;
    }

    /**
     * Returns states for validators.
     *
     * @return multitype:string
     */
    public static function getStates()
    {
        return array(
            self::STATE_DRAFT,
            self::STATE_WAITING,
            self::STATE_PUBLISHED,
            self::STATE_DELETED,
        );
    }
}
