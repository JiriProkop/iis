<?php

namespace App\Controller;

use App\Entity\ClassEntity;
use App\Form\ClassFormType;
use App\Repository\ClassEntityRepository;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassController extends AbstractController
{
    private ClassEntityRepository $classRepository;
    private PersonEntityRepository $personRepository;
    private EntityManagerInterface $em;
    public function __construct(ClassEntityRepository $classRepository, PersonEntityRepository $personRepository, EntityManagerInterface $em) {
        $this->classRepository = $classRepository;
        $this->personRepository = $personRepository;
        $this->em = $em;
    }

    #[Route('/class', name: 'class')]
    public function class(): Response
    {
        $user = $this->getUser();
        if ($user != null) {
            $role = $user->getRoles()[0];
        } else {
            $role = 'ROLE_ELSE';
        }

        $classes = $this->classRepository->findAll();

        return $this->render('class/class_list.html.twig', [
            'classes' => $classes,
            'role' => $role,
        ]);
    }

    #[Route('/class/create', name: 'class_create')]
    public function class_create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $newClass = new ClassEntity();
        $form = $this->createForm(ClassFormType::class, $newClass);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // set things without need for validation
            $newClass->setAbbreviation($form->get('Abbreviation')->getData());
            $newClass->setAnotation($form->get('Anotation')->getData());
            $newClass->setName($form->get('Name')->getData());

            // check credit count
            if ($form->get('Credits')->getData() > 0) {
                $newClass->setCredits($form->get('Credits')->getData());
            } else {
                return $this->render('class/class_create.html.twig', [
                    'error' => 'Credit count must be greater than 0!',
                    'form' => $form->createView(),
                ]);
            }

            $this->em->persist($newClass);
            $this->em->flush();

            return $this->redirectToRoute('class_add_people', ['id' => $newClass->getId()]);
        }

        return $this->render('class/class_create.html.twig', [
            'error' => null,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id}/add_people', name: 'class_add_people')]
    public function class_add_people($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class = $this->classRepository->find($id);

        // get array of possible guarantors
        $guarantors = array();

        $potential_guarantors = $this->personRepository->findAll();
        foreach ($potential_guarantors as $pg) {
            if (in_array('ROLE_GUARANTOR', $pg->getRoles())) {
                $guarantors[] = $pg;
            }
        }

        // get array of possible teachers
        $teachers = array();
        $potential_teachers = $this->personRepository->findAll();
        foreach ($potential_teachers as $pt) {
            if (in_array('ROLE_TEACHER', $pt->getRoles()) && !in_array($pt, $class->getPeople()->getValues())) {
                $teachers[] = $pt;
            }
        }

        return $this->render('class/add_people.html.twig', [
            'class' => $class,
            'guarantors' => $guarantors,
            'teachers' => $teachers
        ]);
    }

    #[Route('/class/{id_class}/set_guarantor/{id_person}', name: 'class_set_guarantor')]
    public function class_set_guarantor($id_class, $id_person): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class= $this->classRepository->find($id_class);
        $guarantor = $this->personRepository->find($id_person);

        if ($class != null && $guarantor != null) {
            $class->setGuarantor($guarantor);
        }

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id}/remove_guarantor', name: 'class_remove_guarantor')]
    public function class_remove_guarantor($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class= $this->classRepository->find($id);
        if ($class != null) {
            $class->setGuarantor(null);
        }

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id_class}/add_teacher/{id_person}', name: 'class_add_teacher')]
    public function class_add_teacher($id_class, $id_person): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class = $this->classRepository->find($id_class);
        $person = $this->personRepository->find($id_person);

        if ($class != null && $person != null) {
            $people = $class->getPeople();
            $people[] = $person;
        }

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id_class}/remove_teacher/{id_person}', name: 'class_remove_teacher')]
    public function class_remove_teacher($id_class, $id_person): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class = $this->classRepository->find($id_class);

        if ($class != null) {
            $people = $class->getPeople();
            foreach ($people as $p) {
                if ($p->getId() == $id_person) {
                    $people->removeElement($p);
                    break;
                }
            }
        }

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id}/edit', name: 'class_edit')]
    public function class_edit($id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class = $this->classRepository->find($id);
        $form = $this->createForm(ClassFormType::class, $class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // set things without need for validation
            $class->setAbbreviation($form->get('Abbreviation')->getData());
            $class->setAnotation($form->get('Anotation')->getData());
            $class->setName($form->get('Name')->getData());

            // check credit count
            if ($form->get('Credits')->getData() > 0) {
                $class->setCredits($form->get('Credits')->getData());
            } else {
                return $this->render('class/class_edit.html.twig', [
                    'error' => 'Credit count must be greater than 0!',
                    'class' => $class,
                    'form' => $form->createView(),
                ]);
            }

            $this->em->flush();
            return $this->redirectToRoute('class');
        }

        return $this->render('class/class_edit.html.twig', [
            'error' => null,
            'class' => $class,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/class/{id}/delete/', name: 'class_delete')]
    public function class_delete($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class= $this->classRepository->find($id);

        $this->em->remove($class);
        $this->em->flush();

        return $this->redirectToRoute('class');
    }
}
