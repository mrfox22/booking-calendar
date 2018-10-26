<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>音乐中心预定系统</title>
		<meta http-equiv="refresh" content="60">
		<meta name="viewport" content="width=1135">
		<meta name="MobileOptimized" content="1135" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="styles/ui.css" media="all" />
		<link rel="stylesheet" href="styles/simpletooltip.min.css" media="screen" />
		<script src="scripts/jquery.js"></script>
		<script src="scripts/simpletooltip.min.js"></script> 
		<script>
			jQuery(document).ready(function($) {
				$("td").simpletooltip({position:"top-right"});
			});
		</script>
		<meta name="renderer" content="webkit">  <!-- for 360 -->
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

			/*红黄绿预订选择器*/
			function booker($dpart, $wid, $dptitle, $ep) {
				if(!empty($dpart) && $dpart!="0,0,0,0") {  
					$wdparr=explode(",", $dpart);
					$refnumber=$wdparr[0];

					$sql_dt="select * from bk_detail1 where d_id=".$refnumber;
					$rs_dt=mysql_query($sql_dt);
					$row_dt=mysql_fetch_array($rs_dt);

					$color="green";
					for($icolor=1; $icolor<4; $icolor++) {
						if($wdparr[$icolor]==$refnumber) {
							if($row_dt['d_sid']!=$_COOKIE['sid']) $color="red";
						} else {
							$color="yellow";
							break;
						}
					}

					if($refnumber!=0) {
						$sql_nm="select * from bk_staff where s_id=".$row_dt['d_sid'];
						$rs_nm=mysql_query($sql_nm);
						$row_nm=mysql_fetch_array($rs_nm);
					
						switch($row_nm['s_depname']) {
							case "资讯采编部":
								$abbrdep="资讯部";
								break;
							case "融媒体部":
								$abbrdep="融媒体";
								break;
							case "音乐节目部":
								$abbrdep="音乐";
								break;
							case "都市节目部":
								$abbrdep="都市";
								break;
							default:
								$abbrdep=$row_nm['s_depname'];
						}
					}

					//时段处于整小时预定状态
					if($color=="red") return "<a href='review.php?wid=".$wid."&daypart=".$dptitle."'>".$row_nm['s_name']." - ".$abbrdep."</a>";	
				
					if($color=="green") {
						if($ep=="noexpired") return "<a href='modify.php?wid=".$wid."&daypart=".$dptitle."'>".$row_nm['s_name']." - ".$abbrdep."</a>";
						if($ep=="expired") return "<a href='review.php?wid=".$wid."&daypart=".$dptitle."'>".$row_nm['s_name']." - ".$abbrdep."</a>";
					}

					//时段处于整小时预定状态
					if($color=="yellow") return "<a href='review.php?wid=".$wid."&daypart=".$dptitle."'>多预订</a>";
				}
			}

			/*鼠标悬停提示*/
			function dtitle($dpart, $wid, $dparttext) {
				if(!empty($dpart) && $dpart!="0,0,0,0") {  
					$wdparr=explode(",", $dpart);
					$refnumber=$wdparr[0];
					
					$sql_dt="select * from bk_detail1 where d_id=".$refnumber;
					$rs_dt=mysql_query($sql_dt);
					$row_dt=mysql_fetch_array($rs_dt);
					
					$color="green";
					for($icolor=1; $icolor<4; $icolor++) {
						if($wdparr[$icolor]==$refnumber) {
							if($row_dt['d_sid']!=$_COOKIE['sid']) $color="red";
						} else {
							$color="yellow";
							break;
						}
					}

					$sql_dtall="select * from bk_detail1 where d_wid=".$wid." and d_dpart='".$dparttext."' order by d_detail";
					$rs_dtall=mysql_query($sql_dtall);
					$rowcount_dtall=mysql_num_rows($rs_dtall);

					if($color=="green" || $color=="red") {  
						$row_dtall=mysql_fetch_array($rs_dtall);
						echo $row_dtall['d_booker']." - ".$row_dtall['d_dep'].":<br>".$row_dtall['d_cont'];
					}

					if($color=="yellow") {
						$m=1;
						while($row_dtall=mysql_fetch_array($rs_dtall)) {
							if($m!=$rowcount_dtall) {
								echo $row_dtall['d_booker']." - ".$row_dtall['d_dep'].":<br>".$row_dtall['d_detail']."分<br>".$row_dtall['d_cont']."<br><br>";
								$m++;
							} else {
								echo $row_dtall['d_booker']." - ".$row_dtall['d_dep'].":<br>".$row_dtall['d_detail']."分<br>".$row_dtall['d_cont'];
							}
						}
					}
				}
			}
			
			$skipdays=!empty($_GET['skip'])?intval($_GET['skip']):0;
			$multiple=$skipdays>=0?ceil($skipdays/7):floor($skipdays/7);

			$day=date("w");
			$offset=!empty($day)?$day-1:6;
			$date_origin=date("Y-m-d", strtotime("-$offset day"));  //本周一 
			$date=date("Y-m-d", strtotime("$date_origin $multiple week"));  //被选择周的周一
			
			$d1=strtotime($date_origin);
			$d2=strtotime($date);
			$days=abs(round(($d1-$d2)/3600/24))+7;
			
			$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
			$rs_user=mysql_query($sql_user);
			$row_user=mysql_fetch_array($rs_user);
		?>
		<div class="nav1"></div>
		<div id="main">
			<table id="daypart">
				<?php 
					if($d1==$d2) {
						echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=-7\"><<上一周</a><span id='scenter'>本周</span><a class='aright' href=\"index.php?skip=+7\">下一周>></a></caption>";
					}	
					
					if($d1<$d2)  {  //往后翻
						if($row_user['s_right']==1) {
							echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=+".($days-14)."\"><<上一周</a><a class='acenter' href='index.php'>回到本周</a><a class='aright' href=\"index.php?skip=+".$days."\">下一周>></a></caption>";
						} else {
							if(($days-7)<=21) echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=+".($days-14)."\"><<上一周</a><a class='acenter' href='index.php'>回到本周</a><a class='aright' href=\"index.php?skip=+".$days."\">下一周>></a></caption>";
							if(($days-7)==28) echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=+".($days-14)."\"><<上一周</a><a class='acenter' href='index.php'>回到本周</a><span class='nav4'>随便写</span></caption>";
						}
					}

					if($d1>$d2)  {  //往前翻
						if($row_user['s_right']==1) {
							echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=-" .($days). "\"><<上一周</a><a class='acenter' href='index.php'>回到本周</a><a class='aright' href=\"index.php?skip=-" .($days-14). "\">下一周>></a></caption>";
						} else {
							if(($days-7)<=21) echo "<caption id='yearweek'><a class='aleft' href=\"index.php?skip=-" .($days). "\"><<上一周</a><a class='acenter' href='index.php'>回到本周</a><a class='aright' href=\"index.php?skip=-" .($days-14). "\">下一周>></a></caption>";
							if(($days-7)==28) echo "<caption id='yearweek'><span class='nav3'>随便写</span><a class='acentertor' href='index.php'>回到本周</a><a class='aright' href=\"index.php?skip=-" .($days-14). "\">下一周>></a></caption>";
						}
					}
				?>
				
				<?php
					if($row_user['s_right']==1 || $days<=35) {
						?>
						<tr id="datehour">
							<th></th>
							<th>09</th>
							<th>10</th>
							<th>11</th>
							<th>12</th>
							<th>13</th>
							<th>14</th>
							<th>15</th>
							<th>16</th>
						</tr>
						<?php
						/*如果周一在表里没有，先创建该周各天*/
						$sql_date="select * from bk_weekform1 where wdate='".$date."'";
						$rs_date=mysql_query($sql_date);
						if(!$row_date=mysql_fetch_array($rs_date)) {
							for($i=0; $i<7; $i++) {
								$wdate=date("Y-m-d", strtotime("$date +$i day"));  //算出被选择周的所有日期
								$weekarray=array("一", "二", "三", "四", "五", "六", "日");
								$wday="周".$weekarray[$i];  //建出“周一~周日”这7个字段
								$sql_in="insert into bk_weekform1 (wdate, wday) values ('$wdate', '$wday')";
								mysql_query($sql_in);
							}
						}

						/*输出weekform1*/
						$sql="select * from bk_weekform1 where wdate>='".$date."' order by wdate limit 7";
						$rs=mysql_query($sql);
						while($row=mysql_fetch_array($rs)) {
							?>
							<tr class="date">
								<td class='insiderheader'>
									<?php 
										echo "<span>";
										echo $row['wdate'];
										echo "</span>";
										echo "<span>";
										echo $row['wday']; 
										echo "</span>";
									?>
								</td>
								<?php
									$hourarray=array("09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00");
									$varietyarray=array($row['w9'], $row['w10'], $row['w11'], $row['w12'], $row['w13'], $row['w14'], $row['w15'], $row['w16']);
									$daypartarray=array("w9", "w10", "w11", "w12", "w13", "w14", "w15", "w16");
									$d_dpart=array("9", "10", "11", "12", "13", "14", "15", "16");

									for($n=0; $n<8; $n++) {
										if(date("Y-m-d H:i:s")<$row['wdate']." ".$hourarray[$n]) {  
											$expired="noexpired";
											if(!empty($varietyarray[$n]) && $varietyarray[$n]!="0,0,0,0") {  
											
												$wdparr=explode(",", $varietyarray[$n]);
												$refnumber=$wdparr[0];

												$sql_dt="select * from bk_detail1 where d_id=".$refnumber;
												$rs_dt=mysql_query($sql_dt);
												$row_dt=mysql_fetch_array($rs_dt);

												$color="green";
												for($icolor=1; $icolor<4; $icolor++) {
													if($wdparr[$icolor]==$refnumber) {
														if($row_dt['d_sid']!=$_COOKIE['sid']) $color="red";
													} else {
														$color="yellow";
														break;
													}
												}

												if($color=="green") {  //绿格子
													?>
													<td class='myhour1' title="<?php dtitle($varietyarray[$n], $row['wid'], $d_dpart[$n]);?>">
														<span class='insider1'>
															<?php echo booker($varietyarray[$n], $row['wid'], $daypartarray[$n], $expired);?>
														</span>
													</td>
													<?php
												} 
									
												if($color=="yellow") {  //黄格子
													$sql_dt="select * from bk_detail1 where d_wid=".$row['wid']." and d_sid=".$_COOKIE['sid']." and d_dpart=".$d_dpart[$n];
													$rs_dt=mysql_query($sql_dt);
													?>
													<td class='timezone1' title="<?php dtitle($varietyarray[$n], $row['wid'], $d_dpart[$n]);?>">
														<span class='insider1'><?php echo booker($varietyarray[$n], $row['wid'], $daypartarray[$n], $expired);?></span>
													</td>
													<?php
												}

												if($color=="red")	{  //红格子
													?>
													<td class='otherhour1'  title="<?php dtitle($varietyarray[$n], $row['wid'], $d_dpart[$n]);?>">
														<span class='insider1'>
															<?php echo booker($varietyarray[$n], $row['wid'], $daypartarray[$n], $expired);?>
														</span>
													</td>
													<?php
												}
												
											} else {  //无色格子
												?>
												<td class='noassign1'>
													<span class='insider1'>
														<?php
															if($row_user['s_right']!=4) {
																?>
																<a href='book.php?wid=<?php echo $row['wid'];?>&daypart=<?php echo $daypartarray[$n];?>'>未预定</a>
																<?php
															} else {
																echo 	"未预定";
															}
														?>
													</span>
												</td>
												<?php
											}
										} else {  //灰格子
											$expired="expired";
											?>
											<td class='expired1'  class='otherhour1'  title="<?php dtitle($varietyarray[$n], $row['wid'], $d_dpart[$n]);?>">
												<span class='insider1'><?php echo booker($varietyarray[$n], $row['wid'], $daypartarray[$n], $expired);?></span>
											</td>
											<?php
										}
									}
								?>
							</tr>
							<?php
						}
					}
				?>
			</table>
			<br><br>
			<a href="calendar/calendar.php" class="button">选题系统</a>
		</div>
	</body>
</html>