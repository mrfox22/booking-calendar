<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>个人信息</title>
		<meta name="viewport" content="width=460">
		<meta name="MobileOptimized" content="460" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="styles/ui.css" media="all" />
		<script src="scripts/jquery.js"></script>
		<script src="scripts/function2.js"></script>
		<!--[if lt IE9]> 
			<script src="scripts/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body>
		<?php
			echo "<header>";
			echo "<h1>个人信息与密码修改</h1>";
			echo "<div id='loginfo'>";
			include("conn.php"); 
			logincheck();
			echo "</div>";
			echo "</header>";

			if(!empty($_GET['id'])) { 
				$sql = "select * from bk_staff where s_id=" .$_GET['id'] ;
				$result = mysql_query($sql);
				if($row = mysql_fetch_array($result)) {
					?>
					<div class="nav1"></div>
					<div id="main">
						<div id="subform">
							<span class="subformtitle">用户名　<?php echo $row['s_username']; ?></span>
							<br><br>
							<span class="subformitem">姓名：</span><span class="subformcontent">　<?php echo $row['s_name']; ?></span>
							<br>
							<span class="subformitem">权限：</span><span class="subformcontent">　<?php echo $row['s_rtitle'];?></span>
							<br>
							<span class="subformitem">部门：</span><span class="subformcontent">　<?php echo $row['s_depname'];?></span>
							<br><br><br>
							<input type="checkbox" id="ckbox">　<label for="ckbox" class="subformtitle">密码修改</label>
							<br><br>
							<form action="changepw.php" method="post" id="pwchangeform">
								<span class="subformitem1">原密码：</span>
								<input type="password" disabled="disabled" name="ops" id="ops" class="inputstyle">　<span id="pOps"></span>
								<br>
								<span class="subformitem1">新密码：</span>
								<input type="password" disabled="disabled" name="ps1" id="ps1" class="inputstyle">　<span id="pPs1">密码只能包括字母（A-Z，a-z）及数字（0-9），长度在4~10位之间，且不能与原密码相同。</span>
								<br>
								<span class="subformitem1" disabled="disabled" >再次输入新密码：</span>
								<input type="password" disabled="disabled" name="ps2" id="ps2" class="inputstyle">　<span id="pPs2"></span>
								<input type="hidden" name="s_id" id="s_id" value="<?php echo $row['s_id'];?>">
								<br><br>
								<input type="submit" value="提交" id="sub" disabled="disabled">　<a href='index.php'>返回首页</a>　<span id="pSuccess"></span>
							</form>
						</div>
					</div>
					<?php
				}
			}
		?>
	</body>
</html>

