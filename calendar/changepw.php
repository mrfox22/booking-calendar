<?php
	include("conn.php"); 
	logincheckforguest();

	if(isset($_POST['ops']) && isset($_POST['ps1']) && isset($_POST['ps2']) && isset($_POST['s_id'])) {
		$ops=$_POST['ops'];
		$ps1=$_POST['ps1'];  //新密码
		$ps2=$_POST['ps2'];  //确认密码
		$s_id=$_POST['s_id'];
		
		$errors=array();
		$data=array();

		$sql = "select * from bk_staff where s_id=".$s_id;
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);

		if(md5($ops)!=$row['s_password1']) $errors['ops']="原密码输入有误。";
		
		if(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9]{3,9}$/', $ps1)) {
			$errors['ps1']="密码只能包括字母（A-Z，a-z）及数字（0-9），长度在4~10位之间，且不能与原密码相同。";
		} else {
			if($ps1==$ops ) $errors['ps1']="密码只能包括字母（A-Z，a-z）及数字（0-9），长度在4~10位之间，且不能与原密码相同。";
		}

		if($ps2!=$ps1) $errors['ps2']="两次输入的密码不符。";

		if(!empty($errors)) {
			$data['success']=false;
			$data['errors']=$errors;
		} else {
			$md5ps1=md5($ps1);
			$sql_update="update bk_staff set s_password1='$md5ps1' where s_id=".$s_id;
			mysql_query($sql_update);

			$data['success']=true;
			$data['message']="修改成功";
		}

		echo json_encode($data);
	}

	if(isset($_GET['ops']) && isset($_GET['s_id'])) {
		$errors_get=array();
		$data_get=array();

		$sql_get = "select * from bk_staff where s_id=".$_GET['s_id'];
		$result_get = mysql_query($sql_get);
		$row_get = mysql_fetch_array($result_get);
		
		if(md5($_GET['ops'])!=$row_get['s_password1']) $errors_get['ops']="原密码输入有误。";

		if(!empty($errors_get)) {
			$data_get['success']=false;
			$data_get['errors']=$errors_get;
		} else {
			$data_get['success']=true;
		}

		echo json_encode($data_get);
	}
?>
	

