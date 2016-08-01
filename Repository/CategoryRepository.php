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

/**
 * Product Category Repository.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class CategoryRepository extends EntityRepository
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
        $qb = $this->createQueryBuilder('c');
        $qb instanceof QueryBuilder;
    
        if ( null !== $states ) {
            $qb->add('where', $qb->expr()->in('c.state', $states));
        }
    
        return $qb;
    }
    
    /**
     * Find categories by exact name
     * 
     * @param unknown $searched_term
     * @param array $states
     * 
     * @return mixed|NULL|\Doctrine\DBAL\Driver\Statement
     */
    public function findByName($searched_term, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);
    
        $qb->add('where', $qb->expr()->like('c.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', $searched_term);
    
        return $qb->getQuery()->getOneOrNullResult();
    }
    
    /**
     * Find categories by name.
     *
     * @param string $searched_term
     */
    public function findByNameContains($searched_term, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);

        $qb->add('where', $qb->expr()->like('b.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', $searched_term.'%');

        return $qb->getQuery()->getResult();
    }
}
