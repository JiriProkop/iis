<?php

namespace App\Controller;

use App\Entity\ClassActivityEntity;
use App\Form\ClassActivityFormType;
use App\Repository\ClassActivityEntityRepository;
use App\Repository\ClassEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassActivityController extends AbstractController
{
    private ClassActivityEntityRepository $activityRepository;
    private ClassEntityRepository $classRepository;
    private EntityManagerInterface $em;
    public function __construct(ClassActivityEntityRepository $activityEntityRepository, ClassEntityRepository $classEntityRepository, EntityManagerInterface $em) {
        $this->activityRepository = $activityEntityRepository;
        $this->classRepository = $classEntityRepository;
        $this->em = $em;
    }
    #[Route('/class/{id}/activities', name: 'class_activities')]
    public function index($id): Response
    {
        $class = $this->classRepository->find($id);

        $activities = $class->getActivities();

        return $this->render('class_activity/index.html.twig', [
            'class' => $class,
            'activities' => $activities,
        ]);
    }

    #[Route('/class/{id}/activities/create', name: 'create_activity')]
    public function create_activity($id, Request $request): Response
    {
        $class = $this->classRepository->find($id);

        $activity = new ClassActivityEntity();
        $form = $this->createForm(ClassActivityFormType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $newActivity = new ClassActivityEntity();

            $newActivity->setClass($class);
            $newActivity->setName($form->get('Name')->getData());
            $newActivity->setLength($form->get('Length')->getData());
            $newActivity->setRepetition($form->get('Repetition')->getData());

            $this->em->persist($newActivity);
            $this->em->flush();

            return $this->redirectToRoute('class_activities', ['id' => $id]);
        }

        return $this->render('person/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/delete', name: 'activity_delete', methods: ['GET', 'DELETE'])]
    public function delete_class($id_class, $id_activity): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $activity = $this->activityRepository->find($id_activity);

        $this->em->remove($activity);
        $this->em->flush();

        return $this->redirectToRoute('class_activities', ['id' => $id_class]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/edit', name: 'activity_edit')]
    public function activity_edit(Request $request, $id_class, $id_activity): Response
    {

        $activity = $this->activityRepository->find($id_activity);
        $form = $this->createForm(ClassActivityFormType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $activity->setName($form->get('Name')->getData());
            $activity->setLength($form->get('Length')->getData());
            $activity->setRepetition($form->get('Repetition')->getData());

            $this->em->flush();
            return $this->redirectToRoute('class_activities', ['id' => $id_class]);
        }

        return $this->render('class/edit.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
        ]);
    }
}
