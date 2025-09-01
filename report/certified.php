<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$category = '';

$sql = 'SELECT cId,cName FROM tCategoryBranch ORDER BY cId ASC;';
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $category .= "<option value='" . $rs->fields['cId'] . "'>" . $rs->fields['cName'] . "</option>\n";
    $rs->MoveNext();
}

$sql    = 'SELECT * FROM tStatusCase ORDER BY sId ASC;';
$rs     = $conn->Execute($sql);
$status = '';

while (!$rs->EOF) {
    $status .= '<option value="' . $rs->fields['sId'] . '">' . $rs->fields['sName'] . "</option>\n";
    $rs->MoveNext();
}

$smarty->assign('status', $status);
$smarty->assign('category', $category);

$smarty->display('certified.inc.tpl', '', 'report');
