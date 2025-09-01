<?php
include_once '../../configs/config.class.php';
include_once 'class/SmartyMain.class.php';
include_once 'class/brand.class.php';
include_once '../../session_check.php' ;
include_once '../../tracelog.php' ;
include_once '../../openadodb.php';

function checkBrandFeed($cId,$branch){
	global $conn;

	if (!$branch) {
		return false;
	}

	$sql = "SELECT fId FROM tFeedBackMoney WHERE fType = '2' AND fDelete = 0 AND fCertifiedId = '".$cId."' AND fStoreId = '".$branch."'";
	$rs = $conn->Execute($sql);
	$total = $rs->RecordCount();

	if ($total > 0) {
		return true; //有資料 不要新增
	}else{
		return false; //
	}
	

}

function addFeedbackmoney($cId,$branch,$money){
	global $conn;

	//先取得業務
	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$branch."'";
	$rs = $conn->Execute($sql);
	$sales = $rs->fields['bSales'];
	
	//
	$sql = "INSERT INTO
				tFeedBackMoney
			SET
				fType = 2,
				fCertifiedId = '".$cId."',
				fStoreId = '".$branch."',
				fMoney = '".$money."',
				fSales = '".$sales."'
			";
	$conn->Execute($sql);
	

}

function updateFeedbackmoney($cId,$branch,$money){
	global $conn;

	//先取得業務
	$sql = "SELECT bSales FROM tBranchSales WHERE bBranch = '".$branch."'";
	$rs = $conn->Execute($sql);
	$sales = $rs->fields['bSales'];
	
	//
	$sql = "UPDATE
				tFeedBackMoney
			SET
				fMoney = '".$money."'
			WHERE
				fType = 2,
				fCertifiedId = '".$cId."',
				fStoreId = '".$branch."',
			";
	// echo $sql;
	$conn->Execute($sql);
	

}

/**
 * 確認是否為實易不動產加盟店
 * return: 是=Array、否=false
 */
Function checkEBRealty($bId) {
    global $conn;

	$sql = 'SELECT 
		a.bId,
		a.bBrand,
		a.bSerialnum,
		a.bRecall as branchRecall,
		b.bRecall,
		b.bBranch AS TargetBranch,
		b.bSignDate
	FROM
		tBranch AS a
	JOIN
		tBrand AS b ON b.bId=a.bBrand
	WHERE
		a.bId 			 =  "'.$bId.'"
		AND a.bBrand 	 =  80
		AND a.bSerialnum <> "90468218"
	;';
	$rs = $conn->Execute($sql);

	return $rs->EOF ? false : $rs->fields;
}

$tlog = new TraceLog() ;
$tlog->updateWrite($_SESSION['member_id'], json_encode($_POST), '更新仲介品牌明細內容') ;

$brand = new Brand();
$data = $brand->GetBrand($_POST["id"]);//取得舊資料比對
$_POST['signDate'] = (substr($_POST['signDate'], 0,3)+1911).substr($_POST['signDate'], 3);

$brand->SaveBrand($_POST);

