<?php

namespace App\Controller;

use App\constants;
use App\Entity\PersonalActivityEntity;
use App\Entity\PersonEntity;
use App\Entity\ScheduleWindowEntity;
use App\Form\PersonalActivityFormType;
use App\Form\PersonFormType;
use App\Repository\RoomEntityRepository;
use Cassandra\Time;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PersonalActivityEntityRepository;
use App\Repository\PersonEntityRepository;
use App\Repository\ScheduleWindowEntityRepository;

class PersonalActivityController extends AbstractController
{
    private PersonalActivityEntityRepository $personalActivityRepository;
    private ScheduleWindowEntityRepository $scheduleWindowRepository;
    private RoomEntityRepository $roomRepository;
    private EntityManagerInterface $em;
    private \DateTime $date;
    private \DateTime $first_date;
    private \DateTime $last_date;


    public function __construct(PersonalActivityEntityRepository $personalActivityEntityRepository,
                                ScheduleWindowEntityRepository $scheduleWindowEntityRepository,
                                RoomEntityRepository $roomEntityRepository,
                                EntityManagerInterface $em) {
        $this->personalActivityRepository = $personalActivityEntityRepository;
        $this->scheduleWindowRepository = $scheduleWindowEntityRepository;
        $this->roomRepository = $roomEntityRepository;
        $this->em = $em;
        $this->date = new \DateTime('now');
        $this->first_date = self::getFirstDayofWeek($this->date);
        $this->last_date = self::getLastDayofWeek($this->date);
    }

    private static function getFirstDayofWeek(\DateTime $date): \DateTime
    {
        $week_day = $date->format('D');
        $new_date = clone $date;
        return $new_date->sub(new \DateInterval(constants::DAY_STR_FORMAT_INTERVALS[$week_day]));
    }

    private static function getLastDayofWeek(\DateTime $date): \DateTime
    {
        $first_date = self::getFirstDayofWeek($date);
        return $first_date->add(new \DateInterval(constants::DAY_STR_FORMAT_INTERVALS['Sun']));
    }

    private static function cutDateFromDatetime(\DateTimeInterface $date): \DateTime
    {
        return new \DateTime($date->format('Y-m-d'));
    }

    private static function cutHourFromDatetime(\DateTimeInterface $date): \DateTime
    {
        $hour = new \DateTime();
        return $hour->createFromFormat('G', $date->format('G'));
    }

