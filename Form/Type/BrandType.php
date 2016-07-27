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
use ASF\ProductBundle\Utils\Manager\DefaultManagerInterface;
use ASF\ProductBundle\Model\Brand\BrandModel;

/**
 * Product Form Type.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class BrandType extends AbstractType
{
    /**
     * @var DefaultManagerInterface
     */
    protected $brandManager;

    /**
     * @param DefaultManagerInterface $brandManager
     */
    public function __construct(DefaultManagerInterface $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
            'label' => 'Brand name',
            'max_length' => 255,
            'required' => true,
        ))
        ->add('state', ChoiceType::class, array(
            'label' => 'State',
            'required' => true,
            'choices' => array(
                BrandModel::STATE_DRAFT => 'Draft',
                BrandModel::STATE_WAITING => 'Waiting',
                BrandModel::STATE_PUBLISHED => 'Published',
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
            'data_class' => $this->brandManager->getClassName(),
            'translation_domain' => 'asf_product',
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
