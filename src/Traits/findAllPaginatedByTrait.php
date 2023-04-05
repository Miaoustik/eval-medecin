<?php

namespace App\Traits;

trait findAllPaginatedByTrait
{

    public function findAllPaginatedBy(int $maxResult, int $page = 1, mixed $property = null, array $criteria = null, string $order = null): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->setMaxResults($maxResult)
            ->setFirstResult($maxResult * ($page - 1));

        if ($property) {
            if (gettype($property) === 'array') {
                foreach ($property as $key => $prop) {
                    if ($key === 0) {
                        $queryBuilder = $queryBuilder->select("r.$prop");
                    } else {
                        $queryBuilder = $queryBuilder->addSelect("r.$prop");
                    }
                }
            } else {
                $queryBuilder = $queryBuilder->select('r.' . $property);
            }
        }

        if ($criteria) {
            foreach ($criteria as $key => $crit) {
                if ($key === 'roles') {
                    $queryBuilder = $queryBuilder->andWhere("r.roles LIKE :role")
                        ->setParameter('role', "%" . $crit . "%");
                } else if (gettype($crit) === 'string') {

                    $queryBuilder = $queryBuilder
                        ->andWhere("r.$key LIKE :param")
                        ->setParameter("param", '%' . ucfirst($crit) . '%' );
                } else {
                    $queryBuilder = $queryBuilder
                        ->andWhere("r.$key = :param")
                        ->setParameter("param", $crit);
                }

            }
        }

        if ($order) {
            $queryBuilder = $queryBuilder->orderBy("r.$order");
        }

        //dd($queryBuilder
            //->getQuery());

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function countString($criteria) :int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('count(r.id)');

        foreach ($criteria as $key => $crit) {
            if ($key === 'roles') {
                $qb = $qb->andWhere("r.roles LIKE :role")
                    ->setParameter('role', "%" . $crit . "%");
            } else if (gettype($crit) === 'string') {
                $qb = $qb->orWhere('r.title LIKE :param')
                    ->setParameter('param', '%' . $crit . '%');
            } else {
                $qb = $qb->orWhere('r.title = :param')
                    ->setParameter('param',  $crit);
            }

        }

        //dd($qb->getQuery());

        return $qb->getQuery()
            ->getSingleScalarResult();

    }
}