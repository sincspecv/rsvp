<?php
namespace TheFancyRobot\RSVP;

ini_set('display_errors', 1);

require_once('lib/config.php');
require('lib/classes.php');

$sms = new SMS();
$exists = $sms->checkForSession('13143028446');

if ($exists == TRUE) {
	echo "TRUE";
} else {
	echo "NOT TRUE";
}

$step = $sms->getStep('13143028446');
echo $step;

$addToList = $sms->addToGuestList('13143028446');
var_dump($addToList->{'result'});
?>

