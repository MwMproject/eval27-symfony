<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\PublicTicketType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $etatNouveau = $etatRepository->findOneBy(['nom' => 'Nouveau']);
        if ($etatNouveau === null) {
            throw new \LogicException("L'état Nouveau doit être chargé par les fixtures.");
        }
        $ticket->setEtat($etatNouveau);

        $form = $this->createForm(PublicTicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            $this->addFlash('success', 'Votre ticket a bien été transmis.');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'ticketForm' => $form,
        ]);
    }
}
