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

/**
 * Check if duplicates exists in category lsit for a product constraint validator.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 * 
 * @Annotation
 */
class CategoryDuplicatesValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Validator\ConstraintValidatorInterface::validate()
     */
    public function validate($categories, Constraint $constraint)
    {
        $passed_categories = array();
        $duplicates_found = false;

        foreach ($categories as $category) {
            if (!in_array($category->getName(), $passed_categories)) {
                $passed_categories[] = $category->getName();
            } else {
                $duplicates_found = true;
            }
        }

        if (true === $duplicates_found) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
