<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>查看</title>
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
		<?php
			echo "<header>";
			echo "<h1>1702会议室预定</h1>";
			echo "<div id='loginfo'>";
			include("conn.php");
			logincheck();
			echo "</div>";
			echo "</header>";

			//review未过期接受yellow和red，过期接受green、red和yellow。
			$dparray=array("w9"=>"09:00:00", "w10"=>"10:00:00", "w11"=>"11:00:00", "w12"=>"12:00:00", "w13"=>"13:00:00", "w14"=>"14:00:00", "w15"=>"15:00:00", "w16"=>"16:00:00");
			$dparray1=array("w9", "w10", "w11", "w12", "w13", "w14", "w15", "w16");
			$dparray2=array("w9"=>9, "w10"=>10, "w11"=>11, "w12"=>12, "w13"=>13, "w14"=>14, "w15"=>15, "w16"=>16); 

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
				
				$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
				$rs_user=mysql_query($sql_user);
				$row_user=mysql_fetch_array($rs_user);
				
				$bookday=$row['wday'];
				$detailtime=$dparray2[$dp];
				$bookersid=$_COOKIE['sid'];
				$bookername=$_COOKIE['name'];
				$bookerdep=$row_user['s_depname'];

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
				
				$m=0;  //数组下标，从0开始。
				if($color=="red") {
					$brief="取消预定".nl2br('\n')."日期：".$row['wdate']."（".$row['wday']."）".nl2br('\n')."时段：".$dparray2[$dp].":00 ~ ".($dparray2[$dp]+1).":00".nl2br('\n')."事由：".$rows_dt[$m]['d_cont'];
					?>
					<div class="nav1"></div>
					<div id="main">
						<table class="book">
							<caption class="info_1702"><?php echo $row['wdate']."（".$row['wday']."）".$dparray2[$dp]."点";?></caption>
							<tr class="row1_1702">
								<td class="col1">时段</td>
								<td class="col2"><?php echo $dparray2[$dp].":00~".($dparray2[$dp]+1).":00";?></td>
							</tr>
							<tr class="row1_1702">
								<td class="col1">状态</td>
								<td class="col2">已预订　<?php echo $rows_dt[$m]['d_booker']." - ".$rows_dt[$m]['d_dep'];?></td>
							</tr>
							<tr class="row1_1702">
								<td class="col1">事由</td>
								<td class="col2"><?php echo $rows_dt[$m]['d_cont'];?></td>
							</tr>
							<tr class="row2_1702">
								<td colspan="2">
									<?php
										if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
											if($row_user['s_right']==1) {
												?>
												<a href="modify.php?did=<?php echo $rows_dt[$m]['d_id'];?>">修改</a>
												<button class="bt" name="book" onclick="if(confirm('<?php echo $brief;?>')) {document.location.href='delete.php?did=<?php echo $rows_dt[$m]['d_id'];?>'}; return false;">删除</button>
												<?php
											}
										}
									?>
								</td>
							</tr>
						</table>
					</div>
					<div class="nav1">
						<div id="innerbtmcenter">
							<a class="a1702" href="index.php?skip=<?php echo $days;?>">返回首页</a>
						</div>
					</div>
					<?php
				}

				if($color=="green") {
					if(date("Y-m-d H:i:s")>=$row['wdate']." ".$dparray[$dp]) {
						?>
						<div class="nav1"></div>
						<div id="main">
							<table class="book">
								<caption class="info_1702"><?php echo $row['wdate']."（".$row['wday']."）".$dparray2[$dp]."点";?></caption>
								<tr class="row1_1702">
									<td class="col1">时段</td>
									<td class="col2"><?php echo $dparray2[$dp].":00~".($dparray2[$dp]+1).":00";?></td>
								</tr>
								<tr class="row1_1702">
									<td class="col1">状态</td>
									<td class="col2">已预订　<?php echo $rows_dt[$m]['d_booker']." - ".$rows_dt[$m]['d_dep'];?></td>
								</tr>
								<tr class="row1_1702">
									<td class="col1">事由</td>
									<td class="col2"><?php echo $rows_dt[$m]['d_cont'];?></td>
								</tr>
								<tr class="row2_1702">
									<td colspan="2">
										
									</td>
								</tr>
							</table>
						</div>
						<div class="nav1">
							<div id="innerbtmcenter">
								<a class="a1702" href="index.php?skip=<?php echo $days;?>">返回首页</a>
							</div>
						</div>
						<?php
					} else {
						echo "<script>alert('该小时为您的整小时预订。');document.location.href='index.php?skip=".$days."'</script>";  
						exit();
					}
				}

				if($color=="yellow") {
					$former=0;  //依然从0开始，因为其实时间是0分0秒。由于$former要参与运算，所以这里必须用数字类型，而不能是"00"这样的字符串。
					$n=1;  //显示出来的序号，由于不一定跟$i完全同步，比如跨几个时段预定，所以单独拿出来做运算。
					?>
					<div class="nav1"></div>
					<div id="main">
						<table class="book">
							<caption class="info_1702"><?php echo $row['wdate']."（".$row['wday']."）".$dparray2[$dp]."点";?> </caption>
							<?php
								//错后输出
								for($i=0; $i<5; $i++) {
									if($i!=4) {
										if($wdparr[$i]!=$refnumber) {  //只有当前遍历值不等于参考值时才输出
											$latter=$i*15;
											$theFormerInString=$former==0?"00":$former;
											$theLatterInString=$latter==60?"00":$latter;
											$hourStart=$dparray2[$dp];
											$hourEnd=$latter==60?((int)$hourStart)+1:$dparray2[$dp];
											?>
											<tr class="row3">
												<td colspan="2" class="col3"><?php echo $n; ?>#</td>
											</tr>
											<tr class="row1_1702">
												<td class="col1">时段</td>
												<td class="col2"><?php echo $hourStart.":".$theFormerInString."~".$hourEnd.":".$theLatterInString;?></td>
											</tr>
											<?php
												if($refnumber!=0) {
													$brief="取消预定".nl2br('\n')."日期：".$row['wdate']."（".$row['wday']."）".nl2br('\n')."时段：".$hourStart.":".$theFormerInString." ~ ".$hourEnd.":".$theLatterInString.nl2br('\n')."事由：".$rows_dt[$m]['d_cont'];
													?>
													<tr class="row1_1702">
														<td class="col1">状态</td>
														<td class="col2">已预订　<?php echo $rows_dt[$m]['d_booker']." - ".$rows_dt[$m]['d_dep'];?></td>
													</tr>
													<tr class="row1_1702">
														<td class="col1">事由</td>
														<td class="col2"><?php echo $rows_dt[$m]['d_cont'];?></td>
													</tr>
													<tr class="row2_1702">
														<td colspan="2">
															<?php
																if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
																	if($_COOKIE['sid']==$rows_dt[$m]['d_sid'] || $row_user['s_right']==1) {
																		?>
																		<a href="modify.php?did=<?php echo $rows_dt[$m]['d_id'];?>">修改</a>
																		<button class="bt" name="book" onclick="if(confirm('<?php echo $brief;?>')) {document.location.href='delete.php?did=<?php echo $rows_dt[$m]['d_id'];?>'}; return false;">删除</button>
																		<?php
																	}
																}
															?>
														</td>
													</tr>
													<?php
													$m++;
												} else {
													?>
													<tr class="row1_1702">
														<td class="col1">状态</td>
														<td class="col2">未预定</td>
													</tr>
													<tr class="row2_1702">
														<td colspan="2">
															<?php
																if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp] && $row_user['s_right']!=4) {
																	?>
																	<a href="book.php?wid=<?php echo $wid;?>&daypart=<?php echo $dp;?>&s1=<?php echo $former;?>&s2=<?php echo $latter;?>">预定</a>
																	<?php
																}
															?>
														</td>
													</tr>
													<?php
												}
												$former=$i*15;
												$refnumber=$wdparr[$i];  //当前遍历值成为参考值，供下一个遍历数参考。
												$n++;
											?>
											<tr class="row4_1702"><td colspan="2"></td></tr>
											<?php
										}
									} else {  //因为是错后输出，因此当遍历不满足条件时，还会有最后一个值需要输出。
										$latter=$i*15;
										$theFormerInString=$former==0?"00":$former;
										$theLatterInString=$latter==60?"00":$latter;
										$hourStart=$dparray2[$dp];
										$hourEnd=$latter==60?((int)$hourStart)+1:$dparray2[$dp];
										?>
										<tr class="row3">
											<td colspan="2" class="col3"><?php echo $n; ?>#</td>
										</tr>
										<tr class="row1_1702">
											<td class="col1">时段</td>
											<td class="col2"><?php echo $hourStart.":".$theFormerInString."~".$hourEnd.":".$theLatterInString;?></td>
										</tr>
										<?php
											if($refnumber!=0) {
												
												$brief="取消预定".nl2br('\n')."日期：".$row['wdate']."（".$row['wday']."）".nl2br('\n')."时段：".$hourStart.":".$theFormerInString." ~ ".$hourEnd.":".$theLatterInString.nl2br('\n')."事由：".$rows_dt[$m]['d_cont'];
												?>
												<tr class="row1_1702">
													<td class="col1">状态</td>
													<td class="col2">已预订　<?php echo $rows_dt[$m]['d_booker']." - ".$rows_dt[$m]['d_dep'];?></td>
												</tr>
												<tr class="row1_1702">
													<td class="col1">事由</td>
													<td class="col2"><?php echo $rows_dt[$m]['d_cont'];?></td>
												</tr>
												<tr class="row2_1702">
													<td colspan="2">
														<?php
															if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
																if($_COOKIE['sid']==$rows_dt[$m]['d_sid'] || $row_user['s_right']==1) {
																	?>
																	<a href="modify.php?did=<?php echo $rows_dt[$m]['d_id'];?>">修改</a>
																	<button class="bt" name="book" onclick="if(confirm('<?php echo $brief;?>')) {document.location.href='delete.php?did=<?php echo $rows_dt[$m]['d_id'];?>'}; return false;">删除</button>
																	<?php
																}
															}
														?>
													</td>
												</tr>
												<?php
											} else {
												?>
												<tr class="row1_1702">
													<td class="col1">状态</td>
													<td class="col2">未预定</td>
												</tr>
												<tr class="row2_1702">
													<td colspan="2">
														<?php
															if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp] && $row_user['s_right']!=4) {
																?>
																<a href="book.php?wid=<?php echo $wid;?>&daypart=<?php echo $dp;?>&s1=<?php echo $former;?>&s2=<?php echo $latter;?>">预定</a>
																<?php
															}
														?>
													</td>
												</tr>
												<?php
											}
									}
								}
							?>
						</table>
					</div>
					<div class="nav1">
						<div id="innerbtmcenter">
							<a class="a1702" href="index.php?skip=<?php echo $days;?>">返回首页</a>
						</div>
					</div>
					<?php
				}
				
				if($color=="nocolor") {
					echo "<script>alert('该小时无预订。'); document.location.href='index.php?skip=".$days."'</script>";
					exit();
				}
			}
		?>
	</body>
</html>