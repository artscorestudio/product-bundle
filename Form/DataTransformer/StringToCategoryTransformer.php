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
use ASF\ProductBundle\Utils\Manager\DefaultManagerInterface;

/**
 * Transform a string to a ProductCategory entity.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class StringToCategoryTransformer implements DataTransformerInterface
{
    /**
     * @var DefaultManagerInterface
     */
    protected $categoryManager;

    /**
     * @param DefaultManagerInterface $categoryManager
     */
    public function __construct(DefaultManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($category)
    {
        if (is_null($category)) {
            return '';
        }

        return $category->getName();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($string)
    {
        $category = $this->categoryManager->getRepository()->findOneBy(array('name' => $string));

        return $category;
    }
}
