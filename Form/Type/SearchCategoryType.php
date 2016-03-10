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

use ASF\ProductBundle\Form\DataTransformer\StringToCategoryTransformer;
use ASF\CoreBundle\Model\Manager\ASFEntityManagerInterface;
use ASF\ProductBundle\Entity\Manager\ASFProductManagerInterface;

/**
 * Field for searching product category
 * 
 * @author Nicolas Claverie qinfo@artscore-studio.fr>
 *
 */
class SearchCategoryType extends AbstractType
{
	/**
	 * @var ASFProductManagerInterface|ASFEntityManagerInterface
	 */
	protected $categoryManager;
	
	/**
	 * @param ASFProductManagerInterface $category_manager
	 */
	public function __construct(ASFProductManagerInterface $category_manager)
	{
		$this->categoryManager = $category_manager;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Form\AbstractType::buildForm()
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$category_transformer = new StringToCategoryTransformer($this->categoryManager);
		$builder->addModelTransformer($category_transformer);
	}

	/**
	 * {@inheritDoc}
	 * @see \Symfony\Component\Form\AbstractType::configureOptions()
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
		    'label' => 'Product Category',
			'class' => $this->categoryManager->getClassName(),
		    'choice_label' => 'name',
		    'placeholder' => 'Choose a product category',
		    'attr' => array('class' => 'select2-entity-ajax', 'data-route' => 'asf_product_ajax_request_category_by_name')
		));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Form\FormTypeInterface::getName()
	 */
	public function getName()
	{
		return 'search_product_category';
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