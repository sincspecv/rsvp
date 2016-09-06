<?php
namespace TheFancyRobot\RSVP;
use \PDO;

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
  public $eventInfoArray;
  public $guestCount;

  /**
   * Add guest to guest list
   * @param string $eventCode EventCode
   * @param string $firstName Frist name of guest
   * @param string $lastName  Last name of guest
   * @param string $addGuests Number of additional guests
   */
  public function addToGuestList($eventCode, $guestName, $addGuests) {
    $this->eventCode = $eventCode;
    $this->guestName = $guestName;
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

  /**
   * Get the guest list from the database
   * @param  string $eventCode Event code
   * @return array            
   */
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

  /**
   * Get number of guests for event
   * @param  string $eventCode Event code
   * @return int            
   */
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

  /**
   * Prints guest list in a HTML table
   */
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

  /**
   * Print event creation form
   * @param  string $firstName First Name
   * @param  string $lastName  Last Name
   */
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

  /**
   * Create event code using last name of hosts and random numbers
   * @param  string $pHostName Last name of primary host
   * @param  string $sHostName Last name of secondary host
   * @return string            Event Code
   */
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

  /**
   * Check if event table exists
   * @param  string $eventCode Event Code
   * @return boolean            
   */
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

  /**
   * Create event table and add event info to user table
   * @param  string $username  Username
   * @param  string $eventCode Event code
   * @param  string $pHostName Primary host name
   * @param  string $sHostName Secondary host name
   * @param  string $eventName Name of event
   * @param  string $eventDate Date of event
   */
  public function createEvent ($username, $eventCode, $pHostName, $sHostName, $eventName, $eventDate) {
   
    //Make sure eventCode doesn't exist   

    if ($this->checkForTable($eventCode) == TRUE) {
      die('Something went wrong. Please <a href="account.php">try again</a>.');
    } else {
      try {
        //Get user id from user table
        $this->preparedQuery("SELECT userid FROM users WHERE username = :username");
        $this->bind(':username', $username);
        $userData = $this->getSingleRow();
        $userid=$userData['userid'];
        //Add event info to event table
        $this->preparedQuery("INSERT INTO events (userid, event_name, event_code, event_date, primary_host, second_host, event_phone) VALUES (:userid, :eventname, :eventcode, :eventdate, :primaryhost, :secondhost, :eventphone)");
        $this->bind(':userid', $userid);
        $this->bind(':eventname', $eventName);
        $this->bind(':eventcode', $eventCode);
        $this->bind(':eventdate', $eventDate);
        $this->bind(':primaryhost', $pHostName);
        $this->bind(':secondhost', $sHostName);
        $this->bind(':eventphone', '8153144282');
        $this->execute();

        //Create event table in db
        $this->preparedQuery("CREATE TABLE $eventCode (id int NOT NULL PRIMARY KEY AUTO_INCREMENT, guest_name VARCHAR(255), add_guest VARCHAR(255), guest_phone VARCHAR(255))");
        $this->execute();

        //Add primary host to event table
        $this->preparedQuery("INSERT INTO $eventCode (guest_name, add_guest) VALUES ( :guestname, :addguests)");
        $this->bind(':guestname', $pHostName);
        $this->bind(':addguests', '0');
        $this->execute();

        //Add second host if provided 
        if ($sHostName != " ") {
          $this->preparedQuery("INSERT INTO $eventCode (guest_name, add_guest) VALUES (:guestname, :addguests)");
          $this->bind(':guestname', $sHostName);
          $this->bind(':addguests', '0');
          $this->execute();
        }
        
      } catch (Exception $err) {
        echo "There was an error: " . $err;
      }
    }
  } 

  public function getEventInfo() {

    $this->eventCodes = $_SESSION['event_codes'];

    

    while(list($key, $value) = each($this->eventCodes)) {
      $this->preparedQuery("SELECT * FROM events WHERE event_code = :eventCode");
      $this->bind(':eventCode', $value);
      $this->eventInfo = $this->getSingleRow();
      $this->eventInfoArray['events'][] = $this->eventInfo;

      //Add guest count to array
      $this->guestCount = $this->getGuestCount($value);
      $this->eventInfoArray['events'][$key]['guest_count'] = $this->guestCount;

      //add guest list to array
      $this->guestList = $this->getGuestList($value);
      $this->eventInfoArray['events'][$key]['guest_list'] = $this->guestList;
    }
   
    return $this->eventInfoArray;
  }
}