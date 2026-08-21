<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/responsables')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $repository): Response
    {
        return $this->render('admin/user/index.html.twig', ['users' => $repository->findBy([], ['nom' => 'ASC'])]);
    }

    #[Route('/nouveau', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = (string) $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Le responsable a été créé.');
            return $this->redirectToRoute('app_admin_user_index');
        }
        return $this->render('admin/user/form.html.twig', ['form' => $form, 'title' => 'Nouveau responsable']);
    }

    #[Route('/{id}/modifier', name: 'app_admin_user_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true])->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if (is_string($plainPassword) && $plainPassword !== '') {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
            $entityManager->flush();
            $this->addFlash('success', 'Le responsable a été modifié.');
            return $this->redirectToRoute('app_admin_user_index');
        }
        return $this->render('admin/user/form.html.twig', ['form' => $form, 'title' => 'Modifier le responsable']);
    }
}
