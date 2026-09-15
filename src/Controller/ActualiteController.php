<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/actualite')]
final class ActualiteController extends AbstractController
{
    

    #[Route(name: 'app_actualite_index', methods: ['GET'])]
    public function index(
        Request $request,
        ActualiteRepository $actualiteRepository
    ): Response
    {
        $search = $request->query->get('search');
        $publier = $request->query->get('publier');
        $admin = $this->getUser();

        $actualites = $actualiteRepository->rechercheActualite(
            $search,
            $publier
        );

        return $this->render('actualite/index.html.twig', [
            'actualites' => $actualites,
            'admin' => $admin,
        ]);
    }


   #[Route('/new', name: 'app_actualite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $actualite = new Actualite();

        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // récupérer plusieurs images
            $images = $form->get('images')->getData();

            foreach ($images as $image) {

                $newFilename = uniqid().'.'.$image->guessExtension();

                $image->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $media = new Media();
                $media->setNomdufichier($newFilename);
                $media->setTypedufichier($image->getClientMimeType());
                $media->setChemindufichier('/uploads/'.$newFilename);
                $media->setUploadat(new \DateTime());

                // liaison avec actualité
                $media->setActualite($actualite);

                $entityManager->persist($media);
            }

            $entityManager->persist($actualite);
            $entityManager->flush();

            return $this->redirectToRoute('app_actualite_index');
        }

        return $this->render('actualite/new.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_actualite_show', methods: ['GET'])]
    public function show(Actualite $actualite): Response
    {
        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_actualite_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Actualite $actualite, 
        EntityManagerInterface $entityManager,
        string $uploadDir = '%kernel.project_dir%/public/uploads/medias'
    ): Response {
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Récupérer les nouveaux fichiers uploadés
            $images = $form->get('images')->getData();

            if ($images && count($images) > 0) {

                // ✅ Supprimer les anciens médias liés
                foreach ($actualite->getMedia() as $ancienMedia) {
                    // Supprimer le fichier physique si il existe
                    $ancienFichier = $this->getParameter('kernel.project_dir').'/public/'.$ancienMedia->getChemindufichier();
                    if (file_exists($ancienFichier)) {
                        unlink($ancienFichier);
                    }
                    $entityManager->remove($ancienMedia);
                }

                // ✅ Sauvegarder les nouveaux fichiers
                $dossierUpload = $this->getParameter('kernel.project_dir').'/public/uploads/medias';
                if (!is_dir($dossierUpload)) {
                    mkdir($dossierUpload, 0777, true);
                }

                foreach ($images as $image) {
                    $nouveauNom = uniqid().'.'.$image->guessExtension();
                    $image->move($dossierUpload, $nouveauNom);

                    $media = new Media();
                    $media->setNomdufichier($nouveauNom);
                    $media->setTypedufichier($image->getClientMimeType());
                    $media->setChemindufichier('uploads/medias/'.$nouveauNom);
                    $media->setUploadat(new \DateTime());
                    $media->setActualite($actualite);

                    $entityManager->persist($media);
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actualite/edit.html.twig', [
            'actualite' => $actualite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_actualite_delete', methods: ['POST'])]
    public function delete(Request $request, Actualite $actualite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actualite->getId(), $request->getPayload()->getString('_token'))) {
            
            foreach ($actualite->getMedia() as $media) {

                $chemin = $this->getParameter('kernel.project_dir')
                    . '/public/' . $media->getChemindufichier();

                if (file_exists($chemin)) {
                    unlink($chemin);
                }
        }
        
            $entityManager->remove($actualite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_actualite_index', [], Response::HTTP_SEE_OTHER);
    }
}
