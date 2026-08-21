<?php

namespace App\Controller\Admin;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\EtatRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/tickets')]
#[IsGranted('ROLE_ADMIN')]
class TicketController extends AbstractController
{
    #[Route('', name: 'app_admin_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $repository): Response
    {
        return $this->render('admin/ticket/index.html.twig', [
            'tickets' => $repository->findBy([], ['dateOuverture' => 'DESC']),
        ]);
    }

    #[Route('/nouveau', name: 'app_admin_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EtatRepository $etatRepository, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $etatNouveau = $etatRepository->findOneBy(['nom' => 'Nouveau']);
        if ($etatNouveau !== null) {
            $ticket->setEtat($etatNouveau);
        }

        $form = $this->createForm(TicketType::class, $ticket)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->synchroniseDateCloture($ticket);
            $entityManager->persist($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Le ticket a été créé.');
            return $this->redirectToRoute('app_admin_ticket_index');
        }
        return $this->render('admin/ticket/form.html.twig', ['form' => $form, 'title' => 'Nouveau ticket']);
    }

    #[Route('/{id}/modifier', name: 'app_admin_ticket_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Ticket $ticket, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->synchroniseDateCloture($ticket);
            $entityManager->flush();
            $this->addFlash('success', 'Le ticket a été modifié.');
            return $this->redirectToRoute('app_admin_ticket_index');
        }
        return $this->render('admin/ticket/form.html.twig', ['form' => $form, 'title' => 'Modifier le ticket']);
    }

    private function synchroniseDateCloture(Ticket $ticket): void
    {
        if ($ticket->getEtat()?->getNom() === 'Fermé') {
            $ticket->setDateCloture($ticket->getDateCloture() ?? new \DateTimeImmutable());
        } elseif ($ticket->getDateCloture() !== null) {
            $ticket->setDateCloture(null);
        }
    }
}
