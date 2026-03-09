<?php

namespace App\Controller\Admin; // Dossier simplifié

use App\Entity\Activity;
use App\Entity\Photos;
use App\Repository\ActivityRepository;
use App\Repository\ParametresRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route; // Nouveau namespace pour les Attributes
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin')]
class ActivityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader
    ) {}

    #[Route('/activity-list', name: 'activity.list')]
    public function listActivity(ActivityRepository $activityRepository, SessionInterface $session): Response
    {
        $session->set('menu', 'articles');
        $session->set('sub-menu', 'list-article');

        return $this->render('admin/activity/list-activity.html.twig', [
            'activities' => $activityRepository->findAll()
        ]);
    }

    #[Route('/activity-new/{id?}', name: 'activity.new')]
    public function newActivity(
        Request $request, 
        SessionInterface $session, 
        ?Activity $activity, 
        ActivityRepository $activityRepository, 
        ParametresRepository $parametreRepository,
        SluggerInterface $slugger
    ): Response
    {
        $session->set('menu', 'articles');
        $session->set('sub-menu', 'add-article');

        $activity ??= new Activity();        
        $types = $parametreRepository->findByType('types activite');

        if ($request->isMethod('POST')) {
            $token = $request->request->get("token");
            $id = $request->request->get("id");
            $activity = $activityRepository->find($id) ?? new Activity();
            
            if (!$this->isCsrfTokenValid('upload', $token)) {
                throw $this->createAccessDeniedException('Opération non autorisée (CSRF invalide)');
            }

            $titre = $request->request->get("titre");
            $type = $parametreRepository->find($request->request->get("type"));

            $activity->setTitre($titre)
                ->setSlug($slugger->slug($titre)->lower()) // Utilisation du Slugger natif
                ->setDateActivity(new \DateTime($request->request->get("date")))
                ->setType($type)
                ->setDescription($request->request->get("desc"))
                // ->updatedTimestamps() // Assure-toi que cette méthode existe toujours dans ton Entité
            ;

            // Gestion de l'utilisateur (Symfony 7+)
            // $activity->updatedUserstamps($this->getUser());

            $file = $request->files->get('couverture');
            if ($file) {
                $image = new Photos();
                $data = $this->fileUploader->upload($file);
                $image->setSource($data['filename'])->setType($data['type'])->setAlt($titre);
                $this->em->persist($image);
                $activity->setCouverture($image);
            }

            $this->em->persist($activity);
            $this->em->flush();

            return $this->redirectToRoute('activity.list');
        }

        return $this->render('admin/activity/new-activity.html.twig', [
            'types' => $types,
            'activity' => $activity
        ]);
    }

    #[Route('/activity-delete-{id}', name: 'activity.delete', methods: ['DELETE', 'POST'])]
    public function deleteActivity(Activity $activity, Request $request): RedirectResponse
    {
        // On vérifie le token CSRF envoyé via un champ caché (souvent nommé _token)
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {
            $this->em->remove($activity);
            $this->em->flush();
        }

        return $this->redirectToRoute('activity.list');
    }
}