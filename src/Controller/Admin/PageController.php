<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\Photos;
use App\Entity\Parametres;
use App\Repository\PageRepository;
use App\Repository\ParametresRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class PageController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PageRepository $pageRepository,
        private readonly ParametresRepository $parametresRepository
    ) {}

    #[Route('/page-list', name: 'page.list')]
    public function listPage(SessionInterface $session): Response
    {
        $session->set('menu', 'pages');
        $session->set('sub-menu', 'list-page');

        return $this->render('admin/page/list-page.html.twig', [
            'pages' => $this->pageRepository->findAll()
        ]);
    }

    #[Route('/page-new', name: 'page.new')]
    public function newPage(
        Request $request, 
        SessionInterface $session, 
        FileUploader $fileUploader, 
        ParametresRepository $parametresRepository, 
        SluggerInterface $slugger
    ): Response
    {
        $session->set('menu', 'pages');
        $session->set('sub-menu', 'add-page');

        $idPage = $request->query->get("id");
        $page = $idPage ? $this->pageRepository->find($idPage) : new Page();
        
        $supMenu = $this->parametresRepository->findByType('sup-menu');
        $mainMenu = $this->parametresRepository->findByType('main-menu');

        if ($request->isMethod('POST')) {
            $token = $request->request->get("token");
            
            if (!$this->isCsrfTokenValid('upload', $token)) {
                throw $this->createAccessDeniedException('Jeton CSRF invalide.');
            }

            $titre = $request->request->get("titre");
            $rubriques = $request->request->all("rubriques");
            $desc = $request->request->get("desc");

            foreach ($page->getMenus() as $value) $page->removeMenu($value);
            foreach ($rubriques as $rubriqueId) {
                if ($rubrique = $parametresRepository->find($rubriqueId)) $page->addMenu($rubrique);
            }

            $page->setTitre($titre)
                ->setSlug($slugger->slug($titre)->lower()) // Utilisation du service natif
                ->setDescription($desc);

            // Gestion automatique des timestamps si la méthode existe
            if (method_exists($page, 'updatedTimestamps')) {
                $page->updatedTimestamps();
            }

            // Gestion de l'utilisateur (Symfony 7+)
            // $page->updatedUserstamps($this->getUser());

            $file = $request->files->get('couverture');
            if ($file) {
                $image = new Photos();
                $fileName = $fileUploader->upload($file);
                $image->setSource($fileName)->setAlt($titre);
                $this->em->persist($image);
                $page->setCouverture($image);
            }

            $this->em->persist($page);
            $this->em->flush();

            $this->addFlash('success', 'Page enregistrée avec succès.');
            return $this->redirectToRoute('page.list');
        }

        return $this->render('admin/page/new-page.html.twig', [
            'supMenu' => $supMenu,
            'mainMenu' => $mainMenu,
            'page' => $page
        ]);
    }

    #[Route('/page-delete-{id}', name: 'page.delete', methods: ['DELETE', 'POST'])]
    public function deletePage(Page $page, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $this->em->remove($page);
            $this->em->flush();
            $this->addFlash('success', 'Page supprimée.');
        }

        return $this->redirectToRoute('page.list');
    }
}