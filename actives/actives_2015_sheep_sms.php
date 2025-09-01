<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/sms/sms_function_act.php';

$cat    = addslashes(trim($_GET['cat'])); //類別
$check  = addslashes(trim($_POST['check'])); //
$people = $_POST['people'];

$date_start = "2015-03-01 00:00:00";
$date_end   = "2015-08-31 23:59:59";

$sms = new SMS_Gateway();

//

if ($check == 'send') {
    // $sms->send('14碼保證號碼' , '地政士id', '仲介店id', 'cheque', 'tExpense_cheque id', 'n', 0);
    $file = "actives_2015_sheep_sms_result.inc.tpl";

    // $sms->send_act($date_start,$date_end,'sheep',  'y', $people);

    $scrivener = $sms->send_act($date_start, $date_end, 'sheep', 'n', $people);

} else {
    $file = "actives_2015_sheep_sms.inc.tpl";

//進行中、已結案
    ##
    $sql = " SELECT
			cc.cCertifiedId AS cCertifiedId,
			cc.cSignDate AS cSignDate,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cs.cScrivener

		 FROM
		 	tContractCase AS cc
		 LEFT JOIN
		 	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		 LEFT JOIN
		 	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId

		 WHERE
		 	cc.cSignDate >='" . $date_start . "'
		 	AND cc.cSignDate<='" . $date_end . "'
		 	AND cc.cCaseStatus IN (2,3)
		 ORDER BY cs.cScrivener ASC
		";

    $rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
    $i = 0;
    while (!$rs->EOF) {

        $list[$i] = $rs->fields;

        $list[$i]['cBrand']  = point($list[$i]['cBrand']);
        $list[$i]['cBrand1'] = point($list[$i]['cBrand1']);
        $list[$i]['cBrand2'] = point($list[$i]['cBrand2']);

        // echo $list[$i]['cBrand']."-".$list[$i]['cBrand1']."-".$list[$i]['cBrand2']."<br>";

        if ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] != 0) {

            $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1'], $list[$i]['cBrand2']);

        } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] == 0) {

            $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1']);

        } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] == 0 && $list[$i]['cBrand2'] == 0) {

            $brand = array($list[$i]['cBrand']);
        }

        rsort($brand);

        $arr[$list[$i]['cScrivener']]['point']      = $arr[$list[$i]['cScrivener']]['point'] + $brand[0];
        $arr[$list[$i]['cScrivener']]['cScrivener'] = $list[$i]['cScrivener'];

        if ($brand[0] == 1) { //台屋

            $arr[$list[$i]['cScrivener']]['tw_point'] = $arr[$list[$i]['cScrivener']]['tw_point'] + $brand[0];
        } elseif ($brand[0] == 2) { //非台屋
            $arr[$list[$i]['cScrivener']]['untw_point'] = $arr[$list[$i]['cScrivener']]['untw_point'] + $brand[0];
        }

        unset($brand);
        $i++;
        $rs->MoveNext();
    }
    unset($list);
// echo "<pre>";
    // print_r($arr);
    // echo "</pre>";
    // die;

##
    $sql = "SELECT
			cc.cCertifiedId AS cCertifiedId,
			cc.cSignDate AS cSignDate,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cs.cScrivener,
			(SELECT cTotalMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId =cc.cCertifiedId) AS totalMoney,
			(SELECT cCertifiedMoney FROM tContractIncome AS ci WHERE ci.cCertifiedId =cc.cCertifiedId) AS cCertifiedMoney

		 FROM
		 	tContractCase AS cc
		 LEFT JOIN
		 	tContractScrivener AS cs ON cs.cCertifiedId=cc.cCertifiedId
		 LEFT JOIN
		 	tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId

		 WHERE
		 	cc.cSignDate >='" . $date_start . "'
		 	AND cc.cSignDate<='" . $date_end . "'
		 	AND cc.cCaseStatus = 4

		 ORDER BY cs.cScrivener ASC";

    $rs = $conn->Execute($sql);
