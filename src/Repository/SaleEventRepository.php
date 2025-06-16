<?php

namespace App\Repository;

use DateTimeImmutable;
use App\Entity\SaleEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SaleEvent>
 *
 * @method SaleEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method SaleEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method SaleEvent[]    findAll()
 * @method SaleEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SaleEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaleEvent::class);
    }

    /**
     * Finds events that are currently active or will start today.
     * These events overlap with the current calendar day and have not yet ended.
     * Ordered by start date ascending.
     *
     * @return SaleEvent[]
     */
    public function findEventsForSpecificDate(?DateTimeImmutable $date = null): array
    {        
        $startOfDay = (clone $date)->setTime(0, 0, 0);   // Today at 00:00:00
        $endOfDay = (clone $date)->setTime(23, 59, 59); // Today at 23:59:59

        return $this->createQueryBuilder('s')
            // Event must start on or before the end of today
            ->andWhere('s.startDate <= :endOfDay')
            // AND Event must end on or after the start of today
            ->andWhere('s.endDate >= :startOfDay')
            // AND Event must not have ended yet (to exclude truly closed events)
            ->andWhere('s.endDate > :date')
            ->setParameter('startOfDay', $startOfDay)
            ->setParameter('endOfDay', $endOfDay)
            ->setParameter('date', $date)
            ->orderBy('s.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds events that are yet to start and are NOT today's events.
     * These events start strictly after the end of today.
     * Ordered by start date ascending.
     *
     * @return SaleEvent[]
     */
    public function findIncomingEvents(): array
    {
        $now = new \DateTimeImmutable();
        $endOfToday = (clone $now)->setTime(23, 59, 59); // End of today

        return $this->createQueryBuilder('s')
            ->andWhere('s.startDate > :endOfToday')
            ->setParameter('endOfToday', $endOfToday)
            ->orderBy('s.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds events that have already ended.
     * An event is closed if its end date is strictly before the current moment.
     * Ordered by end date descending (most recently closed first).
     *
     * @return SaleEvent[]
     */
    public function findClosedEvents(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('s')
            ->andWhere('s.endDate < :now')
            ->setParameter('now', $now)
            ->orderBy('s.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return SaleEvent[] Returns an array of SaleEvent objects for past events
     */
    public function findPassedSaleEvents(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.startDate < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.startDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return SaleEvent[] Returns an array of SaleEvent objects for today's events
     */
    public function findTodaySaleEvents(): array
    {
        $todayStart = new \DateTimeImmutable('today');
        $todayEnd = new \DateTimeImmutable('tomorrow');

        return $this->createQueryBuilder('s')
            ->andWhere('s.startDate >= :todayStart')
            ->andWhere('s.startDate < :todayEnd')
            ->setParameter('todayStart', $todayStart)
            ->setParameter('todayEnd', $todayEnd)
            ->orderBy('s.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return SaleEvent[] Returns an array of SaleEvent objects for incoming events
     */
    public function findIncomingSaleEvents(): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.startDate >= :now')
            ->setParameter('now', new \DateTimeImmutable('tomorrow')) // À partir de demain
            ->orderBy('s.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}