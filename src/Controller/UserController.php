<?php

namespace App\Controller;

use App\Repository\SondagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/api/sondages', name: 'app_sondages_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function listActive(SondagesRepository $repo): Response
    {
        $sondages = $repo->findOneBy(['isActive' => true]);

        return $this->json($sondages, 200, [], ['groups' => 'sondage:read']);
    }
}
