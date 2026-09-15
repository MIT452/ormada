<?php

namespace App\Controller\visiteurs;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementRepository;

final class VoirPlusEvenementsController extends AbstractController
{
   #[Route('/visiteurs/evenements', name: 'app_voir_plus_evenements')]
    public function index(Request $request, EvenementRepository $repo): Response
    {
        $filter = $request->query->get('filter', 'all');

        $evenements = $filter === 'all'
            ? $repo->findBy([], ['datedudebut' => 'ASC'])
            : $repo->findFiltered($filter);

        return $this->render('interfacevisiteur/VoirPlusEvenements.html.twig', [
            'evenements' => $evenements,
            'filter' => $filter
        ]);
    }
}
