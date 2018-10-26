<?php
	include("../conn.php"); 
	adminlogincheckonly();

	if(!empty($_GET['id'])) { 
		$r_ps1="123456";
		$r_ps1=md5($r_ps1);
		mysql_query("update bk_staff set s_password1='$r_ps1', s_pwresetted=1 where s_id=" . $_GET['id']);
		echo "<script>alert('密码已重置。'); document.location.href='index.php';</script>";
		exit();
	}
?>
	