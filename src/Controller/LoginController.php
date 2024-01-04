<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authdUtils): Response
    {
        $error = $authdUtils->getLastAuthenticationError();
        $last_Username = $authdUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'error' => $error,
            'last_username' => $last_Username
        ]);
    }
    #[Route('/logout', name: 'app_logout')]
    public function logout($security): Response
    {

        $security->logout(false);
        return $this->redirectToRoute('app_login');

    }
}
