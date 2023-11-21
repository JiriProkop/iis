<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\UserEntity as User;
use App\Entity\ClassEntity;
use App\Entity\UserInClass;
use App\Entity\TeachingActivity;
use App\Entity\Room;
use App\Entity\RoomActivity;
use App\Entity\SchedulerWindow;
use App\Entity\OwnActivity;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setLogin('xlogin00');
        $user->setPassword('xpassword00');
        $user->setUserEntityRole('teacher');

        $class = new ClassEntity();
        $class->setName('tvorba webových aplikací');
        $class->setAbbr('ITW');
        $class->setAnotation('Tvorba webových aplikací je predmet o tvoreni wekových aplikací. To se vas taky bude snazit
        naucit. Budete se ucit o PHP, HTML, CSS, JS, SQL, a vsechno ostatni co se hodi k vytvoreni wekobé aplikace. glhf');
        $class->setCreditNumber(5);

        $userInClass = new UserInClass();
        $userInClass->setUserEntity($user);
        $userInClass->setClass($class);
        $userInClass->setRelationshipType('garant');

        $activity = new TeachingActivity();
        $activity->setName('prednaska');
        $activity->setRepetition('tydne');
        $activity->setLength(2);
        $activity->setClass($class);
        $activity->setTeacher($user);

        $room = new Room();
        $room->setName('D202');
        $room->setType('prednaskovna');
        
        $roomActivity = new RoomActivity();
        $roomActivity->setActivity($activity);
        $roomActivity->setRoom($room);

        $window1 = new SchedulerWindow();
        $window1->setTeachingActivity($activity);
        $window1->setStart(new \DateTime('2021-03-01 08:00:00'));

        
        $personalActivity = new OwnActivity();
        $personalActivity->setDescription('Setkani ohledne bakalarky se studenty.');
        $personalActivity->setRepetition('liche tydny');
        $personalActivity->setRoom($room);
        $personalActivity->setTeacher($user);

        $window2 = new SchedulerWindow();
        $window2->setPersonalActivity($personalActivity);
        $window2->setStart(new \DateTime('2021-03-01 10:00:00'));

        $manager->persist($window1);
        $manager->persist($window2);
        $manager->persist($personalActivity);
        $manager->persist($roomActivity);
        $manager->persist($room);
        $manager->persist($userInClass);
        $manager->persist($class);
        $manager->persist($user);
        $manager->persist($activity);
        $manager->flush();
    }
}
