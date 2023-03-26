<?php

namespace App\Repository;

use App\Entity\Allergen;
use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findTest ($id): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->select('r', 'ir', 'd', 'a')
            ->leftJoin('r.ingredientRecipes', 'ir')
            ->leftJoin('r.diets', 'd')
            ->leftJoin('r.allergens', 'a')
            ->where('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

    }

//    /**
//     * @return Recipe[] Returns an array of Recipe objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Recipe
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @param int $maxResult
     * @param int $page
     * @param int|string|null $property
     * @param array|null $criteria
     * @return Recipe[]
     */
    public function findAllPaginatedBy(int $maxResult, int $page = 1, mixed $property = null, array $criteria = null): array
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

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $id
     * @return Recipe|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findByIdRecipe(int $id): ?Recipe
    {
        return $this->createQueryBuilder('r')
            ->select("r", 'ir', 'i', 'd')
            ->leftJoin('r.allergens', 'a')
            ->leftJoin('r.diets', 'd')
            ->leftJoin('r.ingredientRecipes', 'ir')
            ->leftJoin('ir.ingredient', 'i')
            ->andWhere("r.id = :id")
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getData($id)
    {
    }


}
