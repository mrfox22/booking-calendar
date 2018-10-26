<?php
	include("../conn.php"); 
	adminlogincheckonly();

	if(!empty($_GET['id'])) { 
		if (isset($_GET['do'])) {
			if ($_GET['do'] == "dep" && $_GET['id'] != 8) {
				$sqlMember = "SELECT `depmembers` 
					FROM `bk_departments` 
					WHERE `depid` = " .$_GET['id'];
				$queryMember = mysql_query($sqlMember);
				$rowMember = mysql_fetch_array($queryMember);
				$members = explode(",", $rowMember['depmembers']);

				// var_dump($members); exit;

				$sqlSysMember = "SELECT `depmembers` 
					FROM `bk_departments` 
					WHERE `depid` = 8";
				$querySysMember = mysql_query($sqlSysMember);
				$rowSysMember = mysql_fetch_array($querySysMember);
				$sysMembers = explode(",", $rowSysMember['depmembers']);

				// var_dump($sysMembers); exit;

				foreach ($members as $value) {
					array_push($sysMembers, $value); // Assign the member in deleted department into system department member array

					$sqlUpdateMember="UPDATE bk_staff 
						SET `s_dep` = 8, `s_depcode` = 'xt', `s_depname` = '系统' 
						WHERE s_id=" . $value;
					mysql_query($sqlUpdateMember);
				}

				$sysMembersStr = implode(",", $sysMembers);

				// echo $sysMembersStr; exit;

				$sqlUpdateSys = "UPDATE `bk_departments` 
					SET `depmembers` = '$sysMembersStr' 
					WHERE `depid` = 8";
				mysql_query($sqlUpdateSys);

				$sqlDel = "DELETE FROM `bk_departments` 
					WHERE `depid` = " .$_GET['id'];
				mysql_query($sqlDel);
				echo "<script>alert('已删除。'); document.location.href='index.php?do=dep';</script>";
			} else {
				header("location:/admin/index.php&do=dep");
				exit();
			}
		} else {
			mysql_query("delete from bk_staff where s_id=" .$_GET['id']);
			echo "<script>alert('已删除。'); document.location.href='index.php';</script>";
		}
	}
?>
	