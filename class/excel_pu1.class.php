<?php

require_once __DIR__ . '/interface_excel.class.php';
require_once __DIR__ . '/contract.class.php';
require_once __DIR__ . '/brand.class.php';
require_once __DIR__ . '/scrivener.class.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class ExcelPu1 extends ExcelBase
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
        $this->mArrTitle['BR1'] = '外銷方式';
        $this->mArrTitle['BS1'] = '付款方式代號';
        $this->mArrTitle['BT1'] = '送貨方式代號';
        $this->mArrTitle['BU1'] = '對方單號';
        $this->mArrTitle['BV1'] = '其他請款費用';
        $this->mArrTitle['BW1'] = '發票日期';
        $this->mArrTitle['BX1'] = '結帳日';
        $this->mArrTitle['BY1'] = '收款日';
        $this->mArrTitle['BZ1'] = '對方品名/品名備註';
        $this->mArrTitle['CA1'] = '對方產品代號';
        $this->mArrTitle['CB1'] = '包裝單位';
        $this->mArrTitle['CC1'] = '包裝數量';
        $this->mArrTitle['CD1'] = '散裝數量';
        $this->mArrTitle['CE1'] = '線上數量換算比例';
        $this->mArrTitle['CF1'] = '包裝單價(登打)';
        $this->mArrTitle['CG1'] = '包裝單價(未稅)';
        $this->mArrTitle['CH1'] = '發票捐贈註記';
        $this->mArrTitle['CI1'] = '發票捐贈對象';
        $this->mArrTitle['CJ1'] = '電子發票註記';
        $this->mArrTitle['CK1'] = '列印紙本電子發票註記';
        $this->mArrTitle['CL1'] = '載具類別號碼';
        $this->mArrTitle['CM1'] = '載具顯碼id';
        $this->mArrTitle['CN1'] = '載具隱碼id';
        $this->mArrTitle['CO1'] = '捐贈(愛心)碼';
        $this->mArrTitle['CP1'] = '對帳日';
        $this->mArrTitle['CQ1'] = '序號(客供商聯絡人)';
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
				SUBSTR(tExport_time,1,10) tDate,
				cas.cCertifiedId cCertifiedId,
				cas.cCaseStatus cCaseStatus,
				cas.cFinishDate cFinishDate,
				tra.tBankLoansDate cEndDate,
				tra.tBankLoansDate tBankLoansDate,
				cas.cLastEditor
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
				AND tKind="保證費"
			GROUP BY
				tra.tMemo
			ORDER BY
				tra.tBankLoansDate,cas.cCertifiedId
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
				cas.cBankList cEndDate,
				cas.cLastEditor
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
        $list_case = array();
        $contract  = new Contract();
        $sc        = new Scrivener();
        $brand     = new Brand();
        $index     = 2;
        $flowNo    = 1;

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
            $b_index     = 0;
            $o_index     = 0;
            $r_index     = 0;
            $s_index     = 0;
            $a_index     = 0;
            $total_index = 0;
            ##

            //設定電子發票相關參數
            $loadNo  = 'EL0003'; //載具類別號碼
            $loadNo1 = ''; //載具顯碼id
            $loadNo2 = ''; //載具隱碼id

            $kindlyName = '財團法人台灣兒童暨家庭扶助基金會'; //發票捐贈對象
            $kindlyNo   = '8585'; //愛心碼
            ##

            $giver_num        = ""; //客戶供應商代號
            $data_buyer       = $contract->GetBuyer($v['cCertifiedId']);
            $data_owner       = $contract->GetOwner($v['cCertifiedId']);
            $data_invoice     = $contract->GetInvoice($v['cCertifiedId']);
            $data_expenditure = $contract->GetExpenditure($v['cCertifiedId']);
            $data_property    = $contract->GetProperty($v['cCertifiedId']);
            $data_res         = $contract->GetRealstate($v['cCertifiedId']);
            $data_sc          = $contract->GetScrivener($v['cCertifiedId']);
            $data_income      = $contract->GetIncome($v['cCertifiedId']);

            $info_sc = $sc->GetScrivenerInfo($data_sc['cScrivener']);

            $this->ConvertToROCYear($pu_roc_year, $v['cEndDate']);
            $this->ConvertToROCDate($pu_roc_date, $v['cEndDate']);

            $now_roc_date = $v['cEndDate']; ///20150618改為tBankLoansDate
            if ($now_roc_date) {
                $now_roc_date = preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", '', $now_roc_date);
                $tmp          = explode('-', $now_roc_date);
                $now_roc_date = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
                unset($tmp);
            }

            //合約書買方
            if ($data_buyer['cInvoiceMoney'] > 0) {
                $giver_num = $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT); //履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼
                $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $AQ        = '1';
                if (($data_buyer['cCategoryIdentify'] == '1') || (preg_match("/\w{10}/", $data_buyer['cIdentifyId']))) {
                    $invoice_kind = '2';
                } else if (preg_match("/^\d{8}$/", $data_buyer['cIdentifyId'])) {
                    $invoice_kind                = '3';
                    $org_cInvoice                = $data_buyer['cInvoiceMoney'];
                    $data_buyer['cInvoiceMoney'] = round($data_buyer['cInvoiceMoney'] / 1.05);
                    $AQ                          = '0';
                } else {
                    $invoice_kind = '';
                }

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $_num;
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $data_buyer['cIdentifyId'];
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_buyer['cInvoiceMoney'];
                $arrItem[$index]['AK'] = $data_buyer['cInvoiceMoney'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $data_buyer['cIdentifyId'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AV'] = $data_buyer['cName'];

                //電子發票項目 2015-06-25
                if ($invoice_kind == '3') {
                    $arrItem[$index]['CK'] = 'Y';
                }
                //統一編號
                else {
                    $arrItem[$index]['CK'] = 'N';
                }
                //身分證字號

                $loadNo1 = $data_buyer['cIdentifyId'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_buyer['cInvoiceDonate'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }
                // $arrItem[$index]['CK'] = 'N' ;            //列印電子發票(2015-07-31)
                if (($data_buyer['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $data_buyer['cInvoiceDonate'] == '1') { //捐贈不印20160203
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {
                        $data_buyer['cInvoiceMoney'] = $org_cInvoice;
                    }
                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractBuyer', $data_buyer['cId'], $data_buyer['cName'], $data_buyer['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $data_buyer['cInvoiceMoney']);
                }
                ##

                $index++;
            }
            ##

            //發票新合約書買方(2015-06-25)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractBuyer" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind = '2';
                        $AQ           = '1';
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind        = '3';
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $AQ                  = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    //取得對應發票對象身分資料
                    $_arr1 = array();

                    $sql  = 'SELECT * FROM tContractBuyer WHERE cId="' . $va['cTBId'] . '";';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr1 = $stmt->fetch(PDO::FETCH_ASSOC);

                    $loadNo1 = $_arr1['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    unset($_arr1);
                    ##

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }
                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {
                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_B', $va['cId'], $va['cName'], $va['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //其他買方
            $_arr = array();

            $sql  = 'SELECT * FROM tContractOthers WHERE	cCertifiedId="' . $v['cCertifiedId'] . '" AND cIdentity="1"	ORDER BY cId ASC';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_arr_max = count($_arr);
            for ($i = 0; $i < $_arr_max; $i++) {
                if ($_arr[$i]['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                    $AQ        = '1';
                    if (preg_match("/^\w{10}$/", $_arr[$i]['cIdentifyId'])) {
                        $invoice_kind = '2';
                    } else if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {
                        $invoice_kind              = '3';
                        $org_cInvoice              = $_arr[$i]['cInvoiceMoney'];
                        $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);

                        $AQ = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $_arr[$i]['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $_arr[$i]['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $_arr[$i]['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $_arr[$i]['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                    $arrItem[$index]['AV'] = $_arr[$i]['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    $loadNo1 = $_arr[$i]['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($_arr[$i]['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($_arr[$i]['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $_arr[$i]['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05 20160914 [寫入資料庫要用原始金額去用]
                        {
                            // $_arr[$i]['cInvoiceMoney'] = Round($_arr[$i]['cInvoiceMoney']*1.05);

                            $_arr[$i]['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractOthers_B', $_arr[$i]['cId'], $_arr[$i]['cName'], $_arr[$i]['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $_arr[$i]['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);unset($org_cInvoice);
            ##

            //發票新其他買方(2015-06-25)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersB" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind = '2';
                        $AQ           = '1';
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind        = '3';
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        $AQ = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    //取得對應發票對象身分資料
                    $_arr1 = array();

                    $sql  = 'SELECT * FROM tContractOthers WHERE cId="' . $va['cTBId'] . '";';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr1 = $stmt->fetch(PDO::FETCH_ASSOC);

                    $loadNo1 = $_arr1['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    unset($_arr1);
                    ##

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);
                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_B', $va['cId'], $va['cName'], $va['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);unset($org_cInvoice);
            ##

            //合約書賣方
            if ($data_owner['cInvoiceMoney'] > 0) {
                $giver_num = $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $AQ        = '1';
                if (($data_owner['cCategoryIdentify'] == '1') || (preg_match("/\w{10}/", $data_owner['cIdentifyId']))) {
                    $invoice_kind = '2';
                } else {
                    $invoice_kind                = '3';
                    $org_cInvoice                = $data_owner['cInvoiceMoney'];
                    $data_owner['cInvoiceMoney'] = round($data_owner['cInvoiceMoney'] / 1.05);
                    $AQ                          = '0';
                }

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $_num;
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $data_owner['cIdentifyId'];
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_owner['cInvoiceMoney'];
                $arrItem[$index]['AK'] = $data_owner['cInvoiceMoney'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $data_owner['cIdentifyId'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                $arrItem[$index]['AV'] = $data_owner['cName'];

                //電子發票項目 2015-06-25
                if ($invoice_kind == '3') {
                    $arrItem[$index]['CK'] = 'Y';
                }
                //統一編號
                else {
                    $arrItem[$index]['CK'] = 'N';
                }
                //身分證字號

                $loadNo1 = $data_owner['cIdentifyId'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_owner['cInvoiceDonate'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }

                // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                if (($data_owner['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $data_owner['cInvoiceDonate'] == '1') {
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {
                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {

                        // $data_owner['cInvoiceMoney'] = Round($data_owner['cInvoiceMoney']*1.05);
                        $data_owner['cInvoiceMoney'] = $org_cInvoice;
                    }

                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractOwner', $data_owner['cId'], $data_owner['cName'], $data_owner['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $data_owner['cInvoiceMoney']);
                }
                ##

                $index++;
            }
            ##
            unset($org_cInvoice);
            //發票新合約書賣方(2015-06-25)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOwner" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind = '2';
                        $AQ           = '1';
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind        = '3';
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $AQ                  = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    //取得對應發票對象身分資料
                    $_arr1 = array();

                    $sql  = 'SELECT * FROM tContractOwner WHERE cId="' . $va['cTBId'] . '";';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr1 = $stmt->fetch(PDO::FETCH_ASSOC);

                    $loadNo1 = $_arr1['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    unset($_arr1);
                    ##

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {
                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);

                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_O', $va['cId'], $va['cName'], $va['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);unset($org_cInvoice);
            ##

            //其他賣方
            $sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cIdentity="2" ORDER BY cId ASC;';

            unset($_arr);
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_arr_max = count($_arr);
            for ($i = 0; $i < $_arr_max; $i++) {
                if ($_arr[$i]['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    $AQ = '1';
                    if (preg_match("/^\w{10}$/", $_arr[$i]['cIdentifyId'])) {
                        $invoice_kind = '2';
                    } else if (preg_match("/^\d{8}$/", $_arr[$i]['cIdentifyId'])) {
                        $invoice_kind              = '3';
                        $org_cInvoice              = $_arr[$i]['cInvoiceMoney'];
                        $_arr[$i]['cInvoiceMoney'] = round($_arr[$i]['cInvoiceMoney'] / 1.05);
                        $AQ                        = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $_arr[$i]['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $_arr[$i]['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $_arr[$i]['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $_arr[$i]['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                    $arrItem[$index]['AV'] = $_arr[$i]['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    $loadNo1 = $_arr[$i]['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($_arr[$i]['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($_arr[$i]['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $_arr[$i]['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $_arr[$i]['cInvoiceMoney'] = Round($_arr[$i]['cInvoiceMoney']*1.05);
                            $_arr[$i]['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractOthers_O', $_arr[$i]['cId'], $_arr[$i]['cName'], $_arr[$i]['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $_arr[$i]['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            ##
            unset($org_cInvoice);
            //發票新其他賣方(2015-06-25)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersO" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind = '2';
                        $AQ           = '1';
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind        = '3';
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $AQ                  = '0';
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    //取得對應發票對象身分資料
                    $_arr1 = array();

                    $sql  = 'SELECT * FROM tContractOthers WHERE cId="' . $va['cTBId'] . '";';
                    $stmt = $this->dbh->prepare($sql);
                    $stmt->execute();
                    $_arr1 = $stmt->fetch(PDO::FETCH_ASSOC);

                    $loadNo1 = $_arr1['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    unset($_arr1);
                    ##

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $org_cInvoice = $va['cInvoiceMoney'];
                            $va['cInvoiceMoney'] = $org_cInvoice;
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_O', $va['cId'], $va['cName'], $va['cIdentifyId'], $loadNo1, $v['cCertifiedId'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);unset($org_cInvoice);
            ##

            //第一家仲介基本資料
            if (($data_res['cBranchNum'] != '0') && ($data_res['cBranchNum'] != '')) {
                $in_branch = $info_branch = array();

                $in_branch = $brand->GetBranch($data_res['cBranchNum']);
                if (!empty($in_branch[0])) {
                    $info_branch = $in_branch[0];
                }
                unset($in_branch);
            }
            ##

            //第二家仲介基本資料
            if (($data_res['cBranchNum1'] != '0') && ($data_res['cBranchNum1'] != '')) {
                $in_branch1 = $info_branch1 = array();

                $in_branch1 = $brand->GetBranch($data_res['cBranchNum1']);
                if (!empty($in_branch1[0])) {
                    $info_branch1 = $in_branch1[0];
                }
                unset($in_branch1);
            }
            ##

            //第三家仲介基本資料
            if (($data_res['cBranchNum2'] != '0') && ($data_res['cBranchNum2'] != '')) {
                $in_branch2 = $info_branch2 = array();

                $in_branch2 = $brand->GetBranch($data_res['cBranchNum2']);
                if (!empty($in_branch2[0])) {
                    $info_branch2 = $in_branch2[0];
                }
                unset($in_branch2);
            }
            ##

            //第一家仲介
            if ($data_res['cInvoiceMoney'] > 0) {
                $giver_num                 = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $_num                      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $org_cInvoice              = $data_res['cInvoiceMoney'];
                $data_res['cInvoiceMoney'] = round($data_res['cInvoiceMoney'] / 1.05);
                $invoice_kind              = '3';
                $AQ                        = '0'; //0:三聯式 1:二聯式

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $_num;
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $info_branch['bSerialnum'];
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney'];
                $arrItem[$index]['AK'] = $data_res['cInvoiceMoney'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $info_branch['bSerialnum'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                $arrItem[$index]['AV'] = $info_branch['bName'];

                //電子發票項目 2015-06-25
                if ($invoice_kind == '3') {
                    $arrItem[$index]['CK'] = 'Y';
                }
                //統一編號
                else {
                    $arrItem[$index]['CK'] = 'N';
                }
                //身分證字號

                $loadNo1 = $info_branch['bSerialnum'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_res['cInvoiceDonate'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }

                // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                if (($data_res['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $data_res['cInvoiceDonate'] == '1') {
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {
                        // $data_res['cInvoiceMoney'] = Round($data_res['cInvoiceMoney']*1.05);
                        $data_res['cInvoiceMoney'] = $org_cInvoice;
                    }

                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractRealestate_R', $info_branch['bId'], $info_branch['bName'], $info_branch['bSerialnum'], $info_branch['bId'], $info_branch['bPassword'], $data_res['cInvoiceMoney']);
                }
                ##

                $index++;
            }
            ##
            unset($org_cInvoice);
            //發票第一家仲介的新增對象 (2015-06-26)
            $_arr1 = array();

            $sql = '
				SELECT
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
				ASC;
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr1 = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr1 as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) { //統編
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $invoice_kind        = '3';
                        $AQ                  = '0';
                    } else { //身分證字號
                        $invoice_kind = '2';
                        $AQ           = '1';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    $loadNo1 = $va['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);
                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_R', $va['cId'], $va['cName'], $va['cIdentifyId'], $info_branch['bId'], $info_branch['bPassword'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);unset($org_cInvoice);
            ##

            //第二家仲介
            if ($data_res['cInvoiceMoney1'] > 0) {
                $giver_num                  = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $_num                       = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $org_cInvoice               = $data_res['cInvoiceMoney1'];
                $data_res['cInvoiceMoney1'] = round($data_res['cInvoiceMoney1'] / 1.05);
                $invoice_kind               = '3';
                $AQ                         = '0'; //0:三聯式 1:二聯式

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $_num;
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $info_branch1['bSerialnum'];
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney1'];
                $arrItem[$index]['AK'] = $data_res['cInvoiceMoney1'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $info_branch1['bSerialnum'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                $arrItem[$index]['AV'] = $info_branch1['bName'];

                //電子發票項目 2015-06-25
                if ($invoice_kind == '3') {
                    $arrItem[$index]['CK'] = 'Y';
                }
                //統一編號
                else {
                    $arrItem[$index]['CK'] = 'N';
                }
                //身分證字號

                $loadNo1 = $info_branch1['bSerialnum'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_res['cInvoiceDonate1'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }

                // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                if (($data_res['cInvoicePrint1'] == 'N' && $invoice_kind != '3') || $data_res['cInvoiceDonate1'] == '1') {
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {

                        $data_res['cInvoiceMoney1'] = $org_cInvoice;
                        // $data_res['cInvoiceMoney1'] = Round($data_res['cInvoiceMoney1']*1.05);
                    }

                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractRealestate_R1', $info_branch1['bId'], $info_branch1['bName'], $info_branch1['bSerialnum'], $info_branch1['bId'], $info_branch1['bPassword'], $data_res['cInvoiceMoney1']);
                }
                ##

                $index++;
            }
            ##
            unset($org_cInvoice);
            //發票第二家仲介的新增對象 (2015-06-26)
            $_arr1 = array();

            $sql = '
				SELECT
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
				ASC;
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr1 = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr1 as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $_num      = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);

                    if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) { //統編
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);

                        $invoice_kind = '3';
                        $AQ           = '0';
                    } else { //身分證字號
                        $invoice_kind = '2';
                        $AQ           = '1';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $_num;
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    $loadNo1 = $va['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $org_cInvoice = $va['cInvoiceMoney'];
                            $va['cInvoiceMoney'] = $org_cInvoice;
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_R1', $va['cId'], $va['cName'], $va['cIdentifyId'], $info_branch1['bId'], $info_branch1['bPassword'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);unset($org_cInvoice);
            ##

            //第三家仲介
            if ($data_res['cInvoiceMoney2'] > 0) {
                $giver_num                  = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $_num                       = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $org_cInvoice               = $data_res['cInvoiceMoney2'];
                $data_res['cInvoiceMoney2'] = round($data_res['cInvoiceMoney2'] / 1.05);
                $invoice_kind               = '3';
                $AQ                         = '0'; //0:三聯式 1:二聯式

                $arrItem[$index]['A']  = $pu_roc_year;
                $arrItem[$index]['B']  = $_num;
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $info_branch2['bSerialnum'];
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_res['cInvoiceMoney2'];
                $arrItem[$index]['AK'] = $data_res['cInvoiceMoney2'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $info_branch2['bSerialnum'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                $arrItem[$index]['AV'] = $info_branch2['bName'];

                //電子發票項目 2015-06-25
                if ($invoice_kind == '3') {
                    $arrItem[$index]['CK'] = 'Y';
                }
                //統一編號
                else {
                    $arrItem[$index]['CK'] = 'N';
                }
                //身分證字號

                $loadNo1 = $info_branch2['bSerialnum'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_res['cInvoiceDonate2'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }

                // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)

                if (($data_res['cInvoicePrint2'] == 'N' && $invoice_kind != '3') || $data_res['cInvoiceDonate2'] == '1') {
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {
                        $data_res['cInvoiceMoney2'] = $org_cInvoice;
                        // $data_res['cInvoiceMoney2'] = Round($data_res['cInvoiceMoney2']*1.05);
                    }

                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractRealestate_R2', $info_branch2['bId'], $info_branch2['bName'], $info_branch2['bSerialnum'], $info_branch2['bId'], $info_branch2['bPassword'], $data_res['cInvoiceMoney2']);
                }
                ##

                $index++;
            }
            ##
            unset($org_cInvoice);
            //發票第三家仲介的新增對象 (2015-06-26)
            $_arr1 = array();

            $sql = '
				SELECT
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
				ASC;
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr1 = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr1 as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) { //統編
                        $org_cInvoice        = $va['cInvoiceMoney'];
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $invoice_kind        = '3';
                        $AQ                  = '0';
                    } else { //身分證字號
                        $invoice_kind = '2';
                        $AQ           = '1';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $va['cName'];

                    //電子發票項目 2015-06-25
                    if ($invoice_kind == '3') {
                        $arrItem[$index]['CK'] = 'Y';
                    }
                    //統一編號
                    else {
                        $arrItem[$index]['CK'] = 'N';
                    }
                    //身分證字號

                    $loadNo1 = $va['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                    if (($va['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $va['cInvoiceDonate'] == '1') {
                        $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                    } else {
                        $arrItem[$index]['CK'] = 'Y';

                        //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id
                    }

                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {
                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            // $va['cInvoiceMoney'] = Round($va['cInvoiceMoney']*1.05);
                            // $org_cInvoice = $va['cInvoiceMoney'];
                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_R2', $va['cId'], $va['cName'], $va['cIdentifyId'], $info_branch2['bId'], $info_branch2['bPassword'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);unset($org_cInvoice);
            ##

            //合約書代書
            if ($data_invoice['cInvoiceScrivener'] > 0) {
                $giver_num    = $v['cCertifiedId'] . '4' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $_num         = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $invoice_kind = '3';
                $org_cInvoice = $data_invoice['cInvoiceScrivener'];
                $AQ           = '0'; //0:三聯式 1:二聯式

                $arrItem[$index]['A'] = $pu_roc_year;
                $arrItem[$index]['B'] = $_num;
                $arrItem[$index]['C'] = $now_roc_date;
                $arrItem[$index]['D'] = '20';

                $_name = '';
                if ($data_sc['cInvoiceTo'] == '2') {
                    //20180206 法人稅制三聯式
                    $arrItem[$index]['G']              = $info_sc['sSerialnum']; //開發票給事務所(統一編號)
                    $data_invoice['cInvoiceScrivener'] = round($data_invoice['cInvoiceScrivener'] / 1.05); //20150722 又文說金額含稅不用除以1.05
                    // $invoice_kind = '3' ;
                    // $invoice_kind = '2' ;
                    // $AQ = '1' ;
                    $_name                 = $info_sc['sOffice'];
                    $arrItem[$index]['CK'] = 'Y'; //事務所
                } else {
                    $arrItem[$index]['G']  = $info_sc['sIdentifyId']; //開發票給個人(身分證字號)
                    $invoice_kind          = '2';
                    $AQ                    = '1';
                    $_name                 = $info_sc['sName'];
                    $arrItem[$index]['CK'] = 'N'; //個人
                }

                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_invoice['cInvoiceScrivener'];
                $arrItem[$index]['AK'] = $data_invoice['cInvoiceScrivener'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $arrItem[$index]['G'];
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '0';
                $arrItem[$index]['AV'] = $_name;
                unset($_name);

                //電子發票項目 2015-06-25
                //if ($invoice_kind == '3') $arrItem[$index]['CK'] = 'Y' ;        //統一編號
                //else $arrItem[$index]['CK'] = 'N' ;                                //身分證字號

                $loadNo1 = $arrItem[$index]['G'];
                $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                $arrItem[$index]['CI'] = ''; //指定捐贈對象
                $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                if ($data_sc['cInvoiceDonate'] == '1') { //決定捐出發票
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                    $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                    $arrItem[$index]['CH'] = 'Y';
                    $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                    $arrItem[$index]['CO'] = $kindlyNo;

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                }

                if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                    $arrItem[$index]['CH'] = 'N';
                    $arrItem[$index]['CI'] = '';
                    $arrItem[$index]['CO'] = '';

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id

                    //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                }

                // $arrItem[$index]['CK'] = 'Y' ;            //列印電子發票(2015-07-31)
                if (($data_sc['cInvoicePrint'] == 'N' && $invoice_kind != '3') || $data_sc['cInvoiceDonate'] == '1') {
                    $arrItem[$index]['CK'] = 'N'; //列印電子發票(2015-09-01)
                } else {
                    $arrItem[$index]['CK'] = 'Y';

                    //CL載具類別號碼/CM載具顯碼id/CN載具隱碼id都空值哦

                    $arrItem[$index]['CL'] = ''; //載具類別號碼
                    $arrItem[$index]['CM'] = ''; //載具顯碼id
                    $arrItem[$index]['CN'] = ''; //載具隱碼id
                }

                ##

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                    if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                    {
                        //地政士不會出現法人 (20180206 會有法人)
                        $data_invoice['cInvoiceScrivener'] = $org_cInvoice;
                        // $data_invoice['cInvoiceScrivener'] = Round($data_invoice['cInvoiceScrivener']*1.05);

                    }

                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractScrivener', $info_sc['sId'], $_name, $info_sc['sIdentifyId'], 'SC' . str_pad($info_sc['sId'], 4, '0', STR_PAD_LEFT), $info_sc['sPassword'], $data_invoice['cInvoiceScrivener']);
                }
                ##

                $index++;
            }
            ##
            unset($org_cInvoice);unset($_name);
            //發票新代書(2015-06-25)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractScrivener" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $org_cInvoice = $va['cInvoiceMoney'];
                    $giver_num    = $v['cCertifiedId'] . '4' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                    $invoice_kind = '2';
                    $AQ           = '';

                    $_name = '';
                    if (preg_match("/\w{10}/", $va['cIdentifyId'])) {
                        $invoice_kind          = '2';
                        $AQ                    = '1';
                        $_name                 = $info_sc['sName'];
                        $arrItem[$index]['CK'] = 'N'; //個人
                    } else if (preg_match("/^\d{8}$/", $va['cIdentifyId'])) {
                        $invoice_kind = '3';
                        // $invoice_kind = '2';
                        $va['cInvoiceMoney'] = round($va['cInvoiceMoney'] / 1.05);
                        $AQ                  = '0';
                        // $AQ = '1' ;
                        $_name                 = $info_sc['sOffice'];
                        $arrItem[$index]['CK'] = 'Y'; //事務所
                    } else {
                        $invoice_kind = '';
                    }

                    $arrItem[$index]['A']  = $pu_roc_year;
                    $arrItem[$index]['B']  = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                    $arrItem[$index]['C']  = $now_roc_date;
                    $arrItem[$index]['D']  = '20';
                    $arrItem[$index]['G']  = $va['cIdentifyId'];
                    $arrItem[$index]['I']  = '00';
                    $arrItem[$index]['J']  = '00';
                    $arrItem[$index]['K']  = $now_roc_date;
                    $arrItem[$index]['M']  = '1';
                    $arrItem[$index]['Q']  = $invoice_kind;
                    $arrItem[$index]['AF'] = '0001';
                    $arrItem[$index]['AG'] = 'A001';
                    $arrItem[$index]['AH'] = '0';
                    $arrItem[$index]['AI'] = '1';
                    $arrItem[$index]['AJ'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AK'] = $va['cInvoiceMoney'];
                    $arrItem[$index]['AQ'] = $AQ;
                    $arrItem[$index]['AR'] = $va['cIdentifyId'];
                    $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '2'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                    $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                    $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '2';
                    $arrItem[$index]['AV'] = $_name;
                    unset($_name);

                    //電子發票項目 2015-06-25
                    //if ($invoice_kind == '3') $arrItem[$index]['CK'] = 'Y' ;    //統一編號
                    //else $arrItem[$index]['CK'] = 'N' ;                            //身分證字號

                    $loadNo1 = $va['cIdentifyId'];
                    $loadNo2 = $loadNo1; //依據關網林先生 2015-06-30 的說法、顯碼與隱碼應該一致

                    $arrItem[$index]['CJ'] = 'Y'; //電子發票註記 (Y)

                    $arrItem[$index]['CH'] = 'N'; //是否捐贈發票
                    $arrItem[$index]['CI'] = ''; //指定捐贈對象
                    $arrItem[$index]['CO'] = ''; //捐贈對象愛心碼

                    $arrItem[$index]['CL'] = $loadNo; //載具類別號碼
                    $arrItem[$index]['CM'] = $loadNo1; //載具顯碼id
                    $arrItem[$index]['CN'] = $loadNo2; //載具隱碼id

                    if ($va['cInvoiceDonate'] == '1') { //決定捐出發票
                        $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AU'] = '出貨單號:' . $pu_roc_date . $giver_num . '1';
                        $arrItem[$index]['AV'] = $kindlyName; //20150825捐贈名稱要帶財團法人創世社會福利基金會

                        $arrItem[$index]['CH'] = 'Y';
                        $arrItem[$index]['CI'] = $kindlyNo; //20150825改為用愛心碼
                        $arrItem[$index]['CO'] = $kindlyNo;

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'N' ;        //因發票捐贈，所以不需列印發票
                    }

                    if ($invoice_kind == '3') { //統編不帶類別代碼、顯碼、隱碼
                        $arrItem[$index]['CH'] = 'N';
                        $arrItem[$index]['CI'] = '';
                        $arrItem[$index]['CO'] = '';

                        $arrItem[$index]['CL'] = ''; //載具類別號碼
                        $arrItem[$index]['CM'] = ''; //載具顯碼id
                        $arrItem[$index]['CN'] = ''; //載具隱碼id

                        //$arrItem[$index]['CK'] = 'Y' ;        //公司行號需列印發票
                    }

                    $arrItem[$index]['CK'] = 'Y'; //列印電子發票(2015-07-31)
                    ##

                    //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                    if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {

                        if ($arrItem[$index]['AQ'] == 0) //法人(不含事務所)要*1.05
                        {
                            $va['cInvoiceMoney'] = $org_cInvoice;
                        }

                        $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', 'tContractInvoiceExt_S', $va['cId'], $va['cName'], $va['cIdentifyId'], 'SC' . str_pad($info_sc['sId'], 4, '0', STR_PAD_LEFT), $info_sc['sPassword'], $va['cInvoiceMoney']);
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##
            $this->setInvoiceClose($v['cCertifiedId']);
            //結案錯誤紀錄表-最後修改者
            // die($v['cLastEditor']."_".$v['cCertifiedId']);
            $this->UpdateLastEditor($v['cLastEditor'], $v['cCertifiedId']);
            //合約書其他(創世基金會)
            if ($data_invoice['cInvoiceOther'] > 0) {
                $giver_num    = $v['cCertifiedId'] . '501';
                $_num         = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                $invoice_kind = '2';
                $AQ           = '1';

                $arrItem[$index]['A'] = $pu_roc_year;
                //$arrItem[$index]['B'] = $pu_roc_date.  substr($giver_num, -6);
                $arrItem[$index]['B']  = $pu_roc_date . str_pad($flowNo++, 6, '0', STR_PAD_LEFT);
                $arrItem[$index]['C']  = $now_roc_date;
                $arrItem[$index]['D']  = '20';
                $arrItem[$index]['G']  = $_num;
                $arrItem[$index]['I']  = '00';
                $arrItem[$index]['J']  = '00';
                $arrItem[$index]['K']  = $now_roc_date;
                $arrItem[$index]['M']  = '1';
                $arrItem[$index]['Q']  = $invoice_kind;
                $arrItem[$index]['AF'] = '0001';
                $arrItem[$index]['AG'] = 'A001';
                $arrItem[$index]['AH'] = '0';
                $arrItem[$index]['AI'] = '1';
                $arrItem[$index]['AJ'] = $data_invoice['cInvoiceOther'];
                $arrItem[$index]['AK'] = $data_invoice['cInvoiceOther'];
                $arrItem[$index]['AQ'] = $AQ;
                $arrItem[$index]['AR'] = $_num;
                $arrItem[$index]['AS'] = $pu_roc_date . $giver_num . '0'; //年月日7碼+履保編號9碼+對象1碼(買1賣2仲介3地政士4)+人數2碼+其他/捐贈1碼(不捐贈0捐贈1其他2)、預設不捐贈0
                $arrItem[$index]['AT'] = '保證號碼:' . $v['cCertifiedId'];
                $arrItem[$index]['AU'] = $pu_roc_date . $giver_num . '0';

                //檢查送貨單資料是否存在?若是、則刪除之後再新增；若否、則直接新增(2015-04-07)
                if ($this->checkInvoice($v['cCertifiedId'], $arrItem[$index]['AS'])) {
                    $this->AddInvoiceNo($v['cCertifiedId'], $_num, $arrItem[$index]['AS'], '', '', '', '創世基金會', '', '', '', $data_invoice['cInvoiceOther'], 'N');
                }
                ##

                $index++;
            }
            ##
        }

        $this->mArrField = $arrItem;
    }
    private function UpdateLastEditor($pId, $cId)
    {
        if ($cId && $pId) {
            $sql = "UPDATE tContractCase SET cCaseEndLastEditor = '" . $pId . "'  WHERE cCertifiedId = '" . $cId . "'";
            // echo $sql;
            // die;
            $rs = $this->dbh->prepare($sql);
            $rs->execute();
        }

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
    //更改狀態
    private function setInvoiceClose($cid)
    {
        $sql = "UPDATE tContractCase SET cInvoiceClose = 'Y' WHERE cCertifiedId = '" . $cid . "'";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

    }

    ##
    //將發票號碼寫入資料庫內(2015-04-07)
    private function AddInvoiceNo($cId, $dNo, $dfNo, $iNo = '', $tb = '', $tg = '', $cName = '', $cIdentifyId = '', $acc = '', $pass = '', $cMoney = 0, $yn = 'Y')
    {
        if ($cId && $dNo && $dfNo) {
            $bCode = '';

            if (preg_match("/\_R/", $tb)) {
                $sql  = 'SELECT b.bCode FROM tBranch AS a JOIN tBrand AS b ON a.bBrand=b.bId WHERE a.bId="' . $acc . '";';
                $stmt = $this->dbh->prepare($sql);
                $stmt->execute();
                $rs    = $stmt->fetch(PDO::FETCH_ASSOC);
                $bCode = $rs['bCode'];

                unset($rs);

                $acc = $bCode . str_pad($acc, 5, '0', STR_PAD_LEFT);
            }

            $sql = '
				INSERT INTO
					tContractInvoiceQuery
				SET
					cCertifiedId="' . $cId . '",
					cDeliveryNo="' . $dNo . '",
					cInvoiceNo="' . $iNo . '",
					cInvoiceDate="",
					cMoney="' . $cMoney . '",
					cDefineFields="' . $dfNo . '",
					cTB="' . $tb . '",
					cTargetId="' . $tg . '",
					cName="' . $cName . '",
					cIdentifyId="' . $cIdentifyId . '",
					cAcc="' . $acc . '",
					cPass="' . $pass . '",
					cQuery="' . $yn . '"
			;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            unset($rs);

            return true;
        } else {
            return false;
        }

    }
    ##

    //檢查發票資料是否已存在?若存在則將記錄刪除(2015-04-07)
    private function checkInvoice($cId, $dfNo)
    {
        if ($cId && $dfNo) {
            $arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceQuery WHERE cCertifiedId="' . $cId . '" AND cDefineFields="' . $dfNo . '";';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            if (count($arr) > 0) {
                $sql  = 'DELETE FROM tContractInvoiceQuery WHERE cCertifiedId="' . $cId . '" AND cDefineFields="' . $dfNo . '";';
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
