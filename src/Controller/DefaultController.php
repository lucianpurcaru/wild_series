<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'App_index')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        return $this->render('index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}