###品牌回饋
//回饋異動
if ($data['signDate'] != $_POST['signDate'] || $_POST['recall'] != $data['bRecall'] || $_POST['TargetBranch'] != $data['bBranch']) {
	$startDate = date("Y-m-d", strtotime($_POST['signDate']."+1 day"));

	$sql ="SELECT 
			cc.cCertifiedId    	 AS cCertifiedId,
			ci.cTotalMoney     	 AS cTotalMoney,
			ci.cCertifiedMoney 	 AS cerifiedmoney,
			cr.cBrand 		   	 AS brand,
			cr.cBrand1 		   	 AS brand1,
			cr.cBrand2 		   	 AS brand2,
			cr.cBranchNum 	   	 AS branch,
			cr.cBranchNum1 	   	 AS branch1,
			cr.cBranchNum2 	   	 AS branch2
		FROM 
			 tContractCase 		 AS cc
		JOIN tContractRealestate AS cr ON cr.cCertifyId   = cc.cCertifiedId
		JOIN tContractIncome 	 AS ci ON ci.cCertifiedId = cc.cCertifiedId
		JOIN tContractScrivener  AS cs ON cs.cCertifiedId = cc.cCertifiedId
		WHERE 
        	ci.cTotalMoney 			     <> 0 
			AND cc.cCaseFeedBackModifier <> '' 
			AND ci.cCertifiedMoney 	     <> 0 
			AND cc.cFeedBackClose 		 =  0 
			AND cc.cSignDate 			 >= '".$startDate." 00:00:00' 
			AND (
				cr.cBrand     = '".$_POST['id']."' 
				OR cr.cBrand1 = '".$_POST['id']."' 
				OR cr.cBrand2 = '".$_POST['id']."'
			)
	;";

	if ($_POST['id'] == 80) {	//實易不動產品牌
		$rs = $conn->Execute($sql);

        while (!$rs->EOF) {
			$_EBFeedBackBrandRecall = 0;	//實易不動產總部回饋比率
            $brandTotalCount 		= 0;	//案件仲介店家數

            if ($rs->fields['brand'] > 0) {
                if ($rs->fields['brand'] == $_POST['id']) {
                    $_branch = checkEBRealty($rs->fields['branch']);	//取得加盟店資訊

                    if ($_branch) {
						$_EBFeedBackBrandRecall += $_branch['bRecall'] - $_branch['branchRecall'];	//實易不動產品牌總部回饋比率加總
                    }
                    $_branch = null; unset($_branch);
                }

                $brandTotalCount ++;
            }

            if ($rs->fields['brand1'] > 0) {
                if ($rs->fields['brand1'] == $_POST['id']) {
					$_branch = checkEBRealty($rs->fields['branch1']);	//取得加盟店資訊

                    if ($_branch) {
						$_EBFeedBackBrandRecall += $_branch['bRecall'] - $_branch['branchRecall'];	//實易不動產品牌總部回饋比率加總
                    }
					$_branch = null; unset($_branch);
                }

                $brandTotalCount ++;
            }

            if ($rs->fields['brand2'] > 0) {
                if ($rs->fields['brand2'] == $_POST['id']) {
					$_branch = checkEBRealty($rs->fields['branch2']);	//取得加盟店資訊

                    if ($_branch) {
						$_EBFeedBackBrandRecall += $_branch['bRecall'] - $_branch['branchRecall'];	//實易不動產品牌總部回饋比率加總
                    }
					$_branch = null; unset($_branch);
                }

                $brandTotalCount ++;
            }
            
			//如果有實易不動產品牌加盟店
			if ($_EBFeedBackBrandRecall > 0) {		//實易不動產總部回饋比率有存在時代表要建立其他回饋對象
				$money = ($rs->fields['cerifiedmoney'] / $brandTotalCount) * ($_EBFeedBackBrandRecall / 100);	//各家仲介店分配的履保費金額

				//實易不動產回饋給店家: 實易不動產總管理(bId = 4901)
				$_POST['TargetBranch'] = empty($_POST['TargetBranch']) ? 4901 : $_POST['TargetBranch'];
				##
				
				//查詢案件是否有回饋金資訊
				if (!checkBrandFeed($rs->fields['cCertifiedId'], $_POST['TargetBranch'])) { 	//無回饋金(tFeedBackMoney)資料
					addFeedbackmoney($rs->fields['cCertifiedId'], $_POST['TargetBranch'], $money);
					$CertifiedId[] = $rs->fields['cCertifiedId'];
				} else {	//有資料
					updateFeedbackmoney($rs->fields['cCertifiedId'], $_POST['TargetBranch'], $money);
					$CertifiedId[] = $rs->fields['cCertifiedId'];
				}
				##
			}
			##

			$rs->MoveNext();
        }
	} else {
        $recall = $_POST['recall']/100;

        $rs = $conn->Execute($sql);
        while (!$rs->EOF) {
            $brandTotalCount = 0;
            $brandCount = 0;//品牌

            if ($rs->fields['brand'] > 0) {
                if ($rs->fields['brand'] == $_POST['id']) {
                    $brandCount++;
                }
                $brandTotalCount++;
            }

            if ($rs->fields['brand1'] > 0) {
                if ($rs->fields['brand1'] == $_POST['id']) {
                    $brandCount++;
                }
                $brandTotalCount++;
            }

            if ($rs->fields['brand2'] > 0) {
                if ($rs->fields['brand2'] == $_POST['id']) {
                    $brandCount++;
                }
                $brandTotalCount++;
            }

            $money = round((($rs->fields['cerifiedmoney'] * $recall)*$brandCount)/$brandTotalCount) ;//回饋金 配件要平分

            if (!checkBrandFeed($rs->fields['cCertifiedId'], $_POST['TargetBranch'])) { //檢查是否有資料
                addFeedbackmoney($rs->fields['cCertifiedId'], $_POST['TargetBranch'], $money);
                $CertifiedId[] = $rs->fields['cCertifiedId'];
            } else {
                updateFeedbackmoney($rs->fields['cCertifiedId'], $_POST['TargetBranch'], $money);
                $CertifiedId[] = $rs->fields['cCertifiedId'];
            }

        
            $rs->MoveNext();
        }
    }
}
###

if (is_array($CertifiedId)) {
	echo '回饋異動案件：'.implode(',', $CertifiedId)."\r\n";
}

echo "儲存完成";
?>
