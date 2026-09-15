<?php

namespace App\Controller\visiteurs;

use App\Entity\Actualite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowActualiteController extends AbstractController
{   
    #[Route('/visiteurs/actualite/{id}', name: 'app_showActualite', methods: ['GET'])]
    public function app_showActualite(Actualite $actualite): Response
    {
        return $this->render('interfacevisiteur/ShowActualite.html.twig', [
            'actualiteShow' => $actualite,
        ]);
    }
}