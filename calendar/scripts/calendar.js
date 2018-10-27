/*总前后端数据逻辑原则，先后端更新再前端刷新*/
$(document).ready(function(){	

	$( "#radioset" ).buttonset();

	//这里面定义的变量都是整个环境要用到的全局变量，注意最后对应的进程结束后，须把其值恢复为初始值，以免影响到下一个进程的初始值。
	var clickDate = "";
	var clickDateNoHyphen = "";
	var clickAgendaItem = "";
	var clickTitle = "";  //事件的标题
	var clickContents = "";  //事件的内容
	var clickEditorId = "2";  //此值为2似乎没意义，应该也跟其他一样，设置成空。
	var clickMyNum = "";
	var clickEditor = "";
	var fullToday = "";
	var sid1 = getCookie("sid");  //进入页面时的最初sid
	var id = "";  //选题组id

	var firstButton = document.getElementById("issueName1");
	//id = firstButton.rel;
	id = firstButton.value;
	console.log(id);

	/**/
	var jfcalplugin = $("#mycal").jFrontierCal({
		date: new Date(),
		dayClickCallback: myDayClickHandler,
		agendaClickCallback: myAgendaClickHandler,
		agendaDropCallback: myAgendaDropHandler,

		agendaMouseoverCallback: myAgendaMouseoverHandler,

		applyAgendaTooltipCallback: myApplyTooltip,

		agendaDragStartCallback : myAgendaDragStart,
		agendaDragStopCallback : myAgendaDragStop,

		dragAndDropEnabled: false
	}).data("plugin");
	

	// re-render agenda items if calendar element moves
	jfcalplugin.reRenderAgendaItems("#mycal");	
	

	/**
	 * Do something when dragging starts on agenda div
	 */
	function myAgendaDragStart(eventObj,divElm,agendaItem){
		// destroy our qtip tooltip
		if(divElm.data("qtip")){
			divElm.qtip("destroy");
		}	
	};

	
	/**
	 * Do something when dragging stops on agenda div
	 */
	function myAgendaDragStop(eventObj,divElm,agendaItem){
		//alert("drag stop");
	};

	
	/**
	 * Custom tooltip - use any tooltip library you want to display the agenda data.
	 * for this example we use qTip - http://craigsworks.com/projects/qtip/
	 *
	 * @param divElm - jquery object for agenda div element
	 * @param agendaItem - javascript object containing agenda data.
	 */
	function myApplyTooltip(divElm,agendaItem){

		// Destroy currrent tooltip if present
		if(divElm.data("qtip")){
			divElm.qtip("destroy");
		}
		
		//提示的内容
		var displayData = "";
		
		var title = agendaItem.title;
		var startDate = agendaItem.startDate;
		var endDate = agendaItem.endDate;
		var allDay = agendaItem.allDay;
		var data = agendaItem.data;

		displayData += "<b>" + title+ "</b><br>";
		displayData += data.editor + "<br><br>";
		
		for (var propertyName in data) {
			if(propertyName == "contents") {
				displayData += nToBr(data[propertyName]) + "<br>";
			}
		}

		// use the user specified colors from the agenda item.
		var backgroundColor = agendaItem.displayProp.backgroundColor;
		var foregroundColor = agendaItem.displayProp.foregroundColor;
		
		var myStyle = {
			border: {
				width: 5,
				radius: 10
			},
			"font-size" : "12px",
			padding: 10, 
			textAlign: "left",
			border: {
				width: 7,
				radius: 5,
				color: '#A2D959'
			},
			tip: true,
			name: "dark" // other style properties are inherited from dark theme		
		};

		if(backgroundColor != null && backgroundColor != ""){
			myStyle["backgroundColor"] = backgroundColor;
		}
		if(foregroundColor != null && foregroundColor != ""){
			myStyle["color"] = foregroundColor;
		}

		// apply tooltip
		divElm.qtip({
			content: displayData,
			position: {
				corner: {
					tooltip: "bottomMiddle",
					target: "topMiddle"			
				},
				adjust: { 
					mouse: true,
					x: 0,
					y: -15
				},
				target: "mouse"
			},
			show: { 
				when: { 
					event: 'mouseover'
				}
			},
			style: myStyle
		});
	};


	//Calendar Size
	jfcalplugin.setAspectRatio("#mycal",0.75);


	//点日期。检查用户权限。
	function myDayClickHandler(eventObj){
		var today = new Date();
		var todayDate = today.getDate()<10 ? "0"+today.getDate() : today.getDate();
		var todayMonth = (today.getMonth()+1)<10 ? "0"+(today.getMonth()+1) : (today.getMonth()+1);
		var todayYear = today.getFullYear();
		fullToday =  todayYear.toString() + todayMonth.toString() + todayDate.toString();

		var date = eventObj.data.calDayDate;
		var monthPrefixZero = date.getMonth()+1 < 10 ? "0" + (date.getMonth()+1) : date.getMonth()+1;  //小日历月份日期带不带0都能认，但通过小日历调整日期后，就会变成带0的。为了后台格式统一，这里都调整为带0的。
		var datePrefixZero = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
		clickDate = date.getFullYear() + "-" + monthPrefixZero + "-" + datePrefixZero;
		clickDateNoHyphen = date.getFullYear().toString() + monthPrefixZero.toString() + datePrefixZero.toString();  //注意，这里要用到字符串相加的结果，注意格式转换。

		var sid2 = getCookie("sid");

		if(sid2 == sid1) {
			$.ajax({
				type : "POST",
				url : "process.php",
				data : {
					action : "getright",
					s_id : sid2,
					id : id
				},
				dataType : "json",
				encode : true,

				success : function(getright) {
					var userRight = parseInt(getright.rt);  //用户在系统中的角色
					if(userRight) {
						//if(userRight == 1 || userRight ==2) {  对任何权限皆不可对历史日期再添加选题。此段代码为以后的功能留用。

						if(userRight==1 || getright.admin) {  //getright.admin是用户在选题组中的角色，下面的getright.member同理。
							$('#add-event-form').dialog({title : "添加新选题"}).data("param", userRight).data("para", sid2).data("admin", getright.admin).dialog('open');  
							$('#add-event-form').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(1).hide(); 
							$('#add-event-form').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(2).hide();
						} else if(userRight == 2 && getright.member) {
						//} else if(userRight == 2) {
							if(parseInt(clickDateNoHyphen) >= parseInt(fullToday)) {
								$('#add-event-form').dialog({title : "添加新选题"}).data("param", userRight).data("para", sid2).data("admin", getright.admin).dialog('open');  //先打开
								$('#add-event-form').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(1).hide();  //再隐藏掉按钮
								$('#add-event-form').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(2).hide();  //也是隐藏按钮，分两步隐藏。
							}
						}
					} 
				}
			});
		} else {  //用户已退出或超时
			alert("用户已退出或用户已被删除。");
			document.location.href = "../login.php";
		}
	};
	

	//点事件。检查用户权限。
	function myAgendaClickHandler(eventObj){
		var today = new Date();
		var todayDate = today.getDate()<10 ? "0"+today.getDate() : today.getDate();
		var todayMonth = (today.getMonth()+1)<10 ? "0"+(today.getMonth()+1) : (today.getMonth()+1);
		var todayYear = today.getFullYear();
		fullToday =  todayYear.toString() + todayMonth.toString() + todayDate.toString();

		// Get ID of the agenda item from the event object
		var agendaId = eventObj.data.agendaId;
		
		// pull agenda item from calendar
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
		clickAgendaItem = agendaItem;

		var monthPrefixZero = clickAgendaItem.data.mm < 10 ? "0" + clickAgendaItem.data.mm : clickAgendaItem.data.mm;
		var datePrefixZero = clickAgendaItem.data.dd < 10 ? "0" + clickAgendaItem.data.dd : clickAgendaItem.data.dd;

		clickDate = clickAgendaItem.data.yyyy + "-" + monthPrefixZero + "-" + datePrefixZero;  
		//clickDateNoHyphen = clickAgendaItem.data.yyyy + monthPrefixZero + datePrefixZero;
		clickDateNoHyphen = clickAgendaItem.data.yyyy.toString() + monthPrefixZero.toString() + datePrefixZero.toString();  //同点日期，需要用字符串相加，而不是数字相加
		clickTitle = clickAgendaItem.title;
		clickContents = clickAgendaItem.data.contents;
		clickEditorId = clickAgendaItem.data.editorId;
		clickMyNum = clickAgendaItem.data.myNum;
		clickEditor = clickAgendaItem.data.editor;
		var clickEditorRight = clickAgendaItem.data.editorRight;
		
		var sid = getCookie("sid");  //sid在这里取值，是因为cookie是有失效时间的，所以应当在每次点击的时候去检查，而不是在加载页面的时候只检查一次。

		$.ajax({
			type : "POST",
			url : "process.php",
			data : {
				action : "getright",
				s_id : sid,
				id : id
			},
			dataType : "json",
			encode : true,

			success : function(getright) {
				var userRight = parseInt(getright.rt);
				if(userRight) {

					//if(userRight==1 || (parseInt(clickEditorId)==parseInt(sid) && userRight==2) || (parseInt(clickEditorRight)==4 && userRight==2)) { 此行为对所有用户包括管理员，都不可以修改历史选题，替换下面的if和else if两个条件。

					if(userRight==1 || getright.admin) {  //1是系统管理员，admin是选题组管理员。
						$('#add-event-form').dialog({title : "编辑选题"}).data("param", userRight).data("para", sid).data("admin", getright.admin).dialog('open').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(3).hide();
					} else if((parseInt(clickEditorId)==parseInt(sid) && userRight==2 && getright.member) || (parseInt(clickEditorRight)==4 && userRight==2 && getright.member)) {
						if(parseInt(clickDateNoHyphen) >= parseInt(fullToday)) {
							$('#add-event-form').dialog({title : "编辑选题"}).data("param", userRight).data("para", sid).data("admin", getright.admin).dialog('open').dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(3).hide();  //一句话隐藏按钮，因为只需要隐藏一个按钮，所以写一句即可。注意，这里传了两值过去。
						} else {
							$("#display-event-form").dialog('open');
						}
					} else {
						$("#display-event-form").dialog('open');
					}
				} else {
					alert("用户已退出或用户已被删除。。");
					document.location.href = "../login.php";
				}
			}
		});
	};

	
	/**
	 * Called when user drops an agenda item into a day cell.
	 */
	function myAgendaDropHandler(eventObj){
		// Get ID of the agenda item from the event object
		var agendaId = eventObj.data.agendaId;
		// date agenda item was dropped onto
		var date = eventObj.data.calDayDate;
		// Pull agenda item from calendar
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);		
		//alert("You dropped agenda item " + agendaItem.title + 
			//" onto " + date.toString() + ". Here is where you can make an AJAX call to update your database.");

		var jsDate = agendaItem.startDate;
		var theYear = jsDate.getFullYear().toString();
		var theMonth = (jsDate.getMonth()+1) < 10 ? "0" + (jsDate.getMonth()+1) : jsDate.getMonth()+1;
		var theDate = jsDate.getDate() < 10 ? "0" + jsDate.getDate() : jsDate.getDate();
		var startDate = theYear + "-" + theMonth + "-" + theDate; 
		
		//更新数据库事件
		$.ajax({
			type : "POST",
			url : "process.php",
			data : {
				action : "drag",
				eventDate : startDate,
				e_id : agendaItem.data.myNum,
				e_etime : phpTime(),
				etimeInPhp : agendaItem.data.etimeInPhp
			},
			dataType : "json",
			encode : true,

			success : function(drag) {
				if(drag.action) {
					eventsOnCalendar(startDate);
				} else {
					alert(drag.msg)
					eventsOnCalendar(startDate);
				}
			}
		});
	};

	
	/**
	 * Called when a user mouses over an agenda item	
	 */
	function myAgendaMouseoverHandler(eventObj){
		var agendaId = eventObj.data.agendaId;
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
		//alert("You moused over agenda item " + agendaItem.title + " at location (X=" + eventObj.pageX + ", Y=" + eventObj.pageY + ")");
	};


	//添加前端事件。不检查用户权限。
	function addEventOnCalendar(event, seqNumber) {
		jfcalplugin.addAgendaItem(
			"#mycal",
			event.e_title[seqNumber],
			new Date(event.yyyy[seqNumber], event.mm[seqNumber]-1,event.e_dd[seqNumber],0,0,0,0),
			new Date(event.yyyy[seqNumber], event.mm[seqNumber]-1,event.e_dd[seqNumber],0,0,0,0),
			false,
			{
				myAddedDate: new Date(parseInt(event.e_atime[seqNumber])),
				myLastEditedDate: new Date(parseInt(event.e_etime[seqNumber])),
				myNum: event.e_id[seqNumber],
				contents: event.e_contents[seqNumber],
				yyyymm : event.e_yyyymm[seqNumber],
				yyyy : event.yyyy[seqNumber],
				mm : event.mm[seqNumber],
				dd : event.e_dd[seqNumber],
				etimeInPhp : event.etimeInPhp[seqNumber],
				editorId : event.e_editor[seqNumber],
				editor : event.editor[seqNumber],
				editorRight : event.e_edright[seqNumber]
			},
			{
				backgroundColor: "#333333",
				foregroundColor: "#ffffff"
			}
		);
	}

	
	//显示邻近三个月的事件。不检查用户权限。
	function eventsOnCalendar(selectedDate, id) {
		
		var selectedDtArray = selectedDate.split("-");
		var selectedYear = selectedDtArray[0];
		var selectedMonth = selectedDtArray[1];
		var selectedYyyymm = selectedYear + selectedMonth;

		//上一月
		var yearOfPreMonth, monthOfPreMonth, preYyyymm;
		if(parseInt(selectedMonth) === 1) {
			yearOfPreMonth = (parseInt(selectedYear) - 1).toString();
			monthOfPreMonth = "12";
		} else {
			yearOfPreMonth = selectedYear;
			if((parseInt(selectedMonth) - 1) < 10) {
				monthOfPreMonth = "0" + (parseInt(selectedMonth) - 1);
			} else {
				monthOfPreMonth = (parseInt(selectedMonth) - 1).toString();
			}
		}
		preYyyymm = yearOfPreMonth + monthOfPreMonth;

		//下一月
		var yearOfNextMonth, monthOfNextMonth, nextYyyymm;
		if(parseInt(selectedMonth) === 12) {
			yearOfNextMonth = (parseInt(selectedYear) + 1).toString();
			monthOfNextMonth = "01";
		} else {
			yearOfNextMonth = selectedYear;
			if((parseInt(selectedMonth) + 1) < 10) {
				monthOfNextMonth = "0" + (parseInt(selectedMonth) + 1);
			} else {
				monthOfNextMonth = (parseInt(selectedMonth) + 1).toString();
			}
		}
		nextYyyymm = yearOfNextMonth + monthOfNextMonth;
		
		//当前日历上的所有事件
		var allItemsArray = jfcalplugin.getAllAgendaItems("#mycal");

		//选出邻近三月并已在前端中的事件
		var adjacentThreeMonthsItemsMyNumArray = [];
		for(var iii = 0; iii < allItemsArray.length; iii++) {
			if(allItemsArray[iii].data.yyyymm == selectedYyyymm || allItemsArray[iii].data.yyyymm == preYyyymm || allItemsArray[iii].data.yyyymm == nextYyyymm) {
				adjacentThreeMonthsItemsMyNumArray.push(parseInt(allItemsArray[iii].data.myNum));
			}
		}

		$.ajax({
			url : "process.php",
			type : "POST",
			data : {
				action : "load",
				selectedDate : selectedDate,  //根据日期选择栏的变化决定每月显示事件
				id : id  
			},
			dataType : "json",
			encode : true,

			success : function(events) {
				var adjacentThreeMonthsItemsMyNumArrayInDb = [];  //数据库中邻近三个月的myNum值数组
				
				//返回值events为邻近三个月的记录，需要有记录内容才开始进行添加。
				if(events.counts) {
					for(var ii = 1; ii < events.counts + 1; ii++) {  //ii从1算起，是因为php过来的数组就是从1开始的。
						adjacentThreeMonthsItemsMyNumArrayInDb.push(parseInt(events.e_id[ii]));

						//myNum值唯一，就是数据库中的自增长的主键。
						var agendaItemDataAttr = jfcalplugin.getAgendaItemByDataAttr("#mycal", "myNum", events.e_id[ii]);  

						//agendaItemDataAttr.length的值如果存在，则为1。
						//删除同数据库中id值相同的前端事件，然后在把数据库中的事件添加到前端。
						if(agendaItemDataAttr.length) {
							if(agendaItemDataAttr[0].data.etimeInPhp != events.etimeInPhp[ii]) {  //判断前后端事件修改事件是否相同，不同再删除前端更新为后端。
								jfcalplugin.deleteAgendaItemById("#mycal", agendaItemDataAttr[0].agendaId);
								addEventOnCalendar(events, ii);
							}
						} else {  //如果该值不存在，直接添加。
							addEventOnCalendar(events, ii);
						}
					}
				}

				//删除前端有而后端没有的事件
				for(var iiii = 0; iiii < adjacentThreeMonthsItemsMyNumArray.length; iiii++) {
					if(adjacentThreeMonthsItemsMyNumArrayInDb.indexOf(adjacentThreeMonthsItemsMyNumArray[iiii]) == -1) {
						jfcalplugin.deleteAgendaItemByDataAttr("#mycal", "myNum", adjacentThreeMonthsItemsMyNumArray[iiii]);
					}
				}
			}
		});
	}


	//js时间转换为php时间
	function phpTime() {
		return Math.floor(new Date().getTime() / 1000);
	}


	//选题组标题显示
	function pageTitle(id) {
		$.ajax({
			url : "process.php",
			type : "POST",
			data : {
				action : "pageTitle",
				id : id
			},

			success : function(responseText) {
				$("#pageTitle").html("选题日历 - " + responseText);
			}
		});
		
	}
	
	/**
	 * Initialize jquery ui datepicker. set date format to yyyy-mm-dd for easy parsing
	 */
	$("#dateSelect").datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat: 'yy-mm-dd'
	});
	
	/**
	 * Set datepicker to current date
	 */
	$("#dateSelect").datepicker('setDate', new Date());
	/**
	 * Use reference to plugin object to a specific year/month。不检查用户权限。
	 */
	$("#dateSelect").bind('change', function() {
		var selectedDate = $("#dateSelect").val();
		var dtArray = selectedDate.split("-");
		var year = dtArray[0];
		// jquery datepicker months start at 1 (1=January)		
		var month = dtArray[1];
		// strip any preceeding 0's		
		month = month.replace(/^[0]+/g,"")	
		//console.log(month-1);
		var day = dtArray[2];
		// plugin uses 0-based months so we subtrac 1
		jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());
		//jfcalplugin.showMonth("#mycal",year,parseInt(month-1));

		//当日期选择栏发生变化时促发每月事件显示
		eventsOnCalendar(selectedDate, id);  //id为选题组号
		pageTitle(id);

		$.ajax({
			url : "process.php",
			type : "POST",
			data : {
				action : "getusers",
				id : id
			},

			success : function(responseText) {
				$("#editor").html(responseText);

				$("#editor").html($("#editor option").sort(function (a, b) {
					//处理多音字
					var pattern = /^曾/;
					if(pattern.test(a.text) || pattern.test(b.text)) {
						var textOfA = a.text;
						var textOfB = b.text;
						if(pattern.test(textOfA)) textOfA = textOfA.replace(/曾/, "增");
						if(pattern.test(textOfB)) textOfB = textOfB.replace(/曾/, "增");
						return (textOfA).localeCompare(textOfB);
					}

					//非多音字
					return (a.text).localeCompare(b.text);
				}));

				$("#editor").prepend("<option value='2'>待定</option>");
			}
		});
	}).change();  //加上change()让页面加载时即执行


	//点击部门按钮刷新日历。不检查用户权限。
	$(".issueName").click(function(event) {
		//if(id != event.target.rel) {
		if(id != event.target.value) {
			//id = event.target.rel;
			id = event.target.value;
			jfcalplugin.deleteAllAgendaItems("#mycal");

			var selectedDate = $("#dateSelect").val();
			var dtArray = selectedDate.split("-");
			var year = dtArray[0];
			// jquery datepicker months start at 1 (1=January)		
			var month = dtArray[1];
			// strip any preceeding 0's		
			month = month.replace(/^[0]+/g,"")		
			var day = dtArray[2];
			// plugin uses 0-based months so we subtrac 1
			jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());

			//当日期选择栏发生变化时促发每月事件显示
			eventsOnCalendar(selectedDate, id);  //id为选题组号
			pageTitle(id);

			//根据选题组，切换成员的下拉菜单选项。
			$.ajax({
				url : "process.php",
				type : "POST",
				data : {
					action : "getusers",
					id : id
				},

				success : function(responseText) {
					$("#editor").html(responseText);

					$("#editor").html($("#editor option").sort(function (a, b) {
						//处理多音字
						var pattern = /^曾/;
						if(pattern.test(a.text) || pattern.test(b.text)) {
							var textOfA = a.text;
							var textOfB = b.text;
							if(pattern.test(textOfA)) textOfA = textOfA.replace(/曾/, "增");
							if(pattern.test(textOfB)) textOfB = textOfB.replace(/曾/, "增");
							return (textOfA).localeCompare(textOfB);
						}

						//非多音字
						return (a.text).localeCompare(b.text);
					}));

					$("#editor").prepend("<option value='2'>待定</option>");
				}
			});
		}
		//console.log(id);
	});


	/**
	 * Initialize previous month button。不检查用户权限。
	 */
	$("#BtnPreviousMonth").button();
	$("#BtnPreviousMonth").click(function() {
		jfcalplugin.showPreviousMonth("#mycal");
		// update the jqeury datepicker value
		var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
		var cyear = calDate.getFullYear();
		// Date month 0-based (0=January)
		var cmonth = calDate.getMonth();
		var cday = calDate.getDate();
		// jquery datepicker month starts at 1 (1=January) so we add 1
		$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);
		
		//当上一月按钮被点击时促发每月事件显示
		var selectedDate = $("#dateSelect").val();
		eventsOnCalendar(selectedDate, id);
		return false;
	});
	/**
	 * Initialize next month button。不检查用户权限。
	 */
	$("#BtnNextMonth").button();
	$("#BtnNextMonth").click(function() {
		jfcalplugin.showNextMonth("#mycal");
		// update the jqeury datepicker value
		var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
		var cyear = calDate.getFullYear();
		// Date month 0-based (0=January)
		var cmonth = calDate.getMonth();
		var cday = calDate.getDate();
		// jquery datepicker month starts at 1 (1=January) so we add 1
		$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);	

		//当下一月按钮被点击时促发每月事件显示
		var selectedDate = $("#dateSelect").val();
		eventsOnCalendar(selectedDate, id);
		return false;
	});
	
	/**
	 * Initialize delete all agenda items button
	 */
	$("#BtnDeleteAll").button();
	$("#BtnDeleteAll").click(function() {	
		jfcalplugin.deleteAllAgendaItems("#mycal");	
		return false;
	});		
	
	/**
	 * Initialize iCal test button
	 */
	$("#BtnICalTest").button();
	$("#BtnICalTest").click(function() {
		// Please note that in Google Chrome this will not work with a local file. Chrome prevents AJAX calls
		// from reading local files on disk.		
		jfcalplugin.loadICalSource("#mycal",$("#iCalSource").val(),"html");	
		return false;
	});	


	/**
	 * Initialize add event modal form
	 */
	$("#add-event-form").dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {
		
			'取消': function() {
				$(this).dialog('close');
			},

			'删除': function() {
				var selectedDate = $("#dateSelect").val();
				var originalSid = $('#add-event-form').data("para");
				var cookieSid = getCookie("sid");

				if(confirm("确定删除此选题？")){
					if(clickAgendaItem != null){
						$.ajax({
							url : "process.php",
							type : "POST",
							data : {
								action : "delete",
								delId : clickAgendaItem.data.myNum,
								cookieSid : cookieSid,
								etimeInPhp : clickAgendaItem.data.etimeInPhp,
								originalSid : originalSid,
								clickDateNoHyphen : clickDateNoHyphen,
								id : id
							},
							dataType : "json",
							encode : true,
							
							success : function(delEvent) {
								if(delEvent.action) {
									eventsOnCalendar(selectedDate, id);
								} else {
									alert(delEvent.msg);
									document.location.href = "calendar.php?id="+id;
								}
							}
						});
					}
					$(this).dialog('close');
				}
			},

			'更新': function() {
				var what = jQuery.trim($("#what").val());
				var contents = jQuery.trim($("#contents").val());
				var originalSid = $('#add-event-form').data("para");
				var cookieSid = getCookie("sid");
				var editorId = $("#editor").val();

				var selectedDate = $("#dateSelect").val();
				var startDate = $("#startDate").val();
				var startDateNoHyphen = startDate.replace(/-/g, "");

				var today = new Date();
				var todayDate = today.getDate()<10 ? "0"+today.getDate() : today.getDate();
				var todayMonth = (today.getMonth()+1)<10 ? "0"+(today.getMonth()+1) : (today.getMonth()+1);
				var todayYear = today.getFullYear();
				fullToday =  todayYear.toString() + todayMonth.toString() + todayDate.toString();  //点击更新时再即时算一遍fullToday的值，防止跨天。

				//var fColor = $("#colorForeground").val();
				//var bColor = $("#colorBackground").val();
			
				if(what == ""){
					alert("请填写“标题”。");
				}else if(contents == "") {
					alert("请填写“选题内容”。");
				} else if(!editorId) {
					alert("请选择“责任编辑”。");
				} else if(!$("#startDate").val().match(/\d{4}-\d{2}-\d{2}/)) {
					alert("日期格式必须为xxxx-xx-xx，其中x代表数字，位数不足时请在前面补0。");

				/*不可再更新历史选题
				} else if(parseInt(startDateNoHyphen) < parseInt(fullToday)) {
					alert("选题日期必须等于或晚于今天。");
				*/

				} else {
					//更新数据库事件
					$.ajax({
						type : "POST",
						url : "process.php",
						data : {
							action : "update",
							e_title : what,
							e_contents : contents,
							eventDate : startDate,
							e_id : clickMyNum,
							e_etime : phpTime(),  //提交时间，作为新的编辑时间，并转化为php时间。
							e_editor : editorId,  //editor下拉菜单的value
							clickEditorId : clickEditorId,  //点击事件的editor
							cookieSid : cookieSid,
							etimeInPhp : clickAgendaItem.data.etimeInPhp,
							originalSid : originalSid,
							clickDateNoHyphen : clickDateNoHyphen,
							startDateNoHyphen : startDateNoHyphen,
							id : id
						},
						dataType : "json",
						encode : true,

						success : function(update) {
							if(update.action) {
								//根据更新后的选题日期跳转到对应月份显示
								$("#dateSelect").val(startDate);

								$("#dateSelect").bind('change', function() {
									var selectedDate = $("#dateSelect").val();
									var dtArray = selectedDate.split("-");
									var year = dtArray[0];
									// jquery datepicker months start at 1 (1=January)		
									var month = dtArray[1];
									// strip any preceeding 0's		
									month = month.replace(/^[0]+/g,"")		
									var day = dtArray[2];
									// plugin uses 0-based months so we subtrac 1
									jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());

									//当日期选择栏发生变化时促发每月事件显示
									eventsOnCalendar(selectedDate, id);  //这个selectedDate是这个jquery里的，不是外面的那个。
								}).change();
							} else {
								alert(update.msg);
								document.location.href = "calendar.php?id="+id;
							}
						}
					});
					$(this).dialog('close');
				}
			},

			'添加': function() {
				var what = jQuery.trim($("#what").val());
				var contents = jQuery.trim($("#contents").val());
				
				var originalSid = $('#add-event-form').data("para");
				var cookieSid = getCookie("sid");
				var editorId = $("#editor").val();

				var selectedDate = $("#dateSelect").val();
				var startDate = $("#startDate").val(); 
				var startDateNoHyphen = startDate.replace(/-/g, "");

				var today = new Date();
				var todayDate = today.getDate()<10 ? "0"+today.getDate() : today.getDate();
				var todayMonth = (today.getMonth()+1)<10 ? "0"+(today.getMonth()+1) : (today.getMonth()+1);
				var todayYear = today.getFullYear();
				fullToday =  todayYear.toString() + todayMonth.toString() + todayDate.toString();
				
				//var fColor = $("#colorForeground").val();
				//var bColor = $("#colorBackground").val();
				
				if(cookieSid == sid1) {
					if(what == ""){
						alert("请填写“标题”。");
					} else if(contents == "") {
						alert("请填写“选题内容”。");
					} else if(!editorId) {
						alert("请选择“责任编辑”。");
					} else if(!$("#startDate").val().match(/\d{4}-\d{2}-\d{2}/)) {
						alert("日期格式必须为xxxx-xx-xx，其中x代表数字，位数不足时请在前面补0。");

					/*不可在历史日期中添加选题。此代码保留为
					} else if(parseInt(startDateNoHyphen) < parseInt(fullToday)) {
						alert("选题日期必须等于或晚于今天。");
					*/

					} else {
						//新建数据库事件。注意，从这步开始页面就已经不再在add-event-form中。
						$.ajax({
							type : "POST",
							url : "process.php",
							data : {
								action : "add",
								e_title : what,
								e_contents : contents,
								eventDate : startDate,
								e_editor : editorId,
								cookieSid : cookieSid,
								e_atime : phpTime(),
								originalSid : originalSid,
								startDateNoHyphen : startDateNoHyphen,
								id : id
							},
							dataType : "json",
							encode : true,
							
							success : function(addEvent) {
								if(addEvent.action) {
									$("#dateSelect").val(startDate);

									$("#dateSelect").bind('change', function() {
										var selectedDate = $("#dateSelect").val();
										var dtArray = selectedDate.split("-");
										var year = dtArray[0];
										// jquery datepicker months start at 1 (1=January)		
										var month = dtArray[1];
										// strip any preceeding 0's		
										month = month.replace(/^[0]+/g,"")		
										var day = dtArray[2];
										// plugin uses 0-based months so we subtrac 1
										jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());

										//当日期选择栏发生变化时促发每月事件显示
										eventsOnCalendar(selectedDate, id);
									}).change();
								} else {
									alert(addEvent.msg);
								}
							}
						});
						$(this).dialog('close');
					}
				} else {
					alert("用户已退出。");
					document.location.href = "oops.php";
				}
			}
		},

		//添加时的打开界面
		open: function(event, ui){
			$("#what").val(htmlDecode(clickTitle));  //打开时如果title有值则进行赋值
			$("#contents").val(htmlDecode(clickContents));  //打开时如果contents有值则进行赋值
			$("#editor").val(clickEditorId);

			var editorId = $("#editor").val();
			var sid = getCookie("sid");

			//取出传进来的登录用户权限值
			var userRight = parseInt($('#add-event-form').data("param"));
			var admin = $('#add-event-form').data("admin");
			
			//根据登录用户权限判断是否显示责任编辑下拉菜单
			if(userRight!=1 && !admin) {
				$("#editorSection").hide();
			} else {
				$("#editorSection").show();
			}

			//$("#tableDate").hide();  隐藏时段选项
			$("#tableColor").hide();  //隐藏颜色选项
			$(".gone").hide();

			// initialize start date picker
			$("#startDate").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: 'yy-mm-dd'
			});
			// initialize end date picker
			$("#endDate").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: 'yy-mm-dd'
			});
			// initialize with the date that was clicked
			$("#startDate").val(clickDate);
			$("#endDate").val(clickDate);
			// initialize color pickers
			$("#colorSelectorBackground").ColorPicker({
				color: "#333333",
				onShow: function (colpkr) {
					$(colpkr).css("z-index","10000");
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$("#colorSelectorBackground div").css("backgroundColor", "#" + hex);
					$("#colorBackground").val("#" + hex);
				}
			});
			//$("#colorBackground").val("#1040b0");		
			$("#colorSelectorForeground").ColorPicker({
				color: "#ffffff",
				onShow: function (colpkr) {
					$(colpkr).css("z-index","10000");
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$("#colorSelectorForeground div").css("backgroundColor", "#" + hex);
					$("#colorForeground").val("#" + hex);
				}
			});
			//$("#colorForeground").val("#ffffff");				
			// put focus on first form input element
			$("#what").focus();
		},

		close: function() {
			// reset form elements when we close so they are fresh when the dialog is opened again.
			$("#startDate").datepicker("destroy");
			$("#endDate").datepicker("destroy");
			$("#startDate").val("");
			$("#endDate").val("");
			$("#startHour option:eq(0)").attr("selected", "selected");
			$("#startMin option:eq(0)").attr("selected", "selected");
			$("#startMeridiem option:eq(0)").attr("selected", "selected");
			$("#endHour option:eq(0)").attr("selected", "selected");
			$("#endMin option:eq(0)").attr("selected", "selected");
			$("#endMeridiem option:eq(0)").attr("selected", "selected");			
			
			//恢复下面的几个按钮显示
			$(this).dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(1).show();
			$(this).dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(2).show();
			$(this).dialog('widget').find('.ui-dialog-buttonpane .ui-button').eq(3).show();
			
			//$("#colorBackground").val("#1040b0");
			//$("#colorForeground").val("#ffffff");

			clickTitle = "";  //退出时清空title的值
			clickContents = "";  //退出时清空contents的值
			clickEditorId = "2";  //退出时恢复到“待定”的状态。这里保持字符串状态，是和事件中的id信息格式保持一致，实际这个值是字符串还是数字，并不影响最后结果。
			clickDate = "";
			clickAgendaItem = "";
			clickMyNum = "";
			clickEditor = "";
			fullToday = "";
			clickDateNoHyphen = "";
		}
	});
	

	//startDate改endDate跟着一起改
	$("#startDate").change(function() {
		var forEndDate = $("#startDate").val();  //经过小日历改过的日期，格式中是带0的。
		$("#endDate").val(forEndDate);
	});


	/**
	 * Initialize display event form.
	 */
	$("#display-event-form").dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,

		buttons: {		
			'取消': function() {
				$(this).dialog('close');
			}
		},

		open: function(event, ui){
			if(clickAgendaItem != null){
				var delId = clickAgendaItem.data.myNum;
				var title = clickAgendaItem.title;

				$("#display-event-form").append(
					"<br><b>" + title + "</b><br><br>"		
				);		

				$("#display-event-form").append(
					"<b>责任编辑：</b>" + clickEditor + "<br>" + 
					"<b>日期：</b>" + clickDate + "<br><br><br>" +
					"<b>选题摘要：</b><br>" + nToBr(clickContents)
				);
			}		
		},

		close: function() {
			// clear agenda data
			$("#display-event-form").html("");
			clickTitle = "";  
			clickContents = "";  
			clickEditorId = "2";
			clickDate = "";
			clickAgendaItem = "";
			clickMyNum = "";
			clickEditor = "";
			fullToday = "";
			clickDateNoHyphen = "";
		}
	});	


	/**
	 * Initialize our tabs
	 */
	$("#tabs").tabs({
		/*
		 * Our calendar is initialized in a closed tab so we need to resize it when the example tab opens.
		 */
		show: function(event, ui){
			if(ui.index == 1){
				jfcalplugin.doResize("#mycal");
			}
		}	
	});
});


//获得某个cookie的值
function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");

		if (c_start!=-1) { 
			c_start=c_start + c_name.length+1; 
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;

			return unescape(document.cookie.substring(c_start,c_end));
		} 
	}
	return "";
}


//htmlspecialchars()_decode的jquery版
function htmlDecode(value) {
	if (value) {
		return $("<div/>").html(value).text();
	} else {
		return "";
	}
}


//转数据库中的换行\n为html页面上的br
function nToBr(value) {
	return value.replace(/\n/g, "<br>");
}