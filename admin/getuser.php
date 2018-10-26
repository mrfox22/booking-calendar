<?php
	include("../conn.php");
	adminlogincheckonly();

	if(isset($_POST['name']) && isset($_POST['username']) && isset($_POST['dep'])) {

		$errors=array();
		$data=array();

		$name=$_POST['name'];
		$username=$_POST['username'];
		$dep=$_POST['dep'];

		
		// From staff management 
		if (isset($_POST['userright'])) {
			$userright = $_POST['userright'];

			switch ($userright) {
				case 1:
					$r_title="管理员";
					break;
				case 2:
					$r_title="用户";
					break;
				case 3:
					$r_title="停用";
					break;
				default:
					$errors['userright']="需要为用户选择一个角色。";
			}

			$sqlPostDep = "SELECT `depcode`, `depname` 
				FROM `bk_departments` 
				WHERE `depid` = ". $dep;
			$queryPostDep = mysql_query($sqlPostDep);
			$rowPostDep = mysql_fetch_array($queryPostDep);

			$depname = $rowPostDep['depname'];
			$depcode = $rowPostDep['depcode'];
		}
		
		

		/* switch ($dep) {
			case 1:
				$depname="融媒体部";
				$depcode = "rmt";
				break;
			case 2:
				$depname="办公室";
				$depcode = "bgs";
				break;
			case 3:
				$depname="资讯采编部";
				$depcode = "zx";
				break;
			case 4:
				$depname="策划部";
				$depcode = "ch";
				break;
			case 5:
				$depname="音乐节目部";
				$depcode = "yyjmb";
				break;
			case 6:
				$depname="都市节目部";
				$depcode = "ds";
				break;
			case 7:
				$depname="音乐中心";
				$depcode = "yyzx";
				break;
			case 8:
				$depname="系统";
				$depcode = "xt";
				break;
			default:
				$errors['dep']="需要为用户选择一个部门。";
		} */
		
		if(!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_]{1,15}$/i', $username)) {  
			if (isset($_POST['userright'])) {
				$errors['username']="用户名只能包括字母（A-Z，a-z）、数字（0-9）及下划线（_），必须以字母或数字开头。最少2个字符，最多
					16个字符。";
				
			} else {
				$errors['username']="部门代码只能包括字母（A-Z，a-z）、数字（0-9）及下划线（_），必须以字母或数字开头。最少2个字符，最
					多16个字符。";
			}
			
		} else {
			if (isset($_POST['userright'])) {
				$sql1="select * from `bk_staff` where `s_username`='".$username."'";
				$query1=mysql_query($sql1);
				if($result1=mysql_fetch_array($query1)) $errors['username']="相同的用户名存在，请修改。";
			} else {
				$sql1="SELECT `depcode` 
					FROM `bk_departments` 
					WHERE `depcode`='".$username."'";
				$query1=mysql_query($sql1);
				if($result1=mysql_fetch_array($query1)) $errors['username']="相同的部门代码存在，请修改。";
			}
			
		}

		if(preg_match('/^\s*$/', $name)) {
			if (isset($_POST['userright'])) {
				$errors['name']="姓名不能为空。";
			} else {
				$errors['name']="部门名称不能为空。";
			}
		}
			

		if(preg_match('/\s/', $name)) {
			if (isset($_POST['userright'])) {
				$errors['name']="姓名中不能有空格。";
			} else {
				$errors['name']="部门名称中不能有空格。";
			}
		}
		
		if ( ! empty($errors)) {

			// if there are items in our errors array, return those errors
			$data['success'] = false;
			$data['errors']  = $errors;

		} else {

			// if there are no errors process our form, then return a message

			// DO ALL YOUR FORM PROCESSING HERE
			// THIS CAN BE WHATEVER YOU WANT TO DO (LOGIN, SAVE, UPDATE, WHATEVER)
			if (isset($userright)) {
				$md5ps1=md5("123456");
				$sql2="INSERT INTO `bk_staff` (`s_username`, `s_password1`, `s_right`, `s_name`, `s_rtitle`, `s_dep`, `s_depcode`, `s_depname`, `s_pwresetted`) VALUES ('$username', '$md5ps1', '$userright', '$name', '$r_title', '$dep', '$depcode', '$depname', 1)";
				mysql_query($sql2);
			} else {
				$sql2="INSERT INTO `bk_departments` 
					(`depnum`, `depcode`, `depname`, `depmembers`,  `depadmin`, `depusers`) 
					VALUES ('', '$username', '$name', '', '', '')";
				// echo $sql2; exit;
				mysql_query($sql2);
			}
			

			// show a message of success and provide a true success variable
			$data['success'] = true;
			$data['message'] = "提交成功！";
		}

		// return all our data to an AJAX call
		echo json_encode($data);
	}
	
?>
	