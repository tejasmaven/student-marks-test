<?php

function db(): mysqli
{
    $host = '127.0.0.1';
    $user = 'root';
    $pass = '';
    $name = 'school_marks';

    $mysqli = new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }

    $mysqli->set_charset('utf8mb4');
    return $mysqli;
}
