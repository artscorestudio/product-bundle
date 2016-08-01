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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ASF\ProductBundle\Validator\Constraints as ProductAssert;
use ASF\ProductBundle\Model\Brand\BrandInterface;
use ASF\ProductBundle\Model\Category\CategoryInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product Model.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @ORM\Entity(repositoryClass="ASF\ProductBundle\Repository\ProductRepository")
 * @ORM\Table(name="asf_product")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"product"="Product", "ProductPack"="ProductPack"})
 * @ORM\HasLifecycleCallbacks
 */
abstract class ProductModel implements ProductInterface
{
    /**
     * All product's states are hardcoded in constantes.
     * For historical features reasons, products are not completelly removed form the DB.
     */
    const STATE_DRAFT = 'draft';
    const STATE_WAITING = 'waiting';
    const STATE_PUBLISHED = 'published';
    const STATE_DELETED = 'deleted';

    /**
     * All types for product.
     */
    const TYPE_PRODUCT = 'Product';
    const TYPE_PRODUCT_PACK = 'ProductPack';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @ProductAssert\CheckProductName
     * 
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Choice(callback = "getStates")
     * 
     * @var string
     */
    protected $state;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Choice(callback = "getTypes")
     * 
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * 
     * @var float
     */
    protected $weight;

    /**
     * @ORM\Column(type="string", nullable=true)
     * 
     * @var float
     */
    protected $capacity;

    /**
     * @ORM\ManyToMany(targetEntity="Category")
     * @ORM\JoinTable(name="product_category",
     *     joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="id")}
     * )
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=true)
     * @ProductAssert\CategoryDuplicates
     * 
     * @var ArrayCollection
     */
    protected $categories;

    /**
     * @ORM\ManyToOne(targetEntity="Brand", inversedBy="products", cascade={"persist"})
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=true)
     * 
     * @var \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    protected $brand;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * 
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * 
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * 
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @var string
     */
    protected $discr;

    public function __construct()
    {
        $this->state = self::STATE_DRAFT;
        $this->categories = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getName()
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::setName()
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getState()
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::setState()
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return float
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * @param float $capacity
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getCategories()
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::addCategories()
     */
    public function addCategory(CategoryInterface $category)
    {
        $this->categories->add($category);

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::removeCategories()
     */
    public function removeCategory(CategoryInterface $category)
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return \ASF\ProductBundle\Model\Brand\BrandInterface
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param \ASF\ProductBundle\Model\Brand\BrandInterface $brand
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setBrand(BrandInterface $brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getCreatedAt()
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::setCreatedAt()
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->createdAt = $created_at;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getUpdatedAt()
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::setUpdatedAt()
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::getDeletedAt()
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \ASF\ProductBundle\Model\Product\ProductInterface::setDeletedAt()
     */
    public function setDeletedAt(\DateTime $deleted_at)
    {
        $this->deletedAt = $deleted_at;

        return $this;
    }

    /**
     * @return string
     */
    public function getDiscr()
    {
        return $this->discr;
    }

    /**
     * @param string $discr
     *
     * @return \ASF\ProductBundle\Model\Product\ProductInterface
     */
    public function setDiscr($discr)
    {
        $this->discr = $discr;

        return $this;
    }

    /**
     * Returns states for validators.
     *
     * @return array
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
     * Returns types for validators.
     *
     * @return array
     */
    public static function getTypes()
    {
        return array(
            self::TYPE_PRODUCT,
            self::TYPE_PRODUCT_PACK
        );
    }

    /**
     * @ORM\PrePersist
     * @return void
     */
    public function onPrePersist()
    {
        $this->type = self::TYPE_PRODUCT;
    }
}
