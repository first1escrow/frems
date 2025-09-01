<?php
include_once 'db/openadodb.php' ;
include_once dirname(dirname(__FILE__)).'/includes/maintain/feedBackData.php';
include_once 'includes/getSalesInfo.php';

//期間代書列表
Function get_all_scrivener($sales, $to, $from='0000-00-00 00:00:00') {
    global $conn ;
    
    $sql = '
        SELECT
            *
        FROM
            tScrivener AS s
        LEFT JOIN
            tScrivenerSales AS ss ON s.sId = ss.sScrivener
        WHERE
            ss.sSales  = "'.$sales.'"
            AND s.sStatus = 1 
            AND s.sCreat_time >= "'.$from.'"
            AND s.sCreat_time <= "'.$to.'"
            AND s.sName NOT LIKE "%業務專用%"
        GROUP BY
            s.sId
        ORDER BY
            s.sId
        ASC;
    ' ;
    
    $rs = $conn->Execute($sql) ;
    
    $list = array() ;
    while (!$rs->EOF) {
        $tmp = explode(',', $rs->fields['sBrand']) ;
        
        if (in_array(1, $tmp) || ($rs->fields['sContractStatusTime'] != '0000-00-00')) {
            $list[$rs->fields['sScrivener']] = 0 ;
        }
        unset($tmp) ;
        
        $rs->MoveNext() ;
    }
    
    return $list ;
}
##

//期間仲介列表
Function get_all_branch($sales, $to, $from='0000-00-00 00:00:00') {
    global $conn ;
    
    $sql = '
        SELECT
            *
        FROM
            tBranch AS b
        LEFT JOIN
            tBranchSales AS bs ON bs.bBranch = b.bId
        WHERE
            bs.bSales = "'.$sales.'"
            AND b.bStatus = 1 AND bSalesReportStore = 0 AND b.bId != 2277
            AND (
                (b.bContractStatusTime > "'.$from.'" AND b.bContractStatusTime <= "'.$to.'")
                OR
                (b.bContractStatusTime = "'.$from.'" AND b.bBrand = 1)
            )

        ORDER BY
            bs.bBranch
        ASC
    ' ;
    
    $rs = $conn->Execute($sql) ;
    
    $list = array() ;
    while (!$rs->EOF) {
        $list[$rs->fields['bBranch']] = 0 ;
        $rs->MoveNext() ;
    }
    
    return $list ;
}
##

//仲介期間內合約件數
Function get_branch_case($br, $sDate, $eDate) {
    global $conn ;
    
    $list = array() ;
    foreach ($br as $k => $v) {
        $sql = '
            SELECT
                a.cCertifiedId
            FROM
                tContractCase AS a
            JOIN
                tContractRealestate AS b ON a.cCertifiedId = b.cCertifyId
            WHERE
                a.cSignDate >= "'.$sDate.'" 
                AND a.cSignDate <= "'.$eDate.'"
                AND (
                    b.cBranchNum = "'.$k.'"
                    OR b.cBranchNum1 = "'.$k.'"
                    OR b.cBranchNum2 = "'.$k.'"
                )
            GROUP BY
                a.cCertifiedId
            ORDER BY
                a.cSignDate
            ASC;
        ' ;
        
        $rs = $conn->Execute($sql) ;
        
        if (!$rs->EOF) {
            if ($br[$k] <= 0) {
                $br[$k] ++ ;
                // $list[] = $rs->fields ;
                $list[] = $k ;
            }
        } 
    }
    
    return $list ;
}
##

//代書期間內合約件數
Function get_scrivener_case($sc, $sDate, $eDate) {
    global $conn ;
    
    $list = array() ;
    foreach ($sc as $k => $v) {
        $sql = '
            SELECT
                a.cCertifiedId
            FROM
                tContractCase AS a
            JOIN
                tContractScrivener AS b ON a.cCertifiedId = b.cCertifiedId
            WHERE
                a.cSignDate >= "'.$sDate.'" 
                AND a.cSignDate <= "'.$eDate.'"
                AND b.cScrivener = "'.$k.'"
            GROUP BY
                a.cCertifiedId
            ORDER BY
                a.cSignDate
            ASC;
        ' ;
        
        $rs = $conn->Execute($sql) ;
        
        if (!$rs->EOF) {
            if ($sc[$k] <= 0) {
                $sc[$k] ++ ;
                // $list[] = $rs->fields ;
                $list[] = $k ;
            }
        }
    }
    
    return $list ;
}
##

