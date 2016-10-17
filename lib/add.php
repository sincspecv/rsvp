<?php
namespace TheFancyRobot\RSVP;

require_once('../bootstrap.php');
use Respect\Validation\Validator as v;

$event = new Event();

if ($_POST['method'] == 'add') {

    $guestName = filter_var(trim($_POST['guestName']), FILTER_SANITIZE_STRING);
    $addGuest = trim($_POST['addGuest']);
    $eventCode = filter_var(trim($_POST['eventCode']), FILTER_SANITIZE_STRING);
    $attending = filter_var(trim($_POST['attending']), FILTER_SANITIZE_STRING);

    //Make sure $attending is valid Y or N
    if (!v::yes()->validate(ucfirst($attending)) && !v::no()->validate(ucfirst($attending))) {
        echo "\$attending invalid";
        header ('\$attending invalid');
    } else if (v::yes()->validate(ucfirst($attending))) {
        $attending = "Y";
    } else if (v::no()->validate(ucfirst($attending))) {
        $attending = "N";
    }
    //insert into db
    if (!$event->addToGuestList($eventCode, $guestName, $addGuest)) {
        echo "Method Failed";
        header('Method Failed', true, 400);
    }

} elseif ($_POST['method'] == 'remove') {
    $eventCode = filter_var(trim($_POST['eventCode']), FILTER_SANITIZE_STRING);
    $rowId = filter_var(trim($_POST['id']), FILTER_SANITIZE_NUMBER_INT);

    //Remove from db
    if (!$event->removeFromGuestList($eventCode, $rowId)) {
       echo "Method Failed";
       header('Method Failed', true, 400);
   }
} else {
    echo "Post Failed";
    header('Post Failed', true, 400);
    die();
}