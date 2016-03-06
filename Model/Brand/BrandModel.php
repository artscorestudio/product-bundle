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
 * Brand Model
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
abstract class BrandModel extends BrandInterface
{
	/**
	 * All brand's states are hardcoded in constantes.
	 * For historical features reasons, products are not completelly removed form the DB.
	 */
	const STATE_DRAFT     = 'draf';
	const STATE_WAITING   = 'waiting';
	const STATE_PUBLISHED = 'published';
	const STATE_DELETED   = 'deleted';
	
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
	 * @var ArrayCollection
	 */
	protected $products;
	
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
	
	public function __construct()
	{
		$this->state = self::STATE_DRAFT;
		$this->createdAt = new \DateTime();
		$this->updatedAt = new \DateTime();
		$this->products = new ArrayCollection();
	}
	
	/**
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getName()
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setName()
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
	 * @return \ASF\ProductBundle\Model\Brand\BrandInterface
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getState()
	 */
	public function getState()
	{
		return $this->state;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setState()
	 */
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getProducts()
	 */
	public function getProducts()
	{
		return $this->products;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::addProduct()
	 */
	public function addProduct(ProductInterface $product)
	{
		$this->products->add($product);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::removeProduct()
	 */
	public function removeProduct(ProductInterface $product)
	{
		$this->products->removeElement($product);
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getCreatedAt()
	 */
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setCreatedAt()
	 */
	public function setCreatedAt(\DateTime $created_at)
	{
		$this->createdAt = $created_at;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getUpdatedAt()
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setUpdatedAt()
	 */
	public function setUpdatedAt(\DateTime $updated_at)
	{
		$this->updatedAt = $updated_at;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::getDeletedAt()
	 */
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \ASF\ProductBundle\Model\Brand\BrandInterface::setDeletedAt()
	 */
	public function setDeletedAt(\DateTime $deleted_at)
	{
		$this->deletedAt = $deleted_at;
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
		$this->createdAt = new \DateTime();
		$this->updatedAt = new \DateTime();
	}
	
	/**
	 * Executed on prePersist doctrine event
	 */
	public function onPreUpdate()
	{
		$this->updatedAt = new \DateTime();
	}
}