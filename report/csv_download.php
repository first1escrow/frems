<?php
include_once '../configs/config.class.php';
include_once dirname(__DIR__) . '/class/SmartyMain.class.php';
include_once dirname(__DIR__) . '/class/intolog.php';
include_once '../session_check.php';
// include_once '../opendb.php' ;
include_once '../openadodb.php';
require_once dirname(__DIR__) . '/first1DB.php';

##
$sql = "SELECT bName,bId FROM tBrand WHERE bContract = 1";
$rs  = $conn->Execute($sql);
while (! $rs->EOF) {
    $BrandContract[$rs->fields['bId']] = $rs->fields['bName'];
    $BrandContractId[]                 = $rs->fields['bId'];
    $rs->MoveNext();
}
##

if ($_POST['export_ok'] == '1') {

    $title      = ['身份別', '編號'];
    $fieldArray = ['iden', 'sn'];

    $str = '身份別,編號'; //CSV  $queryStr = '';

    // 設定顯示名稱與資料表欄位(身份別)
    $ide = $_POST['identity'];

    if ($_SESSION['member_test'] != 0) {

        $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $test_tmp[] = "'" . $rs->fields['zZip'] . "'";

            $rs->MoveNext();
        }
        if ($ide == 'scrivener') {
            $queryStr = ' AND sCpZip1 IN(' . @implode(',', $test_tmp) . ') AND sCategory != 2';
        } else {
            $queryStr = ' AND bZip IN(' . @implode(',', $test_tmp) . ') AND bCategory != 2';
        }
        // $query .= "bZip IN(".implode(',', $test_tmp).")";

        unset($test_tmp);
    } elseif ($_SESSION['member_pDep'] == 7) {
        // $sql = "SELECT FROM WHERE pDep IN()";
        if ($ide == 'scrivener') {
            $sql = "SELECT sScrivener AS store FROM tScrivenerSales WHERE sSales = '" . $_SESSION['member_id'] . "'";
            $col = 'sId';
        } else {
            $sql = "SELECT bBranch AS store FROM tBranchSales WHERE bSales = '" . $_SESSION['member_id'] . "'";
            $col = 'bId';
        }

        $rs = $conn->Execute($sql);
        while (! $rs->EOF) {
            $test_tmp[] = "'" . $rs->fields['store'] . "'";

            $rs->MoveNext();
        }

        $queryStr = " AND " . $col . " IN (" . @implode(',', $test_tmp) . ")";

        unset($test_tmp);unset($col);
    }

    if ($ide == 'scrivener') {
        $ide      = 'tScrivener';
        $identity = '地政士';
        $where    = 'sStatus = 1 AND sId NOT IN (632, 575,552,620,411,224) ' . $queryStr;
        $_fields  = ' sId as sn ';
        $_fields .= ' ,sName as cName,sBirthday ';

        array_push($title, '姓名');
        array_push($title, '生日');
        array_push($fieldArray, 'cName');
        array_push($fieldArray, 'sBirthday');

    } else {
        $ide      = 'tBranch';
        $identity = '仲介';
        $where    = 'bStatus = 1 AND bId NOT IN (0, 505, 980, 1012) ' . $queryStr;
        $_fields  = ' bId as sn ,(SELECT bCode FROM tBrand WHERE bId = bBrand) AS bCode';
        $_fields .= ' ,bBrand as brand ,bStore as cName ,bName as cCompany ,bManager ';
        $str .= ',仲介品牌,仲介公司,仲介店名,店長/店東';

        array_push($title, '仲介品牌');
        array_push($title, '仲介公司');
        array_push($title, '仲介店名');
        array_push($title, '店長/店東');

        array_push($fieldArray, 'brand');
        array_push($fieldArray, 'cName');
        array_push($fieldArray, 'cCompany');
        array_push($fieldArray, 'bManager');

    }

    // 若選擇行動電話選項，則顯示並選取手機號碼資料表欄位
    if ($_POST['field_mobile']) {
        $field_mobile = '';
        if ($ide == 'tScrivener') {$field_mobile = ' sMobileNum as mobile ';} else if ($ide == 'tBranch') {$field_mobile = ' bMobileNum as mobile ';}

        if ($_fields) {$_fields .= ',';}
        $_fields .= $field_mobile;

        array_push($title, '行動電話');
        array_push($fieldArray, 'mobile');

        unset($field_mobile);
    }

    // 若選擇電話選項，則顯示並選取電話區碼與號碼資料表欄位

    if ($_POST['field_tel']) {
        $field_tel = '';
        if ($ide == 'tScrivener') {$field_tel = ' sTelArea as telArea, sTelMain as telMain ';}
        if ($ide == 'tBranch') {$field_tel = ' bTelArea as telArea, bTelMain as telMain ';}

        if ($_fields) {$_fields .= ',';}
        $_fields .= $field_tel;
        // $str .= ',電話號碼' ;
        array_push($title, '電話號碼');
        array_push($fieldArray, 'tel');
        unset($field_tel);
    }

    // 若選擇傳真選項，則顯示並選取傳真區碼與號碼資料表欄位
    if ($_POST['field_fax']) {
        $field_fax = '';
        if ($ide == 'tScrivener') {$field_fax = ' sFaxArea as faxArea, sFaxMain as faxMain ';} else if ($ide == 'tBranch') {$field_fax = ' bFaxArea as faxArea, bFaxMain as faxMain ';}

        if ($_fields) {$_fields .= ',';}
        $_fields .= $field_fax;
        array_push($title, '傳真號碼');
        array_push($fieldArray, 'fax');
        unset($field_fax);
    }

    // 若選擇 E-Mail 選項，則顯示並選取 E-Mail 資料表欄位

    if ($_POST['field_email']) {
        $field_email = '';
        if ($ide == 'tScrivener') {$field_email = ' sEmail as email ';} else if ($ide == 'tBranch') {$field_email = ' bEmail as email ';}

        if ($_fields) {$_fields .= ',';}
        $_fields .= $field_email;
        // $str .= ',電子郵件' ;
        array_push($title, '電子郵件');
        array_push($fieldArray, 'email');
        unset($field_email);
    }

    // 若選擇地址選項，則顯示並選取地址與郵遞區號(前三碼)資料表欄位

    if ($_POST['field_address']) {
        $field_address = '';
        if ($ide == 'tScrivener') {
            $field_address = ' (SELECT zCity FROM tZipArea WHERE zZip=a.sCpZip1) as zCity, (SELECT zArea FROM tZipArea WHERE zZip=a.sCpZip1) as zArea, sCpAddress as cAddress, sCpZip1 as cZip';
        } else if ($ide == 'tBranch') {
            $field_address = ' (SELECT zCity FROM tZipArea WHERE zZip=a.bZip) as zCity, (SELECT zArea FROM tZipArea WHERE zZip=a.bZip) as zArea, bAddress as cAddress, bZip as cZip';
        }

        if ($_fields) {$_fields .= ',';}
        $_fields .= $field_address;

        array_push($title, '郵遞區號');
        array_push($fieldArray, 'cZip');
        array_push($title, '地址');
        array_push($fieldArray, 'address');
        unset($field_address);
        // $str .= ',郵遞區號,地址' ;
    }

    // 若選擇事務所，則顯示事務所欄位
    if ($_POST['field_office']) {
        if ($ide == 'tScrivener') {
            $field_office = ' sOffice as office ';
            if ($_fields) {$_fields .= ',';}
            $_fields .= $field_office;
            // $str .= ',事務所名稱' ;
            array_push($title, '事務所名稱');
            array_push($fieldArray, 'office');
        }
    }

    // 若選擇負責業務選項，則顯示負責業務欄位

    // $field_sales = $_POST['field_sales'] ;
    if ($_POST['field_sales']) {
        array_push($title, '負責業務');
        array_push($fieldArray, 'sales');
        //if ($ide=='tScrivener') { $field_sales = ' sEmail as email ' ; }
        //else if ($ide=='tBranch') { $field_sales = ' (SELECT pName FROM tPeopleInfo WHERE a.=pId) as email ' ; }

        //if ($_fields) { $_fields .= ',' ; }
        //$_fields .= $field_sales ;
        // $str .= ',負責業務' ;
    }

    if ($ide == 'tScrivener') {
        if ($_fields) {$_fields .= ',';}
        $_fields .= 'sCreat_time AS creat_time,sRecall,sSpRecall';
        $str .= ',建立日期,回饋比率,特殊回饋比率,品牌回饋代書'; //比率

        array_push($title, '建立日期');
        array_push($title, '回饋比率');
        array_push($title, '特殊回饋比率');
        array_push($title, '品牌回饋代書');

        array_push($fieldArray, 'creat_time');
        array_push($fieldArray, 'sRecall');
        array_push($fieldArray, 'sSpRecall');
        array_push($fieldArray, 'brandForScrivener');

        array_push($title, '合契');
        array_push($fieldArray, 'feed');

    } else {
        if ($_fields) {$_fields .= ',';}
        $_fields .= 'bCreat_time AS creat_time,bRecall';
        $str .= ',建立日期,抬頭,戶名,回饋比率';

        array_push($title, '建立日期');
        array_push($title, '抬頭');
        array_push($title, '戶名');
        array_push($title, '回饋比率');

        array_push($fieldArray, 'creat_time');
        array_push($fieldArray, 'feedTitle');
        array_push($fieldArray, 'feedAccount'); //回饋金戶名
        array_push($fieldArray, 'bRecall');

        array_push($title, '特約');
        array_push($title, '合契');
        array_push($title, '先行撥付同意書');

        array_push($fieldArray, 'sign');
        array_push($fieldArray, 'feed');
        array_push($fieldArray, 'serviceOrderHas');

        //
    }

    //經辦
    if ($_POST['field_undertaker']) {
        if ($ide == 'tScrivener') {
            if ($_fields) {$_fields .= ',';}
            $_fields .= '(SELECT pName FROM tPeopleInfo WHERE pId = sUndertaker1) AS sUndertaker';
            // $str .= ',經辦';

            array_push($title, '經辦');
            array_push($fieldArray, 'sUndertaker');
        }
    }

    //地政士 合作品牌
    if ($_POST['field_sBrand']) {
        if ($ide == 'tScrivener') {
            // $str .= ',合作品牌';
            array_push($title, '合作品牌');
            array_push($fieldArray, 'sBrand');
        }
    }
    //
    if ($_POST['field_signSales']) {
        // $str .= ',簽約業務';
        array_push($title, '簽約業務');
        array_push($fieldArray, 'signSales');
    }
    ##
    //查詢條件

    if ($_POST['area'] != '') {

        if ($ide == "tScrivener") {
            $where .= " AND sCpZip1 ='" . $_POST['area'] . "'";
        } else {
            $where .= " AND bZip ='" . $_POST['area'] . "'";
        }
    } else if ($_POST['country'] != '') {
        $sql = "SELECT zZip FROM  tZipArea WHERE zCity ='" . $_POST['country'] . "' ";

        $_conn = new first1DB();
        $rel   = $_conn->all($sql);
        $_conn = null;unset($_conn);

        foreach ($rel as $tmp) {
            $zip[] = '"' . $tmp['zZip'] . '"';
        }

        if ($ide == "tScrivener") {
            $where .= " AND sCpZip1 IN(" . implode(',', $zip) . ")"; //bZip sZip1
        } else {
            $where .= " AND bZip IN(" . implode(',', $zip) . ")"; //bZip sZip1

        }
    }

    if ($_POST['book']) {
        $cat = ($ide == 'tScrivener') ? "1" : "2"; //sType 類型1地政2仲介

        for ($i = 0; $i < count($_POST['book']); $i++) {
            if ($_POST['book'][$i] == 1) { //1 => '特約',2 => '合契',3 =>'先行撥付同意書'
                if ($cat == 2) {
                    $sql = "SELECT sStore FROM tSalesSign WHERE sType = '" . $cat . "'"; //有特約
                    $rs  = $conn->Execute($sql);
                    while (! $rs->EOF) {
                        $arrayStore[] = $rs->fields['sStore'];

                        $rs->MoveNext();
                    }
                }

            }

            if ($_POST['book'][$i] == 2) {
                if ($cat == 2) {

                    $where .= ' AND bCooperationHas = 1';
                } elseif ($cat == 1) {
                    $sql = "SELECT fStoreId FROM tFeedBackData WHERE fType = '" . $cat . "' AND fStatus = 0"; //合契
                    $rs  = $conn->Execute($sql);
                    while (! $rs->EOF) {
                        $arrayStore2[] = $rs->fields['fStoreId'];

                        $rs->MoveNext();
                    }
                }

            }

            if ($_POST['book'][$i] == 3) { //1有
                if ($cat == 2) {
                    $where .= ' AND bServiceOrderHas = 1';

                }

            }
        }
        // $where .= "";
        unset($cat);
    }

    //條件選擇某地政士合作品牌
    if ($_POST['BrandContract'] || $_POST['field_sBrand']) {
        if ($_fields) {$_fields .= ',';}
        $_fields .= 'sBrand';

    }

    ##//

    $sql = '
	SELECT
		' . $_fields . '
	FROM
		' . $ide . ' AS a
	WHERE
		' . $where . '
	ORDER BY
		sn
	ASC ;
	';

    ##
    $data = [];
    $list = [];
    $rs   = $conn->Execute($sql);
    while (! $rs->EOF) {
        if (is_array($arrayStore)) { //

            if (! in_array($rs->fields['sn'], $arrayStore)) {
                $rs->MoveNext();
                continue;
            }
        }

        if (is_array($arrayStore2)) { //
            $col2 = '';
            if (! in_array($rs->fields['sn'], $arrayStore2)) {
                $rs->MoveNext();
                continue;
            }
        }

        //條件選擇某地政士合作品牌
        if ($_POST['BrandContract']) {
            if ($ide == 'tScrivener') {
                $tmp2  = explode(',', $rs->fields['sBrand']);
                $check = false;

                foreach ($tmp2 as $k => $v) {

                    if (in_array($v, $_POST['BrandContract'])) {
                        $check = true;
                    }

                }

                if (! $check) {
                    continue;
                }
                unset($tmp2);
            }
        }

        // $data = $rs->fields;

        foreach ($fieldArray as $v) {

            if ($v == 'iden') {
                $data[$v] = $identity;
            } else if ($v == 'tel') {
                $data[$v] = (! empty($rs->fields['telArea']) || ! empty($rs->fields['telMain'])) ? $rs->fields['telArea'] . '-' . $rs->fields['telMain'] : '';
            } else if ($v == 'fax') {
                $data[$v] = (! empty($rs->fields['faxArea']) || ! empty($rs->fields['faxMain'])) ? $rs->fields['faxArea'] . '-' . $rs->fields['faxMain'] : '';
            } else if ($v == 'address') {
                $rs->fields['cAddress'] = preg_replace("/$rs->fields['zCity']/", "", $rs->fields['cAddress']);
                $rs->fields['cAddress'] = preg_replace("/$rs->fields['zArea']/", "", $rs->fields['cAddress']);
                $data[$v]               = $rs->fields['zCity'] . $rs->fields['zArea'] . $rs->fields['cAddress'];
            } else if ($v == 'sales') {

                $data[$v] = getSales($rs->fields['sn'], $ide);

            } else if ($v == 'creat_time') {
                $data[$v] = DateChange($tmp['creat_time']);
            } else if ($v == 'brandForScrivener') {
                $data[$v] = getBrandForScrivener($rs->fields['sn']);
            } else if ($v == 'sign') {

                $data[$v] = sign($ide, $rs->fields['sn'], 'sign');
            } else if ($v == 'feed') {
                $data[$v] = feed($ide, $rs->fields['sn'], 'feed');
            } else if ($v == 'feedTitle') {
                $data[$v] = feed($ide, $rs->fields['sn'], 'title');

            } elseif ($v == 'feedAccount') {
                $data[$v] = feed($ide, $rs->fields['sn'], 'account');
            } else if ($v == 'signSales') {
                $data[$v] = sign($ide, $rs->fields['sn'], 'sales');
            } elseif ($v == 'sn') {
                $data[$v] = ($ide == 'tScrivener') ? 'SC' . str_pad($rs->fields['sn'], 4, 0, STR_PAD_LEFT) : $rs->fields['bCode'] . str_pad($rs->fields['sn'], 5, 0, STR_PAD_LEFT);
            } elseif ($v == 'sBrand') {
                $data[$v] = getBrandName($rs->fields['sBrand']);
            } elseif ($v == 'brand') {
                $data[$v] = getBrandName($rs->fields['brand']);
            } else {
                $data[$v] = $rs->fields[$v];
            }

        }

        // echo "<pre>";
        // 	print_r($fieldArray);
        // 	print_r($data);
        // 	die;

        array_push($list, $data);
        $rs->MoveNext();
    }

    // echo "<pre>";
    // print_r($fieldArray);
    // print_r($data);
    // die;

    // die;
    unset($queryStr);
    if ($_POST['report'] == 2) { // excel

        require_once 'csv_download_excel.php';
        exit;
    } else {
        require_once 'csv_download_csv.php';
        exit;

    }

}

