<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
// include_once 'class/brand.class.php';
include_once '../../openadodb.php' ;
include_once '../../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '刪除仲介群組') ;



$id = addslashes(trim($_POST['id']));

//將對應仲介清除群組
$sql = "SELECT bId FROM  tBranch WHERE bGroup ='".$id."'";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	
	$list[] = $rs->fields;

	$rs->MoveNext();
}


for ($i=0; $i < count($list); $i++) { 
	

	$sql = "UPDATE tBranch SET bGroup =0 WHERE bId = '".$list[$i]['bId']."'";

	$rs = $conn->Execute($sql);

}
##

$sql = "DELETE FROM tBranchGroup WHERE bId ='".$id."'";
$rs = $conn->Execute($sql);
// echo $sql;


echo "刪除完成";
?>
