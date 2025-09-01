<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/bookFunction.php';

header("Content-Type: application/pdf");
header('Content-Disposition: inline; filename="instruction.pdf"');

$_POST = escapeStr($_POST);
$bId   = $_POST['id'];

$sql = "SELECT
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '" . $bId . "'";

$rs = $conn->Execute($sql);

$data = $rs->fields;

require_once __DIR__ . '/taishin01_pdf_file.php';
