<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ActualiteRepository;
use App\Repository\EvenementRepository;
use App\Repository\ParametreRepository;

// #[Route('/page/accueil')]

final class PageAccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_page_accueil')]
    public function index(ActualiteRepository $actualiteRepository, EvenementRepository $evenementRepository, ParametreRepository $parametreRepository): Response
    {   

         $actualites = $actualiteRepository->AfficherQuatreDernieresActualites();
         $evenements = $evenementRepository->AfficherQuatreDerniersEvenements();
         $parametres = $parametreRepository->findAll();
         $admin = $this->getUser();
       
        return $this->render('page_accueil/index.html.twig', [
            'controller_name' => 'PageAccueilController',
            'actualites' => $actualites,
            'evenements' => $evenements,
            'parametres' => $parametres,
            'admin' => $admin,
        ]);
    }
}
