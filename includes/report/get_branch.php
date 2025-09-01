<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
$_POST = escapeStr($_POST) ;
$bStoreClass = $_POST['bStoreClass'] ;
$bCategory = $_POST['bCategory'] ;
$con = '' ;

if ($bStoreClass) {
	$con .= ' AND bStoreClass="'.$bStoreClass.'"' ;
}

if ($bCategory) {
	$con .= ' AND bCategory="'.$bCategory.'"' ;
}

$sql = '
SELECT 
	bId,
	(SELECT bCode FROM tBrand WHERE bId=bra.bBrand) bCode,
	bStore 
FROM 
	tBranch AS bra 
WHERE 
	bId <> "0" '.$con.' 
ORDER BY 
	bId 
ASC;
' ;

$str = '<option value=""></option>' ; 
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$str .= '<option value="'.$rs->fields['bId'].'">'.$rs->fields['bCode'].str_pad($rs->fields['bId'],5,"0",STR_PAD_LEFT).'/'.$rs->fields['bStore']."</option>\n" ;
	

	$rs->MoveNext();
}

echo $str ;
// $max = mysql_num_rows($rel) ;
// for ($i = 0 ; $i < $max ; $i ++) {
// 	$tmp = mysql_fetch_array($rel) ;
// 	$str .= '<option value="'.$tmp['bId'].'">'.$tmp['bCode'].str_pad($tmp['bId'],5,"0",STR_PAD_LEFT).'/'.$tmp['bStore']."</option>\n" ;
// 	unset($tmp) ; unset($code) ;
// }
// include('../../closedb.php') ;
$conn->close();

?>