$z_str = '';
if ($_SESSION['member_test'] != 0) {
    $sql = "SELECT zZip FROM `tZipArea` WHERE zTrainee = '" . $_SESSION['member_test'] . "'";

    $rs = $conn->Execute($sql);

    while (! $rs->EOF) {
        $test_tmp[] = "'" . $rs->fields['zZip'] . "'";

        $rs->MoveNext();
    }
    $z_str = " AND zZip IN(" . implode(',', $test_tmp) . ")";
    unset($test_tmp);

} else if ($_SESSION['member_pDep'] == 7) {
    $z_str = 'AND FIND_IN_SET(' . $_SESSION['member_id'] . ',zSales)';
}

//縣市
$citys = '<option selected="selected" value="">全部</option>' . "\n";

$sql = 'SELECT zCity FROM tZipArea WHERE 1=1 ' . $z_str . '  GROUP BY zCity ORDER BY zZip,zCity ASC;';

$_conn = new first1DB;
$rel   = $_conn->all($sql);
$_conn = null;unset($_conn);

foreach ($rel as $tmp) {
    $citys .= '<option value="' . $tmp['zCity'] . '">' . $tmp['zCity'] . "</option>\n";
    unset($tmp);

}
##
$sql = "SELECT bName,bId FROM tBrand WHERE bContract = 1";
$rs  = $conn->Execute($sql);
while (! $rs->EOF) {
    $BrandContract[$rs->fields['bId']] = $rs->fields['bName'];

    $rs->MoveNext();
}