    #[Route('/person/teacher/activity/delete/{id}', name: 'personal_activity_delete', methods: ['GET', 'DELETE'])]
    public function delete($id): Response
    {
        $person = $this->getUser();
        if($person !== null) {
            if ($person->getRoles()[0] === 'ROLE_TEACHER') {
                $personal_activity = $this->personalActivityRepository->find($id);
                if ($personal_activity !== null) {
                    $Windows = $this->scheduleWindowRepository->findBy(['PersonalActivity' => $personal_activity]);
                    foreach ($Windows as $window) {
                        $this->em->remove($window);
                    }
                    $this->em->remove($personal_activity);
                    $this->em->flush();
                }
                return $this->redirectToRoute('personal_activity');
            }
            return $this->render('personal_activity/AccessDenied.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }


    #[Route('/person/teacher/activity/edit/{id}', name: 'personal_activity_delete')]//, methods: ['GET', 'DELETE'])]
    public function edit($id, Request $request): Response
    {
        $person = $this->getUser();
        if($person !== null) {
            if($person->getRoles()[0] === 'ROLE_TEACHER') {
                $personal_activity = $this->personalActivityRepository->find($id);
                if($personal_activity !== null){
                    $windows = $this->scheduleWindowRepository->findby(['PersonalActivity' => $personal_activity]);
                     // load data to form from database which are not mapped by form automatically
                    $form = $this->createForm(PersonalActivityFormType::class, $personal_activity);
                    if($personal_activity->getRoom() !== null)
                        $form->get('Room')->setData($personal_activity->getRoom()->getId());
                    $form->get('Date')->setData(self::cutDateFromDatetime($windows[0]->getStart()));
                    $form->get('Time_from')->setData(self::cutHourFromDatetime($windows[0]->getStart()));
                    $form->get('Time_to')->setData(self::cutHourFromDatetime($windows[0]->getEnd()));

                    $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $from = $form->get('Time_from')->getData();
                        $to = $form->get('Time_to')->getData();
                        if($from >= $to){
                            return $this->render('personal_activity/edit.html.twig', [
                                'form' => $form->createView(),
                                'warning' => '"Time from" is bigger or equal to "Time to".'
                            ]);
                        }
                        // todo validation $this->scheduleWindowRepository->getWindowsForRoomBetween()
                        if($form->get('Description')->getData() !== null)
                            $personal_activity->setDescription($form->get('Description')->getData());
                        $personal_activity->setPerson($person);
                        $length = $to->format('G');
                        $length -= $from->format('G');
                        $personal_activity->setLength($length);
                        $room_id = $form->get('Room')->getData();
                        // validation of room exists
                        if($room_id !== 'none' && $room_id !== null) {
                            $room = $this->roomRepository->find($room_id);
                            if ($room === null) {
                                return $this->render('personal_activity/edit.html.twig', [
                                    'form' => $form->createView(),
                                    'warning' => 'Room does not exist.'
                                ]);
                            }
                            $personal_activity->setRoom($room);
                        }
                        else{
                            $personal_activity->setRoom(null);
                        }
                        $repetition = $form->get('Repetition')->getData();
                        if($repetition === '')
                            $repetition = constants::REPETITIONS['none'];
                        $personal_activity->setRepetition($repetition);

                        // creation of new schedule windows
                        $date = clone $form->get('Date')->getData();
                        $date->add(constants::getHourInterval($from->format('G')));
                        $end_date = clone $date;
                        $end_date->add(constants::getHourInterval($length));
                        $weeks_left = constants::getWeeksLeft($repetition, $date);

                        for($i = 0; $i < $weeks_left; $i++) {
                            if((constants::isEvenWeek($date) && $repetition === 'even')
                                || (!constants::isEvenWeek($date) && $repetition === 'odd')
                                || $repetition === 'weekly'
                                || $repetition === 'none') {
                                $Window = new ScheduleWindowEntity();
                                $Window->setPersonalActivity($personal_activity);
                                $Window->setStart(clone $date);
                                $Window->setEnd(clone $end_date);
                                $this->em->persist($Window);
                            }
                            $date->add(constants::getWeekInterval(1));
                            $end_date->add(constants::getWeekInterval(1));
                        }
                        foreach($windows as $window){
                            $this->em->remove($window);
                        }
                        $this->em->flush();
                        // return to page with personal activities
                        return $this->redirectToRoute('personal_activity');
                    }
                    return $this->render('personal_activity/edit.html.twig', [
                        'form' => $form->createView(),
                        'warning' => ''
                    ]);

                }
                return $this->redirectToRoute('personal_activity');
            }
            return $this->render('personal_activity/AccessDenied.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }


    #[Route('/person/teacher/activity/create', name: 'personal_activity_create')]
    public function create(Request $request): Response
    {
        $person = $this->getUser();
        if($person !== null) {
            if($person->getRoles()[0] === 'ROLE_TEACHER') {
                $form = $this->createForm(PersonalActivityFormType::class);

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $personal_activity = new PersonalActivityEntity();

                    $from = $form->get('Time_from')->getData();
                    $to = $form->get('Time_to')->getData();
                    if($from >= $to){
                        return $this->render('personal_activity/create.html.twig', [
                            'form' => $form->createView(),
                            'warning' => '"Time from" is bigger or equal to "Time to".'
                        ]);
                    }
                    // todo validation $this->scheduleWindowRepository->getWindowsForRoomBetween()

                    if($form->get('Description')->getData() !== null)
                        $personal_activity->setDescription($form->get('Description')->getData());
                    $personal_activity->setPerson($person);
                    $length = $to->format('G');
                    $length -= $from->format('G');
                    $personal_activity->setLength($length);
                    $room_id = $form->get('Room')->getData();
                    // validation of room exists
                    if($room_id !== 'none') {
                        $room = $this->roomRepository->find($room_id);
                        if ($room === null) {
                            return $this->render('personal_activity/create.html.twig', [
                                'form' => $form->createView(),
                                'warning' => 'Room does not exist.'
                            ]);
                        }
                        $personal_activity->setRoom($room);
                    }
                    $repetition = $form->get('Repetition')->getData();
                    if($repetition === '')
                        $repetition = constants::REPETITIONS['none'];
                    $personal_activity->setRepetition($repetition);

                    $date = $form->get('Date')->getData();
                    $date->add(constants::getHourInterval($from->format('G')));
                    $end_date = clone $date;
                    $end_date->add(constants::getHourInterval($length));
                    $weeks_left = constants::getWeeksLeft($repetition, $date);

                    for($i = 0; $i < $weeks_left; $i++) {
                        if((constants::isEvenWeek($date) && $repetition === 'even')
                            || (!constants::isEvenWeek($date) && $repetition === 'odd')
                            || $repetition === 'weekly'
                            || $repetition === 'none') {
                            $Window = new ScheduleWindowEntity();
                            $Window->setPersonalActivity($personal_activity);
                            $Window->setStart(clone $date);
                            $Window->setEnd(clone $end_date);
                            $this->em->persist($Window);
                        }
                        $date->add(constants::getWeekInterval(1));
                        $end_date->add(constants::getWeekInterval(1));
                    }
                    $this->em->persist($personal_activity);
                    $this->em->flush();

                    return $this->redirectToRoute('personal_activity');
                }
                return $this->render('personal_activity/create.html.twig', [
                    'form' => $form->createView(),
                    'warning' => ''
                ]);
            }
            return $this->render('personal_activity/AccessDenied.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }
    
    #[Route('/person/teacher/activity', name: 'personal_activity')]
    public function index(): Response
    {
        $person = $this->getUser();
        if($person !== null) {
            if($person->getRoles()[0] === 'ROLE_TEACHER') {
                $personId = $person->getId();
                $activities = $this->personalActivityRepository->findBy(['Person' => $personId]);
                $windows = [];
                foreach ($activities as $activity) {
                    $windows[$activity->getId()] = $this->scheduleWindowRepository->findBy(['PersonalActivity' => $activity->getId()]);
                }

                return $this->render('personal_activity/index.html.twig', [
                    'activities' => $activities,
                    'windows' => $windows,
                ]);
            }
            return $this->render('personal_activity/AccessDenied.html.twig');
        }
        return $this->redirectToRoute('app_login');
    }
}
