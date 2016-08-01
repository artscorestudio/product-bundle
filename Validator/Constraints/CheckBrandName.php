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
 * Check Brand name constraint
 * 
 * @author Nicolas Claverie <nicolas.claverie@cd31.fr>
 * 
 * @Annotation
 */
class CheckBrandName extends Constraint
{
    public $message = 'asf.product.msg.error.brand_already_exists';
}
