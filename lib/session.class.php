<?php
namespace TheFancyRobot\RSVP;
use \PDO;

//Session Handling
class Session {

  public function __construct() {

  }

  private function __clone() {

  }

  public static function instance() {

    if (Self::$instance === NULL)
        {
            // Create a new instance
            new Self;
        }

        return Self::$instance;
  }

  public static function createSession($userInfoArray) {

    //Set session data
    $_SESSION['username'] = $userInfoArray['username'];
    $_SESSION['first_name'] = $userInfoArray['firstname'];
    $_SESSION['last_name'] = $userInfoArray['lastname'];
    $_SESSION['event_code'] = $userInfoArray['event_code'];

    //Store HTTP_USER_AGENT for verification
    $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
  }

  public static function verifySession() {
    //Verify session data has been defined and HTTTP_USER_AGENT matches
    if (!isset($_SESSION['agent']) || $_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
      session_destroy();
      header("Location: " . $url . "index.php");
      die("Please try logging in again");
    } 
  }
}