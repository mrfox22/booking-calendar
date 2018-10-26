<?php
	include("conn.php");

	/*在bk_departments表中添加部门成员
	$i = 1;
	$sql = "select * from bk_departments";
	$rs = mysql_query($sql);
	while($row = mysql_fetch_array($rs)) {
		$depMembers = "";
		$sql1 = "select s_id from bk_staff where s_dep=".$row['depnum'];
		$rs1 = mysql_query($sql1);
		while($row1 = mysql_fetch_array($rs1)) {
			$depMembers .= $row1['s_id'].",";
		}
		$depMembers = trim($depMembers, ",");
		$sql2 = "update bk_departments set depmembers='$depMembers' where depnum=".$row['depnum'];
		mysql_query($sql2);
		echo $i;
		$i++;
	}
	*/


	$classesOfDepts = array("1"=>"rmt", "2"=>"bgs", "3"=>"zx", "4"=>"ch", "5"=>"yyjmb", "6"=>"ds", "7"=>"yyzx", "8"=>"xt");
	$sql = "select * from bk_staff";
	$rs = mysql_query($sql);
	while($row = mysql_fetch_array($rs)) {
		echo $sql1 = "update bk_staff set s_depcode='".$classesOfDepts[$row['s_dep']]."' where s_id=".$row['s_id'];
		mysql_query($sql1);
		echo $classesOfDepts[$row['s_dep']]."<br>";
	}

?>