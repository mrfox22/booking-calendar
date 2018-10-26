<?php
	include("conn.php");
	logincheckforguest();

	if(isset($_POST['wid']) && isset($_POST['daypart'])) {
		$wid=$_POST['wid'];
		$dp=$_POST['daypart'];
			
		$dparray=array("w9"=>"09:00:00", "w10"=>"10:00:00", "w11"=>"11:00:00", "w12"=>"12:00:00", "w13"=>"13:00:00", "w14"=>"14:00:00", "w15"=>"15:00:00", "w16"=>"16:00:00");  
		$dparray2=array("w9"=>9, "w10"=>10, "w11"=>11, "w12"=>12, "w13"=>13, "w14"=>14, "w15"=>15, "w16"=>16); 
		
		$sql="select wdate, wday, ".$dp." as daypart from bk_weekform1 where wid=".$_POST['wid'];
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
		
		if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
			if(isset($_POST['book'])) {

				//1为book页面的提交处理
				if($_POST['book']==1) {
					if($color=="green") {
						echo "<script>alert('所选时段已被预订。'); document.location.href='index.php?skip=".$days."'</script>";
						exit();
					}
					if($color=="red") {
						echo "<script>alert('所选时段已被预订。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."'</script>";
						exit();
					}

					if($color=="nocolor" || $color=="yellow") {
						
						if(isset($_POST['dps1']) && isset($_POST['dps2'])) {
							$indexofs1=$_POST['dps1'];
							$indexofs2=$_POST['dps2'];
							
							$status=true;

							//如果为黄色，则须判断所选时段有没有人选过，有则状态为false，无则没有任何操作。
							if($color=="yellow") {
								for($i=$indexofs1; $i<=$indexofs2; $i++) {
									if($wdparr[$i]!=0) {
										$status=false;
										break;
									}
								}
							}
							
							if($status==true) {
								$bookct=htmltocode($_POST['bookct']);
								if(!preg_match('/^\s*$/', $bookct)) {
									switch($indexofs1) {
										case 0:
											$textofs1="00";
											break;
										case 1:
											$textofs1="15";
											break;
										case 2:
											$textofs1="30";
											break;
										case 3:
											$textofs1="45";
											break;
									}

									switch($indexofs2) {
										case 0:
											$textofs2="15";
											break;
										case 1:
											$textofs2="30";
											break;
										case 2:
											$textofs2="45";
											break;
										case 3:
											$textofs2="60";
											break;
									}

									$bookdt=$textofs1."~".$textofs2;
										
									$sql_insd="insert into bk_detail1 (d_wid, d_sid, d_date, d_day, d_dp, d_dpart, d_booker, d_dep, d_cont, d_detail, d_fvalue, d_ftext, d_lvalue, d_ltext) values ('$wid', '$bookersid', '$bookdate', '$bookday', '$dp', '$detailtime', '$bookername', '$bookerdep', '$bookct', '$bookdt', '$indexofs1', '$textofs1', '$indexofs2', '$textofs2')";
									mysql_query($sql_insd);
									
									$sql_dt="select * from bk_detail1 where d_wid=".$wid." and d_dp='".$dp."' and d_detail='".$bookdt."'";
									$rs_dt=mysql_query($sql_dt);
									$row_dt=mysql_fetch_array($rs_dt);

									for($indexofs1; $indexofs1<=$indexofs2; $indexofs1++) {
										$wdparr[$indexofs1]=$row_dt['d_id'];
									}
									$implodedvalue=implode(",", $wdparr);
									$sql_upw="update bk_weekform1 set ".$dp."='".$implodedvalue."' where wid=".$wid;
									mysql_query($sql_upw);
									
									if($_POST['dps1']==0 && $_POST['dps2']==3) {
										echo "<script>alert('预定已成功。'); document.location.href='index.php?skip=".$days."';</script>";
										exit();
									} else {
										echo "<script>alert('预定已成功。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
										exit();
									}
								} else {
									echo "<script>alert('预订事由不能为空。');history.back();</script>";
									exit();
								}
							} else {
								echo "<script>alert('预定失败。该小时已有预定。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
								exit();
							} 
						} else {
							echo "<script>alert('预定时间有误。'); history.back(); </script>";
							exit();
						}
					}
				} 

				//2为modify页面的修改处理，包括红绿黄三种。
				if($_POST['book']==2) {
					$did=$_POST['did'];

					$sql_one="select * from bk_detail1 where d_id=".$did;
					$rs_one=mysql_query($sql_one);
					if($row_one=mysql_fetch_array($rs_one)) {

						if(isset($_POST['dps1']) && isset($_POST['dps2'])) {
						
							$indexofs1=$_POST['dps1'];
							$indexofs2=$_POST['dps2'];
							
							$status=true;
							for($i=$indexofs1; $i<=$indexofs2; $i++) {
								if($wdparr[$i]!=0 && $wdparr[$i]!=$did) {
									$status=false;
									break;
								}
							}
							
							if($status==true) {
								$bookct=htmltocode($_POST['bookct']);

								if(!preg_match('/^\s*$/', $bookct)) {
									for($i=$indexofs1; $i<=$indexofs2; $i++) {
										$wdparr[$i]=$did;
									}

									$fvalue=$row_one['d_fvalue'];
									$lvalue=$row_one['d_lvalue'];

									//消wdparr中不用的did
									if($indexofs1>$fvalue) {
										for($i=$fvalue; $i<$indexofs1; $i++) {
											$wdparr[$i]=0;
										}
									}

									if($indexofs2<$lvalue) {
										for($i=$indexofs2+1; $i<=$lvalue; $i++) {
											$wdparr[$i]=0;
										}
									}

									$implodedvalue=implode(",", $wdparr);
									$sql_upw="update bk_weekform1 set ".$dp."='".$implodedvalue."' where wid=".$wid;
									mysql_query($sql_upw);

									switch($indexofs1) {
										case 0:
											$textofs1="00";
											break;
										case 1:
											$textofs1="15";
											break;
										case 2:
											$textofs1="30";
											break;
										case 3:
											$textofs1="45";
											break;
									}

									switch($indexofs2) {
										case 0:
											$textofs2="15";
											break;
										case 1:
											$textofs2="30";
											break;
										case 2:
											$textofs2="45";
											break;
										case 3:
											$textofs2="60";
											break;
									}

									$bookdt=$textofs1."~".$textofs2;
										
									$sql_upd="update bk_detail1 set d_detail='".$bookdt."', d_cont='".$bookct."', d_fvalue='".$indexofs1."', d_ftext='".$textofs1."', d_lvalue='".$indexofs2."', d_ltext='".$textofs2."' where d_id=".$did;
									mysql_query($sql_upd);
									
									if($_POST['dps1']==0 && $_POST['dps2']==3) {
										if($row_one['d_sid']==$_COOKIE['sid']) {
											echo "<script>alert('修改已成功。'); document.location.href='index.php?skip=".$days."';</script>";
											exit();
										} else {
											echo "<script>alert('修改已成功。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
											exit();
										}
									} else {
										echo "<script>alert('修改已成功。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
										exit();
									}
								} else {
									echo "<script>alert('预订事由不能为空。');history.back();</script>";
									exit();
								}
							} else {
								echo "<script>alert('无法预定。与其他预定时段有冲突。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
								exit();
							}
						} else {
							echo "<script>alert('预定时间有误。'); history.back(); </script>";
							exit();
						}
					} else {
						echo "<script>alert('预定不存在。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
						exit();
					}
				}

				//3为modify页面的删除处理
				if($_POST['book']==3) {
					$did=$_POST['did'];
					$sql_one="select * from bk_detail1 where d_id=".$did;
					$rs_one=mysql_query($sql_one);

					if($row_one=mysql_fetch_array($rs_one)) {

						if(isset($_POST['dps1']) && isset($_POST['dps2'])) {
							$indexofs1=$_POST['dps1'];
							$indexofs2=$_POST['dps2'];

							switch($indexofs1) {
								case 0:
									$textofs1="00";
									break;
								case 1:
									$textofs1="15";
									break;
								case 2:
									$textofs1="30";
									break;
								case 3:
									$textofs1="45";
									break;
							}

							switch($indexofs2) {
								case 0:
									$textofs2="15";
									break;
								case 1:
									$textofs2="30";
									break;
								case 2:
									$textofs2="45";
									break;
								case 3:
									$textofs2="60";
									break;
							}

							$bookdt=$textofs1."~".$textofs2;
							$sql_del="delete from bk_detail1 where d_id=".$did;
							mysql_query($sql_del);
							
							if($color=="green" || $color=="red") $implodedvalue="0,0,0,0";

							if($color=="yellow") {
								for($i=$indexofs1; $i<=$indexofs2; $i++) {
									$wdparr[$i]=0;
								}
								$implodedvalue=implode(",", $wdparr);
							}
							
							$sql_upw="update bk_weekform1 set ".$dp."='".$implodedvalue."' where wid=".$wid;
							mysql_query($sql_upw);
							
							if($implodedvalue=="0,0,0,0") {
								echo "<script>alert('删除已成功。'); document.location.href='index.php?skip=".$days."';</script>";
								exit();
							} else {
								echo "<script>alert('删除已成功。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
								exit();
							}
						} else {
							echo "<script>alert('预定时间有误。'); history.back(); </script>";
							exit();
						}
					} else {
						echo "<script>alert('预定不存在。'); document.location.href='review.php?wid=".$wid."&daypart=".$dp."';</script>";
						exit();
					}
				}

				if($_POST['book']==0) echo "<script>history.back();</script>";
			}
		} else {
			echo "<script>alert('所选时段已过期。'); document.location.href='index.php?skip=".$days."';</script>";  
			exit();
		}
	}
?>
	