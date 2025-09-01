<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$list = array();

$list["data"] = array();





$sql = 'SELECT
			lId AS id,
			lNote AS note,
			lDays AS days,
			(SELECT pName FROM tPeopleInfo WHERE pId=lCreator) AS creator,
			lCreatTime AS creattime,
			(SELECT pName FROM tPeopleInfo WHERE pId= lEditor) AS editor,
			lEditorTime AS editTime
		FROM
			tLegalEvent' ;
$rs = $conn->Execute($sql) ;

while (!$rs->EOF) {


	array_push($list['data'], $rs->fields);

	$rs->MoveNext();
}

if (is_array($list)) {
	echo json_encode($list);
}

// print_r($list);


?>