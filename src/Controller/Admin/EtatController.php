<?php

namespace App\Controller\Admin;

use App\Entity\Etat;
use App\Form\EtatType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/etats')]
#[IsGranted('ROLE_ADMIN')]
class EtatController extends AbstractController
{
    #[Route('', name: 'app_admin_etat_index', methods: ['GET'])]
    public function index(EtatRepository $repository): Response
    {
        return $this->render('admin/etat/index.html.twig', ['etats' => $repository->findBy([], ['nom' => 'ASC'])]);
    }

    #[Route('/nouvel', name: 'app_admin_etat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etat = new Etat();
        $form = $this->createForm(EtatType::class, $etat)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($etat);
            $entityManager->flush();
            $this->addFlash('success', "L'état a été créé.");
            return $this->redirectToRoute('app_admin_etat_index');
        }
        return $this->render('admin/etat/form.html.twig', ['form' => $form, 'title' => 'Nouvel état']);
    }

    #[Route('/{id}/modifier', name: 'app_admin_etat_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Etat $etat, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtatType::class, $etat)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', "L'état a été modifié.");
            return $this->redirectToRoute('app_admin_etat_index');
        }
        return $this->render('admin/etat/form.html.twig', ['form' => $form, 'title' => "Modifier l'état"]);
    }
}
