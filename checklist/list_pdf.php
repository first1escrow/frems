<?php
require_once __DIR__ . '/fpdf/chinese-unicode.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

// 取得保證號碼
$_REQUEST     = escapeStr($_REQUEST);
$cCertifiedId = $_REQUEST['cCertifiedId'];
$iden         = $_REQUEST['iden'];

require_once __DIR__ . '/checklist_pdf.php';
