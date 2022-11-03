<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\IndividualRegistrationFormType;
use App\Form\ProRegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    
    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    #[Route('/verify', name: 'app_verify')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id');

        // Validate the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        // Verify the user id exists in persistence
        $user = $userRepository->find($id);
        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        // Mark user as verified
        $user->setIsVerified(true);
        $entityManager->persist($user);
        $entityManager->flush();

        // Login the user automatically upon verification
        $this->authenticateUser($user);

        $this->addFlash('success', 'Votre email a été vérifié!');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/register', name: 'app_register')]
    public function register(): Response
    {
        return $this->render('registration/register.html.twig');
    }

    #[Route('/register/pro', name: 'app_register_pro')]
    public function registerPro(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(ProRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //If everything is ok, add role and persist
            $user->addRole('ROLE_PRO');

            $entityManager->persist($user);
            $entityManager->flush();
            
            // generate a signed url and email it to the user
            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'app_verify',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );
            
            $email = (new Email())
                ->from('no-reply@localhost')
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->html("Please confirm your email by clicking on this link: <a href=\"{$signatureComponents->getSignedUrl()}\">Confirm your email</a>");
            
            
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                dd('error sending email');
            }

            $this->addFlash('success', 'Un email de vérification vous a été envoyé!');
        }

        return $this->render('registration/register-pro.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/individual', name: 'app_register_individual')]
    public function registerIndividual(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(IndividualRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check that username is given and not already taken
            if ($form->get('username')->getData() == null) {
                $this->addFlash('error', 'Username is required');
                return $this->render('registration/register-individual.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            //If everything is ok, add role and persist
            $user->addRole('ROLE_INDIVIDUAL');

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register-individual.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
