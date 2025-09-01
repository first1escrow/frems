<?php
include_once '../openadodb.php';

$sql = "SELECT bId FROM tBranch WHERE bBrand = 69";
$rs = $conn->Execute($sql);

$branch = array();
while (!$rs->EOF) {
	array_push($branch, $rs->fields['bId']);

	$rs->MoveNext();
}

$txt = "政祺:出賣方服務費之前,請在代書LINE上留下詢問記錄 :\n【請問***案件,是否有要動撥,如果沒有的話,案件要先出服務費囉!】\n(代書若未回,請自動於隔日後出款)";

header("Content-Type:text/html; charset=utf-8");
foreach ($branch as $v) {
	$sql = "INSERT INTO
					tBranchNote
				SET 
					bStore = '".$v."',
					bNote = '".$txt."',
					bCreatTime = '".date("Y-m-d H:i:s")."'			
				";
	$conn->Execute($sql);
	echo $sql.";<br>";

}
?>