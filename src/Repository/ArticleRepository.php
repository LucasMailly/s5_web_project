<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findMostRecents(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.dateParution', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    // get most favorite articles (by length of favoriteUsers array)
    public function findMostFavorites(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('SIZE(a.favoriteUsers)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function search(string $search, array $params): array
    {
    $qb = $this->createQueryBuilder('a');

    $qb = $qb->where('a.title LIKE :search')
        ->InnerJoin('a.category', 'c')
        ->setParameter('search', '%'.$search.'%');

    if(isset($params['c.name ']) && $params['category'] !== 'Tout'){
        $qb = $qb->andWhere('c.name = :category')
            ->setParameter('category', $params['c.name ']);
    }

    if (isset($params['priceMin']) && $params['priceMin'] !== '') {
        $qb = $qb->andWhere('a.price >= :priceMin')
            ->setParameter('priceMin', $params['priceMin']);
    }

    if (isset($params['priceMax']) && $params['priceMax'] !== '') {
        $qb = $qb->andWhere('a.price <= :priceMax')
            ->setParameter('priceMax', $params['priceMax']);
    }

    if(isset($params['dateParution']) && $params['dateParution'] !== ''){
      $qb=$qb->andWhere('a.dateParution >= :dateParution')
        ->setParameter('dateParution', $params['dateParution']);
    }

    if(isset($params['used']) && $params['used'] !== ''){
      $qb=$qb->andWhere('a.used = :used')
        ->setParameter('used', $params['used']);
    }

    if (array_key_exists('order-price', $params) && $params['order-price'] !== ''){
              $qb=$qb->addOrderBy('a.price', $params['order-price']);
    }

    $qb = $qb->addOrderBy('a.dateParution', 'DESC')
    ->getQuery()
    ->getResult();

    return $qb;
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
