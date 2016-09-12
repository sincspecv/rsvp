<?php
namespace TheFancyRobot\RSVP;

include('header.php');

if (empty($_POST) || $_POST['id'] != md5($_SERVER['HTTP_USER_AGENT'])) { //Make sure form was filled out and prevent cross site submission
    header('location: ' . $url . 'index.php');
    die();
}

$username = strtolower(trim($_POST['username']));
$password = $_POST['password'];

$user = new User();

$checkUser = $user->checkUser($username); //check if user exists

if (!$checkUser) {
    echo '<script language="javascript">';
    echo 'alert("Wrong username or password.");';
    echo 'location.href="index.php";';
    echo '</script>';
} else {
    $userInfo = $user->GetUserInfo($username); //Get user info from database

    $dbUsername = $userInfo['username'];
    $dbPassword = $userInfo['password'];

    $checkPassword = $user->checkPassword($password, $dbPassword);

    if ($checkPassword = TRUE) { //If password is a match, create session
        Session::createSession($userInfo);
        header("Location: " . $url . "account.php");
    } else {
        echo '<script language="javascript">';
        echo 'alert("Wrong username or password.");';
        echo 'location.href="index.php";';
        echo '</script>';
    }
}
?>