######
function DateChange($val)
{

    // $val = trim(preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/","",$val)) ;

    if ($val != '') {
        $tmp = explode('-', $val);

        if (preg_match("/0000/", $tmp[0])) {$tmp[0] = '000';} else { $tmp[0] -= 1911;}

        $val = $tmp[0] . '/' . $tmp[1] . '/' . $tmp[2];
        unset($tmp);
    }

    return $val;
}

function sign($ide, $sn, $type)
{

    global $conn;
    $cat = ($ide == 'tScrivener') ? "1" : "2";

    $sql = "SELECT sStore,(SELECT pName FROM tPeopleInfo WHERE pId=sSales) as sSales FROM tSalesSign WHERE sType = '" . $cat . "' AND sStore = '" . $sn . "'"; //有特約
    $rs  = $conn->Execute($sql);

    $total = $rs->RecordCount();

    if ($type == 'sign') {
        if ($total) {
            return '有';
        } else {
            return '無';
        }
    } elseif ($type == 'sales') {
        $sales = [];
        while (! $rs->EOF) {
            array_push($sales, $rs->fields['sSales']);

            $rs->MoveNext();
        }
        return @implode('、', $sales);
    }

}

function feed($ide, $id, $type)
{
    global $conn;
    $cat = ($ide == 'tScrivener') ? "1" : "2";
    $sql = "SELECT fStoreId,fTitle,fAccount FROM tFeedBackData WHERE fType = '" . $cat . "' AND fStoreId ='" . $id . "' AND  fStatus = 0 ORDER BY fId ASC"; //合契
                                                                                                                                                            // echo $sql."<br>";
    $rs = $conn->Execute($sql);

    $total = $rs->RecordCount();

    if ($type == 'feed') {
        if ($total) {
            return '有';
        } else {
            return '無';
        }
    } else if ($type == 'title') {

        return $rs->fields['fTitle'];

    } else if ($type == 'account') {

        return $rs->fields['fAccount'];
    }

}

