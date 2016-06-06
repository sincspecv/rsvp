<?php
namespace TheFancyRobot\RSVP;
use \PDO;

require_once('config.php');
/*
* Database Object
*/
class DatabaseConnection {
  private $host = DB_HOST;
  private $user = DB_USER;
  private $pass = DB_PASS;
  private $dbname = DB_NAME;
  protected $dbConnect;
  private $stmt = NULL;
  private $result;

  public function __construct() {
    // Set DSN
    $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
    // Set options
    $options = array(
        PDO::ATTR_PERSISTENT        => true,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_WARNING,
        PDO::ATTR_EMULATE_PREPARES  => false
    );

    // Create a new PDO instanace
    try{
        $this->dbConnect = new PDO($dsn, $this->user, $this->pass, $options);
    }
    // Catch any errors
    catch(PDOException $e) {
        $this->error = $e->getMessage();
    }
  }

  //Prepare statement
  public function preparedQuery($query) {
    //Unset previous stmt
    unset($this->stmt);
    //Set up new prepared statment
    $this->stmt = $this->dbConnect->prepare($query);
  }

  //Bind paramaters
  public function bind($param, $value, $type = null) {
    if (is_null($type)) {
        switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
        }
    }
    $this->stmt->bindValue($param, $value, $type);
  }

  //Execute statement
  public function execute() {
    return $this->stmt->execute();
  }

  //Return array of set rows
  public function getAllRows() {
    $this->execute();
    return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  //Return single record
  public function getSingleRow() {
    $this->execute();
    return $this->stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function countRows($table) {
    $this->result = $this->dbConnect->query("SELECT * FROM $table");
    return $this->result->rowCount();
  }

  public function checkForTable($table) {
    $this->result = $this->dbConnect->query("SHOW TABLES LIKE '$table'")->rowCount();

    if ($this->result > 0) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

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

class Event extends DatabaseConnection {

  private $guestListQuery;
  private $addGuests;
  private $guestName;
  private $str1 = NULL;
  private $str2 = NULL;
  private $str3 = NULL;
  private $result;
  private $stmtArray;

  public $eventCode;
  public $guestList;

  //Add to Guest List
  public function addToGuestList($eventCode, $firstName, $lastName, $addGuests) {
    $this->eventCode = $eventCode;
    $this->guestName =  $firstName . ' ' . $lastName;
    $this->addGuests = $addGuests;

    if (strlen($this->eventCode) > 8 || !preg_match('/\D{4}\d{4}/', $this->eventCode)) { //If eventCode is more than eight characters or not 4 letters followed by 4 numbers
      die("Not valid event code");
    } else {
      //Insert into DB
      $this->preparedQuery("INSERT into $this->eventCode (guest_name, add_guest) VALUES ( :guestname, :addguests)");
      $this->bind(':guestname', $this->guestName);
      $this->bind(':addguests', $this->addGuests);
      $this->execute();
    }
  }

  //Get Guest List from DB
  public function getGuestList($eventCode) {
    $this->eventCode = $eventCode;

    //Filter $eventCode variable
    if (strlen($this->eventCode) > 8 || !preg_match('/\D{4}\d{4}/', $this->eventCode)) { //If eventCode is more than eight characters or not 4 letters followed by 4 numbers
      die("Not valid event code");
    } else {
      //Check to make sure event has been created
      $this->numRows = $this->countRows($this->eventCode);
      //$this->numRows = $this->dbConnect->query("SELECT COUNT(id) FROM $this->eventCode")->fetchColumn();
      if ($this->numRows > 0) {
        //Get guest list
        $this->guestListQuery = $this->preparedQuery("SELECT * FROM $this->eventCode");
        $this->guestList = $this->getAllRows();
      } else {
        echo "no event found";
        unset($this->dbConnect);
        exit();
      }
    }
  //return event ifno as array
  return $this->guestList;
  }

  //Get number of Guests from DB
  public function getGuestCount($eventCode) {
    $this->eventCode = $eventCode;
    //Filter $eventCode variable
    if (strlen($this->eventCode) > 8 || !preg_match('/\D{4}\d{4}/', $this->eventCode)) { //If eventCode is more than eight characters or not 4 letters followed by 4 numbers
      die("Not valid event code");
    } else {
      //Check to make sure event has been created
      $this->guestCount = $this->countRows($this->eventCode); //Get number of rows
      if ($this->guestCount > 0) {
        //Get guest list
        $this->guestListQuery = $this->preparedQuery("SELECT * FROM $this->eventCode");
        $this->guestList = $this->getAllRows();
        $this->addGuests = array_sum(array_column($this->guestList, 'add_guest'));
        return $this->guestCount += $this->addGuests; //sum of rows + sum of add_guest column
      } else {
        die ("no event found");
        unset($this->dbConnect);

      }
    }
  }

  public function printGuestList() {
      //Start table
      echo '<div id="guestlist" class="guestlist">
              <table id="gltable" class="guestlist">
                <tr>
                  <th>Guest Name</th>
                  <th>Additional Guests</th>
                </tr>
            ';

      //Print Guest List
      foreach ($this->guestList as $index) {
        echo "<tr>
                <td>" . $index['guest_name'] . "</td>
                <td>" . $index['add_guest'] . "</td>
              </tr>
              ";
      }

      //Close table
      echo '  </table>
            </div>
            ';
  }

  public function printEventForm($firstName, $lastName){
    echo '<form action="create.php" method="POST" id="add">
          <h2>Hosts</h2><br />
          <h4>Primary Host (required)</h4><br />
          First Name: <input type="text" name="pHostFirstName" id="pHostFirstName" value="' . $firstName . '">
          Last Name: <input type="text" name="pHostLastName" id="pHostLastName" value="' . $lastName . '"><br />
          <h4>Additional Host (optional)</h4>
          First Name: <input type="text" name="sHostFirstName" id="sHostFirstName" value="">
          Last Name: <input type="text" name="sHostLastName" id="sHostLastName" value=""><br />
          <h2>Event Info</h2><br />
          Event Name: <input type="text" name ="eventName" id="eventName">
          Date: <input type="date" name="eventDate" id="eventDate" value="mm/dd/yyyy"><br />
          <input type="submit" name="submit" value="Submit">
          ';
  }

  public function createEventCode($pHostName, $sHostName) {
    //Create event code
      $this->str1 = substr($pHostName, 0, 2); //First two letters of last name of Primary Host
      $this->str1 = strtoupper($this->str1); //Convert all letters to upper case

      if ($sHostName == NULL) { //Check if name was entered for second host
        $this->str2 = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 0, 2); //If no second host generate random string
      } else {
        $this->str2 = substr($sHostName, 0, 2); //First two letters of last name of Second Host
      }

      $this->str2 = strtoupper($this->str2); //Convert all letters to upper case
      $this->str3 = mt_rand(1000, 9999); //Generate random number

      $this->eventCode = $this->str1 . $this->str2 . $this->str3; //Combine strings to create event code;

      return $this->eventCode;
  }

  public function checkEventCode($eventCode) {
    $this->preparedQuery("SHOW TABLES LIKE $eventCode");
    $this->result = $this->execute();

    if ($this->result) {
      $this->numRows = $this->stmt->fetch(PDO::FETCH_NUM);
        if ($this->numRows[0]) {
            //table was found
            return true;
        } else {
            //table was not found
            return false;
        }
    }
  }

  public function createEvent ($username, $eventCode, $pHostName, $sHostName, $eventName, $eventDate) {
   
    //Make sure eventCode doesn't exist   

    if ($this->checkForTable($eventCode) == TRUE) {
      die('Something went wrong. Please <a href="account.php">try again</a>.');
    } else {
      try {
        //Add event info to user table
        $this->preparedQuery("UPDATE users SET event_name = :eventname, event_code = :eventcode, event_date = :eventdate, primary_host = :primaryhost, second_host = :secondhost WHERE username = :username");
        $this->bind(':eventname', $eventName);
        $this->bind(':eventcode', $eventCode);
        $this->bind(':eventdate', $eventDate);
        $this->bind(':primaryhost', $pHostName);
        $this->bind(':secondhost', $sHostName);
        $this->bind(':username', $username);
        $this->execute();

        //Create event table in db
        $this->preparedQuery("CREATE TABLE $eventCode (id int NOT NULL PRIMARY KEY AUTO_INCREMENT, guest_name VARCHAR(255), add_guest VARCHAR(255))");
        $this->execute();

        //Add primary host to event table
        $this->preparedQuery("INSERT INTO $eventCode (guest_name, add_guest) VALUES ( :guestname, :addguests)");
        $this->bind(':guestname', $pHostName);
        $this->bind(':addguests', '0');
        $this->execute();

        //Add second host if provided 
        if ($sHostName != " ") {
          $this->preparedQuery("INSERT INTO $eventCode (guest_name, add_guest) VALUES ( :guestname, :addguests)");
          $this->bind(':guestname', $sHostName);
          $this->bind(':addguests', '0');
          $this->execute();
        }
        
      } catch (Exception $err) {
        echo "There was an error: " . $err;
      }
    }
  } 
}

class SMS extends DatabaseConnection {
  private $eventExists;
  private $eventCode;

  public $step;
  public $result;

  public function checkForSession ($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->result = $this->getSingleRow();

    if (!empty($this->result)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function checkEventCode($eventCode) {
    $this->eventExists = $this->checkForTable($eventCode);

    if ($this->eventExists == TRUE) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function createSMSSession($phoneNumber) {
     //Add session to sms table
    $this->preparedQuery("INSERT INTO sms (phone_number, step) VALUES (:phone_number, :step)");
    $this->bind(':phone_number', $phoneNumber);
    $this->bind(':step', 1);
    $this->execute();    
  }  

  public function getStep($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->step = $this->getSingleRow();

    return $this->step['step'];
  } 

  public function processStepOne($phoneNumber, $eventCode) {
    $this->step = 2;
    $this->preparedQuery("UPDATE sms SET event_code = :event_code, step = :step WHERE phone_number = :phone_number");
    $this->bind(':event_code', $eventCode);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

  public function processStepTwo($phoneNumber, $name) {
    $this->step = 3;
    $this->preparedQuery("UPDATE sms SET guest_name = :guest_name, step = :step WHERE phone_number = :phone_number");
    $this->bind(':guest_name', $name);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

    public function processStepThree($phoneNumber, $addGuests) {
    $this->step = 4;
    $this->preparedQuery("UPDATE sms SET add_guest = :add_guest, step = :step WHERE phone_number = :phone_number");
    $this->bind(':add_guest', $addGuests);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

  public function addToGuestList($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->result = $this->getSingleRow();

    $this->eventCode = $this->result['event_code'];
    
    $this->preparedQuery("INSERT INTO `$this->eventCode` (guest_name, add_guest) VALUES (:guest_name, :add_guest");
    $this->bind(':guest_name', $this->result['guest_name']);
    $this->bind(':add_guest', $this->result['add_guest']);
    $this->execute(); 
  }
}

//Session Handling
class Session {

  public function __construct() {

  }

  private function __clone() {

  }

  static public function instance() {

    if (Self::$instance === NULL)
        {
            // Create a new instance
            new Self;
        }

        return Self::$instance;
  }

  public function createSession($userInfoArray) {

    //Set session data
    $_SESSION['username'] = $userInfoArray['username'];
    $_SESSION['first_name'] = $userInfoArray['firstname'];
    $_SESSION['last_name'] = $userInfoArray['lastname'];
    $_SESSION['event_code'] = $userInfoArray['event_code'];

    //Store HTTP_USER_AGENT for verification
    $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
  }

  public function verifySession() {
    //Verify session data has been defined and HTTTP_USER_AGENT matches
    if (!isset($_SESSION['agent']) || $_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
      session_destroy();
      header("Location: " . $url . "index.php");
      die("Please try logging in again");
    } 
  }
}
