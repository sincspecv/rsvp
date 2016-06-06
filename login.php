<?php
namespace TheFancyRobot\RSVP;

session_start();

ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

$username = strtolower(trim($_POST['username']));
$password = $_POST['password'];

$user = new User();
$userInfo = $user->GetUserInfo($username);

//Check for valid username
if (!$userInfo) {
    echo '<script language="javascript">';
    echo 'alert("Wrong username or password.");';
    echo 'location.href="login.htm";';
    echo '</script>';
} else {
    //Fetch username, password, and salt from db and return as string
    $dbUsername = $userInfo['username'];
    $dbPassword = $userInfo['password'];
    $salt = $userInfo['salt'];

    $login = $user->login($username, $dbUsername, $password, $dbPassword, $salt);

    if ($login = TRUE) {
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
