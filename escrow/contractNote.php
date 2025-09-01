<?php
    include_once '../configs/config.class.php';
    include_once '../session_check.php';
    include_once '../openadodb.php';

    $_REQUEST = escapeStr($_REQUEST);

    $cCertifiedId = $_REQUEST['cCertifyId'];
    $cat          = $_REQUEST['cat'];
    $save         = isset($_REQUEST['save']) ? $_REQUEST['save'] : '';
    $del          = isset($_POST['del']) ? $_POST['del'] : '';

    if (! empty($_POST['Note'])) {
        $sql = "INSERT INTO tContractNote
				(
					cCertifiedId,
					cCategory,
					cNote
				) VALUES (
					'" . $cCertifiedId . "',
					'" . $cat . "',
					'" . $_POST['Note'] . "'
				)";
        $conn->Execute($sql);
    }

    if (! empty($_POST['id'])) {
        $sql = "UPDATE tContractNote SET cDel = 1 WHERE cId ='" . $_POST['id'] . "'";
        $conn->Execute($sql);
    }

    switch ($cat) {
        case '1':
            $title = '7日內未入帳說明';
            break;
        case '2':
            $title = '2個月未結案之案件';
            break;
        case '3':
            $title = '超過點交日尚未結案';
            break;
        default:
            # code...
            break;
    }

    $sql = "SELECT * FROM tContractNote WHERE cCertifiedId = '" . $cCertifiedId . "' AND cCategory ='" . $cat . "' AND cDel = 0 ORDER BY cModify_Time ASC";

    $rs = $conn->Execute($sql);

    // 初始化 $list 為空陣列，避免未定義及 count() 的致命錯誤
    $list = [];

    while (! $rs->EOF) {
        $list[] = $rs->fields;
        $rs->MoveNext();
    }
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

function add(){
	alert("新增成功");
	$('[name="formAdd"]').submit();
}

function del(id){
	$("[name='id']").val(id);
	alert("刪除成功");
	$('[name="formDel"]').submit();
}
</script>
<style>


.tb th{
	background-color:#E4BEB1;
	font-size:12pt;
	font-weight:bold;
	padding:5px;
	border:1px solid #999;
}
.tb td{

	padding:5px;
	border:1px solid #000;
}
</style>
</head>

<body style="background-color:#F8ECE9;">

<form action="" method="POST" name="formAdd">
<table border="0" style="width:80%;">

	<tr>
		<td colspan="2" style="background-color:#E4BEB1;font-size:12pt;font-weight:bold;padding:5px;">
			<?php echo $title ?>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="Note" cols="80" rows="5"></textarea>
		</td>
		<td width="20%">
			<input type="button" value="新增" onclick="add()" />
		</td>
	</tr>


</table>
</form>
<br />
<table cellspacing="0" cellpadding="0"  width="80%" class="tb">
	<tr>
		<th  width="70%">內容</th>
		<th  width="10%">時間</th>
		<th  width="20%">刪除</th>
	</tr>
	<?php
    for ($i = 0; $i < count($list); $i++) {?>
		<tr>
			<td><?php echo nl2br($list[$i]['cNote']) ?></td>
			<td><?php echo $list[$i]['cModify_Time'] ?></td>
			<td align="center"><input type="button" value="刪除" onclick="del(<?php echo $list[$i]['cId'] ?>)" /></td>
		</tr>
	<?php }?>
</table>
<br>
<form action="" method="POST" name="formDel">
	<input type="hidden" name="id" />
</form>


</div>
</body>
</html>