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

use ASF\ProductBundle\Entity\Manager\ASFProductManagerInterface;
use ASF\CoreBundle\Model\Manager\ASFEntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


/**
 * Product Form Type
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class BrandType extends AbstractType
{
    /**
     * @var ASFProductManagerInterface|ASFEntityManagerInterface
     */
    protected $brandManager;
    
    /**
     * @param ASFProductManagerInterface $brand_manager
     */
    public function __construct(ASFProductManagerInterface $brand_manager)
    {
        $this->brandManager = $brand_manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, array(
			'label' => 'Brand name', 
			'max_length' => 255,
			'required' => true
		))
		->add('state', ChoiceType::class, array(
			'label' => 'State',
			'required' => true,
			'choices' => array(
				BrandModel::STATE_DRAFT => 'Draft',
				BrandModel::STATE_WAITING => 'Waiting',
				BrandModel::STATE_PUBLISHED => 'Published' 
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
            'data_class' => $this->brandManager->getClassName(),
            'translation_domain' => 'asf_product',
        ));
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'brand_type';
    }
}