<?php
namespace TheFancyRobot\RSVP;

include('header.php');

?>

 
  <div class="row form-background" id="rform"> 
   <div class="row text-center" id="title" class="title">
    <img src="lib/img/rsvpd_logo.png">
  </div>
    <div class="col-sm-8" ng-app="regForm" ng-controller="formController" ui-view> 
      
    </div>
  </div>

  <script src="lib/js/regform.js"></script>

<?=include('footer.php');?>
