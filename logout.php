<?php

session_start();

require_once('lib/config.php');
session_destroy();
unset($_SESSION['username']);
header("Location: " . $url);
?>
