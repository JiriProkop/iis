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

    // #[Route('/room/admin/list', name: 'admin_rooms_list')]
    #[Route('/', name: 'admin_rooms_list')]
    public function listRooms(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $res = [];
        $rooms = $this->roomRepository->findAll();
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
                    'id' => $roomActivity->getId(),
                ];
            }
        }

        return $this->render('room/index.html.twig', [
            'res' => $res,
            'user_logged' => false,
        ]);
    }

    #[Route('/room/admin/create', name: 'admin_room_create')]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $person = new RoomEntity();
        $form = $this->createForm(RoomFormType::class, $person);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPerson = $form->getData();

            // TODO validace dat zde $form->get('...');

            $this->em->persist($newPerson);
            $this->em->flush();

            return $this->redirectToRoute('admin_rooms_list');
        }

        return $this->render('person/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/room/admin/edit/{id}', name: 'admin_room_edit')]
    public function edit($id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $room = $this->roomRepository->find($id);
        $form = $this->createForm(RoomFormType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // TODO validace dat zde $form->get('...');

            $room->setName($form->get('Name')->getData());
            $room->setType($form->get('Type')->getData());

            $this->em->flush();
            return $this->redirectToRoute('admin_rooms_list');
        }

        return $this->render('room/edit.html.twig', [
            'room' => $room,
            'user_logged' => false,
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

    // public function permit_access(): bool
    // {
    //     $permitted_role = 'ROLE_ADMIN';
    //     $session = $this->container->get('request_stack')->getSession();
    //     $roles = $session->get('user')->getRoles();
    //     $name = $session->get('user')->getEmail();
    //     var_dump($name);
    //     if (in_array($permitted_role, $roles, true)) {
    //         return true;
    //     }
    //     return false;

    // }
}
