<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>选题日历</title>
		<link rel="stylesheet" type="text/css" href="css/frontierCalendar/jquery-frontier-cal-1.3.2.css" /><!---->
		<link rel="stylesheet" type="text/css" href="css/colorpicker/colorpicker.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui/smoothness/jquery-ui-1.8.1.custom.css" />
		<link rel="stylesheet" type="text/css" href="css/ui.css" />

		<script type="text/javascript" src="js/jquery-core/jquery-1.4.2-ie-fix.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui/smoothness/jquery-ui-1.8.1.custom.min.js"></script>
		<script type="text/javascript" src="js/colorpicker/colorpicker.js"></script>
		<script type="text/javascript" src="js/jquery-qtip-1.0.0-rc3140944/jquery.qtip-1.0.js"></script>

		<script type="text/javascript" src="js/lib/jshashtable-2.1.js"></script>
		<script type="text/javascript" src="js/frontierCalendar/jquery-frontier-cal-1.3.2.js"></script>
		<script type="text/javascript" src="scripts/calendar.js"></script>
	</head>

	<body style="background-color: #aaaaaa;">

		<?php
			include("conn.php");
			logincheck();  

			//进入到以下程序部分的，只可能是属于某个选题组的用户或者管理员。
			
			$sid = $_COOKIE['sid'];
			$sql_issues = "select * from bk_staff where s_id=".$sid;
			$rs_issues = mysql_query($sql_issues);
			$row_issues = mysql_fetch_array($rs_issues);

			if($row_issues['s_right'] != 1) {  
				if(preg_match('/\d+,\d+/', $row_issues['s_inissues'])) {
					$userIssueList = explode(",", $row_issues['s_inissues']);
					sort($userIssueList);  //用户所在选题组。为一数组，并按升序排列。
				} else {
					$userIssueList = array();
					$userIssueList[] = $row_issues['s_inissues'];  //用户所在选题组。仍为数组，但只有一个值，且该值为字符串类型。
				}
			} else {
				$userIssueList = array();
				$sqlGpList = "select id from bk_issuegplist";
				$rsGpList = mysql_query($sqlGpList);
				while($rowGpList = mysql_fetch_array($rsGpList)) {
					$userIssueList[] = $rowGpList['id'];
				}
			}
			
			
			//选题组的所有用户。$id为选题组id。
			function issueMember($id) {
				$sql = "select * from bk_issuegplist where id=".$id;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);

				if(preg_match('/\d+,\d+/', $row['member'])) {
					$member = explode(",", $row['member']);
				} else {
					$member = $row['member'];
				}
				
				return $member;
			}

			//选题组的名称。$id为选题组id。
			function issueName($id) {
				$sql = "select listname from bk_issuegplist where id=".$id;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				return $row['listname'];
			}

			//用户的姓名。$sid为用户id。
			function codeToNameSingle($sid) {
				$sql = "select s_name from bk_staff where s_id=".$sid;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				return $row['s_name'];
			}
		?>

		<style type="text/css" media="screen">
			body { font-size: 62.5%; }
			.shadow {
				-moz-box-shadow: 3px 3px 4px #aaaaaa;
				-webkit-box-shadow: 3px 3px 4px #aaaaaa;
				box-shadow: 3px 3px 4px #aaaaaa;
				/* For IE 8 */
				-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#aaaaaa')";
				/* For IE 5.5 - 7 */
				filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#aaaaaa');
			}
		</style>


		
		<h1 id="pageTitle"></h1>
			
		
		<div id="tabs">
			
			<!--
			<ul>
				<?php
					if(!empty($userIssueList)) {  //判断一下用户是否拥有任何选题组，这步应该在longincheck()的时候就可以把无选题组的用户被排除在外。
						$i = 0;
						foreach($userIssueList as $issue) {
							?>
							<li><a id="issueName<?php echo $i + 1;?>" class="issueName" rel="<?php echo $issue;?>" href="#tabs-1"><?php echo issueName($issue);?></a></li>  
							<?php
							$i++;
						}
					}
				?>
			</ul>
			-->

			<div class="ui-widget-header ui-corner-all" style="padding:3px; vertical-align: middle; white-space:nowrap; overflow: hidden;">
				<form>
					<div id="radioset">
						<?php
							
							if(!empty($userIssueList)) {  //判断一下用户是否拥有任何选题组，这步应该在longincheck()的时候就可以把无选题组的用户被排除在外。
								$i = 1;
								foreach($userIssueList as $issue) {
									?>
									<input type="radio" id="issueName<?php echo $i;?>" class="issueName" value="<?php echo $issue;?>" name="radio" <?php echo $i ==1 ? "checked='checked'" : "";?>><label for="issueName<?php echo $i;?>"><?php echo issueName($issue);?></label>

									<!--
											<input type="radio" id="radio1" name="radio"><label for="radio1">Choice 1</label>
											<input type="radio" id="radio2" name="radio" checked="checked"><label for="radio2">Choice 2</label>
											<input type="radio" id="radio3" name="radio"><label for="radio3">Choice 3</label>
										
									<button id="issueName<?php echo $i;?>" class="issueName" value=<?php echo $issue;?>><?php echo issueName($issue);?></button>  --><!--注意这些button的排列是按照选题组id的号从小到大排列的-->
									<?php
									$i++;
								}
							}
							/**/
						?>
					</div>
				</form>
			</div>

			<!--<div id="tabs-1">-->
		
				<div id="example" style="margin: auto; width:80%;">
				
					<br>
					
					<br><br>

					<div id="toolbar" class="ui-widget-header ui-corner-all" style="padding:3px; vertical-align: middle; white-space:nowrap; overflow: hidden;">
						<button id="BtnPreviousMonth">上个月</button>
						<button id="BtnNextMonth">下个月</button>
						&nbsp;&nbsp;&nbsp;
						日期: <input type="text" id="dateSelect" size="20"/>
					</div>

					<br>

					<div id="mycal"></div>

				</div>

				<!-- debugging-->
				<div id="calDebug"></div>

				<!-- Add event modal form -->
				<style type="text/css">
					//label, input.text, select { display:block; }
					fieldset { padding:0; border:0; margin-top:25px; }
					.ui-dialog .ui-state-error { padding: .3em; }
					.validateTips { border: 1px solid transparent; padding: 0.3em; }
				</style>
				<div id="add-event-form" title="添加新选题">
					<p class="validateTips">请完成所有区域内容的填写。</p>
					
					<form>
						<fieldset>
							<label>标题</label>
							<input type="text" name="what" id="what" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;"/>
							<br>
							
							<!-- 时段选项 -->
							<table style="width:100%; padding:5px;" id="tableDate">
								<tr>
									<td>
										<label>选题日期</label>
										<input type="text" name="startDate" id="startDate" value="" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;"/>				
									</td>
									<td class="gone">&nbsp;</td>
									<td class="gone">
										<label>Start Hour</label>
										<select id="startHour" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="12" SELECTED>12</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
										</select>				
									</td>
									<td class="gone">
										<label>Start Minute</label>
										<select id="startMin" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="00" SELECTED>00</option>
											<option value="10">10</option>
											<option value="20">20</option>
											<option value="30">30</option>
											<option value="40">40</option>
											<option value="50">50</option>
										</select>				
									</td>
									<td class="gone">
										<label>Start AM/PM</label>
										<select id="startMeridiem" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="AM" SELECTED>AM</option>
											<option value="PM">PM</option>
										</select>				
									</td>
								</tr>
								<tr class="gone">
									<td>
										<label>End Date</label>
										<input type="text" name="endDate" id="endDate" value="" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;"/>				
									</td>
									<td>&nbsp;</td>
									<td>
										<label>End Hour</label>
										<select id="endHour" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="12" SELECTED>12</option>
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
											<option value="6">6</option>
											<option value="7">7</option>
											<option value="8">8</option>
											<option value="9">9</option>
											<option value="10">10</option>
											<option value="11">11</option>
										</select>				
									</td>
									<td>
										<label>End Minute</label>
										<select id="endMin" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="00" SELECTED>00</option>
											<option value="10">10</option>
											<option value="20">20</option>
											<option value="30">30</option>
											<option value="40">40</option>
											<option value="50">50</option>
										</select>				
									</td>
									<td>
										<label>End AM/PM</label>
										<select id="endMeridiem" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
											<option value="AM" SELECTED>AM</option>
											<option value="PM">PM</option>
										</select>				
									</td>				
								</tr>			
							</table>
							
							<div id="editorSection">
								<label>责任编辑</label><br>
								<select id="editor" name="editor"></select>
							</div>
							<br>
							
							<label>选题摘要</label>
							<textarea name="contents" id="contents" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
							</textarea>

							<!-- 颜色选择 -->
							<table id="tableColor">
								<tr>
									<td>
										<label>Background Color</label>
									</td>
									<td>
										<div id="colorSelectorBackground"><div style="background-color: #333333; width:30px; height:30px; border: 2px solid #000000;"></div></div>
										<input type="hidden" id="colorBackground" value="#333333">
									</td>
									<td>&nbsp;&nbsp;&nbsp;</td>
									<td>
										<label>Text Color</label>
									</td>
									<td>
										<div id="colorSelectorForeground"><div style="background-color: #ffffff; width:30px; height:30px; border: 2px solid #000000;"></div></div>
										<input type="hidden" id="colorForeground" value="#ffffff">
									</td>						
								</tr>				
							</table>

						</fieldset>
					</form>
				</div>
				
				<div id="display-event-form" title="查看选题"></div>	
				

			<!--</div>--><!-- end example tab -->
			<br>
					
		</div>
		

		<br><br>
		<a href="/" class="button--white">预定系统</a>


		<?php
			/*
			if(!empty($userIssueList)) {  //判断一下用户是否拥有任何选题组，这步应该在longincheck()的时候就可以把无选题组的用户被排除在外。
				$i = 1;
				foreach($userIssueList as $issue) {
					?>
					<button id="issueName<?php echo $i;?>" class="issueName" value=<?php echo $issue;?>><?php echo issueName($issue);?></button>  <!--注意这些button的排列是按照选题组id的号从小到大排列的-->
					<?php
					$i++;
				}
			}
			*/
		?>

	</body>
</html>
