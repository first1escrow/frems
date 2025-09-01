<?php
    include_once '../configs/config.class.php';
    include_once '../class/advance.class.php';
    include_once '../session_check.php';
    include_once '../openadodb.php';
    include_once '../includes/lib.php';

    $advance = new Advance();

    $cCertifiedId = trim(addslashes($_REQUEST['cCertifyId'] ?? ''));
    $_iden        = trim(addslashes($_REQUEST['iden'] ?? ''));
    $save         = trim(addslashes($_REQUEST['save'] ?? ''));
    $del          = trim(addslashes($_POST['del'] ?? ''));
    $sign         = trim(addslashes($_REQUEST['sign'] ?? '')); //判斷合約書位置

                                                       // 初始化相關變數
    $vr_code       = $cCertifiedId;                    // VR代碼等於認證ID
    $cInvoiceOther = $_REQUEST['cInvoiceOther'] ?? ''; // 其他發票資訊

    // $cCertifiedId = substr($vr_code,5) ;

    // $cCertifiedId =

    //刪除資料
    if ($del == 'ok') {
        $del_no = $_POST['del_no'] ?? '';

        if ($del_no) {
            $sql = '
			DELETE FROM
				tContractSpecial
			WHERE
				cId="' . $del_no . '"
		';

            $conn->Execute($sql);
        }
    }
    ##

    //儲存資料
    if ($save == 'ok') {
        //取得表格所有資料
        $data = $_POST;

        if ($cCertifiedId == '') {
            $cCertifiedId = $data['cCertifiedId'];
        }

        ##
        $sql = '
	SELECT
		*
	FROM
		tContractSpecial
	WHERE
		cCertifiedId="' . $cCertifiedId . '"
	ORDER BY
		cId
	ASC ;
';
        // echo $sql;
        //echo "SQL=".$sql ;
        $rs = $conn->Execute($sql);

        //是否有新增對象及處裡
        if ($rs->EOF || ! ($rs->fields['cCertifiedId'] ?? '')) {
            $sqls = '
			INSERT INTO
				tContractSpecial
				(
					cCertifiedId,
					cNote
				)
			VALUES
				(
					"' . $data['cIdentity'] . '",
					"' . $data['cNote'] . '"
				)
		';

            $conn->Execute($sqls);
        } else {

            $sqls = '
			UPDATE
				tContractSpecial
			SET
				cNote="' . $data['cNote'] . '"

			WHERE
				cId="' . $data['cId'] . '"
				AND cCertifiedId="' . $data['cCertifiedId'] . '"
		';

            $conn->Execute($sqls);
        }
        ##

        //儲存更新的資料
        // $max = count($data['cId']) ;
        // for ($i = 0 ; $i < $max ; $i ++) {

        //     $ck = 0 ;

        //     $sqls = '
        //         UPDATE
        //             tContractSpecial
        //         SET
        //             cNote="'.$data['cNote'][$i].'",

        //         WHERE
        //             cId="'.$data['cId'][$i].'"
        //             AND cCertifiedId="'.$data['cCertifiedId'][$i].'"
        //     ' ;
        //     $conn->Execute($sqls) ;
        // }
        // ##

    }
    ##

    //顯示相關資料
    $sql = '
	SELECT
		*
	FROM
		tContractSpecial
	WHERE
		cCertifiedId="' . $cCertifiedId . '"
	ORDER BY
		cId
	ASC ;
