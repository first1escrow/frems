<?
require_once dirname(dirname(__DIR__)).'/first1DB.php';

$bk = $_REQUEST['bk'];
$q  = $_GET['q'];

if (!$q) {
    exit;
}

$query = '
	SELECT
		bBank4, bBank4_name 
	FROM
		tBank 
	WHERE
		bBank3 = :bk
		AND bBank4 <> "" 
	GROUP BY
		bBank4
	ASC;
';

$conn = new first1DB;
$rs = $conn->all($query, ['bk' => $bk]);

foreach ($rs as $v) {
	if (preg_match("/$q/", $v['bBank4_name'])) {
		echo '('.$v['bBank4'].')'.$v['bBank4_name']."\n";
	}
}

?>