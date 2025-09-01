<?php

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/includes/writelog.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';

# 設定檔案存放目錄位置
$uploaddir = __DIR__ . '/excel/';
if (!is_dir($uploaddir)) {
    mkdir($uploaddir, 0777, true);
}
##

// # 設定檔案名稱

$uploadfile = $_FILES['upload_file']['name'];
$uploadfile = $uploaddir . $uploadfile;

//
if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
    $xls = $uploaddir . $_FILES["upload_file"]["name"];
} else {
    die("檔案上傳錯誤");
}
##

//讀取 excel 檔案

$objReader = new PHPExcel_Reader_Excel2007();
$objReader->setReadDataOnly(true);

//檔案名稱
$objPHPExcel  = $objReader->load($xls);
$currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
$allLine      = $currentSheet->getHighestRow(); //取得總列數

$i = 0;
for ($excel_line = 3; $excel_line <= $allLine; $excel_line++) {

    $list[$i]['code'] = $currentSheet->getCell("A{$excel_line}")->getValue(); //編號

    if (mb_substr(trim($list[$i]['code']), 0, 2) == 'SC') {
        $list[$i]['type'] = 1; //地政
    } else {
        $list[$i]['type'] = 2; //仲介
    }

    $list[$i]['id'] = (int) mb_substr(trim($list[$i]['code']), 2);

    $list[$i]['inv_id']      = $currentSheet->getCell("B{$excel_line}")->getValue(); //回饋方式(2:整批 3:結案)
    $list[$i]['name']        = $currentSheet->getCell("C{$excel_line}")->getValue(); //姓名/抬頭
    $list[$i]['phone']       = $currentSheet->getCell("D{$excel_line}")->getValue(); //店長行動電話
    $list[$i]['iden']        = $currentSheet->getCell("E{$excel_line}")->getValue(); //身份別(2:身分證編號 3:統一編號 4:護照號碼)
    $list[$i]['iden_number'] = $currentSheet->getCell("F{$excel_line}")->getValue(); //證件號碼
    $list[$i]['title']       = $currentSheet->getCell("G{$excel_line}")->getValue(); //收件人稱謂
    $list[$i]['cZip']        = trim($currentSheet->getCell("H{$excel_line}")->getValue()); //聯絡地址郵遞區號

    $tmp = getAddr($list[$i]['cZip']);

    $list[$i]['cAddr'] = trim($currentSheet->getCell("I{$excel_line}")->getValue()); //聯絡地址
    $list[$i]['cAddr'] = str_replace($tmp, '', $list[$i]['cAddr']);
    unset($tmp);

    $list[$i]['rZip']  = trim($currentSheet->getCell("J{$excel_line}")->getValue()); //戶藉地址郵遞區號
    $list[$i]['rAddr'] = trim($currentSheet->getCell("K{$excel_line}")->getValue()); //戶藉地址

    $tmp               = getAddr($list[$i]['rZip']);
    $list[$i]['rAddr'] = str_replace($tmp, '', $list[$i]['rAddr']);
    unset($tmp);

    $list[$i]['email']           = $currentSheet->getCell("L{$excel_line}")->getValue(); //電子郵件
    $list[$i]['bank_id']         = $currentSheet->getCell("M{$excel_line}")->getValue(); //總行代號
    $list[$i]['bank_branchId']   = $currentSheet->getCell("N{$excel_line}")->getValue(); //分行代號
    $list[$i]['bank_name']       = $currentSheet->getCell("O{$excel_line}")->getValue(); //總行名稱
    $list[$i]['bank_branchName'] = $currentSheet->getCell("P{$excel_line}")->getValue(); //分行名稱
    $list[$i]['accounting']      = $currentSheet->getCell("Q{$excel_line}")->getValue(); //指定帳號
    $list[$i]['accName']         = $currentSheet->getCell("R{$excel_line}")->getValue(); //戶名
    $list[$i]['note']            = $currentSheet->getCell("S{$excel_line}")->getValue(); //發票總類(REC,INC)

    $i++;

}

$ver = 2;
switch ($ver) {
    case '1': //舊的
        import_v1($list);
        break;
    case '2': //新的
        import_v2($list);
        break;
    default:
        # code...
        break;
}

if (!is_array($list)) {
    die("檔案有問題");
}
##寫入資料庫

$tbl = "<div class='div-inline'><table class='tb' cellpadding='0' cellspacing='0'>";
$tbl .= "<tr cosplan=''><th>更新失敗店家</th></tr>";

if (is_array($error)) {
    for ($i = 0; $i < count($error); $i++) {
        $tbl .= "<tr>
					<td >" . $error[$i]['code'] . "</td>
				</tr>";
    }
} else {
    $tbl .= "<tr><td>無</td></tr>";
}
$tbl .= "</table><br></div>";

