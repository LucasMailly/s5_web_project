<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator): Response
    {
        // check if user search for something
        $search = $request->query->get('search');
        // if he does, then we get the articles that match the search
        if ($search !== null) {
            $articlesQueryResults = $articleRepository->search($search, $request->query->all());

            $limit = 20;
            $articles = $paginator->paginate(
                $articlesQueryResults,
                $request->query->getInt('page', 1),
                $limit
            );

            return $this->render('home/search.html.twig', [
                'articles' => $articles,
                'search' => $search,
            ]);
        }

        // if not, we render the normal homepage
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'mostRecentArticles' => $articleRepository->findMostRecents(),
            'mostFavoriteArticles' => $articleRepository->findMostFavorites(),
        ]);
    }
}
