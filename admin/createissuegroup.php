<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Create Issue Group</title>

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
		?>

		<div class="nav1"></div>

		<div id="main">
			<div id="demo" class="container">
				
				<!--Multiple destinations-->
				<h4 id="demo-multiple-destinations">Multiple destinations</h4>
				<div class="row">
					<div class="col-xs-5">
						<select name="from[]" id="multi_d" class="form-control" size="26" multiple="multiple">
							<?php
								$classesOfDepts = array("1"=>"rmt", "2"=>"bgs", "3"=>"zx", "4"=>"ch", "5"=>"yyjmb", "6"=>"ds", "7"=>"yyzx", "8"=>"xt");
								$sql_all = "select * from bk_staff";
								$rs_all = mysql_query($sql_all);
								while($row_all = mysql_fetch_array($rs_all)) {
									echo "<option value='".$row_all['s_id']."' class='".$classesOfDepts[$row_all['s_dep']]."'>".$row_all['s_name']."</option>";
								}
							?>
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
							
							选题表名称：<input type="text" name="groupName"><br>
							选题表代码：<input type='text' name='groupCode'><br>  <!--不可重复，表名称的唯一值-->

							<b>管理员</b>
							<select name="to[]" id="multi_d_to" class="form-control" size="8" multiple="multiple">
							</select>
							
							<br/><hr/><br/>
							
							<b>用户</b>
							<select name="to_2[]" id="multi_d_to_2" class="form-control" size="8" multiple="multiple">
							</select>
							<input type="hidden" name="act" value="create"><br><br>
							<input type="submit">　<a href="issuegroup.php">返回首页</a>
						</form>
					</div>
				</div>
			</div>
		</div>

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

						var pattern = /^曾/;
						if(pattern.test(a.innerHTML) || pattern.test(b.innerHTML)) {
							var textOfA = a.innerHTML;
							var textOfB = b.innerHTML;
							if(pattern.test(textOfA)) textOfA = textOfA.replace(/曾/, "增");
							if(pattern.test(textOfB)) textOfB = textOfB.replace(/曾/, "增");
							return ((a.className == b.className) ? 0 : ((a.className > b.className) ? 1 : -1)) || (textOfA).localeCompare(textOfB);
						}
						
						return ((a.className == b.className) ? 0 : ((a.className > b.className) ? 1 : -1)) || (a.innerHTML).localeCompare(b.innerHTML);
					}
				});
			});
		</script>
	</body>
</html>
