<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Connection Test</title>
	</head>

	<body>
		<?php
			//mysql_connect("localhost", "root", "") or die("mysql连接失败");
			//mysql_select_db("bk") or die("mysql连接失败");
			//mysql_query("set names utf8");

			$conn = mysqli_connect("localhost", "root", "") or die("mysql连接失败");
			mysqli_select_db($conn, "bk") or die("mysql连接失败");
			mysqli_query("set names utf8");
		?>

		<p>It's not my problem...</p>

	</body>
</html>