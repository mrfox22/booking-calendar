<?php
	/*总逻辑原则，先后端后前端*/

	include("conn.php");
	//logincheckforprocess();  即便是以ajax的形式访问的这页，也是要从头读到尾的。此项负责检查访问用户是否是本域授权及域中权限合格的，防止外部网页直接访问以及权限不足用户。所有用户权限验证都在后面完成，因为要返回错误信息，这里的验证无法返回信息，而且程序会直接退出。

	//转化前端传过来的日期格式为xxxx-xx-xx
	function validateDate($jsTime) {
		$currentYear = (int)date("Y");
		$firstYear = 2006;
		$lastYear = 2026;

		$validation = array();
		$validation['result'] = true;
		$validation['phpTime'] = "";
		
		if(preg_match('/\d{4}-\d{2}-\d{2}/', $jsTime)) {
		
			$phpTimeArr = explode("-", $jsTime);

			
			for($i = 0; $i < count($phpTimeArr); $i++) {
				
				if($i === 0) {
					if((int)$phpTimeArr[$i] > 2026 || (int)$phpTimeArr[$i] < 2006) {
						$validation['error'] = "年份值输入错误";
						$validation['result'] = false;
						break;  //一旦发生该条件，立刻退出整个循环。
					} 
				} else if($i == 1) {
					if((int)$phpTimeArr[$i] > 12 || (int)$phpTimeArr[$i] < 1) {
						$validation['error'] = "月份值输入错误";
						$validation['result'] = false;
						break;
					} 
				} else {
					$year = (int)$phpTimeArr[0];
					$month = (int)$phpTimeArr[1];
					$numOfMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
					if((int)$phpTimeArr[$i] > $numOfMonth || (int)$phpTimeArr[$i] < 1) {
						$validation['error'] = "日期值输入错误";
						$validation['result'] = false;
					} 
				}
				
			}
		} else {
			$validation['error'] = "日期格式必须为xxxx-xx-xx，其中x代表数字，位数不足时请在前面补0。";
			$validation['result'] = false;
		}

		if($validation['result']) {

			foreach($phpTimeArr as $value) {
				$validation['phpTime'] .= $value;
			}
		}


		return $validation;
	}

	//取得editor的姓名
	function getEditorName($sid) {
		if($sid == 2) {
			$editorName = "待定";
		} else {
			$sql = "select * from bk_staff where s_id=".$sid;
			$rs = mysql_query($sql);
			$row = mysql_fetch_array($rs);
			$editorName = $row['s_name'];
		}
		return $editorName;
	}


	function getMemberArray($id) {
		$sql = "select member from bk_issuegplist where id=".$id;
		$rs = mysql_query($sql);
		$row = mysql_fetch_array($rs);
		$memberArr = !empty($row['member']) ? explode(",", $row['member']) : array();  //注意，$row['member']如果无值，即空字节，!empty($row['member'])结果为否，而explode(",", $row['member'])的结果却为有一个值且值为空字节的数组，因此数组并不为空；而通过array()新定义的数组，数组为空。
		return $memberArr;
	}

	//取得用户权限
	function getRight($sid) {
		$sid = (int)$sid;
		$sql = "select * from bk_staff where s_id=".$sid;
		$rs = mysql_query($sql);
		$row = mysql_fetch_array($rs);
		return (int)$row['s_right'];
	}

	function rightValidate($id, $sid, $originalSid, $editor=0) {
		//id为选题组代号，sid为提交者的cookie，editor为选题的编辑。

		$sid = (int)$sid;
		$originalSid = (int)$originalSid;
		$editor = (int)$editor;
		$isMember = memberOrNot($id, $sid);
		$isAdmin = adminOrNot($id, $sid);
		
		$rightValidate = array();
		$loggerRight = getRight($sid);
		$rightValidate['action'] = true;

		if(!empty($sid)) {
			if($sid == $originalSid) {
				if($isMember || $loggerRight === 1) {
					switch($loggerRight) {
						case 1:
							$rightValidate['e_editor'] = $editor;
							$rightValidate['e_edright'] = getRight($editor);
							break;

						case 2:
							if(!$isAdmin) {
								$rightValidate['e_editor'] = $sid;
								$rightValidate['e_edright'] = getRight($sid);
							} else {
								$rightValidate['e_editor'] = $editor;
								$rightValidate['e_edright'] = getRight($editor);
							}
							break;

						default:
							$rightValidate['action'] = false;
							$rightValidate['msg'] = "您的权限不足。";
					}
				} else {
					$rightValidate['action'] = false;
					$rightValidate['msg'] = "您不属于该选题组。";
				}

			} else {
				$rightValidate['action'] = false;
				$rightValidate['msg'] = "用户已退出";
			}
		} else {
			$rightValidate['action'] = false;
			$rightValidate['msg'] = "请登录。";
		}

		return $rightValidate;
	}
	
	//获得表名
	function getTableName($d) {
		$id = (int)$d;
		$sql_tn = "select listcode from bk_issuegplist where id=".$id;
		$rs_tn = mysql_query($sql_tn);
		$row_tn = mysql_fetch_array($rs_tn);
		return $tableName = "bk_issues_".$row_tn['listcode'];
	}

	function memberOrNot($id, $s_id) {
		$id = (int)$id;
		$s_id = (int)$s_id;
		$sql_tb = "select member from bk_issuegplist where id=".$id;
		$rs_tb = mysql_query($sql_tb);
		$row_tb = mysql_fetch_array($rs_tb);
		$member = explode(",", $row_tb['member']);  //如果是空值，分出来的就是一个空数组。
		return $m = in_array($s_id, $member) ? true : false;
	}

	function adminOrNot($id, $s_id) {
		$id = (int)$id;
		$s_id = (int)$s_id;
		$sql_tb = "select admin from bk_issuegplist where id=".$id;
		$rs_tb = mysql_query($sql_tb);
		$row_tb = mysql_fetch_array($rs_tb);
		$admin = explode(",", $row_tb['admin']);
		return $a = in_array($s_id, $admin) ? true : false;
	}
	
	if(isset($_POST['action'])) {
		
		$action = $_POST['action'];

		switch($action) {

			//添加事件
			case "add":
				$tableName = getTableName($_POST['id']);
				$addEvent = array();

				$e_title = htmltocode($_POST['e_title']);
				$e_contents = htmltocode($_POST['e_contents']);
				$e_atime = $_POST['e_atime'];
				$eventDate = $_POST['eventDate'];
				$startDateNoHyphen = $_POST['startDateNoHyphen'];
				$fullToday = str_replace("-", "", date("Y-m-d", time()));
				
				$rightValidate = rightValidate($_POST['id'], $_POST['cookieSid'], $_POST['originalSid'], $_POST['e_editor']);
				
				if($rightValidate['action']) {
					
					$validateDate = validateDate($eventDate);

					if($validateDate['result']) {
						$e_editor = $rightValidate['e_editor'];
						$e_edright = $rightValidate['e_edright'];
					
						$e_yyyymm = (int)substr($validateDate['phpTime'], 0, 6);
						$e_dd = (int)substr($validateDate['phpTime'], 6);
						$isAdmin = adminOrNot($_POST['id'], $_POST['cookieSid']);

						if(getRight($_POST['cookieSid'])==1 || $isAdmin) {  //此条件会忽略历史日期而继续往里添加选题
							$sql = "insert into ".$tableName." (e_title, e_contents, e_yyyymm, e_dd, e_atime, e_etime, e_editor, e_edright) values ('$e_title', '$e_contents', '$e_yyyymm', '$e_dd', '$e_atime', '$e_atime', '$e_editor', $e_edright)";  
							mysql_query($sql);

							$addEvent['action'] = true;
							$addEvent['msg'] = "添加成功。";
						} else {
							if((int)$startDateNoHyphen >= (int)$fullToday) {
								$sql = "insert into ".$tableName." (e_title, e_contents, e_yyyymm, e_dd, e_atime, e_etime, e_editor, e_edright) values ('$e_title', '$e_contents', '$e_yyyymm', '$e_dd', '$e_atime', '$e_atime', '$e_editor', $e_edright)";  //变量是数值可以不用加引号
								mysql_query($sql);

								$addEvent['action'] = true;
								$addEvent['msg'] = "添加成功。";
							} else {
								$addEvent['action'] = false;
								$addEvent['msg'] = "选题日期必须等于或晚于今天。";
							}
						}
					} else {
						$addEvent['action'] = false;
						$addEvent['msg'] = $validateDate['error'];
					}
				} else {
					$addEvent['action'] = $rightValidate['action'];
					$addEvent['msg'] = $rightValidate['msg'];
				}

				echo json_encode($addEvent);
				break;
			
			//读取事件
			case "load":
				$tableName = getTableName($_POST['id']);

				$selectedDate = str_replace("-", "", $_POST['selectedDate']);
				$selectedYear = (int)substr($selectedDate, 0, 4);
				$selectedMonth = (int)substr($selectedDate, 4, 2);

				//上一月
				if($selectedMonth === 1) {
					$yearOfPreMonth = $selectedYear - 1;
					$monthOfPreMonth = 12;
				} else {
					$yearOfPreMonth = $selectedYear;
					$monthOfPreMonth = ($selectedMonth - 1) < 10 ? "0".($selectedMonth - 1) : ($selectedMonth - 1);
				}
				$preYyyymm = (int)($yearOfPreMonth.$monthOfPreMonth);

				//下一月
				if($selectedMonth === 12) {
					$yearOfNextMonth = $selectedYear + 1;
					$monthOfNextMonth = "01";
				} else {
					$yearOfNextMonth = $selectedYear;
					$monthOfNextMonth = ($selectedMonth + 1) < 10 ? "0".($selectedMonth + 1) : ($selectedMonth + 1);
				}
				$nextYyyymm = (int)($yearOfNextMonth.$monthOfNextMonth);


				//本月
				$selectedYyyymm = (int)substr($selectedDate, 0, 6);

				$events = array();
				//$sql = "select * from ".$tableName." where e_yyyymm=".$selectedYyyymm." or e_yyyymm=".$preYyyymm." or e_yyyymm=".$nextYyyymm;
				$sql = "select * from ".$tableName." where e_yyyymm=".$selectedYyyymm." or e_yyyymm=".$preYyyymm." or e_yyyymm=".$nextYyyymm;
				$rs = mysql_query($sql);
				$i = 1;
				while($row = mysql_fetch_array($rs)) {
					$events['e_id'][$i] = $row['e_id'];
					$events['e_title'][$i] = $row['e_title'];
					$events['e_contents'][$i] = $row['e_contents'];
					$events['e_yyyymm'][$i] = $row['e_yyyymm'];
					$events['yyyy'][$i] = (int)substr($row['e_yyyymm'], 0, 4);  //不是数据库字段不用e_打头
					$events['mm'][$i] = (int)substr($row['e_yyyymm'], 4);  //同上
					$events['e_dd'][$i] = $row['e_dd'];
					$events['e_atime'][$i] = $row['e_atime']*1000;  //js时间有毫秒，所以多三位。
					$events['e_etime'][$i] = $row['e_etime']*1000;
					$events['etimeInPhp'][$i] = $row['e_etime'];

					$events['e_editor'][$i] = $row['e_editor'];
					$events['editor'][$i] = getEditorName($row['e_editor']);
					$events['e_edright'][$i] = $row['e_edright'];

					$i++;
				}
				$events['counts'] = $i - 1;
				$events['debug'] = "Write down something you wanna check out here.";

				echo json_encode($events);
				break;
			
			//删除事件
			case "delete":
				$tableName = getTableName($_POST['id']);
				$delEvent = array();
			
				$delId = $_POST['delId'];
				$etimeInPhp = (int)$_POST['etimeInPhp'];  //之前的编辑时间
				$clickDateNoHyphen = $_POST['clickDateNoHyphen'];
				$fullToday = str_replace("-", "", date("Y-m-d", time()));

				$rightValidate = rightValidate($_POST['id'], $_POST['cookieSid'], $_POST['originalSid']);

				if($rightValidate['action']) {

					$sql = "select * from ".$tableName." where e_id=".$delId;
					$rs = mysql_query($sql);
					if($row = mysql_fetch_array($rs)) {
						if($row['e_etime'] != $etimeInPhp) {
							$delEvent['msg'] = "删除失败。在您编辑期间，事件内容已被修改，请返回查看。";
							$delEvent['action'] = false;
						} else {
							$isAdmin = adminOrNot($_POST['id'], $_POST['cookieSid']);
							if(getRight($_POST['cookieSid'])==1  || $isAdmin) {  //同样为管理员的特权，任意删除过期选题。
								$sql_del = "delete from ".$tableName." where e_id=".$delId;
								mysql_query($sql_del); 
								
								$delEvent['msg'] = "删除成功";
								$delEvent['action'] = true;
							} else {
								if((int)$clickDateNoHyphen >= (int)$fullToday) {
									$sql_del = "delete from ".$tableName." where e_id=".$delId;
									mysql_query($sql_del); 
									
									$delEvent['msg'] = "删除成功";
									$delEvent['action'] = true;
								} else {
									$delEvent['action'] = false;
									$delEvent['msg'] = "删除失败。事件已过期。";
								}
							}
						}
					} else {
						$delEvent['msg'] = "删除失败。事件不存在。";
						$delEvent['action'] = false;
					}
				} else {
					$delEvent['action'] = $rightValidate['action'];
					$delEvent['msg'] = $rightValidate['msg'];
				}

				echo json_encode($delEvent);
				break;
			
			//更新事件
			case "update":
				$tableName = getTableName($_POST['id']);
				$update = array();

				$e_id = (int)$_POST['e_id'];
				$e_title = htmltocode($_POST['e_title']);
				$e_contents = htmltocode($_POST['e_contents']);
				$eventDate = $_POST['eventDate'];
				$e_etime = (int)$_POST['e_etime'];  //新的编辑时间
				$etimeInPhp = (int)$_POST['etimeInPhp'];  //之前的编辑时间
				$clickDateNoHyphen = $_POST['clickDateNoHyphen'];
				$startDateNoHyphen = $_POST['startDateNoHyphen'];
				$fullToday = str_replace("-", "", date("Y-m-d", time()));

				$rightValidate = rightValidate($_POST['id'], $_POST['cookieSid'], $_POST['originalSid'], $_POST['e_editor']);

				if($rightValidate['action']) {
					
					$validateDate = validateDate($eventDate);

					if($validateDate['result']) {
						$e_editor = $rightValidate['e_editor'];
						$e_edright = $rightValidate['e_edright'];
					
						$e_yyyymm = (int)substr($validateDate['phpTime'], 0, 6);
						$e_dd = (int)substr($validateDate['phpTime'], 6);

						$sql = "select * from ".$tableName." where e_id=".$e_id;
						$rs = mysql_query($sql);
						if($row = mysql_fetch_array($rs)) {
							
							$etimeInDb = (int)$row['e_etime'];
							
							if($etimeInPhp == $etimeInDb) {
								$isAdmin = adminOrNot($_POST['id'], $_POST['cookieSid']);
								
								if(getRight($_POST['cookieSid'])==1 || $isAdmin) {  //此条件为管理员的特权，可忽视选题本身是否过期及选题时间是否过期的问题。
									$sql_update = "update ".$tableName." set e_title='".$e_title."', e_contents='".$e_contents."', e_yyyymm=".$e_yyyymm.", e_dd=".$e_dd.", e_etime=".$e_etime.", e_editor=".$e_editor.", e_edright=".$e_edright." where e_id=".$e_id;
									mysql_query($sql_update);

									$update['action'] = true;
									$update['msg'] = "修改完成。";
								} else {
									if((int)$clickDateNoHyphen >= (int)$fullToday) {
										if((int)$startDateNoHyphen >= (int)$fullToday) {
											$sql_update = "update ".$tableName." set e_title='".$e_title."', e_contents='".$e_contents."', e_yyyymm=".$e_yyyymm.", e_dd=".$e_dd.", e_etime=".$e_etime.", e_editor=".$e_editor.", e_edright=".$e_edright." where e_id=".$e_id;
											mysql_query($sql_update);

											$update['action'] = true;
											$update['msg'] = "修改完成。";
										} else {
											$update['action'] = false;
											$update['msg'] = "选题日期必须等于或晚于今天。";
										}
									} else {
										$update['action'] = false;
										$update['msg'] = "选题已过期，无法修改。";
									}
								}

							} else {  //不等于说明事件已经被修改过
								$update['action'] = false;
								$update['msg'] = "修改失败。在您编辑期间，事件内容已被修改，请返回查看。";
							
							}
							
						} else {
							$update['action'] = false;
							$update['msg'] = "修改失败。事件不存在。";

						}
					} else {
						$update['action'] = false;
						$update['msg'] = $validateDate['error'];
					}
				} else {
					$update['action'] = $rightValidate['action'];
					$update['msg'] = $rightValidate['msg'];
				}

				echo json_encode($update);
				break;

			//拖动。原则是只改大时间，即天，不改其他内容。
			case "drag":
				$tableName = getTableName($_POST['id']);
				$drag = array();

				$e_id = (int)$_POST['e_id'];
				$eventDate = $_POST['eventDate'];
				$e_etime = (int)$_POST['e_etime'];  //新的编辑时间
				$etimeInPhp = (int)$_POST['etimeInPhp'];  //之前的编辑时间

				$validateDate = validateDate($eventDate);
				
				$e_yyyymm = (int)substr($validateDate['phpTime'], 0, 6);
				$e_dd = (int)substr($validateDate['phpTime'], 6);

				$sql = "select * from ".$tableName." where e_id=".$e_id;
				$rs = mysql_query($sql);
				if($row = mysql_fetch_array($rs)) {
					
					$etimeInDb = (int)$row['e_etime'];
					
					if($etimeInPhp == $etimeInDb) {
						
						$sql_drag = "update ".$tableName." set e_yyyymm=".$e_yyyymm.", e_dd=".$e_dd.", e_etime=".$e_etime." where e_id=".$e_id;
						mysql_query($sql_drag);

						$drag['action'] = true;
						$drag['msg'] = "修改完成。";

					} else {  //不等于说明事件已经被修改过
						$drag['action'] = false;
						$drag['msg'] = "修改失败。在您编辑期间，事件内容已被修改，请返回查看。";
					
					}
					
				} else {
					$drag['action'] = false;
					$drag['msg'] = "修改失败。事件不存在。";

				}

				echo json_encode($drag);
				break;
	
			//获得用户权限信息
			case "getright":
				$getright = array();

				if($_POST['s_id']) {  //用户登录未超时或未退出
					$s_id = (int)$_POST['s_id'];
					$sql = "select * from bk_staff where s_id=".$s_id;
					$result = mysql_query($sql);
					if($row = mysql_fetch_array($result)) {
						$getright['rt'] = $row['s_right'];
						$getright['member'] = memberOrNot($_POST['id'], $_POST['s_id']);
						$getright['admin'] = adminOrNot($_POST['id'], $_POST['s_id']);
					} else {  //用户已经被从数据库中删除
						$getright['rt'] = 0;
					}
				} else {  
					$getright['rt'] = 0;
				}

				echo json_encode($getright);
				break;
	

			//获得选题组页面名称
			case "pageTitle":
				$id_pagetitle = (int)$_POST['id'];
				$sql_pagetitle = "select listname from bk_issuegplist where id=".$id_pagetitle;
				$rs_pagetitle = mysql_query($sql_pagetitle);
				$row_pagetitle= mysql_fetch_array($rs_pagetitle);
				echo $row_pagetitle['listname'];
				break;

			
			//
			case "getusers":
				$id = (int)$_POST['id'];
				$memberArray = getMemberArray($id);
				$optionString = "";
				if(!empty($memberArray)) {
					foreach($memberArray as $member) {
						$optionString .= "<option value='".$member."'>".getEditorName($member)."</option>";
					}
				}
				echo $optionString;
				break;

			
			default:
				return false;
		}

	}
?>