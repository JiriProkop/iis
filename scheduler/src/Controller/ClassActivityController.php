<?php

namespace App\Controller;

use App\constants;
use App\Entity\ClassActivityEntity;
use App\Entity\ScheduleWindowEntity;
use App\Form\ClassActivityFormType;
use App\Form\OneTimeScheduleFormType;
use App\Form\RepeatingScheduleFormType;
use App\Repository\ClassActivityEntityRepository;
use App\Repository\ClassEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

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
            $newActivity->setDraft(true);

            $this->em->persist($newActivity);
            $this->em->flush();

            return $this->redirectToRoute('class_activities', ['id' => $id]);
        }

        return $this->render('person/person_create.html.twig', [
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

        return $this->render('class_edit.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/class/{id_class}/activity/{id_activity}/schedule', name: 'activity_set_schedule')]
    public function activity_set_schedule(Request $request, $id_class, $id_activity): Response
    {

        $schedule = new ScheduleWindowEntity();
        $activity = $this->activityRepository->find($id_activity);

        if ($activity->getRepetition() == 'ONE_TIME') {
            $form = $this->createForm(OneTimeScheduleFormType::class, $schedule);
        } else {
            $form = $this->createForm(RepeatingScheduleFormType::class, $schedule);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');
            if ($activity->getRepetition() == 'ONE_TIME') {
                for ($i = 0; $i < $activity->getLength(); $i++) {
                    // TODO utc time?
                    $schedule = new ScheduleWindowEntity();
                    $datetime = $form->get('Start')->getData();
                    $datetime->modify("+$i hour");
                    $dateNew = new DateTime();
                    $dateNew = new \DateTime($datetime->format('Y-m-d H:i:s'));
                    $schedule->setStart($dateNew);
                    $activity->addScheduledWindow($schedule);
                    $this->em->persist($schedule);
                }

            } else {
                $days = ['Mon' => 'P0D', 'Tue' => 'P1D', 'Wed' => 'P2D', 'Thu' => 'P3D', 'Fri' => 'P4D', 'Sat' => 'P5D', 'Sun' => 'P6D'];
                $constants = new constants();
                $datetime = $constants->getFirstDayOfSemester();

                $offset = new \DateInterval($days[$form->get('Day')->getData()]);

                $datetime->add($offset);

                $datetime->modify('+' . $form->get('Start')->getData() . 'seconds');


                if ($activity->getRepetition() == 'EVEN') {
                    $times = 7;
                } else if ($activity->getRepetition() == 'ODD') {
                    $times = 6;
                    $datetime->add($constants->getWeekInterval());
                } else {
                    $times = 13;
                }


                for ($i = 0; $i < $times; $i++) {

                    for ($j = 0; $j < $activity->getLength(); $j++) {
                        // TODO utc time?
                        $schedule = new ScheduleWindowEntity();
                        $datetime->modify("+$j hour");
                        $dateNew = new DateTime();
                        $dateNew = new \DateTime($datetime->format('Y-m-d H:i:s'));
                        $schedule->setStart($dateNew);
                        $activity->addScheduledWindow($schedule);
                        $this->em->persist($schedule);
                        dump($schedule);
                    }

                    $minus = $activity->getLength() - 1;
                    $datetime->modify("-$minus hour");


                    $datetime->add($constants->getWeekInterval());
                    if ($activity->getRepetition() != 'ALL') {
                        $datetime->add($constants->getWeekInterval());
                    }

                }


            }


            $this->em->flush();
            return $this->redirectToRoute('class_activities', ['id' => $id_class]);
        }

        return $this->render('class_edit.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }
}
