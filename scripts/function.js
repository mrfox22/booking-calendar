$(document).ready(function() {

	optionLoad();
	myFunction();



	//删除
	$("#bt2").on("click", function() {
		var submitValue = 3;
		mySubmitOfModify(submitValue);
	});



	//下拉选项触发
	$("#s1").on("change", function() {
		myFunction();
	});
	


	//book及modify页面事由项的验证
	var checkBookctValidation = function(bookct) {
		if($(bookct).val().match(/^\s*$/)) {
			$("#pBookct").text("事由不能为空");
			$(bookct).addClass("error");
			return false;
		} else {
			$(bookct).removeClass("error");
			$("#pBookct").text("");
			return true;
		}
	};
	
	//book页面
	$("#bt1").on("click", function() {
		checkBookctValidation("#bookct");
		if(checkBookctValidation("#bookct") == false) {return false;} 
		if(checkBookctValidation("#bookct") == true) {mySubmit();}
	});
	
	//modify页面
	$("#bt3").on("click", function() {
		checkBookctValidation("#bookct1");
		if(checkBookctValidation("#bookct1") == false) {return false;} 
		if(checkBookctValidation("#bookct1") == true) {
			var submitValue = 2;
			mySubmitOfModify(submitValue);
		}
	});
});

//下拉菜单的初始化
function optionLoad() {
	var optS1 = document.getElementById("s1").options;
	var optS2 = document.getElementById("s2").options;

	if(color == undefined) {
		//s1：如果选项值为0，则选项enabled。
		for (var i = 0; i < optS1.length; i++) {
			if(optS1[i].value == 0) {
				optS1[i].disabled = false;
			}
		}

		//s1：分别在无预定和多预定时，选项默认停留的位置。
		var submittedS1 = document.getElementById("submittedS1").value;
		var selectedS1;
		switch(submittedS1) {
			case "0":
				selectedS1 = 0;
				break;
			case "15":
				selectedS1 = 1;
				break;
			case "30":
				selectedS1 = 2;
				break;
			case "45":
				selectedS1 = 3;
				break;
			default:
				submittedS1 = "novalue";  //防止瞎填数而且不是这四个特殊值的时候
		}
		
		if(submittedS1 == "novalue") {
			for(i = 0; i < optS1.length; i++) {
				if(optS1[i].value == 0) {
					optS1[i].selected = true;
					var ii = i;
					break;
				}
			}
		} else {
			for(; selectedS1 < optS1.length; selectedS1++) {
				if(optS1[selectedS1].value == 0) {
					optS1[selectedS1].selected = true;
					ii = selectedS1;
					break;
				}
			}
		}

		//s2：如果选项值为0，则选项enabled。
		for (i = 0; i < optS2.length; i++) {
			if(optS2[i].value == 0) {
				optS2[i].disabled = false;
			}
		}

		//s2：该小时没有任何时段被选时，默认停留在60上，否则停在第一个可选时段的最长时段点上。
		var submittedS2 = document.getElementById("submittedS2").value;
		var selectedS2;
		switch(submittedS2) {
			case "15":
				selectedS2 = 0;
				break;
			case "30":
				selectedS2 = 1;
				break;
			case "45":
				selectedS2 = 2;
				break;
			case "60":
				selectedS2 = 3;
				break;
			default:
				submittedS2 = "novalue";
		}

		if(submittedS2 == "novalue") {  
			for(; ii < optS2.length; ii++) {  //根据s1已定的值推出s2该选的项
				if(optS2[ii].value != 0) {  //防止中间出现预订
					break;
				} else {
					optS2[ii].selected = true;
				}
			}
		} else {
			for(; ii < optS2.length; ii++) {
				if(optS2[ii].value == 0) {
					optS2[ii].selected = true;
				} else {
					break;
				}
			}
		}
	} else {
		if(color == "green" || color== "red") {
			for (i = 0; i < optS1.length; i++) {
				optS1[i].disabled = false;
			}

			for (i = 0; i < optS2.length; i++) {
				optS2[i].disabled = false;
			}

			optS1[0].selected = true;
			optS2[3].selected = true;
		}
					
		if(color == "yellow") {
			var submittedS1 = document.getElementById("submittedS1").value;
			var submittedS2 = document.getElementById("submittedS2").value;
			var selectedS1, selectedS2;

			switch(submittedS1) {
				case "00":
					selectedS1 = 0;
					break;
				case "15":
					selectedS1 = 1;
					break;
				case "30":
					selectedS1 = 2;
					break;
				case "45":
					selectedS1 = 3;
					break;
				default:
					submittedS1 = "novalue"; 
			}

			switch(submittedS2) {
				case "15":
					selectedS2 = 0;
					break;
				case "30":
					selectedS2 = 1;
					break;
				case "45":
					selectedS2 = 2;
					break;
				case "60":
					selectedS2 = 3;
					break;
				default:
					submittedS2 = "novalue";
			}

			//解禁需要调整时段的s1和s2的选项
			for(i = selectedS1; i <= selectedS2; i++) {
				if(optS1[i].value == dId) {
					optS1[i].disabled = false;
					optS2[i].disabled = false;
				}
			}

			//以s1所选值向前遍历，若值为0则解禁，一旦出现其他预订值，保持禁止状态并停止遍历。
			for(i = selectedS1 - 1; i >= 0; i--) {
				if(optS1[i].value == 0) {
					optS1[i].disabled = false;
					optS2[i].disabled = false;
				} else {
					break;
				}
			} 

			//以s2所选值向后遍历，若值为0则解禁，一旦出现其他预订值，保持禁止状态并停止遍历。
			if(selectedS2 == optS2.length - 1) {
				ii = selectedS2;
			} else {
				for(i = Number(selectedS2) + 1; i < optS1.length; i++) {
					if(optS1[i].value == 0) {
						optS1[i].disabled = false;
						optS2[i].disabled = false;
						ii = i;
					} else {
						ii = i - 1;
						break;
					}
				}
			}

			optS1[selectedS1].selected = true;
			optS2[selectedS2].selected = true;
		}	
	}
}

