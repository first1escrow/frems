<?
require_once dirname(dirnamne(__DIR__)).'/session_check.php';
require_once dirname(dirnamne(__DIR__)).'/first1DB.php';

$q = $_GET['q'];

if (!$q) {
    exit;
}

$query = '
	SELECT 
		bBank3,
		bBank4_name 
	FROM 
		tBank 
	WHERE
		bBank4 = ""
	ORDER BY 
		bBank4
	ASC;
';

$conn = new first1DB;
$rs = $conn->all($query);

foreach ($rs as $v) {
	if (preg_match("/$q/", $v['bBank4_name'])) {
		echo '('.$v['bBank3'].')'.$v['bBank4_name']."\n";
	}
}

?>