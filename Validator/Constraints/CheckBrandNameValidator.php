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

/**
 * Check Brand name in DB
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @Annotation
 */
class CheckBrandNameValidator extends ConstraintValidator
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
    public function validate($value, Constraint $constraint)
    {
        $brand = $this->em->getRepository($this->entityClassName)->findOneBy(array('name' => $value));
        if ( null !== $brand ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}