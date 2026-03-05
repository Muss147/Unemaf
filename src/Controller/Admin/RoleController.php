<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class RoleController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/user-profile', name: 'role.show')]
    public function showRole(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');
        $user = $userRepository->find($id);
        
        return $this->render('admin/show-admin.html.twig', [
            'user' => $user
        ]);
    }
}