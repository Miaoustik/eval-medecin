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
            $params = [];
            foreach ($criteria as $key => $crit) {
                if ($key === 'roles') {
                    $queryBuilder = $queryBuilder->andWhere("r.roles LIKE :role");
                    $params['role'] = "%" . $crit . "%";
                } else if ($key === 'collection') {
                    foreach ($crit as $name => $values) {
                        $queryBuilder = $queryBuilder
                            ->Join("r.$name", $name)
                            ->addSelect("$name.name");
                        if ($name === 'allergens') {
                            foreach ($values as $index => $value) {
                                $queryBuilder = $queryBuilder
                                    ->andWhere(":$name" . "$index NOT MEMBER OF r.allergens" );

                                $params[$name. $index] = $value;
                            }
                        } else {
                            foreach ($values as $index => $value) {
                                $queryBuilder = $queryBuilder
                                    ->andWhere(":$name" . $index . " MEMBER OF r.diets" );

                                $params[$name. $index] = $value;
                            }
                        }


                    }

                } else if (gettype($crit) === 'string') {
                    $queryBuilder = $queryBuilder
                        ->andWhere("r.$key LIKE :$key");
                    $params[$key] = '%' . ucfirst($crit) . '%';
                } else {
                    $queryBuilder = $queryBuilder
                        ->andWhere("r.$key = :$key");
                    $params[$key] = $crit;
                }
            }
            $queryBuilder->setParameters($params);
        }

        //dd($queryBuilder
        //    ->getQuery()
        //    ->getResult());

        if ($order) {
            $queryBuilder = $queryBuilder->orderBy("r.$order");
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function countString($criteria) :int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('count(r.id)');
        $params = [];
        foreach ($criteria as $key => $crit) {
            if ($key === 'roles') {
                $qb = $qb->andWhere("r.roles LIKE :role");
                $params['role'] = "%" . $crit . "%";
            } else if ($key === 'collection') {
                foreach ($crit as $name => $values) {
                    $qb = $qb
                        ->leftJoin("r.$name", $name);
                    if ($name === 'allergens') {
                        foreach ($values as $index => $value) {
                            $qb = $qb
                                ->andWhere(":$name" . "$index NOT MEMBER OF r.allergens" );

                            $params[$name. $index] = $value;
                        }
                    } else {
                        foreach ($values as $index => $value) {
                            $qb = $qb
                                ->andWhere(":$name" . $index . " MEMBER OF r.diets" );

                            $params[$name. $index] = $value;
                        }
                    }

                }

            } else if (gettype($crit) === 'string') {
                $qb = $qb
                    ->andWhere("r.$key LIKE :$key");
                $params[$key] = '%' . ucfirst($crit) . '%';
            } else {
                $qb = $qb
                    ->andWhere("r.$key = :$key");
                $params[$key] = $crit;
            }
        }
        $qb->setParameters($params);

        return $qb->getQuery()
            ->getSingleScalarResult();

    }
}