// $total=$rs->RecordCount();//計算總筆數
    $i = 0;
    while (!$rs->EOF) {

        $list[$i] = $rs->fields;

        $list[$i]['cBrand']  = point($list[$i]['cBrand']);
        $list[$i]['cBrand1'] = point($list[$i]['cBrand1']);
        $list[$i]['cBrand2'] = point($list[$i]['cBrand2']);

        // echo $list[$i]['cBrand']."-".$list[$i]['cBrand1']."-".$list[$i]['cBrand2']."<br>";

        if ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] != 0) {

            $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1'], $list[$i]['cBrand2']);

        } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] != 0 && $list[$i]['cBrand2'] == 0) {

            $brand = array($list[$i]['cBrand'], $list[$i]['cBrand1']);

        } elseif ($list[$i]['cBrand'] != 0 && $list[$i]['cBrand1'] == 0 && $list[$i]['cBrand2'] == 0) {

            $brand = array($list[$i]['cBrand']);
        }

        rsort($brand);

        //算點數的

        $totalMoney_cmoney = $list[$i]['totalMoney'] * 0.0006;

        if ($list[$i]['cCertifiedMoney'] == (string) $totalMoney_cmoney) {

            $arr[$list[$i]['cScrivener']]['point']      = $arr[$list[$i]['cScrivener']]['point'] + $brand[0];
            $arr[$list[$i]['cScrivener']]['cScrivener'] = $list[$i]['cScrivener'];

            if ($brand[0] == 1) { //台屋

                $arr[$list[$i]['cScrivener']]['tw_point'] = $arr[$list[$i]['cScrivener']]['tw_point'] + $brand[0];
            } elseif ($brand[0] == 2) { //非台屋
                $arr[$list[$i]['cScrivener']]['untw_point'] = $arr[$list[$i]['cScrivener']]['untw_point'] + $brand[0];
            }
        } else {

        }

        ##
        unset($brand);
        $i++;
        $rs->MoveNext();
    }
    unset($list);

    $sql = "
			SELECT
				sName AS MainName,
				sOffice AS MainOffice,
				sId
			FROM
				tScrivener
			WHERE
				sStatus=1
				ORDER BY sId ASC";

    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {

        $list[] = $rs->fields;

        $rs->MoveNext();
    }
    // echo "<pre>";
    // print_r($list);
    // echo "</pre>";
    // die;
    for ($i = 0; $i < count($list); $i++) {
        // $list[$i]['MainOffice'] //$arr[$list[$i]['sId']]['point']
        $sql = "SELECT sId,sName,sMobile FROM tScrivenerSms WHERE sScrivener ='" . $list[$i]['sId'] . "' AND sNID IN('1','20','22','23','24')";

        $rs = $conn->Execute($sql);
        // $j=0;
        while (!$rs->EOF) {

            if ($arr[$list[$i]['sId']]['point'] >= 10) {
                if ($arr[$list[$i]['sId']]['point'] == '') {
                    $arr[$list[$i]['sId']]['point'] = 0;
                }

                $scrivener[$j]['sId']        = $rs->fields['sId'];
                $scrivener[$j]['point']      = $arr[$list[$i]['sId']]['point'];
                $scrivener[$j]['cScrivener'] = $arr[$list[$i]['sId']]['cScrivener'];
                $scrivener[$j]['office']     = $list[$i]['MainOffice'];
                $scrivener[$j]['MainName']   = $list[$i]['MainName'];
                $scrivener[$j]['smsName']    = $rs->fields['sName'];
                $scrivener[$j]['smsMobile']  = $rs->fields['sMobile'];

                $j++;
            }

            $rs->MoveNext();
        }

        unset($sms);
    }
}

function point($brand)
{
    if ($brand == 1 || $brand == 49) {

        $point = 1;
    } else if ($brand != 0) {

        $point = 2;
    } else {
        $point = 0; //
    }

    return $point;
}
##

##
$smarty->assign('scrivener', $scrivener);
$smarty->display($file, '', 'actives');
