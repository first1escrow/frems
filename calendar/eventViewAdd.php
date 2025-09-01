<?php

include_once '../configs/config.class.php';
include_once dirname(dirname(__FILE__)).'/class/SmartyMain.class.php';
include_once '../web_addr.php' ;
include_once '../session_check.php' ;
include_once '../openadodb.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/lib/encode.php' ;
// require_once dirname(dirname(__FILE__)).'/includes/encode.php' ;
require_once dirname(__DIR__) . '/calendar/calendarOverTime.php';

$staffId = $_SESSION['member_id'] ;
//$staffId = '25' ;
$staffDep = $_SESSION['member_pDep'] ;
//$staffDep = '7' ;
$staffName = $_SESSION['member_name'] ;
//$staffName = '方冠為' ;
//$staffName = '吳效承' ;

//產製時間選單
Function timeMenu($patt='', $kind = '') {
	$ampm = '' ;
	$val = '<option value=""></option>'."\n" ;
	
		for ($i = 0 ; $i < 24 ; $i ++) {
			$hrVal = str_pad($i,2,'0',STR_PAD_LEFT) ;
			
			if ($i < 12) {
				$hr = str_pad($i,2,'0',STR_PAD_LEFT) ;
				$ampm = 'AM' ;
			}
			else if ($i == 12) {
				$hr = str_pad($i,2,'0',STR_PAD_LEFT) ;
				$ampm = 'PM' ;
			}
			else {
				$hr = str_pad(($i - 12),2,'0',STR_PAD_LEFT) ;
				$ampm = 'PM' ;
			}
			
			//設定公司休息時刻不能填行程
            if($hrVal == 12){
                if($kind == 'start'){
                    $isDisabled_0 = 'disabled="disabled"' ;
                }
                $isDisabled_30 = 'disabled="disabled"' ;
            } else if($hrVal == 13){
                if($kind == 'end'){
                    $isDisabled_0 = 'disabled="disabled"' ;
                } else {
                    $isDisabled_0 = '';
                }
                $isDisabled_30 = '';
            } else if($hrVal == 18){
                if($kind == 'start') {
                    $isDisabled_0 = 'disabled="disabled"';
                    $isDisabled_30 = '';
                } else if($kind == 'end') {
                    $isDisabled_0 = '';
                    $isDisabled_30 = 'disabled="disabled"';
                }
            } else {
                $isDisabled_0 = '';
                $isDisabled_30 = '';
            }

            //整點
			$val .= '<option value="'.$hrVal.':00:00" '.$isDisabled_0 ;
			if ($patt == $hrVal.':00:00') $val .= ' selected="selected"' ;
			$val .= '>'.$hr.':00 '.$ampm."</option>\n" ;
			##
			
			//半點
			$val .= '<option value="'.$hrVal.':30:00" '.$isDisabled_30 ;
			if ($patt == $hrVal.':30:00') $val .= ' selected="selected"' ;
			$val .= '>'.$hr.':30 '.$ampm."</option>\n" ;
			##
		}
	
	return $val ;
}
##

$saveid = $_REQUEST['saveid'] ;
$date = $_REQUEST['date'] ;
$tf = false ;

