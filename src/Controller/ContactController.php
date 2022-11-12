<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
     public function index(Request $request, ContactRepository $contactRepository): Response
     {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);
        $user=$this->getUser();

         if ($form->isSubmitted() && $form->isValid()) {
            $contact->setUser($user);
            $contactRepository->add($contact, true);

            $this->addFlash('success', 'Votre message a bien été envoyé !');

             return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
         }

         return $this->renderForm('contact/index.html.twig', [
             'contact' => $contact,
             'form' => $form,
         ]);
    }
}
