<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
	<head>
		<meta charset="utf-8" />
		<title>管理页面 - 登录</title>
		<meta name="viewport" content="width=460">
		<meta name="MobileOptimized" content="460" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="../styles/ui.css" media="all" />

		<!--[if lt IE9]> 
			<script src="../scripts/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body>
		<header>
			<h1>音乐中心后台管理</h1>
			<div id="loginfo">
				<a href="../index.php">返回预订页面</a>
			</div>
		</header>

		<?php	
			include("../conn.php");

			// 此处判断post过来有没有值，无值则页面无反应，且拒绝0。
			if (!empty($_POST["s_username"]) && !empty($_POST["s_password1"])) {	
				$usr = $_POST["s_username"];
				$pwd=md5($_POST["s_password1"]);	
				$status=true;
				
				// 这两行是处理用户名和密码中的html标签之类的非法字符的
				if (!filter_var($usr, FILTER_SANITIZE_STRING))  $status=false;
				if (!filter_var($pwd, FILTER_SANITIZE_STRING))  $status=false;

				if ($status==true) {
					$sql="select * from bk_staff where s_username='".$usr."' and s_password1='".$pwd."'";
					$rs=mysql_query($sql);
					$row=mysql_fetch_array($rs);
					if($row['s_right']==1) {
						$_SESSION['adminflag']="logged";
						setcookie("name", $row['s_name'], time()+36000);
						setcookie("sid", $row['s_id'], time()+36000);
						header("Location:index.php");	
						exit();
					} else {
						$status=false;
					}
				}

				if ($status==false) {
					echo "<script>alert('您的输入有误或权限不足。'); document.location.href='login.php';</script>";
					exit();
				}
			}
		?>

		<form action="login.php" method="post">
			<fieldset id="login">
				用户名　<input  class="con" type="text" name="s_username"><br><br>
			    　密码　<input  class="con" type="password" name="s_password1"><br><br>
				<input type="submit" value="登录"  id="login_btn"> 
			</fieldset>
		</form>
	</body>
</html>