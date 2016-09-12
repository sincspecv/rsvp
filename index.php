<?php
namespace TheFancyRobot\RSVP;

include('header.php');

if (isset($_SESSION['agent']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])) {
  header('Location: ' . $url . 'account.php');
}
?>

 <div class="row text-center" id="title" class="title">
    <img src="lib/img/rsvpd_logo.png">
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
    <div class="text-center col-md-6" data-brix_class="1465703790546" id="conversion-col">
        <form class="form-signin form-horizontal" role="form">
            <h2 class="form-signin-heading">
                <br>Don't have an account?<br>
            </h2>
            <span id="conversion-span">
                That's okay. We forgive you. Just <a href="register.php">click here to register</a>.<br>
            </span>
        </form>
    </div>
  </div>
</div> <!-- /container -->

 <?=include('footer.php');?>
