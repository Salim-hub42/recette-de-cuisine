<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RecipeType;

final class RecipeController extends AbstractController
{

    #[Route('/recettes', name: 'recipe.index')] // Route pour afficher la liste des recettes
    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $entityManager): Response //EntityManagerInterface pour gérer les entités dans la base de données
    {
        $recipes = $repository->findWithDurationLowerThan(60); // Récupère les recettes avec une durée inférieure à 60 minutes


        $existing = $repository->findOneBy(['slug' => 'barbe-a-papa']); // Vérifie si la recette existe déjà
        if (!$existing) { // Si elle n'existe pas, on la crée
            $recipe = (new Recipe())
                ->setTitle('Barbe à papa')
                ->setSlug('barbe-a-papa')
                ->setContent('La barbe à papa est une confiserie légère et sucrée, faite de sucre filé en fines mèches,
                 souvent colorée et servie sur un bâtonnet.
                  Elle est populaire lors des fêtes foraines et des carnavals.')
                ->setDuration(15)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($recipe); // Prépare la recette à être insérée dans la base de données
            $entityManager->flush(); // Persiste les modifications dans la base de données
        }



        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }



    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['slug' => '[a-z0-9\-]+', 'id' => '\d+'])] // requirements pour valider le format du slug et de l'id
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }


        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recettes/{id}/edit', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La recette a bien été modifiée.');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }

    #[Route('/recettes/creat', name: 'recipe.creat')]
    public function creat(Request $request, EntityManagerInterface $entityManager)
    {
        $recipe = new Recipe(); // Crée une nouvelle instance de Recipe
        $form = $this->createForm(RecipeType::class, $recipe); // Crée le formulaire lié à cette instance
        $form->handleRequest($request); // Récupère les données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            $this->addFlash('success', 'La nouvelle recette a bien été créée.');
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('recipe/creat.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/recettes/{id}', name: 'recipe.delete', methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe); // remove vien de l'entity manager pour supprimer une entité
        $entityManager->flush();
        $this->addFlash('success', 'La recette a bien été supprimée.');
        return $this->redirectToRoute('recipe.index');
    }
}
