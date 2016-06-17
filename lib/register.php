<?php
namespace TheFancyRobot\RSVP;

include('../header.php');


$user = new User();

//Get user info from POST array
$username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
$password = trim($_POST['password']);
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$email = trim($_POST['email']);
$userPhone = trim($_POST['userphone']);

//Enter info into DB
$user->createUser($username, $password, $firstname, $lastname, $email, $userPhone);


//Session Variables
$sessionArray = array('username' => $username, 'firstname' => $firstname, 'lastname' => $lastname);
Session::createSession($sessionArray);

include('../footer.php');
?>
