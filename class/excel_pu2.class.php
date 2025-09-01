<?php

require_once __DIR__ . '/interface_excel.class.php';
require_once __DIR__ . '/contract.class.php';
require_once __DIR__ . '/brand.class.php';
require_once __DIR__ . '/scrivener.class.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class ExcelPu2 extends ExcelBase
{
    const CS_TARGET_NUM_BUYER     = 1;
    const CS_TARGET_NUM_OWNER     = 2;
    const CS_TARGET_NUM_BRANCH    = 3;
    const CS_TARGET_NUM_SCRIVENER = 4;

    public function __construct($rule)
    {
        parent::__construct();
        $this->mArrRule = $rule;
    }

    public function GenerateTitle()
    {
        $this->mArrTitle = array();
        $this->mArrBg    = array();

        $style = array('fill' => array(
            'type'  => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FDFF55')));

        $this->mArrBg['A1']  = $style;
        $this->mArrBg['B1']  = $style;
        $this->mArrBg['C1']  = $style;
        $this->mArrBg['D1']  = $style;
        $this->mArrBg['G1']  = $style;
        $this->mArrBg['I1']  = $style;
        $this->mArrBg['J1']  = $style;
        $this->mArrBg['M1']  = $style;
        $this->mArrBg['Q1']  = $style;
        $this->mArrBg['AF1'] = $style;
        $this->mArrBg['AG1'] = $style;
        $this->mArrBg['AH1'] = $style;
        $this->mArrBg['AI1'] = $style;
        $this->mArrBg['AJ1'] = $style;
        $this->mArrBg['AK1'] = $style;
        $this->mArrBg['AR1'] = $style;

        $this->mArrTitle['A1'] = '年度';
        $this->mArrTitle['B1'] = '進銷單號碼';
        $this->mArrTitle['C1'] = '日期';
        $this->mArrTitle['D1'] = '單別';
        $this->mArrTitle['E1'] = '庫別(出)';
        $this->mArrTitle['F1'] = '庫別(入)';
        $this->mArrTitle['G1'] = '客戶供應商';
        $this->mArrTitle['H1'] = '進銷特殊欄位';
        $this->mArrTitle['I1'] = '序號(客戶供應商統編)';
        $this->mArrTitle['J1'] = '序號(客戶供應商地址)';
        $this->mArrTitle['K1'] = '業務員代號';
        $this->mArrTitle['L1'] = '外幣代號';
        $this->mArrTitle['M1'] = '匯率';
        $this->mArrTitle['N1'] = '批號';
        $this->mArrTitle['O1'] = '訂單單號';
        $this->mArrTitle['P1'] = '採購單號';
        $this->mArrTitle['Q1'] = '稅別';
        $this->mArrTitle['R1'] = '發票號碼';
        $this->mArrTitle['S1'] = '製成品代號';
        $this->mArrTitle['T1'] = '轉B 帳註記';
        $this->mArrTitle['U1'] = '類別科目代號';
        $this->mArrTitle['V1'] = '立帳傳票號碼';
        $this->mArrTitle['W1'] = '沖帳傳票號碼';
        $this->mArrTitle['X1'] = '列印註記';
        $this->mArrTitle['Y1'] = '部門\工地編號';
        $this->mArrTitle['Z1'] = '專案\項目編號';

        $this->mArrTitle['AA1'] = 'A|B 帳唯一流水號';
        $this->mArrTitle['AB1'] = '產品組合代號';
        $this->mArrTitle['AC1'] = '帳款號碼';
        $this->mArrTitle['AD1'] = '報價單號';
        $this->mArrTitle['AE1'] = '內聯單號';
        $this->mArrTitle['AF1'] = '序號';
        $this->mArrTitle['AG1'] = '產品代號';
        $this->mArrTitle['AH1'] = '包裝別';
        $this->mArrTitle['AI1'] = '數量';
        $this->mArrTitle['AJ1'] = '未稅單價';
        $this->mArrTitle['AK1'] = '未稅金額';
        $this->mArrTitle['AL1'] = '外幣未稅單價';
        $this->mArrTitle['AM1'] = '外幣未稅金額';
        $this->mArrTitle['AN1'] = '生產數量';
        $this->mArrTitle['AO1'] = '明細備註';
        $this->mArrTitle['AP1'] = '明細備註二';
        $this->mArrTitle['AQ1'] = '單價含稅否」欄位';
        $this->mArrTitle['AR1'] = '請款客戶';
        $this->mArrTitle['AS1'] = '主檔備註';
        $this->mArrTitle['AT1'] = '主檔自定義欄位一';
        $this->mArrTitle['AU1'] = '主檔自定義欄位二';
        $this->mArrTitle['AV1'] = '主檔自定義欄位三';
        $this->mArrTitle['AW1'] = '主檔自定義欄位四';
        $this->mArrTitle['AX1'] = '主檔自定義欄位五';
        $this->mArrTitle['AY1'] = '主檔自定義欄位六';
        $this->mArrTitle['AZ1'] = '主檔自定義欄位七';

        $this->mArrTitle['BA1'] = '主檔自定義欄位八';
        $this->mArrTitle['BB1'] = '主檔自定義欄位九';
        $this->mArrTitle['BC1'] = '主檔自定義欄位十';
        $this->mArrTitle['BD1'] = '主檔自定義欄位十一';
        $this->mArrTitle['BE1'] = '主檔自定義欄位十二';
        $this->mArrTitle['BF1'] = '明細自定義欄位一';
        $this->mArrTitle['BG1'] = '明細自定義欄位二';
        $this->mArrTitle['BH1'] = '明細自定義欄位三';
        $this->mArrTitle['BI1'] = '明細自定義欄位四';
        $this->mArrTitle['BJ1'] = '明細自定義欄位五';
        $this->mArrTitle['BK1'] = '明細自定義欄位六';
        $this->mArrTitle['BL1'] = '明細自定義欄位七';
        $this->mArrTitle['BM1'] = '明細自定義欄位八';
        $this->mArrTitle['BN1'] = '明細自定義欄位九';
        $this->mArrTitle['BO1'] = '明細自定義欄位十';
        $this->mArrTitle['BP1'] = '明細自定義欄位十一';
        $this->mArrTitle['BQ1'] = '明細自定義欄位十二';
    }

    //取得合約銀行資訊
    private function ConBank()
    {
        $sql = '
			SELECT
				*
			FROM
				tContractBank
			WHERE
				cShow="1"
			ORDER BY
				cId
			ASC;
		';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $cbank = $stmt->fetchALL(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($cbank); $i++) {
            $arr[$i] = $cbank[$i]['cBankAccount']; //活儲帳號
        }

        return implode('","', $arr);
    }
    ##

    //取得合約銀行資訊(銀行代碼)
    private function ConBankCode()
    {
        $sql = '
			SELECT
				*
			FROM
				tContractBank
			WHERE
				cShow="1"
			ORDER BY
				cId
			ASC;
		';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $cbank = $stmt->fetchALL(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($cbank); $i++) {
            $arr[$i] = $cbank[$i]['cBankCode']; //活儲帳號
        }

        return implode('","', $arr);
    }
    ##

    private function GetCaseList()
    {
        $fds = '';
        $fde = '';

        $fds = $this->mArrRule['fds'];

        $tmp = explode('-', $fds);
        $tmp[0] += 1911;
        $tmp[1] = sprintf("%02d", $tmp[1]);
        $tmp[2] = sprintf("%02d", $tmp[2]);
        $fds    = join('-', $tmp);
        unset($tmp);

        $fde = $this->mArrRule['fde'];

        $tmp = explode('-', $fde);
        $tmp[0] += 1911;
        $tmp[1] = sprintf("%02d", $tmp[1]);
        $tmp[2] = sprintf("%02d", $tmp[2]);
        $fde    = join('-', $tmp);
        unset($tmp);

        $_data = array();
        $sql   = '
			SELECT
				tra.tVR_Code vr_code,
				tra.tMoney tMoney,
				tra.tExport_time tDate,
				cas.cCertifiedId cCertifiedId,
				cas.cCaseStatus cCaseStatus,
				cas.cFinishDate cFinishDate,
				tra.tBankLoansDate cEndDate,
				tra.tBankLoansDate tBankLoansDate
			FROM
				tBankTrans AS tra
			JOIN
				tContractCase AS cas ON tra.tMemo=cas.cCertifiedId
			WHERE
				tra.tExport="1"
				AND tra.tPayOk="1"
				AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
				AND tra.tBankLoansDate >="' . $fds . '"
				AND tra.tBankLoansDate<="' . $fde . '"
				AND tra.tAccount IN ("' . $this->ConBank() . '")
				AND tra.tObjKind IN ("點交(結案)","解除契約")
			GROUP BY
				tra.tMemo
			ORDER BY
				cas.cCertifiedId,tra.tExport_time
			ASC ;
		';

        // echo $sql;
        // die;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //無履保費出款但有出利息
        $_data1 = array();
        $sql    = '
			SELECT
				cas.cEscrowBankAccount as vr_code,
				cas.cBankList as tDate,
				cas.cCertifiedId as cCertifiedId,
				cas.cCaseStatus cCaseStatus,
				cas.cFinishDate cFinishDate,
				cas.cEndDate cEndDate
			FROM
				tContractCase AS cas
			WHERE
				cas.cBankList>="' . $fds . '"
				AND cas.cBankList<="' . $fde . '"
				AND cas.cBankList<>""
				AND cas.cBank IN ("' . $this->ConBankCode() . '")
			ORDER BY
				cas.cBankList,cas.cCertifiedId
			ASC ;
		';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $_data1 = $stmt->fetchALL(PDO::FETCH_ASSOC);
        $_data  = array_merge($_data, $_data1);
        unset($_data1);
        ##

        return $_data;
    }

    public function GenerateField()
    {
        $contract = new Contract();
        $sc       = new Scrivener();
        $brand    = new Brand();
        $index    = 2;

        //$list_case = $this->GetCaseList();
        $arr = $this->GetCaseList();
        $j   = 0;

        $max = count($arr);
        for ($i = 0; $i < $max; $i++) {
            if (@($arr[$i]['cCertifiedId'] != $arr[($i + 1)]['cCertifiedId'])) {
                $list_case[$j++] = $arr[$i];
            }
        }
        unset($arr);
        $max = count($list_case);
        for ($i = 0; $i < $max; $i++) {
            for ($j = 0; $j < ($max - 1); $j++) {
                if ($list_case[$j]['tDate'] > $list_case[$i]['tDate']) {
                    $tmp           = $list_case[$i];
                    $list_case[$i] = $list_case[$j];
                    $list_case[$j] = $tmp;
                    unset($tmp);
                }
            }
        }

        $arrItem = array();
        foreach ($list_case as $k => $v) {
            //設定最後一碼
            $b_index             = 0;
            $o_index             = 0;
            $r_index             = 0;
            $s_index             = 0;
            $a_index             = 0;
            $InvoiceDonate_money = 0;
            ##

            $giver_num        = ""; //客戶供應商代號
            $target           = 0;
            $data_buyer       = $contract->GetBuyer($v['cCertifiedId']);
            $data_owner       = $contract->GetOwner($v['cCertifiedId']);
            $data_invoice     = $contract->GetInvoice($v['cCertifiedId']);
            $data_expenditure = $contract->GetExpenditure($v['cCertifiedId']);
            $data_property    = $contract->GetProperty($v['cCertifiedId']);
            $data_res         = $contract->GetRealstate($v['cCertifiedId']);
            $data_sc          = $contract->GetScrivener($v['cCertifiedId']);
            $data_income      = $contract->GetIncome($v['cCertifiedId']);

            $info_sc = $sc->GetScrivenerInfo($data_sc['cScrivener']);

            //第一家仲介
            $info_branch = $brand->GetBranch($data_res['cBranchNum']);
            if (!empty($info_branch[0])) {
                $info_branch = $info_branch[0];
            }
            ##

            //第二家仲介
            $info_branch1 = $brand->GetBranch($data_res['cBranchNum1']);
            if (!empty($info_branch1[0])) {
                $info_branch1 = $info_branch1[0];
            }
            ##

            //第三家仲介
            $info_branch2 = $brand->GetBranch($data_res['cBranchNum2']);
            if (!empty($info_branch2[0])) {
                $info_branch2 = $info_branch2[0];
            }
            ##

            $target = $this->GetInvoiceTarget($data_invoice);

            //if ($v['cCaseStatus']=="4") {
            //    $this->ConvertToROCYear($pu_roc_year, $v['cFinishDate']);
            //    $this->ConvertToROCDate($pu_roc_date, $v['cFinishDate']);
            //}
            //else {
            $this->ConvertToROCYear($pu_roc_year, $v['cEndDate']);
            $this->ConvertToROCDate($pu_roc_date, $v['cEndDate']);
            //}

            //$this->ConvertToROCDate($now_roc_date, date('Y-m-d', time()));
            // $now_roc_date = $v['tDate'] ;   //
            $now_roc_date = $v['cEndDate']; ///20150618改為tBankLoansDate
            if ($now_roc_date) {
                $now_roc_date = preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", '', $now_roc_date);
                $tmp          = explode('-', $now_roc_date);
                $now_roc_date = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
                unset($tmp);
            }

            //買方
            if (($target & (1 << 1)) > 0) {
                //捐贈加總
                if ($data_buyer['cInvoiceDonate'] == 1) {
                    if (preg_match("/^\d{8}$/", $data_buyer['cIdentifyId'])) {

                        $data_buyer['cInvoiceMoney'] = round($data_buyer['cInvoiceMoney'] / 1.05);

                    }

                    $InvoiceDonate_money = $InvoiceDonate_money + $data_buyer['cInvoiceMoney'];
                }
                //合約書買方

                if ($data_buyer['cInvoiceMoney'] > 0 && $data_buyer['cInvoiceDonate'] == '0') {
                    $giver_num = $v['cCertifiedId'] . '1' . $this->no2ascii(++$b_index);
                    $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                    $AQ        = '1';
                    if (($data_buyer['cCategoryIdentify'] == '1') || (preg_match("/\w{10}/", $data_buyer['cIdentifyId']))) {
                        $invoice_kind = '2';
                    } else if (preg_match("/^\d{8}$/", $data_buyer['cIdentifyId'])) {
                        $invoice_kind = '3';
                        //$data_invoice['cInvoiceBuyer'] = round($data_invoice['cInvoiceBuyer'] / 1.05) ;
                        $data_buyer['cInvoiceMoney'] = round($data_buyer['cInvoiceMoney'] / 1.05);
                        $AQ                          = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                    //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                    //}
                    ##

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $_num;
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                    $arrItem[$index]['AJ'] = $data_buyer['cInvoiceMoney'];
                    //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                    $arrItem[$index]['AK'] = $data_buyer['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $_num;
                    $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                    $index++;
                }
                ##

                //發票新合約書買方(2015-07-31)
                $_arr = array();

                $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractBuyer" ORDER BY cId ASC;';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                foreach ($_arr as $ka => $va) {

                    //捐贈加總
                    if ($va['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                    }
                    if ($va['cInvoiceMoney'] > 0 && $va['cInvoiceDonate'] == '0') {
                        $giver_num = $v['cCertifiedId'] . '1' . $this->no2ascii(++$b_index);
                        // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                        $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                        if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                            $invoice_kind = '2';
                            $AQ           = '1';
                        } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                            $invoice_kind        = '3';
                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                            $AQ                  = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                unset($_arr);
                ##

                //其他買方
                $sql = '
					SELECT
						*
					FROM
						tContractOthers
					WHERE
						cCertifiedId="' . $v['cCertifiedId'] . '"
						AND cIdentity="1"
					ORDER BY
						cId
					ASC
				';

                unset($_arr);
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                $_arr_max = count($_arr);
                for ($i = 0; $i < $_arr_max; $i++) {
                    //捐贈加總
                    if ($_arr[$i]['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {

                            $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $_arr[$i]['cInvoiceMoney'];
                    }

                    if ($_arr[$i]['cInvoiceMoney'] > 0 && $_arr[$i]['cInvoiceDonate'] == '0') {
                        $giver_num = $v['cCertifiedId'] . '1' . $this->no2ascii(++$b_index);
                        $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        $AQ        = '1';
                        if (preg_match("/^\w{10}$/", $_arr[$i]['cIdentifyId'])) {
                            $invoice_kind = '2';
                        } else if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {
                            $invoice_kind              = '3';
                            $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);
                            $AQ                        = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                        //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                        //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                        //}
                        ##

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        $arrItem[$index]['AJ'] = $_arr[$i]['cInvoiceMoney'];
                        $arrItem[$index]['AK'] = $_arr[$i]['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                ##
                //發票新合約書其他買方(2015-07-31)
                $_arr = array();

                $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersB" ORDER BY cId ASC;';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                foreach ($_arr as $ka => $va) {
                    //捐贈加總
                    if ($va['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                    }

                    if ($va['cInvoiceMoney'] > 0 && $va['cInvoiceDonate'] == '0') {
                        $giver_num = $giver_num = $v['cCertifiedId'] . '1' . $this->no2ascii(++$b_index);
                        // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                        $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                        if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                            $invoice_kind = '2';
                            $AQ           = '1';
                        } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                            $invoice_kind        = '3';
                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                            $AQ                  = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                unset($_arr);
                ##
            }
            ##

            //賣方
            if (($target & (1 << 2)) > 0) {
                //捐贈加總
                if ($data_owner['cInvoiceDonate'] == 1) {
                    if (preg_match("/^\d{8}$/", $data_owner['cIdentifyId'])) {

                        $data_owner['cInvoiceMoney'] = round($data_owner['cInvoiceMoney'] / 1.05);

                    }

                    $InvoiceDonate_money = $InvoiceDonate_money + $data_owner['cInvoiceMoney'];
                }

                //合約書賣方
                if ($data_owner['cInvoiceMoney'] > 0 && $data_owner['cInvoiceDonate'] == '0') {
                    $giver_num = $v['cCertifiedId'] . '2' . $this->no2ascii(++$o_index);
                    $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                    $AQ        = '1';
                    if (($data_owner['cCategoryIdentify'] == '1') || (preg_match("/\w{10}/", $data_owner['cIdentifyId']))) {
                        $invoice_kind = '2';
                    } else {
                        $invoice_kind = '3';
                        //$data_invoice['cInvoiceOwner'] = round($data_invoice['cInvoiceOwner'] / 1.05) ;
                        $data_owner['cInvoiceMoney'] = round($data_owner['cInvoiceMoney'] / 1.05);
                        $AQ                          = '0';
                    }

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                    //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                    //}
                    ##

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $_num;
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceOwner'];
                    $arrItem[$index]['AJ'] = $data_owner['cInvoiceMoney'];
                    //$arrItem[$index]['AK'] = $data_invoice['cInvoiceOwner'];
                    $arrItem[$index]['AK'] = $data_owner['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $_num;
                    $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                    $index++;
                }
                ##
                //發票新合約書賣方(2015-07-31)
                $_arr = array();

                $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOwner" ORDER BY cId ASC;';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                foreach ($_arr as $ka => $va) {
                    //捐贈加總
                    if ($va['cInvoiceDonate'] == 1 && $va['cInvoiceDonate'] == '0') {
                        if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                    }
                    if ($va['cInvoiceMoney'] > 0) {
                        $giver_num = $v['cCertifiedId'] . '2' . $this->no2ascii(++$o_index);
                        // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                        $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                            $invoice_kind = '2';
                            $AQ           = '1';
                        } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                            $invoice_kind        = '3';
                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                            $AQ                  = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                unset($_arr);
                ##
                //其他賣方
                $sql = '
					SELECT
						*
					FROM
						tContractOthers
					WHERE
						cCertifiedId="' . $v['cCertifiedId'] . '"
						AND cIdentity="2"
					ORDER BY
						cId
					ASC
				';

                unset($_arr);
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                $_arr_max = count($_arr);
                for ($i = 0; $i < $_arr_max; $i++) {
                    //捐贈加總
                    if ($_arr[$i]['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {

                            $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $_arr[$i]['cInvoiceMoney'];
                    }
                    if ($_arr[$i]['cInvoiceMoney'] > 0 && $_arr[$i]['cInvoiceDonate'] == '0') {
                        $giver_num = $v['cCertifiedId'] . '2' . $this->no2ascii(++$o_index);
                        $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        $AQ        = '1';
                        if (preg_match("/^\w{10}$/", $_arr[$i]['cIdentifyId'])) {
                            $invoice_kind = '2';
                        } else if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {
                            $invoice_kind              = '3';
                            $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);
                            $AQ                        = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                        //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                        //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                        //}
                        ##

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        $arrItem[$index]['AJ'] = $_arr[$i]['cInvoiceMoney'];
                        $arrItem[$index]['AK'] = $_arr[$i]['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                ##
                //發票新合約書其他賣方(2015-07-31)
                $_arr = array();

                $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersO" ORDER BY cId ASC;';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                foreach ($_arr as $ka => $va) {
                    //捐贈加總
                    if ($va['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                    }

                    if ($va['cInvoiceMoney'] > 0 && $va['cInvoiceDonate'] == '0') {
                        $giver_num = $v['cCertifiedId'] . '2' . $this->no2ascii(++$o_index);
                        // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                        $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                        if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                            $invoice_kind = '2';
                            $AQ           = '1';
                        } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                            $invoice_kind        = '3';
                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                            $AQ                  = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                unset($_arr);
                ##
            }
            ##

            //仲介
            if (($target & (1 << 3)) > 0) {
                //第一家仲介
                if (!empty($info_branch)) {
                    //捐贈加總
                    if ($data_res['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $data_res['cIdentifyId'])) {

                            $data_res['cInvoiceMoney'] = round($data_res['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $data_res['cInvoiceMoney'];
                    }

                    if ($data_res['cInvoiceMoney'] > 0 && $data_res['cInvoiceDonate'] == 0) {
                        $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                        $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        //$data_invoice['cInvoiceRealestate'] = round($data_invoice['cInvoiceRealestate'] / 1.05) ;
                        $data_res['cInvoiceMoney'] = round($data_res['cInvoiceMoney'] / 1.05);

                        //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                        //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                        //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                        //}
                        ##

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = '3';
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AK'] = $data_res['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = '0';
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }

                    //發票新合約書仲介(2015-07-31)
                    $_arr = array();

                    $sql = 'SELECT
							a.*,
							b.zCity,
							b.zArea
						FROM
							tContractInvoiceExt AS a
						LEFT JOIN
							tZipArea AS b ON b.zZip=a.cInvoiceZip
						WHERE
							a.cCertifiedId="' . $v['cCertifiedId'] . '"
							AND a.cDBName="tContractRealestate"
						ORDER BY
							a.cId
						ASC;';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                    foreach ($_arr as $ka => $va) {
                        //捐贈加總
                        if ($va['cInvoiceDonate'] == 1) {
                            if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                                $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                            }

                            $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                        }

                        if ($va['cInvoiceMoney'] > 0 & $va['cInvoiceDonate'] == 0) {
                            $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                            // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                            $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                            if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                                $invoice_kind = '2';
                                $AQ           = '1';
                            } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                                $invoice_kind        = '3';
                                $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                                $AQ                  = '0';
                            } else {
                                $invoice_kind = '';
                            }

                            $arrItem[$index]['A']  = $pu_roc_year;
                            $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                            $arrItem[$index]['C']  = $now_roc_date;
                            $arrItem[$index]['D']  = '20';
                            $arrItem[$index]['G']  = $_num;
                            $arrItem[$index]['I']  = '00';
                            $arrItem[$index]['J']  = '00';
                            $arrItem[$index]['M']  = '1';
                            $arrItem[$index]['Q']  = $invoice_kind;
                            $arrItem[$index]['AF'] = '0001';
                            $arrItem[$index]['AG'] = 'A001';
                            $arrItem[$index]['AH'] = '0';
                            $arrItem[$index]['AI'] = '1';
                            //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                            $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                            //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                            $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                            $arrItem[$index]['AQ'] = $AQ;
                            $arrItem[$index]['AR'] = $_num;
                            $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                            $index++;
                        }
                    }
                    unset($_arr);
                    ##
                }
                ##

                //第二家仲介
                if (!empty($info_branch1)) {
                    //捐贈加總
                    if ($data_res['cInvoiceDonate1'] == 1) {
                        if (preg_match("/^\d{8}$/", $data_res['cIdentifyId'])) {

                            $data_res['cInvoiceMoney1'] = round($data_res['cInvoiceMoney1'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $data_res['cInvoiceMoney1'];
                    }
                    ##
                    if ($data_res['cInvoiceMoney1'] > 0 && $data_res['cInvoiceDonate'] == 0) {
                        $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                        $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        //$data_invoice['cInvoiceRealestate'] = round($data_invoice['cInvoiceRealestate'] / 1.05) ;
                        $data_res['cInvoiceMoney1'] = round($data_res['cInvoiceMoney1'] / 1.05);

                        //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                        //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                        //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                        //}
                        ##

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = '3';
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney1'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AK'] = $data_res['cInvoiceMoney1'];
                        $arrItem[$index]['AQ'] = '0';
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }

                    //發票新合約書仲介2(2015-07-31)
                    $_arr = array();

                    $sql = 'SELECT
								a.*,
								b.zCity,
								b.zArea
							FROM
								tContractInvoiceExt AS a
							LEFT JOIN
								tZipArea AS b ON b.zZip=a.cInvoiceZip
							WHERE
								a.cCertifiedId="' . $v['cCertifiedId'] . '"
								AND a.cDBName="tContractRealestate1"
							ORDER BY
								a.cId
							ASC;';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                    foreach ($_arr as $ka => $va) {
                        //捐贈加總
                        if ($va['cInvoiceDonate'] == 1) {
                            if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                                $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                            }

                            $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                        }

                        if ($va['cInvoiceMoney'] > 0 & $va['cInvoiceDonate'] == 0) {
                            $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                            // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                            $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                            if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                                $invoice_kind = '2';
                                $AQ           = '1';
                            } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                                $invoice_kind        = '3';
                                $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                                $AQ                  = '0';
                            } else {
                                $invoice_kind = '';
                            }

                            $arrItem[$index]['A']  = $pu_roc_year;
                            $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                            $arrItem[$index]['C']  = $now_roc_date;
                            $arrItem[$index]['D']  = '20';
                            $arrItem[$index]['G']  = $_num;
                            $arrItem[$index]['I']  = '00';
                            $arrItem[$index]['J']  = '00';
                            $arrItem[$index]['M']  = '1';
                            $arrItem[$index]['Q']  = $invoice_kind;
                            $arrItem[$index]['AF'] = '0001';
                            $arrItem[$index]['AG'] = 'A001';
                            $arrItem[$index]['AH'] = '0';
                            $arrItem[$index]['AI'] = '1';
                            //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                            $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                            //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                            $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                            $arrItem[$index]['AQ'] = $AQ;
                            $arrItem[$index]['AR'] = $_num;
                            $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                            $index++;
                        }
                    }
                    unset($_arr);
                    ##
                }
                ##

                //第三家仲介
                if (!empty($info_branch2)) {
                    //捐贈加總
                    if ($data_res['cInvoiceDonate2'] == 1) {
                        if (preg_match("/^\d{8}$/", $data_res['cIdentifyId'])) {

                            $data_res['cInvoiceMoney2'] = round($data_res['cInvoiceMoney2'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $data_res['cInvoiceMoney2'];
                    }

                    if ($data_res['cInvoiceMoney2'] > 0 && $data_res['cInvoiceDonate2'] == 0) {
                        $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                        $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                        //$data_invoice['cInvoiceRealestate'] = round($data_invoice['cInvoiceRealestate'] / 1.05) ;
                        $data_res['cInvoiceMoney2'] = round($data_res['cInvoiceMoney2'] / 1.05);

                        //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                        //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                        //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                        //}
                        ##

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = '3';
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney2'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceRealestate'];
                        $arrItem[$index]['AK'] = $data_res['cInvoiceMoney2'];
                        $arrItem[$index]['AQ'] = '0';
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                ##

                //發票新合約書仲介三(2015-07-31)
                $_arr = array();

                $sql = 'SELECT
							a.*,
							b.zCity,
							b.zArea
						FROM
							tContractInvoiceExt AS a
						LEFT JOIN
							tZipArea AS b ON b.zZip=a.cInvoiceZip
						WHERE
							a.cCertifiedId="' . $v['cCertifiedId'] . '"
							AND a.cDBName="tContractRealestate2"
						ORDER BY
							a.cId
						ASC;';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

                foreach ($_arr as $ka => $va) {
                    //捐贈加總
                    if ($va['cInvoiceDonate'] == 1) {
                        if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        }

                        $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                    }

                    if ($va['cInvoiceMoney'] > 0 & $va['cInvoiceDonate'] == 0) {
                        $giver_num = $v['cCertifiedId'] . '3' . $this->no2ascii(++$r_index);
                        // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                        $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                        if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                            $invoice_kind = '2';
                            $AQ           = '1';
                        } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                            $invoice_kind        = '3';
                            $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                            $AQ                  = '0';
                        } else {
                            $invoice_kind = '';
                        }

                        $arrItem[$index]['A']  = $pu_roc_year;
                        $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                        $arrItem[$index]['C']  = $now_roc_date;
                        $arrItem[$index]['D']  = '20';
                        $arrItem[$index]['G']  = $_num;
                        $arrItem[$index]['I']  = '00';
                        $arrItem[$index]['J']  = '00';
                        $arrItem[$index]['M']  = '1';
                        $arrItem[$index]['Q']  = $invoice_kind;
                        $arrItem[$index]['AF'] = '0001';
                        $arrItem[$index]['AG'] = 'A001';
                        $arrItem[$index]['AH'] = '0';
                        $arrItem[$index]['AI'] = '1';
                        //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                        //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                        $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                        $arrItem[$index]['AQ'] = $AQ;
                        $arrItem[$index]['AR'] = $_num;
                        $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                        $index++;
                    }
                }
                unset($_arr);
                ##
            }
            ##

            //合約書代書
            if (($target & (1 << 4)) > 0) {

                $giver_num = $v['cCertifiedId'] . '4' . $this->no2ascii(++$s_index);
                $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                //}
                ##

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $_num;
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = '3';
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_invoice['cInvoiceScrivener'];
                $arrItem[$index]['AK'] = $data_invoice['cInvoiceScrivener'];
                $arrItem[$index]['AQ'] = '0'; //0:三聯式 1:二聯式
                $arrItem[$index]['AR'] = $_num;
                $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                $index++;

            }
            //發票新合約書地政士(2015-07-31)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractScrivener" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                //捐贈加總
                if ($va['cInvoiceDonate'] == 1) {
                    if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {

                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                    }

                    $InvoiceDonate_money = $InvoiceDonate_money + $va['cInvoiceMoney'];
                }

                if ($va['cInvoiceMoney'] > 0 & $va['cInvoiceDonate'] == 0) {
                    $giver_num = $v['cCertifiedId'] . '4' . $this->no2ascii(++$s_index);
                    // $_num = $pu_roc_date.str_pad($flowNo ++,6,'0',STR_PAD_LEFT) ;
                    $_num = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind = '2';
                        $AQ           = '1';
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind        = '3';
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $AQ                  = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $_num;
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    //$arrItem[$index]['AJ'] = $data_invoice['cInvoiceBuyer'];
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    //$arrItem[$index]['AK'] = $data_invoice['cInvoiceBuyer'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $_num;
                    $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                    $index++;
                }
            }
            unset($_arr);
            ##
            ##

            //合約書其他(創世基金會)
            if (($target & (1 << 5)) > 0 || $InvoiceDonate_money > 0) {
                $giver_num = $v['cCertifiedId'] . '51';
                $_num      = $v['cCertifiedId'] . $this->no2ascii(++$a_index);

                if ($InvoiceDonate_money > 0) {
                    $data_invoice['cInvoiceOther'] = $data_invoice['cInvoiceOther'] + $InvoiceDonate_money;
                }

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                //if ($this->checkInvoice($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6))) {
                //    $this->AddInvoiceNo($v['cCertifiedId'], $pu_roc_date.substr($giver_num, -6)) ;
                //}
                ##

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $pu_roc_date . substr($giver_num, -6);
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $_num;
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = '2';
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_invoice['cInvoiceOther'];
                $arrItem[$index]['AK'] = $data_invoice['cInvoiceOther'];
                $arrItem[$index]['AQ'] = '1';
                $arrItem[$index]['AR'] = $_num;
                $arrItem[$index]['AT'] = '保證號碼:' . $giver_num;

                $index++;
            }
            ##
        }
        $this->mArrField = $arrItem;
    }

    public function GenerateFileName($prefix)
    {
        $filename = $prefix . "_";
        $filename .= str_replace('-', '', $this->mArrRule['fds']) . "_";
        $filename .= str_replace('-', '', $this->mArrRule['fde']);
        return $filename;
    }

    //數字轉文字
    private function no2ascii($no = 0)
    {
        if ($no >= 10) {
            $no += 55;
            $no = chr($no);
        }
        return $no;
    }
    ##

    //將發票號碼寫入資料庫內(2015-04-07)
    private function AddInvoiceNo($cId, $dNo, $iNo = '', $iDt = '', $aName = '', $aMoney = 0)
    {
        if ($cId && $dNo) {
            $sql = '
				INSERT INTO
					tAccountDelivery
				SET
					aCertifiedId="' . $cId . '",
					aDeliveryNo="' . $dNo . '",
					aInvoiceNo="' . $iNo . '",
					aInvoiceDate="' . $iDt . '",
					aName="' . $aName . '",
					aMoney="' . $aMoney . '"
			;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            return true;
        } else {
            return false;
        }

    }
    ##

    //檢查發票資料是否已存在?若存在則將記錄刪除(2015-04-07)
    private function checkInvoice($cId, $dNo)
    {
        if ($cId && $dNo) {
            $arr = array();

            $sql  = 'SELECT * FROM tAccountDelivery WHERE aCertifiedId="' . $cId . '" AND aDeliveryNo="' . $dNo . '";';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            if (count($arr) > 0) {
                $sql  = 'DELETE FROM tAccountDelivery WHERE aCertifiedId="' . $cId . '" AND aDeliveryNo="' . $dNo . '";';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
            }

            return true;
        } else {
            return false;
        }

    }
    ##
}
