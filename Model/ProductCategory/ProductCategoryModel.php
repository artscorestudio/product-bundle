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
 * Product Category
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
abstract class ProductCategoryModel extends ProductCategoryInterface
{
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
}