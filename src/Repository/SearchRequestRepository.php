<?php

namespace App\Repository;

use App\Entity\SearchRequest;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchRequest[]    findAll()
 * @method SearchRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchRequest::class);
    }

    public function save(SearchRequest $searchRequest): void
    {
        $this->getEntityManager()->persist($searchRequest);
        $this->getEntityManager()->flush();
    }

    public function findRequest(
        string $firstName,
        string $lastName,
        string $city,
        string $state,
        DateTimeInterface $period
    ): ?SearchRequest {
        $qb = $this->createQueryBuilder('sr');

        $qb
            ->where($qb->expr()->eq('sr.first_name', ':firstName'))->setParameter('firstName', $firstName)
            ->andWhere($qb->expr()->eq('sr.last_name', ':lastName'))->setParameter('lastName', $lastName)
            ->andWhere($qb->expr()->eq('sr.city', ':city'))->setParameter('city', $city)
            ->andWhere($qb->expr()->eq('sr.state', ':state'))->setParameter('state', $state)
            ->andWhere($qb->expr()->gte('sr.createdAt', ':created'))->setParameter('created', $period);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException|NonUniqueResultException) {
            return null;
        }
    }
}
