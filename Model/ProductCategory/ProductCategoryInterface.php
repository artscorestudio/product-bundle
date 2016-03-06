<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Model\ProductCategory;

/**
 * Product Interface
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
interface ProductCategoryInterface
{
	/**
	 * @return string
	 */
	public function getName();
	
	/**
	 * @param string $name
	 * @return \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface
	 */
	public function setName($name);
	
	/**
	 * @return string
	 */
	public function getState();
	
	/**
	 * @param string $state
	 * @return \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface
	 */
	public function setState($state);
	
	/**
	 * @return string
	 */
	public function getParent();
	
	/**
	 * @param \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface $category
	 * @return \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface
	 */
	public function setParent(ProductCategoryInterface $category);
	
	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getChildren();
	
	/**
	 * @param \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface $category
	 * @return \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface
	 */
	public function addChild(ProductCategoryInterface $category);
	
	/**
	 * @param \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface $category
	 * @return \ASF\ProductBundle\Model\ProductCategory\ProductCategoryInterface
	 */
	public function removeChild(ProductCategoryInterface $category);
}