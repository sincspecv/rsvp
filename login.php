<?php
namespace TheFancyRobot\RSVP;

include('header.php');

$username = strtolower(trim($_POST['username']));
$password = $_POST['password'];

$user = new User();
$userInfo = $user->GetUserInfo($username); //Get user info from database

//Check for valid username
if (!$userInfo) {
    echo '<script language="javascript">';
    echo 'alert("Wrong username or password.");';
    echo 'location.href="login.htm";';
    echo '</script>';
} else {
    //User info from db as array $userInfo
    $dbUsername = $userInfo['username'];
    $dbPassword = $userInfo['password'];

    $checkPassword = $user->checkPassword($password, $dbPassword);

    if ($checkPassword = TRUE) { //If password is a match, create session
        Session::createSession($userInfo);
        header("Location: " . $url . "account.php");
    } else {
        echo '<script language="javascript">';
        echo 'alert("Wrong username or password.");';
        echo 'location.href="login.htm";';
        echo '</script>';
    }
}
?>
