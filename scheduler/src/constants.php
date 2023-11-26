<?php

namespace App;

class constants
{
    // it's monday
    const STR_FIRST_DAY_OF_SEMESTER = '2024-02-05';
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
    const REPETITIONS = ['none' => 'none', 'even' => 'even', 'odd' => 'odd', 'weekly' => 'weekly'];

    public static function getFirstDayOfSemester(): \DateTime
    {
        return new \DateTime(self::STR_FIRST_DAY_OF_SEMESTER);
    }

    public static function getWeekInterval(int $weeks): \DateInterval
    {
        return new \DateInterval('P'.$weeks.'W');
    }

    public static function getHourInterval(int $hour): \DateInterval
    {
        return new \DateInterval('PT'.$hour.'H');
    }

    public static function getIntegerRepetition(string $repetition, \DateTime $date): int
    {
        if($repetition === 'none'){
            return 1;
        }
        elseif($repetition === 'tydne'){
            $first_day_of_semester = self::getFirstDayOfSemester();
            $first_week_of_semester = intval($first_day_of_semester->format('W'));
            $week_of_date = intval($date->format('W'));
            $difference_of_weeks = $week_of_date - $first_week_of_semester;
            $last_week = $first_week_of_semester + self::TERM_LENGTH;
            return $last_week - $difference_of_weeks;
        }
        return 2;
    }
}