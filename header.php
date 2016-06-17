<?php

session_start();

ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

$Loader = (new josegonzalez\Dotenv\Loader(__DIR__ . '/lib/.env'))
              ->parse()
              ->toEnv(); // Throws LogicException if ->parse() is not called first

require_once __DIR__ . '/lib/config.php';

?>


<!DOCTYPE html>
<html>
<head>
  <title> RSVP </title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="lib/css/custom.css" rel="stylesheet">

    <!--- AngularJS
    ================================================== -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.8/angular-ui-router.min.js"></script>
    <script src="https://code.angularjs.org/1.5.6/angular-animate.min.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <!--[endif]---->
  </head>

  <body>

    <div class="container" id="container">