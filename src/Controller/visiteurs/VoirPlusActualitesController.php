<?php

namespace App\Controller\visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ActualiteRepository;
final class VoirPlusActualitesController extends AbstractController
{
    #[Route('/visiteurs/VoirPlusActualites', name: 'app_voir_plus_actualites')]
        public function index(Request $request, ActualiteRepository $repo): Response
        {
            $filter = $request->query->get('filter', 'all');

            $actualites = $repo->findFiltered($filter);

            return $this->render('interfacevisiteur/VoirPlusActualites.html.twig', [
                'actualites' => $actualites,
                'filter' => $filter
            ]);
        }
}