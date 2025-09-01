<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');
include_once 'bookFunction.php';



$_POST = escapeStr($_POST) ;
$bId = $_POST['id'] ;
$bId = '26720';
$sql = "SELECT 
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '".$bId."'";


$rs = $conn->Execute($sql);

$data = $rs->fields;


require_once 'taishin01_pdf_file.php';

?>
