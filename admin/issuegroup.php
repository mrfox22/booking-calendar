<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Issue Group</title>

		<meta name="viewport" content="width=800">
		<meta name="MobileOptimized" content="800" /> 
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
		<?php 
			echo "<header>";
			echo "<h1>音乐中心后台管理</h1>";
			echo "<div id='loginfo'>";
			include("conn.php");
			adminlogincheck();
			echo "</div>";
			echo "</header>";
		
			//个人由代号转名称
			function codeToNameSingle($code) {
				$sql = "select s_name from bk_staff where s_id=".$code;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				return $row['s_name'];
			}
		?>
		
		<div class="nav1"></div>
		<div id="main">
			<table id="stuff">
				<caption><span>选题组管理</span> <span><a href="/admin/index.php">人员管理</a></span> <span><a href="/admin/index.php?do=dep">部门管理</a></span> <a href="createissuegroup.php">创建选题组</a></caption>
				<tr id="stuffitem">
					<th>序号</th>
					<th>选题组名称</th>
					<th>选题组代码</th>
					<th>成员</th>
					<th>管理员</th>
					<th>用户</th>
					<th>挂起</th>
				</tr>
				<?php
					//所有成员由代号转名称
					function codeToNameGroup($member) {
						$memberArr = explode(",", $member);
						$memberStr = "";
						foreach($memberArr as $m) $memberStr .= codeToNameSingle($m).",";
						$memberStr = trim($memberStr, ",");
						return $memberStr;
					}

					$n = 1;
					$sql = "select * from bk_issuegplist";
					$rs = mysql_query($sql);
					while($row = mysql_fetch_array($rs))  {
						/*
						$memberArr = explode(",", $row['member']);
						$memberStr = "";
						foreach($memberArr as $m) $memberStr .= codeToNamesingle($m).",";
						$memberStr = trim($memberStr, ",");
						*/

						
						$member = !empty($row['member']) ? codeToNameGroup($row['member']) : "";
						$admin = !empty($row['admin']) ? codeToNameGroup($row['admin']) : "";
						$user = !empty($row['user']) ? codeToNameGroup($row['user']) : "";

						echo "<tr>";
						echo "<td>".$n."</td>";
						echo "<td><a href='gprightallocate.php?id=".$row['id']."'>".$row['listname']."</a></td>";
						echo "<td>".$row['listcode']."</td>";
						//echo "<td>".$row['member']."</td>";
						echo "<td>".$member."</td>";
						echo "<td>".$admin."</td>";
						echo "<td>".$user."</td>";
						
						if(!empty($row['member'])) {
							echo "<td><a href='process.php?sid=".$row['id']."'>清空成员</a></td>";
						} else {
							echo "<td>已挂起</td>";
						}
						$n++;
					}
				?>
			</table>
		</div>
	</body>
</html>
