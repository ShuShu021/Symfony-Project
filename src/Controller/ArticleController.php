<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ArticleController extends AbstractController {
    #[Route('/articles/create', name: 'app_article_create')]
    public function index(EntityManagerInterface $em, Request $request): Response {

        $article = new Article(); // On crée un nouvel article vide
        $form = $this->createForm(ArticleType::class, $article); // On crée le formulaire en lui passant l'article vide

        // On "hydrate" le formulaire avec les données de la requête
        // (Le formulaire gère la requête tout seul)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire a été soumis et que les données sont valides, on enregistre l'article
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_article_list');
        }

        // Si on n'a pas soumis le formulaire, on affiche la vue avec le formulaire
        return $this->render('article/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/articles/remove', name: 'app_article_remove')]
    function remove(ArticleRepository $ar, EntityManagerInterface $em) {
        $articles = $ar->findAll();

        foreach ($articles as $article) {
            $em->remove($article);
        }

        $em->flush();

        return new Response('Articles supprimés');
    }

    #[Route('/articles/{id}', name: 'app_article_details')]
    public function details($id, ArticleRepository $ar) {
        $article = $ar->find($id);

        if (empty($article)) {
            throw new NotFoundHttpException;
        }

        return $this->render('article/details.html.twig', [
            'article' => $article
        ]);
    }

    #[Route('/articles', name: 'app_article_list')]
    function list(ArticleRepository $ar): Response {

        /**
         * $ar est une instance de ArticleRepository
         * On le reçoit en argument de la méthode list() 
         * grâce au mécanisme d'injection de dépendances de Symfony
         */

        $articles = $ar->findAll(); // On récupère tous les articles en base de données

        // Puis on les passe à la vue pour affichage
        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
