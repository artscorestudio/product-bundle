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

/**
 * Transform a string to a liter.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class StringToLiterTransformer implements DataTransformerInterface
{
    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::transform()
     */
    public function transform($string)
    {
        if (is_null($string)) {
            return $string;
        }

        return str_replace(',', '.', trim($string));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\DataTransformerInterface::reverseTransform()
     */
    public function reverseTransform($string)
    {
        $string = strtolower(str_replace(',', '.', trim($string)));

        if (false !== strpos($string, 'ml')) {
            $string = trim($string, 'ml');
            $liter = $string / 1000;
        } elseif (false !== strpos($string, 'cl')) {
            $string = trim($string, 'cl');
            $liter = $string / 100;
        } elseif (false !== strpos($string, 'dl')) {
            $string = trim($string, 'dl');
            $liter = $string / 10;
        } else {
            $string = trim($string, 'l');
            $liter = $string;
        }

        return $liter;
    }
}
