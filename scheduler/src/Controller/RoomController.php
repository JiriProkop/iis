<?php

namespace App\Controller;


use App\Entity\RoomEntity;
use App\Repository\RoomEntityRepository;
use App\Form\RoomFormType;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

// vytvoreni, detail, overview

class RoomController extends AbstractController
{
    private RoomEntityRepository $roomRepository;
    private EntityManagerInterface $em;
    public function __construct(RoomEntityRepository $roomEntityRepository, EntityManagerInterface $em)
    {
        $this->roomRepository = $roomEntityRepository;
        $this->em = $em;
    }

    #[Route('/room/admin/list', name: 'admin_rooms_list')]
    public function listRooms(): Response
    {
        $res = [];
        $rooms = $this->roomRepository->findAll();
        foreach ($rooms as $room) {
            $res[$room->getId()] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'type' => $room->getType(),
            ];
        }

        return $this->render('room/index.html.twig', [
            'res' => $res,
        ]);
    }

    #[Route('/room/admin/create', name: 'admin_room_create')]
    public function create(Request $request): Response
    {
        $room = new RoomEntity();
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRoom = $form->getData();

            $this->em->persist($newRoom);
            $this->em->flush();

            return $this->redirectToRoute('admin_rooms_list');
        }

        return $this->render('person_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/room/admin/edit/{id}', name: 'admin_room_edit')]
    public function edit($id, Request $request): Response
    {
        $room = $this->roomRepository->find($id);
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room_name = $form->get('Name')->getData();
            $room_type = $form->get('Type')->getData();
           
            $room->setName($room_name);
            $room->setType($room_type);

            $this->em->flush();
            return $this->redirectToRoute('admin_rooms_list');
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/room/admin/delete/{id}', name: 'admin_room_delete', methods: ['GET', 'DELETE'])]
    public function delete($id): Response
    {
        $room = $this->roomRepository->find($id);

        $this->em->remove($room);
        $this->em->flush();

        return $this->redirectToRoute('admin_rooms_list');
    }
}
