<?php
namespace TheFancyRobot\RSVP;
use \PDO;

class User extends DatabaseConnection {

  public $username;
  public $password;
  public $firstName;
  public $lastName;
  public $email;
  public $userPhone;

  private $pwOptions;
  private $userInfo;
  private $userExists;
  private $hashedPW;

  /**
   * @param  string $password     User submitted password
   * @param  string $dbPassword   Password from database
   * @return boolean
   */
  public function checkPassword ($password, $dbPassword) {

    //Combine password with salt in db
    $this->pwOptions = ['cost' => 10];
    $this->hashedPW = password_hash($password, PASSWORD_BCRYPT, $this->pwOptions);

    if (password_verify($password, $this->hashedPW)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * @param  string $username    User submitted username
   * @return boolean
   */
  public function checkUser ($username) {
    $this->preparedQuery("SELECT userid FROM users WHERE username = :username");
    $this->bind(':username', $username);
    $this->execute();
    $this->userInfo = $this->getSingleRow();

    if (!empty($this->userInfo)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /*
   * @param  string $username   User submitted username
   * @return array
   */
  public function getUserInfo($username) {
    $this->username = $username;

    //Get user info using prepared statement
    $this->preparedQuery("SELECT * FROM users WHERE username = :username");
    $this->bind(':username', $username);
    $this->userInfo = $this->getSingleRow();

    //Get event codes
    $this->preparedQuery("SELECT event_code FROM events where userid = :userid");
    $this->bind(':userid', $this->userInfo['userid']);
    $this->eventInfo = $this->getAllRows();

    //Merge info into single array
    
    $this->userInfo['event_codes'] = $this->eventInfo;

    $this->userInfo['event_codes'] = array_map(function($eventCode) {
      return $eventCode['event_code'];
    }, $this->userInfo['event_codes'] );


    //return user info as array
    return $this->userInfo;
  }

  /**
   * @param  string $username   User submitted username
   * @param  string $password   User submitted password
   * @param  string $firstName  User submitted first name
   * @param  string $lastName   User submitted last name
   * @param  string $email      User submitted email address
   * @param  string $userPhone  User submiteed phone number
   * @return none
   */
  public function createUser ($username, $password, $firstName, $lastName, $email, $userPhone) {
    $this->username = $username;
    $this->password = $password;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->email = $email;
    $this->userPhone = $userPhone;

    //Make username lowercase
    $this->username = strtolower($this->username);

    //Generate a random salt to use for this account
    //$this->salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));

    //Set hash cost to 12 and use random salt
    $this->pwOptions = ['cost' => 10];

    $this->password = password_hash($this->password, PASSWORD_BCRYPT, $this->pwOptions);

    //Check if username exists
    $this->userExists = $this->checkUser($this->username);
    if ($this->userExists == TRUE) {
      echo '<script language="javascript">alert("Username already exists"); location.href="' . getenv("HTTP_REFERER") . '";</script>';
      die('Username already exists. Please <a href="' . getenv("HTTP_REFERER") . '">try again</a>');
    } else {
      //Insert into db
      $this->preparedQuery("INSERT INTO users (username, password, email, firstname, lastname, user_phone) VALUES (:username, :password, :email, :firstname, :lastname, :userphone)");
      $this->bind(':username', $this->username);
      $this->bind(':password', $this->password);
     // $this->bind(':salt', $this->salt);
      $this->bind(':email', $this->email);
      $this->bind(':firstname', $this->firstName);
      $this->bind(':lastname', $this->lastName);
      $this->bind(':userphone', $this->userPhone);
      $this->execute();
    }
  }
}