<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;


#[Route('/admin/category', name: 'admin.category.')] // Préfixe pour toutes les routes de ce contrôleur
class CategoryController extends AbstractController
{

   #[Route(name: 'index')] // Route pour afficher la liste des catégories
   public function index(CategoryRepository $categoryRepository)
   {


      return $this->render('admin/category/index.html.twig', [
         'categories' => $categoryRepository->findAll() // Récupère toutes les catégories depuis la base de données et les passe à la vue
      ]);
   }



   #[Route('/create', name: 'create')] // Route pour créer une nouvelle catégorie
   public function create(Request $request, EntityManagerInterface $entityManager)
   {
      $Category = new Category(); // Crée une nouvelle instance de la classe Category
      $form = $this->createForm(CategoryType::class, $Category); // Crée un formulaire basé sur la classe CategoryType et lié à l'objet $Category
      $form->handleRequest($request); // Traite la requête pour le formulaire
      if ($form->isSubmitted() && $form->isValid()) {
         $entityManager->persist($Category); // Prépare l'objet $Category à être enregistré dans la base de données
         $entityManager->flush(); // Exécute les opérations en attente, enregistrant ainsi la nouvelle catégorie dans la base de données
         $this->addFlash('success', 'La catégorie a été créée avec succès.');
         return $this->redirectToRoute('admin.category.index');
      }
      return $this->render('admin/category/create.html.twig', [
         'form' => $form->createView()
      ]);
   }



   #[Route('/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])] // Route pour éditer une catégorie, avec une contrainte pour que l'id soit un nombre
   public function edit(Category $Category, Request $request, EntityManagerInterface $entityManager)
   {
      $form = $this->createForm(CategoryType::class, $Category); // Crée un formulaire basé sur la classe CategoryType et lié à l'objet $Category
      $form->handleRequest($request); // Traite la requête pour le formulaire
      if ($form->isSubmitted() && $form->isValid()) {
         $entityManager->flush(); // Exécute les opérations en attente, enregistrant ainsi la nouvelle catégorie dans la base de données
         $this->addFlash('success', 'La catégorie a été mise à jour avec succès.');
         return $this->redirectToRoute('admin.category.index');
      }
      return $this->render('admin/category/edit.html.twig', [
         'category' => $Category,
         'form' => $form->createView()
      ]);
   }




   #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])] // Route pour supprimer une catégorie, avec une contrainte pour que l'id soit un nombre
   public function remove(Category $Category, EntityManagerInterface $entityManager)
   {
      $entityManager->remove($Category); // Supprime l'objet $Category de la base de données
      $entityManager->flush(); // Exécute les opérations en attente, supprimant ainsi la catégorie de la base de données
      $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
      return $this->redirectToRoute('admin.category.index');
   }
}
