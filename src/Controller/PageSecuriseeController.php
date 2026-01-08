<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PageSecuriseeController extends AbstractController {
    #[IsGranted('ROLE_ADMIN')] // Equivalent
    #[Route('/secure', name: 'app_secure')]
    function secure(): Response {
        // Equivalent
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException;
        }

        // Equivalent
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return new Response('<body>OK</body>');
    }
}
