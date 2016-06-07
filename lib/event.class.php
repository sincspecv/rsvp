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