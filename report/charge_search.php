<?php
include_once '../openadodb.php' ;

$sales=trim(addslashes($_POST['sales']));
$act=trim(addslashes($_POST['act']));
$count=trim(addslashes($_POST['count']));

if($act=='add')
{
	 addSales($sales,$count);
}

function addSales($pid,$c)
{
	 GLOBAL $conn;

	$sql='SELECT pName,pId FROM tPeopleInfo WHERE pId ='.$pid;
	$rs = $conn->Execute($sql) ;

	echo "<spna style='line-height:30px;' id='sales".$rs->fields['pId']."'>".$rs->fields['pName']."</span>&nbsp;
			<input type='button' value='刪除' onClick='del_sales(".$rs->fields['pId'].")'>
			<input type='hidden' name='charge_sales[]' value='".$rs->fields['pId']."'>
			&nbsp;&nbsp;<br>";
}

?>