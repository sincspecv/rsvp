<?php
namespace TheFancyRobot\RSVP;

session_start();

ini_set('display_errors', 1);

include('../header.php');

Session::verifySession();

$event = new Event();

$pHostFirstName = filter_var(trim($_POST['pHostFirstName']), FILTER_SANITIZE_STRING);
$pHostLastName = filter_var(trim($_POST['pHostLastName']), FILTER_SANITIZE_STRING);
$sHostFirstName = filter_var(trim($_POST['sHostFirstName']), FILTER_SANITIZE_STRING);
$sHostLastName = filter_var(trim($_POST['sHostLastName']), FILTER_SANITIZE_STRING);
$eventName = filter_var($_POST['eventName'], FILTER_SANITIZE_STRING);
$eventDate = date('Y-m-d', strtotime($_POST['eventDate']));

$pHostName = $pHostFirstName . " " . $pHostLastName;
$sHostName = $sHostFirstName . " " . $sHostLastName;

$username = $_SESSION['username'];

//Make sure event code is unique
do {
  $eventCode = $event->createEventCode($pHostLastName, $sHostLastName);
  $eventCheck = $event->checkForTable($eventCode);
} while ($eventCheck == TRUE);

//Enter event info into database
$eventCode = $eventCode; //make sure $eventCode doesn't change
$event->createEvent($username, $eventCode, $pHostName, $sHostName, $eventName, $eventDate);

$_SESSION['event_codes'][] = $eventCode;
//array_push($_SESSION['event_codes'], $eventCode);

header("Location: " . $url . "account.php");

 ?>
