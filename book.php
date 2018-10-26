<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>预定</title>
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

			//book只接受无色和黄色
			$dparray=array("w9"=>"09:00:00", "w10"=>"10:00:00", "w11"=>"11:00:00", "w12"=>"12:00:00", "w13"=>"13:00:00", "w14"=>"14:00:00", "w15"=>"15:00:00", "w16"=>"16:00:00"); 
			$dparray1=array("w9", "w10", "w11", "w12", "w13", "w14", "w15", "w16");
			$dparray2=array("w9"=>9, "w10"=>10, "w11"=>11, "w12"=>12, "w13"=>13, "w14"=>14, "w15"=>15, "w16"=>16); 

			if(!empty($_GET['wid']) && !empty($_GET['daypart']) && in_array($_GET['daypart'], $dparray1)) {	 
				$wid=$_GET['wid'];
				$dp=$_GET['daypart'];
				$s1=isset($_GET['s1'])?$_GET['s1']:"novalue";
				$s2=isset($_GET['s2'])?$_GET['s2']:"novalue";

				$sql="select wdate, wday, ".$_GET['daypart']." as daypart from bk_weekform1 where wid=".$_GET['wid'];
				$rs=mysql_query($sql);
				if($row=mysql_fetch_array($rs)) {  
				
					//以下为计算离本周一的偏移
					$day=date("w");
					$offset=!empty($day)?$day-1:6;  
					$date_origin=date("Y-m-d", strtotime("-$offset day"));   
					$bookdate=$row['wdate'];  
					$d1=strtotime($date_origin);
					$d2=strtotime($bookdate);
					$days=intval(($d2-$d1)/3600/24/7)*7;  
					//以上为计算离本周一的偏移

					$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
					$rs_user=mysql_query($sql_user);
					$row_user=mysql_fetch_array($rs_user);
					
					$bookday=$row['wday'];
					$detailtime=$dparray2[$dp];
					$bookersid=$_COOKIE['sid'];
					$bookername=$_COOKIE['name'];
					$bookerdep=$row_user['s_depname'];

					//以下为颜色判断
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
					//以上为颜色判断

					if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) { 
						if($row_user['s_right']!=4) {
							if($color=="nocolor" || $color=="yellow") {  
								?>
								<div class="nav1"></div>
								<div id="main">
									<table class="book" id="book">
										<caption class="info_1702"><?php echo $row['wdate']."（".$row['wday']."）".$dparray2[$dp]."点";?></caption>
										
										<input type="hidden" id="dateAndDay" value="<?php echo $row['wdate']."（".$row['wday']."）";?>">
										<input type="hidden" id="selectedHour" value="<?php echo $dparray2[$dp];?>">
										<input type="hidden" id="submittedS1" value="<?php echo $s1;?>">
										<input type="hidden" id="submittedS2" value="<?php echo $s2;?>">

										<form action="process.php" method="post" id="bookform">
											<input type="hidden" name="daypart" value="<?php echo $dp;?>">
											<input type="hidden" name="wid" value="<?php echo $wid;?>">
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
												<td class="col2">预订中</td>
											</tr>
											<tr class="row1_1702">
												<td class="col1">事由</td>
												<td class="col2">
													<input type="text" name="bookct" id="bookct">　<span id="pBookct"></span>
												</td>
											</tr>
											<tr class="row2_1702">
												<td colspan="2">
													<button class="bt" name="book" type="submit" id="bt1">提交</button>
												</td>
											</tr>
										</form>
									</table>
								</div>
								<div class="nav1">
									<div id="innerbtmcenter">
										<?php
											if($color=="nocolor" || $color=="yellow") echo "<a class='a1702' href='index.php?skip=".$days."'>返回首页</a>";
											if($color=="yellow") echo "<a class='a1702' href='review.php?wid=".$wid."&daypart=".$dp."'>返回时段</a>";
										?>
									</div>
								</div>
								<?php
							}
							
							if($color=="green") {
								echo "<script>alert('所选时段已被预订。'); document.location.href='index.php?skip=".$days."';</script>";
								exit();
							}
							if($color=="red") {
								echo "<script>alert('所选时段已被预订。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."'</script>";
								exit();
							}
						} else {
							echo "<script>alert('您无权进行操作。'); document.location.href='index.php?skip=".$days."';</script>";
							exit();
						}
					} else {
						echo "<script>alert('所选时段已过期。'); document.location.href='index.php?skip=".$days."';</script>"; 
						exit();
					}
				}
			}
		?>
	</body>

	<script>
		var jWdparr = <?php echo json_encode($wdparr); ?>;
		var color;
	</script>
</html>