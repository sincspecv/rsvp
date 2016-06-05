<?php

################################################################################
###### Print HTML Head Info ######
################################################################################
function printHTMLHead($incl1 = NULL, $incl2 = NULL) {
  echo '<!DOCTYPE html>
        <html>
        <head>
          <title> RSVP </title>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
          <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
          ';
  echo $incl1;
  echo $incl2;
  echo '</head>
        <body>
        <div data-role="page">
        ';
}

################################################################################
###### Print Closing HTML Tags ######
################################################################################
function printHTMLClose() {
  echo '</div>
        </body>
        </html>';
}

################################################################################
###### Print form to add event ######
################################################################################
function printEventForm($firstName, $lastName){
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

################################################################################
###### Print event Info ######
###############################################################################
function printEventInfo ($eventCode){
  global $db;
  global $db_connect;

  //Query database for guest list
  $sql = "SELECT * FROM $eventCode";
  $result = mysqli_query($db_connect, $sql);

  if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  //Get number of guests that have RSVP'd
  $numRows = mysqli_num_rows($result);
  $sql = "SELECT SUM(add_guest) AS guest_sum from $eventCode";
  $query = mysqli_query($db_connect, $sql);
  $addGuestsArray = mysqli_fetch_assoc($query);
  $addGuestSum = $addGuestsArray['guest_sum'];

  //Print results
  echo 'Total number of guests: ' . $numRows += $addGuestSum;

  //Start table
  echo '<div id="gldiv" class="guestlist">
          <table id="gltable" class="guestlist">
            <tr>
              <th>Guest Name</th>
              <th>Additional Guests</th>
            </tr>
        ';

  //Print guest list
  while ($list = mysqli_fetch_assoc($result)) {
    $guestName = $list['guest_name'];
    $addGuests = $list['add_guest'];
    $guestId = $list['id'];
    echo '<tr>
            <td>' . $guestName . '</td>
            <td>' . $addGuests . '</td>
          </tr>
          ';
  }
  //Close table
  echo '  </table>
        </div>
      ';

}

################################################################################
###### Create event code using $eventCode variable ######
################################################################################
function createEventCode($str1, $str2) {
  global $eventCode; //use global variable $eventCode

  $str1 = substr($str1, 0, 2); //First two letters of last name of Primary Host
  $str1 = strtolower($str1); //Convert all letters to lower case

  if ($str2 == NULL) { //Check if name was entered for second host
    $str2 = substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 5)), 0, 2); //If no second host generate random string
  } else {
    $str2 = substr($str2, 0, 2); //First two letters of last name of Second Host
    $str2 = strtolower($str2); //Convert all letters to lower case
  }

  $str3 = mt_rand(1000, 9999); //Generate random number

  $eventCode = $str1 . $str2 . $str3; //Combine strings to create event code;
}

################################################################################
###### Check if database table exists ######
################################################################################
function table_exists($table){
  global $db;
  global $db_connect;
  $sql = "show tables like '".$table."'";
  $result = $db_connect->query($sql);
  return ($result->num_rows > 0);
}
?>
