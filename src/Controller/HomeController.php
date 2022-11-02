<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, Request $request): Response
    {
        // check if user search for something
        $search = $request->query->get('search');
        // if he does, then we get the articles that match the search
        if ($search && $search !== '') {
            $page = $request->query->get('page', 1);
            if ($page < 1) {
                $page = 1;
            }
            $limit = 20;
            $offset = ($page - 1) * $limit;
            $articles = $articleRepository->search($search, $limit, $offset, $request->query->all());
            $total = $articleRepository->countSearch($search);
            $nbPages = ceil($total / $limit);

            return $this->render('home/search.html.twig', [
                'articles' => $articles,
                'page' => $page,
                'nbPages' => $nbPages,
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
