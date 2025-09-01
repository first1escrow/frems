<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$q = $_GET['q'];
if (!$q) {
    exit;
}

$query = '
	SELECT
		bId,bName,bStore,bBrand, 
		(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode 
	FROM
		tBranch b
	ORDER BY
		bStore
	ASC;
' ;

$conn = new first1DB;
$rs = $conn->all($query);

foreach ($rs as $v) {
	if (preg_match("/$q/", $v['bStore'])) {
		echo '('.$v['bCode'].sprintf("%05d", $v['bId']).')'.$v['bStore']."\n";
	}
}

?>