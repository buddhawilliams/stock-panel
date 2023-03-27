<?php

namespace App\Repository;

use App\Entity\Stock;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class StockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stock::class);
    }

    public function getAll(): array
    {
        $qb = $this->createQueryBuilder("s");
        $qb->orderBy("s.name", "ASC");
        $stocks = $qb->getQuery()->execute();
        $index = [];
        /** @var Stock $stock */
        foreach ($stocks as $stock) {
            $index[$stock->getSymbol()] = $stock;
        }
        return $index;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @throws Exception
     */
    public function getLastUpdate(): DateTime
    {
        $qb = $this->createQueryBuilder("s");
        $qb->select("MAX(s.updatedAt)");
        $lastUpdate = $qb->getQuery()->getSingleScalarResult();
        return $lastUpdate ? new DateTime($lastUpdate) : new DateTime("-1 day");
    }

    /**
     * @throws Exception

    public function findOneById(int $id)
    {
        throw new Exception("Function not defined: findOneById(int \$id");
    }
     */
}
