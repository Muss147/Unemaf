<?php

namespace App\Controller\Admin;

use App\Entity\Info;
use App\Repository\InfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class InfoController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly InfoRepository $infoRepository
    ) {}

    #[Route('/info', name: 'info.list')]
    public function infoAction(SessionInterface $session): Response
    {
        $session->set('menu', 'params');
        $session->set('sub-menu', 'info');

        return $this->render('admin/info/list-info.html.twig', [
            'infos' => $this->infoRepository->findAll()
        ]);
    }

    #[Route('/info-new', name: 'info.new')]
    public function newInfo(Request $request, SessionInterface $session): Response
    {
        $session->set('menu', 'params');
        $session->set('sub-menu', 'info');

        if ($request->isMethod('POST')) {
            $textes = $request->request->all('texte'); // Récupère le tableau d'inputs
            $liens = $request->request->all('lien');

            foreach ($textes as $i => $texteValue) {
                $lienValue = $liens[$i] ?? null;

                if (empty($texteValue) || empty($lienValue)) {
                    $this->addFlash('error', "Veuillez renseigner tous les champs de la section N°" . ($i + 1));
                    return $this->redirectToRoute('info.new');
                }

                $info = new Info();
                $info->setTexte($texteValue)
                     ->setLien($lienValue);
                
                if (method_exists($info, 'updatedTimestamps')) {
                    $info->updatedTimestamps();
                }

                $this->em->persist($info);
            }

            $this->em->flush(); // Un seul flush pour toutes les entités
            $this->addFlash('success', 'Informations ajoutées avec succès');
            
            return $this->redirectToRoute('info.list');
        }

        return $this->render('admin/info/add-info.html.twig');
    }

    #[Route('/info-edit', name: 'info.edit', methods: ['POST'])]
    public function editInfo(Request $request): RedirectResponse
    {
        $id = $request->request->get("id");
        $info = $this->infoRepository->find($id);

        if ($info) {
            $info->setTexte($request->request->get("texte"))
                 ->setLien($request->request->get("lien"));

            if (method_exists($info, 'updatedTimestamps')) {
                $info->updatedTimestamps();
            }

            $this->em->flush();
            $this->addFlash('success', 'Information mise à jour');
        } else {
            $this->addFlash('error', 'Information introuvable');
        }
        
        return $this->redirectToRoute('info.list');
    }

    #[Route('/info-delete-{id}', name: 'info.delete', methods: ['DELETE', 'POST'])]
    public function deleteInfo(Info $info, Request $request): RedirectResponse
    {
        // Vérification CSRF standard Symfony
        if ($this->isCsrfTokenValid('delete' . $info->getId(), $request->request->get('_token'))) {
            $this->em->remove($info);
            $this->em->flush();
            $this->addFlash('success', 'Information supprimée');
        }

        return $this->redirectToRoute('info.list');
    }
}