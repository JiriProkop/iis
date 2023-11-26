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

    #[Route('/person/teacher/activity/create', name: 'app_personal_activity_create')]
    public function create(Request $request): Response
    {
        $person = $this->getUser();
        if($person != null) {
            if($person->getRoles()[0] === 'ROLE_TEACHER') {
                $form = $this->createForm(PersonalActivityFormType::class);

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $personal_activity = new PersonalActivityEntity();
                    $Window = new ScheduleWindowEntity();

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
                    $str_room = $form->get('Room')->getData();
                    // validation of room exists
                    if($str_room !== '' && $str_room !== null) {
                        $room = $this->roomRepository->findOneBy(['Name' => $str_room]);
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

                    // create window //todo multiple windows
                    $date = $form->get('Date')->getData();
                    $date->add(constants::getHourInterval($from->format('G')));
                    echo $date->format(' Y-m-d H');
                    $Window->setPersonalActivity($personal_activity);
                    $Window->setStart($date);
                    $end_date = clone $date;
                    $end_date->add(constants::getHourInterval($length));
                    echo $date->format(' Y-m-d H');
                    $Window->setEnd($end_date);

                    $this->em->persist($personal_activity);
                    $this->em->persist($Window);
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




//    #[Route('/person/admin/create', name: 'admin_person_create')]
//    public function createcopy(Request $request, UserPasswordHasherInterface $passwordHasher): Response
//    {
//        $person = new PersonEntity();
//        $form = $this->createForm(PersonFormType::class, $person);
//
//        $form->handleRequest($request);
//        if ($form->isSubmitted() && $form->isValid()) {
              //TODO validace dat zde $form->get('...');
//
//            $newPerson = new PersonEntity();
//
//            $newPerson->setEmail($form->get('Email')->getData());
//            $newPerson->setRoles($form->get('roles')->getData());
//            $newPerson->setLogin($form->get('Login')->getData());
//
//            $hashedPassword = $passwordHasher->hashPassword(
//                $newPerson,
//                $form->get('Password')->getData()
//            );
//
//            $newPerson->setPassword($hashedPassword);
//
//            $this->em->persist($newPerson);
//            $this->em->flush();
//
//            return $this->redirectToRoute('admin_person');
//        }
//
//        return $this->render('person/create.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }




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
