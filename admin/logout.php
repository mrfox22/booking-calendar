<?php
	session_start();

	function logoff($url)  {
		unset($_SESSION['adminflag']);
		setcookie("adminName","",time()-36000);
		setcookie("adminSid","",time()-36000);
		if ($url!="") header("location:index.php");
	}

	logoff("login.php");
?>