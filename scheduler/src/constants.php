<?php

namespace App;

class constants
{
    // it's monday
    const STR_FIRST_DAY_OF_SEMESTER = '2024-02-05';
    const STR_WEEK_INTERVAL = 'P1W';
    const TERM_LENGTH = 13;
    const DAY_STR_FORMAT_INTERVALS = ['Mon' => 'P0D', 'Tue' => 'P1D', 'Wed' => 'P2D', 'Thu' => 'P3D', 'Fri' => 'P4D', 'Sat' => 'P5D', 'Sun' => 'P6D'];
    const USER_ROLES = [
        'ROLE_ADMIN' => 'ROLE_ADMIN',
        'ROLE_GUARANTOR' => 'ROLE_GUARANTOR',
        'ROLE_TEACHER' => 'ROLE_TEACHER',
        'ROLE_SCHEDULER' => 'ROLE_SCHEDULER',
        'ROLE_STUDENT' => 'ROLE_STUDENT',
        'ROLE_ELSE' => 'ROLE_ELSE'
    ];
    const REPETITIONS = ['even' => 'even', 'odd' => 'odd', 'weekly' => 'weekly', 'none' => 'none'];

    const EMAIL_PATTERN = '/[A-Za-z0-9.-_]*@([a-z]*.[a-z]*)*/';

    public function getFirstDayOfSemester(): \DateTime
    {
        return new \DateTime(self::STR_FIRST_DAY_OF_SEMESTER);
    }

    public function getWeekInterval(): \DateInterval
    {
        return new \DateInterval(self::STR_WEEK_INTERVAL);
    }
}