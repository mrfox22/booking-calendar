<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
	<head>
		<meta charset="UTF-8">
		<title>登录</title>
		<meta name="viewport" content="width=460">
		<meta name="MobileOptimized" content="460" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="styles/ui.css" media="all" />
		<!--[if lt IE9]> 
			<script src="scripts/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body>
		<header>
			<h1>音乐中心预定平台</h1>
			<div id="loginfo">
				<a href="index.php">返回预订页面</a>
			</div>
		</header>

		<?php
			include("conn.php");

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
					$query=mysql_query($sql);
					if($rs=mysql_fetch_array($query)) {
						if($rs['s_right']!=3) {
							$_SESSION['flag']="logged";
							setcookie("name", $rs['s_name'], time()+3600);
							setcookie("sid", $rs['s_id'], time()+3600);
							$sql_log="update bk_staff set s_logged=s_logged+1 where s_id=".$rs['s_id'];
							if(mysql_query($sql_log)) {
								header("Location:index.php");
								exit();
							}
						} else {
							$status=false;
							echo "<script>alert('用户已被停用。请联系管理员。'); document.location.href='login.php';</script>";
							exit();
						}
					} else {
						$status=false;
					}
				}

				if ($status==false) {
					echo "<script>alert('请检查您的输入。'); document.location.href='login.php';</script>";
					exit();
				}
				
			}
		?>

		<form action="login.php" method="post">
			<fieldset id="login">
				用户名　<input class="con" type="text" name="s_username"><br><br>
				　密码　<input class="con" type="password" name="s_password1"><br /><br>
				<?php
				if(isset($_GET['do']) && $_GET['do'] == "calendar") {
					?>
					<input type="hidden" name="calendar" value="calender">
					<?php
				}
				?>
				<input type="submit" value="登录" id="login_btn"> 
			</fieldset>
		</form>
	</body>
</html>