<?php
namespace TheFancyRobot\RSVP;

ini_set('display_errors', 1);

//Plivo API
require_once('bootstrap.php');
use Plivo\RestAPI;
use Respect\Validation\Validator as v;

$auth_id = $_ENV['PLIVO_ID'];
$auth_token = $_ENV['PLIVO_TOKEN'];



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
			$message = "What is your name? (First and last name, e.g. John Doe)";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		} else {
			$message = "Event not found. Please check your event code and try again.";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		}
		break;
	case 2:
		if (preg_match('/[0-9]/', $text)) { //check if input contains number so as to prevent saving event code as name
			$message = "Please enter a valid name (e.g. John Doe)";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
			break;
		} else {
			$text = ucwords($text); //Make first letter of each word upper case
			$smsSession->processStepTwo($fromNumber, $text);

			$message = "How many additional guests will you be bringing? (Numeric value only)";
			$params = array(
		        'src' => $toNumber, // Sender's phone number with country code
		        'dst' => $fromNumber, // Receiver's phone number with country code
		        'text' => $message // Your SMS text message
		    );
			$sms = $response->send_message($params);
		}
		break;
	case 3:
		if (is_numeric($text)) {
			$smsSession->processStepThree($fromNumber, $text);

            $message = "Will you be attending? (Y or N)";
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
        if (!v::yes()->validate(ucfirst($text)) && !v::no()->validate(ucfirst($text))) {
            $message = "Please respond with a Y or N. Will you be attending?";
            $params = array(
                'src' => $toNumber, // Sender's phone number with country code
                'dst' => $fromNumber, // Receiver's phone number with country code
                'text' => $message // Your SMS text message
            );
            $sms = $response->send_message($params);
        } else {
            $smsSession->processStepFour($fromNumber, $text);
            $smsSession->smsAddToGuestList($fromNumber);

            $message = "Thank you! You are officially RSVP'd!";
            $params = array(
                'src' => $toNumber, // Sender's phone number with country code
                'dst' => $fromNumber, // Receiver's phone number with country code
                'text' => $message // Your SMS text message
            );
            $sms = $response->send_message($params);
        }
        break;
	case 5:
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