';
    // echo $sql;
    //echo "SQL=".$sql ;
    $rs = $conn->Execute($sql);

    // 初始化計數器
    $count = $rs->RecordCount();

    ##

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>代理人</title>
<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="/js/rocCal.js"></script>
<script src="/js/IDCheck.js"></script>
<script type="text/javascript">
$(document).ready(function() {


	// //初始設定
	// $('#new_record').hide() ;

	//新增一筆紀錄
	$('#addnew').click(function() {
		$('#new_record').show() ;
		$('#addnew_field').html('&nbsp;') ;
	}) ;

	//儲存資料
	$('#savedata').click(function() {
		$('[name="save"]').val('ok') ;
		$('[name="myform"]').submit() ;
	}) ;

<?php if ($save == 'ok') {?>
	$('#dialog_save').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
<?php }?>

<?php if ($del == 'ok') {?>
	$('#dialog_save').dialog({
		modal: true,
		buttons: {
			"確認": function() {
				$(this).dialog("close") ;
			}
		}
	}) ;
<?php }?>

	//關閉視窗
	$('#closewin').click(function() {
		window.close() ;
	}) ;

	//變更按鈕樣式
	$('#addnew').button({
		icons:{
			primary: "ui-icon-plus"
		}
	}) ;


	$('#closewin').button({
		icons:{
			primary: "ui-icon-close"
		}
	}) ;

}) ;




//資料更新存檔
function save_data() {
	$('[name="save"]').val('ok') ;
	$('[name="myform"]').submit() ;
	//alert('OK') ;
}




//刪除資料
function del(no) {
	$('#dialog_save').html('確認是否刪除本筆資料？') ;
	$('#dialog_save').prop('title','ID = ' + no + ', 刪除？') ;

	$('#dialog_save').dialog({
		resizable: false,
		height: 140,
		modal: true,
		buttons: {
			"確認": function() {
				$('form[name="del_form"] input[name="del"]').val('ok') ;
				$('form[name="del_form"] input[name="del_no"]').val(no) ;
				$('form[name="del_form"]').submit() ;
			},
			"取消": function() {
				$(this).dialog("close") ;
				$('[name="myform"]').submit() ;
			}
		}
	}) ;
}



</script>
<style>
.sign-red {
	color:red;
}
table tr td {
	text-align:left;
}
fieldset {
	border-radius: 6px;
}
</style>
</head>

<body style="background-color:#F8ECE9;">
<form id="myform" name="myform" method="POST">
<input type="hidden" name="cCertifyId" value="<?php echo $vr_code ?>">

<div style="height:10px;">
</div>
<input type="hidden" name="cInvoiceOther" value="<?php echo $cInvoiceOther ?>">
<input type="hidden" name="save" value="">
<table border="0" style="width:1000px;">


	<tr>
		<td colspan="6" style="background-color:#E4BEB1;font-size:12pt;font-weight:bold;padding:5px;">
			<span class="sign-red">*</span>特約事項<?php echo $count ?>&nbsp;&nbsp;(<?php echo $cCertifiedId ?>)
			<input type="hidden" name="cId" value="<?php echo ! $rs->EOF ? ($rs->fields['cId'] ?? '') : '' ?>">
			<input type="hidden" name="cCertifiedId" value="<?php echo ! $rs->EOF ? ($rs->fields['cCertifiedId'] ?? '') : '' ?>">


		</td>
	</tr>

	<tr>

		<td style="width:380px;">
			<table border="0" style="width:300px;">
				<tr>
					<td>
						<input type="hidden" name="cIdentity" value="<?php echo $cCertifiedId ?>">

						<textarea name="cNote" cols="50" rows="10"><?php echo ! $rs->EOF ? ($rs->fields['cNote'] ?? '') : '' ?></textarea>
					</td>
				</tr>

			</table>
		</td>
	</tr>


	<tr>
		<td id="addnew_field" colspan="6" style="text-align:right;">
		<?php if ($sign == 1) {?>
			<input id="savedata" type="button" style="width:100px;" value="存檔">
		<?php	}?>
			<!-- <button id="addnew">增加事項</button> -->
		</td>
	</tr>
</table>

<br>
<hr align="left" style="width:1100px;">

<div style="width:1100px;text-align:right;">

</div>

</form>
<form method="post" name="del_form">
<input type="hidden" name="del" value="">
<input type="hidden" name="del_no" value="">
</form>
<input type="hidden" id="dialog_confirm_count" value="0">
<div id="dialog_confirm">
</div>
<div id="dialog_save">
<?php
    if ($save == "ok") {
        echo '資料已更新!!';
    } else if ($del == 'ok') {
        echo '資料已刪除!!';
    }
?>
</div>
</body>
</html>