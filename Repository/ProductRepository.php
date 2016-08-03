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
 * Product Repository.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class ProductRepository extends EntityRepository
{
    /**
     * Return a Query Builder with states filter.
     * 
     * @param array|null $states
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder($states)
    {
        $qb = $this->createQueryBuilder('p');
        $qb instanceof QueryBuilder;

        if (null !== $states) {
            $qb->add('where', $qb->expr()->in('p.state', $states));
        }

        return $qb;
    }

    /**
     * Return number of products.
     * 
     * @param array $states
     *
     * @return number
     */
    public function countProducts(array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->select('COUNT(p.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find products by exact name.
     *
     * @param string     $searched_term
     * @param array|null $states
     *
     * @return array
     */
    public function findProductsByName($searched_term, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', $searched_term);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products with name containing searched terms.
     *
     * @param string     $searched_term
     * @param array|null $states
     * 
     * @return array
     */
    public function findProductsByNameContains($searched_term, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', '%'.$searched_term.'%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get products by brand exact name.
     *
     * @param string     $brand_name
     * @param array|null $states
     *
     * @return array
     */
    public function findProductsByBrandName($brand_name, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->leftJoin('p.brand', 'b')
            ->where($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
            ->setParameter(':brand_name', $brand_name);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get products by brand name containing searched terms.
     *
     * @param string     $brand_name
     * @param array|null $states
     * 
     * @return array
     */
    public function findProductsByBrandNameContains($brand_name, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->leftJoin('p.brand', 'b')
            ->where($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
            ->setParameter(':brand_name', '%'.$brand_name.'%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by exact product name and brand name.
     * 
     * @param unknown $product_name
     * @param unknown $brand_name
     * @param array   $states
     * 
     * @return mixed|null|\Doctrine\DBAL\Driver\Statement
     */
    public function findProductsByNameAndBrand($product_name, $brand_name = null, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->leftJoin('p.brand', 'b')
            ->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':product_name')))
            ->setParameter('product_name', $product_name);

        if (null !== $brand_name) {
            $qb->andWhere($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
                ->setParameter(':brand_name', $brand_name);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Find products by name and brand name containg searched terms.
     *
     * @param string     $searched_term
     * @param array|null $states
     * 
     * @return array
     */
    public function findProductsByNameAndBrandContains($product_name, $brand_name, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->leftJoin('p.brand', 'b')
            ->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':product_name')))
            ->andWhere($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
            ->setParameter('product_name', '%'.$product_name.'%')
            ->setParameter(':brand_name', '%'.$brand_name.'%');

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by category.
     *
     * @param CategoryInterface $category
     * @param array|null        $states
     * 
     * @return array
     */
    public function findProductsByCategory(CategoryInterface $category, array $states = null)
    {
        $categories = array();
        $categories[] = $category->getId();

        return $this->findProductsByCategoriesArray($categories, $states);
    }

    /**
     * Find products by a list of categories.
     *
     * @param array      $categories
     * @param array|null $states
     * 
     * @return array
     */
    public function findProductsByCategoriesArray($categories, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->select('COUNT(p.id)')->join('p.categories', 'c')->where($qb->expr()->in('c.id', $categories));

        return $qb->getQuery()->getScalarResult();
    }

    /**
     * Find products by weight and capacity properties.
     *
     * @param string     $weight
     * @param string     $capacity
     * @param array|null $states
     *
     * @return array
     */
    public function findProductsByWeightAndCapacity($weight, $capacity, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', 'weight=:weight AND capacity=:capacity')
            ->setParameter(':weight', $weight)
            ->setParameter(':capacity', $capacity);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by weight property.
     *
     * @param string     $weight
     * @param array|null $states
     *
     * @return array
     */
    public function findProductsByWeight($weight, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', 'weight=:weight')->setParameter(':weight', $weight);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find products by capacity property.
     *
     * @param string     $capacity
     * @param array|null $states
     *
     * @return array
     */
    public function findProductsByCapacity($capacity, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', 'capacity=:capacity')->setParameter(':capacity', $capacity);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find product by name, brand name, weight and capacity.
     *
     * @param string     $product_name
     * @param string     $brand_name
     * @param string     $weight
     * @param string     $capacity
     * @param array|null $states
     * 
     * @return array
     */
    public function findProductByNameBrandWeightAndCapacity($product_name, $brand_name, $weight, $capacity, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
        $qb->add('where', $qb->expr()->like('p.name', $qb->expr()->lower(':product_name')))
            ->setParameter(':product_name', '%'.$product_name.'%');

        if (is_null($brand_name)) {
            $qb->andWhere('p.brand IS NULL');
        } else {
            $qb->leftJoin('p.brand', 'b')
            ->andWhere($qb->expr()->like('b.name', $qb->expr()->lower(':brand_name')))
            ->setParameter(':brand_name', '%'.$brand_name.'%');
        }

        if (is_null($weight)) {
            $qb->andWhere('p.weight IS NULL');
        } else {
            $qb->andWhere('p.weight=:weight')->setParameter(':weight', $weight);
        }

        if (is_null($capacity)) {
            $qb->andWhere('p.capacity IS NULL');
        } else {
            $qb->andWhere('p.capacity=:capacity')->setParameter(':capacity', $capacity);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}
