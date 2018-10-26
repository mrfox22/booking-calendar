$(document).ready(function() {
	
	$("#groupName").on("blur", function() {
		if($(this).val().match(/^\s*$/)) {
			$("#groupNameError").text("选题组名称不能为空。");
			$(this).addClass("error");
		} else if($(this).val.match(/^s/)) {
			$("#groupNameError").text("选题组名称中不能有空格。");
			$(this).addClass("error");
		} else {
			var groupName = $()
			$.ajax({
				type : "GET",
				url : "process.php",

			})
		}
	});
});