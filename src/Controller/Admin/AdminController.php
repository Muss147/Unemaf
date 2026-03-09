<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Photos;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Nouveau service
use Symfony\Component\Routing\Attribute\Route; // Nouveau namespace Attributes

#[Route('/admin')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
        private readonly UserPasswordHasherInterface $passwordHasher // Mis à jour
    ) {}

    #[Route('/', name: 'admin')]
    public function index(
        UserRepository $userRepository,
        ActivityRepository $activityRepository, 
        SessionInterface $session
    ): Response {
        $session->set('menu', 'dash');
        $session->set('sub-menu', '');
        
        return $this->render('admin/index.html.twig', [
            'users' => $userRepository->findAll(),
            'activities' => $activityRepository->findAll()
        ]);
    }

    #[Route('/user-profile-{id}', name: 'admin.show', methods: ['GET', 'POST'])]
    public function showAdmin(User $user): Response
    {
        return $this->render('admin/show-admin.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/admin-list', name: 'admin.list')]
    public function listAdmin(UserRepository $userRepository, SessionInterface $session): Response
    {
        $session->set('menu', 'admins');
        $session->set('sub-menu', 'list-admin');

        return $this->render('admin/list-admin.html.twig', [
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/admin-new', name: 'admin.new')]
    public function newAdmin(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        $session->set('menu', 'admins');
        $session->set('sub-menu', 'add-admin');

        if ($request->isMethod('POST')) {
            $token = $request->request->get("token");
            
            // Validation CSRF
            if (!$this->isCsrfTokenValid('upload', $token)) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }

            $prenom = $request->request->get("prenom");
            $nom = $request->request->get("nom");
            $contact = $request->request->get("contact");
            $email = $request->request->get("email");
            $username = $request->request->get("username");
            $role = (array) $request->request->all("role"); // S'assure que c'est un tableau
            $pswd = $request->request->get("pswd");

            // Validation des champs obligatoires
            if (empty($prenom) || empty($nom) || empty($email) || empty($contact) || empty($role)) {
                $this->addFlash('error', 'Veuillez remplir les champs obligatoires');
                return $this->render('admin/add-admin.html.twig');
            }

            // Gestion du mot de passe
            if (!empty($pswd)) {
                $v_pswd = $request->request->get("v_pswd");
                $uppercase = preg_match('@[A-Z]@', $pswd);
                $lowercase = preg_match('@[a-z]@', $pswd);
                $number    = preg_match('@[0-9]@', $pswd);
                $specialChars = preg_match('@[^\w]@', $pswd);

                if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($pswd) < 8) {
                    throw new \Exception('Le mot de passe ne respecte pas les critères de sécurité.');
                }
                if ($pswd !== $v_pswd) {
                    throw new \Exception('Les deux mots de passe ne sont pas conformes.');
                }
            }

            $user = $userRepository->findOneBy(['username' => $username]) ?? new User();
            
            $user->setFirstname($prenom)
                ->setLastname($nom)
                ->setContact($contact)
                ->setEmail($email)
                ->setUsername($username)
                ->setRoles($role);

            if (!empty($pswd)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $pswd);
                $user->setPassword($hashedPassword);
            }

            // Gestion de l'avatar
            $file = $request->files->get('kt_user_add_user_avatar');
            if ($file) {
                $photo = new Photos();
                $fileName = $this->fileUploader->upload($file);
                $photo->setSource($fileName)->setAlt($prenom . " " . $nom);
                $this->em->persist($photo);
                $user->setAvatar($photo);
            }

            $this->em->persist($user);
            $this->em->flush();
            
            $this->addFlash('success', 'Utilisateur enregistré avec succès');

            if ($request->request->get("action") === "update") {
                return $this->redirectToRoute('admin.list');
            }
        }

        return $this->render('admin/add-admin.html.twig');
    }

    #[Route('/user-delete-{id}', name: 'admin.delete', methods: ['DELETE', 'POST'])]
    public function deleteAdmin(User $user, Request $request): RedirectResponse
    {
        // En Symfony 7/8, on vérifie souvent le token via '_token'
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }

        return $this->redirectToRoute('admin.list');
    }
}