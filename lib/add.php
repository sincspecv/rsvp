<?php
namespace TheFancyRobot\RSVP;

require_once('../bootstrap.php');

$event = new Event();

$guestName = filter_var(trim($_POST['guestName']), FILTER_SANITIZE_STRING);
$addGuest = trim($_POST['addGuest']);
$eventCode = filter_var(trim($_POST['eventCode']), FILTER_SANITIZE_STRING);

echo $guestName;
echo $addGuest;
echo $eventCode;

//insert into db
$event->addToGuestList($eventCode, $guestName, $addGuest);