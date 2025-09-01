<?php
//紀錄來源 IP
$ips = $_SERVER["REMOTE_ADDR"] ;

if (preg_match("/^192.168.1/",$ips)) {
	echo '僅限內部人員使用!!' ;
	exit ;
}
##

//取得仲介店全名
Function getRealty($cn,$cid) {
	$realty = '' ;
	
	$sql = 'SELECT * FROM  tContractRealestate WHERE cCertifyId="'.$cid.'";' ;
	
	$rs1 = $cn->Execute($sql) ;
	while (!$rs1->EOF) {
		$bid1 = $rs1->fields['cBranchNum'] ;
		$bid2 = $rs1->fields['cBranchNum1'] ;
		$bid3 = $rs1->fields['cBranchNum2'] ;
		
		if ($bid1 > 0) {
			$realty .= ','.getBranch($cn,$bid1) ;
		}
		
		if ($bid2  > 0) {
			$realty .= ','.getBranch($cn,$bid2) ;
		}
		
		if ($bid3 > 0) {
			$realty .= ','.getBranch($cn,$bid3) ;
		}
		
		$rs1->MoveNext() ;
	}
	$realty = preg_replace("/^,/","",$realty) ;
	
	return $realty ;
}

Function getBranch($lnk,$id) {
	$branch = '' ;
	$sql = 'SELECT * FROM tBranch WHERE bId="'.$id.'";' ;
	$_rs = $lnk->Execute($sql) ;
	$branch = $_rs->fields['bStore'] ;
	
	return $branch ;
}
##

//取得地址
Function getAddr($cn,$cid) {
	$addr = '' ;
	$sql = '
		SELECT
			*,
			(SELECT zCity FROM tZipArea WHERE zZip=a.cZip) as city,
			(SELECT zArea FROM tZipArea WHERE zZip=a.cZip) as area
		FROM
			tContractProperty AS a
		WHERE
			cCertifiedId="'.$cid.'";
	' ;
	$rs2 = $cn->Execute($sql) ;
	
	$addr = $rs2->fields['cAddr'] ;
	$city = $rs2->fields['city'] ;
	$area = $rs2->fields['area'] ;
	
	$addr = preg_replace("/$city/","",$addr) ;
	$addr = preg_replace("/$area/","",$addr) ;
	$addr = $city.$area.$addr ;
	
	return $addr ;
}
##
//連結資料庫
include_once 'openadodb.php' ;
##

//取得輸入參數
$search = $_POST['search'] ;
$bId = $_POST['bId'] ;		//買方身分證字號
$oId = $_POST['oId'] ;		//賣方身分證字號
##

$tbl = '' ;
$notMatch = '' ;

