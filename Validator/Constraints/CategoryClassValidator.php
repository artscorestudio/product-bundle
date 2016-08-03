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

/**
 * Check Category name in DB.
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
     * @param string                 $entityClassName
     */
    public function __construct(EntityManagerInterface $em, $entityClassName)
    {
        $this->em = $em;
        $this->entityClassName = $entityClassName;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($category, Constraint $constraint)
    {
        $result = $this->em->getRepository($this->entityClassName)->findByName($category->getName(), array(
            CategoryModel::STATE_DRAFT,
            CategoryModel::STATE_WAITING,
            CategoryModel::STATE_PUBLISHED,
        ));
        if (null !== $result && $result->getId() !== $category->getId()) {
            $this->context->buildViolation($constraint->alreadyExistsMessage)->atPath('name')->addViolation();
        }
    }
}
