<?php
	session_start();

	
	function logoff($url)  {
		unset($_SESSION['flag']);
		setcookie("name","",time()-3600);
		setcookie("sid","",time()-3600);
		if ($url!="") header("location:../index.php");
	}

	logoff("calendar.php");
?>