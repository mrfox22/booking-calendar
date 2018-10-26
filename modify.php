<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>修改</title>
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
		<script src="scripts/function.js"></script>
		<!--[if lt IE9]> 
			<script src="scripts/html5shiv.js"></script>
		<![endif]-->
	</head>

	<body>
		<?php
			echo "<header>";
			echo "<h1>1702会议室预定</h1>";
			echo "<div id='loginfo'>";
			include("conn.php");
			logincheck();
			echo "</div>";
			echo "</header>";

			//modify只对red、green和yellow有用
			$dparray=array("w9"=>"09:00:00", "w10"=>"10:00:00", "w11"=>"11:00:00", "w12"=>"12:00:00", "w13"=>"13:00:00", "w14"=>"14:00:00", "w15"=>"15:00:00", "w16"=>"16:00:00");
			$dparray1=array("w9", "w10", "w11", "w12", "w13", "w14", "w15", "w16");
			$dparray2=array("w9"=>9, "w10"=>10, "w11"=>11, "w12"=>12, "w13"=>13, "w14"=>14, "w15"=>15, "w16"=>16);

			$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
			$rs_user=mysql_query($sql_user);
			$row_user=mysql_fetch_array($rs_user);
			
			$bookersid=$_COOKIE['sid'];
			$bookername=$_COOKIE['name'];
			$bookerdep=$row_user['s_depname'];

			//green
			if(!empty($_GET['wid']) && !empty($_GET['daypart']) && in_array($_GET['daypart'], $dparray1)) {	
				$wid=$_GET['wid'];
				$dp=$_GET['daypart'];

				$sql="select wdate, wday, ".$_GET['daypart']." as daypart from bk_weekform1 where wid=".$_GET['wid'];
				$rs=mysql_query($sql);
				$row=mysql_fetch_array($rs);
				
				$day=date("w"); 
				$offset=!empty($day)?$day-1:6;  
				$date_origin=date("Y-m-d", strtotime("-$offset day"));   
				$bookdate=$row['wdate']; 
				$d1=strtotime($date_origin);
				$d2=strtotime($bookdate);
				$days=intval(($d2-$d1)/3600/24/7)*7;  
				
				$bookday=$row['wday'];
				$detailtime=$dparray2[$dp];

				$sql_dt="select * from bk_detail1 where d_wid=".$wid." and d_dp='".$dp."' order by d_fvalue";
				$rs_dt=mysql_query($sql_dt);
				$rowcount_dt=mysql_num_rows($rs_dt);
				$rows_dt=array();
				while($row_dt=mysql_fetch_assoc($rs_dt)) $rows_dt[]=$row_dt;

				$wdparr=empty($row['daypart'])? array(0, 0, 0, 0): explode(",", $row['daypart']);
				$refnumber=$wdparr[0];
				if($rowcount_dt==1) {
					for($icolor=0; $icolor<4; $icolor++) {
						if($wdparr[$icolor]==0) {
							$color="yellow";
							break;
						} else {
							$color=$rows_dt[0]['d_sid']!=$_COOKIE['sid'] ?"red":"green";
						}
					}
				}
				if($rowcount_dt>1) $color="yellow";
				if($wdparr==array(0, 0, 0, 0)) $color="nocolor";

				if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
					if($color=="green" || $color=="red") {
						if($rows_dt[0]['d_sid']==$_COOKIE['sid'] || $row_user['s_right']==1) {
							$s1=$rows_dt[0]['d_fvalue'];
							$s2=$rows_dt[0]['d_fvalue'];
							$did=$rows_dt[0]['d_id'];
							?>
							<div class="nav1"></div>
							<div id="main">
								<table class="book">
									<caption class="info_1702"><?php echo $row['wdate']."（".$row['wday']."）".$dparray2[$dp]."点";?></caption>
									
									<input type="hidden" id="dateAndDay" value="<?php echo $row['wdate']."（".$row['wday']."）";?>">
									<input type="hidden" id="selectedHour" value="<?php echo $dparray2[$dp];?>">

									<form action="process.php" method="post" id="modifyform">
										<input type="hidden" name="daypart" value="<?php echo $dp;?>">
										<input type="hidden" name="wid" value="<?php echo $wid;?>">
										<input type="hidden" name="did" value="<?php echo $rows_dt[0]['d_id'];?>">

										<tr class="row1_1702">
											<td class="col1">时段</td>
											<td class="col2">
												从
												<select name="dps1" id="s1">
													<option value="<?php echo $wdparr[0];?>" disabled="disabled">00</option>
													<option value="<?php echo $wdparr[1];?>" disabled="disabled">15</option>
													<option value="<?php echo $wdparr[2];?>" disabled="disabled">30</option>
													<option value="<?php echo $wdparr[3];?>" disabled="disabled">45</option>
												</select>
												 ~ 
												<select name="dps2" id="s2">
													<option value="<?php echo $wdparr[0];?>" disabled="disabled">15</option>
													<option value="<?php echo $wdparr[1];?>" disabled="disabled">30</option>
													<option value="<?php echo $wdparr[2];?>" disabled="disabled">45</option>
													<option value="<?php echo $wdparr[3];?>" disabled="disabled">60</option>
												</select>
												分
											</td>
										</tr>
										<tr class="row1_1702">
											<td class="col1">状态</td>
											<td class="col2">已预订　<?php echo $rows_dt[0]['d_booker']." - ".$rows_dt[0]['d_dep'];?></td>
										</tr>
										<tr class="row1_1702">
											<td class="col1">事由</td>
											<td class="col2"><input type="text" name="bookct" id="bookct1" value="<?php echo $rows_dt[0]['d_cont'];?>">　<span id="pBookct"></span></td>
										</tr>
										<tr class="row2_1702">
											<td colspan="2">
												<button class="bt" name="book" type="submit" id="bt3">提交</button>
												<button class="bt" name="book" type="submit" id="bt2">删除</button>
											</td>
										</tr>
									</form>
								</table>
							</div>

							<div class="nav1">
								<div id="innerbtmcenter">
									<a class="a1702" href="index.php?skip=<?php echo $days;?>">返回首页</a>
								</div>
							</div>
							<?php
						} else {
							echo "<script>alert('您无权进行操作。'); document.location.href='index.php?skip=".$days."';</script>";
							exit();
						}
					}
					
					if($color=="yellow") {
						echo "<script>alert('该小时为多时段预定。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."'</script>";
						exit();
					}
					if($color=="nocolor") {
						echo "<script>alert('该小时无预定。'); document.location.href='index.php?skip=".$days."';</script>";
						exit();
					}
				} else {
					 echo "<script>alert('时段已过期。'); document.location.href='index.php?skip=".$days."';</script>";
					 exit();
				}
			}

			//yellow & red
			if(!empty($_GET['did'])) {
				$did=$_GET['did'];
				
				$sql_one="select * from bk_detail1 where d_id=".$did;
				$rs_one=mysql_query($sql_one);
				if($row_one=mysql_fetch_array($rs_one)) {

					$sql="select wdate, wday, ".$row_one['d_dp']." as daypart from bk_weekform1 where wid=".$row_one['d_wid'];
					$rs=mysql_query($sql);
					$row=mysql_fetch_array($rs);

					$day=date("w"); 
					$offset=!empty($day)?$day-1:6; 
					$date_origin=date("Y-m-d", strtotime("-$offset day"));  
					$bookdate=$row['wdate']; 
					$d1=strtotime($date_origin);
					$d2=strtotime($bookdate);
					$days=intval(($d2-$d1)/3600/24/7)*7;  

					$bookday=$row['wday'];
					$detailtime=$row_one['d_dpart'];

					$sql_dt="select * from bk_detail1 where d_wid=".$row_one['d_wid']." and d_dp='".$row_one['d_dp']."' order by d_fvalue";
					$rs_dt=mysql_query($sql_dt);
					$rowcount_dt=mysql_num_rows($rs_dt);
					$rows_dt=array();
					while($row_dt=mysql_fetch_assoc($rs_dt)) $rows_dt[]=$row_dt;

					$wdparr=empty($row['daypart'])? array(0, 0, 0, 0): explode(",", $row['daypart']);
					$refnumber=$wdparr[0];
					if($rowcount_dt==1) {
						for($icolor=0; $icolor<4; $icolor++) {
							if($wdparr[$icolor]==0) {
								$color="yellow";
								break;
							} else {
								$color=$rows_dt[0]['d_sid']!=$_COOKIE['sid'] ?"red":"green";
							}
						}
					}
					if($rowcount_dt>1) $color="yellow";
					if($wdparr==array(0, 0, 0, 0)) $color="nocolor";
					
					$s1=$row_one['d_ftext'];
					$s2=$row_one['d_ltext'];

					$wdparr=explode(",", $row['daypart']);
					
					$dp=$row_one['d_dp'];
					if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
						if($row_one['d_sid']==$_COOKIE['sid'] || $row_user['s_right']==1) {
							?>
							<div class="nav1"></div>
							<div id="main">
								<table class="book">
									<caption class="info_1702"><?php echo $row_one['d_date']."（".$row_one['d_day']."）".$row_one['d_dpart']."点";?></caption>
									
									<input type="hidden" id="dateAndDay" value="<?php echo $row_one['d_date']."（".$row_one['d_day']."）";?>">
									<input type="hidden" id="selectedHour" value="<?php echo $row_one['d_dpart'];?>">
									<input type="hidden" id="submittedS1" value="<?php echo $s1;?>">
									<input type="hidden" id="submittedS2" value="<?php echo $s2;?>">

									<form action="process.php" method="post" id="modifyform">
										
										<input type="hidden" name="daypart" value="<?php echo $row_one['d_dp'];?>">
										<input type="hidden" name="wid" value="<?php echo $row_one['d_wid'];?>">
										<input type="hidden" name="did" value="<?php echo $did;?>">

										<tr class="row1_1702">
											<td class="col1">时段</td>
											<td class="col2">
												从
												<select name="dps1" id="s1">
													<option value="<?php echo $wdparr[0];?>" disabled="disabled">00</option>
													<option value="<?php echo $wdparr[1];?>" disabled="disabled">15</option>
													<option value="<?php echo $wdparr[2];?>" disabled="disabled">30</option>
													<option value="<?php echo $wdparr[3];?>" disabled="disabled">45</option>
												</select>
												 ~ 
												<select name="dps2" id="s2">
													<option value="<?php echo $wdparr[0];?>" disabled="disabled">15</option>
													<option value="<?php echo $wdparr[1];?>" disabled="disabled">30</option>
													<option value="<?php echo $wdparr[2];?>" disabled="disabled">45</option>
													<option value="<?php echo $wdparr[3];?>" disabled="disabled">60</option>
												</select>
												分
											</td>
										</tr>

										<tr class="row1_1702">
											<td class="col1">状态</td>
											<td class="col2">已预订　<?php echo $row_one['d_booker']." - ".$row_one['d_dep'];?></td>
										</tr>
						
										<tr class="row1_1702">
											<td class="col1">事由</td>
											<td class="col2"><input type="text" name="bookct" id="bookct1" value="<?php echo $row_one['d_cont'];?>">　<span id="pBookct"></span></td>
										</tr>

										<tr class="row2_1702">
											<td colspan="2">
												<button class="bt" name="book" type="submit" id="bt3">提交</button>
												<button class="bt" name="book" type="submit" id="bt2">删除</button>
											</td>
										</tr>
									</form>
								</table>
							</div>
							<div class="nav1">
								<div id="innerbtmcenter">
									<a class="a1702" href="index.php?skip=<?php echo $days;?>">返回首页</a>
									<a class="a1702" href="review.php?wid=<?php echo $row_one['d_wid'];?>&daypart=<?php echo $row_one['d_dp'];?>">返回时段</a>
								</div>
							</div>
							<?php
						} else {
							echo "<script>alert('您无权进行操作。'); history.back();</script>";
							exit();
						}
					} else {
						echo "<script>alert('所选时段已过期。'); document.location.href='index.php?skip=".$days."';</script>";
						exit();
					}
				} else {
					echo "<script>alert('所选时段无预定。'); history.back();</script>";
					exit();
				}
			}
		?>
	</body>

	<script>
		var color = "<?php echo $color;?>";
		var optS1 = document.getElementById("s1").options;
		var optS2 = document.getElementById("s2").options;
		var jWdparr = <?php echo json_encode($wdparr); ?>;
		var dId = "<?php echo $did;?>";
	</script>
</html>