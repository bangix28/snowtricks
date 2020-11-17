<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function search($search)
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('u.content', ':query'),
                        $qb->expr()->like('u.name', ':query')
                    )
                )

            )
            ->setParameter('query', '%' . $search . '%' )
        ;
        return $qb
            ->getQuery()
            ->getResult();
    }
}
