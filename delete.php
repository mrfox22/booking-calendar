<?php
	include("conn.php");
	logincheckforguest();

	$dparray=array("w9"=>"09:00:00", "w10"=>"10:00:00", "w11"=>"11:00:00", "w12"=>"12:00:00", "w13"=>"13:00:00", "w14"=>"14:00:00", "w15"=>"15:00:00", "w16"=>"16:00:00");

	//处理来自review的删除
	if(isset($_GET['did'])) {
		$did=$_GET['did'];

		$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
		$rs_user=mysql_query($sql_user);
		$row_user=mysql_fetch_array($rs_user);

		//找出detail1中的d_did与did值相同的那行记录
		$sql_dt="select * from bk_detail1 where d_id=".$did;
		$rs_dt=mysql_query($sql_dt);
		if($row_dt=mysql_fetch_array($rs_dt)) {

			$day=date("w"); 
			$offset=!empty($day)?$day-1:6;  
			$date_origin=date("Y-m-d", strtotime("-$offset day"));   
			$bookdate=$row_dt['d_date'];  
			$d1=strtotime($date_origin);
			$d2=strtotime($bookdate);
			$days=intval(($d2-$d1)/3600/24/7)*7; 

			if($row_dt['d_sid']==$_COOKIE['sid'] || $row_user['s_right']==1) {  //防止通过modify界面改成delete删别人的
				
				//从上面那行记录中找出对应的weekform1的那行
				$sql="select * from bk_weekform1 where wid=".$row_dt['d_wid'];
				$rs=mysql_query($sql);
				$row=mysql_fetch_array($rs);

				//从上面记录中的第四列开始遍历，前三列是序号、日期、星期几，没必要遍历。
				for($i=3; $i<11; $i++) {
					if(!empty($row[$i])) {
						if(strpos($row[$i], $did)!==false) {  //如果遍历到的这个值中有和要删除的id号吻合的。注意用到的是!==全不等。
							$therow=$row[$i];
							$dp="w".($i+6);
							break;
						}
					}
				}

				$therow=str_replace($did, 0, $therow);  //只要$therow中有$did出现，全都换成0。

				$dp=$row_dt['d_dp'];
				if(date("Y-m-d H:i:s")<$row['wdate']." ".$dparray[$dp]) {
					$sql_upw="update bk_weekform1 set ".$dp."='".$therow."' where wid=".$row_dt['d_wid'];
					mysql_query($sql_upw);

					$sql_del="delete from bk_detail1 where d_id=".$did;
					mysql_query($sql_del);
					
					if($therow=="0,0,0,0") {
						echo "<script>alert('更新已成功。'); document.location.href='index.php?skip=".$days."';</script>"; 
						exit();
					} else {
						echo "<script>alert('更新已成功。'); document.location.href='review.php?wid=".$row_dt['d_wid']."&daypart=".$dp."';</script>";
						exit();
					}
				} else {
					echo "<script>alert('所选时段已过期。'); document.location.href='index.php?skip=".$days."';</script>";
					exit();
				}
			} else {
				echo "<script>alert('您无权进行操作。'); document.location.href='index.php?skip=".$days."';</script>";
				exit();
			}
		} else {
			echo "<script>alert('所选时段无预定。'); history.back();</script>";
			exit();
		}
	}
?>
	