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
        $user->setRole('teacher');

        $user_admin = new User();
        $user_admin->setLogin('xspage12');
        $user_admin->setPassword('AdMiNaDmIn');
        $user_admin->setUserEntityRole('admin');

        $user_timesheeter = new User();
        $user_timesheeter->setLogin('xtimes32');
        $user_timesheeter->setPassword('time32sheet');
        $user_timesheeter->setUserEntityRole('timesheeter');

        $user_student = new User();
        $user_student->setLogin('xstude09');
        $user_student->setPassword('sTudent09');
        $user_student->setUserEntityRole('student');

        $class_ITW = new ClassEntity();
        $class_ITW->setName('tvorba webových aplikací');
        $class_ITW->setAbbr('ITW');
        $class_ITW->setAnotation('Tvorba webových aplikací je predmet o tvoreni wekových aplikací. To se vas taky bude snazit
        naucit. Budete se ucit o PHP, HTML, CSS, JS, SQL, a vsechno ostatni co se hodi k vytvoreni wekobé aplikace. glhf');
        $class_ITW->setCreditNumber(5);

        $userInClass_teacehr_ITW = new UserInClass();
        $userInClass_teacehr_ITW->setUserEntity($user_teacher);
        $userInClass_teacehr_ITW->setClass($class_ITW);
        $userInClass_teacehr_ITW->setRelationshipType('garant');

        $userInClass_student_ITW = new UserInClass();
        $userInClass_student_ITW->setUserEntity($user_student);
        $userInClass_student_ITW->setClass($class_ITW);
        $userInClass_student_ITW->setRelationshipType('zapsal');

        $activity_prednaska = new TeachingActivity();
        $activity_prednaska->setName('prednaska');
        $activity_prednaska->setRepetition('tydne');
        $activity_prednaska->setLength(2);
        $activity_prednaska->setClass($class_ITW);
        $activity_prednaska->setTeacher($user_teacher);

        $room_D202 = new Room();
        $room_D202->setName('D202');
        $room_D202->setType('prednaskovna');
        
        $roomAct_prednaska_D202 = new RoomActivity();
        $roomAct_prednaska_D202->setActivity($activity_prednaska);
        $roomAct_prednaska_D202->setRoom($room_D202);

        $window_act_prednaska = new SchedulerWindow();
        $window_act_prednaska->setTeachingActivity($activity_prednaska);
        $window_act_prednaska->setStart(new \DateTime('2021-03-01 08:00:00'));

        $activity_personal = new OwnActivity();
        $activity_personal->setDescription('Setkani ohledne bakalarky se studenty.');
        $activity_personal->setRepetition('liche tydny');
        $activity_personal->setRoom($room_D202);
        $activity_personal->setTeacher($user_teacher);

        $window_act_personal = new SchedulerWindow();
        $window_act_personal->setPersonalActivity($activity_personal);
        $window_act_personal->setStart(new \DateTime('2021-03-01 10:00:00'));

        // users
        $manager->persist($user_teacher);
        $manager->persist($user_admin);
        $manager->persist($user_timesheeter);
        $manager->persist($user_student);
        // classes (subjects)
        $manager->persist($class_ITW);
        // userInClass
        $manager->persist($userInClass_teacehr_ITW);
        $manager->persist($userInClass_student_ITW);
        // activities
        $manager->persist($activity_prednaska);
        $manager->persist($activity_personal);
        // rooms
        $manager->persist($room_D202);
        // roomActivities
        $manager->persist($roomAct_prednaska_D202);
        // windows
        $manager->persist($window_act_prednaska);
        $manager->persist($window_act_personal);
        $manager->flush();
    }
}
