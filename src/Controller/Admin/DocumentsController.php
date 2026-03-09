<?php

namespace App\Controller\Admin;

use App\Entity\Documents;
use App\Entity\Parametres;
use App\Entity\Photos; // Assurez-vous que cette entité existe dans App\Entity
use App\Repository\DocumentsRepository;
use App\Repository\ParametresRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DocumentsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DocumentsRepository $documentsRepository,
        private readonly ParametresRepository $parametresRepository
    ) {}

    #[Route('/document-list', name: 'document.list')]
    public function listDocument(SessionInterface $session): Response
    {
        $session->set('menu', 'docs');
        $session->set('sub-menu', 'list-doc');

        return $this->render('admin/document/list-document.html.twig', [
            'documents' => $this->documentsRepository->findAll()
        ]);
    }

    #[Route('/edit/document-{id}', name: 'document.edit')]
    public function editDocument(Documents $document): Response
    {
        // Grâce au ParamConverter automatique, Symfony trouve l'objet Documents via l'ID
        return $this->render('admin/document/new-document.html.twig', [
            'document' => $document // Suppression du "$" dans la clé
        ]);
    }

    #[Route('/document-new', name: 'document.new')]
    public function newDocument(Request $request, FileUploader $fileUploader, SessionInterface $session): Response
    {
        $session->set('menu', 'docs');
        $session->set('sub-menu', 'add-doc');

        $id = $request->query->get("id") ?? $request->request->get("id");
        $document = $id ? $this->documentsRepository->find($id) : new Documents();
        
        $types = $this->parametresRepository->findByType('document');

        if ($request->isMethod('POST')) {
            $token = $request->request->get("token");
            $titre = $request->request->get("titre");
            $typeId = $request->request->get("type");
            $type = $this->parametresRepository->find($typeId);

            if (!$this->isCsrfTokenValid('upload', $token)) {
                throw $this->createAccessDeniedException('Opération non autorisée (CSRF invalide)');
            }

            if (empty($titre) || !$type) {
                $this->addFlash('error', 'Veuillez remplir les champs obligatoires');
            } else {
                $document->setTitre($titre)
                    ->setType($type);
                
                // Si tes méthodes de timestamps sont gérées via des traits ou LifecycleCallbacks
                if (method_exists($document, 'updatedTimestamps')) {
                    $document->updatedTimestamps();
                }

                $file = $request->files->get('fichier');
                if ($file) {
                    $photo = new Photos();
                    $data = $fileUploader->upload($file);
                    $photo->setSource($data['filename'])->setType($data['type'])->setAlt($titre);
                    $this->em->persist($photo);
                    $document->setFichier($photo);
                }

                $this->em->persist($document);
                $this->em->flush();

                $this->addFlash('success', 'Document enregistré avec succès');
                return $this->redirectToRoute('document.list');
            }
        }

        return $this->render('admin/document/add-document.html.twig', [
            'types' => $types,
            'document' => $document
        ]);
    }

    #[Route('/document-delete-{id}', name: 'document.delete', methods: ['DELETE', 'POST'])]
    public function deleteDocument(Documents $document, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->request->get('_token'))) {
            $this->em->remove($document);
            $this->em->flush();
            $this->addFlash('success', 'Document supprimé');
        }

        return $this->redirectToRoute('document.list');
    }
}