
function formatDate(fields) {
	var ans = '' ;
	//var patt = /^\D+$/ ;
	
	var tag = $('#' + fields + 'Y') ;
	var a = tag.val() ;
	//if (patt.test(a)) ans = 'f' ;
	
	var tag = $('#' + fields + 'M') ;
	var b = tag.val() ;
	//if (patt.test(b)) ans = 'f' ;
	
	var tag = $('#' + fields + 'D') ;
	var c = tag.val() ;
	//if (patt.test(c)) ans = 'f' ;
	
	if ((a != '') && (b != '') && (c != '') && (ans == '')) ans = a + '-' + b + '-' + c ;
	else if ((a == '') && (b == '') && (c == '')) ans = 'e' ;
	else ans = 'f' ;
	
	return ans ;
}
