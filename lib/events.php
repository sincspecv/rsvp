<?php
namespace TheFancyRobot\RSVP;

include('../bootstrap.php');

$event = new Event();

$eventInfo = $event->getEventInfo();

echo json_encode($eventInfo);
