<?php

namespace TheFancyRobot\RSVP;

ini_set('display_errors', 1);

require '../bootstrap.php';


$user = new User();

$username = filter_var(trim($_GET['username']), FILTER_SANITIZE_STRING);

if ($user->checkUser($username)) {
    echo json_encode(1);
} else {
    echo json_encode(0);
}