<?php
namespace TheFancyRobot\RSVP;
use \PDO;
include('header.php');

$fileArray = file('lib/.env');

$fileArray = array_map('trim', $fileArray); //Trim away newlines so concatenated strings are on same line

if (isset($_POST['dbPass'])) {

    $fileArray[0] = $fileArray[0] . trim($_POST['webAddr']);
    $fileArray[3] = $fileArray[3] . trim($_POST['dbHost']);
    $fileArray[4] = $fileArray[4] . trim($_POST['dbUser']);
    $fileArray[5] = $fileArray[5] . trim($_POST['dbPass']);
    $fileArray[8] = $fileArray[8] . trim($_POST['plivoId']);
    $fileArray[9] = $fileArray[9] . trim($_POST['plivoToken']);

    file_put_contents('lib/.env', implode("\n", $fileArray), LOCK_EX);

    chmod('lib/.env', 0644);

    $dbName = DB_NAME;
    $dbHost = $_POST['dbHost'];
    $dbUser = $_POST['dbUser'];
    $dbPass = $_POST['dbPass'];

    try {

        //Create db
        $dbh = new PDO("mysql:host=$dbHost;", $dbUser, $dbPass);
        // set the PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8 COLLATE utf8_unicode_ci;";
        $dbh->exec($sql);

        //Create user and event tables
        $dbh = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        // set the PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql=("CREATE TABLE users (userid int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                                                  username VARCHAR(50), 
                                                  password VARCHAR(72), 
                                                  email VARCHAR(50), 
                                                  firstname VARCHAR(50), 
                                                  lastname VARCHAR(50), 
                                                  user_phone CHAR(10))");
        $dbh->exec($sql);

        $sql=("CREATE TABLE events (id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                                                  userid int(11) NOT NULL, 
                                                  event_name VARCHAR(50), 
                                                  event_date DATE, 
                                                  event_code VARCHAR(8), 
                                                  primary_host VARCHAR(255),
                                                  second_host VARCHAR(255),
                                                  event_phone VARCHAR(10))");
        $dbh->exec($sql);

        header('Location: index.php');
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }

    $conn = null;
}



?>

<div class="form-wrap row" ng-app="installForm">
    <div class="col-sm-5 text-center margin-15">
        <p><strong>Please make certain that all information entered is correct. If anything is entered incorrectly, you will need to edit the .env file manually!</strong></p>
    </div>
    <form role="form" name="installForm" class="form-horizontal" action="install.php" method="POST" id="register" ng-controller="installCtrl" novalidate>
        <div class="col-sm-6">
            <div class="alert alert-danger" role="alert alert-danger" ng-show="showError">Something went wrong. Please check your entries and try again.</div>
            <div class="form-group">

                <!-- WEB ADDRESS -->
                <div ng-class="{ 'has-error' : installForm.webAddr.$touched && installForm.webAddr.$invalid }" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.webAddr.$touched && installForm.webAddr.$invalid">This field is required.</span>
                    <input type="text" class="form-control" name="webAddr" id="webAddr" ng-model="formData.webAddr" placeholder="Website Address" value="" required>
                </div>

                <!-- DB HOST -->
                <div ng-class="{ 'has-error' : installForm.dbHost.$touched && installForm.dbHost.$invalid }" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.dbHost.$touched && installForm.dbHost.$invalid">Invalid dbHost address.</span>
                    <input type="dbHost" class="form-control" name="dbHost" id="dbHost" ng-model="formData.dbHost" placeholder="Database Host" value="" required>
                </div>


                <!-- DB USER -->
                <div ng-class="{ 'has-error' : installForm.dbUser.$touched && installForm.dbUser.$invalid }" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.dbUser.$touched && installForm.dbUser.$error.required">This field is required.</span>
                    <input type="text" class="form-control" name="dbUser" id="dbUser" ng-model="formData.dbUser" placeholder="Database Username" value="" required>
                </div>

                <!-- DB PREFIX -->
                <div ng-class="{ 'has-error' : installForm.dbPass.$touched && installForm.dbPass.$invalid }" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.dbPass.$touched && installForm.dbPass.$error.required">This field is required.</span>
                    <input type="text" class="form-control" name="dbPass" id="dbPass" ng-model="formData.dbPass" autocomplete="off" placeholder="Database Password" value="" required>
                </div>

                <!-- PLIVO ID -->
                <div ng-class="{ 'has-error' : installForm.plivoId.$touched && installForm.plivoId.$invalid }" ng-class="dynamicClass" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.plivoId.$touched && installForm.plivoId.$error.required">This field is required.</span>
                    <input type="text" class="form-control" name="plivoId" id="plivoId" placeholder="Plivo API ID" ng-model="formData.plivoId" value="" maxlength="20" required><br />
                </div>

                <!-- PLIVO TOKEN -->
                <div ng-class="{ 'has-error' : installForm.plivoToken.$touched && installForm.plivoToken.$invalid }" ng-class="dynamicClass" class="form-group ">
                    <span class="alert-danger" ng-show="installForm.plivoToken.$touched && installForm.plivoToken.$error.required">This field is required.</span>
                    <input type="text" class="form-control" name="plivoToken" id="plivoToken" placeholder="Plivo API Token" ng-model="formData.plivoToken" value=""  required><br />
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="btn btn-default" ng-disabled="installForm.$invalid">Submit</button>

            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="lib/js/install.js"></script>

<?=include('footer.php');?>