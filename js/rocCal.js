
var gCal ;
var tag_loc ; 
var gCal_m ;
var tag_loc_m ; 
var age_loc;

// 將選取的日期顯示到欄位上
function getROCDate() {
	var yy = gCal.fr.get_year.value ;
	var mm = gCal.fr.get_month.value ;
	var dd = gCal.fr.get_day.value ;
	tag_loc.value = yy + '-' + mm + '-' + dd ;


	if (age_loc) {
		var today = new Date() ;
		var this_yr = parseInt(today.getFullYear()) - 1911 ;	// 取今年
		var age;
		
		age = this_yr-yy;
		age_loc.value=age;
		// alert('QQ');

	}
	
	gCal.window.close() ;
}

// 主程式 showdate(form.id,check) check 判斷是否要計算屋齡
function showdate(loc,check) { 
	var today = new Date() ;
	var this_yr = parseInt(today.getFullYear()) - 1911 ;	// 取今年
	var this_mn = parseInt(today.getMonth()) + 1 ;			// 取本月
	var this_dy = parseInt(today.getDate()) ;				// 取本日
	
	var txt = '<html>' ;
	txt = txt + '<body bgcolor="#F8ECE9"><form name="fr">民國<select id="get_year" name="get_year" size="1">' ;
	tag_loc = loc ;
	age_loc = check;
	
	for (i = (this_yr+2) ; i > 0 ; i=i-1) {
		txt = txt + '<option value="'+i+'"' ;
		if (i==this_yr) {
			txt = txt + ' selected="selected"' ;
		}
		txt = txt + '>' + i + '</option>' ;
	}
	txt = txt + '</select>年<select id="get_month" name="get_month" size="1">' ;
	
	for (i = 1 ; i <= 12 ; i=i+1) {
		txt = txt + '<option value="' + i + '"' ;
		if (i==this_mn) {
			txt = txt + ' selected="selected"' ;
		}
		txt = txt + '>' + i + '</option>' ;
	}
	
	txt = txt + '</select>月<select id="get_day" name="get_day" size="1">' ;
	for (i = 1 ; i <= 31 ; i=i+1) {
		txt = txt + '<option value="' + i + '"' ;
		if (i==this_dy) {
			txt = txt + ' selected="selected"' ;
		}
		txt = txt + '>' + i + '</option>' ;
	}
	
	txt = txt + '</select>&nbsp;&nbsp;' ;
	txt = txt + '<input type="button" value="確定" onclick="javascript:window.opener.getROCDate()">&nbsp;&nbsp;' ;
	txt = txt + '<input type="button" value="關閉" onclick="javascript:window.close()">' ;
	txt = txt + '</body></html>' ;
	
	var newwin= window.open("","","width=350px,height=100px,scrollbars=yes,location=no,menubar=no,location=no,resizable=yes,top="+window.event.screenY+",left="+window.event.screenX) ;
	with (newwin.document) {
		open() ;
		write(txt) ;
		close() ;
	}
	gCal = newwin ;
}

// 將選取的年月顯示到欄位上
function getROCDate_m() {
	var yy = gCal_m.fr.get_year.value ;
	var mm = gCal_m.fr.get_month.value ;
	
	tag_loc_m.value = yy + '-' + mm ;
	
	gCal_m.window.close() ;
}

// 年月主程式 showdate(form.id)
function showdate_m(loc) {
	var today = new Date() ;
	var this_yr = parseInt(today.getFullYear()) - 1911 ;	// 取今年
	var this_mn = parseInt(today.getMonth()) + 1 ;			// 取本月
	var this_dy = parseInt(today.getDate()) ;				// 取本日
	var b_year = this_yr + 2 ;
	
	var txt = '<html>' ;
	txt = txt + '<body bgcolor="#F8ECE9"><form name="fr">民國<select id="get_year" name="get_year" size="1">' ;
	tag_loc_m = loc ;
	
	for (i = b_year ; i > (this_yr-100) ; i=i-1) {
		txt = txt + '<option value="'+i+'"' ;
		if (i==this_yr) {
			txt = txt + ' selected="selected"' ;
		}
		txt = txt + '>' + i + '</option>' ;
	}
	txt = txt + '</select>年<select id="get_month" name="get_month" size="1">' ;
	
	for (i = 1 ; i <= 12 ; i=i+1) {
		txt = txt + '<option value="' + i + '"' ;
		if (i==this_mn) {
			txt = txt + ' selected="selected"' ;
		}
		txt = txt + '>' + i + '</option>' ;
	}
	
	txt = txt + '</select>月' ;
	
	txt = txt + '</select>&nbsp;&nbsp;' ;
	txt = txt + '<input type="button" value="確定" onclick="javascript:window.opener.getROCDate_m()">&nbsp;&nbsp;' ;
	txt = txt + '<input type="button" value="關閉" onclick="javascript:window.close()">' ;
	txt = txt + '</body></html>' ;
	
	var newwin= window.open("","","width=350px,height=100px,status=yes,scrollbars=yes,location=no,menubar=no,location=no,resizable=yes,top="+window.event.screenY+",left="+window.event.screenX) ;
	with (newwin.document) {
		open() ;
		write(txt) ;
		close() ;
	}
	gCal_m = newwin ;
}
