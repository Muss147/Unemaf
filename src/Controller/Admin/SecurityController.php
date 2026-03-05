<?php

namespace App\Controller\Admin; // Namespace simplifié (sans Bundle)

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route; // Nouveau namespace Attributes
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController 
{
    #[Route('/admin/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige (optionnel mais recommandé)
        if ($this->getUser()) {
            return $this->redirectToRoute('admin');
        }

        // Récupération de l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier nom d'utilisateur saisi par l'internaute
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
        // Cette méthode peut rester vide, Symfony intercepte la route automatiquement 
        // selon la configuration de ton security.yaml
        throw new \LogicException('Cette méthode peut être vide ; elle sera interceptée par le firewall.');
    }
}