//下拉菜单的变化引起的逻辑变化。用s1决定s2，并且根据选项选中的值更改选项的value。
function myFunction() {
	var optS1 = document.getElementById("s1").options;
	var optS2 = document.getElementById("s2").options;

	//重置s1的value值
	for(var indexOfRollback = 0; indexOfRollback < jWdparr.length; indexOfRollback++) {
		optS1[indexOfRollback].value = jWdparr[indexOfRollback];
	}

	var a;  //根据触发事件生成
	var x = document.getElementById("s1");
	var y = x.options[x.selectedIndex].text;
	switch(y) {
		case "00":
			a = 0;
			break;
		case "15":
			a = 1;
			break;
		case "30":
			a = 2;
			break;
		case "45":
			a = 3;
			break;
	}

	if(color == undefined) {
		//禁掉选择时段点后其他人预订时段之后的所有空白时段点位
		if(optS1[a].selected == true) {
			optS2[a].disabled = false;
			optS1[a].value = a; 
			var b; 
			if(a+1 > 3) {
				b = a;
			} else {
				b = a + 1;
			}
			for (b; b < optS1.length; b++) {
				if(optS1[b].value != 0) {  
					var c = b;
					for(c; c < optS1.length; c++) {
						if(optS1[c].value == 0) {
							var d = c;
							for(d; d < optS2.length; d++) {
								optS2[d].disabled = true;
								optS2[d].selected = false;
							}
							break;
						}
					}
					break;	
				} else {
					optS2[b].disabled = false;
				}
			}
			
			//早于s1中selected的之前s2的时段全部disable掉。
			var e = a - 1;
			if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/8./i)=="8.") {  //判断是否为ie8
				optS2[a].selected = true;  //ie8需要通过直接为选项的selected值为true才能避免选项不会被选在值为false的选项上
				for(; e >= 0 ; e--) {
					optS2[e].disabled = true; 
				}
			} else {
				for(; e >= 0 ; e--) {
					optS2[e].disabled = true;
					optS2[e].selected = false;  //ie8不能通过改selected为false的方式让下拉菜单选到其他selected值为true的选项上
				}
			}
		}

		//永远让s2的value为index值
		for(var f = 0; f < optS2.length; f++) {
			optS2[f].value = f;
		}
	} else {
		if(optS1[a].selected == true) {
			optS1[a].value = a;
			var b = a;
			var c = a - 1;

			//红绿
			if(color == "green" || color== "red") {
				for(; b < optS2.length; b++) {
					optS2[b].disabled = false;
				}
				
				if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/8./i)=="8.") {
					optS2[a].selected = true;
					for(; c >= 0 ; c--) {
						optS2[c].disabled = true;
					}
				} else {
					for(; c >= 0 ; c--) {
						optS2[c].disabled = true;
						optS2[c].selected = false;
					}
				}
			}

			//黄
			if(color == "yellow") {
				var submittedS2 = document.getElementById("submittedS2").value;
				var selectedS2;
				switch(submittedS2) {
					case "15":
						selectedS2 = 0;
						break;
					case "30":
						selectedS2 = 1;
						break;
					case "45":
						selectedS2 = 2;
						break;
					case "60":
						selectedS2 = 3;
						break;
				}

				var ii;
				if(selectedS2 == optS2.length - 1) {
					ii = selectedS2;
				} else {
					for(i = Number(selectedS2) + 1; i < optS1.length; i++) {
						if(optS1[i].value == 0) {
							optS1[i].disabled = false;
							optS2[i].disabled = false;
							ii = i;
						} else {
							ii = i - 1;
							break;
						}
					}
				}

				for(; b <= ii; b++) {
					optS2[b].disabled = false;
				}

				if(navigator.appName == "Microsoft Internet Explorer" && navigator.appVersion.match(/8./i)=="8.") {
					optS2[a].selected = true;
					for(; c >= 0 ; c--) {
						optS2[c].disabled = true;
					}
				} else {
					for(; c >= 0 ; c--) {
						optS2[c].disabled = true;
						optS2[c].selected = false;
					}
				}
			}
		}

		for(var f = 0; f < optS2.length; f++) {
			optS2[f].value = f;
		}
	}
}

