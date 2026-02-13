<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }





    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */

    public function findWithDurationLowerThan(int $duration): array // méthode personnalisée pour trouver des recettes avec une durée inférieure à une valeur donnée
    {
        return $this->createQueryBuilder('r') // le createQueryBuilder permet de construire une requete
            ->where('r.duration <= :duration') // on ajoute une condition where pour filtrer : ici les recettes avec une durée inférieure ou égale à la valeur passée en paramètre
            ->orderBy('r.duration', 'ASC') // on trie les résultats par durée croissante
            ->setMaxResults(10) // on limite le nombre de résultats à 10
            ->setParameter('duration', $duration) // on bind la valeur du paramètre :duration avec la valeur passée en paramètre de la méthode
            ->getQuery() // on récupère la requête construite
            ->getResult(); // on exécute la requête et on retourne les résultats sous forme de tableau d'objets Recipe
    }















    //    /**
    //     * @return Recipe[] Returns an array of Recipe objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recipe
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
