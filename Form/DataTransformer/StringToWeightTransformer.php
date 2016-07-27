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
 * Transform a string to a weight.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class StringToWeightTransformer implements DataTransformerInterface
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
        if (false !== strpos($string, 't')) {
            $string = trim($string, 't');
            $weight = $string * 1000;
        } elseif (false !== strpos($string, 'kg')) {
            $string = trim($string, 'kg');
            $weight = $string;
        } elseif (false !== strpos($string, 'g')) {
            $string = trim($string, 'g');
            $weight = $string / 1000;
        } else {
            $string = trim($string);
            $weight = $string;
        }

        return $weight;
    }
}
