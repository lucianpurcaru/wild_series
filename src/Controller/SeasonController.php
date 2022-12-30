<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/season')]
class SeasonController extends AbstractController
{
    #[Route('/', name: 'app_season_index', methods: ['GET'])]
    public function index(SeasonRepository $seasonRepository, CategoryRepository $categoryRepository): Response
    {

        return $this->render('season/index.html.twig', [
            'seasons' => $seasonRepository->findAll(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_season_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SeasonRepository $seasonRepository, CategoryRepository $categoryRepository): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seasonRepository->save($season, true);
            $this->addFlash('success', 'La saison est ajoutée.');

            return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('season/new.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }




    #[Route('/{id}', name: 'app_season_show', methods: ['GET'])]
    public function show(Season $season, CategoryRepository $categoryRepository): Response
    {

        return $this->render('season/show.html.twig', [
            'season' => $season,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_season_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Season $season, SeasonRepository $seasonRepository, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seasonRepository->save($season, true);
            $this->addFlash('success', 'La saison est modifiée.');

            return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('season/edit.html.twig', [
            'season' => $season,
            'form' => $form,
        ]);
    }



    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_season_delete', methods: ['POST'])]
    public function delete(Request $request, Season $season, SeasonRepository $seasonRepository, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $season->getId(), $request->request->get('_token'))) {
            $seasonRepository->remove($season, true);
            $this->addFlash('alert', 'La saison est supprimée.');
        }

        return $this->redirectToRoute('app_season_index', [], Response::HTTP_SEE_OTHER);
    }
}