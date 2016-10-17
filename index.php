<?php
namespace TheFancyRobot\RSVP;

include('header.php');

if (DB_HOST == '') {
    header('Location: install.php');
}

if (isset($_SESSION['agent']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])) {
  header('Location: dashboard.php');
}
?>

    <div class="sign-in-wrap col-md-10 col-md-offset-1 " ng-app="signIn">
        <div class="text-center logo" id="logo">
            rsvpd
        </div>
          <div class="row form-background" id="sign-in-row">

            <div class="col-md-6 text-center">
                <form class="form-signin" role="form" action="login.php" method="post" >
                    <h2 class="form-signin-heading">Please sign in</h2>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="" autofocus="">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
                    <input type="hidden" name="id" value="<?php echo md5($_SERVER['HTTP_USER_AGENT']); ?>">
                    <button class="btn btn-lg btn-primary btn-block" type="submit" id="button-signin">Sign in</button>
                </form>
            </div>
            <div class="text-center col-md-6" id="conversion-col" ng-controller="modalController">
                <form class="form-signin form-horizontal" role="form">
                    <h2 class="form-signin-heading">
                        Don't have an account?<br>
                    </h2>
                    <span id="conversion-span">
                        That's okay. We forgive you.
                        <button ng-click="open('lg')" class="btn btn-lg btn-warning btn-block btn-register">click here to register</button>.<br>
                    </span>
                </form>
            </div>
          </div>
    </div>
</div> <!-- /container -->

<script type="text/javascript" src="lib/js/signin.js"></script>

 <?=include('footer.php');?>
