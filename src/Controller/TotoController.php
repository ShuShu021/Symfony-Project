<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TotoController extends AbstractController {

    #[Route('/toto')]
    public function afficherToto(){
        return $this->render('accueil.html');
    }

}