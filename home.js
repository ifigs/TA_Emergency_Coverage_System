function initialization() {
	var code = localStorage.getItem('session_code');
	if(code == null){
		window.location.href = 'login.html';
	}
}

$( function() {
	$("#datepicker").datepicker();
	$("#datepicker2").datepicker();
} );

var offset = 0;
function next_button() {
	offset++;
	weekly_dates();
}

function back_button() {
	offset--;
	weekly_dates();
}

function current_button() {
	offset=0;
	weekly_dates();
}

function weekly_dates() {
	var today = new Date();
	var dates = [];
	for (i = 0; i < 7; i++) {
		var day = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay()+i+7*offset);
		document.getElementById(i).innerHTML = day.getMonth()+1 + "/" + day.getDate() + "/" + day.getFullYear();
		dates.push(('0' + (day.getMonth()+1)).slice(-2) + "/" + ('0' + day.getDate()).slice(-2) + "/" + day.getFullYear());
	}
	for (i = 7; i < 14; i++) {
		var day = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today.getDay()+i+7*offset);
		document.getElementById(i).innerHTML = day.getMonth()+1 + "/" + day.getDate();
	}
	refreshDates(dates);
}

function logout() {
	localStorage.removeItem("session_code");
	window.location.href = 'login.html';
}

window.onclick = function(event) {
	var evform = document.getElementById('add_event');
	var rep = document.getElementById('replacements');
	if (event.target == evform) {
		evform.style.display = "none";
	}
	if (event.target == rep) {
		removeReplacements();
	}
}

function validate() {
	var start_hour = parseInt(document.getElementById("start_hour").value, 10);
	var end_hour = parseInt(document.getElementById("end_hour").value, 10);
	var start_min = parseInt(document.getElementById("start_min").value, 10);
	var end_min = parseInt(document.getElementById("end_min").value, 10);
	var start = start_hour + start_min*1.0/60;
	var end = end_hour + end_min*1.0/60;
	if (start >= end) {
		alert("start time of an event cannot be at or after its end time");
		return false;
	}
	return true;
}

