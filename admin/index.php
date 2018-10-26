<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>管理页面 - 首页</title>
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

			$page=!empty($_GET['page']) ? intval($_GET['page']) :1; 
			$pagesize=10;

			if (isset($_GET['do'])) {
				if($_GET['do'] == 'dep') {
					$sqlPag = "SELECT `depid`, `depcode`, `depname`
					FROM `bk_departments`";
				} else {
					header("location:index.php&do=dep");
					exit();
				}
			} else {
				$sqlPag = "SELECT `s_username` 
					FROM `bk_staff`
					WHERE `s_username` <> 'admin' AND `s_username` <> 'guest'";
			}

			$numq=mysql_query($sqlPag);
			$num=mysql_num_rows($numq);
			$pagenum=ceil($num/$pagesize); 

			// prePrintR($num);

			$offset=($page-1)*$pagesize;

			if (isset($_GET['do'])) {
				if ($_GET['do'] == 'dep') {
					$sql="SELECT `depid`, `depcode`, `depname` 
						FROM `bk_departments` 
						WHERE `depid` <> 8 
						ORDER BY depname
						LIMIT $offset, $pagesize";
				} else {
					header("location:index.php&do=dep");
					exit();
				}
			} else {
				$sql="SELECT * 
					FROM `bk_staff` 
					WHERE `s_username` <> 'guest' AND `s_username` <> 'admin' 
					ORDER BY CONVERT(`s_depname` USING gb2312), CONVERT(`s_name` USING gb2312) 
					LIMIT $offset, $pagesize";  	
			}

			$query=mysql_query($sql);  

			if (empty($num)) {
				$pagenum=1;
				echo "无记录";
				echo "<hr>";
			}

			If($page>$pagenum) echo "<script>alert('无法找到该页'); location.href='index.php'</script>";
		?>

		<div class="nav1"></div>
		<div id="main">
			<table id="stuff">
				<?php
				if(isset($_GET['do'])) {
					if ($_GET['do'] == 'dep') {
						?>
						<caption><span>部门管理</span> <span><a href="index.php">人员管理</a></span> <span><a href="issuegroup.php">选题组管理</a></span> <a href="add.php?do=dep">添加部门</a></caption>
						<tr id="stuffitem">
						<th>序号</th>
						<th>部门</th>
						<th>部门代号</th>
						<th>操作</th>
						<?php
					} else {
						header("location:index.php?do=dep");
						exit();
					}
				} else {
					?>
					<caption><span>人员管理</span> <span><a href="index.php?do=dep">部门管理</a></span> <span><a href="issuegroup.php">选题组管理</a></span> <a href="add.php">添加用户</a></caption>
					<tr id="stuffitem">
					<th>序号</th>
					<th>用户名</th>
					<th>姓名</th>
					<th>角色</th>
					<th>部门</th>
					<th>操作</th>
					<?php
				}
				?>
				
				
				</tr>
				<?php
				$n=10*($page-1)+1;
				while($rs=mysql_fetch_array($query))  {
					if(isset($_GET['do'])) {
						$brief2=$rs['depname'].nl2br('\n')."部门代码：".$rs['depcode'].nl2br('\n')."确定删除部门？";

						if ($_GET['do'] == "dep") {
							echo "<tr>";
							echo "<td class='number'>".$n."</td>";
							echo "<td>".$rs['depname']."</td>";
							echo "<td>".$rs['depcode']."</td>"; 

							?>
							<td>
								<a href="edit.php?do=dep&id=<?php echo $rs['depid']; ?>">编辑</a> 
								<a href="#<?php echo $n;?>" onclick="if(confirm('<?php echo $brief2;?>')) {document.location.href='delete.php?id=<?php echo $rs['depid']; ?>&do=dep'}; return false;">删除</a>
							</td>
							</tr>
							<?php
						} else {
							header("location:index.php");
							exit();
						}
					} else {
						$brief1=$rs['s_name'].nl2br('\n')."用户名：".$rs['s_username'].nl2br('\n')."重置密码为123456？";
						$brief2=$rs['s_name'].nl2br('\n')."用户名：".$rs['s_username'].nl2br('\n')."确定删除用户？";
						
						echo "<tr>";
						echo "<td class='number'>".$n."</td>";
						echo "<td>".$rs['s_username']."</td>"; 
						echo "<td>".$rs['s_name']."</td>";
						echo "<td>".$rs['s_rtitle']."</td>";
						echo "<td>".$rs['s_depname']."</td>";

						?>
						<td>
							<a href="edit.php?id=<?php echo $rs['s_id']; ?>">编辑</a> 
							<a href="#<?php echo $n;?>" onclick="if(confirm('<?php echo $brief1;?>')) {document.location.href='reset.php?id=<?php echo $rs['s_id']; ?>'}; return false;">重置密码</a> 
							<a href="#<?php echo $n;?>" onclick="if(confirm('<?php echo $brief2;?>')) {document.location.href='delete.php?id=<?php echo $rs['s_id']; ?>'}; return false;">删除</a>
						</td>
						</tr>
						<?php
					}

					$n++;
				}
				?>


				<!-- Pagination -->
				<tr>
					<td colspan="6">
						<?php
							if ($page!=1) echo "<a href='index.php?page=".($page-1)."'>上一页</a> ";

							if ($pagenum<=10) {
								for($i=1;$i<=$pagenum;$i++) {
								   $show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
								   echo $show." ";
								}
							} 

							if ($pagenum==11) {
								if ($page<=6) {
									for($i=1; $i<=11;$i++) {
										$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
										echo $show." ";
									}
								} 

								if ($page>6) {
									echo "<a href='index.php?page=1'>1</a> ... ";
									for($i=$page-4; $i<=11;$i++) {
										$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
										echo $show." ";
									}	
								}
							}

							if ($pagenum>11) {
								if ($page<=5) {
									for($i=1; $i<=10;$i++) {
										$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
										echo $show." ";
									}
									echo "... <a href='index.php?page=".$pagenum."'>".$pagenum."</a>";
								} 

								if ($page==6) {
									echo "<a href='index.php?page=1'>1</a> ";
									if ($pagenum<=$page+4){
										for($i=$page-4; $i<=$pagenum;$i++) {
											$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
											echo $show." ";
										}
									} elseif($pagenum==$page+5) {
										for($i=$page-4; $i<=$page+4;$i++) {
												$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
												echo $show." ";
										}
										echo " <a href='index.php?page=".$pagenum."'>".$pagenum."</a>";
									} else {
										for($i=$page-4; $i<=$page+4;$i++) {
											$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
											echo $show." ";
										}
										echo " ... <a href='index.php?page=".$pagenum."'>".$pagenum."</a>";
									}
								}

								if ($page>6) {
									echo "<a href='index.php?page=1'>1</a> ... ";
									if ($pagenum<=$page+4) {
										for($i=$page-4; $i<=$pagenum;$i++) {
											$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
											echo $show." ";
										}
									} elseif($pagenum==$page+5) {
										for($i=$page-4; $i<=$page+4;$i++) {
											$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
											echo $show." ";
										}
										echo " <a href='index.php?page=".$pagenum."'>".$pagenum."</a>";
									} else {
										for($i=$page-4; $i<=$page+4;$i++) {
											$show=($i!=$page)?"<a href='index.php?page=".$i."'>$i</a>":"<b>$i</b>";
											echo $show." ";
										}
										echo " ... <a href='index.php?page=".$pagenum."'>".$pagenum."</a>";
									}
								}
							} 

							if ($page!=$pagenum) echo " <a href='index.php?page=".($page+1)."'>下一页</a>";

							if (($pagenum==11 && $page>6) || $pagenum>11) {
								?>
								<form action="index.php" method="get">
									<input type="text" name="page">
									<input type="submit" value="go">
								</form>
								<?php
							}
						?>
					</td>
				</tr>
			</table>
		</div>

		
	</body>
</html>

