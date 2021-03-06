<?php

/**
 *
 *  * This is an iumio component [https://iumio.com]
 *  *
 *  * (c) Mickael Buliard <mickael.buliard@iumio.com>
 *  *
 *  * Bill&Go, gérer votre administratif efficacement [https://www.billandgo.fr]
 *  *
 *  * To get more information about licence, please check the licence file
 *
 */


namespace BillAndGoBundle\Repository;

/**
 * LineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LineRepository extends \Doctrine\ORM\EntityRepository
{
    public function lastLinesNames($refUser)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a.name');
        $qb->where('a.refUser = :user')
            ->setParameter('user', $refUser)
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(10)
            //->andWhere('a.type = 1')
        ;
        return $qb->getQuery()->getResult();
    }

    public function lastLinesDescriptions($refUser)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->select('a.description');
        $qb->where('a.refUser = :user')
            ->setParameter('user', $refUser)
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(10)
            //->andWhere('a.type = 1')
        ;
        return $qb->getQuery()->getResult();
    }
}
