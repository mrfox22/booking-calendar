<?php
	session_start();

	function logoff($url)  {
		unset($_SESSION['adminflag']);
		setcookie("name","",time()-36000);
		setcookie("sid","",time()-36000);
		if ($url!="") header("location:index.php");
	}

	logoff("login.php");
?>