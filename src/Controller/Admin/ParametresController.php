<?php

namespace App\Controller\Admin;

use App\Entity\Parametres;
use App\Repository\ParametresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class ParametresController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ParametresRepository $parametresRepository
    ) {}

    #[Route('/params-list', name: 'param.list')]
    public function listParam(Request $request, SessionInterface $session): Response
    {
        // Utilisation de query->get pour les paramètres d'URL (GET)
        $type = $request->query->get('type');
        $session->set('menu', 'menus');
        $session->set('sub-menu', $type);

        $listParams = $this->parametresRepository->findByType($type);
        $parents = $this->parametresRepository->findBy(['type' => $type, 'parent' => null]);
        
        return $this->render('admin/parametres/list-param.html.twig', [
            'params' => $listParams,
            'parents' => $parents,
            'type' => $type
        ]);
    }

    #[Route('/param/new-{type?}', name: 'param.new')]
    public function newParam(
        Request $request, 
        SluggerInterface $slugger, 
        ParametresRepository $parametresRepository, 
        string $type
    ): RedirectResponse {
        $id = $request->request->get("id"); // On cherche l'ID dans le POST
        $param = $id ? $this->parametresRepository->find($id) : new Parametres();

        if ($request->isMethod('POST')) {
            $libelle = $request->request->get("libelle");
            $present = $request->request->get("present");
            $desc = $request->request->get("desc");

            $parentId = $request->request->get("parent");
            $parent = $parentId ? $parametresRepository->find($parentId) : null;
            
            $param->setLibelle($libelle)
                ->setSlug($slugger->slug($libelle)->lower())
                ->setType($type)
                ->setParent($parent)
                ->setPresentation($present)
                ->setDescription($desc);

            if (method_exists($param, 'updatedTimestamps')) {
                $param->updatedTimestamps();
            }

            // Gestion de l'utilisateur (Symfony 7+)
            // if (method_exists($param, 'updatedUserstamps')) {
            //     $param->updatedUserstamps($this->getUser());
            // }

            $this->em->persist($param);
            $this->em->flush();
            
            $this->addFlash('success', 'Paramètre enregistré.');
        }

        return $this->redirectToRoute('param.list', ['type' => $type]);
    }

    #[Route('/param-delete-{id}', name: 'param.delete', methods: ['POST', 'DELETE'])]
    public function deleteParametre(Parametres $param, Request $request): RedirectResponse
    {
        $type = $param->getType(); // On récupère le type avant la suppression pour la redirection

        if ($this->isCsrfTokenValid('delete'.$param->getId(), $request->request->get('_token'))) {
            $this->em->remove($param);
            $this->em->flush();
            $this->addFlash('success', 'Paramètre supprimé.');
        }

        return $this->redirectToRoute('param.list', ['type' => $type]);
    }
}