<?php

namespace App\Controller\visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ParametreRepository;
use App\Repository\ActualiteRepository;
use App\Repository\EvenementRepository;


final class InterfacevisiteurController extends AbstractController
{
    #[Route('/interfacevisiteur', name: 'app_interfacevisiteur', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ParametreRepository $parametreRepository, ActualiteRepository $actualiteRepository, EvenementRepository $evenementRepository): Response
    {   

        $actualitesPublier = $actualiteRepository->AfficherQuatreDernieresActualites();
        $evenements = $evenementRepository->AfficherTroisDerniersEvenements();
        $contact = new Contact();
        $contact->setCreerle(new \DateTime());
        $contact->setDejalu(false);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('app_interfacevisiteur', [], Response::HTTP_SEE_OTHER);
        }

        //recuperer les donnees du parametre
        $parametres = $parametreRepository->findAll();

        return $this->render('interfacevisiteur/index.html.twig', [
            'contact' => $contact,
            'form' => $form->createView(),
            'parametres' => $parametres,
            'actualitesPublier' => $actualitesPublier,
            'evenements' => $evenements,
        ]);
    }

    #[Route('/api/contact/submit', name: 'api_contact_submit', methods: ['POST'])]
    public function submitContact(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Parse JSON request
            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse(['success' => false, 'error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
            }

            // Validate required fields
            $required = ['nom', 'email', 'titre', 'description'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return new JsonResponse(['success' => false, 'error' => "Le champ '$field' est requis"], Response::HTTP_BAD_REQUEST);
                }
            }

            // Create and persist contact
            $contact = new Contact();
            $contact->setNom($data['nom']);
            $contact->setEmail($data['email']);
            $contact->setTitre($data['titre']);
            $contact->setDescription($data['description']);
            $contact->setCreerle(new \DateTime());
            $contact->setDejalu(false);

            $entityManager->persist($contact);
            $entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Votre message a bien été envoyé !'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/interfacevisiteur/histoiredurova', name: 'app_histoire_du_rova')]
    public function histoire(ParametreRepository $parametreRepository): Response
    {
        //recuperer les donnees du parametre
        $parametres = $parametreRepository->findAll();
        return $this->render('interfacevisiteur/histoiredurova.html.twig', [
            'parametres' => $parametres
        ]);
    }
}