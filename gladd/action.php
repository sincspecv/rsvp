<?php
include('functions.php');
$check = mysql_query("SELECT * FROM guestlist order by id desc");
if(isset($_POST['content']))
{
$content=mysql_real_escape_string($_POST['content']);
$ip=mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
mysql_query("insert into guestlist(first_name,last_name,guest_num) values ('$fname','$lname','$gnum')");
$fetch= mysql_query("SELECT first_name,last_name,guest_num FROM guestlist order by id desc");
$row=mysql_fetch_array($fetch);
}
?>

<div class="showbox"> <?php echo $row['first_name','last_name','guest_num']; ?> </div>
