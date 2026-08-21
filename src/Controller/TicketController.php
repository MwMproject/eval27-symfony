<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketStatusType;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tickets')]
#[IsGranted('ROLE_STAFF')]
class TicketController extends AbstractController
{
    #[Route('', name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $ticketRepository->findBy([], ['dateOuverture' => 'DESC']),
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', ['ticket' => $ticket]);
    }

    #[Route('/{id}/statut', name: 'app_ticket_status', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function status(Ticket $ticket, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketStatusType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($ticket->getEtat()?->getNom() === 'Fermé') {
                $ticket->setDateCloture($ticket->getDateCloture() ?? new \DateTimeImmutable());
            } else {
                $ticket->setDateCloture(null);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le statut du ticket a été mis à jour.');

            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('ticket/status.html.twig', ['ticket' => $ticket, 'form' => $form]);
    }
}
