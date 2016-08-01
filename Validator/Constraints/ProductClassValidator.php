<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Doctrine\ORM\EntityManagerInterface;
use ASF\ProductBundle\Model\Product\ProductModel;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * Product Class Validator
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @Annotation
 */
class ProductClassValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    
    /**
     * @var string
     */
    protected $entityClassName;
    
    /**
     * @var boolean
     */
    protected $brandEnabled;
    
    /**
     * @param EntityManagerInterface $em
     * @param string $entityClassName
     */
    public function __construct(EntityManagerInterface $em, $entityClassName, $brandEnabled)
    {
        $this->em = $em;
        $this->entityClassName = $entityClassName;
        $this->brandEnabled = $brandEnabled;
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($product, Constraint $constraint)
    {
        if ( true === $this->brandEnabled && null !== $product->getBrand() ) {
            $result = $this->em->getRepository($this->entityClassName)->findProductsByNameAndBrand($product->getName(), 
                $product->getBrand()->getName(), 
                array(
                    ProductModel::STATE_DRAFT,
                    ProductModel::STATE_WAITING,
                    ProductModel::STATE_PUBLISHED
                )
            );
        } else {
            $result = $this->em->getRepository($this->entityClassName)->findProductsByNameAndBrand($product->getName(), null, array(
                ProductModel::STATE_DRAFT,
                ProductModel::STATE_WAITING,
                ProductModel::STATE_PUBLISHED
            ));
        }
        
        if ( null !== $result && $result->getId() !== $product->getId() ) {
            $this->context->buildViolation($constraint->alreadyExistsMessage)->atPath('name')->addViolation();
        }
    }
}
