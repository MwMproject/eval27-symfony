<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Etat;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('auteur', EmailType::class)
            ->add('description', TextareaType::class, ['attr' => ['rows' => 6]])
            ->add('dateOuverture', DateTimeType::class, ['widget' => 'single_text'])
            ->add('dateCloture', DateTimeType::class, ['widget' => 'single_text', 'required' => false])
            ->add('categorie', EntityType::class, ['class' => Categorie::class])
            ->add('etat', EntityType::class, ['class' => Etat::class])
            ->add('responsable', EntityType::class, [
                'class' => User::class,
                'required' => false,
                'placeholder' => 'Non assigné',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Ticket::class]);
    }
}
