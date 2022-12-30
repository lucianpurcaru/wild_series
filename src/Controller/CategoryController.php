<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{

    #[Route('/list', name: 'app_category_index', methods: ['GET'])]
    public function app_index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/app_index.html.twig', [
        ]);
    }


    #[Route('/', name: 'index')]
    public function index(Request $request, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $formSearch = $this->createForm(SearchType::class);
        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $search = $formSearch->getData();
            $programs = $programRepository->findLikeName($search);

            return $this->render('program/index.html.twig', [
                'programs' => $programs,

            ]);
            
        }
    
    return $this->renderForm('category/index.html.twig', [
        'categories' => $categoryRepository->findAll(),
        'formSearch' => $formSearch,
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_category_new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            $this->addFlash('success', 'La catégorie est ajoutée.');

            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }
    


    #[Route('/show/{id}', name: 'app_category_show', methods: ['GET'])]
    public function app_show(Category $category, CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/app_show.html.twig', [
            'category' => $category,
        ]);
    }




    #[Route('/{categoryName}', requirements: ['categoryName' => '^[a-zA-Z\-_]+$'], methods: ['GET'], name: 'show')]
    public function show(string $categoryName, ProgramRepository $programRepository, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        $programs = $programRepository->findBy(['category' => $category], ['title' => 'ASC']);

        if (!$programs)
            throw $this->createNotFoundException('Aucun programme pour la catégorie.');
        
        return $this->render('category/show.html.twig', [
            'programs' => $programs, 
            'category' => $category, 
        ]);
    }
    
    
    
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);
            $this->addFlash('success', 'La catégorie est modifiée.');

            return $this->redirectToRoute('category_app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
            $this->addFlash('danger', 'La catégorie est supprimée.');
        }

        return $this->redirectToRoute('category_app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}