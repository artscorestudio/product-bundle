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

use ASF\ProductBundle\Model\Brand\BrandInterface;
use ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface;

/**
 * Product Model
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
abstract class ProductModel extends ProductInterface
{
	/**
	 * All product's states are hardcoded in constantes.
	 * For historical features reasons, products are not completelly removed form the DB.
	 */
	const STATE_DRAFT     = 'draft';
	const STATE_WAITING   = 'waiting';
	const STATE_PUBLISHED = 'published';
	const STATE_DELETED   = 'deleted';
	
	/**
	 * All types for product.
	 */
	const TYPE_PRODUCT = 'Product';
	const TYPE_PRODUCT_PACK = 'ProductPack';
	
	/**
	 * @var integer
	 */
	protected $id;
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * @var string
	 */
	protected $name;
	
	/**
	 * @var string
	 */
	protected $content;
	
	/**
	 * @var string
	 */
	protected $state;
	
	/**
	 * @var string
	 */
	protected $type;
	
	/**
	 * @var float
	 */
	protected $weight;
	
	/**
	 * @var float
	 */
	protected $capacity;
	
	/**
	 * @var ArrayCollection
	 */
	protected $categories;
	
	/**
	 * @var \ASF\ProductBundle\Model\Brand\BrandInterface
	 */
	protected $brand;
	
	/**
	 * @var \DateTime
	 */
	protected $createdAt;
	
	/**
	 * @var \DateTime
	 */
	protected $updatedAt;
	
	/**
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
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getId()
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getName()
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * (non-PHPdoc)
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
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getState()
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * (non-PHPdoc)
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
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setCapacity($capacity)
	{
		$this->capacity = $capacity;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getCategories()
	 */
	public function getCategories()
	{
		return $this->categories;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::addCategories()
	 */
	public function addCategory(ProductCategoryInterface $category)
	{
		$this->categories->add($category);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::removeCategories()
	 */
	public function removeCategory(ProductCategoryInterface $category)
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
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setBrand(BrandInterface $brand)
	{
		$this->brand = $brand;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getCreatedAt()
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::setCreatedAt()
	 */
	public function setCreatedAt(\DateTime $created_at)
	{
		$this->createdAt = $created_at;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getUpdatedAt()
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::setUpdatedAt()
	 */
	public function setUpdatedAt(\DateTime $updated_at)
	{
		$this->updatedAt = $updated_at;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Product\ProductInterface::getDeletedAt()
	 */
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}
	
	/**
	 * (non-PHPdoc)
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
	 * @return \ASF\ProductBundle\Model\Product\ProductInterface
	 */
	public function setDiscr($discr)
	{
		$this->discr = $discr;
		return $this;
	}
	
	/**
	 * Returns states for validators
	 *
	 * @return multitype:string
	 */
	public static function getStates()
	{
		return array(
			self::STATE_DRAFT,
			self::STATE_WAITING,
			self::STATE_PUBLISHED,
			self::STATE_DELETED
		);
	}
	
	/**
	 * Executed on prePersist doctrine event
	 */
	public function onPrePersist()
	{
		$this->type = self::TYPE_PRODUCT;
	}
}