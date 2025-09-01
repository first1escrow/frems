<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$sId = $_REQUEST['sId'];
$act = $_REQUEST['act'];

//新增修改
if ($act == 'save') {
    //更新
    $smsId   = $_POST['smsId'];
    $sNID    = $_POST['sNID'];
    $sName   = $_POST['sName'];
    $sMobile = $_POST['sMobile'];

    for ($i = 0; $i < count($sMobile); $i++) {
        $default = 0;

        $sql = '
			UPDATE
				tScrivenerFeedSms
			SET
				sNID="' . $sNID[$i] . '",
				sName="' . $sName[$i] . '",
				sMobile="' . $sMobile[$i] . '"
			WHERE
				sScrivener="' . $sId . '"
				AND sId="' . $smsId[$i] . '"
		;';
        // echo $sql;
        $conn->Execute($sql);
    }
    ##

    //新增
    $newsNID    = $_POST['newsNID'];
    $newsName   = $_POST['newsName'];
    $newsMobile = $_POST['newsMobile'];

    for ($i = 0; $i < count($newsMobile); $i++) {
        $default = 0;
        if ($newsMobile[$i] != '') {
            $sql = '
				INSERT INTO
					tScrivenerFeedSms
				(
					sNID,
					sName,
					sMobile,
					sScrivener
				)
				VALUES
				(
					"' . $newsNID[$i] . '",
					"' . $newsName[$i] . '",
					"' . $newsMobile[$i] . '",
					"' . $sId . '"
				)
			;';
            // echo $sql;
            $conn->Execute($sql);
        }
    }
    ##
}
##

//刪除
else if ($act == 'del') {
    $delno = $_POST['delno'];
    $sql   = 'DELETE FROM tScrivenerFeedSms WHERE sId="' . $delno . '" AND sScrivener="' . $sId . '";';
    $conn->Execute($sql);
}
##

//簡訊對象 title
$pos = array();
$sql = 'SELECT * FROM tTitle_SMS WHERE tKind="1" GROUP BY tTitle ORDER BY tTitle ASC;';
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $pos[] = $rs->fields;
    $rs->MoveNext();
}

##

//取得簡訊資料
$list = array();
$sql  = '
	SELECT
		a.*,
		(SELECT tTitle FROM tTitle_SMS WHERE id=a.sNID) as tTitle
	FROM
		tScrivenerFeedSms as a
	WHERE
		sScrivener="' . $sId . '";
';
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $list[] = $rs->fields;
    $rs->MoveNext();
}
##
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<link rel="stylesheet" href="colorbox.css" />
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.colorbox-min.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	/* 儲存 */
	$('#save').click(function() {
		checkNewSMS() ;
		$('[name="act"]').val('save') ;
		$('#form_sms').submit() ;
	}) ;
	////

	/* 返回 */
	$('#cancel').click(function() {
		parent.jQuery.fn.colorbox.close() ;
	}) ;
	////
});

/* 檢查新增簡訊姓名電話格式 */
function checkNewSMS() {
	$('[name="sMobile[]"]').each(function() {
		var no = $(this).val() ;
		if (noFormat(no) == true) {
			return false ;
		}
	}) ;

	$('[name="newsMobile[]"]').each(function() {
		var no = $(this).val() ;
		if (noFormat(no) == true) {
			return false ;
		}
	}) ;
}
////

/* 若手機號碼輸入格式不正確 */
function noFormat(no) {
	if ((!/^09\d{8}$/.test(no)) && (no != '')) {
		alert('輸入手機號碼格式錯誤!!') ;
		return true ;
	}
	else {
		return false ;
	}
}
////

/* 刪除簡訊對象 */
function del(no,mo) {
	if (confirm("確定刪除本筆簡訊對象(" + mo + ")?") == true) {
		$('[name="act"]').val('del') ;
		$('[name="delno"]').val(no) ;
		$('#form_sms').submit() ;
	}
	else {
		return false ;
	}
}
////
</script>
<style type="text/css">
th, td {
	text-align: center;
}

</style>
</head>
<body style="background-color:#F8ECE9;">
<form id="form_sms" method="POST">
<input type="hidden" name="act" value="">
<input type="hidden" name="sId" value="<?=$sId?>">
<input type="hidden" name="delno" value="">
<table border="0" width="100%">
	<tr>
		<th colspan="6"><center>回饋金簡訊對象</center></th>
	</tr>
	<tr>
		<th colspan="6"><center><hr></center></th>
	</tr>
	<tr>
<!-- 		<th width="10%"><center>預設</center></th> -->
		<th width="20%"><center>職稱</center></th>
		<th width="30%"><center>姓名</center></th>
		<th width="30%"><center>行動電話</center></th>
		<th>&nbsp;</th>
	</tr>
	<?php
foreach ($list as $k => $v) {
    ?>
		<tr>

			<td><input type="hidden" name="smsId[]" value="<?=$v['sId']?>">
				<select name="sNID[]">
				<?php
for ($i = 0; $i < count($pos); $i++) {
        $sel = '';
        if ($pos[$i]['id'] == $v['sNID']) {
            $sel = ' selected="selected"';
        }

        echo '<option value="' . $pos[$i]['id'] . '"' . $sel . '>' . $pos[$i]['tTitle'] . "</option>\n";
    }
    ?>
				</select>
			</td>
			<td><input type="text" class="input-text-per" name="sName[]" value="<?=$v['sName']?>"></td>
			<td><input type="text" class="input-text-per" name="sMobile[]" maxlength="10" value="<?=$v['sMobile']?>"></td>
			<td><input type="button" value="刪除" onclick="del('<?=$v['sId']?>','<?=$v['sMobile']?>')"></td>
		</tr>
	<?php
}

for ($j = 0; $j < 5; $j++) {
    ?>
		<tr>

			<td>
				<select name="newsNID[]">
					<option value="" selected="selected"></option>
				<?php
for ($i = 0; $i < count($pos); $i++) {
        echo '<option value="' . $pos[$i]['id'] . '">' . $pos[$i]['tTitle'] . "</option>\n";
    }
    ?>
				</select>
			</td>
			<td><input type="text" class="input-text-per" name="newsName[]" value=""></td>
			<td><input type="text" class="input-text-per" name="newsMobile[]" maxlength="10" value=""></td>
		</tr>
	<?php
}
?>
</table>
<center>
	<br/>
	<button id="save">儲存</button>
	<!-- <button id="cancel">返回</button> -->
</center>
</form>

<form name="form_back" id="form_back" method="POST"  action="formscriveneredit.php">
	<input type="hidden" name="id" value="<?=$sId?>">
</form>

</body>
</html>










