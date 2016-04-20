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
use ASF\ProductBundle\Model\Product\ProductModel;
use Symfony\Component\HttpFoundation\Request;
use ASF\ProductBundle\Utils\Manager\ProductManagerInterface;

/**
 * Product Form Handler
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductFormHandler extends FormHandlerModel
{
	/**
	 * @var ProductManagerInterface
	 */
	protected $productManager;
	
	/**
	 * @param FormInterface           $form
	 * @param Request                 $request
	 * @param ProductManagerInterface $product_manager
	 */
	public function __construct(FormInterface $form, Request $request, ProductManagerInterface $product_manager)
	{
		parent::__construct($form, $request);
		$this->productManager = $product_manager;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \ASF\CoreBundle\Form\Handler\FormHandlerModel::processForm()
	 */
	public function processForm($model)
	{
		try {
		    $producManager = $this->productManager;
			$product = $model;
			$product->setType(ProductModel::TYPE_PRODUCT);
			
			if ( is_null($product->getId()) ) {
				$isProductExist = $producManager->getRepository()->findOneBy(array('name' => $product->getName()));
				if ( !is_null($isProductExist) ) {
					throw new \Exception($this->getTranslator()->trans('A product with that name already exists.', array(), 'asf_product'));
					return false;
				}
			}
			
			$categories = $product->getCategories();
			$passed_categories = array();
			$doublons_found = false;
			foreach($categories as $category) {
				if (!in_array($category->getName(), $passed_categories)) {
					$passed_categories[] = $category->getName();
				} else {
					$doublons_found = true;
					$name = $category->getName();
				}
			}
				
			if (true === $doublons_found) {
				throw new \Exception(sprintf('The category "%s" is twofold. Please verify your entries and remove one.', $name));
				return false;
			}
			
			return true;
			
		} catch (\Exception $e) {
			throw new \Exception(sprintf('An error occured : %s', $e->getMessage()));
		}
		
		return false;
	}
}