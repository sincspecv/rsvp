<?php
/**
	define('HOST','localhost');
	define('USERNAME', 'root');
	define('PASSWORD','mctrek23');
	define('DB','gl1');

  $con = mysqli_connect(HOST,USERNAME,PASSWORD,DB);

  $fname = $_POST['firstname'];
  $lname = $_POST['lastname'];
  $gnum = $_POST['gnum'];

  $sql = "insert into guestlist (first_name, last_name, guest_num) values ('$fname','$lname','$gnum')";

  if(mysqli_query($con, $sql)){
  echo 'success';
  }
**/

	function user_login($username, $password) {

		//take the username and prevent SQL injections
		$username = string mysqli_real_escape_string($username);

		//begin the query
		$sql = $mysqli->query("SELECT * FROM users WHERE username = '".$username."' AND password = '".$password."' LIMIT 1"); 

		//check to see how many rows were returned
		$rows = mysql_num_rows($sql);

		if ($rows<=0 ){
			echo "Incorrect username/password";
			} else {
			//have them logged in
			$_SESSION['username'] = $username;
			}
		}
?>
