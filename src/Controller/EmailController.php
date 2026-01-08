<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

final class EmailController extends AbstractController {
    #[Route('/email', name: 'app_email')]
    public function index(MailerInterface $mailer): Response {

        $email = new Email();
        $email
            ->subject('Ceci est un test')
            ->from('emmanuel.macron@elysee.gouv.fr')
            ->to('toto@example.fr')
            ->text('Tu veux être le prochain Premier Ministre ?');

        $mailer->send($email);

        return new Response('<body></body>');
    }
}
