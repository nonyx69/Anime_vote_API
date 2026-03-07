<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Choix;
use App\Entity\Questions;
use App\Entity\Sondages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AdminController extends AbstractController
{

    #[Route('/api/admin/sondage', name: 'admin_sondage_create', methods: ['POST'])]
    // #[IsGranted('ROLE_ADMIN')] // A modifier marche en commentaire mais pas dans le code
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $sondage = new Sondages();
        $sondage->setName($data['name']);
        $sondage->setIsActive($data['visible'] ?? false);

        $question = new Questions();
        $question->setLabel($data['question_label']);
        $question->setMultiple(false);
        $question->setSondage($sondage);

        foreach ($data['choices'] as $choiceLabel) {
            $choix = new Choix();
            $choix->setLabel($choiceLabel);
            $choix->setQuestions($question);
            $em->persist($choix);
        }

        $em->persist($sondage);
        $em->persist($question);
        $em->flush();

        return $this->json([
            'status' => 'Sondage QCM créé !'
        ], 201);
    }
}
