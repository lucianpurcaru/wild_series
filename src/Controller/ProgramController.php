<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use App\Repository\EpisodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Repository\ProgramRepository;
use App\Entity\Program;
use App\Repository\SeasonRepository;
use App\Entity\Season;
use App\Entity\Episode;
use App\Form\ProgramType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render(
            'program/index.html.twig',
            ['programs' => $programs]
        );
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);
            $this->addFlash('success', 'La série a été ajoutée.');

            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', methods: ['GET'],  requirements: ['id' => '\d+'], name: 'show')]
    public function show(Program $program, SeasonRepository $seasonRepository, ProgramDuration $programDuration): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de saison trouvée.'
            );
        }

        $seasons = $seasonRepository->findBy(['program' => $program], ['id' => 'DESC']);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'programDuration' => $programDuration->calculate($program, 'minutes'),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);

            $this->addFlash('success', 'La série a été modifiée.');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);

            $this->addFlash('danger', 'La série a été supprimée.');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(
        '/{programId}/season/{seasonId}',
        methods: ['GET'],
        requirements: ['programId' => '\d+', 'seasonId' => '\d+'],
        name: 'season_show'
    )]
    #[ParamConverter('program', options: ['mapping' => ['programId' => 'id']])]
    #[ParamConverter('season', options: ['mapping' => ['seasonId' => 'id']])]
    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de série trouvée.'
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                'Pas de saison trouvée dans la série.'
            );
        }

        $episodes = $episodeRepository->findBy(['season' => $season]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    #[Route(
        '/{programId}/season/{seasonId}/episode/{episodeId}',
        methods: ['GET'],
        requirements: ['programId' => '\d+', 'seasonId' => '\d+'],
        name: 'episode_show'
    )]
    #[ParamConverter('program', options: ['mapping' => ['programId' => 'id']])]
    #[ParamConverter('season', options: ['mapping' => ['seasonId' => 'id']])]
    #[ParamConverter('episode', options: ['mapping' => ['episodeId' => 'id']])]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'Pas de saison trouvée.'
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                'Pas de saison trouvée.'
            );
        }

        if (!$episode) {
            throw $this->createNotFoundException(
                'Pas d\'épisode trouvé dans la saison.'
            );
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }
}
