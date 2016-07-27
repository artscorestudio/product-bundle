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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ASF\ProductBundle\Form\DataTransformer\StringToBrandTransformer;
use ASF\ProductBundle\Utils\Manager\DefaultManagerInterface;

/**
 * Field for searching brand.
 * 
 * @author Nicolas Claverie qinfo@artscore-studio.fr>
 */
class SearchBrandType extends AbstractType
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
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $brand_transformer = new StringToBrandTransformer($this->brandManager);
        $builder->addModelTransformer($brand_transformer);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Brand',
            'class' => $this->brandManager->getClassName(),
            'choice_label' => 'name',
            'placeholder' => 'Choose a brand',
            'attr' => array('class' => 'select2-entity-ajax', 'data-route' => 'asf_brand_ajax_request_brand_by_name'),
        ));
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'search_brand';
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
