<?php

include_once '../openadodb.php';

//4 5 月結案
$sql = "SELECT
			cc.cCertifiedId,
			cc.cSignDate,
			cc.cEndDate,
			cr.cBrand,
			cr.cBrand1,
			cr.cBrand2,
			cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cs.cScrivener,
			cc.cFeedbackTarget,
			cc.cFeedbackTarget1,
			cc.cFeedbackTarget2
		FROM
			tContractCase AS cc
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId = cc.cCertifiedId
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId = cc.cCertifiedId
		LEFT JOIN
			tContractProperty AS cp ON cp.cCertifiedId = cc.cCertifiedId
		WHERE 
			cCaseStatus = 2
			-- AND cc.cEndDate >= '2016-04-01 00:00:00' AND cc.cEndDate <='2016-05-31 23:59:59'
		GROUP BY cc.cCertifiedId
		";//cc.cEndDate >= '2016-04-01 00:00:00' AND cc.cEndDate <='2016-05-31 23:59:59'

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	# code...
	$list[] = $rs->fields;

	$rs->MoveNext();
}


for ($i=0; $i < count($list); $i++) { 


	if ($list[$i]['cBranchNum'] > 0) {
		if ($list[$i]['cBranchNum'] == 505 || $list[$i]['cFeedbackTarget'] == 2) {
			$sql='SELECT
					a.sId,
					a.sSales AS Sales,
					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
					b.sOffice
				FROM
					tScrivenerSales AS a,
					tScrivener AS b
				WHERE
					a.sScrivener='.$list[$i]['cScrivener'].' AND
					b.sId=a.sScrivener
				ORDER BY
					sId
				ASC';
		}else{
			$sql='SELECT
						a.bId,
						a.bSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
						b.bName,
						b.bStore
					FROM
						tBranchSales AS a,
						tBranch AS b
					WHERE
						bBranch='.$list[$i]['cBranchNum'].' AND
						b.bId=a.bBranch 

					ORDER BY
						bId
					ASC';
		}

		$rs = $conn->Execute($sql);

		$real_sales[] = $rs->fields['Sales'];
		// $msg = $list[$i]['cCertifiedId']."_".$list[$i]['cFeedbackTarget']."_".$rs->fields['Sales']."_".$list[$i]['cBranchNum'];
		// AddContract_Sales($list[$i]['cCertifiedId'],$list[$i]['cFeedbackTarget'],$rs->fields['Sales'],$list[$i]['cBranchNum']);
		
	}

	if ($list[$i]['cBranchNum1'] > 0) {
		
			if ($list[$i]['cBranchNum1'] == 505 || $list[$i]['cFeedbackTarget1'] == 2) {
				$sql='SELECT
						a.sId,
						a.sSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
						b.sOffice
					FROM
						tScrivenerSales AS a,
						tScrivener AS b
					WHERE
						a.sScrivener='.$list[$i]['cScrivener'].' AND
						b.sId=a.sScrivener
					ORDER BY
						sId
					ASC';
			}else{
				$sql='SELECT
							a.bId,
							a.bSales AS Sales,
							(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
							b.bName,
							b.bStore
						FROM
							tBranchSales AS a,
							tBranch AS b
						WHERE
							bBranch='.$list[$i]['cBranchNum1'].' AND
							b.bId=a.bBranch 

						ORDER BY
							bId
						ASC';
			}

			$rs = $conn->Execute($sql);

			$real_sales[] = $rs->fields['Sales'];
			// AddContract_Sales($list[$i]['cCertifiedId'],$list[$i]['cFeedbackTarget1'],$rs->fields['Sales'],$list[$i]['cBranchNum1']);
			
		
		
	}


	if ($list[$i]['cBranchNum2'] > 0) {
		if ($list[$i]['cBranchNum2'] == 505 || $list[$i]['cFeedbackTarget2'] == 2) {
			$sql='SELECT
					a.sId,
					a.sSales AS Sales,
					(SELECT pName FROM tPeopleInfo WHERE pId=a.sSales) as sSalesName,
					b.sOffice
				FROM
					tScrivenerSales AS a,
					tScrivener AS b
				WHERE
					a.sScrivener='.$list[$i]['cScrivener'].' AND
					b.sId=a.sScrivener
				ORDER BY
					sId
				ASC';
		}else{
			$sql='SELECT
						a.bId,
						a.bSales AS Sales,
						(SELECT pName FROM tPeopleInfo WHERE pId=a.bSales) as bSalesName,
						b.bName,
						b.bStore
					FROM
						tBranchSales AS a,
						tBranch AS b
					WHERE
						bBranch='.$list[$i]['cBranchNum2'].' AND
						b.bId=a.bBranch 

					ORDER BY
						bId
					ASC';
		}

		$rs = $conn->Execute($sql);

		
		$real_sales[] = $rs->fields['Sales'];
	
	}

	$check = checkSales($list[$i]['cCertifiedId'],$real_sales);
	
	if (!$check) {
		echo $list[$i]['cCertifiedId']."<br>";

		print_r($real_sales);
		echo "<bR>";
	}

	unset($real_sales);
}

function checkSales($cid,$arr)
{
	global $conn;

	$sql = "SELECT * FROM tContractSales WHERE cCertifiedId = '".$cid."' ";

	$rs = $conn->Execute($sql);

	$total=$rs->RecordCount();

	$c = 0;
	while (!$rs->EOF) {
			
		if (in_array($rs->fields['cSalesId'], $arr)) {
			$c++;
		}
	
		$rs->MoveNext();
	}

	if ($c != $total) {
		return false;
	}
	

	return true;
}


?>