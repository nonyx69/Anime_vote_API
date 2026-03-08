<?php

namespace App\Controller;

use App\Repository\SondagesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/api/sondages', name: 'app_sondages_list', methods: ['GET'])]
    public function listActive(Request $request, SondagesRepository $repo, UserRepository $userRepo): Response
    {

        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $userRepo->findOneBy(['token' => $token]);

        if (!$user || !in_array('ROLE_USER', $user->getRoles())) {
            return $this->json([
                'error' => 'acces refuse tu doit etre user'
            ], 403);
        }

        $sondages = $repo->findBy(['isActive' => true]);

        return $this->json($sondages, 200, [], ['groups' => 'sondage:read']);
    }
}
