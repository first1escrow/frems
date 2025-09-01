<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;

$sql = "SELECT pName,pJob,pCaseManage,pBasicManage,pReportManage,pSystemManage,pSalesManage,pId FROM tPeopleInfo ORDER BY pId ASC";

$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$list[$i] = $rs->fields;

	$list[$i]['pJob'] = ($list[$i]['pJob'] == 1)? '有效':'無效';
	$list[$i]['pCaseManage'] = ($list[$i]['pCaseManage'] == 1)? '有效':'無效';
	$list[$i]['pBasicManage'] = ($list[$i]['pBasicManage'] == 1)? '有效':'無效';
	$list[$i]['pReportManage'] = ($list[$i]['pReportManage'] == 1)? '有效':'無效';
	$list[$i]['pSystemManage'] = ($list[$i]['pSystemManage'] == 1)? '有效':'無效';
	$list[$i]['pSalesManage'] = ($list[$i]['pSalesManage'] == 1)? '有效':'無效';

	$i++;
	//pCaseManage 案件管理
	//pBasicManage 基本資料
	//pReportManage 報表作業
	//pSystemManage 系統管理
	//pSalesManage 業務管理
	$rs->MoveNext();
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>人員列表</title>
		<script src="../../js/jquery-1.7.2.min.js"></script>
	<link rel="stylesheet" href="../../css/colorbox.css" />
    <script src="../../js/jquery.colorbox.js"></script>

	<script>
		$(document).ready(function() {
		});

		function edit(cat,id){
			var url = 'salesPower.php?cat='+cat+'&id='+id;
			 $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
		}
	</script>
	<style>
		th{
			width: 10%;
			background-color: #E4BEB1;
			display: inline-block;			
			line-height: 30px;
			height: 30px;
			border: 1px solid #999;
		}
		td{
			width: 10%;
			background-color: #F8ECE9;
			display: inline-block;			
			line-height: 30px;
			height: 30px;
			border: 1px solid #CCC;
			text-align: center;
		}
		.xxx-button {
			background-color: #a63c38;
		    border: 1px solid #a63c38;
		    border-radius: 0;
		    font-weight: bold;
		    padding: 0 20px;
		    margin: 20px auto 20px auto;
		    color:#FFF;
		    line-height: 20px;
		    text-align: center;
		    cursor: pointer;

		}
	</style>
</head>
<body>
	<div style="line-height: 20px;">&nbsp;</div>
	<div><input type="button" value="新增" onclick="edit('add')" class="xxx-button"></div>
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<th>姓名</th>
			<th>是否有效</th>
			<th>案件管理</th>
			<th>基本資料</th>
			<th>報表作業</th>
			<th>系統管理</th>
			<th>業務管理</th>
			<th>編輯</th>
		</tr>
		<?php
		for ($i=0; $i < count($list); $i++) {  ?>
		<tr>
			<td><?=$list[$i]['pName']?></td>
			<td><?=$list[$i]['pJob']?></td>
			<td><?=$list[$i]['pCaseManage']?></td>
			<td><?=$list[$i]['pBasicManage']?></td>
			<td><?=$list[$i]['pReportManage']?></td>
			<td><?=$list[$i]['pSystemManage']?></td>
			<td><?=$list[$i]['pSalesManage']?></td>
			<td><input type="button" value="編輯" onclick="edit('modify','<?=$list[$i]['pId']?>')"></td>
		</tr>
		<?php } ?>
		
	</table>
</body>
</html>