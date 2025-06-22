<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Label>
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    /**
     * Crée un QueryBuilder pour récupérer les étiquettes (labels) qui ne sont liées à aucun produit.
     *
     * @return QueryBuilder
     */
    public function findUnlinkedLabels(): array
    {
        return $this->createQueryBuilder('l')
            ->leftJoin('l.produit', 'p')
            ->andWhere('l.produit IS NULL')
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult(); // <-- La requête est exécutée ici
    }
}
