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
 * Brand Repository.
 *
 * @author Nicolas Claverie <info@artscore-studio.fr>
 */
class BrandRepository extends EntityRepository
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
		$qb = $this->createQueryBuilder('b');
		$qb instanceof QueryBuilder;
	
		if ( null !== $states ) {
			$qb->add('where', $qb->expr()->in('b.state', $states));
		}
	
		return $qb;
	}
	
	/**
	 * Return number of brands.
	 *
	 * @param array $states
	 * @return number
	 */
	public function countBrands(array $states = null)
	{
	    $qb = $this->getQueryBuilder($states);
	    $qb->select('COUNT(b.id)');
	
	    return $qb->getQuery()->getSingleScalarResult();
	}
	
	/**
	 * Find brands by exact name.
	 * 
	 * @param string $searched_term
	 * @param array $states
	 * 
	 * @return mixed|NULL|\Doctrine\DBAL\Driver\Statement
	 */
	public function findByName($searched_term, array $states = null)
	{
		$qb = $this->getQueryBuilder($states);
	
		$qb->add('where', $qb->expr()->like('b.name', $qb->expr()->lower(':searched_term')))
			->setParameter('searched_term', $searched_term);
	
		return $qb->getQuery()->getOneOrNullResult();
	}
	
    /**
     * Find brands by name.
     * 
     * @param string $searched_term
     * @param array $states
     */
    public function findByNameContains($searched_term, array $states = null)
    {
        $qb = $this->getQueryBuilder($states);

        $qb->add('where', $qb->expr()->like('b.name', $qb->expr()->lower(':searched_term')))
            ->setParameter('searched_term', $searched_term.'%');

        return $qb->getQuery()->getResult();
    }
}
