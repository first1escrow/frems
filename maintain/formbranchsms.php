<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';

$bId       = $_REQUEST['bId'];
$act       = $_REQUEST['act'];
$checkCase = $_REQUEST['same'];

//新增修改
if ($act == 'save') {
    //更新
    $smsId    = $_POST['smsId'];
    $bDefault = $_POST['bDefault'];
    $bNID     = $_POST['bNID'];
    $bName    = $_POST['bName'];
    $bMobile  = $_POST['bMobile'];

    for ($i = 0; $i < count($bMobile); $i++) {
        $default = 0;
        foreach ($bDefault as $k => $v) {

            if ($v == $smsId[$i]) {
                $default = 1;
                break;
            }
        }

        $sql = '
			UPDATE
				tBranchSms
			SET
				bNID="' . $bNID[$i] . '",
				bName="' . $bName[$i] . '",
				bMobile="' . $bMobile[$i] . '",
				bDefault="' . $default . '"
			WHERE
				bBranch="' . $bId . '"
				AND bId="' . $smsId[$i] . '"
		;';

        $conn->Execute($sql);
        write_log($bId . ',更新簡訊對象,' . $default . ',' . $bName[$i] . ',' . $bMobile[$i] . ',' . $checkCase, 'branchsms');
    }
    ##

    //新增
    $newbNID    = $_POST['newbNID'];
    $newbName   = $_POST['newbName'];
    $newbMobile = $_POST['newbMobile'];

    for ($i = 0; $i < count($newbMobile); $i++) {
        $default = 0;
        if ($newbMobile[$i] != '') {
            $sql = '
				INSERT INTO
					tBranchSms
				(
					bNID,
					bName,
					bMobile,
					bDefault,
					bBranch
				)
				VALUES
				(
					"' . $newbNID[$i] . '",
					"' . $newbName[$i] . '",
					"' . $newbMobile[$i] . '",
					"' . $default . '",
					"' . $bId . '"
				)
			;';

            $conn->Execute($sql);
            write_log($bId . ',新增簡訊對象,' . $default . ',' . $newbName[$i] . ',' . $newbMobile[$i] . ',' . $checkCase, 'branchsms');
        }
    }
    ##
    if ($checkCase == 1) {
        changeCaseMobile($bId);
    }

}
##

//刪除
else if ($act == 'del') {
    $delno = $_POST['delno'];
    // $sql = 'DELETE FROM tBranchSms WHERE bId="'.$delno.'" AND bBranch="'.$bId.'";' ;
    $sql = "UPDATE tBranchSms SET bDel = 1 WHERE bId='" . $delno . "' AND bBranch='" . $bId . "'";
    // echo $sql;
    // die;

    write_log($bId . ',刪除簡訊對象,,,' . $delno, '../log2/branchsms.log');
    $conn->Execute($sql);

    if ($checkCase == 1) {
        changeCaseMobile($bId);
    }
}
##

function changeCaseMobile($bId)
{
    global $conn;

    $str = '';

    $sql = "SELECT
				cc.cCertifiedId,
				cr.cBranchNum,
				cr.cBranchNum1,
				cr.cBranchNum2,
				cr.cBranchNum3
			FROM
				tContractCase AS cc
			LEFT JOIN
				tContractRealestate AS cr ON cr.cCertifyId =cc.cCertifiedId
			WHERE
				cc.cCaseStatus = 2
				AND (cr.cBranchNum = '" . $bId . "' OR cr.cBranchNum1 = '" . $bId . "' OR cr.cBranchNum2 = '" . $bId . "' OR cr.cBranchNum3 = '" . $bId . "') ";

    // echo $sql;
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        if ($rs->fields['cBranchNum'] == $bId) {
            addSmsDefault($bId, $rs->fields['cCertifiedId'], 'cSmsTarget');
        }

        if ($rs->fields['cBranchNum1'] == $bId) {
            addSmsDefault($bId, $rs->fields['cCertifiedId'], 'cSmsTarget1');
        }

        if ($rs->fields['cBranchNum2'] == $bId) {
            addSmsDefault($bId, $rs->fields['cCertifiedId'], 'cSmsTarget2');
        }

        if ($rs->fields['cBranchNum3'] == $bId) {
            addSmsDefault($bId, $rs->fields['cCertifiedId'], 'cSmsTarget3');
        }

        $rs->MoveNext();
    }

}

function addSmsDefault($bid, $cId, $colum)
{

    global $conn;

    $sql = 'SELECT bMobile FROM tBranchSms WHERE bBranch="' . $bid . '" AND bDefault="1" AND bNID NOT IN ("14","15") AND bDel = 0 ORDER BY bNID,bId ASC;';

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        $smsTarget[] = $rs->fields['bMobile'];

        $rs->MoveNext();
    }

    $sql = "UPDATE tContractRealestate SET " . $colum . " = '" . @implode(",", $smsTarget) . "' WHERE cCertifyId = '" . $cId . "'";
    write_log($bId . ',連動,' . $sql, 'branchsms');
    // echo $sql."<bR>";
    $conn->Execute($sql);

}

