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

  protected $salt = NULL;

  private $pwOptions;
  private $userInfo;
  private $userExists;
  private $hashedPW;

  public function login($username, $dbUsername, $password, $dbPassword, $salt) {

    //Combine password with salt in db
    $this->pwOptions = ['cost' => 10,
                        'salt' => $salt
                       ];
    $this->hashedPW = password_hash($password, PASSWORD_BCRYPT, $this->pwOptions);

    //Check if password and username matches
    if ($this->hashedPW == $dbPassword && $username == $dbUsername) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

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

  //Get user info from DB
  public function getUserInfo($username) {
    $this->username = $username;

    //Get user info using prepared statement
    $this->preparedQuery("SELECT * FROM users WHERE username = :username");
    $this->bind(':username', $username);
    $this->execute();
    $this->userInfo = $this->getSingleRow();

    //return user info as array
    return $this->userInfo;
  }

  //Create new user
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
    $this->salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));

    //Set hash cost to 10 and use random salt
    $this->pwOptions = ['cost' => 10,
                        'salt' => $this->salt
                       ];

    $this->password = password_hash($this->password, PASSWORD_BCRYPT, $this->pwOptions);

    //Check if username exists
    $this->userExists = $this->checkUser($this->username);
    if ($this->userExists == TRUE) {
      echo '<script language="javascript">alert("Username already exists"); location.href="' . getenv("HTTP_REFERER") . '";</script>';
      die('Username already exists. Please <a href="' . getenv("HTTP_REFERER") . '">try again</a>');
    } else {
      //Insert into db
      $this->preparedQuery("INSERT INTO users (username, password, salt, email, firstname, lastname, user_phone) VALUES (:username, :password, :salt, :email, :firstname, :lastname, :userphone)");
      $this->bind(':username', $this->username);
      $this->bind(':password', $this->password);
      $this->bind(':salt', $this->salt);
      $this->bind(':email', $this->email);
      $this->bind(':firstname', $this->firstName);
      $this->bind(':lastname', $this->lastName);
      $this->bind(':userphone', $this->userPhone);
      $this->execute();
    }
  }
}