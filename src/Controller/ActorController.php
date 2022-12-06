<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {
        $actors = $actorRepository->findAll();
        return $this->render(
            'actor/index.html.twig',
            ['actors' => $actors]
        );
    }

    #[Route('/{id}', methods: ['GET'],  requirements: ['id' => '\d+'], name: 'show')]
    public function show(Actor $actor, ProgramRepository $programRepository): Response
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'Pas d\'acteur trouvé dans la table.'
            );
        }

        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }
}