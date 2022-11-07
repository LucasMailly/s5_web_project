<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;



#[Route('/article')]
class ArticleController extends AbstractController
{
    //pro dashboard 
    #[Route('/', name: 'app_article_dashboard', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();

        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findBy(['author' => $user]),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isGranted('ROLE_BLOCKED')) {
            return new Response('You are blocked!', 403);
        }
        $user = $this->getUser();

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($user);
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($article->getAuthor() != $this->getUser()) {
            return $this->redirectToRoute('app_article_dashboard');
        }
        if ($this->isGranted('ROLE_BLOCKED')) {
            return new Response('You are blocked!', 403);
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($article->getAuthor() != $this->getUser()) {
            return new Response('You are not the author!', 403);
        }

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/favorite/add/{id}', name: 'app_article_add', methods: ['GET'])]
    public function add(Request $request, Article $article, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $user->addFavoriteArticle($article);
            $entityManager->flush();
        }

        // redirect to previous page
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/favorite/drop/{id}', name: 'app_article_drop', methods: ['GET'])]
    public function drop(Request $request, Article $article, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user) {
            $user->removeFavoriteArticle($article);
            $entityManager->flush();
        }

        // redirect to previous page
        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/update/quantity/{id}/{update}', name: 'app_article_update_quantity', methods: ['GET'])]
    public function updateQuantity(Request $request, Article $article, $update, ArticleRepository $articleRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        if ($article->getAuthor() != $this->getUser()) {
            return $this->redirectToRoute('app_article_dashboard');
        }
        if ($this->isGranted('ROLE_BLOCKED')) {
            return new Response('You are blocked!', 403);
        }

        if ($update == 'plus') {
            $article->setQuantity($article->getQuantity() + 1);
        }else if ($update == 'minus' && $article->getQuantity() > 0) {
            $article->setQuantity($article->getQuantity()-1);
            }
        if ($article->getQuantity() == 0){
            $this->addFlash(
                'notice',
                'Rupture de Stock'
            );
        }
        $articleRepository->add($article, true);

        // redirect to previous page
        return $this->redirect($request->headers->get('referer'));
    }
}
