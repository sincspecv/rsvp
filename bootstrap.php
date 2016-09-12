<?php

session_start();

ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';

$Loader = (new josegonzalez\Dotenv\Loader(__DIR__ . '/lib/.env'))
              ->parse()
              ->toEnv(); // Throws LogicException if ->parse() is not called first

require_once __DIR__ . '/lib/config.php';