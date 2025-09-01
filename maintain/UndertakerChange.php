<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
// require_once dirname(__FILE__).'/rc4.php' ;
// include_once 'rc4.php';

$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);

if ($_POST) {
    if ($_POST['undertaker'] > 0 && $_POST['o_undertaker'] > 0) {

        if ($_POST['city'] != "0" && $_POST['city'] != '') {
            ##區域##
            $area = '';
            if ($_POST['area'] != "0" && $_POST['area'] != '') { //只有區域
                $sql   = "SELECT zArea FROM tZipArea WHERE zZip = '" . $_POST['area'] . "'";
                $rs    = $conn->Execute($sql);
                $area  = $rs->fields['zArea'];
                $zip[] = $_POST['area'];
            } else { //縣市
                $sql = "SELECT zZip FROM tZipArea WHERE zCity ='" . $_POST['city'] . "'";
                $rs  = $conn->Execute($sql);
                while (!$rs->EOF) {
                    $zip[] = $rs->fields['zZip'];

                    $rs->MoveNext();
                }
            }
            ##經辦##
            //新
            $sql   = "SELECT pName FROM tPeopleInfo WHERE pId ='" . $_POST['undertaker'] . "'";
            $rs    = $conn->Execute($sql);
            $pName = $rs->fields['pName'];
            //原本的
            $sql    = "SELECT pName FROM tPeopleInfo WHERE pId ='" . $_POST['o_undertaker'] . "'";
            $rs     = $conn->Execute($sql);
            $pName2 = $rs->fields['pName'];
            ####
            // print_r($zip);
            $str = (date('Y') - 1911) . '.' . date('m.d') . $_POST['city'] . $area . "經辦" . $pName2 . "改為" . $pName;
            $sql = "INSERT INTO tNote SET nCategory = 3,nContent ='" . $str . "',nModifyId = '" . $_SESSION['member_id'] . "'";
            // echo $sql."<br>";
            $conn->Execute($sql);

            $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) AS sUndertakerName,sId,sRemark3,sUndertaker1 FROM tScrivener WHERE sCpZip1 IN(" . @implode(',', $zip) . ") AND sUndertaker1 = '" . $_POST['o_undertaker'] . "'";
            // echo $sql;

            $rs = $conn->Execute($sql);
            while (!$rs->EOF) {
                if ($rs->fields['sUndertaker1'] != $_POST['undertaker']) {
                    $str = (date('Y') - 1911) . '.' . date('m.d') . '經辦' . $rs->fields['sUndertakerName'] . "改為" . $pName;
                    setUndertaker($str, $rs->fields['sId'], $_POST['undertaker'], $rs->fields['sRemark3']);

                }
                $rs->MoveNext();
            }

        } else {
            //新
            $sql   = "SELECT pName FROM tPeopleInfo WHERE pId ='" . $_POST['undertaker'] . "'";
            $rs    = $conn->Execute($sql);
            $pName = $rs->fields['pName'];
            //原本的
            $sql    = "SELECT pName FROM tPeopleInfo WHERE pId ='" . $_POST['o_undertaker'] . "'";
            $rs     = $conn->Execute($sql);
            $pName2 = $rs->fields['pName'];

            $str = (date('Y') - 1911) . '.' . date('m.d') . $_POST['city'] . $area . "經辦" . $pName2 . "改為" . $pName;
            $sql = "INSERT INTO tNote SET nCategory = 3,nContent ='" . $str . "',nModifyId = '" . $_SESSION['member_id'] . "'";
            // echo $sql."<br>";
            $conn->Execute($sql);

            $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) AS sUndertakerName,sId,sRemark3,sUndertaker1 FROM tScrivener WHERE sUndertaker1 = '" . $_POST['o_undertaker'] . "'";

            $rs = $conn->Execute($sql);
            while (!$rs->EOF) {
                if ($rs->fields['sUndertaker1'] != $_POST['undertaker']) {
                    $str = (date('Y') - 1911) . '.' . date('m.d') . '經辦' . $rs->fields['sUndertakerName'] . "改為" . $pName;
                    setUndertaker($str, $rs->fields['sId'], $_POST['undertaker'], $rs->fields['sRemark3']);

                }
                $rs->MoveNext();
            }
        }

        //
    }
}
##

$city[0] = '請選擇';
$sql     = "SELECT zCity FROM tZipArea GROUP BY zCity ORDER BY nid ASC";
$rs      = $conn->Execute($sql);

while (!$rs->EOF) {
    $city[$rs->fields['zCity']] = $rs->fields['zCity'];

    $rs->MoveNext();
}
##
$undertaker[0] = '請選擇';
$sql           = "SELECT pId,pName FROM tPeopleInfo WHERE pJob= 1 AND (pDep = 5 OR pDep = 6)";
$rs            = $conn->Execute($sql);
while (!$rs->EOF) {
    $undertaker[$rs->fields['pId']] = $rs->fields['pName'];

    $rs->MoveNext();
}
##
$sql = "SELECT nContent,nModifyTime,(SELECT pName FROM tPeopleInfo WHERE pId=nModifyId) AS Name FROM tNote WHERE nCategory = 3";
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $note[] = $rs->fields;

    $rs->MoveNext();
}
##
function setUndertaker($str, $sId, $pId, $remark)
{
    global $conn;

    $remark = str_replace($str, '', $remark);

    $str = ($remark) ? $remark . ';' . $str : $str;

    $sql = "UPDATE tScrivener SET sRemark3 = '" . $str . "',sUndertaker1 = '" . $pId . "' WHERE sId = '" . $sId . "'";
    $conn->Execute($sql);
}

###
$smarty->assign('note', $note);
$smarty->assign('undertaker', $undertaker);
$smarty->assign('city', $city);
$smarty->display('UndertakerChange.inc.tpl', '', 'maintain');