//寫入資料表
Function insertDB($data) {
    global $conn ;
    
    if (!empty($data['sDate']) && !empty($data['sSales'])) {
        $sql = 'SELECT * FROM tSalesReportStore WHERE sDate = "'.$data['sDate'].'" AND sSales = "'.$data['sSales'].'";' ;
        $rs = $conn->Execute($sql) ;
        
        if ($rs->EOF) {     //新增紀錄
            $sql = '
                INSERT INTO
                    tSalesReportStore
                SET
                    sDate       = "'.$data['sDate'].'",
                    sSales      = "'.$data['sSales'].'",
                    sSalesName  = "'.$data['sSalesName'].'",
                    sScrTotal   = "'.$data['sScrTotal'].'",
                    sRealTotal  = "'.$data['sRealTotal'].'",
                    sScrivener  = "'.$data['sScrivener'].'",
                    sRealty     = "'.$data['sRealty'].'",
                    sStore      = "'.addslashes($data['sStore']).'",
                    sLastModify = "'.date("Y-m-d H:i:s").'"
            ' ;
        }
        else {      //更新
            $sql = '
                UPDATE
                    tSalesReportStore
                SET
                    sDate       = "'.$data['sDate'].'",
                    sSales      = "'.$data['sSales'].'",
                    sSalesName  = "'.$data['sSalesName'].'",
                    sScrTotal   = "'.$data['sScrTotal'].'",
                    sRealTotal  = "'.$data['sRealTotal'].'",
                    sScrivener  = "'.$data['sScrivener'].'",
                    sRealty     = "'.$data['sRealty'].'",
                    sStore      = "'.addslashes($data['sStore']).'",
                    sLastModify = "'.date("Y-m-d H:i:s").'"
                WHERE
                    sId = "'.$rs->fields['sId'].' AND sLock = 0"
            ' ;
        }
        
        return $conn->Execute($sql) ;
    }
    else return false ;
}
##

//分辨是否有進案
Function identifyExists($arr=array(), $idnt=array(), $keys) {
    $data = array() ;
    
    $data[$keys.'_yes'] = $idnt ;
    foreach ($arr as $k => $v) {
        $data[$keys.'_all'][] = $k ;
        if (!in_array($k, $idnt)) $data[$keys.'_no'][] = $k ;
    }
    
    return $data ;
}
##

//取得所有業務代碼
$detail = array() ;
$sql = 'SELECT * FROM tPeopleInfo WHERE pDep IN ("7", "8") AND pJob = "1" ORDER BY pId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
    $detail[] = array('pId' => $rs->fields['pId'], 'pName' => $rs->fields['pName']) ;
    $rs->MoveNext() ;
}
// print_r($detail) ; exit ;
##

//判斷季別
$nowDate = date("Y-m-d") ;
// $nowDate = '2017-12-31' ;
$tmp = explode('-', $nowDate) ;

$tmp[1] = (int)$tmp[1] ;
if ($tmp[1] <= 3) {     //第一季
    //本季
    $sDate = $tmp[0].'-01-01 00:00:00' ;
    $eDate = $tmp[0].'-03-31 23:59:59' ;
    ##
    
    //本季分母用
    $sDateDiv = ($tmp[0] - 1).'-10-01 00:00:00' ;
    $eDateDiv = ($tmp[0] - 1).'-12-31 23:59:59' ;
    ##
}
else if (($tmp[1] >= 4) && ($tmp[1] <= 6)) {    //第二季
    //本季
    $sDate = $tmp[0].'-04-01 00:00:00' ;
    $eDate = $tmp[0].'-06-30 23:59:59' ;
    ##
    
    //本季分母用
    $sDateDiv = $tmp[0].'-01-01 00:00:00' ;
    $eDateDiv = $tmp[0].'-03-31 23:59:59' ;
    ##
}
else if (($tmp[1] >= 7) && ($tmp[1] <= 9)) {    //第三季
    //本季
    $sDate = $tmp[0].'-07-01 00:00:00' ;
    $eDate = $tmp[0].'-09-30 23:59:59' ;
    ##
    
    //本季分母用
    $sDateDiv = $tmp[0].'-04-01 00:00:00' ;
    $eDateDiv = $tmp[0].'-06-30 23:59:59' ;
    ##
}
else {      //第四季
    //本季
    $sDate = $tmp[0].'-10-01 00:00:00' ;
    $eDate = $tmp[0].'-12-31 23:59:59' ;
    ##
    
    //本季分母用
    $sDateDiv = $tmp[0].'-07-01 00:00:00' ;
    $eDateDiv = $tmp[0].'-09-30 23:59:59' ;
    ##
}

