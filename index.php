<?php
namespace TheFancyRobot\RSVP;
session_start();

require __DIR__ . '/vendor/autoload.php';

printHTMLHead();

if (isset($_SESSION['agent']) && $_SESSION['agent'] == md5($_SERVER['HTTP_USER_AGENT'])) {
  header('Location: ' . $url . 'account.php');
}
?>
<div id="title" class="title">
  <p>Welcom to RSVP!</p>
  <p>hello</p>
</div>
<div class="form" id="login">
  <h3>Login:</h3>
  <form action="login.php" method="post" id="loginform">
    username: <input type="text" name="username" id="username" value=""><br />
    password: <input type="password" name="password" id="password" value=""><br />
    <input type="submit" value="Login" id="login">
  </form>
</div>
  or <a href="register.htm">Register</a>
</body>
</html>
