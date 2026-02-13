<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ContactType;
use App\DTO\ContactDTO;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;



final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();


        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Envoyer un email.
            $email = (new TemplatedEmail()) // Utilisation de TemplatedEmail pour envoyer un email avec un template Twig
                ->to($data->service) // Destinataire de l'email
                ->from($data->email) // Expéditeur de l'email (utilise l'email fourni dans le formulaire)
                ->subject('Nouveau message de contact') // Sujet de l'email
                ->htmlTemplate('emails/contact.html.twig') // Template Twig pour le contenu de l'email
                ->context(['data' => $data]); // Passer les données du formulaire au template Twig
            try {
                $mailer->send($email); // Envoyer l'email
                $this->addFlash('success', 'Votre message a été envoyé avec succès !'); // Ajouter un message flash de succès
                return $this->redirectToRoute('contact'); // Rediriger vers la même page après l'envoi de l'email
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.'); // Ajouter un message flash d'erreur
                return $this->redirectToRoute('contact'); // Rediriger vers la même page après l'échec de l'envoi de l'email
            }
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
