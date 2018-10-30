<?php
	include("conn.php"); 
	adminlogincheckonly(); // For pure PHP page

	if(!empty($_GET['id'])) { 
		if (isset($_GET['do'])) {
			if ($_GET['do'] == "dep") {
				$sqlMember = "SELECT `depcode`, `depmembers` 
					FROM `bk_departments` 
					WHERE `depid` = " .$_GET['id'];
				$queryMember = mysql_query($sqlMember);
				$rowMember = mysql_fetch_array($queryMember);
				

				if ($rowMember['depcode'] == 'xt' || $rowMember['depcode'] == 'yyzx') { 
					// Cannot delete Dept. system and Music Center

					header("location:index.php?do=dep");
					exit();
				} else {
					$members = explode(",", $rowMember['depmembers']);

					// var_dump($members); exit;

					$sqlSysMember = "SELECT `depid`, `depmembers` 
						FROM `bk_departments` 
						WHERE `depcode` = 'yyzx'";
					$querySysMember = mysql_query($sqlSysMember);
					$rowSysMember = mysql_fetch_array($querySysMember);
					$sysMembers = explode(",", $rowSysMember['depmembers']);
					$zxId = $rowSysMember['depid'];

					// var_dump($sysMembers); exit;

					foreach ($members as $value) {
						array_push($sysMembers, $value); // Assign the member in deleted department into system department member array

						$sqlUpdateMember="UPDATE bk_staff 
							SET `s_dep` = '$zxId', `s_depcode` = 'yyzx', `s_depname` = '音乐中心' 
							WHERE s_id=" . $value;
						mysql_query($sqlUpdateMember);
					}

					$sysMembersStr = implode(",", $sysMembers);

					// echo $sysMembersStr; exit;

					$sqlUpdateSys = "UPDATE `bk_departments` 
						SET `depmembers` = '$sysMembersStr' 
						WHERE `depid` = ".$zxId;
					mysql_query($sqlUpdateSys);

					$sqlDel = "DELETE FROM `bk_departments` 
						WHERE `depid` = " .$_GET['id'];
					mysql_query($sqlDel);
					echo "<script>alert('已删除。'); document.location.href='index.php?do=dep';</script>";
				}
			} else {
				header("location:index.php?do=dep");
				exit();
			}
		} else {
			$sql = "SELECT * 
				FROM `bk_staff` 
				WHERE `s_id` = " .$_GET['id'] ;
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result); 

			if($row['s_username'] != "guest" && $row['s_username'] != "admin") {
				// Delete from bk_issuegplist
				$sqlUserInIssue = "SELECT `id`, `member`, `admin`, `user` 
					FROM `bk_issuegplist`";

				$queryUserInIssue = mysql_query($sqlUserInIssue);
				while ($rowUserInIssue = mysql_fetch_array($queryUserInIssue)) {
					$memberArr = explode(",", $rowUserInIssue['member']);
					$keyInMember = array_search($_GET['id'], $memberArr);
					if ($keyInMember !== false) {
						unset($memberArr[$keyInMember]);
						$memberStr = implode(",", $memberArr);

						$adminArr = explode(",", $rowUserInIssue['admin']);
						$keyInAdmin = array_search($_GET['id'], $adminArr);
						if ($keyInAdmin !== false) {
							unset($adminArr[$keyInAdmin]);
							$adminStr = implode(",", $adminArr);
							$sqlUpdate = "UPDATE `bk_issuegplist` 
								SET `member` = '$memberStr', `admin` = '$adminStr' 
								WHERE `id` = " .$rowUserInIssue['id'];
							mysql_query($sqlUpdate);
							continue;
						}

						$userArr = explode(",", $rowUserInIssue['user']);
						$keyInUser = array_search($_GET['id'], $userArr);
						unset($userArr[$keyInUser]);
						$userStr = implode(",", $userArr);
						$sqlUpdate = "UPDATE `bk_issuegplist` 
							SET `member` = '$memberStr', `user` = '$userStr' 
							WHERE `id` = " .$rowUserInIssue['id'];
						mysql_query($sqlUpdate);
					}
				}


				// Delete from bk_departments
				$sqlExDep = "SELECT `depmembers` 
					FROM `bk_departments` 
					WHERE `depid` = ". $row['s_dep'];
				$queryExDep = mysql_query($sqlExDep);
				$rowExDep = mysql_fetch_array($queryExDep);

				$memberArr = explode(",", $rowExDep['depmembers']);
				$keyInMember = array_search($_GET['id'], $memberArr);
				if ($keyInMember !== false) {
					unset($memberArr[$keyInMember]);
					$memberStr = implode(",", $memberArr);

					$sqlDeleteMember = "UPDATE `bk_departments` 
					SET `depmembers` = '$memberStr' 
					WHERE `depid` = ". $row['s_dep'];
					mysql_query($sqlDeleteMember);
				}

				mysql_query("delete from bk_staff where s_id=" .$_GET['id']);
				echo "<script>alert('已删除。'); document.location.href='index.php';</script>";
			} else {
				header("location:index.php");
				exit();
			}
		}
	}
?>
	