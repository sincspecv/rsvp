<?php
namespace TheFancyRobot\RSVP;

session_start();

ini_set('display_errors', 1);

require_once('lib/config.php');
require_once('lib/classes.php');
//require_once('vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php'); //Mobile browser detection

Session::verifySession();
?>

<!DOCTYPE html>
<html>
<head>
  <title> RSVP </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
  <body>
    <div data-role="page">

<?php

//Create GuestList object
$event = new Event();

//Add guest to db
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $firstName = filter_var(trim($_POST['firstName']), FILTER_SANITIZE_STRING);
  $lastName = filter_var(trim($_POST['lastName']), FILTER_SANITIZE_STRING);
  $addGuests = trim($_POST['addGuests']);

  //insert into db
  $event->addToGuestList($_SESSION['event_code'], $firstName, $lastName, $addGuests);
}

//Say hello to user
echo 'Weclome Back ' . $_SESSION['first_name'] . '!';
echo "<br />";




//list events
if ($_SESSION['event_code'] == '') {

  $event->printEventForm($_SESSION['first_name'], $_SESSION['last_name']);

} else {

  $guestList = $event->getGuestList($_SESSION['event_code']);
  $numberOfGuests = $event->getGuestCount($_SESSION['event_code']);

  echo '<div id="eventinfo" class="guestlist">
          <br />
          <p>Your Event Code is ' . $_SESSION['event_code'] . '<br />
          <p>Total Number of Guests: ' . $numberOfGuests;
  //Print Guest List
  $event->printGuestList();

  //Print modal div for manual entry
  echo '</div>
        <!-- Modal Button -->
        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#myModal">Add Manually</button>

        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
          <div class="modal-dialogm">

            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Guest</h4>
              </div>
              <div class="modal-body">
                <form action="account.php" method="POST" id="add">
                      First Name: <input type="text" name="firstName" id="firstName" value="">
                      Last Name: <input type="text" name="lastName" id="lastName" value=""><br />
                      Additional Guests: <input type="text" name="addGuests" id="addGuests" value="0">
                      <button type="submit" class="btn btn-info btn-sm">Add</button>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>

          </div>
        </div>
        ';
}

echo '';

?>
<a href="logout.php">Logout</a>


