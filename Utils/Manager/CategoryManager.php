<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ASF\ProductBundle\Utils\Manager;

use ASF\ProductBundle\Model\Category\CategoryInterface;
use Doctrine\ORM\EntityManager;
use ASF\ProductBundle\Model\Category\CategoryModel;

/**
 * Product Category Entity Manager.
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class CategoryManager extends DefaultManager implements DefaultManagerInterface
{
    /**
     * @var DefaultManagerInterface
     */
    protected $productManager;

    /**
     * @param EntityManager           $entity_manager
     * @param string                  $entity_name
     * @param DefaultManagerInterface $product_manager
     */
    public function __construct(EntityManager $entity_manager, $entity_name, DefaultManagerInterface $product_manager)
    {
        parent::__construct($entity_manager, $entity_name);
        $this->productManager = $product_manager;
    }

    /**
     * Removing category.
     *
     * This method removes the category if it has no product linked to it.
     * Otherwise, the category is tagged as deleted and thus not visible
     *
     * @param CategoryInterface $category
     */
    public function removeCategory(CategoryInterface $category)
    {
        // Get products linked to this category
        $productManager = $this->productManager;
        $products = $productManager->getRepository()->findProductsByCategory($category);

        // Get categories linked to this category
        $children = $this->getRepository()->findBy(array('parent' => $category));

        // If the category has no relationships with other entities, we delete it form the DB
        if ($products == 0 && count($children) == 0) {
            $this->getEntityManager()->remove($category);

            return;
        }

        // Otherwise, we change his state to CategoryModel::STATE_DELETED
        $category->setState(CategoryModel::STATE_DELETED);
    }
}
