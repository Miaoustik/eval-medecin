<?php

namespace App\Repository;

use App\Entity\Allergen;
use App\Entity\Recipe;
use App\Traits\findAllPaginatedByTrait;
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
    use findAllPaginatedByTrait;

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
}
