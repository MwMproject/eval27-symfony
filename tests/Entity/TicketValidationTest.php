<?php

namespace App\Tests\Entity;

use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TicketValidationTest extends KernelTestCase
{
    public function testInvalidEmailAndShortDescriptionAreRejected(): void
    {
        self::bootKernel();
        $validator = self::getContainer()->get('validator');

        $ticket = (new Ticket())
            ->setAuteur('adresse-invalide')
            ->setDescription('Trop court')
            ->setCategorie((new Categorie())->setNom('Incident'))
            ->setEtat((new Etat())->setNom('Nouveau'));

        $violations = $validator->validate($ticket);
        $properties = [];
        foreach ($violations as $violation) {
            $properties[] = $violation->getPropertyPath();
        }

        self::assertContains('auteur', $properties);
        self::assertContains('description', $properties);
    }

    public function testValidTicketHasNoValidationError(): void
    {
        self::bootKernel();
        $validator = self::getContainer()->get('validator');

        $ticket = (new Ticket())
            ->setAuteur('client@example.com')
            ->setDescription('Une description suffisamment longue pour être valide.')
            ->setCategorie((new Categorie())->setNom('Incident'))
            ->setEtat((new Etat())->setNom('Nouveau'));

        self::assertCount(0, $validator->validate($ticket));
    }
}
