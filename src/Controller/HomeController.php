<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Parametres;
use App\Repository\ActivityRepository;
use App\Repository\DocumentsRepository;
use App\Repository\InfoRepository;
use App\Repository\PageRepository;
use App\Repository\ParametresRepository;
use App\Repository\PhotosRepository;
use App\Repository\SliderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    /**
     * Note: En Symfony 8, on évite d'injecter ManagerRegistry partout. 
     * On injecte directement les Repositories spécifiques dans les méthodes.
     */

    public function header(
        ParametresRepository $parametreRepository,
        InfoRepository $infoRepository
    ): Response {
        return $this->render('pages/_header.html.twig', [
            'mainMenu' => $parametreRepository->findBy(['type' => 'main-menu']),
            'supMenu' => $parametreRepository->findBy(['type' => 'sup-menu']),
            'infos' => $infoRepository->findAll()
        ]);
    }

    public function secondarySidebar(DocumentsRepository $documentsRepository, ParametresRepository $parametreRepository): Response
    {
        // On suppose que findByType est une méthode personnalisée dans ton repository
        // Sinon, utilise findBy(['type' => 3])
        return $this->render('pages/_secondarySidebar.html.twig', [
            'noteCirculaires' => $documentsRepository->findByType($parametreRepository->findOneByLibelle('Notes circulaires')),
            'docUtiles' => $documentsRepository->findByType($parametreRepository->findOneByLibelle('Documents utiles')),
            'catalogues' => $documentsRepository->findByType($parametreRepository->findOneByLibelle('Catalogue')),
        ]);
    }

    #[Route('/', name: 'home')]
    public function index(
        ActivityRepository $activityRepository,
        SliderRepository $sliderRepository,
        ParametresRepository $parametreRepository,
        PhotosRepository $photosRepository
    ): Response {
        return $this->render('pages/home.html.twig', [
            'activites' => $activityRepository->findBy(['active' => true]),
            'sliders' => $sliderRepository->findAll(),
            'photos' => $photosRepository->findAll(),
            'docTypes' => $parametreRepository->findBy(['type' => 'activite']),
        ]);
    }

    #[Route('/page/{slug}', name: 'pages')]
    public function showpage(PageRepository $pageRepository, $slug): Response
    {
        $page = $pageRepository->findOneBySlug($slug);
        if (!$page) throw $this->createNotFoundException('Page non trouvée.');
        
        return $this->render('pages/pages.html.twig', [
            'page' => $page,
            'menus' => $page->getMenus(),
        ]);
    }

    #[Route('/articles/{type}/{slug}', name: 'activite')]
    public function showpage3(
        string $type, 
        string $slug, 
        ActivityRepository $activityRepository,
        SliderRepository $sliderRepository
    ): Response {
        $activity = $activityRepository->findOneBy(['slug' => $slug]);
        
        $similaires = $activityRepository->findBy(
            ['type' => $type], 
            ['dateActivity' => 'DESC']
        );

        return $this->render('pages/activite.html.twig', [
            'activity' => $activity,
            'similaires' => $similaires,
            'sliders' => $sliderRepository->findAll(),
        ]);
    }

    #[Route('/articles/{slug}', name: 'list_activite')]
    public function showpage4(
        Parametres $type, 
        ActivityRepository $activityRepository,
        SliderRepository $sliderRepository
    ): Response {
        $activites = $activityRepository->findBy(
            ['type' => $type], 
            ['dateActivity' => 'DESC']
        );

        return $this->render('pages/listActivites.html.twig', [
            'type' => $type,
            'activites' => $activites,
            'sliders' => $sliderRepository->findAll(),
        ]);
    }

    public function footer(
        ActivityRepository $activityRepository,
        DocumentsRepository $documentsRepository
    ): Response {
        return $this->render('pages/_footer.html.twig', [
            'footerActs' => $activityRepository->findBy(['active' => true], ['createdAt' => 'DESC'], 3),
            'footerDocs' => $documentsRepository->findBy([], ['createdAt' => 'DESC'], 4)
        ]);
    }
}