//
if ($saveid == 'ok') {
	$val = $_POST ;
	
	//取得店家代號
	$bId = '' ;
	if ($val['cStore']) {
		$sql = 'SELECT bId FROM tBranch WHERE bCategory="'.$val['cCategory'].'" AND bBrand="'.$val['cBrand'].'" AND bStore="'.$val['cStore'].'";' ;
		$rs = $conn->Execute($sql) ;
		$bId = $rs->fields['bId'] ;
	}
	##
	
	//取得代書代號
	$sId = '' ;
	if ($val['cScrivener']) {
		$sql = 'SELECT sId FROM tScrivener WHERE sName="'.$val['cScrivener'].'";' ;
		$rs = $conn->Execute($sql) ;
		$sId = $rs->fields['sId'] ;
	}
	##
	
	$_s = $val['startDate'].' '.$val['startTime'] ;
	$_e = $val['endDate'].' '.$val['endTime'] ;
	
	if ($_s > $_e) {
		$tmp = $_s ;
		$_s = $_e ;
		$_e = $tmp ;
		unset($tmp) ;
	}
	
	$sql = '
		INSERT INTO
			tCalendar
		(
			cClass,
			cSubject,
			cDescription,
			cBrand,
			cCategory,
			cStore,
			cStoreId,
			cScrivener,
			cScrivenerId,
			cStartDateTime,
			cEndDateTime,
			cCreator,
			cCreateDateTime,
			cCity
		)
		VALUES
		(
			"'.$val['cClass'].'",
			"'.$val['cSubject'].'",
			"'.$val['cDescription'].'",
			"'.$val['cBrand'].'",
			"'.$val['cCategory'].'",
			"'.$val['cStore'].'",
			"'.$bId.'",
			"'.$val['cScrivener'].'",
			"'.$sId.'",
			"'.$_s.'",
			"'.$_e.'",
			"'.$val['cCreator'].'",
			"'.date("Y-m-d H:i:s").'",
			"'.$val['city'].'"
		)
	' ;
	
	$conn->Execute($sql) ;
	$tf = true ;
	$id = $conn->Insert_ID(); 
	// $sql = "SELECT pName FROM tPeopleInfo WHERE pId = '".$val['cCreator']."'";
	// $rs = $conn->Execute($sql);

	// $lineId = 'U4b14569b842b0d5d4613b77b94af02b6'; //測試
	// sendMsg($lineId,$id,$rs->fields['pName']);
	// 

    //行程類別選單
    $calendarClass = '';
    $sql_class = 'SELECT cName FROM tCalendarClass WHERE cId ='.$val['cClass'] ;
    $rs_class = $conn->Execute($sql_class) ;
    if (!$rs_class->EOF) {
        $calendarClass = $rs_class->fields['cName'] ;
    }

    //確認是否進入加班行程
    $isOverTime = false;
    $checkOutput = checkOvertime($_s, $_e);
    if(isset($checkOutput['isOverTime'])){
        $isOverTime = $checkOutput['isOverTime'];
    }

    if($isOverTime){
        calendarOverTime([
            'calendarId' => $id,
            'staffId' => $val['cCreator'],
            'staffName' => $staffName,
            'fromDateTime' => $_s,
            'toDateTime' => $_e,
            'reason' => $calendarClass.'：'.$val['cDescription']
        ]);

//        echo "<script>alert('此行程將一併申請加班送審。');</script>";
    }
	
	$lineId = 'Ue3a988aae4cc2d611cd4b4ed56420d85'; //政耀
	sendMsg($lineId,$id,$staffName);
	// 
	// 
	// $staffId = 68;
	// 通知組長 sCalendarNotify=1 通知
	$sql = "SELECT
				la.lLineId
			FROM
				tSalesGroup AS sg 
			LEFT JOIN
				tLineAccount AS la ON la.lpId=sg.sManager
			WHERE
				FIND_IN_SET('".$staffId."',sg.sMember) AND sg.sCity = '".$val['city']."' AND sg.sCalendarNotify = 1";
	$rs = $conn->Execute($sql);
	while (!$rs->EOF) {
		$lineId = $rs->fields['lLineId'];
		$lineId = 'U4b14569b842b0d5d4613b77b94af02b6';//測試
		sendMsg($lineId,$id,$staffName);

		$rs->MoveNext();
	}

}
##


function sendMsg($lineId,$Id,$Name){

		
     	$lineStr = enCrypt('lineId='.$lineId.'&s=SC0224&c=O');
     	
		$msg = "業務行程";
		$url = "https://www.first1.com.tw/line/firstSales/EditCalendar.php?v=".$lineStr."&id=".$Id."&type=show";
	   
	    $data['lineId'] = $lineId;
	    $data['url'] = $url;
	    $data['title'] ='業務行程';
	    $data['text'] = $Name."新增行程";
	    $data['label'] = '查看';
    	
    	$url = "https://firstbotnew.azurewebsites.net/bot/api/linePushBubble.php?v=".enCrypt(json_encode($data));
    	// echo $url;
    	// 
     	 file_get_contents($url);
     	 // die;
   
}
if ($tf) {
?>
<script>
parent.$.colorbox.close()
</script>
<?php
}
else {
//行程類別選單
$cClassMenu = array() ;

$sql = 'SELECT * FROM tCalendarClass WHERE cId <> 4 ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$cClassMenu[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//行程主題選單
$cSubjectMenu = array() ;

$sql = 'SELECT * FROM tCalendarSubject ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$cSubjectMenu[] = $rs->fields ;
	$rs->MoveNext() ;
}
##

//取得店家名稱
$bMenu = '<option value="">品牌</option>'."\n" ;
$sql = 'SELECT bId, bName FROM tBrand ORDER BY bId ASC;' ;

$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$bMenu .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bName']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//取得代書名稱
$sMenu = '<option value=""></option>'."\n" ;
$sql = 'SELECT * FROM tScrivener WHERE sStatus="1" ORDER BY sName ASC;' ;

