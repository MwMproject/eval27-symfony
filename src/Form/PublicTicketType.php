<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('auteur', EmailType::class, ['label' => 'Votre adresse e-mail'])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre demande',
                'attr' => ['rows' => 6],
                'help' => 'Entre 20 et 250 caractères.',
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'placeholder' => 'Choisissez une catégorie',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Ticket::class]);
    }
}
