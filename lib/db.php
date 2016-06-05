<?php

ini_set('display_errors', 1);

$db_connect = new mysqli($dbHost, $dbUsername, $dbPassword, $db );

//Thorow error if connection fails;
if ($db_connect->connect_errno) {
    echo "Failed to connect to MySQL: (" . $db_connect->connect_errno . ") " . $db_connect->connect_error;
  }

 ?>
