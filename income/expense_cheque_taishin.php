<?php
include_once '../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once '../session_check.php' ;
include_once '../tracelog.php' ;
include_once '../openadodb.php';

 $_POST = escapeStr($_POST) ;
$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_POST), '台新即時簡訊') ;

$textfield = $_REQUEST['textfield'] ;

//取得合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$rs->MoveNext() ;
}
unset($rs) ;
##
$_end_date = (date("Y")-1911).date("md") ;
$_start_date = date("Y-m-d",mktime(0,0,0,date("m"),(date("d")-6),date("Y"))) ;		// 設定(6+1)天的顯示日期範圍

$tmp = explode('-',$_start_date) ;
$_start_date = ($tmp[0] - 1911).$tmp[1].$tmp[2] ;
unset($tmp) ;

if ($_POST) {
	$str = 'ect.eDepAccount LIKE "%'.$textfield.'"';
}elseif($_SESSION['member_income'] == '1'){
	$str = "eTradeDate >= '".$_start_date."' AND eTradeDate <= '".$_end_date."'";
}else{
	$str = 'scr.sUndertaker1 = "'.$_SESSION['member_id'].'"';
	$str .= " AND eTradeDate >= '".$_start_date."' AND eTradeDate <= '".$_end_date."' AND p.pId = '".$_SESSION['member_id']."'";
}


$sql = "SELECT	
			ect.*,
			scr.sName,
			bco.bSID,
			p.pName
		FROM
			tExpense_cheque_taishin AS ect
		LEFT JOIN 
			tBankCode AS bco ON  bco.bAccount=SUBSTRING(ect.eDepAccount,-14)
		LEFT JOIN
			tScrivener AS scr ON scr.sId=bco.bSID
		LEFT JOIN
			tPeopleInfo AS p ON p.pId=scr.sUndertaker1
		WHERE ".$str ."
		ORDER BY 
			ect.eSms ASC,ect.eTradeDate
		DESC";

// if ($_SESSION['member_id'] == 6) {
// 	echo $sql;


// }

$rs = $conn->Execute($sql);
$i = 0;
while (!$rs->EOF) {
	$list[$i] = $rs->fields;
	$list[$i]['eLender'] = (int)substr($list[$i]['eLender'],0,13);
	$list[$i]['eTicketCount'] = (int) $list[$i]['eTicketCount'];
	if ($list[$i]['eSms'] != 1) {

        if ($list[$i]['eSms'] == 2) {
            $list[$i]['check'] = "checked=checked";
        }
    }

    if($list[$i]['eSms'] > 0){
		$list[$i]['color']='#CCCCCC';
	}else{
		$list[$i]['color']='#ffffff';
	}

	// $list[$i]['date'] = $rs->fields["eCreatTime"];

	$i++;
	$rs->MoveNext();
}
##
$smarty->assign('start_date',$_start_date);
$smarty->assign('end_date',$_end_date);
$smarty->assign('list',$list);
$smarty->display('expense_cheque_taishin.inc.tpl', '', 'income');
?>