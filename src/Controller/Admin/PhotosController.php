<?php

namespace App\Controller\Admin;

use App\Entity\Photos;
use App\Repository\PhotosRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class PhotosController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PhotosRepository $photosRepository,
        private FileUploader $fileUploader
    ) {}

    #[Route('/photo-list', name: 'photo.list')]
    public function listPhotos(SessionInterface $session): Response
    {
        $session->set('menu', 'params');
        $session->set('sub-menu', 'photos');

        // On suppose que tu as un champ 'type' dans ton entité Photos
        $listPhotos = $this->photosRepository->findBy(['categ' => 'photo']);

        return $this->render('admin/slider/photos.html.twig', [
            'photos' => $listPhotos
        ]);
    }

    #[Route('/photo-new', name: 'photo.new')]
    public function newPhotos(Request $request): RedirectResponse
    {
        $id = $request->query->get('id');
        $photo = $id ? $this->photosRepository->find($id) : new Photos();

        if ($request->isMethod('POST')) {
            $alt = $request->request->get('alt');
            $photo->setAlt($alt);
            
            if (method_exists($photo, 'updatedTimestamps')) {
                $photo->updatedTimestamps();
            }

            $file = $request->files->get('img');
            if ($file) {
                // Le FileUploader doit retourner le nom du fichier stocké
                $data = $this->fileUploader->upload($file);
                $photo->setSource($data['filename'])
                    ->setType($data['type'])
                    ->setCateg('photo')
                    ->setAlt($data['originalName']);
                
                if (empty($photo->getAlt())) {
                    $photo->setAlt($alt);
                }
            }

            $this->em->persist($photo);
            $this->em->flush();

            $this->addFlash('success', 'Photo enregistrée avec succès.');
        }

        return $this->redirectToRoute('photo.list');
    }

    #[Route('/delete/photo-{id}', name: 'photo.delete', methods: ['POST', 'DELETE'])]
    public function deletePhoto(Photos $photo, Request $request): RedirectResponse
    {
        // Vérification CSRF obligatoire pour la sécurité
        if ($this->isCsrfTokenValid('delete' . $photo->getId(), $request->request->get('_token'))) {
            $this->fileUploader->delete($photo->getSource());
            $this->em->remove($photo);
            $this->em->flush();
            $this->addFlash('success', 'Photo supprimée.');
        }

        return $this->redirectToRoute('photo.list');
    }
}