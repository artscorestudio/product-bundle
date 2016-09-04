<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Field for searching product.
 * 
 * @author Nicolas Claverie qinfo@artscore-studio.fr>
 */
class SearchProductType extends AbstractType
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }
    
    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'asf.product.form.search_product',
            'class' => $this->className,
            'choice_label' => function($product) {
                return $product->getName().
                    (!is_null($product->getBrand()) ? ' '.$product->getBrand()->getName() : '').
                    (!is_null($product->getWeight()) ? ' '.$product->getWeight().'Kg' : '').
                    (!is_null($product->getCapacity()) ? ' '.$product->getCapacity().'L' : ''); 
            },
            'placeholder' => 'asf.product.form.choose_a_product',
            'attr' => array('class' => 'select2-entity-ajax'),
        ));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getBlockPrefix()
    {
        return 'search_product';
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::getParent()
     */
    public function getParent()
    {
        return EntityType::class;
    }
}
