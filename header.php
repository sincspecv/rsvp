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
    <link href="lib/css/bootstrap_custom.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://fonts.googleapis.com/css?family=Monsieur+La+Doulaise" rel="stylesheet">

    <!--- AngularJS
    ================================================== -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.2.8/angular-ui-router.min.js"></script>
    <script src="https://code.angularjs.org/1.5.6/angular-animate.min.js"></script>
    <script type="text/javascript" src="bower_components/angular-validation-match/dist/angular-validation-match.min.js"></script>
    <script src="lib/js/ui-bootstrap-tpls-2.1.4.min.js"></script>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <!--[endif]---->
  </head>

  <body>

  <?php
  if (!empty($_SESSION)) {
      ?>
      <nav class="navbar navbar-default bg-primary navbar-fixed-top" ng-controller="NavCtrl">
          <div class="nav-container">
              <div class="navbar-header">
                  <button type="button" class="navbar-toggle navbar-right" data-toggle="collapse" data-target="#nav" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed">
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                      <span class="icon-bar"></span>
                  </button>
              </div>
              <span class="navbar-brand">rsvpd</span>
              <div class="collapse navbar-collapse navbar-right" id="nav" uib-collapse="navCollapsed">
                  <ul class="nav navbar-nav">
                      <li><a href="#/dashboard">Dashboard</a></li>
                      <li><a href="#/create">New Event</a></li>
                      <li><a href="account.php">Account</a</li>
                      <li><a href="logout.php">Logout</a></li>
                  </ul>
              </div>
          </div>

      </nav>
  <?php
  }
  ?>
      <div class="container" id="container" ng-app="account">