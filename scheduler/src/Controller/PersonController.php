<?php

namespace App\Controller;

use App\Entity\PersonEntity;
use App\Form\PersonalPersonFormType;
use App\Form\PersonFormType;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PersonController extends AbstractController
{
    private PersonEntityRepository $personRepository;
    private EntityManagerInterface $em;
    public function __construct(PersonEntityRepository $personEntityRepository, EntityManagerInterface $em) {
        $this->personRepository = $personEntityRepository;
        $this->em = $em;
    }

    #[Route('/person', name: 'person', methods: ['GET'])]
    public function personal_index(): Response
    {
        $person = $this->getUser();

        return $this->render('person/personal_index.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/person/edit', name: 'person_edit')]
    public function personal_edit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $person = $this->getUser();

        if (!is_null($person)) {
            $form = $this->createForm(PersonalPersonFormType::class, $person);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // TODO validace dat zde $form->get('...');

                $person->setEmail($form->get('Email')->getData());

                $hashedPassword = $passwordHasher->hashPassword(
                    $person,
                    $form->get('Password')->getData()
                );

                $person->setPassword($hashedPassword);

                $this->em->flush();
                return $this->redirectToRoute('person');
            }

            return $this->render('person/edit.html.twig', [
                'person' => $person,
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('person');
    }

    #[Route('/person/admin/create', name: 'admin_person_create')]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $person = new PersonEntity();
        $form = $this->createForm(PersonFormType::class, $person);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $newPerson = new PersonEntity();

            $newPerson->setEmail($form->get('Email')->getData());
            $newPerson->setRoles($form->get('roles')->getData());
            $newPerson->setLogin($form->get('Login')->getData());

            $hashedPassword = $passwordHasher->hashPassword(
                $newPerson,
                $form->get('Password')->getData()
            );

            $newPerson->setPassword($hashedPassword);

            $this->em->persist($newPerson);
            $this->em->flush();

            return $this->redirectToRoute('admin_person');
        }

        return $this->render('person/create.html.twig', [
           'form' => $form->createView(),
        ]);
    }

    #[Route('/person/admin/edit/{id}', name: 'admin_person_edit')]
    public function edit($id, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $person = $this->personRepository->find($id);
        $form = $this->createForm(PersonFormType::class, $person);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $person->setEmail($form->get('Email')->getData());
            $person->setRoles($form->get('roles')->getData());
            $person->setLogin($form->get('Login')->getData());

            $hashedPassword = $passwordHasher->hashPassword(
                $person,
                $form->get('Password')->getData()
            );

            $person->setPassword($hashedPassword);

            $this->em->flush();
            return $this->redirectToRoute('admin_person');
        }

        return $this->render('person/edit.html.twig', [
            'person' => $person,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/person/admin/delete/{id}', name: 'admin_person_delete', methods: ['GET', 'DELETE'])]
    public function delete($id): Response
    {
        $person = $this->personRepository->find($id);

        $this->em->remove($person);
        $this->em->flush();

        return $this->redirectToRoute('admin_person');
    }

    #[Route('/person/admin', name: 'admin_person', methods: ['GET'])]
    public function index(): Response
    {
        $people = $this->personRepository->findAll();

        return $this->render('person/index.html.twig', [
            'people' => $people,
        ]);
    }
}
