<?php

namespace App\Controller\visiteurs;

use App\Entity\Evenement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowEvenementController extends AbstractController
{   
  #[Route('/visiteurs/evenement/{id}', name: 'app_showevenement', methods: ['GET'])]
    public function ShowEvenement(Evenement $evenement): Response
    {
        return $this->render('interfacevisiteur/ShowEvenement.html.twig', [
            'evenement' => $evenement,
        ]);
    }
}