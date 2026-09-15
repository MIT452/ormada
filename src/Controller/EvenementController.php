<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Media;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(
        Request $request,
        EvenementRepository $evenementRepository
    ): Response
    {
        $search = $request->query->get('search');

        $dateDebut = $request->query->get('date_debut');

        $dateFin = $request->query->get('date_fin');

        $admin = $this->getUser();

        $evenements = $evenementRepository->searchEvenements(
            $search,
            $dateDebut,
            $dateFin
        );

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'admin' => $admin,
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();

            foreach ($images as $imageFile) {

                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('uploads_directory'),
                    $newFilename
                );

                $media = new Media();
                $media->setNomdufichier($newFilename);
                $media->setTypedufichier($imageFile->getClientMimeType());
                $media->setChemindufichier('/uploads/'.$newFilename);
                $media->setUploadat(new \DateTime());

                $media->setEvenement($evenement);

                $entityManager->persist($media);
            }
            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Evenement $evenement,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Récupérer les nouveaux fichiers uploadés
            $images = $form->get('images')->getData();

            if ($images && count($images) > 0) {

                // ✅ Supprimer les anciens médias liés à l'événement
                foreach ($evenement->getMedia() as $ancienMedia) {

                    $ancienFichier = $this->getParameter('kernel.project_dir')
                        . '/public/' . $ancienMedia->getChemindufichier();

                    if (file_exists($ancienFichier)) {
                        unlink($ancienFichier);
                    }

                    $entityManager->remove($ancienMedia);
                }

                // ✅ Créer dossier upload si inexistant
                $dossierUpload = $this->getParameter('kernel.project_dir') . '/public/uploads/medias';

                if (!is_dir($dossierUpload)) {
                    mkdir($dossierUpload, 0777, true);
                }

                // ✅ Enregistrer nouvelles images
                foreach ($images as $image) {

                    $nouveauNom = uniqid() . '.' . $image->guessExtension();
                    $image->move($dossierUpload, $nouveauNom);

                    $media = new Media();
                    $media->setNomdufichier($nouveauNom);
                    $media->setTypedufichier($image->getClientMimeType());
                    $media->setChemindufichier('uploads/medias/' . $nouveauNom);
                    $media->setUploadat(new \DateTime());

                    // liaison avec evenement
                    $media->setEvenement($evenement);

                    $entityManager->persist($media);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute(
                'app_evenement_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->getPayload()->getString('_token'))) {

            foreach ($evenement->getMedia() as $media) {

                $chemin = $this->getParameter('kernel.project_dir')
                    . '/public/' . $media->getChemindufichier();

                if (file_exists($chemin)) {
                    unlink($chemin);
                }
            }

            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
}
