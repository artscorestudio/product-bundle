<?php
/**
 * This file is part of Artscore Studio Website package
 *
 * (c) 2012-2015 Artscore Studio <info@artscore-studio.fr>
 *
 * This source file is subject to the MIT Licence that is bundled
 * with this source code in the file LICENSE.
 */
namespace ASF\ProductBundle\Entity;

use ASF\ProductBundle\Model\Product\ProductPackProductInterface;
use ASF\ProductBundle\Model\Product\ProductInterface;
use ASF\ProductBundle\Model\Product\ProductPackInterface;

/**
 * Relation between ProductPack entity and Product Interface entity
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductPackProduct implements ProductPackProductInterface
{
	/**
	 * @var integer
	 */
	protected $id;
	
	/**
	 * @var \Asf\Bundle\ProductBundle\Model\Product\ProductPackInterface
	 */
	protected $productPack;
	
	/**
	 * @var \Asf\Bundle\ProductBundle\Model\Product\ProductInterface
	 */
	protected $product;

	/**
	 * @var numeric
	 */
	protected $order;
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::getId()
	 */
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::getProductPack()
	 */
	public function getProductPack()
	{
		return $this->productPack;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::setProductPack()
	 */
	public function setProductPack(ProductPackInterface $product_pack)
	{
		$this->productPack = $product_pack;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::getProduct()
	 */
	public function getProduct()
	{
		return $this->product;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::setProduct()
	 */
	public function setProduct(ProductInterface $product)
	{
		$this->product = $product;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::getOrder()
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackProductInterface::setOrder()
	 */
	public function setOrder($order)
	{
		$this->order = $order;
		return $this;
	}
}