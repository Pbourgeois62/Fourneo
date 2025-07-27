<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Récupère un QueryBuilder pour les produits ayant la catégorie "sous-recette" OU "ingrédient" (ou les deux).
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForSubRecipeOrIngredientProducts(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c') // Utilise INNER JOIN car le produit DOIT avoir au moins une de ces catégories
            ->andWhere('c.name IN (:categories_names)')
            ->setParameter('categories_names', ['sous-recette', 'ingrédient'])
            ->orderBy('p.name', 'ASC')
            ->groupBy('p.id'); // Grouper par ID pour éviter les doublons si un produit a les deux catégories
    }

    /**
     * Trouve les produits par le nom de leur catégorie.
     *
     * @param string $categoryName Le nom de la catégorie à filtrer.
     * @return Product[]
     */
    public function findByCategoryName(string $categoryName): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.categories', 'c')
            ->andWhere('c.name = :categoryName')
            ->setParameter('categoryName', $categoryName)
            ->orderBy('p.name', 'ASC')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult();
    }
}