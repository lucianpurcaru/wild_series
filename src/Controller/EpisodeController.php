<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Form\CommentType;
use App\Form\EpisodeType;
use Symfony\Component\Mime\Email;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/episode')]
class EpisodeController extends AbstractController
{
    private SluggerInterface $slugger;
    public const RATES = [
        0 => '😞',
        1 => '😕',
        2 => '🫤',
        3 => '😐',
        4 => '🙂',
        5 => '😃',
    ];



    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }



    #[Route('/', name: 'app_episode_index', methods: ['GET'])]
    public function index(EpisodeRepository $episodeRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }



    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_episode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EpisodeRepository $episodeRepository, MailerInterface $mailer, CategoryRepository $categoryRepository): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($episode->getTitle());
            $episode->setSlug($slug);
            $episodeRepository->save($episode, true);
            $this->addFlash('success', 'L\épisode est ajouté.');

            $mail = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('william.bouet.14@gmail.com')
                ->subject('Un nouvel épisode dans la base')
                ->html($this->renderView('episode/newEpisodeEmail.html.twig', [
                    'episode' => $episode,
                    'category' => $episode->getSeason()->getProgram()->getCategory(),
                ]));
            $mailer->send($mail);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_episode_show', methods: ['GET'])]
    public function show(Episode $episode, CategoryRepository $categoryRepository): Response
    {
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{slug}/edit', name: 'app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode, EpisodeRepository $episodeRepository, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $this->slugger->slug($episode->getTitle());
            $episode->setSlug($slug);
            $episodeRepository->save($episode, true);
            $this->addFlash('success', 'L\'épisode est modifié.');

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form,
        ]);
    }

    #[Route('/detail/{slug}', name: 'episode_show', methods: ['GET', 'POST'])]
    public function episode(
        Episode $episode,
        CategoryRepository $categoryRepository,
        Request $request,
        CommentRepository $commentRepository,
    ): Response {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setCreationDate(new \DateTime('now'));
            $comment->setAuthor($this->getUser());
            $comment->setEpisodeId($episode);
            

            $commentRepository->save($comment, true);

            $this->addFlash('success', 'commentaire enregistré.');

            return $this->redirectToRoute('episode_show', [
                'slug' => $episode->getSlug(),

            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('episode/episode.html.twig', [
            'episode' => $episode,
            'form' => $form,
            'comments' => $commentRepository->findBy(['episode_id' => $episode->getId()], ['creationDate' => 'DESC']),
            'category' => $episode->getSeason()->getProgram()->getCategory(),
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode, EpisodeRepository $episodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $episode->getId(), $request->request->get('_token'))) {
            $episodeRepository->remove($episode, true);
            $this->addFlash('danger', 'L\'épisode est supprimé.');
        }

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}