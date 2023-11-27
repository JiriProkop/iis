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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassActivityController extends AbstractController
{
    private ClassActivityEntityRepository $activityRepository;
    private ClassEntityRepository $classRepository;
    private EntityManagerInterface $em;
    public function __construct(ClassActivityEntityRepository $activityRepository, ClassEntityRepository $classRepository, EntityManagerInterface $em) {
        $this->activityRepository = $activityRepository;
        $this->classRepository = $classRepository;
        $this->em = $em;
    }
    #[Route('/class/{id}/activities', name: 'class_activities')]
    public function class_activities($id): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id);

        $guarantees = false;
        if (in_array('ROLE_ADMIN', $user->getRoles()) || ($user != null && $class->getGuarantor()->getId() == $user->getId())) {
            $guarantees = true;
        }

        $activities = null;
        if ($class != null) {
            $activities = $class->getActivities();
        }

        return $this->render('/class_activity/activity_list.html.twig', [
            'class' => $class,
            'activities' => $activities,
            'guarantees' => $guarantees,
        ]);
    }

    #[Route('/class/{id}/activity/create', name: 'activity_create')]
    public function activity_create($id, Request $request): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id);

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && ($user == null || $class->getGuarantor()->getId() != $user->getId())) {
            return $this->render('class_activity/activity_create.html.twig', [
                'error' => 'You do not have the permissions to create new activity!',
                'form' => null,
            ]);
        }

        $newActivity = new ClassActivityEntity();
        $form = $this->createForm(ClassActivityFormType::class, $newActivity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // set activity data
            $newActivity->setClass($class);
            $newActivity->setName($form->get('Name')->getData());
            $newActivity->setRepetition($form->get('Repetition')->getData());

            // check length and set
            if ($form->get('Length')->getData() > 0) {
                $newActivity->setLength($form->get('Length')->getData());
            } else {
                return $this->render('class_activity/activity_create.html.twig', [
                    'error' => 'Activity length must be greater than 0!',
                    'form' => $form->createView(),
                ]);
            }

            // the activity set by the guarantor is in the draft state until confirmed by scheduler
            $newActivity->setDraft(true);

            $this->em->persist($newActivity);
            $this->em->flush();

            return $this->redirectToRoute('class_activities', ['id' => $id]);
        }

        return $this->render('class_activity/activity_create.html.twig', [
            'error' => null,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/edit', name: 'activity_edit')]
    public function activity_edit($id_class, $id_activity, Request $request): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id_class);

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && ($user == null || $class->getGuarantor()->getId() != $user->getId())) {
            return $this->render('class_activity/activity_edit.html.twig', [
                'error' => 'You do not have the permissions to edit this activity!',
                'form' => null,
            ]);
        }

        $activity = $this->activityRepository->find($id_activity);
        $form = $this->createForm(ClassActivityFormType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setName($form->get('Name')->getData());
            $activity->setRepetition($form->get('Repetition')->getData());

            // check length and set
            if ($form->get('Length')->getData() > 0) {
                $activity->setLength($form->get('Length')->getData());
            } else {
                return $this->render('class_activity/activity_edit.html.twig', [
                    'error' => 'Activity length must be greater than 0!',
                    'form' => $form->createView(),
                ]);
            }

            $this->em->flush();
            return $this->redirectToRoute('class_activities', ['id' => $id_class]);
        }

        return $this->render('class_activity/activity_edit.html.twig', [
            'error' => null,
            'activity' => $activity,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/delete', name: 'activity_delete')]
    public function activity_delete($id_class, $id_activity): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id_class);

        // only admin or guarantor can delete
        if (in_array('ROLE_ADMIN', $user->getRoles()) || ($user != null && $class->getGuarantor()->getId() == $user->getId())) {
            $activity = $this->activityRepository->find($id_activity);

            $this->em->remove($activity);
            $this->em->flush();
        }

        return $this->redirectToRoute('class_activities', ['id' => $id_class]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/schedule', name: 'activity_schedule')]
    public function activity_schedule($id_class, $id_activity, Request $request): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id_class);

        if (!in_array('ROLE_ADMIN', $user->getRoles()) && ($user == null || $class->getGuarantor()->getId() != $user->getId())) {
            return $this->render('class_activity/activity_schedule.html.twig', [
                'error' => 'You do not have the permissions to schedule this activity!',
                'form' => null,
            ]);
        }

        $schedule = new ScheduleWindowEntity();
        $activity = $this->activityRepository->find($id_activity);
        if ($activity->getScheduledWindows() != null && $activity->getScheduledWindows()->get(0) != null) {
            $schedule = $activity->getScheduledWindows()->get(0);
        }

        if ($activity->getRepetition() == 'ONE_TIME') {
            $form = $this->createForm(OneTimeScheduleFormType::class, $schedule);
        } else {
            $form = $this->createForm(RepeatingScheduleFormType::class, $schedule);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // remove all already scheduled windows
            if ($activity->getScheduledWindows() != null && $activity->getScheduledWindows()->count() > 0) {
                foreach ($activity->getScheduledWindows() as $sw) {
                    $activity->removeScheduledWindow($sw);
                }
            }

            if ($activity->getRepetition() == 'ONE_TIME') {
                // if the activity is only a one time thing:
                $newSchedule = new ScheduleWindowEntity();
                // set start
                $start = $form->get('Start')->getData();
                $newSchedule->setStart(new \DateTime($start->format('Y-m-d H:i:s')));
                // set end
                $length = $activity->getLength();
                $start->modify("+$length hour");
                $newSchedule->setEnd(new \DateTime($start->format('Y-m-d H:i:s')));

                $this->em->persist($newSchedule);
                $activity->addScheduledWindow($newSchedule);
            } else {
                // if the activity is repeating:

                // get first day of semester
                $datetime = constants::getFirstDayOfSemester();

                // and add the offset based on the chosen day
                $offsetDays = new \DateInterval($form->get('Day')->getData());
                $datetime->add($offsetDays);

                // and based of the chosen hour
                $offsetSeconds = $form->get('Start')->getData();
                $datetime->modify("+ $offsetSeconds seconds");

                // get the number of repetitions
                if ($activity->getRepetition() == 'EVEN') {
                    $times = constants::EVEN_WEEKS;
                } else if ($activity->getRepetition() == 'ODD') {
                    $times = constants::ODD_WEEKS;
                    // the first week is even, so go forward one week
                    $datetime->add(constants::getWeekInterval());
                } else {
                    $times = constants::TERM_LENGTH;
                }

                for ($i = 0; $i < $times; $i++) {
                    $newSchedule = new ScheduleWindowEntity();
                    // set start
                    $start = new \DateTime($datetime->format('Y-m-d H:i:s'));
                    $newSchedule->setStart($start);
                    // set end
                    $length = $activity->getLength();
                    $datetime->modify("+$length hour");
                    $end = new \DateTime($datetime->format('Y-m-d H:i:s'));
                    $newSchedule->setEnd($end);

                    // modify the start time back
                    $datetime->modify("-$length hour");

                    $this->em->persist($newSchedule);
                    $activity->addScheduledWindow($newSchedule);

                    // go to next week
                    $datetime->add(constants::getWeekInterval());
                    if ($activity->getRepetition() != 'ALL') {
                        // if odd or even, go one week more
                        $datetime->add(constants::getWeekInterval());
                    }
                }
            }

            $this->em->flush();
            return $this->redirectToRoute('class_activities', ['id' => $id_class]);
        }

        return $this->render('class_activity/activity_schedule.html.twig', [
            'error' => null,
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }
}
