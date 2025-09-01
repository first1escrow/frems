/* 檢核輸入證號 */
function checkUID(sn) {
	let result = false ;

	if (sn.length == 8) {			//檢查統一編號
		result = UNID(sn);
	} else if (sn.length == 10) {
		sn = sn.toUpperCase();		//將英文字母設定為大寫

        let reg = /^[A-Z]{1}[A-D]{1}[0-9]{8}$/ ;//檢查是否為滿183天格式(居留證字號)
        let reg1 = /^[0-9]{8}[A-Z]{1}[A-Z]{1}$/;

		if (reg.test(sn)) {			//檢查居留證字號
			result = RID(sn);
		} else if(reg1.test(sn)) {//未住滿183天的非大陸民眾(第一~八碼採護照西元年出生日;第九~十碼彩護照內英文姓名第一個字之前2個字母)
			result = true;
		} else {						//檢查身分證字號
			result = PID(sn) ;
		}
	} else if (sn.length == 7) {//未住滿183天的大陸民眾證號為 "9" + 西元出生年後2碼 + 月2碼 + 日2碼 = 7碼)
		let reg2 = /^9[0-9]{6}$/;

		if (reg2.test(sn)) {
			result = true;
		}
	}
	
	return result ;
}
////

/* 統一編號檢核 */
function UNID(sn) {
	var cx = new Array(1,2,1,2,1,2,4,1) ;		//驗算基數
	var sum = 0 ;
	if (sn.length != 8) {
		//alert("統編錯誤，要有 8 個數字");
		return false ;
	}
	
	var cnum = sn.split("") ;
	for (i = 0 ; i <= 7 ; i ++) {
		if (sn.charCodeAt() < 48 || sn.charCodeAt() > 57) {
			//alert("統編錯誤，要有 8 個 0-9 數字組合");
			return false ;
		}
		sum += cc(cnum[i] * cx[i]) ;		//加總運算碼結果
	}
	
	if (sum % 5 == 0) {
		//alert("統一編號："+sn+" 正確!");
		return true ;
	}
	else if (cnum[6] == 7 && (sum + 1) % 10 == 0) {
		//alert("統一編號："+sn+" 正確!");
		return true ;
	}
	else {
		//alert("統一編號："+sn+" 錯誤!");
		return false ;
	}
}
////

/* 計算數字大於 10 之處理 */
function cc(n){
  if (n > 9) {
    var s = n + "";
    n1 = s.substring(0,1) * 1;
    n2 = s.substring(1,2) * 1;
    n = n1 + n2;
  }
  return n;
}
////

/* 身份證字號檢核 */
function PID(sn) {	
	/* 定義字母對應的數字 */
	var a = new Array ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z') ;
	var b = new Array ('10','11','12','13','14','15','16','17','34','18','19','20','21','22','35','23','24','25','26','27','28','29','32','30','31','33') ;
	var max = a.length ;
	var alphabet = new Array() ;
	for (var i = 0 ; i < max ; i ++) {
		alphabet[i] = new Array(a[i],b[i]) ;
	}
	////
	
	sn = sn.toUpperCase() ;			//將英文字母設定為大寫
	var snLen = sn.length ;			//計算字數長度
	
	/* 若號碼長度不等於10，代表輸入長度不合格式 */
	if (snLen != 10) {
		//alert('輸入字號長度不正確!!') ;
		return false ;
	}
	////
	
	/* 取出第一個英文字母 */
	var ch = sn.substr(0,1) ;
	var chVal = '' ;
	for (var i = 0 ; i < max ; i ++) {
		if (alphabet[i][0] == ch) {
			chVal = alphabet[i][1] ;
			break ;
		}
	}
	////
	
	/* 取出檢查碼 */
	var lastch = sn.substr(-1,1) ;
	////
	
	var ch1 = parseInt(chVal.substr(0,1)) ;		//十位數
	var ch2 = parseInt(chVal.substr(1,1)) ;		//個位數
	
	var _val = (ch2 * 9) + ch1 ;			//個位數 x 9 再加上十位數
	_val = _val % 10 ;						//除以10取餘數
	
	/* 計算檢核碼 */
	var t1 = parseInt(_val) * 1 ;
	var t2 = parseInt(sn.substr(1,1)) * 8 ;
	var t3 = parseInt(sn.substr(2,1)) * 7 ;
	var t4 = parseInt(sn.substr(3,1)) * 6 ;
	var t5 = parseInt(sn.substr(4,1)) * 5 ;
	var t6 = parseInt(sn.substr(5,1)) * 4 ;
	var t7 = parseInt(sn.substr(6,1)) * 3 ;
	var t8 = parseInt(sn.substr(7,1)) * 2 ;
	var t9 = parseInt(sn.substr(8,1)) * 1 ;
	
	var checkCode = (t1 + t2 + t3 + t4 + t5 + t6 + t7 + t8 + t9) % 10 ;
	if (checkCode == 0) {
		checkCode = 0 ;
	}
	else {
		checkCode = 10 - checkCode ;			//檢查碼
	}
	////
	
	/* 比對檢核碼是否相符 */
	if (checkCode == lastch) {
		//alert('checkCode=' + checkCode) ;
		return true ;
	}
	else {
		//alert('checkCode<>' + checkCode) ;
		return false ;
	}
	////
}
////

