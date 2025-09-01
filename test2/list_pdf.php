<?php
require_once '../checklist/fpdf/chinese-unicode.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

// 取得保證號碼
$_REQUEST = escapeStr($_REQUEST) ;
$cCertifiedId = $_REQUEST['cCertifiedId'] ;
$iden = $_REQUEST['iden'] ;

include_once '../checklist/checklist_pdf.php';

?>