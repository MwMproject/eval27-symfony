<?php

namespace App\Tests\Controller;

use App\Entity\Etat;
use App\Entity\Ticket;
use App\Repository\CategorieRepository;
use App\Repository\EtatRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationControllerTest extends WebTestCase
{
    public function testHomePageDisplaysTicketForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Créer un ticket');
        self::assertSelectorExists('form[name="public_ticket"]');
    }

    public function testAnonymousUserIsRedirectedToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tickets');

        self::assertResponseRedirects('http://localhost/connexion');
    }

    public function testVisitorCanCreateTicket(): void
    {
        $client = static::createClient();
        $categorie = static::getContainer()->get(CategorieRepository::class)->findOneBy(['nom' => 'Incident']);
        $repository = static::getContainer()->get(TicketRepository::class);
        $countBefore = $repository->count([]);

        $crawler = $client->request('GET', '/');
        $client->submit($crawler->selectButton('Envoyer le ticket')->form([
            'public_ticket[auteur]' => 'nouveau.client@example.com',
            'public_ticket[description]' => 'Le service ne répond plus depuis plusieurs minutes.',
            'public_ticket[categorie]' => (string) $categorie?->getId(),
        ]));

        self::assertResponseRedirects('/');
        self::assertSame($countBefore + 1, $repository->count([]));
    }

    public function testAdminCanLogInAndOpenAdministration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        $client->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@symfony',
            '_password' => 'Admin123!',
        ]));

        self::assertResponseRedirects();
        $client->followRedirect();
        $client->request('GET', '/admin/categories');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Catégories');
    }

    public function testStaffCannotOpenAdministration(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        $client->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'staff@eval27.test',
            '_password' => 'Staff123!',
        ]));
        $client->followRedirect();
        $client->request('GET', '/admin/categories');

        self::assertResponseStatusCodeSame(403);
    }

    public function testStaffCanCloseTicket(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');
        $client->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'staff@eval27.test',
            '_password' => 'Staff123!',
        ]));
        $client->followRedirect();

        $ticket = static::getContainer()->get(TicketRepository::class)->findOneBy([]);
        $etatFerme = static::getContainer()->get(EtatRepository::class)->findOneBy(['nom' => 'Fermé']);
        self::assertInstanceOf(Ticket::class, $ticket);
        self::assertInstanceOf(Etat::class, $etatFerme);

        $crawler = $client->request('GET', '/tickets/'.$ticket->getId().'/statut');
        $client->submit($crawler->selectButton('Enregistrer')->form([
            'ticket_status[etat]' => (string) $etatFerme->getId(),
        ]));

        self::assertResponseRedirects('/tickets/'.$ticket->getId());
        $ticketFerme = static::getContainer()->get(TicketRepository::class)->find($ticket->getId());
        self::assertNotNull($ticketFerme?->getDateCloture());
    }
}
