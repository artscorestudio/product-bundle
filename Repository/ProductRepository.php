<?php
/*
 * This file is part of the Artscore Studio Framework package.
 *
 * (c) Nicolas Claverie <info@artscore-studio.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ASF\ProductBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;

use ASF\ProductBundle\Model\Category\CategoryInterface;

/**
 * Product Repository
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 *
 */
class ProductRepository extends EntityRepository
{
    /**
     * Find products by name
     *
     * @param string $searched_term
     */
    public function findProductsByNameContains($searched_term)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':searched_term')))
        ->setParameter('searched_term', '%' . $searched_term . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get products by brand name
     *
     * @param string $brand_name
     * @return array
     */
    public function findProductsByBrandNameContains($brand_name)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->leftJoin('p.brand', 'b')
        ->where($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
        ->setParameter(':brand_name', '%' . $brand_name . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by name and brand name
     *
     * @param string $searched_term
     */
    public function findProductsByNameAndBrandContains($product_name, $brand_name)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->leftJoin('p.brand', 'b')
        ->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':product_name')))
        ->andWhere($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
        ->setParameter('product_name', '%' . $product_name . '%')
        ->setParameter(':brand_name', '%' . $brand_name . '%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by category
     *
     * @param CategoryInterface $category
     */
    public function findProductsByCategory(CategoryInterface $category)
    {
        $categories = array();
        $categories[] = $category->getId();

        return $this->findProductsByCategoriesArray($categories);
    }

    /**
     * Find products by a list of categories
     *
     * @param array $categories
     */
    public function findProductsByCategoriesArray($categories)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->select('COUNT(p.id)')->join('p.categories', 'c')->addSelect('c')->where($qb->expr()->in('c.id', $categories));

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * Find products by weight and capacity properties
     *
     * @param string $weight
     * @param string $capacity
     * @return array
     */
    public function findProductsByWeightAndCapacity($weight, $capacity)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->add('where', 'weight=:weight AND capacity=:capacity')
        ->setParameter(':weight', $weight)
        ->setParameter(':capacity', $capacity);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by weight property
     *
     * @param string $weight
     * @return array
     */
    public function findProductsByWeight($weight)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->add('where', 'weight=:weight')->setParameter(':weight', $weight);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by capacity property
     *
     * @param string $capacity
     * @return array
     */
    public function findProductsByCapacity($capacity)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->add('where', 'capacity=:capacity')->setParameter(':capacity', $capacity);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find product by name, brand name, weight and capacity
     *
     * @param string $product_name
     * @param string $brand_name
     * @param string $weight
     * @param string $capacity
     */
    public function findProductByNameBrandWeightAndCapacity($product_name, $brand_name, $weight, $capacity)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        $qb->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':product_name')))
        ->setParameter(':product_name', '%' . $product_name . '%');

        if ( is_null($brand_name) ) {
            $qb->andWhere('p.brand IS NULL');
        } else {
            $qb->leftJoin('p.brand', 'b')
            ->andWhere($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
            ->setParameter(':brand_name', '%' . $brand_name . '%');
        }

        if ( is_null($weight) ) {
            $qb->andWhere('p.weight IS NULL');
        } else {
            $qb->andWhere('p.weight=:weight')->setParameter(':weight', $weight);
        }

        if ( is_null($capacity) ) {
            $qb->andWhere('p.capacity IS NULL');
        } else {
            $qb->andWhere('p.capacity=:capacity')->setParameter(':capacity', $capacity);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}