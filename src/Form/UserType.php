<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('prenom', null, ['label' => 'Prénom'])
            ->add('nom')
            ->add('roles', ChoiceType::class, [
                'choices' => ['Personnel' => 'ROLE_STAFF', 'Administrateur' => 'ROLE_ADMIN'],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$options['is_edit'],
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'constraints' => array_values(array_filter([
                    $options['is_edit'] ? null : new Assert\NotBlank(message: 'Le mot de passe est obligatoire.'),
                    new Assert\Length(min: 8, minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères.'),
                ])),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class, 'is_edit' => false]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
