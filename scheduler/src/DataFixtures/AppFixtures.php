<?php

namespace App\DataFixtures;

use App\Entity\ClassActivityEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\PersonEntity as User;
use App\Entity\ClassEntity;
use App\Entity\RoomEntity as Room;
use App\Entity\ScheduleWindowEntity;
use App\Entity\PersonalActivityEntity as OwnActivity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setLogin('xlogin00');
        $user->setPassword('xpassword00');
        $user->setEmail('omegalul@gmail.com');
        $user->setRole('teacher');

        $class = new ClassEntity();
        $class->setName('tvorba webových aplikací');
        $class->setAbbreviation('ITW');
        $class->setAnotation('Tvorba webových aplikací je predmet o tvoreni wekových aplikací. To se vas taky bude snazit
        naucit. Budete se ucit o PHP, HTML, CSS, JS, SQL, a vsechno ostatni co se hodi k vytvoreni wekobé aplikace. glhf');
        $class->setGuarantor($user);
        $class->setCredits(5);

        $activity = new ClassActivityEntity();
        $activity->setName('prednaska');
        $activity->setRepetition('tydne');
        $activity->setLength(2);
        $activity->setClass($class);
        $activity->setTeacher($user);
        
        $room = new Room();
        $room->setName('D202');
        $room->setType('prednaskovna');
        $room->addTeachedActivity($activity);

        $window1 = new ScheduleWindowEntity();
        $window1->setClassActivity($activity);
        $window1->setStart(new \DateTime('2021-03-01 08:00:00'));

        $personalActivity = new OwnActivity();
        $personalActivity->setDescription('Setkani ohledne bakalarky se studenty.');
        $personalActivity->setRepetition('liche tydny');
        $personalActivity->setRoom($room);
        $personalActivity->setPerson($user);

        $window2 = new ScheduleWindowEntity();
        $window2->setPersonalActivity($personalActivity);
        $window2->setStart(new \DateTime('2021-03-01 10:00:00'));

        $manager->persist($window1);
        $manager->persist($window2);
        $manager->persist($personalActivity);
        $manager->persist($room);
        $manager->persist($class);
        $manager->persist($user);
        $manager->persist($activity);
        $manager->flush();
    }
}
