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

use Symfony\Component\Validator\Constraint;

/**
 * Check if duplicates exists in category lsit for a product constraint.
 * 
 * @author Nicolas Claverie <nicolas.claverie@cd31.fr>
 * 
 * @Annotation
 */
class CategoryDuplicates extends Constraint
{
    public $message = 'asf.product.msg.error.category_duplicates';
}
