<?php

namespace App\Controller;

use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController {

    #[Route('/accueil', name: 'app_home')]
    public function afficherAccueil(Request $request): Response {
        $n = rand(1, 10);

        $obj = new stdClass(); // Une classe "générique"
        $obj->nom = "Dupont";
        $obj->prenom = "Jean";
        $obj->age = 35;

        $this->getUser(); // Entité User ou null si pas connecté


        return $this->render('accueil.html.twig', [
            'nombre' => $n,
            'personne' => $obj
        ]);
    }

}
