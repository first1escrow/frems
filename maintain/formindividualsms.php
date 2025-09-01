<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/sms.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$_POST   = escapeStr($_REQUEST);
$storeId = $_REQUEST['storeId'];
$act     = $_REQUEST['act'];
$cat     = $_REQUEST['cat'];
//新增修改
if ($act == 'save') {
    //更新
    $Name   = $_POST['Name'];
    $Mobile = $_POST['Mobile'];
    $Id     = $_POST['Id'];
    $Title  = $_POST['Title'];

    for ($i = 0; $i < count($Mobile); $i++) {
        $default = 0;

        $sql = '
			UPDATE
				tFeedBackStoreSms
			SET
				fTitle="' . $Title[$i] . '",
				fName="' . $Name[$i] . '",
				fMobile="' . $Mobile[$i] . '"
			WHERE
				fId="' . $Id[$i] . '"
		;';
        // echo $sql;
        $conn->Execute($sql);
    }
    ##

    //新增
    $newbTitle  = $_POST['newTitle'];
    $newbName   = $_POST['newbName'];
    $newbMobile = $_POST['newbMobile'];

    for ($i = 0; $i < count($newbMobile); $i++) {
        $default = 0;
        if ($newbMobile[$i] != '') {
            $sql = '
				INSERT INTO
					tFeedBackStoreSms
				(
					fType,
					fTitle,
					fStoreId,
					fName,
					fMobile
				)
				VALUES
				(
					"' . $cat . '",
					"' . $newbTitle[$i] . '",
					"' . $storeId . '",
					"' . $newbName[$i] . '",
					"' . $newbMobile[$i] . '"
				)
			;';

            // echo $sql;
            // if ($_SESSION['member_id']) {
            //     # code...
            //     echo $sql;
            // }
            $conn->Execute($sql);
        }
    }
    ##
}
##

//刪除
else if ($act == 'del') {
    $delno = $_POST['delno'];
    $sql   = 'UPDATE tFeedBackStoreSms SET fDelete = 1 WHERE fId="' . $delno . '";';
    // echo $sql;
    $conn->Execute($sql);
}
##
//簡訊對象 title
$pos = array();
if ($cat == 1) {
    $sql = 'SELECT * FROM tTitle_SMS WHERE tKind="1" GROUP BY tTitle ORDER BY tTitle ASC;';
} elseif($cat == 2) {
    $sql = 'SELECT * FROM tTitle_SMS WHERE id IN ("12","13","14","15") ORDER BY id ASC;';
} elseif($cat == 3) {
    $sql = 'SELECT * FROM tTitle_SMS WHERE id IN ("12","13") ORDER BY id ASC;';
}

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $pos[] = $rs->fields;
    $rs->MoveNext();
}

//取得簡訊資料
$list = array();
$sql  = "SELECT
			*
		FROM
			tFeedBackStoreSms AS fbs
		LEFT JOIN
			tTitle_SMS AS b ON fbs.fTitle=b.id
		WHERE
			fbs.fType = '" . $cat . "'
			AND fbs.fStoreId = '" . $storeId . "'
			AND fbs.fDelete = 0
		ORDER BY
			fbs.fTitle,b.tTitle
		ASC";

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
		$('[name="act"]').val('del') ;
		$('[name="delno"]').val(no) ;
		$('#form_sms').submit() ;
	}
	else {
		return false ;
	}
}
function copyToFeedBackSms(id,type){
	$.ajax({
		url: 'copyToFeedBackSms.php',
		type: 'POST',
		dataType: 'html',
		data: {id: id,cat:3,type:type},
	})
	.done(function(msg) {
		alert(msg);

	});

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
<input type="hidden" name="bId" value="<?=$bId?>">
<input type="hidden" name="delno" value="">
<table border="0" width="100%">
	<tr>
		<th colspan="6"><center>回饋金通知簡訊對象</center></th>
	</tr>
	<tr>
		<th colspan="6"><center><hr></center></th>
	</tr>
	<tr>
		<th><center>職稱</center></th>
		<th><center>姓名</center></th>
		<th><center>行動電話</center></th>
		<th>出款回饋簡訊</th>
		<th>&nbsp;</th>
	</tr>
	<?php
foreach ($list as $k => $v) {
    ?>
		<tr>

			<td>
				<select name="Title[]">
				<?php
    for ($i = 0; $i < count($pos); $i++) {
        $sel = '';
        if ($pos[$i]['id'] == $v['fTitle']) {
            $sel = ' selected="selected"';
        }

        echo '<option value="' . $pos[$i]['id'] . '"' . $sel . '>' . $pos[$i]['tTitle'] . "</option>\n";
    }
    ?>
				</select>
			</td>
			<td><input type="hidden" name="Id[]" value="<?=$v['fId']?>"><input type="text" class="input-text-per" name="Name[]" value="<?=$v['fName']?>"></td>
			<td><input type="text" class="input-text-per" name="Mobile[]" maxlength="10" value="<?=$v['fMobile']?>"></td>
			<td><input type="button" value="同步" onclick="copyToFeedBackSms(<?=$v['fId']?>,3)"></td>
			<td><input type="button" value="刪除" onclick="del('<?=$v['fId']?>','<?=$v['fMobile']?>')"></td>

		</tr>
	<?php
}

for ($j = 0; $j < 5; $j++) {
    ?>
		<tr>
			<td>
				<select name="newTitle[]">
					<option value="" selected="selected"></option>
                    <?php
                        for ($i = 0; $i < count($pos); $i++) {
                            echo '<option value="' . $pos[$i]['id'] . '">' . $pos[$i]['tTitle'] . "</option>\n";
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
</table>
<center>
	<br/>
	<button id="save">儲存</button>
</center>
</form>



</body>
</html>