unset($tmp) ;
##

/***********************************************************************************/
echo date("Y-m-d H:i:s").' Staring .... '."\n" ;
foreach ($detail as $k => $v) {
    $sales = $v['pId'] ;
    $sales_name = $v['pName'] ;   
    
    //取得所有地政士數量
    $sc = array() ;
    $sc = get_all_scrivener($sales, $eDateDiv) ;
    $sc_no = count($sc) ;
    ##
    
    //取得所有仲介數量
    $br = array() ;
    $br = get_all_branch($sales, $eDateDiv) ;
    $br_no = count($br) ;
    ##
    
    echo '業務：'.$sales_name.' ('.$sDate.' ~ '.$eDate.')'."\n" ;
    echo '代書通路數：'.$sc_no.', 仲介通路數：'.$br_no.', 通路總數：'.($sr_no + $bc_no).'('.$sDateDiv.'~'.$eDateDiv.')'."\n" ;
    echo '=============================================================='."\n" ;
    
    //取得有進案且不重複地政士
    $sc_case = array() ;
    $sc_case = get_scrivener_case($sc, $sDate, $eDate) ;
    $sc_case_no = count($sc_case) ;
    ##
    
    //取得有進案且不重複仲介
    $br_case = array() ;
    $br_case = get_branch_case($br, $sDate, $eDate) ;
    $br_case_no = count($br_case) ;
    ##
    
    //紀錄當季地政士所有詳細資料
    $sc_store = array() ;
    $sc_store = identifyExists($sc, $sc_case, 'sc') ;
    // print_r($sc_store) ; exit ;
    echo '地政士總數：'.count($sc_store['sc_all']).', 有進案地政士：'.count($sc_store['sc_yes']).', 未進案地政士：'.count($sc_store['sc_no'])."\n" ;
    ##
    
    //紀錄當季仲介所有詳細資料
    $br_store = array() ;
    $br_store = identifyExists($br, $br_case, 'br') ;
    // print_r($br_store) ; exit ;
    echo '仲介總數：'.count($br_store['br_all']).', 有進案仲介：'.count($br_store['br_yes']).', 未進案仲介：'.count($br_store['br_no'])."\n" ;
    ##
    
    //結合仲介與地政士所有詳細資料
    $stores = array() ;
    $stores = array_merge($sc_store, $br_store) ;
    ##
    
    $sdata = array() ;
    $sdata = array(
        'sDate'         => substr($eDate, 0, 10),
        'sSales'        => $sales,
        'sSalesName'    => $sales_name,
        'sScrTotal'     => $sc_no,
        'sRealTotal'    => $br_no,
        'sScrivener'    => $sc_case_no,
        'sRealty'       => $br_case_no,
        'sStore'        => json_encode($stores)
    ) ;
    
    if (!insertDB($sdata)) echo "\n".'無法新增/更新資料庫：'.json_encode($sdata)."\n\n" ;
    
    echo '仲介家數：'.$br_case_no.', 代書家數：'.$sc_case_no."\n" ;
    echo '有效使用率(本季點數 / 總通路數)：'.round((($br_case_no + $sc_case_no) / ($br_no + $sc_no) * 100),2)."%\n" ;

    unset($br_case, $br_case_no, $br_nocase, $br_nocase_no, $sc_case, $sc_case_no, $sc_store, $br_store, $stores, $sdata) ;
    echo '=============================================================='."\n" ;
    echo "\n" ;
}
echo date("Y-m-d H:i:s").' Finished .... '."\n" ;
/******/
?>