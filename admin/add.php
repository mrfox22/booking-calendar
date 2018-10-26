<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>管理页面 - 添加</title>
		<meta name="viewport" content="width=460">
		<meta name="MobileOptimized" content="460" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="../styles/ui.css" media="all" />
		<script src="../scripts/jquery.js"></script>
		<script src="../scripts/function3.js"></script>
		
		<!--[if lt IE9]> 
			<script src="../scripts/html5shiv.js"></script>
		<![endif]--> 
	</head>

	<body>
		<header>
			<h1>音乐中心后台管理</h1>
			<div id='loginfo'>
				<?php
					include("../conn.php"); 
					adminlogincheck();
				?>
			</div>
		</header>

		<?php
		if (isset($_GET['do'])) {
			if ($_GET['do'] == 'dep') {
				?>
				<div class="nav1"></div>
				<div id="main">
					<div id="subform">
						<span class="subformtitle">添加部门</span>
						<br><br>
						<form action="getuser.php" method="post" id="adduserpost">
							<span class="subformitem">部门名称：</span>
							<input type="text" name="name" class="inputstyle" id="name">　<span id="pName"></span>
							<br>
							<span class="subformitem">部门代码：</span>
							<input type="text" name="username" class="inputstyle" id="username">　<span id="pUsername"></span>
							<input type="hidden" name="dep" id="dep" value="dep" />
							<br><br>
							<input type="submit" name="sub" id="sub" value="提交">　<a href='index.php?do=dep'>返回首页</a>　<span id="pSuccess"></span>
						</form>
					</div>
				</div>
				<?php
			} else {
				header("location:/admin/index.php?do=dep");
				exit();
			}
		} else {
			$sqlPostDep = "SELECT `depid`, `depname` 
				FROM `bk_departments` 
				ORDER BY CONVERT(`depname` USING gb2312)";
			$queryPostDep = mysql_query($sqlPostDep);
			?>
			<!--添加用户-->
			<div class="nav1"></div>
			<div id="main">
				<div id="subform">
					<span class="subformtitle">添加用户</span>
					<br><br>
					<form action="getuser.php" method="post" id="adduserpost">
						<span class="subformitem">姓名：</span>
						<input type="text" name="name" class="inputstyle" id="name">　<span id="pName"></span>
						<br>
						<span class="subformitem">用户名：</span>
						<input type="text" name="username" class="inputstyle" id="username">　<span id="pUsername"></span>
						<br>
						<span class="subformitem">权限：</span>
						<select name="userright" id="userright">
							<option value="1">管理员</option>
							<option value="2">用户</option>
							<option value="3">停用</option>
						</select>　<span id="pUserright"></span>
						<br>
						<span class="subformitem">部门：</span>
						<select name="dep" id="dep">
							<?php
							while ($rowPostDep = mysql_fetch_array($queryPostDep)) {
								echo "<option value='{$rowPostDep['depid']}'>{$rowPostDep['depname']}</option>";
							}
							?>
						</select>　<span id="pDep"></span>
						<br><br>
						<input type="submit" name="sub" id="sub" value="提交">　<a href='index.php'>返回首页</a>　<span id="pSuccess"></span>
					</form>
				</div>
			</div>
			<?php
		}
		?>
		
		
	</body>
</html>

