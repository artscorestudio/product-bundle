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
use ASF\ProductBundle\Model\Brand\BrandModel;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * Brand Class Constraint
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @Annotation
 */
class BrandClassValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var string
     */
    private $entityClassName;
    
    /**
     * @param EntityManagerInterface $em
     * @param string $entityClassName
     */
    public function __construct(EntityManagerInterface $em, $entityClassName)
    {
        $this->em = $em;
        $this->entityClassName = $entityClassName;
    }
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($brand, Constraint $constraint)
    {
    	$result = $this->em->getRepository($this->entityClassName)->findByName($brand->getName(), array(
            BrandModel::STATE_DRAFT,
            BrandModel::STATE_WAITING,
            BrandModel::STATE_PUBLISHED
        ));
        if ( null !== $result && $result->getId() !== $brand->getId() ) {
            $this->context->buildViolation($constraint->alreadyExistsMessage)->atPath('name')->addViolation();
        }
    }
}