//簡訊對象 title
$pos = array();
$sql = 'SELECT * FROM tTitle_SMS WHERE id IN ("12","13","14","15","29") ORDER BY id ASC;';
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
		(SELECT tTitle FROM tTitle_SMS WHERE id=a.bNID) as tTitle,
		(SELECT tSmsNote FROM tTitle_SMS WHERE id=a.bNID) as tSmsNote
	FROM
		tBranchSms as a
	WHERE
		bBranch="' . $bId . '"
		AND bDel = 0
		AND bCheck_id = 0;
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

		if (confirm("要聯動至進行中的案件?")) {
			$('[name="same"]').val(1);
		}else{
			$('[name="same"]').val('');
		}

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
	$('[name="bMobile[]"]').each(function() {
		var no = $(this).val() ;
		if (noFormat(no) == true) {
			return false ;
		}
	}) ;

	$('[name="newbMobile[]"]').each(function() {
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
		if (confirm("要聯動至進行中的案件?")) {
			$('[name="same"]').val(1);
		}else{
			$('[name="same"]').val('');
		}
		$('[name="act"]').val('del') ;
		$('[name="delno"]').val(no) ;
		$('#form_sms').submit() ;
	}
	else {
		return false ;
	}
}
////
function copyToFeedBackSms(id,type){
	$.ajax({
		url: 'copyToFeedBackSms.php',
		type: 'POST',
		dataType: 'html',
		data: {id: id,cat:2,type:type},
	})
	.done(function(msg) {
		alert(msg);
		// console.log(msg);

		// $("#show").html(msg);
	});

}
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
<input type="hidden" name="bId" value="<?=$bId?>">
<input type="hidden" name="delno" value="">
<div id="show"></div>
<table border="0" width="100%">
	<tr>
		<th colspan="7"><center>發送簡訊對象</center></th>
	</tr>
	<tr>
		<th colspan="7"><center><hr></center></th>
	</tr>
	<tr>
		<th width="10%"><center>預設</center></th>
		<th width="10%"><center>職稱</center></th>
		<th width="25%"><center>姓名</center></th>
		<th width="25%"><center>行動電話</center></th>
		<th width="10%">通知回饋簡訊</th>
		<th width="10%">出款回饋簡訊</th>
		<th width="10%">&nbsp;</th>
	</tr>
	<?php
foreach ($list as $k => $v) {
    ?>
		<tr >
			<td align="center">
				<input type="hidden" name="smsId[]" value="<?=$v['bId']?>">
				<?php
$checked = '';
    if ($v['bDefault'] != '0') {
        $checked = ' checked="checked"';
    }

    echo '<input type="checkbox" name="bDefault[]" value="' . $v['bId'] . '"' . $checked . '>';
    ?>
			</td>
			<td>
				<select name="bNID[]">
				<?php
for ($i = 0; $i < count($pos); $i++) {
        $sel = '';
        if ($pos[$i]['id'] == $v['bNID']) {
            $sel = ' selected="selected"';
        }

        echo '<option value="' . $pos[$i]['id'] . '"' . $sel . '>' . $pos[$i]['tTitle'] . $pos[$i]['tSmsNote'] . "</option>\n";
    }
    ?>
				</select>
			</td>
			<td><input type="text" class="input-text-per" name="bName[]" value="<?=$v['bName']?>"></td>
			<td><input type="text" class="input-text-per" name="bMobile[]" maxlength="10" value="<?=$v['bMobile']?>"></td>
			<td><input type="button" value="同步" onclick="copyToFeedBackSms(<?=$v['bId']?>,1)"></td>
			<td><input type="button" value="同步" onclick="copyToFeedBackSms(<?=$v['bId']?>,2)"></td>
			<td><input type="button" onclick="del('<?=$v['bId']?>','<?=$v['bMobile']?>')" value="刪除"></td>
		</tr>
	<?php
}

for ($j = 0; $j < 5; $j++) {
    ?>
		<tr>
			<td align="center">
				<input type="checkbox" name="bDefault[]" value="0" disabled="disabled">
			</td>
			<td>
				<select name="newbNID[]">
					<option value="" selected="selected"></option>
				<?php
for ($i = 0; $i < count($pos); $i++) {
        echo '<option value="' . $pos[$i]['id'] . '">' . $pos[$i]['tTitle'] . $pos[$i]['tSmsNote'] . "</option>\n";
    }
    ?>
				</select>
			</td>
			<td><input type="text" class="input-text-per" name="newbName[]" value=""></td>
			<td><input type="text" class="input-text-per" name="newbMobile[]" maxlength="10" value=""></td>
		</tr>
	<?php
}
?>
	<!-- <tr>
		<td colspan="5"><font color="red">※同步到回饋金:請儲存後再按同步，如果沒有資料會新增，有資料會更新，更新部分只有同步過去的資料才會更新</font></td>
	</tr> -->
</table>
<center>
	<br/>
	<button id="save">儲存</button>
	<button id="cancel">返回</button>
	<input type="hidden" name="same" >
</center>
</form>

<form name="form_back" id="form_back" method="POST"  action="formbranchedit.php">
	<input type="hidden" name="id" value="<?=$bId?>">
</form>

</body>
</html>










