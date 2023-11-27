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
use App\Repository\PersonEntityRepository;
use App\Repository\RoomEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassActivityController extends AbstractController
{
    private ClassActivityEntityRepository $activityRepository;
    private ClassEntityRepository $classRepository;
    private PersonEntityRepository $personRepository;
    private RoomEntityRepository $roomRepository;
    private EntityManagerInterface $em;
    public function __construct(ClassActivityEntityRepository $activityRepository, ClassEntityRepository $classRepository, PersonEntityRepository $personRepository, RoomEntityRepository $roomRepository, EntityManagerInterface $em) {
        $this->activityRepository = $activityRepository;
        $this->classRepository = $classRepository;
        $this->personRepository = $personRepository;
        $this->roomRepository = $roomRepository;
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

            // delete all scheduled windows
            foreach ($activity->getScheduledWindows() as $sw) {
                $activity->removeScheduledWindow($sw);
                $this->em->remove($sw);
            }

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
                'set_time' => null,
                'activity' => null,
                'teachers' => null,
                'schedule' => null,
                'form' => null,
            ]);
        }

        $activity = $this->activityRepository->find($id_activity);

        // get array of possible teachers
        $teachers = array();
        $potential_teachers = $class->getPeople();
        foreach ($potential_teachers as $pt) {
            if (in_array('ROLE_TEACHER', $pt->getRoles()) && $activity->getTeacher() != $pt) {
                $teachers[] = $pt;
            }
        }

        $schedule = new ScheduleWindowEntity();

        if ($activity->getRepetition() == 'ONE_TIME') {
            $form = $this->createForm(OneTimeScheduleFormType::class, $schedule);
        } else {
            $form = $this->createForm(RepeatingScheduleFormType::class, $schedule);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // set the activity room
            foreach ($form->get('Rooms')->getData() as $room_id) {
                $activity->addRoom($this->roomRepository->find($room_id));
            }

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

        // get the set time into a string
        $set_time = 'repetition: ' . $activity->getRepetition();
        if ($activity->getScheduledWindows() != null && $activity->getScheduledWindows()->get(0) != null) {
            if ($activity->getRepetition() == 'ONE_TIME') {
                $set_time = $set_time . ' date: ' . $activity->getScheduledWindows()->get(0)->getStart()->format('Y-m-d H:i');
            } else {
                $set_time = $set_time . ' time: ' . $activity->getScheduledWindows()->get(0)->getStart()->format('l H:i');
            }
        }

        return $this->render('class_activity/activity_schedule.html.twig', [
            'error' => null,
            'set_time' => $set_time,
            'activity' => $activity,
            'teachers' => $teachers,
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/set_teacher/{id_person}', name: 'activity_set_teacher')]
    public function activity_set_teacher($id_class, $id_activity, $id_person): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id_class);

        // check permissions
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && ($user == null || $class->getGuarantor()->getId() != $user->getId())) {
            return $this->redirectToRoute('activity_schedule', ['id_class' => $id_class, 'id_activity' => $id_activity]);
        }

        $activity = $this->activityRepository->find($id_activity);
        $person = $this->personRepository->find($id_person);

        // set the teacher to the activity
        if ($activity != null && $person != null && in_array($person, $class->getPeople()->toArray())) {
            $activity->setTeacher($person);
        }

        $this->em->flush();

        return $this->redirectToRoute('activity_schedule', ['id_class' => $id_class, 'id_activity' => $id_activity]);
    }

    #[Route('/class/{id_class}/activity/{id_activity}/remove_teacher', name: 'activity_remove_teacher')]
    public function activity_remove_teacher($id_class, $id_activity): Response
    {
        $user = $this->getUser();
        $class = $this->classRepository->find($id_class);

        // check permissions
        if (!in_array('ROLE_ADMIN', $user->getRoles()) && ($user == null || $class->getGuarantor()->getId() != $user->getId())) {
            return $this->redirectToRoute('activity_schedule', ['id_class' => $id_class, 'id_activity' => $id_activity]);
        }

        $activity = $this->activityRepository->find($id_activity);

        // remove the teacher from activity
        if ($activity != null) {
            $activity->setTeacher(null);
        }

        $this->em->flush();

        return $this->redirectToRoute('activity_schedule', ['id_class' => $id_class, 'id_activity' => $id_activity]);
    }
}
