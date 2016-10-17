<?php
namespace TheFancyRobot\RSVP;

include('../header.php');


$user = new User();

//Make sure there are no empty values in $_POST (Angular omits empty fields, so any omitted index will not be present in $_POST)
$control = array('username' => '', 'password' => '', 'firstname' => '', 'lastname' => '', 'email' => '', 'userphone' => ''); //$control contains all keys required for submission

//compare the arrays to ensure all data was submitted.
$intersect = array_intersect_key($_POST, $control);
if (count($intersect) != count($control)) { //if $_POST and $control do not have an equal number of indexes, that means data was omitted on the form
    header('Field Left Empty', true, 400);
    die();
}

//Get user info from POST array
$username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
$password = trim($_POST['password']);
$firstname = filter_var(trim($_POST['firstname']), FILTER_SANITIZE_STRING);
$lastname = filter_var(trim($_POST['lastname']), FILTER_SANITIZE_STRING);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$userPhone = trim($_POST['userphone']);

//clean up and verify phone number
$userPhone = preg_replace('/[^0-9]/', '', $userPhone);

if (strlen($userPhone) != 10) {
    header('Invalid Phone Number', true, 400);
    die();
}

//Enter info into DB
$user->createUser($username, $password, $firstname, $lastname, $email, $userPhone);


//Session Variables
$sessionArray = array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname, 'event_codes' => '');
Session::createSession($sessionArray);
include('../footer.php');
?>
