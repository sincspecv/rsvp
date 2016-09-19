<?php
namespace TheFancyRobot\RSVP;
use Respect\Validation\Validator as v;

session_start();

ini_set('display_errors', 1);

include('../header.php');

Session::verifySession();

$event = new Event();

//Make sure there are no empty values in $_POST except for second host (Angular omits empty inputs)
$postData = $_POST;
unset($postData['sHostFirstName'], $postData['sHostLastName']); // $postData contanis all key value pairs submitted by angular which can't be empty
$control = array('pHostFirstName' => '', 'pHostLastName' => '', 'eventName' => '', 'eventDate' => ''); //$control contains all keys required for submission

//compare the arrays to ensure all data was submitted.
$intersect = array_intersect_key($postData, $control);
if (count($intersect) != count($control)) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    die();
}




$pHostFirstName = filter_var(trim($_POST['pHostFirstName']), FILTER_SANITIZE_STRING);
$pHostLastName = filter_var(trim($_POST['pHostLastName']), FILTER_SANITIZE_STRING);
$sHostFirstName = filter_var(trim($_POST['sHostFirstName']), FILTER_SANITIZE_STRING);
$sHostLastName = filter_var(trim($_POST['sHostLastName']), FILTER_SANITIZE_STRING);
$eventName = filter_var($_POST['eventName'], FILTER_SANITIZE_STRING);
$eventDate = date('Y-m-d', strtotime($_POST['eventDate']));

//validate date entry is actually a date
if (!v::date()->validate($eventDate)) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    die();
}

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

header('HTTP/1.1 200 OK', true, 200);

?>
