<?php

namespace App\Controller;

use App\Entity\Reponses;
use App\Repository\ChoixRepository;
use App\Repository\QuestionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReponsesController extends AbstractController
{
    #[Route('/api/reponses/ajouter', name: 'app_reponses_add', methods: ['POST', 'OPTIONS'])]
    public function addReponse(Request $request, UserRepository $userRepo, QuestionsRepository $questionsRepo, ChoixRepository $choixRepo, EntityManagerInterface $em): Response
    {
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return $this->json(["status" => "error", "message" => "token manquant"], 401);
        }

        $token = substr($token, 7);
        $user = $userRepo->findOneBy(['token' => $token]);

        if (!$user || !in_array('ROLE_USER', $user->getRole())) {
            return $this->json(['error' => 'acces refuse'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['question_id']) || !isset($data['choix_id'])) {
            return $this->json(['error' => 'donnée vide'], 400);
        }

        $question = $questionsRepo->find($data['question_id']);
        $choix = $choixRepo->find($data['choix_id']);

        if (!$question || !$choix) {
            return $this->json(['error' => 'Question ou chooix non trouvée'], 404);
        }

        $reponse = new Reponses();
        $reponse->setQuestion($question);
        $reponse->setChoix($choix);
        $reponse->setIdUser((string)$user->getId());

        $message = $data['message'] ?? null;
        $reponse->setMessage($message);

        $em->persist($reponse);
        $em->flush();

        return $this->json([
            'message' => 'commentaire ajouter'
        ], 201);
    }
}
