<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PersonEntity;
use App\Entity\PersonalActivityEntity;
use App\Repository\PersonEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\constants;

class ScheduleController extends AbstractController
{
    private PersonEntityRepository $personRepository;
    private EntityManagerInterface $em;
    public function __construct(PersonEntityRepository $personEntityRepository, EntityManagerInterface $em)
    {
        $this->personRepository = $personEntityRepository;
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
            'selectedWeek' => $session->get('selected_week', 1),
        ]);
    }



    #[Route('/weekSelect', name: 'update_week')]
    public function weekSelect(Request $request, SessionInterface $session)
    {
        $weekNumber = $request->request->get('weekNumber');

        // Store the selected week in the session
        $session->set('selected_week', $weekNumber);

        return $this->redirectToRoute('my_schedule', ['id' => $this->getUser()->getId()]);
    }

    private function include_week($datetime, SessionInterface $session): bool
    {
        // var_dump($datetime , "\n");
        $week = ((int) $datetime->format('W'));

        $const = new constants();
        $selected_week = $const->getFirstDayOfSemester();
        $user_selected_week = (int) $session->get('selected_week', 1);
        $selected_week->add(new \DateInterval('P' . ($user_selected_week - 1) . 'W'));
        $selected_week = ((int) $selected_week->format('W'));

        var_dump($week == $selected_week);
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
                    $res[$day][$hour] = [
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
