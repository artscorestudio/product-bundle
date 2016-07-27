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
     * Find categories by name.
     *
     * @param string $searched_term
     */
    public function findByNameContains($searched_term)
    {
        $qb = $this->createQueryBuilder('b');
        $qb instanceof QueryBuilder;

        $qb->add('where', $qb->expr()->like('b.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', $searched_term.'%');

        return $qb->getQuery()->getResult();
    }
}