function create_event_box(evdata) {
	var edate = evdata.date;
	var evdate = edate.replace(/\b0(?=\d)/g, '');
	var evname = evdata.title;
	var starthour = parseInt(evdata.start_hour, 10);
	var startmin = parseInt(evdata.start_min, 10);
	var endhour = parseInt(evdata.end_hour, 10);
	var endmin = parseInt(evdata.end_min, 10);
	var min = endmin - startmin;
	var length = endhour - starthour + (min)*1.0/60;
	var department = "";

	var evtype = evdata.ta_event;
	if (evtype == "yes") {
		evtype = " ta_event";
		department = evdata.department;
	} else {
		evtype = "";
	}

	var id = evname.replace(/\s/g, '') + evdate.replace(/\//g, '') + length.toString() + starthour.toString() + startmin.toString();

	var w = parseInt(window.getComputedStyle(document.getElementById('14'),null).getPropertyValue('width'));
	w = w + 13;

	var current_week = []
	for (i=0; i < 7; i++) {
		current_week.push(document.getElementById(i).innerHTML);
	}
	var day = current_week.indexOf(evdate);

	if (day >= 0) {
		var box = document.createElement('div');
		box.setAttribute('data-date', edate);
		box.setAttribute('data-starthour', starthour);
		box.setAttribute('data-startmin', startmin);
		box.setAttribute('data-endhour', endhour);
		box.setAttribute('data-endmin', endmin);
		box.setAttribute('data-department', department);

		length = length * 43 - 1;
		var off = starthour*43 + startmin*1.0/60*42 + 8;
		if (starthour < 6)
			starthour += 7;
		else
			starthour -= 5;
		if (endhour < 6)
			endhour += 7;
		else
			endhour -= 5;
		if (startmin < 10)
			startmin = "0" + startmin;
		if (endmin < 10)
			endmin = "0" + endmin;
		day += 14;

		box.id = id;
		box.className = 'ebox' + evtype;
		box.style.height = length + 'px';
		box.style.width = w + 'px';
		box.style.transform = 'translate(-4.5px, ' + off + 'px)';

		var box_text = document.createElement('p');
		box_text.className = 'etext';

		var name_text = document.createTextNode(evname)
		var br = document.createElement('br');
		var time_text = document.createTextNode(starthour + ':' + startmin + ' - ' + endhour + ':' + endmin);

		box_text.appendChild(name_text);
		box_text.appendChild(br);
		box_text.appendChild(time_text);
		box.appendChild(box_text);
		document.getElementById(day).appendChild(box);
	}
}

var contextTarget;

$(document).ready(function() {
	$("#event_form").submit(function(event) {
		var flag = validate();
		if (flag == true) {
			event.preventDefault();
			// var eventData = JSON.stringify($("#event_form").serializeArray());
			// var evdata = $("#event_form").serializeArray();
			var evdata = {};
			for (var i = 0; i < 6; i++) {
				evdata[$("#event_form")[0][i].name] = $("#event_form")[0][i].value;
			}
			evdata["ta_event"] = $("form input[type=radio]:checked").val();
			if (evdata["ta_event"] == 'yes') {
				evdata["department"] = $("#event_form")[0][8].value.toUpperCase();
			}
			var stfy = JSON.stringify(evdata);
			var eventData = {username:localStorage.getItem('username'), jsondata:stfy};
			$.ajax({
				url: "event.php",
				type: "POST",
				data: eventData,
				success: function() {
					document.getElementById('add_event').style.display = "none";
					document.getElementById("event_form").reset()
					create_event_box(evdata);
				},
				failure: function(d) {
					console.log(d);
				}
			});
			return false;
		}
		else {
			return flag;
		}
	});

	$("th").on("click", ".ebox", function(event) {
		event.stopPropagation();
		$(".selected").removeClass("selected");
		$(this).addClass("selected");
		$(".right-click-menu").hide(50);
		if (this.classList.contains("ta_event")) {
			$(".right-click-menu.ta_event").finish().show(50).css({
				top: event.pageY + "px",
				left: event.pageX + "px"
			});
		}
		else {
			$(".right-click-menu.busy_event").finish().show(50).css({
				top: event.pageY + "px",
				left: event.pageX + "px"
			});
		}
		contextTarget = this;
	});

	$("th").on("contextmenu", ".ebox", function(event) {
		event.preventDefault();
		event.stopPropagation(); // not working in firefox for some reason...
		$(".selected").removeClass("selected");
		$(this).addClass("selected");
		$(".right-click-menu").hide(50);
		if (this.classList.contains("ta_event")) {
			$(".right-click-menu.ta_event").finish().show(50).css({
				top: event.pageY + "px",
				left: event.pageX + "px"
			});
		}
		else {
			$(".right-click-menu.busy_event").finish().show(50).css({
				top: event.pageY + "px",
				left: event.pageX + "px"
			});
		}
		contextTarget = this;
	});

	$(document).click(function(event) {
		if ((event.target.tagName != "DIV") && (event.target.tagName != "P")) {
			$(".selected").removeClass("selected");
			$(".right-click-menu").hide(50);
		}
	});

	$(".right-click-menu li").click(function(event){
		switch($(this).attr("data-action")) {
			case "delete_event":
				delete_event();
				break;
			case "find_replacement":
				find_replacement();
				break;
		}
		$(".right-click-menu").hide(50);
	});

	$('#start_hour').change(function() {
		if (parseInt($(this).val()) <= 4) {
			document.getElementById("start_meridiem").innerHTML = "am";
		}
		else {
			document.getElementById("start_meridiem").innerHTML = "pm";
		}
	});

	$('#end_hour').change(function() {
		if (parseInt($(this).val()) <= 4) {
			document.getElementById("end_meridiem").innerHTML = "am";
		}
		else {
			document.getElementById("end_meridiem").innerHTML = "pm";
		}
	});

	$('#repeat').change(function() {
		if (this.checked) {
			$('#repeat_info :input').removeAttr('disabled');
			// document.getElementById('repeat_info').style.display='block';
		} else {
			$('#repeat_info :input').attr('disabled','');
			// document.getElementById('repeat_info').style.display='';
		}
	});

	$('form input[type=radio][name=ta_event]').change(function() {
		if ($('form input[type=radio]:checked').val() == 'yes') {
			$('#department').removeAttr('disabled');
		} else if ($("form input[type=radio]:checked").val() == 'no') {
			$('#department').attr('disabled','');
		}
	});
});


function find_replacement() {
	$.ajax({
		url: "find_replacement.php",
		type: "GET",
		data: {username:localStorage.getItem('username')},
		// ^ ADD DEPARTMENT HERE
		success: function(files) {
			document.getElementById("replacements").style.display= 'block';
			document.body.style.overflow = "hidden";
			var subs = document.createElement('div');
			subs.id = 'subs';
			// subs.style.width = '95%';
			if (files.length == 0) {
				var text = document.createElement('p');
				text.innerHTML = "Sorry, there are no qualified TAs available to cover this class. Please contact your supervisor or professor.";
				subs.appendChild(text);
			} else {
				var mydate = contextTarget.dataset.date;
				var mydept = contextTarget.dataset.department.toUpperCase();
				var mystarthour = parseInt(contextTarget.dataset.starthour);
				var mystartmin = parseInt(contextTarget.dataset.startmin);
				var myendhour = parseInt(contextTarget.dataset.endhour);
				var myendmin = parseInt(contextTarget.dataset.endmin);
				var mystart = mystarthour + mystartmin*1.0/60;
				var myend = myendhour + myendmin*1.0/60;
				var avail = [];
				for (var i = 0; i < files.length; i++) {
					var conflict = false;
					$.ajax({
						url: files[i],
						type: 'GET',
						dataType: 'json',
						async: false,
						success: function(evs) {
							if (evs['departments'].includes(mydept)) {
								if (evs[mydate]) {
									for (var i = 0; i < evs[mydate].length; i++) {
										var edate = evs[mydate][i].date;
										if (mydate == edate) {
											var starthour = parseInt(evs[mydate][i].start_hour, 10);
											var startmin = parseInt(evs[mydate][i].start_min, 10);
											var endhour = parseInt(evs[mydate][i].end_hour, 10);
											var endmin = parseInt(evs[mydate][i].end_min, 10);
											var start = starthour + startmin*1.0/60;
											var end = endhour + endmin*1.0/60;
											if (start >= mystart && start < myend) {
												conflict = true;
											}
											if (end > mystart && end <= myend ) {
												conflict = true;
											}
											if (start <= mystart && end >= myendhour ) {
												conflict = true;
											}
										}
									}
								}
							} else {
								conflict = true;
							}
						}
					})
					if (!conflict) {
						var uname = files[i].replace('data/','');
						uname = uname.replace('.json','');
						avail.push(uname);
					}
				}
				if (avail.length == 0) {
					var text = document.createElement('p');
					text.innerHTML = "Sorry, there are no qualified TAs available to cover this class. Please contact your supervisor or professor.";
					subs.appendChild(text);
				} else {
					var jsonAvail = JSON.stringify(avail);
					$.ajax({
						url: 'contact_info.php',
						type: 'GET',
						data: {tas: jsonAvail},
						dataType: 'json',
						success: function(ta_info) {
							var d = document.createElement('div');
							d.style.maxHeight = '70vh';
							d.style.overflow = 'auto';
							var tab = document.createElement('table');
							tab.style.minWidth = '100%';
							tab.style.display = 'inline';
							var header = document.createElement('thead');
							var header_row = document.createElement('tr');
							var na = document.createElement('th');
							na.style.padding = '10px 10px 10px 10px';
							na.innerHTML = 'Name';
							var em = document.createElement('th');
							em.style.padding = '10px 10px 10px 10px';
							em.innerHTML = 'Email';
							var ph = document.createElement('th');
							ph.style.padding = '10px 10px 10px 10px';
							ph.innerHTML = 'Phone Number';
							var bod = document.createElement('tbody');
							for (ta in ta_info) {
								var row = document.createElement('tr');
								var n = document.createElement('td');
								n.style.padding = '10px 10px 10px 10px';
								n.style.border = 'none';
								n.innerHTML = ta_info[ta][0] + ' ' + ta_info[ta][1];
								var e = document.createElement('td');
								e.style.padding = '10px 10px 10px 10px';
								e.style.border = 'none';
								e.style.borderLeft = '1px solid #9E9EFF';
								e.style.borderRight = '1px solid #9E9EFF';
								e.innerHTML = ta_info[ta][2];
								var p = document.createElement('td');
								p.style.padding = '10px 10px 10px 10px';
								p.style.border = 'none';
								p.innerHTML = ta_info[ta][3];
								row.appendChild(n);
								row.appendChild(e);
								row.appendChild(p);
								bod.appendChild(row);
							}

							header_row.appendChild(na);
							header_row.appendChild(em);
							header_row.appendChild(ph);
							header.appendChild(header_row);
							tab.appendChild(header);
							tab.appendChild(bod);
							d.appendChild(tab);
							subs.appendChild(d);
						},
						failure: function(e) {
							console.log(e);
						}
					})
				}
			}
			document.getElementById('replacements_content').appendChild(subs);
		},
		failure: function(e) {
			console.log(e);
		},
		dataType: "json"
	});
}

function removeReplacements() {
	document.getElementById('replacements').style.display='none';
	document.body.style.overflow = "visible";
	var del = document.getElementById('subs');
	del.parentNode.removeChild(del);
}

function delete_event() {
	var mydate = contextTarget.dataset.date;
	contextTarget.parentNode.removeChild(contextTarget);
	$(".selected").removeClass("selected");
	$(".right-click-menu").hide(50);
	$.getJSON('data/' + localStorage.getItem('username') + '.json', function(data) {
		for (var i = 0; i < data[mydate].length; i++) {
			var evdate = data[mydate][i].date;
			evdate = evdate.replace(/\b0(?=\d)/g, '');
			var evname = data[mydate][i].title;
			var starthour = parseInt(data[mydate][i].start_hour, 10);
			var startmin = parseInt(data[mydate][i].start_min, 10);
			var endhour = parseInt(data[mydate][i].end_hour, 10);
			var endmin = parseInt(data[mydate][i].end_min, 10);
			var min = endmin - startmin;
			var length = endhour - starthour + min*1.0/60;
			var iden = evname.replace(/\s/g, '') + evdate.replace(/\//g, '') + length.toString() + starthour.toString() + startmin.toString();
			if (contextTarget.id == iden) {
				data[mydate].splice(i, 1);
				if (data[mydate].length == 0) {
					delete data[mydate];
				}
				break;
			}
		}
		var myjson = JSON.stringify(data);
		var postData = {username:localStorage.getItem('username'), jsondata:myjson};
		$.ajax({
			url: "remove_event.php",
			type: "POST",
			data: postData,
			failure: function(e) {
				console.log(e);
			}
		});
	})  
}

window.onresize = function() {
	var w = parseInt(window.getComputedStyle(document.getElementById('14'),null).getPropertyValue('width'));
	w = w + 13;
	var els = document.getElementsByClassName('ebox');
	for (var i = 0; i < els.length; i++) {
		els[i].style.width = w + 'px';
	}
}

window.onload = function() {
	initialization();
	document.getElementById('fname_welcome').innerHTML = localStorage.getItem('username') + '.';
	weekly_dates();
}

function refreshDates(dates) {
	$.getJSON('data/' + localStorage.getItem('username') + '.json', function(data) {
	console.log(data);
	var z = document.getElementsByClassName('ebox');
	while (z[0]) {
		z[0].parentNode.removeChild(z[0]);
	}
	for (var i = 0; i < dates.length; i++) {
		if (data[dates[i]]) {
			for (var j = 0; j < data[dates[i]].length; j++) {
				create_event_box(data[dates[i]][j]);
			}
		}
	}
})}
