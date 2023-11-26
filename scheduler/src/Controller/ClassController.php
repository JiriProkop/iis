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
    public function __construct(ClassEntityRepository $classEntityRepository, PersonEntityRepository $personRepository, EntityManagerInterface $em) {
        $this->classRepository = $classEntityRepository;
        $this->personRepository = $personRepository;
        $this->em = $em;
    }

    #[Route('/class/admin/create', name: 'admin_class_create')]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $class = new ClassEntity();
        $form = $this->createForm(ClassFormType::class, $class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $newClass = new ClassEntity();

            $newClass->setAbbreviation($form->get('Abbreviation')->getData());
            $newClass->setName($form->get('Name')->getData());
            $newClass->setAnotation($form->get('Anotation')->getData());
            $newClass->setCredits($form->get('Credits')->getData());

            $this->em->persist($newClass);
            $this->em->flush();

            return $this->redirectToRoute('class_add_people', ['id' => $newClass->getId()]);
        }

        return $this->render('person_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/edit/{id}', name: 'class_edit')]
    public function personal_edit(Request $request, $id): Response
    {
        $class = $this->classRepository->find($id);
        $form = $this->createForm(ClassFormType::class, $class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $class->setAbbreviation($form->get('Abbreviation')->getData());
            $class->setName($form->get('Name')->getData());
            $class->setAnotation($form->get('Anotation')->getData());
            $class->setCredits($form->get('Credits')->getData());

            $this->em->flush();
            return $this->redirectToRoute('class');
        }

        return $this->render('class/edit.html.twig', [
            'class' => $class,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id}/add_people', name: 'class_add_people')]
    public function add_people($id): Response
    {
        $class = $this->classRepository->find($id);

        $user = $this->getUser();

        $guarantors = null;

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $guarantors = array();

            $potential_guarantors = $this->personRepository->findAll();
            foreach ($potential_guarantors as $pg) {
                if (in_array('ROLE_GUARANTOR', $pg->getRoles())) {
                    $guarantors[] = $pg;
                }
            }
        }

        // TODO vyfiltrovat jen ucitele pro zobrazeni

        $teachers = array();
        $potential_teachers = $this->personRepository->findAll();
        foreach ($potential_teachers as $pt) {
            if (in_array('ROLE_TEACHER', $pt->getRoles())) {
                $teachers[] = $pt;
            }
        }


        return $this->render('class/add_people.html.twig', [
            'class' => $class,
            'guarantors' => $guarantors,
            'teachers' => $teachers
        ]);
    }

    #[Route('/class/{id}/remove_guarantor', name: 'class_remove_guarantor')]
    public function remove_guarantor($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $class= $this->classRepository->find($id);

        $class->setGuarantor(null);

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id_class}/set_guarantor/{id_person}', name: 'class_set_guarantor')]
    public function set_guarantor($id_class, $id_person): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $class= $this->classRepository->find($id_class);

        $class->setGuarantor($this->personRepository->find($id_person));

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id_class}/remove_teacher/{id_person}', name: 'class_remove_teacher')]
    public function remove_teacher($id_class, $id_person): Response
    {
        $class = $this->classRepository->find($id_class);

        $people = $class->getPeople();

        foreach ($people as $p) {
            if ($p->getId() == $id_person) {
                $people->removeElement($p);
                break;
            }
        }

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/{id_class}/add_teacher/{id_person}', name: 'class_add_teacher')]
    public function add_teacher($id_class, $id_person): Response
    {
        $class = $this->classRepository->find($id_class);

        $people = $class->getPeople();
        $person = $this->personRepository->find($id_person);

        $people[] = $person;

        $this->em->flush();

        return $this->redirectToRoute('class_add_people', ['id' => $class->getId()]);
    }

    #[Route('/class/admin', name: 'class')]
    public function index_admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $classes = $this->classRepository->findAll();

        return $this->render('class/index.html.twig', [
            'classes' => $classes,
        ]);
    }

    #[Route('/class/admin/delete/{id}', name: 'class_delete')]
    public function delete_class($id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $class= $this->classRepository->find($id);

        $this->em->remove($class);
        $this->em->flush();

        return $this->redirectToRoute('class');
    }
}
