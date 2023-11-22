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
        // create users
        $user_teacher = new User();
        $user_teacher->setLogin('xlogin00');
        $user_teacher->setPassword('xpassword00');
        $user_teacher->setEmail('omegalul@gmail.com');
        $user_teacher->setRole('teacher');

        $user_admin = new User();
        $user_admin->setLogin('xspage12');
        $user_admin->setPassword('AdMiNaDmIn');
        $user_admin->setRole('admin');
        $user_admin->setEmail('feelsOkayMan@gmail.com');

        $user_timesheeter = new User();
        $user_timesheeter->setLogin('xtimes32');
        $user_timesheeter->setPassword('time32sheet');
        $user_timesheeter->setRole('timesheeter');
        $user_timesheeter->setEmail('pepela@gmail.com');

        $user_student = new User();
        $user_student->setLogin('xstude09');
        $user_student->setPassword('sTudent09');
        $user_student->setRole('student');
        $user_student->setEmail('peepoHappy@gmail.com');

        $class_ITW = new ClassEntity();
        $class_ITW->setName('tvorba webových aplikací');
        $class_ITW->setAbbreviation('ITW');
        $class_ITW->setAnotation('Tvorba webových aplikací je predmet o tvoreni wekových aplikací. To se vas taky bude snazit
        naucit. Budete se ucit o PHP, HTML, CSS, JS, SQL, a vsechno ostatni co se hodi k vytvoreni wekobé aplikace. glhf');
        $class_ITW->setCredits(5);

        $class_ITW->setGuarantor($user_teacher);
        $class_ITW->addPerson($user_teacher);
        $class_ITW->addPerson($user_student);

        $activity_prednaska = new ClassActivityEntity();
        $activity_prednaska->setName('prednaska');
        $activity_prednaska->setRepetition('tydne');
        $activity_prednaska->setLength(2);
        $activity_prednaska->setClass($class_ITW);
        $activity_prednaska->setTeacher($user_teacher);

        $room_D202 = new Room();
        $room_D202->setName('D202');
        $room_D202->setType('prednaskovna');
        
        $room_D202->addTeachedActivity($activity_prednaska);

        $window_act_prednaska = new ScheduleWindowEntity();
        $window_act_prednaska->setClassActivity($activity_prednaska);
        $window_act_prednaska->setStart(new \DateTime('2021-03-01 08:00:00'));

        $activity_personal = new OwnActivity();
        $activity_personal->setDescription('Setkani ohledne bakalarky se studenty.');
        $activity_personal->setRepetition('liche tydny');
        $activity_personal->setRoom($room_D202);
        $activity_personal->setPerson($user_teacher);

        $window_act_personal = new ScheduleWindowEntity();
        $window_act_personal->setPersonalActivity($activity_personal);
        $window_act_personal->setStart(new \DateTime('2021-03-01 10:00:00'));

        // users
        $manager->persist($user_teacher);
        $manager->persist($user_admin);
        $manager->persist($user_timesheeter);
        $manager->persist($user_student);
        // classes (subjects)
        $manager->persist($class_ITW);
        // activities
        $manager->persist($activity_prednaska);
        $manager->persist($activity_personal);
        // rooms
        $manager->persist($room_D202);
        // windows
        $manager->persist($window_act_prednaska);
        $manager->persist($window_act_personal);
        $manager->flush();
    }
}
