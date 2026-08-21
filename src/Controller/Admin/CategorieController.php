<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategorieController extends AbstractController
{
    #[Route('', name: 'app_admin_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $repository): Response
    {
        return $this->render('admin/categorie/index.html.twig', ['categories' => $repository->findBy([], ['nom' => 'ASC'])]);
    }

    #[Route('/nouvelle', name: 'app_admin_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été créée.');
            return $this->redirectToRoute('app_admin_categorie_index');
        }
        return $this->render('admin/categorie/form.html.twig', ['form' => $form, 'title' => 'Nouvelle catégorie']);
    }

    #[Route('/{id}/modifier', name: 'app_admin_categorie_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Categorie $categorie, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie a été modifiée.');
            return $this->redirectToRoute('app_admin_categorie_index');
        }
        return $this->render('admin/categorie/form.html.twig', ['form' => $form, 'title' => 'Modifier la catégorie']);
    }
}
