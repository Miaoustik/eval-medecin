<?php

namespace App\Repository;

use App\Entity\Diet;
use App\Traits\findAllPaginatedByTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Diet>
 *
 * @method Diet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diet[]    findAll()
 * @method Diet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DietRepository extends ServiceEntityRepository
{
    use findAllPaginatedByTrait;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diet::class);
    }

    public function save(Diet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Diet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Diet[] Returns an array of Diet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Diet
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByIds(array $allergenIds)
    {
        $qb = $this->createQueryBuilder('a');
        $params = [];

        foreach ($allergenIds as $index => $allergenId) {
            $qb->orWhere('a.id = :id' . $index);
            $params[':id' . $index] = $allergenId;
        }

        return $qb
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }


}
