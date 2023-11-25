<?php
const TERM_LENGTH = 13;
const WEEK_INTERVAL = new DateInterval("P1W");
const DAY_STR_FORMAT_INTERVALS = ['Mon' => 'P0D', 'Tue' => 'P1D', 'Wed' => 'P2D', 'Thu' => 'P3D', 'Fri' => 'P4D', 'Sat' => 'P5D', 'Sun' => 'P6D'];
const USER_ROLES = [
    'ROLE_ADMIN' => 'ROLE_ADMIN',
    'ROLE_GUARANTOR' => 'ROLE_GUARANTOR',
    'ROLE_TEACHER' => 'ROLE_TEACHER',
    'ROLE_SCHEDULER' => 'ROLE_SCHEDULER',
    'ROLE_STUDENT' => 'ROLE_STUDENT',
    'ROLE_ELSE' => 'ROLE_ELSE'
];