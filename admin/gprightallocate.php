<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Group Right Allocate</title>

		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="lib/google-code-prettify/prettify.css" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/customized.css" />

		<meta name="viewport" content="width=800">
		<meta name="MobileOptimized" content="800" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="../styles/ui.css" media="all" />
	</head>

	<body>
		<?php 
			echo "<header>";
			echo "<h1>音乐中心后台管理</h1>";
			echo "<div id='loginfo'>";
			include("conn.php");
			adminlogincheck();
			echo "</div>";
			echo "</header>";
		
			function codeToName($code) {
				$sql = "select s_name from bk_staff where s_id=".$code;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				return $row['s_name'];
			}


			function codeToCode($code) {
				$sql = "select s_depcode from bk_staff where s_id=".$code;
				$rs = mysql_query($sql);
				$row = mysql_fetch_array($rs);
				return $row['s_depcode'];
			}


			/*
			if(isset($_POST['rightAllocate'])) {
				print_r($_POST['to']);
				print_r($_POST['to_2']);
			}
			*/

			
			if(!empty($_GET['id'])) {
				$id = $_GET['id'];  //这是初始的id，不存在被人篡改的情况。
				$sql = "select * from bk_issuegplist where id=".$id;
				$rs = mysql_query($sql);
				if($row = mysql_fetch_array($rs)) {
					?>
					<div class="nav1"></div>

					<div id="main">
						<div id="demo" class="container">

							<!--Multiple destinations-->
							<h4 id="demo-multiple-destinations">Multiple destinations</h4>
							<div class="row">
								<div class="col-xs-5">
									<!--<select>-->
									<select name="from[]" id="multi_d" class="form-control" size="26" multiple="multiple">
										<!--<option value="200">NA</option>对方法sort的测试-->
										<!--<optgroup label="test">不能使分组方式，因为加上分组后，排序就不起作用了-->
										<?php
											$classesOfDepts = array();
											$sqlDeptIdCode = "SELECT `depid`, `depcode` 
												FROM bk_departments";
											$queryDeptIdCode = mysql_query($sqlDeptIdCode);
											while ($rowDeptIdCode = mysql_fetch_array($queryDeptIdCode)) {
												$classesOfDepts[$rowDeptIdCode['depid']] = $rowDeptIdCode['depcode'];
											}

											//$sql_all = "select * from bk_staff order by convert(s_name using gbk)";  //php方法按照中文名称排序 
											$sql_all = "SELECT * from `bk_staff` 
											WHERE `s_username` <> 'guest' AND `s_username` <> 'admin'";
											$rs_all = mysql_query($sql_all);
											while($row_all = mysql_fetch_array($rs_all)) {
												echo "<option value='".$row_all['s_id']."' class='".$classesOfDepts[$row_all['s_dep']]."'>".$row_all['s_name']."</option>";
											}
										?>
										<!--<option value="300">NA</option>对方法sort的测试-->
										<!--</optgroup>-->
									</select>
								</div>
								
								<!-- 中间的两排按钮 -->
								<div class="col-xs-2">
									<button type="button" id="multi_d_rightAll" class="btn btn-default btn-block" style="margin-top: 20px;"><i class="glyphicon glyphicon-forward"></i></button>
									<button type="button" id="multi_d_rightSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
									<button type="button" id="multi_d_leftSelected" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
									<button type="button" id="multi_d_leftAll" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
									
									<hr style="margin: 40px 0 60px;" />
									
									<button type="button" id="multi_d_rightAll_2" class="btn btn-default btn-block"><i class="glyphicon glyphicon-forward"></i></button>
									<button type="button" id="multi_d_rightSelected_2" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
									<button type="button" id="multi_d_leftSelected_2" class="btn btn-default btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
									<button type="button" id="multi_d_leftAll_2" class="btn btn-default btn-block"><i class="glyphicon glyphicon-backward"></i></button>
								</div>
								
								<div class="col-xs-5">
									<form method="post" action="process.php">
										
										选题表名称：<input type="text" name="groupName" value="<?php echo $row['listname'];?>" id="groupName">　<span id="groupNameError"></span><br>
										选题表代码：<?php echo $row['listcode'];?><br>

										<b>管理员</b>
										<select name="to[]" id="multi_d_to" class="form-control" size="8" multiple="multiple">
											<?php 
												if(!empty($row['admin'])) {
													$adminArr = explode(",", $row['admin']);
													foreach($adminArr as $a) echo "<option value='".$a."' class='".codeToCode($a)."'>".codeToName($a)."</option>";
												}
											?>
										</select>
										
										<br/><hr/><br/>
										
										<b>用户</b>
										<select name="to_2[]" id="multi_d_to_2" class="form-control" size="8" multiple="multiple">
											<?php 
												if(!empty($row['user'])) {
													$userArr = explode(",", $row['user']);
													foreach($userArr as $u) echo "<option value='".$u."' class='".codeToCode($u)."'>".codeToName($u)."</option>";
												}
											?>
										</select>
										<input type="hidden" name="act" value="edit">
										<input type="hidden" name="id" value="<?php echo $id;?>"><br><br>
										<input type="submit">　<a href="issuegroup.php">返回首页</a>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					<?php
				} else {
					echo "<script>alert('选题组不存在'); document.location.href='issuegroup.php';</script>";
					exit();
				}
			} else {
				echo "<script>alert('未选择任何选题组'); document.location.href='issuegroup.php';</script>";
				exit();
			}
		?>

		


		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/prettify.min.js"></script>
		<script type="text/javascript" src="js/multiselect.min.js"></script>

		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#multi_d').multiselect({
					right: '#multi_d_to, #multi_d_to_2',
					rightSelected: '#multi_d_rightSelected, #multi_d_rightSelected_2',
					leftSelected: '#multi_d_leftSelected, #multi_d_leftSelected_2',
					rightAll: '#multi_d_rightAll, #multi_d_rightAll_2',
					leftAll: '#multi_d_leftAll, #multi_d_leftAll_2',
			 
					search: {
						left: '<input type="text" name="q" class="form-control" placeholder="Search..." />'
					},
			 
					moveToRight: function(Multiselect, $options, event, silent, skipStack) {
						var button = $(event.currentTarget).attr('id');
			 
						if (button == 'multi_d_rightSelected') {
							var $left_options = Multiselect.$left.find('> option:selected');
							Multiselect.$right.eq(0).append($left_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$right.eq(0).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(0));
							}
						} else if (button == 'multi_d_rightAll') {
							var $left_options = Multiselect.$left.children(':visible');
							Multiselect.$right.eq(0).append($left_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$right.eq(0).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(0));
							}
						} else if (button == 'multi_d_rightSelected_2') {
							var $left_options = Multiselect.$left.find('> option:selected');
							Multiselect.$right.eq(1).append($left_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$right.eq(1).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(1));
							}
						} else if (button == 'multi_d_rightAll_2') {
							var $left_options = Multiselect.$left.children(':visible');
							Multiselect.$right.eq(1).append($left_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$right.eq(1).eq(1).find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$right.eq(1));
							}
						}
					},
			 
					moveToLeft: function(Multiselect, $options, event, silent, skipStack) {
						var button = $(event.currentTarget).attr('id');
			 
						if (button == 'multi_d_leftSelected') {
							var $right_options = Multiselect.$right.eq(0).find('> option:selected');
							Multiselect.$left.append($right_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
							}
						} else if (button == 'multi_d_leftAll') {
							var $right_options = Multiselect.$right.eq(0).children(':visible');
							Multiselect.$left.append($right_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
							}
						} else if (button == 'multi_d_leftSelected_2') {
							var $right_options = Multiselect.$right.eq(1).find('> option:selected');
							Multiselect.$left.append($right_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
							}
						} else if (button == 'multi_d_leftAll_2') {
							var $right_options = Multiselect.$right.eq(1).children(':visible');
							Multiselect.$left.append($right_options);
			 
							if ( typeof Multiselect.callbacks.sort == 'function' && !silent ) {
								Multiselect.$left.find('> option').sort(Multiselect.callbacks.sort).appendTo(Multiselect.$left);
							}
						}
					},

					sort: function(a, b) {

						/*在下面的处理多音字的计算逻辑中，虽然用“增”替换了“曾”，但结果文字显示并没有也被替换成“增”，是因为不管你用什么去替换，这个排序函数最终运算的结果是1或-1，通过这个值再去确定传送进来的两个参数a和b的大小关系，中间的运算只是过程，而不是结果。*/
						var pattern = /^曾/;
						if(pattern.test(a.innerHTML) || pattern.test(b.innerHTML)) {
							var textOfA = a.innerHTML;
							var textOfB = b.innerHTML;
							if(pattern.test(textOfA)) textOfA = textOfA.replace(/曾/, "增");
							if(pattern.test(textOfB)) textOfB = textOfB.replace(/曾/, "增");
							//console.log("textOfA is " + textOfA + " and textOfB is " + textOfB + ".  By the way, the return is " + (textOfA).localeCompare(textOfB) + ".");
							//return (textOfA).localeCompare(textOfB);
							return ((a.className == b.className) ? 0 : ((a.className > b.className) ? 1 : -1)) || (textOfA).localeCompare(textOfB);
						}
						

						//这里的NA实际上是对应选项文字就是NA的，而在本设计中，没有文字是NA的选项，所以其实这个判断也是没用的。
						/*
						if (a.innerHTML == 'NA') {
							//console.log(a.innerHTML + " is 1");
							return 1;   
						} else if (b.innerHTML == 'NA') {
							//console.log(b.innerHTML + " is -1");
							return -1;   
						}
						*/

						//return (a.innerHTML > b.innerHTML) ? 1 : -1;  //按照ASCII，即英文方式排序

						//console.log("a is " + a.innerHTML + ", b is " + b.innerHTML + " and the reture is " + (a.innerHTML).localeCompare(b.innerHTML));
						//return (a.innerHTML).localeCompare(b.innerHTML);  //按照拼音排序。这个才是真对排序起作用的判断。

						//双重排序的方法，哪个标准优先哪个放||的前面。参考http://stackoverflow.com/questions/13211709/javascript-sort-array-by-multiple-number-fields
						return ((a.className == b.className) ? 0 : ((a.className > b.className) ? 1 : -1)) || (a.innerHTML).localeCompare(b.innerHTML);
					}
					
					
					
					//keepRenderingSort: true  //保持读取后台值时的顺序，否则前端会按自己的默认方式重新排序。
				});


			});
		</script>
	</body>
</html>
