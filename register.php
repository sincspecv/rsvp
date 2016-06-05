<?php
namespace TheFancyRobot\RSVP;

session_start();

//Error Reporting
ini_set('display_errors', 1);

require_once('lib/config.php');
require_once('lib/classes.php');

// Check DB connection_status

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

//Redirect
header("Location: " . $url . "account.php");
?>
