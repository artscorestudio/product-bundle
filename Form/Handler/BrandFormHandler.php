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
 * Brand Form Handler
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class BrandFormHandler extends FormHandlerModel
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
		    $brandManager = $this->container->get('asf_product.brand.manager');
		    $brand = $model;

			if ( is_null($brand->getId()) ) {
				$isBrandExist = $brandManager->getRepository()->findOneBy(array('name' => $brand->getName()));
				if ( !is_null($isBrandExist) ) {
					throw new \Exception($this->getTranslator()->trans('A brand with that name already exists.', array(), 'asf_product'));
					return false;
				}
			}
			
			return true;

		} catch (\Exception $e) {
			throw new \Exception(sprintf('An error occured : %s', $e->getMessage()));
		}
		
		return false;
	}
}