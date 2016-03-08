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
use ASF\CoreBundle\Model\Manager\ASFEntityManagerInterface;
use ASF\ProductBundle\Entity\Manager\ASFProductManagerInterface;

/**
 * Transform a string to a Brand entity
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class StringToBrandTransformer implements DataTransformerInterface
{
    /**
     * @var ASFProductManagerInterface|ASFEntityManagerInterface
     */
    protected $brandManager;

    /**
     * @param ASFProductManagerInterface $brand_manager
     */
    public function __construct(ASFProductManagerInterface $brand_manager)
    {
        $this->brandManager = $brand_manager;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($brand)
    {
        if ( is_null($brand) )
            return '';

            return $brand->getName();
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($string)
    {
        $brand = $this->brandManager->getRepository()->findOneBy(array('name' => $string));
        return $brand;
    }
}