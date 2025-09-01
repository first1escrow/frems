<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';

$data = $_POST;

if ($data['query'] == 'ok') {
    $sql      = '';
    $join_sql = '';

    if ($data['iDate']) {
        $data['iDate'] = str_replace('-', '/', $data['iDate']);
        $sql .= ' AND a.cInvoiceDate="' . $data['iDate'] . '" ';
    }

    if ($data['iNo']) {
        $sql .= ' AND a.cInvoiceNo="' . $data['iNo'] . '" ';
    }

    if ($data['iCertifiedId']) {
        $sql .= ' AND a.cCertifiedId="' . $data['iCertifiedId'] . '" ';
    }

    if ($data['iName']) {
        $sql .= ' AND a.cName="' . $data['iName'] . '" ';
    }

    // 2022-07-01 修改經辦只會看到自己的案件
    if (($_SESSION['member_pDep'] == 5) && !in_array($_SESSION['member_id'], [1, 12])) {
        $sql .= ' AND b.cUndertakerId = "' . $_SESSION['member_id'] . '" ';
    }
    ##

    // $sql = 'SELECT a.* FROM tContractInvoiceQuery AS a JOIN tContractCase AS b ON a.cCertifiedId=b.cCertifiedId WHERE a.cObsolete = "N" ' . $sql . ' ORDER BY a.cInvoiceNo DESC, a.cId ASC;';
    $sql = 'SELECT a.* FROM tContractInvoiceQuery AS a JOIN tContractCase AS b ON a.cCertifiedId=b.cCertifiedId WHERE a.cObsolete = "N" ' . $sql . ' ORDER BY a.cInvoiceDate, a.cInvoiceNo DESC;';
    // exit($sql);

    $conn = new first1DB;
    $rs   = $conn->all($sql);

    $list = [];
    if (!empty($rs)) {
        foreach ($rs as $k => $v) {
            $v['cMoney'] = number_format($v['cMoney']);

            if ($v['cQuery'] == 'N') {
                $v['cQuery'] = '<span style="color:red">' . $v['cQuery'] . '</span>';
            }

            $v['cPrint'] = inv_print($conn, $v['cTB'], $v['cTargetId'], $v['cCertifiedId']);
            $v['iden']   = inv_iden($v['cTB']);

            $v['member_name'] = getMemberName($conn, $v['cCertifiedId']);

            $list[] = $v;

            $rs[$k] = null;
            unset($rs[$k]);
        }
    }

}

function inv_iden($tb)
{
    if ($tb == 'tContractRealestate_R' || $tb == 'tContractRealestate_R1' || $tb == 'tContractRealestate_R2') {
        $iden = '仲介';
    } elseif ($tb == 'tContractBuyer' || $tb == 'tContractInvoiceExt_B' || $tb == 'tContractOthers_B') {
        $iden = '買方';
    } elseif ($tb == 'tContractOwner' || $tb == 'tContractInvoiceExt_O' || $tb == 'tContractOthers_O') {
        $iden = '賣方';
    } elseif ($tb == 'tContractScrivener') {
        $iden = '地政士';
    }

    return $iden;
}

function inv_print(&$conn, $tb, $id, $cid) //20150917++

{
    if ($tb == 'tContractRealestate_R' || $tb == 'tContractRealestate_R1' || $tb == 'tContractRealestate_R2') {
        $tmp = explode('_', $tb);

        // $sql = "SELECT * FROM ".$tmp[0]." WHERE cCertifyId = '".$cid."' ";
        $sql = "SELECT cInvoicePrint, cInvoicePrint1, cInvoicePrint2 FROM " . $tmp[0] . " WHERE cCertifyId = '" . $cid . "' ";
        $rs  = $conn->one($sql);

        if ($tmp[1] == 'R') {
            $data = $rs['cInvoicePrint'];
        } elseif ($tmp[1] == 'R1') {
            $data = $rs['cInvoicePrint1'];
        } elseif ($tmp[1] == 'R2') {
            $data = $rs['cInvoicePrint2'];
        }

    } else {
        $tmp = explode('_', $tb);

        // $sql = "SELECT * FROM ".$tmp[0]." WHERE cId ='".$id."'";
        $sql  = "SELECT cInvoicePrint FROM " . $tmp[0] . " WHERE cId ='" . $id . "'";
        $rs   = $conn->one($sql);
        $data = $rs['cInvoicePrint'];
    }

    return $data;
}

function getMemberName(&$conn, $cCertifiedId)
{
    $sql = '
		SELECT
			(SELECT pName FROM tPeopleInfo WHERE pId=b.sUndertaker1) as undertaker
		FROM
			tContractScrivener AS a
		JOIN
			tScrivener as b ON b.sId=a.cScrivener
		WHERE
			a.cCertifiedId = :cId
	;';

    $rs = $conn->one($sql, ['cId' => $cCertifiedId]);

    return empty($rs['undertaker']) ? '' : $rs['undertaker'];
}

$smarty->assign('q', $data);
$smarty->assign('list', $list);
if ($_SESSION['member_invoice'] == 1) {
    $smarty->display('invoiceModifyData.inc.tpl', '', 'accounting');
} elseif ($_SESSION['member_invoice'] == 0) {
    $smarty->display('invoiceModifyData_view.inc.tpl', '', 'accounting');
}