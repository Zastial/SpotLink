<?php

namespace App\Repository;
use App\Enum\StatusEnum;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function getEvents(): array
    {
        return $this->findBy([], ['created_at' => 'ASC']);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function getAllWithStatusValidated(): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.eventStatus', 'es')
            ->innerJoin('es.status', 's')
            ->andWhere('s.id = :statusId')
            ->setParameter('statusId', StatusEnum::VALIDATED->value)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function getAllWithStatusAwaiting(): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.eventStatus', 'es')  // Jointure avec la table event_status
            ->innerJoin('es.status', 's')       // Jointure avec la table status
            ->andWhere('s.id = :statusId')      // Condition sur le status.id
            ->setParameter('statusId', StatusEnum::AWAITING_VALIDATION->value)       // Valeur du paramètre (status_id = 1)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllWithFilters(?string $search, ?int $status): array
    {
        $qb = $this->createQueryBuilder('e')
            ->innerJoin('e.eventStatus', 'es')
            ->innerJoin('es.status', 's');

        if (!empty($search)) {
            $qb->andWhere('e.name LIKE :search')
                ->setParameter('search', "%$search%");
        }

        if ($status !== -1) {
            $qb->andWhere('s.id = :status')
                ->setParameter('status', $status);
        }

        return $qb->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getAllEventsFromUserWithStatus(?int $user_id, StatusEnum $status): array
{
    $qb = $this->createQueryBuilder('e')
        ->innerJoin('e.eventStatus', 'es')
        ->innerJoin('es.status', 's')
        ->innerJoin('e.creator', 'u') 
        ->andWhere('e.creator = :user_id')
        ->setParameter('user_id', $user_id)
        ->andWhere('s.id = :status')
        ->setParameter('status', $status->value);

    return $qb->orderBy('e.created_at', 'ASC')
        ->getQuery()
        ->getResult();
}



    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
