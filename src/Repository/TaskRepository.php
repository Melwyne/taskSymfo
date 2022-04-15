<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Task $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Task $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */

    //Je rentre mes paramètres que je retrouve dans mon controller
    public function searchTaskByTitle($title, $taskActive, $type, $dateStart, $dateEnd){
        // alias est égal au nom de notre entité
        $qb = $this->createQueryBuilder('task');
        $qb
            //selcetionne la table (entité)
            ->select('task')
            //Je lui joins la table type, je donne l'entité de ma table
            ->leftJoin('task.type', 'type')
            //selectionne la table (entité)
            ->addSelect('type');
            //si $title est appelé ALORS
            if ($title){
                //LIKE = le mot doit contenir ce qui a été écrit
                $qb->Where('task.title LIKE :title')
                // mettre en clé le nom donné au-dessus
                //entourer le $title de '%' permet de dire qu'on cherche tous les mots qui possèdent le mot de $title
                ->setParameter('title', '%' . $title . '%');
            }
            //si $taskActive est appelé ALORS
            if ($taskActive){
                //2e condition -> grâce à LIKE, taskActive doit comporter un mot qui correspond à task.active
                $qb->andWhere('task.active LIKE :taskActive')
                //mettre le nom donné à task.active et lui donner une valeur
                //ici je lui attribue la valeur de ma variable $taskActive afin de comparer les 2 valeurs
                ->setParameter('taskActive', $taskActive);
            }
            //si $type est appelé ALORS
            if ($type){
                //rappeler à chaque fois le $qb pour faire WHERE et setParameter
                $qb->andWhere('type.id LIKE :typeId')
                ->setParameter('typeId', $type);
            }
            if ($dateStart || $dateEnd){
                $qb->andWhere('task.dateheure BETWEEN :dateStart AND :dateEnd')
                ->setParameter('dateStart', $dateStart)
                ->setParameter('dateEnd', $dateEnd);
            }
            $query = $qb
                //ranger par ordre de date décroissant
                ->orderBy('task.dateheure', 'DESC')
                ->getQuery();
        //je récupère le résultat dans ma variable $tasks
        $tasks = $query->getArrayResult();
        //je retourne ma variable
        return $tasks;
    }

    /* public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
