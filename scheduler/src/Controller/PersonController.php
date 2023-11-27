<?php

namespace App\Controller;

use App\constants;
use App\Entity\PersonEntity;
use App\Form\EditPersonFormType;
use App\Form\PersonFormType;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PersonController extends AbstractController
{
    private PersonEntityRepository $personRepository;
    private EntityManagerInterface $em;
    public function __construct(PersonEntityRepository $personRepository, EntityManagerInterface $em) {
        $this->personRepository = $personRepository;
        $this->em = $em;
    }

    #[Route('/person', name: 'person_view')]
    public function person_view(): Response
    {
        $person = $this->getUser();

        return $this->render('person/person_view.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/person/{id}/edit', name: 'person_edit')]
    public function person_edit($id, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $person = $this->getUser();

        // check if a person is logged in
        if (!is_null($person)) {
            // if the current user is admin, the edit form is extended
            if (in_array('ROLE_ADMIN', $person->getRoles())) {
                $form = $this->createForm(PersonFormType::class, $person);
            } else {
                $form = $this->createForm(EditPersonFormType::class, $person);
            }

            // check if the logged user is the same as the one they are editing (or admin)
            if (!in_array('ROLE_ADMIN', $person->getRoles()) && $person->getId() != $id) {
                return $this->render('person/person_edit.html.twig', [
                    'error' => 'You do not have the permissions to edit that user!',
                    'person' => $person,
                    'form' => $form->createView(),
                ]);
            }

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // check email address and set it
                if (preg_match(constants::EMAIL_PATTERN, $form->get('Email')->getData())) {
                    $person->setEmail($form->get('Email')->getData());
                } else {
                    return $this->render('person/person_edit.html.twig', [
                        'error' => 'The email address is in a wrong format!',
                        'person' => $person,
                        'form' => $form->createView(),
                    ]);
                }

                if (strlen($form->get('Password')->getData()) != 0) {
                    // if the password is longer than 8 characters and the passwords match, hash it and set it
                    if (strlen($form->get('Password')->getData()) < 8) {
                        return $this->render('person/person_edit.html.twig', [
                            'error' => 'The password must be at least 8 characters!',
                            'person' => $person,
                            'form' => $form->createView(),
                        ]);
                    } else if ($form->get('Password')->getData() != $form->get('PasswordAgain')->getData()) {
                        return $this->render('person/person_edit.html.twig', [
                            'error' => 'The passwords do not match!',
                            'person' => $person,
                            'form' => $form->createView(),
                        ]);
                    } else {
                        $hashedPassword = $passwordHasher->hashPassword($person, $form->get('Password')->getData());
                        $person->setPassword($hashedPassword);
                    }
                }

                // set roles and login
                if (in_array('ROLE_ADMIN', $person->getRoles())) {
                    // check if login is longer than 0 characters and set it
                    if (strlen($form->get('Login')->getData()) > 0) {
                        $person->setLogin($form->get('Login')->getData());
                    } else {
                        return $this->render('person/person_edit.html.twig', [
                            'error' => 'The login is too short!',
                            'form' => $form->createView(),
                        ]);
                    }

                    // set the user roles
                    $person->setRoles($form->get('roles')->getData());
                }

                $this->em->flush();
                return $this->redirectToRoute('person_view');
            }

            return $this->render('person/person_edit.html.twig', [
                'error' => null,
                'person' => $person,
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('person_view');
    }

    #[Route('/person/list', name: 'person_list')]
    public function person_list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $people = $this->personRepository->findAll();

        return $this->render('person/person_list.html.twig', [
            'people' => $people,
        ]);
    }

    #[Route('/person/create', name: 'person_create')]
    public function person_create(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // create new entity
        $newPerson = new PersonEntity();
        $form = $this->createForm(PersonFormType::class, $newPerson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // check email address and set it
            if (preg_match(constants::EMAIL_PATTERN, $form->get('Email')->getData())) {
                $newPerson->setEmail($form->get('Email')->getData());
            } else {
                return $this->render('person/person_create.html.twig', [
                    'error' => 'The email address is in a wrong format!',
                    'form' => $form->createView(),
                ]);
            }

            // check if login is longer than 0 characters and set it
            if (strlen($form->get('Login')->getData()) > 0) {
                $newPerson->setLogin($form->get('Login')->getData());
            } else {
                return $this->render('person/person_create.html.twig', [
                    'error' => 'The login is too short!',
                    'form' => $form->createView(),
                ]);
            }

            // set the user roles
            $newPerson->setRoles($form->get('roles')->getData());

            // if the password is longer than 8 characters and the passwords match, hash it and set it
            if (strlen($form->get('Password')->getData()) < 8) {
                return $this->render('person/person_create.html.twig', [
                    'error' => 'The password must be at least 8 characters!',
                    'form' => $form->createView(),
                ]);
            } else if ($form->get('Password')->getData() != $form->get('PasswordAgain')->getData()) {
                return $this->render('person/person_create.html.twig', [
                    'error' => 'The passwords do not match!',
                    'form' => $form->createView(),
                ]);
            } else {
                $hashedPassword = $passwordHasher->hashPassword($newPerson, $form->get('Password')->getData());
                $newPerson->setPassword($hashedPassword);
            }

            $this->em->persist($newPerson);
            $this->em->flush();

            return $this->redirectToRoute('person_list');
        }

        return $this->render('person/person_create.html.twig', [
            'error' => null,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/person/{id}/delete/', name: 'person_delete')]
    public function person_delete($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $person = $this->personRepository->find($id);

        $this->em->remove($person);
        $this->em->flush();

        return $this->redirectToRoute('person_list');
    }
}
