<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
include_once '../maintain/feedBackData.php' ;

$_POST = escapeStr($_POST) ;

$act = $_POST['act'];
$type = $_POST['type'];
$val = $_POST['val'];
$chekcsp = $_POST['chekcsp'];
$scrivenerId = $_POST['scrivenerId'];

if ($act =='st') {
	// $data = getStore($_POST['type']);

	if($type == 1){

		if ($_SESSION['member_pDep'] == 7) {
            $queryStr = '';
            if($chekcsp == 1) {$queryStr = ' AND sId != ' . $scrivenerId; }
			$sql = "SELECT
					s.sName AS Name,
					s.sOffice AS Name2,
					s.sId AS ID,
					CONCAT('SC',LPAD(s.sId,4,'0')) as Code
				FROM
					tScrivener AS s
				LEFT JOIN
					tScrivenerSales AS ss ON ss.sScrivener = s.sId
				WHERE
					ss.sSales = '".$_SESSION['member_id']."'
				". $queryStr;
		}else{
            $queryStr = '';
            if($chekcsp == 1) {$queryStr = ' WHERE sId != ' . $scrivenerId; }
			$sql = "SELECT sName AS Name,sOffice AS Name2,sId AS ID,CONCAT('SC',LPAD(sId,4,'0')) as Code FROM tScrivener ". $queryStr ." ORDER BY sId ASC";
		}
		

		
	}elseif ($type == 2) {
		if ($_SESSION['member_pDep'] == 7) {
			$sql = "SELECT
						(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS Name,
						b.bStore AS Name2 ,
						b.bId AS ID,
						CONCAT((Select bCode From `tBrand` c Where c.bId = b.bBrand ),LPAD(b.bId,5,'0')) as Code
					FROM
						tBranch AS b
					LEFT JOIN
						tBranchSales AS bs ON bs.bBranch = b.bId
					WHERE
						bs.bSales = '".$_SESSION['member_id']."'
					ORDER BY b.bId ASC";
		}else{
			$sql = "SELECT (SELECT bName FROM tBrand AS b WHERE b.bId=bBrand) AS Name,bStore AS Name2 ,bId AS ID,CONCAT((Select bCode From `tBrand` c Where c.bId = bBrand ),LPAD(bId,5,'0')) as Code FROM tBranch ORDER BY bBrand ASC";
		}
		
	}
	$rs = $conn->Execute($sql);
	$option = '<option value="">請選擇</option>';
	while (!$rs->EOF) {
		// $data[$rs->fields['ID']]= $rs->fields['Name'].'-'.$rs->fields['Name2'];
		$data[$rs->fields['ID']]= $rs->fields['Code'].$rs->fields['Name']."(".$rs->fields['Name2'].")";

		$rs->MoveNext();
	}


	foreach ($data as $k => $v) {
		$selected = ($val == $k)?'selected=selected':'';
		$option .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
	}

	echo $option;
} else if ($act == 'del') {
    $sql = 'SELECT fCertifiedId FROM tFeedBackMoney WHERE fId = "'.$_POST['id'].'";';
    $rs = $conn->Execute($sql);
    $_cId = $rs->fields['fCertifiedId'];

	$sql = "UPDATE tFeedBackMoney SET fDelete ='1' WHERE fId ='".$_POST['id']."'";
    $conn->Execute($sql);

    $sql = 'UPDATE tContractCase SET cCaseFeedBackModifier = "'.$_SESSION['member_id'].'" WHERE cCertifiedId = "'.$_cId.'";';
    $conn->Execute($sql);

    $sql = $conn = $_cId = null;
	unset($sql, $conn, $_cId);
}


?>