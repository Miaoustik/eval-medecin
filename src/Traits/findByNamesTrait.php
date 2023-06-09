<?php

namespace App\Traits;

trait findByNamesTrait
{
    public function findAllByNames(array $names)
    {
        if (count($names) === 0) {
            return [];
        }

        $qb = $this->createQueryBuilder('r');
        $params = [];
        foreach ( $names as $k => $name) {
            $qb = $qb->orWhere("r.name = :name$k");
            $params["name$k"] = $name;
        }

        $qb->setParameters($params);

        return $qb
            ->getQuery()
            ->getResult();
    }
}