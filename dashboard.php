<?php
namespace TheFancyRobot\RSVP;

include('header.php');

if (!isset($_SESSION['agent']) || $_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) {
  header('Location: index.php');
}

?>


  <div id="rform" ng-app="account">
	
    <div class="col-xs-12 col-md-10 col-md-offset-1" ng-init="user=<?php echo htmlspecialchars(json_encode($_SESSION)); ?>" ui-view>
      
    </div>
  </div>

  <script src="lib/js/account.js"></script>

<?php include('footer.php'); ?>
