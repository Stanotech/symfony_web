<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function searchAndSort(?string $searchTerm, ?string $sortField, ?string $sortOrder)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a') 
            ->addSelect('a');

        if ($searchTerm) { 
            $qb->andWhere('p.title LIKE :searchTerm OR p.content LIKE :searchTerm OR a.firstName LIKE :searchTerm OR a.lastName LIKE :searchTerm')
               ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if ($sortField) {
            $sortOrder = $sortOrder === 'DESC' ? 'DESC' : 'ASC';            

            switch ($sortField) {
                case 'title':
                    $qb->orderBy('p.title', $sortOrder);
                    break;
                case 'createdAt':
                    $qb->orderBy('p.createdAt', $sortOrder);
                    break;
                case 'authorFirstName':
                    $qb->orderBy('a.firstName', $sortOrder);
                    break;
                case 'authorLastName':
                    $qb->orderBy('a.lastName', $sortOrder);
                    break;
                default:
                    $qb->orderBy('p.createdAt', 'DESC'); 
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function findByAuthor($authorId)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.author = :authorId')
            ->setParameter('authorId', $authorId)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
