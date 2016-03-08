<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Form\Handler;

use ASF\CoreBundle\Form\Handler\FormHandlerModel;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Category Form Handler
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class CategoryFormHandler extends FormHandlerModel
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;
	
	/**
	 * @param FormInterface      $form
	 * @param ContainerInterface $container
	 */
	public function __construct(FormInterface $form, ContainerInterface $container)
	{
		parent::__construct($form);
		$this->container = $container;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Asf\ApplicationBundle\Application\Form\FormHandlerModel::processForm()
	 * @throw \Exception
	 */
	public function processForm($model)
	{
		try {
		    $categoryManager = $this->container->get('asf_product.product_category.manager');
			$category = $model;
			
			if ( is_null($category->getId()) ) {
				$isCategoryExist = $categoryManager->getRepository()->findOneBy(array('name' => $category->getName()));
				if ( !is_null($isCategoryExist) ) {
					throw new \Exception(sprintf('A product category with the name "%s" already exists', $category->getName()));
				}
			}
			
			return true;

		} catch (\Exception $e) {
			throw new \Exception(sprintf('An error occured : %s', $e->getMessage()));
		}
		
		return false;
	}
}