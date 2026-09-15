<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/media')]
final class MediaController extends AbstractController
{
    #[Route(name: 'app_media_index', methods: ['GET'])]
    public function index(MediaRepository $mediaRepository): Response
    {
        $admin = $this->getUser();
        return $this->render('media/index.html.twig', [
            'media' => $mediaRepository->findAll(),
            'admin' => $admin,
        ]);
    }

    // #[Route('/new', name: 'app_media_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $medium = new Media();
    //     $form = $this->createForm(MediaType::class, $medium);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($medium);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('media/new.html.twig', [
    //         'medium' => $medium,
    //         'form' => $form,
    //     ]);
    // }
    #[Route('/new', name: 'app_media_new', methods: ['GET', 'POST'])]
        public function new(
            Request $request,
            EntityManagerInterface $entityManager,
            SluggerInterface $slugger
        ): Response
        {
            $medium = new Media();
            $form = $this->createForm(MediaType::class, $medium);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $imageFile = $form->get('imageFile')->getData();

                if ($imageFile) {

                    // ✅ Vérifier si fichier valide
                    if (!$imageFile->isValid()) {
                        $this->addFlash('error', 'Erreur lors de l\'upload du fichier.');
                        return $this->redirectToRoute('app_media_new');
                    }

                    // Nom sécurisé
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Impossible d\'enregistrer le fichier.');
                        return $this->redirectToRoute('app_media_new');
                    }

                    // Enregistrer en base
                    $medium->setNomdufichier($newFilename);
                    $medium->setTypedufichier($imageFile->getClientMimeType());
                    $medium->setChemindufichier('/uploads/' . $newFilename);
                }

                $medium->setUploadat(new \DateTime());

                $entityManager->persist($medium);
                $entityManager->flush();

                $this->addFlash('success', 'Image uploadée avec succès !');

                return $this->redirectToRoute('app_media_index');
            }

            return $this->render('media/new.html.twig', [
                'form' => $form->createView(),
            ]);
        }



    #[Route('/{id}', name: 'app_media_show', methods: ['GET'])]
    public function show(Media $medium): Response
    {
        return $this->render('media/show.html.twig', [
            'medium' => $medium,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_media_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Media $medium, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MediaType::class, $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media/edit.html.twig', [
            'medium' => $medium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_media_delete', methods: ['POST'])]
    public function delete(Request $request, Media $medium, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$medium->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($medium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_media_index', [], Response::HTTP_SEE_OTHER);
    }
}
