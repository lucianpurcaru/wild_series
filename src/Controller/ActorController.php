<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, ActorRepository $actorRepository ): Response
    {
        return $this->render('actor/index.html.twig', [
            'actors' => $actorRepository->findAll(),
        ]);   
    }

    #[Route('/index', name: 'app_actor_index', methods: ['GET'])]
    public function app_index(ActorRepository $actorRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('actor/app_index.html.twig', [
            'actors' => $actorRepository->findAll(),
        ]);
    }

    #[Route('/show/{id}', name: 'id', methods: ['GET'])]
    public function show(Actor $actor, CategoryRepository $categoryRepository): Response
    {
        return $this->render('actor/show.html.twig', [
            'actor' => $actor,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/{id}', name: 'app_actor_delete', methods: ['POST'])]
    public function delete(Request $request, Actor $actor, ActorRepository $actorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$actor->getId(), $request->request->get('_token'))) {
            $actorRepository->remove($actor, true);
        }

        return $this->redirectToRoute('actor_app_actor_index', [], Response::HTTP_SEE_OTHER);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_actor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActorRepository $actorRepository, CategoryRepository $categoryRepository): Response
    {
        $actor = new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actorRepository->save($actor, true);

            return $this->redirectToRoute('actor_app_actor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actor/new.html.twig', [
            'actor' => $actor,
            'form' => $form, 
        ]);
    }

    #[Route('/{id}', name: 'app_actor_show', methods: ['GET'])]
    public function app_show(Actor $actor, CategoryRepository $categoryRepository): Response
    {
        return $this->render('actor/app_show.html.twig', [
            'actor' => $actor,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_actor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Actor $actor, ActorRepository $actorRepository, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actorRepository->save($actor, true);

            return $this->redirectToRoute('actor_app_actor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form,
        ]);
    }
}