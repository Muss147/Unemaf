<?php

namespace App\Controller\Admin;

use App\Entity\Slider;
use App\Entity\Photos;
use App\Repository\SliderRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class SliderController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SliderRepository $sliderRepository,
        private FileUploader $fileUploader
    ) {}

    #[Route('/slider-list', name: 'slider.list')]
    public function listSlider(SessionInterface $session): Response
    {
        $session->set('menu', 'params');
        $session->set('sub-menu', 'sliders');

        return $this->render('admin/slider/list-slider.html.twig', [
            'sliders' => $this->sliderRepository->findAll()
        ]);
    }

    #[Route('/slider-new', name: 'slider.new')]
    public function newSlider(Request $request): RedirectResponse
    {
        $id = $request->request->get('id');
        $slider = $id ? $this->sliderRepository->find($id) : new Slider();

        if ($request->isMethod('POST')) {
            $libelle = $request->request->get('libelle');
            $text = $request->request->get('text');
            $lien = $request->request->get('lien');
            
            $slider->setLibelle($libelle)
                ->setText($text)
                ->setLien($lien);
            
            if (method_exists($slider, 'updatedTimestamps')) {
                $slider->updatedTimestamps();
            }

            $file = $request->files->get('img');
            if ($file) {
                // On supprime l'ancien fichier physiquement
                if ($slider->getImage()) $this->fileUploader->delete($slider->getImage()->getSource());

                $image = new Photos();
                $data = $this->fileUploader->upload($file);
                $image->setSource($data['filename'])->setType($data['type'])->setAlt($libelle);
                
                $this->em->persist($image);
                $slider->setImage($image);
            }

            $this->em->persist($slider);
            $this->em->flush();
            
            $this->addFlash('success', 'Slider enregistré avec succès.');
        }
        
        return $this->redirectToRoute('slider.list');
    }

    #[Route('/delete/slider-{id}', name: 'slider.delete', methods: ['POST', 'DELETE'])]
    public function deleteSlider(Slider $slider, Request $request): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$slider->getId(), $request->request->get('_token'))) {
            $this->fileUploader->delete($slider->getImage()->getSource());
            $this->em->remove($slider);
            $this->em->flush();
            $this->addFlash('success', 'Slider supprimé.');
        }

        return $this->redirectToRoute('slider.list');
    }
}