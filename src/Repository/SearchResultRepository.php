<?php

namespace App\Repository;

use App\Entity\SearchResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchResult[]    findAll()
 * @method SearchResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchResult::class);
    }

    public function save(SearchResult $result): void
    {
        $this->getEntityManager()->persist($result);
        $this->getEntityManager()->flush();
    }
}
