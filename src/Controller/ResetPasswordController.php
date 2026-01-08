<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ResetPasswordController extends AbstractController
{
    private int $tokenTtl = 3600; // 1 hour

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function request(Request $request, UserRepository $ur, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $ur->findOneBy(['email' => $email]);

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetRequestedAt(new \DateTime());
                $em->persist($user);
                $em->flush();

                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], 0);

                $emailMessage = (new TemplatedEmail())
                    ->from(new Address('noreply@example.com', 'CourMaker'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('security/password_reset/email.html.twig')
                    ->context(['resetUrl' => $resetUrl]);

                $mailer->send($emailMessage);
            }

            $this->addFlash('success', 'Si l’adresse existe, un email a été envoyé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password_reset/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $ur, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $user = $ur->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token invalide.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if (!$user->isPasswordRequestNonExpired($this->tokenTtl)) {
            $this->addFlash('error', 'Le lien a expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = $form->get('plainPassword')->getData();
            $user->setPassword($hasher->hashPassword($user, $plain));
            $user->setResetToken(null);
            $user->setResetRequestedAt(null);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/password_reset/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }
}
