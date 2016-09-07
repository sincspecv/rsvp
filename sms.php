<?php
namespace TheFancyRobot\RSVP;

ini_set('display_errors', 1);

//Plivo API
require_once('bootstrap.php');
use Plivo\RestAPI;
use Respect\Validation;

$auth_id = "MAODLMYTLJODEYNZUZNG";
$auth_token = "ZTZmZGNjOTYwZTg5NjUzMDk3Y2MwOTM0YTFhYTFm";



// Sender's phone numer
$fromNumber = $_POST['From'];
// Receiver's phone number - Plivo number
$toNumber = $_POST["To"];
// The SMS text message which was received
$text = $_POST["Text"];

$smsSession = new SMS();
$sessionExists = $smsSession->checkForSession($fromNumber);

$response = new RestAPI($auth_id, $auth_token);

//check if user has already started rsvp process
if ($sessionExists == TRUE) {
	$step = $smsSession->getStep($fromNumber);
} else {
	$smsSession->createSMSSession($fromNumber);
	$step = $smsSession->getStep($fromNumber);
}

switch($step) {
	case 1: 
		$text = strtoupper($text);
		$eventCheck = $smsSession->checkEventCode($text);

		if ($eventCheck == TRUE) {
			$smsSession->processStepOne($fromNumber, $text);
			$message = "What is your name? (First Name, Last Name)";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		} else {
			$message = "Event not found. Please check your code and try again.";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		}
		break;
	case 2:
		$text = ucwords($text); //Make first letter of each word upper case
		$smsSession->processStepTwo($fromNumber, $text);

		$message = "How many additional guests will you be bringing? (Numeric value only)";
		$params = array(
	        'src' => $toNumber, // Sender's phone number with country code
	        'dst' => $fromNumber, // Receiver's phone number with country code
	        'text' => $message // Your SMS text message
	    );
		$sms = $response->send_message($params);
		break;
	case 3:
		if (is_numeric($text)) {
			$smsSession->processStepThree($fromNumber, $text);
			$smsSession->addToGuestList($fromNumber);

			$message = "Thank you! You are officially RSVP'd!";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		} else {
			$message = "Please enter only a numeric value (e.g. 2). How many additional guests will you be bringing?";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		}
		break; 
	case 4:
		if ($smsSession->checkIfRegistered($fromNumber, $text) == TRUE) {
			$message = "It looks like you've already RSVP'd for this event. Please try again with a different event code.";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
		    $sms = $response->send_message($params);
		} else {
			$smsSession->refreshSMSSession($fromNumber);

			$text = strtoupper($text);
			$eventCheck = $smsSession->checkEventCode($text);

			if ($eventCheck == TRUE) {
				$smsSession->processStepOne($fromNumber, $text);
				$message = "What is your name? (First Name, Last Name)";
				$params = array(
			        'src' => $toNumber, // Sender's phone number with country code
			        'dst' => $fromNumber, // Receiver's phone number with country code
			        'text' => $message // Your SMS text message
			    );
				$sms = $response->send_message($params);
			} else {
				$message = "Event not found. Please check your code and try again.";
				$params = array(
			        'src' => $toNumber, // Sender's phone number with country code
			        'dst' => $fromNumber, // Receiver's phone number with country code
			        'text' => $message // Your SMS text message
			    );
				$sms = $response->send_message($params);
			}

		}
		break;
}



?>