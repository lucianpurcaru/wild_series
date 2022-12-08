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
use App\Service\ProgramDuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
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
    public function new(Request $request, ProgramRepository $programRepository, SluggerInterface $slugger, MailerInterface $mailer): Response
    {
    $program = new Program();
    $form = $this->createForm(ProgramType::class, $program);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $slug = $slugger->slug($program->getTitle());
        $program->setSlug($slug);
        $programRepository->save($program, true);     
        
        $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('their_email@example.com')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('program/newProgramEmail.html.twig', ['program' => $program]));

        $mailer->send($email);

        // Once the form is submitted, valid and the data inserted in database, you can define the success flash message
        $this->addFlash('success', 'The new program has been created');

        return $this->redirectToRoute('program_index');
    }

        return $this->renderForm('program/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', methods: ['GET'], name: 'show')]
    public function show(Program $program, SeasonRepository $seasonRepository, ProgramDuration $programDuration): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }

        $seasons = $seasonRepository->findBy(['program' => $program], ['id' => 'DESC']);

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'programDuration' => $programDuration->calculate($program, 'minutes'),
        ]);
    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $programRepository->save($program, true);

            $this->addFlash('success', 'The program has been edited successfully');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$program->getId(), $request->request->get('_token'))) {
            $programRepository->remove($program, true);

            $this->addFlash('danger', 'The program has been deleted successfully');
        }

        return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{programSlug}/season/{seasonId}', methods: ['GET'], name: 'season_show')]
    #[ParamConverter('program', options: ['mapping' => ['programSlug' => 'slug']])]
    #[ParamConverter('season', options: ['mapping' => ['seasonId' => 'id']])]
    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with this id found for this program.'
            );
        }

        $episodes = $episodeRepository->findBy(['season' => $season]);

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/{programSlug}/season/{seasonId}/episode/{episodeSlug}', methods: ['GET'],  requirements: ['seasonId' => '\d+'], name: 'episode_show')]
    #[ParamConverter('program', options: ['mapping' => ['programSlug' => 'slug']])]
    #[ParamConverter('season', options: ['mapping' => ['seasonId' => 'id']])]
    #[ParamConverter('episode', options: ['mapping' => ['episodeSlug' => 'slug']])]
    public function showEpisode(Program $program, Season $season, Episode $episode): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with this id found in program\'s table.'
            );
        }

        if (!$season) {
            throw $this->createNotFoundException(
                'No season with this id found for this program.'
            );
        }

        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode with this id found for this program.'
            );
        }

        return $this->render('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }
}