//后台管理add页面的表单验证
$(document).ready(function() {
			
	//username的即时检测
	$("#username").on("blur", function() {
		if(!$(this).val().match(/^[a-zA-Z0-9][a-zA-Z0-9_]{1,15}$/i)) {
			$("#pUsername").text("用户名只能包括字母（A-Z，a-z）、数字（0-9）及下划线（_），必须以字母或数字开头。最少2个字符，最多16个字符。");
			$(this).addClass("error");
		} else {
			//方法三，传统的ajax用法，success是一个参数。
			var usernameData = {"username" : $("#username").val()};
			$.ajax({
				type : "GET",
				url : "process.php",
				data : usernameData,
				dataType : "json",
				encode : true,
				
				success : function(data) {
					console.log(data);

					if(!data.success) {
						$("#username").addClass("error");
						$("#pUsername").text(data.errors.username);
					} else {
						$("#username").removeClass("error");
						$("#pUsername").text("");
					}
				}
			})
		}
	});

	//name的即时检测
	$("#name").on("blur", function() {
		if($(this).val().match(/^\s*$/)) {
			$("#pName").text("姓名不能为空。");
			$(this).addClass("error");
		} else if($(this).val().match(/\s/)) {
			$("#pName").text("姓名中不能有空格。");
			$(this).addClass("error");
		} else {
			$("#name").removeClass("error");
			$("#pName").text("");
		}
	});

	//提交的即时检测
	$("#adduserpost").submit(function(event) {
		
		var formData = {
			"name" : $("#name").val(),
			"username" : $("#username").val(),
			"userright": $("#userright").val(),
			"dep" : $("#dep").val()
		};

		$.ajax({
			type : "POST",
			url : "getuser.php",
			data : formData,
			dataType : "json",
			encode : true
		})

			.done(function(data) {
				console.log(data);

				if(!data.success) {
					if(data.errors.name) {
						$("#name").addClass("error");
						$("#pName").text(data.errors.name);
					} else {
						$("#name").removeClass("error");
						$("#pName").text("");
					}

					if(data.errors.username) {
						$("#username").addClass("error");
						$("#pUsername").text(data.errors.username);
					} else {
						$("#username").removeClass("error");
						$("#pUsername").text("");
					}

					$("#pSuccess").text("");
					$("#pSuccess").removeClass("done");
				} else {
					$("#pSuccess").addClass("done");
					$("#pSuccess").text(data.message);
					$("#name").val("");
					$("#name").removeClass("error");
					$("#pName").text("");
					$("#username").val("");
					$("#username").removeClass("error");
					$("#pUsername").text("");
				}
			});

		event.preventDefault();
		
	});
});