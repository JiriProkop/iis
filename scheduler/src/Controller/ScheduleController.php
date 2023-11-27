<?php

namespace App\Controller;

use App\Repository\ClassActivityEntityRepository;
use App\Repository\PersonalActivityEntityRepository;
use App\Repository\RoomEntityRepository;
use App\Repository\ScheduleWindowEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PersonEntity;
use App\Entity\ClassActivityEntity;
use App\Entity\ScheduleWindowEntity;
use App\Entity\PersonalActivityEntity;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\constants;

class ScheduleController extends AbstractController
{
    private ClassActivityEntityRepository $classActivityRepository;
    private RoomEntityRepository $roomRepository;
    private PersonEntityRepository $personRepository;
    private PersonalActivityEntityRepository $personalActivityRepository;
    private ScheduleWindowEntityRepository $scheduleWindowRepository;
    private EntityManagerInterface $em;
    public function __construct(PersonEntityRepository $personEntityRepository, ScheduleWindowEntityRepository $scheduleWindowRepository, PersonalActivityEntityRepository $personalActivityRepository, ClassActivityEntityRepository $classActivityRepository, RoomEntityRepository $roomEntityRepository, EntityManagerInterface $em)
    {
        $this->classActivityRepository = $classActivityRepository;
        $this->personRepository = $personEntityRepository;
        $this->roomRepository = $roomEntityRepository;
        $this->personalActivityRepository = $personalActivityRepository;
        $this->scheduleWindowRepository = $scheduleWindowRepository;
        $this->em = $em;
    }

    #[Route('/my_schedule/{id}', name: 'my_schedule')]
    public function index($id, SessionInterface $session): Response
    {
        $personalActivities = $this->personRepository->find($id)->getPersonalActivities();
        $classActivities = $this->personRepository->find($id)->getClassActivities();
        $res = [];
        $selectedWeek = $session->get('selected_week', 1);

        $res = $this->fill_with_activities($res, $personalActivities, $session);
        $res = $this->fill_with_activities($res, $classActivities, $session);
        return $this->render('schedule/index.html.twig', [
            'activities' => $res,
            'selectedWeek' => $selectedWeek,
        ]);
    }


    #[Route('/schedule/remove_activity/{id}', name: 'schedule_remove_activity')]
    public function unscheduleActivity($id, SessionInterface $session): Response
    {
        $activity = $this->classActivityRepository->find($id);
        $windows = $activity->getScheduledWindows();
        foreach ($windows as $window) {
            $this->em->remove($window);
        }
        $this->em->flush();
        return $this->redirectToRoute('make_schedule');
    }


