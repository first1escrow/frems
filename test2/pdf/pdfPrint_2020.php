<?php
include_once '../../configs/config.class.php';
include_once '../../session_check.php' ;
include_once '../../openadodb.php';
include_once dirname(dirname(dirname(__FILE__))).'/checklist/fpdf/chinese-unicode.php' ;
$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;


include_once 'pdfPrint_2020_pdf.php';

?>