<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RecipeType;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/recettes', name: 'admin.recipe.')] // Préfixe pour toutes les routes de ce contrôleur
final class RecipeController extends AbstractController
{

    #[Route('/', name: 'index')] // Route pour afficher la liste des recettes
    public function index(RecipeRepository $repository): Response //EntityManagerInterface pour gérer les entités dans la base de données
    {
        $recipes = $repository->findWithDurationLowerThan(60); // Récupère les recettes avec une durée inférieure à 60 minutes

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }



    #[Route('/creat', name: 'creat')]
    public function creat(Request $request, EntityManagerInterface $entityManager)
    {
        $recipe = new Recipe(); // Crée une nouvelle instance de Recipe
        $form = $this->createForm(RecipeType::class, $recipe); // Crée le formulaire lié à cette instance
        $form->handleRequest($request); // Récupère les données du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();
            $this->addFlash('success', 'La nouvelle recette a bien été créée.');
            return $this->redirectToRoute('admin.recipe.index'); // Redirige vers la liste des recettes après la création
        }
        return $this->render('admin/recipe/creat.html.twig', [
            'form' => $form->createView()
        ]);
    }




    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])] // Route pour éditer une recette, avec une contrainte pour que l'id soit un nombre
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La recette a bien été modifiée.');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }



    #[Route('/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])] // Route pour supprimer une recette, avec une contrainte pour que l'id soit un nombre
    public function remove(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe); // remove vien de l'entity manager pour supprimer une entité
        $entityManager->flush();
        $this->addFlash('success', 'La recette a bien été supprimée.');
        return $this->redirectToRoute('admin.recipe.index');
    }
}
