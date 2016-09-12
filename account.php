<?php
namespace TheFancyRobot\RSVP;

include('header.php');

?>

 
  <div class="row form-background" id="rform"> 
	<div class="row text-center" id="title" class="title">
		<img src="lib/img/rsvpd_logo.png">
	</div>
	
    <div class="col-sm-8" ng-app="account" ng-init="user=<?php echo htmlspecialchars(json_encode($_SESSION)); ?>" ui-view> 
      
    </div>
  </div>

  <script src="lib/js/account.js"></script>

<?php include('footer.php'); ?>
