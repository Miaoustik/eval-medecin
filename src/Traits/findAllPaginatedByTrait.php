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
                $queryBuilder = $queryBuilder
                    ->andWhere("r.$key = :param")
                    ->setParameter("param", $key);
            }
        }

        if ($order) {
            $queryBuilder = $queryBuilder->orderBy("r.$order");
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}