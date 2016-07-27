<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Entity;

use ASF\ProductBundle\Model\Product\ProductPackInterface;
use ASF\ProductBundle\Model\Product\ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Product Pack Entity.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class ProductPack extends ProductModel implements ProductPackInterface
{
    /**
     * @var ArrayCollection
     */
    protected $products;

    public function __construct()
    {
        parent::__construct();
        $this->products = new ArrayCollection();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackInterface::getProducts()
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackInterface::addProduct()
     */
    public function addProduct(ProductInterface $product)
    {
        $this->products->add($product);

        return $this;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Asf\Bundle\ProductBundle\Model\Product\ProductPackInterface::removeProduct()
     */
    public function removeProduct(ProductInterface $product)
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     * Executed on prePersist doctrine event.
     */
    public function onPrePersist()
    {
        $this->type = self::TYPE_PRODUCT_PACK;
    }
}
