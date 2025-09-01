<?php
// ini_set("display_errors", "On");
// error_reporting(E_ALL & ~E_NOTICE);
include_once '../configs/config.class.php';
include_once '../class/SmartyMain.class.php';
include_once '../class/sms.class.php';
include_once '../class/contract.class.php';
include_once '../session_check.php';
include_once '../openadodb.php';

// 不需要初始化 Smarty 對象，因為 SmartyMain.class.php 已經建立了全域 $smarty 對象

$certified_id = isset($_GET['certified_id']) ? trim($_GET['certified_id']) : '';
$scid         = isset($_GET['scid']) ? trim($_GET['scid']) : '';
$ok           = isset($_GET['ok']) ? trim($_GET['ok']) : '';

$cSignCategory = isset($_GET['cSignCategory']) ? trim($_GET['cSignCategory']) : '';

$sql = "SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId = '" . $certified_id . "'";

$rs = $conn->Execute($sql);

if ($rs->fields['cCaseStatus'] == 2) {
    //找出案件預設簡訊對象
    $sql = '
		SELECT
			cs.cSmsTarget,
			cs.cSmsTargetName,
			cs.csend,
			cs.cSendName2,
			cs.cManage,
			cs.cManage2,
			cs.cSend2,
			(SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId=cs.cCertifiedId) AS cCaseStatus
		FROM
			tContractScrivener AS cs
		WHERE
			cs.cCertifiedId="' . $certified_id . '"
			AND cs.cScrivener="' . $scid . '" ;
	';
    // echo 'SQL='.$sql ;
    $rs           = $conn->Execute($sql);
    $tgArr        = $rs->fields;
    $cCaseStatus  = $rs->fields['cCaseStatus'];
    $SmsScrTarget = [];
    $send         = [];

                                      // $send=$tgArr['cSend2'];//是否接收仲介服務費簡訊通知
                                      //取得勾選的簡訊對象
    if ($tgArr['cSmsTarget'] == '') { //若無預設之簡訊對象，則將地政士預設的簡訊對象複製一份過來

        //取出地政士的預設紀錄
        $sql = 'SELECT sMobile,sSend,sDefault,sName FROM tScrivenerSms WHERE sScrivener="' . $scid . '" AND sDel = 0 AND sCheck_id = "" AND sLock = 0 ORDER BY sNID,sId ASC;';

        $rs        = $conn->Execute($sql);
        $smsTarget = [];

        while (! $rs->EOF) {
            $tmp = $rs->fields;
            // $smsTarget[] = $tmp['sMobile'] ;

            if ($tmp['sDefault'] == 1) {
                $smsTarget[] = $tmp['sMobile'];
                $name[]      = $tmp['sName'];
            }

            if ($tmp['sSend'] == 1) {
                $send[]  = $tmp['sMobile'];
                $name2[] = $tmp['sName'];
            }
            unset($tmp);

            $i++;
            $rs->MoveNext();
        }
        ##

        //複製到案件的預設簡訊對象
        if ($smsTarget) {
            $sql = 'UPDATE tContractScrivener SET cSmsTarget="' . implode(',', $smsTarget) . '",cSmsTargetName="' . implode(',', $name) . '",cSend2 = "' . implode(',', $send) . '",cSendName2="' . implode(',', $name2) . '" WHERE cCertifiedId="' . $certified_id . '" AND cScrivener="' . $scid . '";';

            $conn->Execute($sql);
        }
        ##

        $SmsScrTarget = $smsTarget;
    } else { //已有預設的簡訊發送對象
        $SmsScrTarget = explode(',', $tgArr['cSmsTarget']);
        $send         = explode(',', $tgArr['cSend2']);

        if ($tgArr['cSmsTargetName']) {
            $name = explode(',', $tgArr['cSmsTargetName']);
        }

        if ($tgArr['cSendName2']) {
            $name2 = explode(',', $tgArr['cSendName2']);
        }

    }
    ##

    ##

    //建立簡訊列表
    $sql = '
		SELECT
			a.sId AS sId,
			a.sNID as id,
			b.tTitle as tTitle,
			a.sName as sName,
			a.sMobile as sMobile,
			a.sSend  as sSend,
			a.sCheck_id as sCheck_id

		FROM
			tScrivenerSms AS a
		JOIN
			tTitle_SMS AS b ON b.id=a.sNID
		WHERE
			a.sScrivener="' . $scid . '"
			AND b.tKind="1"
			AND a.sDel = 0
			AND sLock = 0
		ORDER BY
			b.tTitle,a.sId
		ASC;
	';

    $rs = $conn->Execute($sql);
//初始化變數
    $book     = [];
    $i        = 0;
    $j        = 0;
    $otherSms = [];

    while (! $rs->EOF) {
        // echo $rs->fields['sCheck_id']."_";
        if ($rs->fields['sCheck_id'] == '') {

            $book[$i] = $rs->fields;
            foreach ($SmsScrTarget as $k => $v) {
                if ($book[$i]['sMobile'] == $v) {
                    // if (is_array($name)) {
                    //     if ($book[$i]['sName'] ==$name[$k]) {
                    //         $book[$i]['isSelect'] = '1' ;
                    //     }
                    // }else{
                    $book[$i]['isSelect'] = '1';
                    // }

                }
            }

            foreach ($send as $key => $value) {

                if ($book[$i]['sMobile'] == $value) {
                    // if (is_array($name2)) {
                    //     if ($book[$i]['sName'] ==$name2[$key]) {
                    //         $book[$i]['sSend2']=1;
                    //     }
                    // }else{
                    $book[$i]['sSend2'] = 1;
                    // }

                }
            }

            if ($tgArr['cManage'] == $book[$i]['sId']) {

                $book[$i]['isManage'] = '1';
            }

            if ($tgArr['cManage2'] == $book[$i]['sId']) {

                $book[$i]['isManage2'] = '1';
            }

            $i++;
        } else if ($rs->fields['sCheck_id'] == $certified_id) {
            // echo $rs->fields['sCheck_id'];
            $otherSms[$j] = $rs->fields;

            foreach ($SmsScrTarget as $k => $v) {
                if ($otherSms[$j]['sMobile'] == $v) {
                    // if (is_array($name)) {
                    //     if ($otherSms[$j]['sName'] ==$name[$k]) {
                    //         $otherSms[$j]['isSelect'] = '1' ;
                    //     }
                    // }else{
                    $otherSms[$j]['isSelect'] = '1';
                    // }
                }

            }

            foreach ($send as $key => $value) {

                if ($otherSms[$j]['sMobile'] == $value) {
                    // if (is_array($name2)) {
                    //     if ($otherSms[$j]['sName'] ==$name2[$key]) {
                    //         $otherSms[$j]['sSend2']=1;
                    //     }
                    // }else{
                    $otherSms[$j]['sSend2'] = 1;
                    // }

                }
            }

            if ($tgArr['cManage'] == $otherSms[$j]['sId']) {

                $otherSms[$j]['isManage'] = '1';
            }

            if ($tgArr['cManage2'] == $otherSms[$j]['sId']) {

                $otherSms[$j]['isManage2'] = '1';
            }

            $j++;
        }

        $rs->MoveNext();
    }

    if (is_array($otherSms)) {
        $book = array_merge($book, $otherSms);
    }

    ##

    //是否顯示"已更新"
    $dialog = '';
    if ($ok == '1') {
        $dialog = '
			$("#dialog").html("簡訊對象已更新!!") ;
			$("#dialog").dialog({
				modal: true,
				buttons: {
					"OK": function () {
						$(this).dialog("close") ;
					}
				}
			}) ;
		';
    }
} else {
    $sql = '
		SELECT
			cs.cSmsTarget,
			cs.cSmsTargetName,
			cs.csend,
			cs.cSendName2,
			cs.cManage,
			cs.cManage2,
			cs.cSend2,
			(SELECT cCaseStatus FROM tContractCase AS cc WHERE cc.cCertifiedId=cs.cCertifiedId) AS cCaseStatus
		FROM
			tContractScrivener AS cs
		WHERE
			cs.cCertifiedId="' . $certified_id . '"
			AND cs.cScrivener="' . $scid . '" ;
	';

    // echo 'SQL='.$sql ;
    $rs           = $conn->Execute($sql);
    $tgArr        = $rs->fields;
    $cCaseStatus  = $rs->fields['cCaseStatus'];
    $SmsScrTarget = [];
    $send         = [];

    $count = 0;
    //簡訊發送對象
    $SmsScrTarget = explode(',', $tgArr['cSmsTarget']);

    if ($tgArr['cSmsTargetName']) {
        $name = explode(',', $tgArr['cSmsTargetName']);
    }

    for ($i = 0; $i < count($SmsScrTarget); $i++) {
        // $tmp3 = getScrSMS($scid,'',$SmsScrTarget[$i],$name[$i]);
        $ScrSms = getScrSMS($scid, '', $SmsScrTarget[$i], $name[$i]);
        foreach ($ScrSms as $k => $v) {
            $book[$SmsScrTarget[$i] . $v['sName']]['tTitle']   = $v['tTitle'];
            $book[$SmsScrTarget[$i] . $v['sName']]['sMobile']  = $SmsScrTarget[$i];
            $book[$SmsScrTarget[$i] . $v['sName']]['sName']    = $v['sName'];
            $book[$SmsScrTarget[$i] . $v['sName']]['isSelect'] = 1;
        }

        unset($ScrSms);

        $count++;
    }

    unset($SmsScrTarget);unset($name);
    //是否接收仲介服務費簡訊通知
    $send = explode(',', $tgArr['cSend2']);

    if ($tgArr['cSendName2']) {
        $name2 = explode(',', $tgArr['cSendName2']);
    }

    for ($i = 0; $i < count($send); $i++) {
        if ($send[$i] != '') {
            $ScrSms = getScrSMS($scid, '', $send[$i], $name[$i]);
            // print_r($ScrSms);
            foreach ($ScrSms as $k => $v) {
                $book[$send[$i] . $v['sName']]['tTitle']  = $v['tTitle'];
                $book[$send[$i] . $v['sName']]['sMobile'] = $send[$i];
                $book[$send[$i] . $v['sName']]['sName']   = $v['sName'];
                $book[$send[$i] . $v['sName']]['sSend2']  = 1;
            }
        }

        unset($ScrSms);
    }
    unset($send);unset($name2);
    //承辦地政士
    if ($tgArr['cManage'] != 0) {
        $ScrSms = getScrSMS($scid, $tgArr['cManage']);
        // print_r($ScrSms);
        if ($ScrSms) {
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['tTitle']   = $ScrSms[0]['tTitle'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['sMobile']  = $ScrSms[0]['sMobile'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['sName']    = $ScrSms[0]['sName'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['isManage'] = 1;
        }
        unset($ScrSms);
    }

    //承辦地政士助理
    if ($tgArr['cManage2'] != 0) {
        $ScrSms = getScrSMS($scid, $tgArr['cManage2']);
        if ($ScrSms) {
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['tTitle']    = $ScrSms[0]['tTitle'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['sMobile']   = $ScrSms[0]['sMobile'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['sName']     = $ScrSms[0]['sName'];
            $book[$ScrSms[0]['sMobile'] . $ScrSms[0]['sName']]['isManage2'] = 1;

        }

        unset($ScrSms);
    }

}

function getScrSMS($scid, $id, $mobile = '', $name = '')
{
    global $conn;

    $query = '';
    if ($name) {$query .= 'AND a.sName = "' . $name . '"';}
    if ($mobile) {$query .= 'AND a.sMobile = "' . $mobile . '"';}
    if ($id) {$query .= 'AND a.sId = "' . $id . '"';}

    //建立簡訊列表
    $sql = '
		SELECT
			a.sId AS sId,
			a.sNID as id,
			b.tTitle as tTitle,
			a.sName as sName,
			a.sMobile as sMobile,
			a.sSend  as sSend
		FROM
			tScrivenerSms AS a
		JOIN
			tTitle_SMS AS b ON b.id=a.sNID
		WHERE
			a.sScrivener="' . $scid . '"
			' . $query . '
			AND b.tKind="1"

		ORDER BY
			b.tTitle,a.sId
		ASC;
	';
    // if ($mobile == '0980424726') {
    //     echo $sql."<br>";
    // }

    $rs = $conn->Execute($sql);
    while (! $rs->EOF) {
        $data[] = $rs->fields;

        $rs->MoveNext();
    }
    return $data;
}

##
// 確保所有 $book 元素都有必要的鍵
if (! empty($book)) {
    foreach ($book as &$item) {
        if (! isset($item['isManage'])) {
            $item['isManage'] = '0';
        }
        if (! isset($item['isManage2'])) {
            $item['isManage2'] = '0';
        }
        if (! isset($item['isSelect'])) {
            $item['isSelect'] = '0';
        }
        if (! isset($item['sSend2'])) {
            $item['sSend2'] = '0';
        }
    }
    unset($item); // 解除引用
}

$smarty->assign('cSignCategory', $cSignCategory);
$smarty->assign('book', $book);
$smarty->assign('certified_id', $certified_id);
$smarty->assign('sScrivener', $scid);
$smarty->assign('dialog', $dialog);
$smarty->assign('send', $send);
$smarty->assign('cCaseStatus', $cCaseStatus);
$smarty->display('formcasesms.inc.tpl', '', 'escrow');
