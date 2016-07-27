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
 * Transform a string to a Brand entity.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class StringToBrandTransformer implements DataTransformerInterface
{
    /**
     * @var DefaultManagerInterface
     */
    protected $brandManager;

    /**
     * @param DefaultManagerInterface $brandManager
     */
    public function __construct(DefaultManagerInterface $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($brand)
    {
        if (is_null($brand)) {
            return '';
        }

        return $brand->getName();
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($string)
    {
        $brand = $this->brandManager->getRepository()->findOneBy(array('name' => $string));

        return $brand;
    }
}
