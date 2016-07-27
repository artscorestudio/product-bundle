<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use ASF\ProductBundle\Utils\Manager\ProductManagerInterface;

/**
 * Transform a string to a Product entity.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class StringToProductTransformer implements DataTransformerInterface
{
    /**
     * @var ProductManagerInterface
     */
    protected $productManager;

    /**
     * @param ProductManagerInterface $productManager
     */
    public function __construct(ProductManagerInterface $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($product)
    {
        if (is_null($product)) {
            return '';
        }

        return $product->getName();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($string)
    {
        $product = $this->productManager->getRepository()->findOneBy(array('name' => $string));

        return $product;
    }
}