    #[Route('/make_schedule', name: 'make_schedule')]
    public function make_schedule(SessionInterface $session): Response
    {
        $all_rooms = $this->roomRepository->findAll();
        $selected_room = $session->get('selected_room', $all_rooms[0]->getName());
        $session->set('selected_room', $selected_room); // some value need to be set in session


        // $personalActivities = $this->personalActivityRepository->findAll();

        $classActivities = $this->classActivityRepository->findAll();
        // filter by room
        foreach ($classActivities as $activity) {

            $flag = false;
            foreach ($activity->getRooms() as $room) {
                if ($room->getName() == $selected_room) {
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                unset($classActivities[array_search($activity, $classActivities)]);
            }
        }

        $activities_without_windows = $this->activitiesWithoutWindows($classActivities);

        $res = [];
        $odd = (int) $session->get('odd', 1);
        $const = new constants();
        $first_week = $const->getFirstDayOfSemester();
        $week = ((int) $first_week->format('W'));
        if ($odd == -1) {
            if ($week % 2 == 0) {
                // first week's even
                $week = 2;
            } else {
                // first week's odd
                $week = 1;
            }
        } else if ($odd == 0) {
            if ($week % 2 == 0) {
                // first week's even
                $week = 1;
            } else {
                // first week's odd
                $week = 2;
            }
        } else {
            $week = $odd;
        }

        $session->set('selected_week', $week);

        $res = $this->fill_with_activities($res, $classActivities, $session);

        $rooms_info = [];
        foreach ($all_rooms as $room) {
            $rooms_info[$room->getId()] = [
                'name' => $room->getName(),
                'id' => $room->getId(),
            ];
        }
        $unscheduled = [];
        foreach ($activities_without_windows as $activity) {
            $unscheduled[$activity->getId()] = [
                'id' => $activity->getId(),
                'name' => $activity->getName(),
                'length' => $activity->getLength(),
            ];
        }

        $error = $session->get('error_msg', '');
        var_dump($session->get('collision_cause_href', ""));

        return $this->render('schedule/create.html.twig', [
            'activities' => $res,
            'activities_without_windows' => $unscheduled,
            'selectedRoom' => $selected_room,
            'oddness' => $odd,
            'rooms' => $rooms_info,
            'error' => $error,
            // 'error_href' => $session->get('collision_cause_href', ""),
        ]);
    }

    private function activitiesWithoutWindows($activities)
    {
        $res = [];
        foreach ($activities as $activity) {
            if (count($activity->getScheduledWindows()) == 0) {
                $res[] = $activity;
            }
        }
        return $res;
    }


    #[Route('/schedule/roomSelect', name: 'update_room_make_schedule')]
    public function roomSelect(Request $request, SessionInterface $session)
    {
        $room = $request->request->get('roomName');

        // Store the selected week in the session
        $session->set('selected_room', $room);

        return $this->redirectToRoute('make_schedule');
    }

    #[Route('/schedule/activitySelect', name: 'select_activity_make_schedule')]
    public function activitySelect(Request $request, SessionInterface $session)
    {
        $error = '';
        $data = $request->request->get('selected_activity');
        $data = str_getcsv($data, ',');
        $act_id = (int) $data[0];
        $day = trim($data[1]);
        $hour = trim($data[2]);

        var_dump($act_id);
        var_dump($day);
        $activity = $this->classActivityRepository->find($act_id);


        if ($activity->getRepetition() == 'ONE_TIME') {
            // if the activity is only a one time thing:
            $newSchedule = new ScheduleWindowEntity();
            // set start
            $datetime = constants::getFirstDayOfSemester();

            $user_selected_week = (int) $session->get('selected_week', 1);
            $datetime->add(new \DateInterval('P' . ($user_selected_week - 1) . 'W'));
            $day = constants::DAY_STR_FORMAT_INTERVALS[substr($day, 0, 3)];
            $datetime->add(new \DateInterval($day));
            $datetime->add(new \DateInterval('PT' . $hour . 'H'));

            $start = $datetime;
            $newSchedule->setStart(new \DateTime($start->format('Y-m-d H:i:s')));
            // set end
            $length = $activity->getLength();
            $start->modify("+$length hour");
            $newSchedule->setEnd(new \DateTime($start->format('Y-m-d H:i:s')));

            if (!$this->check_collisions($newSchedule, $activity, $session)) {
                $error = $session->get('collision_cause', "Collision!");
                $session->set('error_msg', $error);
                return $this->redirectToRoute('make_schedule');
            }
            $this->em->persist($newSchedule);
            $activity->addScheduledWindow($newSchedule);
        } else {
            // if the activity is repeating:

            // get first day of semester
            $datetime = constants::getFirstDayOfSemester();

            // and add the offset based on the chosen day
            $offsetDays = constants::DAY_STR_FORMAT_INTERVALS[substr($day, 0, 3)];
            $datetime->add(new \DateInterval($offsetDays));

            // and based of the chosen hour
            $offsetSeconds = $hour * 60 * 60;

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

                if (!$this->check_collisions($newSchedule, $activity, $session)) {
                    $error = $session->get('collision_cause', "Collision!");
                    $session->set('error_msg', $error);
                    return $this->redirectToRoute('make_schedule');
                }
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

        $session->set('error_msg', $error);

        return $this->redirectToRoute('make_schedule');
    }

    private function check_collisions($new_window, $activity, $session): bool
    {
        $all_windows = $this->scheduleWindowRepository->findAll();
        $new_length = $activity->getLength();
        $new_window_end = clone $new_window->getStart();
        $new_window_end->add(new \DateInterval('PT' . $new_length . 'H'));

        foreach ($all_windows as $window) {
            if($window->getClassActivity() == null){
                continue;
            }
            $length = $window->getClassActivity()->getLength();
            $window_end = clone $window->getStart();
            $window_end->add(new \DateInterval('PT' . $length . 'H'));

            if ($window->getStart() < $new_window_end && $window_end > $new_window->getStart()) {
                $user = $window->getClassActivity()->getTeacher();
                if($user == null){
                    $href = '';
                    $session->set('collision_cause_href', $href);
                }else{
                    $href = "<a href='/my_schedule/". $user->getId() . "'>COLLIDING SCHEDULE</a>";
                    $session->set('collision_cause_href', $href);
                }
                $session->set('collision_cause', "Collision with activity: " . $window->getClassActivity()->getName() . " ");
                return false;
            }
        }
        return true;
    }

    #[Route('/schedule/oddnessSelect', name: 'oddevenweek_make_schedule')]
    public function oddnessSelect(Request $request, SessionInterface $session)
    {
        $odd = $request->request->get('oddness');

        // Store the selected week in the session
        $session->set('odd', $odd);

        return $this->redirectToRoute('make_schedule');
    }


    #[Route('/schedule/weekSelect', name: 'update_week')]
    public function weekSelect(Request $request, SessionInterface $session)
    {
        $weekNumber = $request->request->get('weekNumber');

        // Store the selected week in the session
        $session->set('selected_week', $weekNumber);

        return $this->redirectToRoute('my_schedule', ['id' => $this->getUser()->getId()]);
    }

    private function include_week($datetime, SessionInterface $session): bool
    {
        $week = ((int) $datetime->format('W'));

        $const = new constants();
        $selected_week = $const->getFirstDayOfSemester();
        $user_selected_week = (int) $session->get('selected_week', 1);
        $selected_week->add(new \DateInterval('P' . ($user_selected_week - 1) . 'W'));
        $selected_week = ((int) $selected_week->format('W'));

        if ($week === $selected_week) {
            return true;
        }

        return false;
    }

    private function fill_with_activities($res, $activities, SessionInterface $session)
    {
        foreach ($activities as $activity) {
            foreach ($activity->getScheduledWindows() as $window) {
                $start_time = $window->getStart();
                // is this activity in selected week?
                if (!$this->include_week($start_time, $session)) {
                    continue;
                }

                $day = $start_time->format('l');
                $hour = (int) $start_time->format('G');
                if (!isset($res[$day])) {
                    $res[$day] = [];
                }
                if (!isset($res[$day][$hour])) {
                    $res[$day][$hour] = [];
                }
                for ($i = 0; $i < $activity->getLength(); $i++) {
                    $res[$day][$hour][$activity->getId()] = [
                        'id' => $activity->getId(),
                        'name' => $activity->getName(),
                        'rooms' => $activity->getRooms(),
                    ];
                    $hour += 1;
                }
            }
        }
        return $res;
    }
}
