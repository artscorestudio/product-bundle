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
use Symfony\Component\HttpFoundation\Request;
use ASF\ProductBundle\Utils\Manager\DefaultManagerInterface;

/**
 * Category Form Handler.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class CategoryFormHandler extends FormHandlerModel
{
    /**
     * @var DefaultManagerInterface
     */
    protected $categoryManager;

    /**
     * @param FormInterface           $form
     * @param Request                 $request
     * @param DefaultManagerInterface $category_manager
     */
    public function __construct(FormInterface $form, Request $request, DefaultManagerInterface $category_manager)
    {
        parent::__construct($form, $request);
        $this->categoryManager = $category_manager;
    }

    /**
     * (non-PHPdoc).
     *
     * @see \Asf\ApplicationBundle\Application\Form\FormHandlerModel::processForm()
     * @throw \Exception
     */
    public function processForm($model)
    {
        try {
            $categoryManager = $this->categoryManager;
            $category = $model;

            if (is_null($category->getId())) {
                $isCategoryExist = $categoryManager->getRepository()->findOneBy(array('name' => $category->getName()));
                if (!is_null($isCategoryExist)) {
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
