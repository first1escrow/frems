<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

##
$_POST = escapeStr($_POST);
$_GET  = escapeStr($_GET);
$id    = empty($_POST["scid"])
? $_GET["scid"]
: $_POST["scid"];

//建立簡訊列表
$sql = '
	SELECT
		a.sId as sn,
		a.sNID as id,
		a.sName as sName,
		a.sMobile as sMobile,
		a.sDefault as sDefault,
		a.sSend as sSend,
		a.sLock,
		b.tTitle as tTitle
	FROM
		tScrivenerSms AS a
	JOIN
		tTitle_SMS AS b ON b.id=a.sNID
	WHERE
		b.tKind <> "0"
		AND a.sScrivener = "' . $id . '"
		AND a.sDel = 0
		AND a.sCheck_id =""
	ORDER BY
		b.tTitle,a.sId
	ASC ;
';
//echo 'SQL='.$sql ;
$rs   = $conn->Execute($sql);
$data = array();
$i    = 0;
while (!$rs->EOF) {

    $data[$i] = $rs->fields;

    $data[$i]['readonly']      = ($rs->fields['sLock'] == 0) ? '' : 'readonly=readonly';
    $data[$i]['readonlystyle'] = ($rs->fields['sLock'] == 0) ? '' : 'background-color: rgba(239, 239, 239, 0.3);border-color: rgba(118, 118, 118, 0.3);color:rgb(84, 84, 84)';
    $data[$i]['disabled']      = ($rs->fields['sLock'] == 0) ? '' : 'disabled=disabled';
    $data[$i]['lock']          = ($rs->fields['sLock'] == 0) ? '隱藏' : '顯示';

    $i++;
    $rs->MoveNext();
}
##

//建立簡訊對象身分
$sms_sNID = '';
$sql      = 'SELECT * FROM tTitle_SMS WHERE tKind="1" GROUP BY tTitle ORDER BY tTitle ASC;';
$rs       = $conn->Execute($sql);
while ($tmp = $rs->fields) {
    $sms_sNID .= '<option value="' . $tmp['id'] . '">' . $tmp['tTitle'] . "</option>\n";
    unset($tmp);
    $rs->MoveNext();
}
##
$sql                              = "SELECT sSmsLocationMark FROM tScrivener WHERE sId = '" . $id . "'";
$rs                               = $conn->Execute($sql);
$dataScrivner['sSmsLocationMark'] = $rs->fields['sSmsLocationMark'];
##
$smarty->assign('menu_choice', array('1' => '是', '0' => '否'));
$smarty->assign('data', $data);
$smarty->assign('dataScrivner', $dataScrivner);
$smarty->assign('sms_sNID', $sms_sNID);
$smarty->assign('scid', $id);
$smarty->display('formscrivenersms.inc.tpl', '', 'maintain');
