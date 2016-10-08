<?php
namespace TheFancyRobot\RSVP;

include('header.php');

?>

 
  <div class="row form-background" id="rform">
      <div class="row text-center logo" id="logo">
          rsvpd
      </div>
    <div class="col-md-8" ng-app="regForm" ng-controller="formController" ui-view>
      
    </div>
  </div>

  <script src="lib/js/regform.js"></script>

<?=include('footer.php');?>
