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

use ASF\ProductBundle\Form\DataTransformer\StringToProductTransformer;
use ASF\ProductBundle\Utils\Manager\ProductManagerInterface;

/**
 * Field for searching product
 * 
 * @author Nicolas Claverie qinfo@artscore-studio.fr>
 *
 */
class SearchProductType extends AbstractType
{
	/**
	 * @var ProductManagerInterface
	 */
	protected $productManager;
	
	/**
	 * @param DefaultManagerInterface $productManager
	 */
	public function __construct(ProductManagerInterface $productManager)
	{
		$this->productManager = $productManager;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Form\AbstractType::buildForm()
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$product_transformer = new StringToProductTransformer($this->productManager);
		$builder->addModelTransformer($product_transformer);
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
		    'label' => 'Product',
			'class' => $this->productManager->getClassName(),
		    'choice_label' => 'name',
		    'placeholder' => 'Choose a product',
		    'attr' => array('class' => 'select2-entity-ajax', 'data-route' => 'asf_product_ajax_request_product_by_name')
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Form\FormTypeInterface::getName()
	 */
	public function getName()
	{
		return 'search_product';
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Form\AbstractType::getParent()
	 */
	public function getParent()
	{
		return EntityType::class;
	}
}