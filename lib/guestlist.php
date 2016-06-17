<?php

include('../header.php');

$event = new Event();

$eventCode = $_POST['event_code'];

$guestlist = $event->getGuestList($eventCode);

