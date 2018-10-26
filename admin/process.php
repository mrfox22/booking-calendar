<?php
	include("conn.php");
	//adminlogincheckonly();

	
	//在bk_staff的s_inissues项中删掉已脱离的选题组
	function exitIssue($m, $sid) {
		$m = (int)$m;
		$sql1 = "select s_inissues from bk_staff where s_id=".$m;
		$rs1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($rs1);

		$inissues = $row1['s_inissues'];  //成员所在选题组记录的字符串形式
		$inissuesArr = explode(",", $inissues);  //成员所在选题组记录的数组形式

		//以下删除数组中某一元素的方法来源http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key。注意该方法有两个特点：
		//1. array_search()函数经测试对数字和字符串不敏感，即如果是用一个数字在以数字都由字符串形式出现的数组中搜索时，不会因为数组中都是字符串形式而判断其值不相同；
		//2. 本方法的前提是，数组中的元素不会出现相同值的情况。如果非此情况，此方法并不适用。
		$key = array_search($sid, $inissuesArr);
		unset($inissuesArr[$key]);

		$inissues = implode(",", $inissuesArr);
		$sql2 = "update bk_staff set s_inissues='$inissues' where s_id=".$m;
		mysql_query($sql2);
	}


	//在bk_staff的s_inissues项中添加新加入的选题组
	function enterIssue($m, $sid) {
		$m = (int)$m;
		$sql1 = "select s_inissues from bk_staff where s_id=".$m;
		$rs1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($rs1);

		$inissues = !empty($row1['s_inissues']) ? $row1['s_inissues'].",".$sid : $sid;  //成员所在选题组记录的字符串形式
		$sql2 = "update bk_staff set s_inissues='$inissues' where s_id=".$m;
		mysql_query($sql2);
	}


	
	if(!empty($_GET['username'])) {

		$errors=array();
		$data=array();

		$username=$_GET['username'];

		$sql="SELECT * FROM bk_staff WHERE s_username = '".$username."'";
		$result = mysql_query($sql);
		if($row = mysql_fetch_array($result)) $errors['username']="相同的用户名存在，请修改。";

		if ( ! empty($errors)) {
			
			$data['success'] = false;
			$data['errors']  = $errors;

		} else {
			$data['success'] = true;
		}

		echo json_encode($data);
	}


	if(!empty($_POST['act'])) {

		$groupName = $_POST['groupName'];
		
		/*
		$member = "";
		$admin = "";
		$user = "";
		*/
		
		$to = isset($_POST['to']) ? $_POST['to'] : array();
		$to_2 = isset($_POST['to_2']) ? $_POST['to_2'] : array();

		$admin = implode(",", $to);  //空数组合成字符串也是空字符串
		$user = implode(",", $to_2);
		$member = implode(",", array_merge($to, $to_2));
		
		$memberArr = array_merge($to, $to_2);  //合并数组

		/*
		if(!empty($_POST['to'])) {
			$to = $_POST['to'];
			foreach($to as $t) {
				$member .= $t.",";
				$admin .= $t.",";
			}
			$admin = trim($admin, ",");
		}

		if(!empty($_POST['to_2'])) {
			$to_2 = $_POST['to_2'];
			foreach($to_2 as $t_2) {
				$member .= $t_2.",";
				$user .= $t_2.",";
			}
			$user = trim($user, ",");
		}

		$member = trim($member, ",");
		*/

		if($_POST['act'] == "create") {
			//echo "create";
			$groupCode = $_POST['groupCode'];

			$sqlCode = "select * from bk_issuegplist where listcode='".$groupCode."'";
			$rsCode = mysql_query($sqlCode);
			if(!$rowCode = mysql_fetch_array($rsCode)) {  //注意，=比其它大多数的运算符的优先级低，但在这种表达式中是例外，这里右边的值赋给了$rowCode。说明参看php手册。
				//$foreach($memberArr as $m)
				
				$sql = "insert into bk_issuegplist (listname, listcode, member, admin, user) values ('$groupName', '$groupCode', '$member', '$admin', '$user')";
				mysql_query($sql);

				$sql_tb = "create table bk_issues_".$groupCode." (e_id int(10) not null primary key auto_increment, e_title varchar(50), e_editor int(5), e_edright int(5), e_contents varchar(200), e_yyyymm int(10), e_dd int(10), e_atime bigint(15), e_etime int(10))";
				mysql_query($sql_tb);

				echo "<script>alert('create'); document.location.href='issuegroup.php'</script>";
				exit();
			} else {
			
			}
		}

		if($_POST['act'] == "edit") {
			//echo "edit";
			$id = (int)$_POST['id'];

			$sql = "select * from bk_issuegplist where id=".$id;
			$rs = mysql_query($sql);
			if($row = mysql_fetch_array($rs)) {

				$originalMember = $row['member'];  //未更改前的成员
				$originalMemberArr = explode(",", $originalMember);  

				$decrementArr = array_diff($originalMemberArr, $memberArr);  //选题组用户中被剔除的用户
				foreach($decrementArr as $d) {
					exitIssue($d, $id);
				}

				$incrementArr = array_diff($memberArr, $originalMemberArr);  //选题组用户中新加的用户
				foreach($incrementArr as $i) {
					enterIssue($i, $id);
				}

				$sql = "update bk_issuegplist set listname='$groupName', member='$member', admin='$admin', user='$user' where id=".$id;
				mysql_query($sql);

				echo "<script>alert('更新已提交。'); document.location.href='issuegroup.php';</script>";
				exit();
			} else {
				echo "<script>alert('选题组不存在。'); document.location.href='issuegroup.php';</script>";
				exit();
			}
		}
	}


	//挂起选题组
	if(!empty($_GET['sid'])) {
		$sid = (int)$_GET['sid'];  //选题组ID

		$sql = "select * from bk_issuegplist where id=".$sid;
		$rs = mysql_query($sql);
		if($row = mysql_fetch_array($rs)) {
			
			if(!empty($row['member'])) {
				$member = $row['member'];
				$memberArr = explode(",", $member);  //选题组成员的数组形式。注意，即便$member只有一个值而不是用逗号区隔的多个值，该函数也可以把只有一个值的字符串变成数组。

				foreach($memberArr as $m) {
					exitIssue($m, $sid);
				}

				$sql3 = "update bk_issuegplist set member='', admin='', user='' where id=".$sid;
				mysql_query($sql3);

				echo  "<script>alert('挂起选题组成功。'); document.location.href='issuegroup.php';</script>";
				exit();

			} else {  //member已经被清空
				echo  "<script>alert('选题组已被挂起。'); document.location.href='issuegroup.php';</script>";
				exit();
			}
		} else {  //当选题组已经被删除时
			echo "<script>alert('选题组不存在。'); document.location.href='issuegroup.php';</script>";
			exit();
		}
	}


	/*
	if(!empty($_POST['rightAllocate']) && !empty($_POST['tableCode'])) {
		$id = $_POST['rightAllocate'];
		$tableCode = $_POST['tableCode'];
		$to = $_POST['to'];
		$to_2 = $_POST['to_2'];

		$admin = "";
		foreach($to as $t) $admin .= $t.",";
		$admin = trim($admin, ",");

		$user = "";
		foreach($to_2 as $t_2) $user .= $t_2.",";
		$user = trim($user, ",");

		$sql = "update bk_issuegplist set admin='$admin', user='$user' where id=".$id;
		mysql_query($sql);

		//第一次时进行创建。若已有表格，则不进行该操作。
		$sql_tb = "create table bk_events_".$tableCode." (e_id int(10) not null primary key auto_increment, e_title varchar(50), e_editor int(5), e_edright int(5), e_contents varchar(200), e_yyyymm int(10), e_dd int(10), e_atime bigint(15), e_etime int(10))";
		mysql_query($sql_tb);

		echo "<script>alert('ok'); document.location.href='issuegroup.php'</script>";
	}
	*/

?>