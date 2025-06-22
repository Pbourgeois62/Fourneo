<?php

namespace App\Repository;

use App\Entity\DeliveryNote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeliveryNote>
 */
class DeliveryNoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeliveryNote::class);
    }

    //    /**
    //     * @return DeliveryNote[] Returns an array of DeliveryNote objects
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

    //    public function findOneBySomeField($value): ?DeliveryNote
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Récupère les bons de livraison qui ne sont liés à aucun produit.
     *
     * @return DeliveryNote[] Returns an array of DeliveryNote objects
     */
    public function findUnlinkedDeliveryNotes(): array
    {
        return $this->createQueryBuilder('dn') // Alias 'dn' pour DeliveryNote
            ->leftJoin('dn.products', 'p') // 'products' est la propriété de relation dans DeliveryNote
            ->andWhere('p.id IS NULL')
            ->orderBy('dn.number', 'ASC') // 'number' est la propriété du bon de livraison
            ->getQuery() // Obtient l'objet Query
            ->getResult(); // Exécute la requête et retourne les résultats sous forme de tableau d'objets
    }
}
