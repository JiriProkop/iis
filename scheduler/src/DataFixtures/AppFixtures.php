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
    private UserPasswordHasherInterface $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    private function set_hashed_password(string $pwd, User $user): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $pwd
            )
        );
    }

    public function load(ObjectManager $om): void
    {

        // create users
        $user_teacher = new User();
        $user_teacher->setLogin('xteach00');
        $this->set_hashed_password('xlogin00', $user_teacher);
        $user_teacher->setEmail('omegalul@gmail.com');
        $user_teacher->setRoles(['ROLE_TEACHER']);

        $user_admin = new User();
        $user_admin->setLogin('xspage12');
        $this->set_hashed_password('adminos', $user_admin);
        $user_admin->setRoles(['ROLE_ADMIN']);
        $user_admin->setEmail('feelsOkayMan@gmail.com');

        $user_timesheeter = new User();
        $user_timesheeter->setLogin('xtimes32');
        $this->set_hashed_password('time2sheet', $user_timesheeter);
        $user_timesheeter->setRoles(['ROLE_SCHEDULER']);
        $user_timesheeter->setEmail('pepela@gmail.com');

        $user_student = new User();
        $user_student->setLogin('xstude09');
        $this->set_hashed_password('sTudent09', $user_student);
        $user_student->setRoles(['ROLE_STUDENT']);
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
        $activity_prednaska->setRepetition('weekly');
        $activity_prednaska->setLength(2);
        $activity_prednaska->setDraft(false);
        $activity_prednaska->setClass($class_ITW);
        $activity_prednaska->setTeacher($user_teacher);

        $room_D202 = new Room();
        $room_D202->setName('D202');
        $room_D202->setType('prednaskovna');

        $room_D203 = new Room();
        $room_D203->setName('D203');
        $room_D203->setType('prednaskovna');

        $room_D202->addTeachedActivity($activity_prednaska);

        $window_act_prednaska = new ScheduleWindowEntity();
        $window_act_prednaska->setClassActivity($activity_prednaska);
        $window_act_prednaska->setStart(new \DateTime('2024-05-01 08:00:00'));
        $window_act_prednaska->setEnd(new \DateTime('2024-05-01 10:00:00'));

        $activity_prednaska_1 = new ClassActivityEntity();
        $activity_prednaska_1->setName('prednaska_1');
        $activity_prednaska_1->setRepetition('weekly');
        $activity_prednaska_1->setLength(2);
        $activity_prednaska_1->setClass($class_ITW);
        $activity_prednaska_1->setTeacher($user_teacher);

        $room_D202->addTeachedActivity($activity_prednaska_1);

        $window_act_prednaska_1 = new ScheduleWindowEntity();
        $window_act_prednaska_1->setClassActivity($activity_prednaska_1);
        $window_act_prednaska_1->setStart(new \DateTime('2024-04-01 17:00:00'));
        $window_act_prednaska_1->setEnd(new \DateTime('2024-04-01 20:00:00'));

        // Activity 2
        $activity_prednaska_2 = new ClassActivityEntity();
        $activity_prednaska_2->setName('prednaska_2');
        $activity_prednaska_2->setRepetition('weekly');
        $activity_prednaska_2->setLength(2);
        $activity_prednaska_2->setClass($class_ITW);
        $activity_prednaska_2->setTeacher($user_teacher);

        $room_D203->addTeachedActivity($activity_prednaska_2);

        $window_act_prednaska_2 = new ScheduleWindowEntity();
        $window_act_prednaska_2->setClassActivity($activity_prednaska_2);
        $window_act_prednaska_2->setStart(new \DateTime('2024-02-13 13:00:00'));
        $window_act_prednaska_2->setEnd(new \DateTime('2024-02-13 19:00:00'));


        $activity_personal = new OwnActivity();
        $activity_personal->setDescription('Setkani ohledne bakalarky se studenty.');
        $activity_personal->setRepetition('odd');
        $activity_personal->setRoom($room_D202);
        $activity_personal->setPerson($user_teacher);
        $activity_personal->setLength(8);

        $window_act_personal = new ScheduleWindowEntity();
        $window_act_personal->setPersonalActivity($activity_personal);
        $window_act_personal->setStart(new \DateTime('2024-03-01 10:00:00'));
        $window_act_personal->setEnd(new \DateTime('2024-03-01 12:00:00'));

        $window_act_personal2 = new ScheduleWindowEntity();
        $window_act_personal2->setPersonalActivity($activity_personal);
        $window_act_personal2->setStart(new \DateTime('2024-02-05 10:00:00'));
        $window_act_personal2->setEnd(new \DateTime('2024-02-05 18:00:00'));

        $om->persist($user_teacher);
        $om->persist($user_admin);
        $om->persist($user_timesheeter);
        $om->persist($user_student);

        $om->persist($class_ITW);

        $om->persist($activity_prednaska);
        $om->persist($activity_personal);
        $om->persist($activity_prednaska_1);
        $om->persist($activity_prednaska_2);
        

        $om->persist($room_D202);
        $om->persist($room_D203);

        $om->persist($window_act_prednaska);
        $om->persist($window_act_personal);
        $om->persist($window_act_personal2);
        $om->persist($window_act_prednaska_1);
        $om->persist($window_act_prednaska_2);

        $om->flush();
    }
}
