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

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use ASF\ProductBundle\Validator\Constraints as CategoryAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product Category.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @ORM\Entity(repositoryClass="ASF\ProductBundle\Repository\CategoryRepository")
 * @ORM\Table(name="asf_product_category")
 * @ORM\HasLifecycleCallbacks
 * 
 * @CategoryAssert\CategoryClass
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
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(visible=false)
     * 
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @GRID\Column(title="asf.product.category_name", defaultOperator="like", operatorsVisible=false)
     * 
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Choice(callback = "getStates")
     * @GRID\Column(title="asf.product.state", filter="select",  selectFrom="values", values={
     *     CategoryModel::STATE_DRAFT = "draft",
     *     CategoryModel::STATE_WAITING = "waiting",
     *     CategoryModel::STATE_PUBLISHED = "published",
     *     CategoryModel::STATE_DELETED = "deleted"
     * }, defaultOperator="eq", operatorsVisible=false)
     * 
     * @var string
     */
    protected $state;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @GRID\Column(visible=false)
     * 
     * @var \ASF\ProductBundle\Model\Category\CategoryInterface
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"persist", "remove"})
     * @GRID\Column(visible=false)
     * 
     * @var ArrayCollection
     */
    protected $children;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @GRID\Column(visible=false)
     *
     * @var \DateTime
     */
    protected $createdAt;
    
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @GRID\Column(visible=false)
     *
     * @var \DateTime
     */
    protected $updatedAt;
    
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @GRID\Column(visible=false)
     *
     * @var \DateTime
     */
    protected $deletedAt;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::getCreatedAt()
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::setCreatedAt()
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->createdAt = $created_at;
    
        return $this;
    }
    
    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::getUpdatedAt()
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::setUpdatedAt()
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updatedAt = $updated_at;
    
        return $this;
    }
    
    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::getDeletedAt()
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
    
    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Category\CategoryInterface::setDeletedAt()
     */
    public function setDeletedAt(\DateTime $deleted_at)
    {
        $this->deletedAt = $deleted_at;
    
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

    /**
     * @ORM\PreUpdate
     * @return void
     */
    public function onPreUpdate()
    {
        if ( self::STATE_DELETED === $this->state ) {
            $this->deletedAt = new \DateTime();
        }
        $this->updatedAt = new \DateTime();
    }
}
