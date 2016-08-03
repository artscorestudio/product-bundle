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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use ASF\ProductBundle\Model\Product\ProductModel;
use ASF\ProductBundle\Form\DataTransformer\StringToWeightTransformer;
use ASF\ProductBundle\Form\DataTransformer\StringToLiterTransformer;
use ASF\LayoutBundle\Form\Type\BaseCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Product Form Type.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class ProductType extends AbstractType
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var bool
     */
    protected $isBrandEntityEnabled;

    /**
     * @param string $className
     * @param bool   $isBrandEntityEnabled
     */
    public function __construct($className, $isBrandEntityEnabled)
    {
        $this->className = $className;
        $this->isBrandEntityEnabled = $isBrandEntityEnabled;
    }

    /**
     * Pass the image URL to the view.
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['display_brand_field'] = $this->isBrandEntityEnabled;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $weight_transformer = new StringToWeightTransformer();
        $liter_transformer = new StringToLiterTransformer();

        $builder->add('name', TextType::class, array(
            'label' => 'asf.product.product_name',
            'required' => true,
        ))

        ->add($builder->create('weight', TextType::class, array(
            'label' => 'asf.product.weight',
            'required' => false,
        ))->addModelTransformer($weight_transformer))

        ->add($builder->create('capacity', TextType::class, array(
            'label' => 'asf.product.capacity',
            'required' => false,
        ))->addModelTransformer($liter_transformer));

        if (true === $this->isBrandEntityEnabled) {
            $builder->add('brand', SearchBrandType::class);
        }

        $builder->add('categories', BaseCollectionType::class, array(
            'entry_type' => SearchCategoryType::class,
            'label' => 'asf.product.form.categories_list',
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'containerId' => 'categories-collection',
        ))

        ->add('state', ChoiceType::class, array(
            'label' => 'asf.product.state',
            'required' => true,
            'choices' => array(
                'asf.product.state.draft' => ProductModel::STATE_DRAFT,
                'asf.product.state.waiting' => ProductModel::STATE_WAITING,
                'asf.product.state.published' => ProductModel::STATE_PUBLISHED,
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
            'data_class' => $this->className,
        ));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'product_type';
    }
}
