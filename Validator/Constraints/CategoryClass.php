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
 * Category Class Constraint
 * 
 * @author Nicolas Claverie <nicolas.claverie@cd31.fr>
 * 
 * @Annotation
 */
class CategoryClass extends Constraint
{
public $alreadyExistsMessage = 'asf.product.msg.error.category_already_exists';
    
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Validator\Constraint::getTargets()
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
