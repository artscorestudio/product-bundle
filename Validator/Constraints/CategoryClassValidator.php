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
use ASF\ProductBundle\Model\Category\CategoryModel;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;

/**
 * Check Category name in DB
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @Annotation
 */
class CategoryClassValidator extends ConstraintValidator
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
    public function validate($category, Constraint $constraint)
    {
        $criteria = new Criteria();
        $criteria->where(new Comparison("name", Comparison::EQ, $category->getName()));
        $criteria->andWhere(new Comparison("state", Comparison::EQ, CategoryModel::STATE_DRAFT));
        $criteria->orWhere(new Comparison("state", Comparison::EQ, CategoryModel::STATE_PUBLISHED));
        $criteria->orWhere(new Comparison("state", Comparison::EQ, CategoryModel::STATE_WAITING));
        $criteria->setMaxResults(1);
        
        $result = $this->em->getRepository($this->entityClassName)->matching($criteria)->first();
        
        if ( null !== $result && $result->getId() !== $category->getId() ) {
            $this->context->buildViolation($constraint->alreadyExistsMessage)->atPath('name')->addViolation();
        }
    }
}