$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$sMenu .= '<option value="'.$rs->fields['sId'].'">'.$rs->fields['sName']."</option>\n" ;
	$rs->MoveNext() ;
}
##

//建立時間選單
$tmp = explode("T",$date) ;
$_dt = $tmp[0] ;
$_tm = $tmp[1] ;
unset($tmp) ;

$_dtt = $_dt ;
$_tmt = date("H:i:s",strtotime($_tm." +60 minutes")) ;
if ($_tmt == '00:00:00') {
	$_dtt = date("Y-m-d",strtotime($_dtt." + 1 day")) ;
}
//echo $_dtt.' '.$_tmt ; exit ;

$sTime = timeMenu($_tm, 'start') ;
$eTime = timeMenu($_tmt, 'end') ;
##
//地區
$str = "";
if ($staffId == 6 && $staffId == 3) {
	$str = "WHERE FIND_IN_SET('".$staffId."',zSales)";
}
$sql = "SELECT zCity FROM tZipArea ".$str." GROUP BY zCity ORDER BY nid";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menu_City[$rs->fields['zCity']] = $rs->fields['zCity'];
	
	$rs->MoveNext();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>新增行程</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta charset='utf-8' />
<link rel='stylesheet' href='lib/cupertino/jquery-ui.min.css' />
<link href='fullcalendar.css' rel='stylesheet' />
<link href='fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='lib/moment.min.js'></script>
<script src='lib/jquery.min.js'></script>
<script src='fullcalendar.min.js'></script>

<link href="lib/jquery-ui.css" rel="stylesheet">
<script src="lib/jquery-ui.js"></script>

<script src='lang-all.js'></script>
<script src='gcal.js'></script>

<script type='text/Javascript'>
$(document).ready(function() {
	$( "input[type=button], input[type=submit]" ).button();
	$( "input, select, textarea" ).change( function() {
		$('#change').val(1) ;
	}) ;

	$("#startDate").datepicker({
		dateFormat:"yy-mm-dd",
		selectOtherMonth: true
	}) ;
	$("#endDate").datepicker({
		dateFormat:"yy-mm-dd",
		selectOtherMonth: true
	}) ;
	
	$( "#dialog" ).dialog({
		modal: true,
		autoOpen: false,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			$("#dialog").html('') ;
		}
	}) ;
	
	$('[name="cDescription"]').focus() ;
});

function getStore() {
	var _b = $('[name="cBrand"]').val() ;
	var _c = $('[name="cCategory"]').val() ;
	var url = 'getStore.php?b=' + _b + '&c=' + _c ;
	
	if (_b == '' || _c == '') {
		$("#dialog").html('請先選取品牌名稱與店家種類!!') ;
		$("#dialog").dialog("open") ;
	}
	
	$('[name="cStore"]').autocomplete({
		source: url
	}) ;
}

function getScrivener() {
	var url = 'getScrivener.php' ;
	
	$('[name="cScrivener"]').autocomplete({
		source: url
	}) ;
}

function winclose() {
	if ($('#change').val() == '1') {
		$("#dialog1").html('行程記錄已變更!!請確認是否放棄修改!?') ;
		$("#dialog1").dialog({
			resizable: false,
			height: 200,
			modal: true,
			buttons: {
				"返回修改": function() {
					$("#dialog1").dialog("close") ;
					$("#dialog1").html('') ;
				},
				"放棄離開": function() {
					$("#dialog1").dialog("close") ;
					parent.$.colorbox.close()
				}
			}
		});
	}
	else {
		parent.$.colorbox.close()
	}
}

function toggleSH() {
//storeSH
	var sel = $('[name="cClass"]').val() ;
	
	if (sel == '1') {
		$('.storeSH').show() ;
		$('.scrSH').hide() ;
	}
	else if (sel == '2') {
		$('.storeSH').hide() ;
		$('.scrSH').show() ;
	}
    else if (sel == '4') {
        $('.storeSH').hide() ;
        $('.scrSH').hide() ;
        $('.cityTr').hide() ;
        $("select[name='cSubject']").val('4');
    }
	else {
		$('.storeSH').hide() ;
		$('.scrSH').hide() ;
	}
}
</script>
<style>
	.custom-combobox {
		position: relative;
		display: inline-block;
	}
	.custom-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
	}
	.custom-combobox-input {
		margin: 0;
		padding: 5px 10px;
	}
	body {
		margin: 40px 10px;
		padding: 0;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
		font-size: 14px;
	}
	div {
		margin-top:10px;
		margin-bottom: 10px;
	}
	.lineB {
		float: left;
		width: 160px;
	}
	.lineE {
		float: left;
	}
	.ui-icon-triangle-1-s1 {
		background-position:-65px -16px;
		background-repeat: repeat-y; 
	}
	.ui-autocomplete-input {
		width:200px;
	}
	#mytb td {
		margin: 0px;
		padding: 5px;
		min-width:120px;
	}
	.lineT {
		background-color: #AED0EA;
		text-align: right;
		min-width: 80px;
		border-bottom-width: 1px;
		border-bottom-style: solid;
		border-bottom-color: #FFF;
	}
