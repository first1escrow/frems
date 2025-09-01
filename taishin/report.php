<?php
ini_set("display_errors", "On");
error_reporting(E_ALL&~E_NOTICE);

require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/class/SmartyMain.class.php';
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Writer/Excel2007.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/IOFactory.php';
require_once dirname(__DIR__) . '/bank/Classes/PHPExcel/Reader/Excel5.php';

if ($_POST) {
    # 設定檔案存放目錄位置
    $uploaddir = __DIR__ . '/xls';
    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777, true);
    }
    $uploaddir .= '/';
    ##

    //設定檔案名稱
    $uploadfile = $_FILES['upload_file']['name'];
    $uploadfile = $uploaddir . $uploadfile;

    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $uploadfile)) {
        $xls = $uploaddir . $_FILES["upload_file"]["name"];
    } else {
        die("檔案上傳錯誤");
    }

    if (!file_exists($uploadfile)) {
        die("No file");
    }

    if ($_POST['report'] == 1) {
        $objReader = new PHPExcel_Reader_Excel2007();
        $objReader->setReadDataOnly(true);

        //檔案名稱
        $objPHPExcel  = $objReader->load($xls);
        $currentSheet = $objPHPExcel->getSheet(0); //讀取第一個工作表(編號從 0 開始)
        $allLine      = $currentSheet->getHighestRow(); //取得總列數

        $i = 0;
        for ($excel_line = 1; $excel_line <= $allLine; $excel_line++) {
            $data[$i]['A'] = $currentSheet->getCell("A{$excel_line}")->getValue(); //統編
            $data[$i]['B'] = $currentSheet->getCell("B{$excel_line}")->getValue(); //店號
            $data[$i]['C'] = $currentSheet->getCell("C{$excel_line}")->getValue(); //店名
            $data[$i]['D'] = $currentSheet->getCell("D{$excel_line}")->getValue(); //檢查號
            $data[$i]['E'] = $data[$i]['B'] . $data[$i]['D']; //虛擬帳號
            $data[$i]['F'] = $currentSheet->getCell("F{$excel_line}")->getValue(); //存款人姓名

            $i++;
        }

        unlink($uploadfile);

        $objPHPExcel = new PHPExcel();

        //Set properties 設置文件屬性
        $objPHPExcel->getProperties()->setCreator("第一建經");
        $objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
        $objPHPExcel->getProperties()->setTitle("第一建經");
        $objPHPExcel->getProperties()->setSubject("表");
        $objPHPExcel->getProperties()->setDescription("表");

        //指定目前工作頁
        $objPHPExcel->setActiveSheetIndex(0);
        //命名工作表標籤
        $objPHPExcel->getActiveSheet()->setTitle('表');

        //寫入清單標題列資料
        $col = 65;
        $row = 1;

        foreach ($data as $key => $value) {
            $col = 65;

            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['A'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['B'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['C'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['D'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, $value['E'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($col++) . $row, getScrivener($value['E']), PHPExcel_Cell_DataType::TYPE_STRING);

            $row++;
        }

        $_file = date('YmdHis');
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-type:application/force-download');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $_file . '.xlsx');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save("php://output");

        exit;
    }
    ##
}

function getScrivener($id)
{
    global $conn;

    $sql = "SELECT
				sName,
				(SELECT sName FROM tScrivenerSms WHERE sId = cManage) AS scrivener
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON s.sId =cs.cScrivener
			WHERE
				cs.cCertifiedId = '" . $id . "'";
    $rs = $conn->Execute($sql);

    if (!empty($rs->fields['scrivener'])) {
        $scrivener = $rs->fields['scrivener'];
    } else {
        $scrivener = $rs->fields['sName'];
    }

    //如果有()則濾除掉
    if (preg_match("/\(.*\)/iu", $scrivener)) {
        $match = [];
        preg_match("/^(.*)\(.*\)(.*)$/iu", $scrivener, $match);
        $scrivener = $match[1] . $match[2];

        $match = null;unset($match);
    }
    ##

    //搜尋取代
    foreach (scrivener_mapping() as $pattern => $replace_name) {
        if (preg_match("/$pattern/iu", $scrivener)) {
            $scrivener = $replace_name;
            break;
        }
    }
    ##

    return $scrivener;
}

function scrivener_mapping()
{
    return [
        '文鼎' => '鄭志驊',
        '正業' => '鄭文在',
        '迦南' => '徐吉麟',
        '金門' => '蕭琪琳',
        '昱森' => '蘇晉得',
        '永業' => '姜銀燕',
        '和興' => '曾瑞泰',
        '群凱' => '蘇春華',
    ];
}

function getUndertaker($id)
{
    global $conn;

    $sql = "SELECT
			(SELECT pName FROM tPeopleInfo WHERE pId = s.sUndertaker1) AS Name
			FROM
				tContractScrivener AS cs
			LEFT JOIN
				tScrivener AS s ON s.sId = cs.cScrivener
			WHERE
				cs.cCertifiedId = '" . $id . "'";

    $rs = $conn->Execute($sql);

    return $rs->fields['Name'];
}
##

$smarty->display('taishin_atm_report.inc.tpl', '', 'bank');
