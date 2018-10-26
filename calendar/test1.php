<!doctype html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>test2</title>
		<script type="text/javascript" src="js/jquery-core/jquery-1.4.2.js"></script>
	</head>

	<body>
		<?php
			include("conn.php");

			if(!empty($_POST['test1']) && isset($_POST['test2'])) {
				var_dump($_POST['test1']);
				echo "<br>";
				var_dump($_POST['test2']);
				echo "<br>";

				echo $result = $_POST['test2'] == 444 ? "==会自动转格式" : "==不会自动转格式";
				echo "<br>";

				$sql = "select * from bk_staff where s_id='".$_POST['test1']."'";
				$result = mysql_query($sql);
				if($row = mysql_fetch_array($result)) {
					echo $row['s_name']."test1在取值范围内，sql会自动转格式。<br>";
				}
			} else {
				echo "shit";
			}

			echo "<br>";

			$arr = array();
			echo !empty($arr) ? "Empty array is actually not empty." : "Empty array is just empty.";

			echo "<br>";
			print_r($arr);
			var_dump($arr);

			echo "<br>";
			$str = "4,9,45,76";
			$unknownType = explode(",", $str);
			var_dump($unknownType);

			echo "<br>";
			$str1 = "";
			$unknownType1 = explode(",", $str1);
			echo !empty($str1) ? "Blank string is actually not empty.<br>" : "Blank string is just empty.<br>";
			var_dump($unknownType1);

			echo "<br>";
			$i = 1;
			echo $i == 1 ? "exist" : "undefined";

		?>

		<form action="" method="post">
			<input id="test1" type="text" name="test1" value="99">
			
			<select name="test2">
				<option value="444">四四四</option>
				<option value=444>444</option>
			</select>
			<input type="submit">
		</form>

		<script>
			var j = "";
			if(j == 0) {
				console.log("js中空字符相等于0（==）");
			} else {
				console.log("js中空字符不相等于0（==）");
			}

			var k = null;
			if(k == 0) {
				console.log("js中null相等于0（==）");
			} else {
				console.log("js中null不相等于0（==）");
			}

			var l = NaN;
			if(l == 0) {
				console.log("js中NaN相等于0（==）");
			} else {
				console.log("js中NaN不相等于0（==）");
			}

			var m;
			if(m == 0) {
				console.log("js中未赋值相等于0（==）");
			} else if(m == undefined) {
				console.log("js中未赋值相等于undefined（==）");
			} else {
				console.log("js中未赋值不相等于0，也不相等于undefined（==）");
			}

			console.log(typeof $("#test1").val());

			var hey = "ajkslloishhjss";
			console.log(hey.replace(/s/g, "x"));

			var today = new Date();
			console.log(today.getDate().toString());

			var pp = 1
			function c() {
				pp = pp+2;
			}
			c();
			console.log(pp);

			var hey1 = "2016-04-20";
			console.log(hey1.replace(/-/g, ""));

			console.log(parseInt(""));
			console.log(0);
			console.log("0");

			var s = 0;
			if(s) {
				console.log("js中单独的数值0在if判断中表存在。");
			} else {
				console.log("js中单独的数值0在if判断中表不存在。");
			}
		</script>
	</body>
</html>
