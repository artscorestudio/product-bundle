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

use ASF\ProductBundle\Entity\ProductModel;
use ASF\ProductBundle\Form\DataTransformer\StringToWeightTransformer;
use ASF\ProductBundle\Form\DataTransformer\StringToLiterTransformer;
use ASF\LayoutBundle\Form\Type\BaseCollectionType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use ASF\ProductBundle\Utils\Manager\ProductManagerInterface;

/**
 * Product Form Type
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductType extends AbstractType
{
    /**
     * @var ProductManagerInterface
     */
    protected $productManager;

    /**
     * @var boolean
     */
    protected $isBrandEntityEnabled;
    
    /**
     * @param ProductManagerInterface $product_manager
     * @param boolean                 $is_brand_entity_enabled
     */
    public function __construct(ProductManagerInterface $product_manager, $is_brand_entity_enabled)
    {
        $this->productManager = $product_manager;
        $this->isBrandEntityEnabled = $is_brand_entity_enabled;
    }

    /**
     * Pass the image URL to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['display_brand_field'] = $this->isBrandEntityEnabled;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $weight_transformer = new StringToWeightTransformer();
        $liter_transformer = new StringToLiterTransformer();

        $builder->add('name', TextType::class, array(
            'label' => 'Product name',
            'required' => true
        ))
        
        ->add($builder->create('weight', TextType::class, array(
            'label' => 'Weight (Kg)',
            'required' => false
        ))->addModelTransformer($weight_transformer))
        
        ->add($builder->create('capacity', TextType::class, array(
            'label' => 'Capacity (Liter)',
            'required' => false
        ))->addModelTransformer($liter_transformer));
        
        if ( true === $this->isBrandEntityEnabled ) {
            $builder->add('brand', SearchBrandType::class);
        }
        
        $builder->add('categories', BaseCollectionType::class, array(
            'entry_type' => SearchCategoryType::class,
            'label' => 'List of categories',
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'containerId' => 'categories-collection'))
            
        ->add('state', ChoiceType::class, array(
            'label' => 'State',
            'required' => true,
            'choices' => array(
                ProductModel::STATE_DRAFT => 'Draft',
                ProductModel::STATE_WAITING => 'Waiting',
                ProductModel::STATE_PUBLISHED => 'Published'
            )
        ));
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->productManager->getClassName(),
            'translation_domain' => 'asf_product',
        ));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'product_type';
    }
}