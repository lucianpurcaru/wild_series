<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgramController extends AbstractController
{
    #[Route('/program', name: 'program_')]
    public function index(): Response
    {
        return $this->render('program/index.html.twig', [
            'website' => 'Wild Series',
        ]);
    }

    #[Route('program/{id}', requirements: ['id' => '\d+'], methods: ['GET'], name: 'show')]
    public function show(int $id): Response
    {
        return $this->render('program/show.html.twig', ['id' => $id]);
    }
}
