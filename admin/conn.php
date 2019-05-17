<?php
	session_start();
	mysql_connect("localhost", "root", "") or die("mysql连接失败");
	mysql_select_db("muscen") or die("mysql连接失败");
	mysql_query("set names utf8");
	
	//防止php处理页面在游客权限下也能操作
	function logincheckforguest() {
		if (isset($_COOKIE['adminSid']) && isset($_SESSION['adminflag'])) {
			if ($_SESSION['adminflag']=="logged" && !empty($_COOKIE['adminSid'])) {
				$sql_user="select * from bk_staff where s_id=".$_COOKIE['adminSid'];
				$rs_user=mysql_query($sql_user);
				$row_user=mysql_fetch_array($rs_user);
				if($row_user['s_right']==4) {
					header("location:index.php");
					exit();
				} 
			} else {
				header("location:index.php");
				exit();
			}
		} else {
			header("location:index.php");
			exit();
		}
	}

	function adminlogincheck() {
		if (!isset($_SESSION['adminflag']) || !isset($_COOKIE['adminSid'])) {
			header("location:login.php");
			exit();
		} else {
			if ($_SESSION['adminflag']=="logged" && !empty($_COOKIE['adminSid'])) {
				echo "<div id='upperinfo'>";
				echo "<span id='upperlogout'><a href='logout.php'>退出</a></span>";
				echo "<span id='upperlogin'><p>已登录</p><a id='namelink' href='../profile.php?id=".$_COOKIE['adminSid']."'>".$_COOKIE['adminName']."</a></span>";
				echo "</div>";
				echo "<div class='nav2'></div>";
				echo "<div id='lowerinfo'>";
				echo "<span id='lowerswitch'><a href='../index.php'>进入预定页面</a></span>";
				echo "</div>";
			} else {
				header("location:login.php");
				exit();
			}
		}
	}

	//为处理页面，即纯php页面。
	function adminlogincheckonly() {
		if (!isset($_SESSION['adminflag']) || !isset($_COOKIE['adminSid'])) {
			header("location:login.php");
			exit();
		} else {
			if ($_SESSION['adminflag'] != "logged" || empty($_COOKIE['adminSid'])) {
				header("location:login.php");
				exit();
			}
		}
	}

	function htmltocode($content)  { 
		$content=htmlspecialchars($content, ENT_QUOTES);
		return $content;
	}

	function prePrintR($print, $die=FALSE){
		echo '<pre>';
		print_r($print);
		echo '</pre><hr>';
	
		if($die) die();
	}
?>