</style>
</head>
<body>

<center>
	<div id="detail" style="width:500px;">
	<form method="POST" name="savefrm">
		<input type="hidden" name="saveid" value="ok">
		<div style="text-align:center;font-size:14pt;font-weight:bold;color:#550088;">新增行程</div>
		<hr>
		<table id="mytb" cellspacing="0" width="400px;">
			<tr>
				<td class="lineT">行程時間：</td>
				<td>
					<input type="text" name="startDate" id="startDate" style="width:100px;" value="<?=$_dt?>">
					<select name="startTime" id="startTime" style="width:100px;">
					<?=$sTime?>
					</select>（起）
				</td>
			</tr>
			<tr>
				<td class="lineT">&nbsp;</div>
				<td>
					<input type="text" name="endDate" id="endDate" style="width:100px;" value="<?=$_dtt?>">
					<select name="endTime" id="endTime" style="width:100px;">
					<?=$eTime?>
					</select>（迄）
				</td>
			</tr>
			<tr>
				<td class="lineT">行程類別：</td>
				<td>
					<select name="cClass" id="cClass" style="width:200px;" onchange="toggleSH()">
					<?php
					foreach ($cClassMenu as $k => $v) {
						echo '<option value="'.$v['cId'].'"' ;
						//if ($v['cId'] == $list['cClass']) echo ' selected="selected"' ;
						echo '>'.$v['cName']."</option>\n" ;
					}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="lineT">行程主題：</td>
				<td>
					<select name="cSubject" style="width:200px;">
						<?php
						foreach ($cSubjectMenu as $k => $v) {
							echo '<option value="'.$v['cId'].'"' ;
							//if ($v['cId'] == $list['cSubject']) echo ' selected="selected"' ;
							echo '>'.$v['cName']."</option>\n" ;
						}
						?>
					</select>
				</td>
			</tr>
			<tr class="storeSH">
				<td class="lineT">拜訪店家：</td>
				<td>
					<select name="cBrand" style="width:100px;">
					<?=$bMenu?>
					</select>
					
					<select name="cCategory" style="width:60px;">
						<option value="">類型</option>
						<option value="1">加盟</option>
						<option value="2">直營</option>
						<option value="3">非仲介成交</option>
					</select>
					
					<input type="text" name="cStore" style="width:120px;" onclick="getStore()" placeholder="請輸入仲介店名">
				</td>
			</tr>
			<tr class="scrSH" style="display: none;">
				<td class="lineT">拜訪代書：</td>
				<td>
					<input type="text" name="cScrivener" style="width:120px;" onclick="getScrivener()" placeholder="請輸入代書姓名">
				</td>
			</tr>
			<tr class="cityTr">
				<td class="lineT">拜訪縣市</td>
				<td>
					<select name="city" id="">
						<option value="">請選擇</option>
						<?php foreach ($menu_City as $key => $value): ?>
							<option value="<?=$key?>"><?=$value?></option>
						<?php endforeach ?>
						
					</select>
				</td>
			</tr>
			<tr>
				<td class="lineT">內容簡述：</td>
				<td>
					<textarea cols="35" rows="10" name="cDescription" maxlength="200"></textarea>
				</td>
			</tr>
            <tr>
                <td colspan="2" style="color: red">休假資料與假勤系統同步，不需再填寫休假行程。</td>
            </tr>
		</table>
		<hr>
		<div style="float:left;margin:-2px 0 0 5px;">
			行程建立：
			<input type="hidden" name="cCreator" value="<?=$staffId?>"><?=$staffName?>
		</div>
		<div style="float:right;margin:-2px 0 0 5px;">
			<input type="submit" style="margin-left:2px;" value="儲存" title="儲存已修改的行程記錄">
			<input type="button" style="margin-left:2px;" value="返回" onclick="winclose()" title="返回行事曆主頁">			
		</div>
	</form>
	</div>
	
	<div id="dialog" title="注意"></div>
	<div id="dialog1" title="注意"></div>
	<input type="hidden" id="change" value="">
</center>
</body>
</html>
<?php
}
?>