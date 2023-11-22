<?php

namespace App\Controller;

use App\Entity\RoomEntity;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

// vytvoreni, detail, overview

class RoomController extends AbstractController
{
    #[Route('/rooms', name: 'rooms_list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $rooms = $doctrine->getRepository(RoomEntity::class)->findAll();

        return $this->render('room/index.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/', name: 'rooms_listing')]
    public function listRoomz(ManagerRegistry $doctrine): Response
    {
        $res = [];
        $rooms = $doctrine->getRepository(RoomEntity::class)->findAll();
        foreach ($rooms as $room) {
            $res[$room->getId()] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'type' => $room->getType(),
                'activities' => [],
            ];
            foreach ($room->getTeachedActivities() as $roomActivity) {
                $res[$room->getId()]['activities'][$roomActivity->getId()] = [
                    'name' => $roomActivity->getName(),
                    'length' => $roomActivity->getLength(),
                    'repetition' => $roomActivity->getRepetition(),
                ];
            }
        }

        return $this->render('room/index.html.twig', [
            'res' => $res,
        ]);
    }
}
