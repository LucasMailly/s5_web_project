<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\IndividualUserFormType;
use App\Form\ProUserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/profile')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]

    public function edit(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        $form = null;
        if ( in_array('ROLE_ADMIN', $user->getRoles()) ) {
            // Admin is not supposed to be able to edit his profile
            // If he really wants to, he can do it in the admin panel, so we redirect him there
            return $this->redirectToRoute('app_admin');
        }
        if ( in_array('ROLE_INDIVIDUAL', $user->getRoles(), true)) {
            $form = $this->createForm(IndividualUserFormType::class, $user);
            $form->handleRequest($request);}
        if ( in_array('ROLE_PRO', $user->getRoles(), true)) {
            $form = $this->createForm(ProUserFormType::class, $user);
            $form->handleRequest($request);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, UserRepository $userRepository): Response
    {
        // We check if the user is logged in
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        
        if ( in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            // Admin is not supposed to be able to delete his profile
            // If he really wants to, he can do it in the admin panel, so we redirect him there
            return $this->redirectToRoute('app_admin');
        }

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
