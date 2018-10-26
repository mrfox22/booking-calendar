$(document).ready(function() {

	//以下为更改密码页面表单提交的输入验证
	var checkPs1Validation = function() {
		if(!$("#ps1").val().match(/^[a-zA-Z0-9][a-zA-Z0-9]{3,9}$/i) || $("#ps1").val() == $("#ops").val()) {
			console.log($("#ps1").val());
			$("#ps1").addClass("error");
			$("#pPs1").addClass("redword");
		} else {
			$("#ps1").removeClass("error");
			$("#pPs1").removeClass("redword");
		}
	};

	var checkPs2Validation = function() {
		if($("#ps2").val() != $("#ps1").val()) {
			$("#ps2").addClass("error");
			$("#pPs2").text("两次输入的密码不符。");
			$("#pPs2").addClass("redword");
		} else {
			$("#ps2").removeClass("error");
			$("#pPs2").text("");
			$("#pPs2").removeClass("redword");
		}
	};
	
	//原密码的验证
	$("#ops").on("blur", function() {
		var opsData = {
			"ops" : $("#ops").val(),
			"s_id" : $("#s_id").val()
		};
		
		$.ajax({
			type : "GET",
			url : "changepw.php",
			data : opsData,
			dataType : "json",
			encode : true
		})

			.done(function(data) {
				console.log(data);
				if(!data.success) {
					$("#ops").addClass("error");
					$("#pOps").text(data.errors.ops);
				} else {
					$("#ops").removeClass("error");
					$("#pOps").text("");
				}
			});
	});
	
	$("#ps1").blur(function(e) {
		checkPs1Validation();
	});

	$("#ps2").blur(function(e) {
		checkPs2Validation();
	});
	
	
	$("#pwchangeform").submit(function(event) {
		var formData = {
			"ops" : $("#ops").val(),
			"ps1" : $("#ps1").val(),
			"ps2" : $("#ps2").val(),
			"s_id" : $("#s_id").val()
		};

		$.ajax({
			type : "POST",
			url : "changepw.php",
			data: formData,
			dataType : "json",
			encode : true,

			success : function(data) {
				console.log(data);

				if(!data.success) {
					if(data.errors.ops) {
						$("#ops").addClass("error");
						$("#pOps").text(data.errors.ops);
					} else {
						$("#ops").removeClass("error");
						$("#pOps").text("");
					}

					if(data.errors.ps1) {
						$("#ps1").addClass("error");
						$("#pPs1").addClass("redword");
					} else {
						$("#ps1").removeClass("error");
						$("#pPs1").removeClass("redword");
					}

					if(data.errors.ps2) {
						$("#ps2").addClass("error");
						$("#pPs2").text(data.errors.ps2);
					} else {
						$("#ps2").removeClass("error");
						$("#pPs2").text("");
					}

					$("#pSuccess").text("");
					$("#pSuccess").removeClass("done");
				} else {
					$("#pSuccess").addClass("done");
					$("#pSuccess").text(data.message);
					$("#ops").val("");
					$("#ops").removeClass("error");
					$("#pOps").text("");
					$("#ps1").val("");
					$("#ps1").removeClass("error");
					$("#pPs1").removeClass("redword");
					$("#ps2").val("");
					$("#ps2").removeClass("error");
					$("#pPs2").text("");
				}
			}
		})
		
		event.preventDefault();
	
	}); 
	

	//更改密码的启用禁用
	function myClick() {
		var ckbox = document.getElementById("ckbox");
		var obj = document.getElementById("pwchangeform");
		var elements = obj.getElementsByTagName("input");

		if(ckbox.checked == true) {for(var i = 0; i < elements.length; i++) {elements[i].disabled = false;}}
		if(ckbox.checked == false) {
			for(var i = 0; i < elements.length; i++) {elements[i].disabled = true;}
			$("#ops").val("");
			$("#ops").removeClass("error");
			$("#pOps").text("");
			$("#ps1").val("");
			$("#ps1").removeClass("error");
			$("#pPs1").removeClass("redword");
			$("#ps2").val("");
			$("#ps2").removeClass("error");
			$("#pPs2").text("");
		}
	}
	
	$("#ckbox").on("click", function() {
		myClick();
	});
	
	
	


	//以下为后台管理edit页面的验证
	var checkNameValidation = function() {
		if($("#name").val().match(/^\s*$/)) {
			$("#pName").text("姓名不能为空。");
			$("#name").addClass("error");
			return false;
		} else if($("#name").val().match(/\s/)) {
			$("#pName").text("姓名中不能有空格。");
			$("#name").addClass("error");
			return false;
		} else {
			$("#name").removeClass("error");
			$("#pName").text("");
			return true;
		}
	}

	$("#name").on("blur", function() {
		checkNameValidation();
	});

	$("#admineditpost").submit(function(e) {
		checkNameValidation();
		if(checkNameValidation() == false) {return false;}
	});
});