function mySubmit() {
	var selectedHour = document.getElementById("selectedHour").value;
	var x1 = document.getElementById("s1");
	var y1 = x1.options[x1.selectedIndex].text;
	var theFormer =  selectedHour + ":" + y1;

	var x2 = document.getElementById("s2");
	var y2 = x2.options[x2.selectedIndex].text;
	if(y2 == 60) {
		selectedHour = Number(selectedHour) + 1;
		y2 = "00";
	}
	var theLatter = selectedHour + ":" + y2;
	
	var xx = "";
	xx += "预订时段" + "\n" 
		+ "日期：" + document.getElementById("dateAndDay").value + "\n"
		+ "时段：" + theFormer + " ~ " + theLatter + "\n" 
		+ "事由：" + document.getElementById("bookct").value;

	if(confirm(xx)) {
		document.getElementById("bt1").value = 1;
	} else {
		document.getElementById("bt1").value = 0;
	}
}

//modify页面的提交
function mySubmitOfModify(submitValue) {
	var selectedHour = document.getElementById("selectedHour").value;
	var x1 = document.getElementById("s1");
	var y1 = x1.options[x1.selectedIndex].text;
	var theFormer =  selectedHour + ":" + y1;

	var x2 = document.getElementById("s2");
	var y2 = x2.options[x2.selectedIndex].text;
	if(y2 == 60) {
		selectedHour = Number(selectedHour) + 1;
		y2 = "00";
	}
	var theLatter = selectedHour + ":" + y2;
	var submitTitle;
	if(submitValue ==2) submitTitle = "修改预定";
	if(submitValue ==3) submitTitle = "取消预定";
	
	var xx = "";
	xx += submitTitle + "\n" 
		+ "日期：" + document.getElementById("dateAndDay").value + "\n"
		+ "时段：" + theFormer + " ~ " + theLatter + "\n" 
		+ "事由：" + document.getElementById("bookct1").value;
	if(confirm(xx)) {
		if(submitValue == 2) document.getElementById("bt3").value = 2;
		if(submitValue == 3) document.getElementById("bt2").value = 3;
	} else {
		if(submitValue == 2) document.getElementById("bt3").value = 0;
		if(submitValue == 3) document.getElementById("bt2").value = 0;
	}
}
