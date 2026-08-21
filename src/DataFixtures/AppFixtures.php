<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = [];
        foreach (['Incident', 'Panne', 'Evolution', 'Anomalie', 'Information'] as $nom) {
            $categorie = (new Categorie())->setNom($nom);
            $manager->persist($categorie);
            $categories[$nom] = $categorie;
        }

        $etats = [];
        foreach (['Nouveau', 'Ouvert', 'Résolu', 'Fermé'] as $nom) {
            $etat = (new Etat())->setNom($nom);
            $manager->persist($etat);
            $etats[$nom] = $etat;
        }

        $admin = (new User())
            ->setEmail('admin@eval27.test')
            ->setPrenom('Alice')
            ->setNom('Admin')
            ->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        $staff = (new User())
            ->setEmail('staff@eval27.test')
            ->setPrenom('Paul')
            ->setNom('Support')
            ->setRoles(['ROLE_STAFF']);
        $staff->setPassword($this->passwordHasher->hashPassword($staff, 'Staff123!'));
        $manager->persist($staff);

        $tickets = [
            ['client@example.com', 'Impossible de me connecter à mon espace depuis ce matin.', 'Incident', 'Nouveau', null],
            ['contact@example.com', "Le formulaire d'inscription affiche une erreur après validation.", 'Anomalie', 'Ouvert', $staff],
            ['direction@example.com', 'Ajouter un export PDF à la liste des demandes traitées.', 'Evolution', 'Résolu', $staff],
        ];

        foreach ($tickets as [$auteur, $description, $categorie, $etat, $responsable]) {
            $ticket = (new Ticket())
                ->setAuteur($auteur)
                ->setDescription($description)
                ->setCategorie($categories[$categorie])
                ->setEtat($etats[$etat])
                ->setResponsable($responsable);
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
