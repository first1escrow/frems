<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$q = $_GET['q'];

if (!$q) {
    exit;
}

$query = '
	SELECT 
		bId,
		bName,
		bStore,
		bBrand, 
		(SELECT bCode FROM tBrand AS a WHERE a.bId=b.bBrand) bCode 
	FROM 
		tBranch AS b
	WHERE
		bStore LIKE "%'.$q.'%";
';

$conn = new first1DB;
$rs = $conn->all($query);

foreach ($rs as $v) {
	echo '('.$v['bCode'].sprintf("%05d", $v['bId']).')'.$v['bStore']."\n";
}

?>
