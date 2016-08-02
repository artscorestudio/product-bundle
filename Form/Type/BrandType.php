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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use ASF\ProductBundle\Model\Brand\BrandModel;

/**
 * Product Form Type.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class BrandType extends AbstractType
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
        $this->brandclassName = $className;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'asf.product.brand_name',
            'required' => true,
        ))
        ->add('state', ChoiceType::class, array(
            'label' => 'asf.product.state',
            'required' => true,
            'choices' => array(
                'asf.product.state.draft' => BrandModel::STATE_DRAFT,
                'asf.product.state.waiting' => BrandModel::STATE_WAITING,
                'asf.product.state.published' => BrandModel::STATE_PUBLISHED,
            ),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->className
        ));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'brand_type';
    }
}
