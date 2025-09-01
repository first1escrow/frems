<?php
require_once dirname(__DIR__).'/session_check.php';
require_once dirname(__DIR__).'/first1DB.php';

$id = empty($_POST["id"]) ? $_GET["id"] : $_POST["id"];

$sql = 'UPDATE tContractCase SET cCaseStatus = 2 WHERE cCertifiedId = :id;';

$conn = new first1DB;
$conn->exeSql($sql, ['id' => $id]);

header("location:formbuyowneredit.php?id=$id");
exit;
?>