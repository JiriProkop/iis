<?php

namespace App\Controller;

use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Room;

// vytvoreni, detail, overview

class RoomController extends AbstractController
{
    #[Route('/rooms', name: 'rooms_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $rooms = $doctrine->getRepository(Room::class)->findAll();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/', name: 'rooms_listing')]
    public function listRoomz(ManagerRegistry $doctrine): Response
    {
        $res = [];
        $rooms = $doctrine->getRepository(Room::class)->findAll();
        foreach ($rooms as $room) {
            $room->getRoomActivities();
            $res[$room->getId()] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'type' => $room->getType(),
                'activities' => [],
            ];
            foreach ($room->getRoomActivities() as $roomActivity) {
                $res[$room->getId()]['activities'][$roomActivity->getId()] = [
                    'name' => $roomActivity->getActivity()->getName(),
                    'length' => $roomActivity->getActivity()->getLength(),
                    'repetition' => $roomActivity->getActivity()->getRepetition(),
                ];
            }
        }

        return $this->render('room/index.html.twig', [
            'res' => $res,
        ]);
    }
}
