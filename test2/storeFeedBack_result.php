<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
##
// 年度季別
$_POST = escapeStr($_POST) ;

$banktranStatus = $_POST['banktranStatus'];
$sDate = $_POST['sDate'];
$sDate2 = $_POST['sDate2'];
$scrivener = $_POST['scrivener'];
$branch = $_POST['branch'];



if ($banktranStatus == 2) {
	$qstr = ' sStatus IN(2,3) AND sMethod = 3 AND sDelete = 0';
	if ($sDate) {
		$tmp = explode('-', $sDate);
		$sDate = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];


		unset($tmp);

		$tmp = explode('-', $sDate2);
		$sDate2 = ($tmp[0]+1911)."-".$tmp[1]."-".$tmp[2];
		unset($tmp);
		
		if ($qstr) { $qstr .= " AND ";}
		$qstr .= " (sCaseCloseTime >= '".$sDate."' AND sCaseCloseTime <= '".$sDate2."')";

	}else{
		if ($qstr) { $qstr .= " AND ";}
		$qstr .= " sCaseCloseTime != '0000-00-00'";
	}
	

	
}else{
	$qstr = ' sStatus = 2 AND sMethod = 3 AND sDelete = 0';
	if ($qstr) { $qstr .= " AND ";}
	$qstr .= " sCaseCloseTime = '0000-00-00'";
}

if ($scrivener && $branch) {
	if ($qstr) { $qstr .= " AND ";}
	$qstr .= "((sType = 1 AND sStoreId IN(".$scrivener.")) OR (sType = 2 AND sStoreId IN(".$branch.")))";
}elseif ($scrivener) {
	if ($qstr) { $qstr .= " AND ";}
	$qstr .= "(sType = 1 AND sStoreId IN(".$scrivener."))";
}elseif ($branch) {
	if ($qstr) { $qstr .= " AND ";}
	$qstr .= "(sType = 2 AND sStoreId IN(".$branch."))";
}

// $sql = "SELECT * FROM tStoreFeedBackMoneyFrom WHERE sStatus = 2 AND sMethod = 3" ;
$sql = "SELECT * FROM tStoreFeedBackMoneyFrom WHERE ".$qstr." ORDER BY sSeason DESC,sType DESC,sStoreId ASC";

if ($_SESSION['member_id'] == 6) {
	// echo $sql;
}
// echo $sql;
$rs = $conn->Execute($sql);
$i = 0;
$list = array();
while (!$rs->EOF) {

	$list[$i] = $rs->fields;
	$list[$i]['code'] = ($rs->fields['sType'] == 1)? $rs->fields['sStoreCode'].str_pad($rs->fields['sStoreId'], 4,'0',STR_PAD_LEFT):$rs->fields['sStoreCode'].str_pad($rs->fields['sStoreId'], 5,'0',STR_PAD_LEFT);
    //0產出、1已發布(未確認)、2店家已確認、3已完成(要等上傳完收款記錄才算)
    if($list[$i]['sStatus'] == 1){
		$list[$i]['status'] = '待請款';
					
	}elseif($list[$i]['sStatus'] == 2) {
		$list[$i]['status'] = '處理中';
					
	}elseif ($list[$i]['sStatus'] == 3) {
		$list[$i]['status'] = '已完成';
					
	}
	// if ($list[$i]['sStatus'] == 1) {
	// 	$list[$i]['status'] = '已發佈';
	// }else if($list[$i]['sStatus'] == 2){
	// 	$list[$i]['status'] = '已結案';
	// }else{
	// 	$list[$i]['status'] = '未發佈';
	// }

	//1公司2事務所3個人
	if ($list[$i]['sMethod'] == 1) {
		$list[$i]['method'] = '公司';
	}elseif ($list[$i]['sMethod'] == 2) {
		$list[$i]['method'] = '事務所';
	}elseif($list[$i]['sMethod'] == 3){
		$list[$i]['method'] = '個人';
	}

	$list[$i]['sConfirmationTime'] = ($list[$i]['sConfirmationTime'] != '0000-00-00')?str_replace('-', '/', (substr($list[$i]['sConfirmationTime'], 0,4)-1911).substr($list[$i]['sConfirmationTime'],4)):'000/00/00' ;
	
	$list[$i]['sEndTime'] = ($list[$i]['sEndTime'] != '0000-00-00')?str_replace('-', '/', (substr($list[$i]['sEndTime'], 0,4)-1911).substr($list[$i]['sEndTime'],4)):'000/00/00' ;
	$list[$i]['sEndTime2'] = ($list[$i]['sEndTime2'] != '0000-00-00')?str_replace('-', '/', (substr($list[$i]['sEndTime2'], 0,4)-1911).substr($list[$i]['sEndTime2'],4)):'000/00/00' ;
	
	$list[$i]['sCaseCloseTime'] = ($list[$i]['sCaseCloseTime'] != '0000-00-00')?str_replace('-', '/', (substr($list[$i]['sCaseCloseTime'], 0,4)-1911).substr($list[$i]['sCaseCloseTime'],4)):'000/00/00' ;

	$list[$i]['sFeedBackMoneyTotal'] = ($list[$i]['sFeedBackMoneyTotal'] =='')?getFeedBackMoney($list[$i]['sId']):$list[$i]['sFeedBackMoneyTotal'];
	
	$i++;
	$rs->MoveNext();
}
//原本tStoreFeedBackMoneyFrom表沒有計算總金額，casefeedbackPDF2_resultPDF.php有加上去了，但怕有遺漏暫時保留
function getFeedBackMoney($id){
	global $conn;

	$sql = "SELECT sFeedBackMoneyTotal FROM tStoreFeedBackMoneyFrom_Money WHERE sFromId = '".$id."'";
	$rs = $conn->Execute($sql);

	$sql = "UPDATE tStoreFeedBackMoneyFrom SET sFeedBackMoneyTotal = '".$rs->fields['sFeedBackMoneyTotal']."' WHERE sId = '".$id."' ";
	$conn->Execute($sql);
	

	return $rs->fields['sFeedBackMoneyTotal'];
}
?>

<table cellspacing="0" cellpadding="0" border="0" class="tb" width="100%">
    <tr>
        <th><input type="checkbox" name="all" checked="" onclick="checkALL()"></th>
        <th>編號</th>
        <th>店家名稱</th>
        <th>請款方式</th>
        <th>結算期間</th>
        <th>金額</th>
        <th>狀態</th>
        <th>確認時間</th>
        <th>匯出表單時間</th>
                                                            <!-- <th></th> -->
    </tr>
	<?php foreach ($list as $k => $v): ?>
		<tr>
        <td><input type="checkbox" name="allForm[]" value="<?=$v['sId']?>" checked="" onclick="showcount()"> </td>
        <td><?=$v['code']?></td>
        <td><?=$v['sStoreName']?></td>
        <td><?=$v['method']?></td>
        <td><?=$v['sSeason']?></td>
        <td align="right"><?=number_format($v['sFeedBackMoneyTotal'])?> <input type="hidden" name="sFeedBackMoneyTotal[]" value="<?=$v['sFeedBackMoneyTotal']?>"></td>
        <td><?=$v['status']?></td>
        <td><?=$v['sConfirmationTime']?></td>
        <td><?=$v['sCaseCloseTime']?></td>
        <!-- <td><input type="button" value="PDF" class="xxx-button2" onclick="downloadPDF(<{$item.sId}>)"></td> -->
    </tr>
	<?php endforeach ?>
    
   
</table>