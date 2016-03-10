<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Entity\Manager;

/**
 * Product Category Entity Manager
 * 
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class CategoryManager extends ASFProductManager
{
    /**
     * Removing category
     *
     * This method removes the category if it has no product linked to it.
     * Otherwise, the category is tagged as deleted and thus not visible
     *
     * @param CategoryInterface $category
     */
    public function removeCategory(CategoryInterface $category)
    {
        // Get products linked to this category
        $productManager = $this->container->get('asf_product.product.manager');
        $products = $productManager->getRepository()->findProductsByCategory($category);
    
        // Get categories linked to this category
        $children = $this->getRepository()->findBy(array('parent' => $category));
    
        // If the category has no relationships with other entities, we delete it form the DB
        if ( $products == 0 && count($children) == 0) {
            $this->getEntityManager()->remove($category);
            return ;
        }
    
        // Otherwise, we change his state to CategoryModel::STATE_DELETED
        $category->setState(CategoryModel::STATE_DELETED);
    }
}