function getSales($sn, $ide)
{
    global $conn;

    if ($ide == 'tScrivener') {
        $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=sSales) AS sales FROM tScrivenerSales as sales WHERE sScrivener = '" . $sn . "'";
    } else {
        $sql = "SELECT (SELECT pName FROM tPeopleInfo WHERE pId=bSales) AS sales FROM tBranchSales WHERE bBranch ='" . $sn . "'";
    }

    $rs       = $conn->Execute($sql);
    $tmpSales = [];
    while (! $rs->EOF) {
        $tmpSales[] = $rs->fields['sales'];

        $rs->MoveNext();
    }

    return @implode('、', $tmpSales);
}

function getBrandForScrivener($sn)
{
    global $conn;
    $sql = "SELECT *,(SELECT bName FROM tBrand WHERE bId = sBrand) AS BrandName FROM tScrivenerFeedSp WHERE sScrivener ='" . $sn . "' AND sDel =0";
    $rs  = $conn->Execute($sql);
    $txt = [];
    while (! $rs->EOF) {
        $txt[] = $rs->fields['BrandName'] . ":" . $rs->fields['sReacllBrand'] . "%(品牌)、" . $rs->fields['sRecall'] . "%(地政士)";

        $rs->MoveNext();
    }

    return @implode(';', $txt);
}

function getBrandName($val)
{
    global $conn;

    $brand = [];
    $sql   = "SELECT bName FROM tBrand WHERE bId IN (" . $val . ")";
    $rs    = $conn->Execute($sql);
    while (! $rs->EOF) {
        array_push($brand, $rs->fields['bName']);

        $rs->MoveNext();
    }
    // print_r($brand);

    // die;

    return @implode('、', $brand);
}
#######
// $smarty->assign("y",$y) ;
// $smarty->assign("m",$m) ;
$smarty->assign("citys", $citys);
$smarty->assign("menu", [1 => '特約', 2 => '合契', 3 => '先行撥付同意書']);
$smarty->assign('menuBrandContract', $BrandContract);
$smarty->display('csv_download.inc.tpl', '', 'report');
