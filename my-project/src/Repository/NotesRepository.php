<?php

namespace App\Repository;

use App\Entity\Notes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Notes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notes[]    findAll()
 * @method Notes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Notes::class);
    }

    /**
     * @return Notes[] Returns an array of Notes objects
     **/
    public function findAverageById($idFilm)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('AVG(entity.valeur) as Moyenne')
            ->from(Notes::class, 'entity')
            ->leftJoin('entity.film',"film")
            ->where('film.idApi = :id')
            ->setParameters(array(
                'id' => $idFilm,
            ));
        return $qb->getQuery()->getOneOrNullResult();
    }


    /*
    public function findOneBySomeField($value): ?Notes
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
