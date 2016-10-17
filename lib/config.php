<?php
namespace TheFancyRobot\RSVP;

//$url = $_ENV['WEB_ADDR']; //Website Address

//Database Config
define("DB_HOST", $_ENV['DB_HOST']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("DB_NAME", $_ENV['DB_NAME']);
define("DB_PREFIX", $_ENV['DB_PREFIX']);

//Plivo Config
define("PLIVO_ID", $_ENV['PLIVO_ID']);
define("PLIVO_TOKEN", $_ENV['PLIVO_TOKEN']);