##
function import_v1($list)
{
    global $conn;
    for ($i = 0; $i < count($list); $i++) {

        if ($list[$i]['type'] == 1) { //地政

            $sql = "UPDATE
						tScrivener
					SET
						`sFeedBack` = '" . $list[$i]['inv_id'] . "',
	                    `sTtitle` = '" . $list[$i]['name'] . "',
	                    `sMobileNum2` = '" . $list[$i]['phone'] . "',
	                    `sIdentity` = '" . $list[$i]['iden'] . "',
	                    `sIdentityNumber` = '" . $list[$i]['iden_number'] . "',
	                    `sRtitle` = '" . $list[$i]['title'] . "',
	                    `sZip3` = '" . $list[$i]['cZip'] . "',
	                    `sAddr3` = '" . $list[$i]['cAddr'] . "',
	                    `sZip2f` = '" . $list[$i]['rZip'] . "',
	                    `sAddr2f` = '" . $list[$i]['rAddr'] . "',
	                    `sEmail2` = '" . $list[$i]['email'] . "',
	                    `sAccountNum5` = '" . $list[$i]['bank_id'] . "',
	                    `sAccountNum6` = '" . $list[$i]['bank_branchId'] . "',
	                    `sAccount7` = '" . $list[$i]['accounting'] . "',
	                    `sAccount8` = '" . $list[$i]['accName'] . "',
	                    `sfnote` = '" . $list[$i]['note'] . "'
	                   WHERE
	                   	 sId ='" . $list[$i]['id'] . "'
					";

        } elseif ($list[$i]['type'] == 2) {
            $sql = "UPDATE
						tBranch
					SET
						`bFeedBack` = '" . $list[$i]['inv_id'] . "',
	                    `bTtitle` = '" . $list[$i]['name'] . "',
	                    `bMobileNum2` = '" . $list[$i]['phone'] . "',
	                    `bIdentity` = '" . $list[$i]['iden'] . "',
	                    `bIdentityNumber` = '" . $list[$i]['iden_number'] . "',
	                    `bRtitle` = '" . $list[$i]['title'] . "',
	                    `bZip3` = '" . $list[$i]['cZip'] . "',
	                    `bAddr3` = '" . $list[$i]['cAddr'] . "',
	                    `bZip2` = '" . $list[$i]['rZip'] . "',
	                    `bAddr2` = '" . $list[$i]['rAddr'] . "',
	                    `bEmail2` = '" . $list[$i]['email'] . "',
	                    `bAccountNum5` = '" . $list[$i]['bank_id'] . "',
	                    `bAccountNum6` = '" . $list[$i]['bank_branchId'] . "',
	                    `bAccount7` = '" . $list[$i]['accounting'] . "',
	                    `bAccount8` = '" . $list[$i]['accName'] . "',
	                    `bfnote` = '" . $list[$i]['note'] . "'
	                   WHERE
	                   	 bId ='" . $list[$i]['id'] . "'
					";
        }

        if (!$conn->Execute($sql)) {
            $error[] = $list[$i];
        }

        unset($sql);
    }

    if (is_array($error)) {
        return $error;
    } else {
        return true;
    }
}

//動態新增
function import_v2($list)
{
    global $conn;

    setDelete($list);

    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]['id'] != 0 || $list[$i]['id'] != '') {
            $sql = "INSERT INTO tFeedBackData(
					fType,
					fStoreId,
					fFeedBack,
					fRtitle,
					fTitle,
					fIdentity,
					fIdentityNumber,
					fZipC,
					fAddrC,
					fZipR,
					fAddrR,
					fMobileNum,
					fEmail,
					fAccountNum,
					fAccountNumB,
					fAccount,
					fAccountName,
					fNote
				)VALUES(
					'" . trim($list[$i]['type']) . "',
					'" . trim($list[$i]['id']) . "',
					'" . trim($list[$i]['inv_id']) . "',
					'" . trim($list[$i]['title']) . "',
					'" . trim($list[$i]['name']) . "',
					'" . trim($list[$i]['iden']) . "',
					'" . trim($list[$i]['iden_number']) . "',
					'" . trim($list[$i]['cZip']) . "',
					'" . trim($list[$i]['cAddr']) . "',
					'" . trim($list[$i]['rZip']) . "',
					'" . trim($list[$i]['rAddr']) . "',
					'" . trim($list[$i]['phone']) . "',
					'" . trim($list[$i]['email']) . "',
					'" . trim($list[$i]['bank_id']) . "',
					'" . trim($list[$i]['bank_branchId']) . "',
					'" . trim($list[$i]['accounting']) . "',
					'" . trim($list[$i]['accName']) . "',
					'" . trim($list[$i]['note']) . "'
				)";
            // echo $sql."<br><br>";

            setModifyTime($list[$i]['type'], $list[$i]['id']);

            if (!$conn->Execute($sql)) {
                $error[] = $list[$i];
            }

            unset($sql);
        }

    }

    if (is_array($error)) {
        return $error;
    } else {
        return true;
    }
}

function setDelete($list)
{
    global $conn;

    for ($i = 0; $i < count($list); $i++) {
        $sql2 = "UPDATE tFeedBackData SET fStatus = 1 WHERE fType = '" . $list[$i]['type'] . "' AND fStoreId = '" . $list[$i]['id'] . "'";
        $conn->Execute($sql2);
    }

}

function setModifyTime($type, $id)
{
    global $conn;
    if ($type == 1) { //地政
        $sql = "UPDATE tScrivener SET sEditor_Accounting ='" . $_SESSION['member_name'] . "',sModify_time_Accounting='" . date('Y-m-d H:i:s', time()) . "' WHERE sId ='" . $id . "'";

    } else {
        $sql = "UPDATE tBranch SET bEditor_Accounting ='" . $_SESSION['member_name'] . "',bModify_time_Accounting='" . date('Y-m-d H:i:s', time()) . "' WHERE bId ='" . $id . "'";

    }
    // echo $sql."<br>";

    $conn->Execute($sql);

}
function getAddr($zip)
{
    global $conn;

    $sql = "SELECT zCity,zArea FROM tZipArea WHERE zZip ='" . $zip . "'";

    $rs = $conn->Execute($sql);

    return $rs->fields['zCity'] . $rs->fields['zArea'];
}
##
$smarty->assign('show', $tbl);
// die;
##
// die('--');
