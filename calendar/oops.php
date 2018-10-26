<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
	<head>
		<meta name="viewport" content="width=460">
		<meta name="MobileOptimized" content="460" /> 
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-touch-fullscreen" content="yes" />
		<meta content="telephone=no" name="format-detection" />
		<meta content="email=no" name="format-detection" />
		<meta name="Description" content="">
		<link rel="stylesheet" href="styles/ui.css" media="all" />
		<!--[if lt IE9]> 
			<script src="scripts/html5shiv.js"></script>
		<![endif]-->

		<meta charset="UTF-8">
		<title>出错了</title>

		<link rel="stylesheet" type="text/css" href="css/frontierCalendar/jquery-frontier-cal-1.3.2.css" />
		<link rel="stylesheet" type="text/css" href="css/colorpicker/colorpicker.css" />
		<link rel="stylesheet" type="text/css" href="css/jquery-ui/smoothness/jquery-ui-1.8.1.custom.css" />
		<link rel="stylesheet" type="text/css" href="css/ui.css" />

		<script type="text/javascript" src="js/jquery-core/jquery-1.4.2-ie-fix.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui/smoothness/jquery-ui-1.8.1.custom.min.js"></script>
		<script type="text/javascript" src="js/colorpicker/colorpicker.js"></script>
		<script type="text/javascript" src="js/jquery-qtip-1.0.0-rc3140944/jquery.qtip-1.0.js"></script>

		<script type="text/javascript" src="js/lib/jshashtable-2.1.js"></script>
		<script type="text/javascript" src="js/frontierCalendar/jquery-frontier-cal-1.3.2.js"></script>
		
		
	</head>

	<body style="background-color: #aaaaaa;">

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
		
		<h1 id="pageTitle">选题日历</h1>
			
		<div id="tabs">

			<div id="tabs-2">

				<p style="font-size:20px">出错了！您可能不属于任何选题组，或您还没有 <a href="../login.php" style="color:red; font-style:italic">登录</a>。</p>
			
			</div>

		</div>

		<script>
			$(document).ready(function() {
				$("#tabs").tabs();
			});
		</script>
	</body>
</html>