//進行搜尋
if ($search == 'ok') {
	//比對買方身分證
	if ($bId) {
		$bId = strtoupper($bId) ;
		
		$sql = '
			SELECT
				*
			FROM
				tContractBuyer
			WHERE
				cIdentifyId="'.$bId.'" ;
		' ;
		$rs = $conn->Execute($sql) ;
		
		$i = 0 ;
		while (!$rs->EOF) {
		//if ($rs->RecordCount() > 0) {
			$buyer[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'] ;
			$buyer[$i]['cIdentifyId'] = $rs->fields['cIdentifyId'] ;
			$buyer[$i]['cName'] = $rs->fields['cName'] ;
			
			$buyer[$i]['Realty'] = getRealty($conn,$buyer[$i]['cCertifiedId']) ;
			$buyer[$i]['Addr'] = getAddr($conn,$buyer[$i]['cCertifiedId']) ;
			$buyer[$i]['yn'] = 'Ｏ' ;
			
			$i ++ ;
			$rs->MoveNext() ;
		}
		
		$sql = '
			SELECT
				*
			FROM
				tContractOthers
			WHERE
				cIdentity="1"
				AND cIdentifyId="'.$bId.'" ;
		' ;
		
		$rs = $conn->Execute($sql) ;
		
		while (!$rs->EOF) {
		//if ($rs->RecordCount() > 0) {
			$buyer[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'] ;
			$buyer[$i]['cIdentifyId'] = $rs->fields['cIdentifyId'] ;
			$buyer[$i]['cName'] = $rs->fields['cName'] ;
			
			$buyer[$i]['Realty'] = getRealty($conn,$buyer[$i]['cCertifiedId']) ;
			$buyer[$i]['Addr'] = getAddr($conn,$buyer[$i]['cCertifiedId']) ;
			$buyer[$i]['yn'] = 'Ｏ' ;
			
			$i ++ ;
			$rs->MoveNext() ;
		}
		
		for ($i = 0 ; $i < count($buyer) ; $i ++) {
			if (preg_match("/^[0-9]{9}$/",$buyer[$i]['cCertifiedId'])) {
				$sql = 'SELECT * FROM tContractCase WHERE cCertifiedId="'.$buyer[$i]['cCertifiedId'].'";' ;
				$rs = $conn->Execute($sql) ;
				$buyer[$i]['cApplyDate'] = substr($rs->fields['cApplyDate'],0,10) ;
				$buyer[$i]['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
			}
		}
	}
	##
	//print_r($buyer) ; exit ;
	
	//比對賣方身分證
	if ($oId) {
		$oId = strtoupper($oId) ;
		
		$sql = '
			SELECT
				*
			FROM
				tContractOwner
			WHERE
				cIdentifyId="'.$oId.'" ;
		' ;
		
		$rs = $conn->Execute($sql) ;
		
		$i = 0 ;
		while (!$rs->EOF) {
			$owner[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'] ;
			$owner[$i]['cIdentifyId'] = $rs->fields['cIdentifyId'] ;
			$owner[$i]['cName'] = $rs->fields['cName'] ;
			
			$owner[$i]['Realty'] = getRealty($conn,$owner[$i]['cCertifiedId']) ;
			$owner[$i]['Addr'] = getAddr($conn,$owner[$i]['cCertifiedId']) ;
			$owner[$i]['yn'] = 'Ｏ' ;
			
			$i ++ ;
			$rs->MoveNext() ;
		}
		
		$sql = '
			SELECT
				*
			FROM
				tContractOthers
			WHERE
				cIdentity="2"
				AND cIdentifyId="'.$oId.'" ;
		' ;
		
		$rs = $conn->Execute($sql) ;
		
		while (!$rs->EOF) {
			$owner[$i]['cCertifiedId'] = $rs->fields['cCertifiedId'] ;
			$owner[$i]['cIdentifyId'] = $rs->fields['cIdentifyId'] ;
			$owner[$i]['cName'] = $rs->fields['cName'] ;
			
			$owner[$i]['Realty'] = getRealty($conn,$owner[$i]['cCertifiedId']) ;
			$owner[$i]['Addr'] = getAddr($conn,$owner[$i]['cCertifiedId']) ;
			$owner[$i]['yn'] = 'Ｏ' ;
			
			$i ++ ;
			$rs->MoveNext() ;
		}
		
		for ($i = 0 ; $i < count($owner) ; $i ++) {
			if (preg_match("/^[0-9]{9}$/",$owner[$i]['cCertifiedId'])) {
				$sql = 'SELECT * FROM tContractCase WHERE cCertifiedId="'.$owner[$i]['cCertifiedId'].'";' ;
				$rs = $conn->Execute($sql) ;
				$owner[$i]['cApplyDate'] = substr($rs->fields['cApplyDate'],0,10) ;
				$owner[$i]['cSignDate'] = substr($rs->fields['cSignDate'],0,10) ;
			}
		}
	}
	##
	
	$a = '買' ;
	$b = '賣' ;
	$datatable = 'tContractOwner' ;
	if (count($buyer) < count($owner)) {
		$a = '賣' ;
		$b = '買' ;
		
		if (count($buyer) <= 0) {
			$buyer = array() ;
		}
		
		$arr = array_merge($buyer) ;
		$buyer = array() ;
		$buyer = array_merge($owner) ;
		$owner = array() ;
		$owner = array_merge($arr) ;
		
		$datatable = 'tContractBuyer' ;
		unset($arr) ;
	}
	
	$ans = '' ;
	if ((count($buyer) == 0) && (count($owner) == 0)) {
		$ans = '<h2 style="color:red;">第一建經查無此資料!!</h2>' ;
	}
	
	for ($i = 0 ; $i < count($buyer) ; $i ++) {
		$fg = 0 ;
		
		for ($j = 0 ; $j < count($owner) ; $j ++) {
			if ($buyer[$i]['cCertifiedId'] == $owner[$j]['cCertifiedId']) {
				$owner[$j]['yn'] = 'Ｏ' ;
			}
			else {
				$owner[$j]['yn'] = 'Ｘ' ;
				$fg ++ ;
			}
		}
		
		if (count($owner) == 0) {
			//echo 'first' ;
			$sql = 'SELECT * FROM '.$datatable.' WHERE cCertifiedId="'.$buyer[$i]['cCertifiedId'].'";' ;
			$rs = $conn->Execute($sql) ;
			if ($rs->RecordCount() <= 0) {
				if ($database == 'tContractOwner') {
					$database = '2' ;
				}
				else {
					$database = '1' ;
				}
				$sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="'.$buyer[$i]['cCertifiedId'].'" AND cIdentity="'.$database.'";' ;
				$rs = $conn->Execute($sql) ;
			}
			
			$v = array() ;
			while (!$rs->EOF) {
				$v['cName'] .= $rs->fields['cName'].'&nbsp;' ;
				$v['cIdentifyId'] .= $rs->fields['cIdentifyId'].'&nbsp;' ;
				$v['Realty'] .= getRealty($conn,$buyer[$i]['cCertifiedId']).'&nbsp;' ;
				$v['Addr'] .= getAddr($conn,$buyer[$i]['cCertifiedId']).'&nbsp;' ;
				$v['cSignDate'] .= $buyer[$i]['cSignDate'].'&nbsp;' ;
				$v['cApplyDate'] .= $buyer[$i]['cApplyDate'].'&nbsp;' ;
				$v['cCertifiedId'] .= $buyer[$i]['cCertifiedId'].'&nbsp;' ;
				$rs->MoveNext() ;
			}
			
			$tbl .= '
				<table border="1" style="width:600px;">
					<tr>
						<td style="width:100px;">'.$i.'&nbsp;</td>
						<td style="width:250px;text-align:center;">'.$a.'方</td>
						<td style="width:250px;text-align:center;">'.$b.'方</td>
					</tr>
					<tr>
						<td style="font-size:10pt;">是否搜尋得到</td>
						<td style="text-align:center;">'.$buyer[$i]['yn'].'&nbsp;</td>
						<td style="text-align:center;"><span style="color:red;">(Ｘ)</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">姓名</td>
						<td>'.$buyer[$i]['cName'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['cName'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">身分證字號</td>
						<td>'.$buyer[$i]['cIdentifyId'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['cIdentifyId'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">仲介店家</td>
						<td>'.$buyer[$i]['Realty'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['Realty'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">標的物地址</td>
						<td>'.$buyer[$i]['Addr'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['Addr'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">簽約日期</td>
						<td>'.$buyer[$i]['cSignDate'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['cSignDate'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">建經建檔日期</td>
						<td>'.$buyer[$i]['cApplyDate'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['cApplyDate'].')</span></td>
					</tr>
					<tr>
						<td style="font-size:10pt;">建經保證號碼</td>
						<td>'.$buyer[$i]['cCertifiedId'].'&nbsp;</td>
						<td><span style="color:red;">('.$v['cCertifiedId'].')</span></td>
					</tr>
				</table>
			' ;
		}
		else if ($fg >= count($owner)) {		//完全無比對到的資料
			//echo 'second' ;
			foreach ($owner as $k => $v) {
				$tbl .= '
					<table border="1" style="width:600px;">
						<tr>
							<td style="width:100px;">'.$i.'&nbsp;</td>
							<td style="width:250px;text-align:center;">'.$a.'方</td>
							<td style="width:250px;text-align:center;">'.$b.'方</td>
						</tr>
						<tr>
							<td style="font-size:10pt;">是否搜尋得到</td>
							<td style="text-align:center;">'.$buyer[$i]['yn'].'&nbsp;</td>
							<td style="text-align:center;"><span style="color:red;">('.$v['yn'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">姓名</td>
							<td>'.$buyer[$i]['cName'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['cName'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">身分證字號</td>
							<td>'.$buyer[$i]['cIdentifyId'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['cIdentifyId'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">仲介店家</td>
							<td>'.$buyer[$i]['Realty'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['Realty'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">標的物地址</td>
							<td>'.$buyer[$i]['Addr'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['Addr'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">簽約日期</td>
							<td>'.$buyer[$i]['cSignDate'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['cSignDate'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">建經建檔日期</td>
							<td>'.$buyer[$i]['cApplyDate'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['cApplyDate'].')</span></td>
						</tr>
						<tr>
							<td style="font-size:10pt;">建經保證號碼</td>
							<td>'.$buyer[$i]['cCertifiedId'].'&nbsp;</td>
							<td><span style="color:red;">('.$v['cCertifiedId'].')</span></td>
						</tr>
					</table>
				' ;
			}
		}
		else {							//有比對到
			//echo 'third' ;
			foreach ($owner as $k => $v) {
				if ($v['yn'] == 'Ｏ') {
					$tbl .= '
						<table border="1" style="width:600px;">
							<tr>
								<td style="width:100px;">'.$i.'&nbsp;</td>
								<td style="width:250px;text-align:center;">'.$a.'方</td>
								<td style="width:250px;text-align:center;">'.$b.'方</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">是否搜尋得到</td>
								<td style="text-align:center;">'.$buyer[$i]['yn'].'&nbsp;</td>
								<td style="text-align:center;">'.$v['yn'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">姓名</td>
								<td>'.$buyer[$i]['cName'].'&nbsp;</td>
								<td>'.$v['cName'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">身分證字號</td>
								<td>'.$buyer[$i]['cIdentifyId'].'&nbsp;</td>
								<td>'.$v['cIdentifyId'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">仲介店家</td>
								<td>'.$buyer[$i]['Realty'].'&nbsp;</td>
								<td>'.$v['Realty'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">標的物地址</td>
								<td>'.$buyer[$i]['Addr'].'&nbsp;</td>
								<td>'.$v['Addr'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">簽約日期</td>
								<td>'.$buyer[$i]['cSignDate'].'&nbsp;</td>
								<td>'.$v['cSignDate'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">建經建檔日期</td>
								<td>'.$buyer[$i]['cApplyDate'].'&nbsp;</td>
								<td>'.$v['cApplyDate'].'&nbsp;</td>
							</tr>
							<tr>
								<td style="font-size:10pt;">建經保證號碼</td>
								<td>'.$buyer[$i]['cCertifiedId'].'&nbsp;</td>
								<td>'.$v['cCertifiedId'].'&nbsp;</td>
							</tr>
					
						</table>
					' ;
				}
			}
		}
		
	}
	##
}
##

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>建經案件查詢</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="/js/IDCheck.js"></script>
<script type="text/javascript">
$(function() {	
	//$('[name="oId"]').focus() ;
	//$('[name="oId"]').select() ;
	var oid = $('[name="oId"]').val().toUpperCase() ;
	if (checkUID(oid)) {
		$('#oIdImg').prop({'src':'/images/ok.png'}) ;
	}
	else {
		$('#oIdImg').prop({'src':'/images/ng.png'}) ;
	}
	
	var bid = $('[name="bId"]').val().toUpperCase() ;
	if (checkUID(bid)) {
		$('#bIdImg').prop({'src':'/images/ok.png'}) ;
	}
	else {
		$('#bIdImg').prop({'src':'/images/ng.png'}) ;
	}
	
	$('[name="oId"]').keyup(function() {
		var str = $('[name="oId"]').val().toUpperCase() ;
		$('[name="oId"]').val(str) ;
		
		if (checkUID(str)) {
			$('#oIdImg').prop({'src':'/images/ok.png'}) ;
		}
		else {
			$('#oIdImg').prop({'src':'/images/ng.png'}) ;
		}
	}) ;
	
	$('[name="bId"]').keyup(function() {
		var str = $('[name="bId"]').val().toUpperCase() ;
		$('[name="bId"]').val(str) ;
		
		if (checkUID(str)) {
			$('#bIdImg').prop({'src':'/images/ok.png'}) ;
		}
		else {
			$('#bIdImg').prop({'src':'/images/ng.png'}) ;
		}
	}) ;
	
}) ;

/* 執行 */
function go() {
	$('[name="search"]').val('ok') ;
	$('form[name="myform"]').submit() ;
}
//
</script>
<style>
form div {
	margin: 10px;
}
td {
	padding: 5px;
}
</style>
</head>
<body>
<form method="POST" name="myform">
<div>賣方身份證字號：<input type="text" name="oId" value="<?=$oId?>">&nbsp;<img id='oIdImg' src='/images/ng.png'></div>
<div>買方身份證字號：<input type="text" name="bId" value="<?=$bId?>">&nbsp;<img id='bIdImg' src='/images/ng.png'></div>
<div><input type="button" value="開始查詢" onclick="go()"></div>
<input type="hidden" name="search" value="">
</form>

<hr style="margin:10px;border:1px solid #ccc;">
<?=$tbl?>
<?=$notMatch?>
<?=$ans?>
</body>
</html>