/* 居留證字號檢核 */
function RID(sn) {	
	/* 定義字母對應的數字 */
	var a = new Array ('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z') ;
	var b = new Array ('10','11','12','13','14','15','16','17','34','18','19','20','21','22','35','23','24','25','26','27','28','29','32','30','31','33') ;
	var max = a.length ;
	var alphabet = new Array() ;
	for (var i = 0 ; i < max ; i ++) {
		alphabet[i] = new Array(a[i],b[i]) ;
	}
	////
	
	sn = sn.toUpperCase() ;			//將英文字母設定為大寫
	var snLen = sn.length ;			//計算字數長度
	
	/* 若號碼長度不等於10，代表輸入長度不合格式 */
	if (snLen != 10) {
		//alert('輸入字號長度不正確!!') ;
		return false ;
	}
	////
	
	/* 取出英文字母 */
	var ch1 = sn.substr(0,1) ;		//
	var ch2 = sn.substr(1,1) ;		//
	var chVal1 = '' ;
	var chVal2 = '' ;
	for (var i = 0 ; i < max ; i ++) {
		/* 取出第一個英文字母對應的數值 */
		if (alphabet[i][0] == ch1) {
			chVal1 = alphabet[i][1] ;
			//break ;
		}
		////
		
		/* 取出第二個英文字母對應的數值 */
		if (alphabet[i][0] == ch2) {
			chVal2 = alphabet[i][1] ;
			//break ;
		}
		////
	}
	////
	
	/* 取出檢查碼 */
	var lastch = sn.substr(-1,1) ;
	////
	
	/* 第一碼英文字的轉換 */
	var ch1 = parseInt(chVal1.substr(0,1)) ;		//十位數
	var ch2 = parseInt(chVal1.substr(1,1)) ;		//個位數
	var t0 = (ch2 * 9 + ch1) % 10 ;
	////
	
	/* 第二碼英文字的轉換 */
	var _val = parseInt(chVal2.substr(-1,1)) * 1 ;	//個位數
	////
	
	/* 計算檢核碼 */
	var t1 = parseInt(_val) * 8 ; ;
	var t2 = parseInt(sn.substr(2,1)) * 7 ;
	var t3 = parseInt(sn.substr(3,1)) * 6 ;
	var t4 = parseInt(sn.substr(4,1)) * 5 ;
	var t5 = parseInt(sn.substr(5,1)) * 4 ;
	var t6 = parseInt(sn.substr(6,1)) * 3 ;
	var t7 = parseInt(sn.substr(7,1)) * 2 ;
	var t8 = parseInt(sn.substr(8,1)) * 1 ;
	
	var checkCode = t0 + t1 + t2 + t3 + t4 + t5 + t6 + t7 + t8 + 1 - 1 ;
	// checkCode = 10 - (checkCode % 10) ;

	if ((checkCode % 10) == 0) {
		checkCode = 0;
	}else{
		checkCode = 10 - (checkCode % 10) ;
	}
	
	/* 比對檢核碼是否相符 */
	if (checkCode == lastch) {
		return true ;
	}
	else {
		return false ;
	}
	////
}
////