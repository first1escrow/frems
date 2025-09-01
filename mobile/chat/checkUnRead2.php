<?php
include_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
// require_once 'snoopy/Snoopy.class.php' ;
include_once dirname(dirname(dirname(__FILE__))).'/session_check.php' ;
 $_POST = escapeStr($_POST) ;

$undertaker = $_SESSION['member_id'];

##test value##
// if ($undertaker == 6) {
// 	$undertaker = 22 ;
// }
// $undertaker = 33; //1,5,12,18,19,21,33
$sql = "SELECT
			aa.aId,
			aa.aAccount,
			aa.aName,
			aa.aParentId,
			s.sOffice
		FROM
			tAppAccount AS aa,
			tScrivener AS s
		WHERE
			SUBSTR(aa.aParentId,3)=s.sId  
			AND sUndertaker1 = '".$undertaker."'
			AND s.sStatus = '1'
			AND aa.aStatus = 1
			AND aa.aIdentity = 1
			AND aa.aOK = 1
		ORDER BY aa.aParentId ASC

		";
// echo $sql;
$rs = $conn->Execute($sql);
$count = 0;
while (!$rs->EOF) {
	
	$count += getUnRead($rs->fields['aId']);	
	$rs->MoveNext();
}

// $count =10;
if ($count > 0 ) {
	$sql = "SELECT pLineUserId FROM tPeopleInfo WHERE pId ='".$undertaker."'";
	$rs = $conn->Execute($sql);

	$lineToken = $rs->fields['pLineUserId'];

	// $lineToken = 'U42a053dde4940102bf8c9c7b750bb9a1';//U42a053dde4940102bf8c9c7b750bb9a1

	$msg = '您的即時通訊有未讀訊息';
	
	// http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=U62e7e4ca0ad872f7d38c603757f8fb7f&msg=%E4%BF%9D%E8%AD%89%E8%99%9F%E7%A2%BC%3A123456789%0A%E7%9B%AE%E5%89%8D%E7%8B%80%E6%85%8B%3AOK
	file_get_contents("http://www.design.e-twlink.com/first1/lineBot/first1LinePush.php?userId=".$lineToken."&msg=".$msg);
}
echo $count;

function getUnRead($id){
	global $conn;
	$sql = "SELECT COUNT(id) AS unRead FROM tAppMessages WHERE aFlow = 1 AND aRead = 'N' AND aAccount ='".$id."' ";
	
	$rs = $conn->Execute($sql);

	return $rs->fields['unRead'];
}
// $undertaker = 19;
#####
?>