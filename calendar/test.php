<?php
	session_start();
	mysql_connect("localhost", "root", "") or die("mysql连接失败");
	mysql_select_db("bk") or die("mysql连接失败");
	mysql_query("set names utf8");
	
	$startDates = array(2016, 2, 6);
	
	//$startDates = explode("-", $startDate);
	$edate = "";
	foreach($startDates as $value) {
		if($value < 10) {
			$edate .= "0".$value;
		} else {
			$edate .= $value;
		}
	}
	
	echo $edate."<br>";
	
	//print_r($startDates);
	
	//echo $startDate = implode("", $startDates);

	echo time();

	echo "<br>";

	if(time() > null) echo "null can be treated as 0<br>";

	echo $sql = "select e_etime from bk_events where e_id=12";
	$rs = mysql_query($sql);
	$row = mysql_fetch_array($rs);

	echo "<br>";
	echo $row['e_etime'];

	echo "<br>";

	echo $d=strtotime("tomorrow");

	echo "<br>";

	var_dump(time());
	echo "<br>";
	var_dump($d);

	echo "<br>";

	$dd = mktime(9, 12, 31, 6, 10, 2015);
	echo $dd;
	echo "<br>";
	echo date("Y-m-d", $dd);
	echo "<br>";

	$a = 5;
	$b = 6;
	echo $a.$b;
	echo "<br>";
	var_dump($a.$b);
	echo "<br>";

	if(0 == 0) {
		echo "0 equal to 0 is true<br>";
	} else {
		echo "0 equal to 0 is not true<br>";
	}

	$phpTimeArr = explode("-", "123-45-6");
	print_r($phpTimeArr);
	echo "<br>";

	$thisYear = (int)date('Y');
	echo $thisYear."<br>";
	var_dump($thisYear);
	echo "<br>";
	echo $thisYear - 1;
	echo "<br>";
	var_dump($thisYear - 1);
	echo "<br>";

	$arr = array(2009, 3, 32);
	for($i = 0; $i < count($arr); $i++) {
		/*
		switch($i) {
			case 0:
				if($arr[$i] < 2006 || $arr[$i] > 2026) {
					echo "year invalid<br>";
					break;
				} else {
					echo "year valid<br>";
				}
				break;

			case 1:
				if($arr[$i] < 1 || $arr[$i] > 12) {
					echo "month invalid<br>";
					break;
				} else {
					echo "month valid<br>";
				}
				break;

			case 2:
				if($arr[$i] < 1 || $arr[$i] > 31) {
					echo "date invalid<br>";
					break;
				} else {
					echo "date valid<br>";
				}
				break;

			default:
				echo "It's impossible<br>";
		}
		*/

		if($i === 0) {
			if($arr[$i] < 2006 || $arr[$i] > 2026) {
				echo "year invalid<br>";
				break;
			} else {
				echo "year valid<br>";
			}
		} else if($i === 1) {
			if($arr[$i] < 1 || $arr[$i] > 12) {
				echo "month invalid<br>";
				break;
			} else {
				echo "month valid<br>";
			}
		} else {
			if($arr[$i] < 1 || $arr[$i] > 31) {
				echo "date invalid<br>";
				break;
			} else {
				echo "date valid<br>";
			}
		}
	}

	$num = cal_days_in_month(CAL_GREGORIAN, 2, 2016);
	echo $num."<br>";
	var_dump($num);
	echo "<br>";

	$hi = NULL;
	if(isset($hi)) {
		echo "null is issetted<br>";
	} else {
		echo "null isn't issetted<br>";
	}

	$j = "";
	if($j == 0) {
		echo "php中空字符相等于0（==）<br>";
	} else {
		echo "php中空字符不相等于0（==）<br>";
	}

	$q = NULL;
	if($q == 0) {
		echo "php中NULL相等于0（==）<br>";
	} else {
		echo "php中NULL不相等于0（==）<br>";
	}

	$j = "";
	if($j == 1) {
		echo "php中空字符相等于1（==）<br>";
	} else {
		echo "php中空字符不相等于1（==）<br>";
	}

	$j = "j";
	if($j == 1) {
		echo "php中字符串j相等于1（==）<br>";
	} else {
		echo "php中字符串j不相等于1（==）<br>";
	}

	echo (int)NULL;
	echo "<br>";

	$nul = (int)NULL;
	if(isset($nul)) {
		echo "(int)null is issetted<br>";
	} else {
		echo "(int)null isn't issetted<br>";
	}

	if(empty($nul)) {
		echo "(int)null is empty<br>";
	} else {
		echo "(int)null isn't empty<br>";
	}

	echo date("Y-m-d h:i:sa", time());
	echo "<br>";
	echo $tday = date("Y-m-d", time());
	echo "<br>";
	echo str_replace("-", "", $tday);
	echo "<br>";

	echo "<hr>";
	echo "以下部分为160726后的内容<br>";

	$arr_26 = "2,11,5,6,7";
	if(!empty($arr_26)) {
		$member = explode(",", $arr_26);
		sort($member);
		print_r($member);
		echo "<br>";
		foreach($member as $key=>$m) {
			echo $key."~";
			var_dump($m);
			echo "<br>";
		}
	} else {
		echo "数组为空<br>";
	}

	if ($_SESSION['flag']=="logged" && !empty($_COOKIE['sid'])) {
		echo $_COOKIE['sid']."<br>";
	}


	$sql_user="select * from bk_staff where s_id=".$_COOKIE['sid'];
	$rs_user=mysql_query($sql_user);
	$row_user=mysql_fetch_array($rs_user);

	echo $row_user['s_right']."<br>";
	echo $row_user['s_inissues']."<br>";
	$arr = explode(",", $row_user['s_inissues']);
	//echo $arr;

	$arr = array(8);
	foreach($arr as $a) echo $a."<br>";
?>