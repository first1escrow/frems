<?php
require_once __DIR__ . '/interface_excel.class.php';
require_once __DIR__ . '/contract.class.php';
require_once __DIR__ . '/brand.class.php';
require_once __DIR__ . '/scrivener.class.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class ExcelCs extends ExcelBase
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
        $this->mArrBg['X1']  = $style;
        $this->mArrBg['AA1'] = $style;
        $this->mArrBg['AK1'] = $style;
        $this->mArrBg['BB1'] = $style;
        $this->mArrBg['BC1'] = $style;
        $this->mArrBg['BF1'] = $style;

        $this->mArrTitle['A1'] = '客戶供應商代號';
        $this->mArrTitle['B1'] = '客戶供應商類別';
        $this->mArrTitle['C1'] = '客戶供應商簡稱';
        $this->mArrTitle['D1'] = '客戶供應商全稱';
        $this->mArrTitle['E1'] = '行業別';
        $this->mArrTitle['F1'] = '類別科目代號';
        $this->mArrTitle['G1'] = '統一編號';
        $this->mArrTitle['H1'] = '稅籍編號';
        $this->mArrTitle['I1'] = '郵遞區號';
        $this->mArrTitle['J1'] = '發票地址';
        $this->mArrTitle['K1'] = '聯絡地址';
        $this->mArrTitle['L1'] = '送貨地址';
        $this->mArrTitle['M1'] = '電話(發票地址)';
        $this->mArrTitle['N1'] = '電話(公司地址)';
        $this->mArrTitle['O1'] = '電話(送貨地址)';
        $this->mArrTitle['P1'] = '傳真';
        $this->mArrTitle['Q1'] = '數據機種類';
        $this->mArrTitle['R1'] = '傳呼機號碼';
        $this->mArrTitle['S1'] = '行動電話';
        $this->mArrTitle['T1'] = '網址';
        $this->mArrTitle['U1'] = '負責人';
        $this->mArrTitle['V1'] = '聯絡人';
        $this->mArrTitle['W1'] = '備註(30C)';
        $this->mArrTitle['X1'] = '銷售折數';
        $this->mArrTitle['Y1'] = '等級';
        $this->mArrTitle['Z1'] = '區域';

        $this->mArrTitle['AA1'] = '進貨折數';
        $this->mArrTitle['AB1'] = '部門\工地編號';
        $this->mArrTitle['AC1'] = '業務員代號';
        $this->mArrTitle['AD1'] = '服務人員';
        $this->mArrTitle['AE1'] = '建立日期';
        $this->mArrTitle['AF1'] = '最近交易日';
        $this->mArrTitle['AG1'] = '信用額度';
        $this->mArrTitle['AH1'] = '保證額度';
        $this->mArrTitle['AI1'] = '抵押額度';
        $this->mArrTitle['AJ1'] = '已用額度';
        $this->mArrTitle['AK1'] = '開立發票方式';
        $this->mArrTitle['AL1'] = '收款方式';
        $this->mArrTitle['AM1'] = '匯款銀行代號';
        $this->mArrTitle['AN1'] = '匯款帳號';
        $this->mArrTitle['AO1'] = '結帳方式';
        $this->mArrTitle['AP1'] = '銷貨後幾個月結帳';
        $this->mArrTitle['AQ1'] = '銷貨後逢幾日結帳';
        $this->mArrTitle['AR1'] = '結帳後幾個月收款';
        $this->mArrTitle['AS1'] = '結帳後逢幾日收款';
        $this->mArrTitle['AT1'] = '收款後幾個月兌現';
        $this->mArrTitle['AU1'] = '收款後逢幾日兌現';
        $this->mArrTitle['AV1'] = '進貨後幾個月結帳';
        $this->mArrTitle['AW1'] = '進貨後逢幾日結帳';
        $this->mArrTitle['AX1'] = '結帳後幾個月付款';
        $this->mArrTitle['AY1'] = '結帳後逢幾日付款';
        $this->mArrTitle['AZ1'] = '付款後幾個月兌現';

        $this->mArrTitle['BA1'] = '付款後逢幾日兌現';
        $this->mArrTitle['BB1'] = '郵遞區號(聯絡地址)';
        $this->mArrTitle['BC1'] = '郵遞區號(送貨地址)';
        $this->mArrTitle['BD1'] = '職稱';
        $this->mArrTitle['BE1'] = '專案\項目編號';
        $this->mArrTitle['BF1'] = '請款客戶';

        $this->mArrTitle['BG1'] = 'EAMIL ADDRS';
        $this->mArrTitle['BH1'] = '收款/付款方式(描述)';
        $this->mArrTitle['BI1'] = '交貨/收貨方式';
        $this->mArrTitle['BJ1'] = '進出口交易方式';
        $this->mArrTitle['BK1'] = '交易幣別';
        $this->mArrTitle['BL1'] = '英文負責人';
        $this->mArrTitle['BM1'] = '英文聯絡人';
        $this->mArrTitle['BN1'] = '電子發票通知方式';
        $this->mArrTitle['BO1'] = '發票預設捐贈';
        $this->mArrTitle['BP1'] = '預設發票捐贈對象';
        $this->mArrTitle['BQ1'] = '自定義欄位一';
        $this->mArrTitle['BR1'] = '自定義欄位二';
        $this->mArrTitle['BS1'] = '自定義欄位三';
        $this->mArrTitle['BT1'] = '自定義欄位四';
        $this->mArrTitle['BU1'] = '自定義欄位五';
        $this->mArrTitle['BV1'] = '自定義欄位六';
        $this->mArrTitle['BW1'] = '自定義欄位七';
        $this->mArrTitle['BX1'] = '自定義欄位八';
        $this->mArrTitle['BY1'] = '自定義欄位九';
        $this->mArrTitle['BZ1'] = '自定義欄位十';
        $this->mArrTitle['CA1'] = '自定義欄位十一';
        $this->mArrTitle['CB1'] = '自定義欄位十二';
        $this->mArrTitle['CC1'] = '會員卡號';
        $this->mArrTitle['CD1'] = '客供商英文名稱';
        $this->mArrTitle['CE1'] = '客戶英文地址';
        $this->mArrTitle['CF1'] = '銷貨結帳終止日';
        $this->mArrTitle['CG1'] = '進貨結帳終止日';
        $this->mArrTitle['CH1'] = '銷貨收款週期選項';
        $this->mArrTitle['CI1'] = '進貨付款週期選項';
        $this->mArrTitle['CJ1'] = '進貨收付條件';
        $this->mArrTitle['CK1'] = '客供商英文聯絡電話';
        $this->mArrTitle['CL1'] = '匯款戶名';
        $this->mArrTitle['CM1'] = '使用電子發票';
        $this->mArrTitle['CN1'] = '單價含稅否';
        $this->mArrTitle['CO1'] = '批次結帳';
        $this->mArrTitle['CP1'] = '發票服務平台登入密碼';
        $this->mArrTitle['CQ1'] = '聯絡地址經度';
        $this->mArrTitle['CR1'] = '聯絡地址緯度';
    }

    //取得合約銀行資訊
    private function ConBank()
    {
        $sql  = 'SELECT cBankAccount FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;';
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
        $sql  = 'SELECT cBankCode FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;';
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
				SUBSTR(tExport_time,1,10) tDate,
				tMemo cCertifiedId,
				tVR_Code vr_code,
				tMoney,
				tBankLoansDate cEndDate
			FROM
				tBankTrans AS tra
			JOIN
				tContractCase AS cas ON tra.tMemo = cas.cCertifiedId
			WHERE
				tra.tExport="1"
				AND tra.tPayOk="1"
				AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
				AND tra.tBankLoansDate>="' . $fds . '"
				AND tra.tBankLoansDate<="' . $fde . '"
				AND tra.tAccount IN ("' . $this->ConBank() . '")
				AND tKind="保證費"
			ORDER BY
				tra.tBankLoansDate,cas.cCertifiedId
			ASC ;
		';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $_data = $stmt->fetchALL(PDO::FETCH_ASSOC);

        //無履保費出款但有出利息
        $_data1 = array();
        $sql    = '
			SELECT
				cas.cBankList as tDate,
				cas.cCertifiedId as cCertifiedId,
				cas.cEscrowBankAccount as vr_code,
				cas.cBankList cEndDate
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

        $max    = count($_data);
        $k      = 0;
        $l      = 0;
        $detail = array();
        for ($i = 0; $i < $max; $i++) {
            $tmp    = explode('-', $_data[$i]['tDate']);
            $_tDate = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
            unset($tmp);
            $_tMoney = str_pad($_data[$i]['tMoney'], 13, '0', STR_PAD_LEFT) . '00';

            $sql = '
				SELECT
					*
				FROM
					tExpense
				WHERE
					eTradeCode="178Y"
					AND eExportCode="8888888"
					AND eDepAccount="00' . $_data[$i]['vr_code'] . '"
					AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
					AND eTradeDate="' . $_tDate . '"
					AND eLender="' . $_tMoney . '"
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_exp = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_exp_max = count($_exp);
            $fg       = 0;
            for ($j = 0; $j < $_exp_max; $j++) {
                @$arr[$_data[$i]['cCertifiedId']]++;
                if ($arr[$_data[$i]['cCertifiedId']] > 1) {
                    $detail[$k] = $_data[$i];
                    $k++;
                }
                $fg++;
            }
            if (!$fg) {
                $detail[$k] = $_data[$i];
                $k++;
            }
        }
        return $detail;
    }

    private function GetCaseData($cId)
    {
        $cId = implode('","', $cId);

        $_data = array();
        $sql   = '
			SELECT
				SUBSTR(tExport_time,1,10) tDate,
				tMemo cCertifiedId,
				tVR_Code vr_code,
				tMoney,
				tBankLoansDate cEndDate
			FROM
				tBankTrans AS tra
			JOIN
				tContractCase AS cas ON tra.tMemo = cas.cCertifiedId
			WHERE
				tra.tExport="1"
				AND tra.tPayOk="1"
				AND tra.tExport_nu NOT LIKE "aaaaaaaaaaaa_"
				AND tra.tMemo IN ("' . $cId . '")
				AND tra.tAccount IN ("' . $this->ConBank() . '")
				AND tKind="保證費"
			ORDER BY
				tra.tBankLoansDate,cas.cCertifiedId
			ASC ;
		';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $_data = $stmt->fetchALL(PDO::FETCH_ASSOC);

        //無履保費出款但有出利息
        $_data1 = array();
        $sql    = '
			SELECT
				cas.cBankList as tDate,
				cas.cCertifiedId as cCertifiedId,
				cas.cEscrowBankAccount as vr_code,
				cas.cBankList cEndDate
			FROM
				tContractCase AS cas
			WHERE
				cas.cCertifiedId IN ("' . $cId . '")
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

        $max    = count($_data);
        $k      = 0;
        $l      = 0;
        $detail = array();
        for ($i = 0; $i < $max; $i++) {
            $tmp    = explode('-', $_data[$i]['tDate']);
            $_tDate = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
            unset($tmp);
            $_tMoney = str_pad($_data[$i]['tMoney'], 13, '0', STR_PAD_LEFT) . '00';

            $sql = '
				SELECT
					*
				FROM
					tExpense
				WHERE
					eTradeCode="178Y"
					AND eExportCode="8888888"
					AND eDepAccount="00' . $_data[$i]['vr_code'] . '"
					AND (ePayTitle LIKE "%退款回存%" OR ePayTitle LIKE "退匯存入")
					AND eTradeDate="' . $_tDate . '"
					AND eLender="' . $_tMoney . '"
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_exp = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_exp_max = count($_exp);
            $fg       = 0;
            for ($j = 0; $j < $_exp_max; $j++) {
                @$arr[$_data[$i]['cCertifiedId']]++;
                if ($arr[$_data[$i]['cCertifiedId']] > 1) {
                    $detail[$k] = $_data[$i];
                    $k++;
                }
                $fg++;
            }
            if (!$fg) {
                $detail[$k] = $_data[$i];
                $k++;
            }
        }
        return $detail;
    }

    //取得、轉換郵遞區號為完整地址
    private function ConvertZipToAddr($zip, $addr)
    {
        $sql = 'SELECT * FROM tZipArea WHERE zZip="' . $zip . '" ;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $zip_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);
        if ($zip_arr) {
            $_city = $zip_arr[0]['zCity'];
            $_area = $zip_arr[0]['zArea'];
            $addr  = preg_replace("/$_city/", '', $addr);
            $addr  = preg_replace("/$_area/", '', $addr);

            $zip_arr[0]['addr'] = $addr;
            if ($_city == $_area) {
                $addr = $_city . $addr;
            } else {
                $addr = $_city . $_area . $addr;
            }
        }
        return $addr;
    }
    ##

    //濾除新竹郵遞區號問題
    private function zipfilter($zip)
    {
        $zip = preg_replace("/[A-Za-z]/", '', $zip);
        return $zip;
    }
    ##

    //數字轉文字
    private function no2ascii($no = 0)
    {
        if ($no >= 10) {
            $no += '55';
            $no = chr($no);
        }
        return $no;
    }
    ##

    public function GenerateField($certifiedId = null)
    {
        $contract = new Contract();
        $sc       = new Scrivener();
        $brand    = new Brand();

        $index = 2;
        if ($certifiedId) {
            $arr = $this->GetCaseData($certifiedId);
        } else {
            $arr = $this->GetCaseList();
        }

        $j = 0;

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
            $info_sc          = $sc->GetScrivenerInfo($data_sc['cScrivener']);
            $address          = $this->getAddress($v['cCertifiedId']);

            $roc_date = $v['cEndDate']; ///20150618改為tBankLoansDate
            if ($roc_date) {
                $roc_date = preg_replace("/ [0-9]{2}:[0-9]{2}:[0-9]{2}$/", '', $roc_date);
                $tmp      = explode('-', $roc_date);
                $roc_date = ($tmp[0] - 1911) . $tmp[1] . $tmp[2];
                unset($tmp);
            }

            //合約書買方
            if ($data_buyer['cInvoiceMoney'] > 0) {
                $giver_num = $data_buyer['cIdentifyId']; //客供商代號(統編或身分證字號)
                $_num      = $roc_date . $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                if (($data_buyer['cCategoryIdentify'] == '1') || (preg_match("/^\w{10}$/", $giver_num))) { //自然人
                    $invoice_kind = '2';
                    $serialnum    = '';
                } else if (($data_buyer['cCategoryIdentify'] == '2') || (preg_match("/^\d{8}$/", $giver_num))) { //法人
                    $invoice_kind = '3';
                    $serialnum    = $giver_num;
                } else {
                    $invoice_kind = '';
                    $serialnum    = '';
                }

                $data_buyer['cBaseAddr'] = $this->ConvertZipToAddr($data_buyer['cBaseZip'], $data_buyer['cBaseAddr']);
                $data_buyer['cBaseZip']  = $this->zipfilter($data_buyer['cBaseZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($data_buyer['cName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $data_buyer['cName'];
                $arrItem[$index]['G']  = $serialnum;
                $arrItem[$index]['I']  = $data_buyer['cBaseZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $data_buyer['cBaseAddr'];
                $arrItem[$index]['M']  = $data_buyer['cTelArea1'] . '-' . $data_buyer['cTelMain1'];
                $arrItem[$index]['N']  = $data_buyer['cTelArea1'] . '-' . $data_buyer['cTelMain1'];
                $arrItem[$index]['O']  = $data_buyer['cTelArea1'] . '-' . $data_buyer['cTelMain1'];
                $arrItem[$index]['S']  = $data_buyer['cMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $data_buyer['cBaseZip'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1'; //使用電子發票
                $arrItem[$index]['CN'] = '1'; //自然人身分，為 1

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $data_buyer['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    $arrItem[$index]['L']  = $data_buyer['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                } else {
                    $arrItem[$index]['BQ'] = $data_buyer['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $data_buyer['cName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }
                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_buyer['cInvoiceDonate'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票新合約書買方(2015-06-30)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractBuyer" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2'; //無捐贈是指定
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-30)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }

                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //其他買方
            $_arr = array();

            $sql  = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cIdentity="1"	ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_arr_max = count($_arr);
            for ($i = 0; $i < $_arr_max; $i++) {
                if ($_arr[$i]['cInvoiceMoney'] > 0) {
                    $giver_num = $_arr[$i]['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num) or preg_match("/^\w{7}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $_arr[$i]['cBaseAddr'] = $this->ConvertZipToAddr($_arr[$i]['cBaseZip'], $_arr[$i]['cBaseAddr']);
                    $_arr[$i]['cBaseZip']  = $this->zipfilter($_arr[$i]['cBaseZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($_arr[$i]['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $_arr[$i]['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $_arr[$i]['cBaseZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $_arr[$i]['cBaseAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = $_arr[$i]['cMobileNum'];
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $_arr[$i]['cBaseZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '0';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $_arr[$i]['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $_arr[$i]['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $_arr[$i]['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $_arr[$i]['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($_arr[$i]['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //發票新其他買方(2015-06-30)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersB" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '1' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-30)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }

                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //合約書賣方
            if ($data_owner['cInvoiceMoney'] > 0) {
                $giver_num = $data_owner['cIdentifyId']; //客供商代號(統編或身分證字號)
                $_num      = $roc_date . $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                if (($data_owner['cCategoryIdentify'] == '1') || (preg_match("/^\w{10}$/", $giver_num))) {
                    $invoice_kind = '2';
                    $serialnum    = '';
                } else if (($data_owner['cCategoryIdentify'] == '2') || (preg_match("/^\d{8}$/", $giver_num))) {
                    $invoice_kind = '3';
                    $serialnum    = $giver_num;
                } else {
                    $invoice_kind = '';
                    $serialnum    = '';
                }

                $data_owner['cBaseAddr'] = $this->ConvertZipToAddr($data_owner['cBaseZip'], $data_owner['cBaseAddr']);
                $data_owner['cBaseZip']  = $this->zipfilter($data_owner['cBaseZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($data_owner['cName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $data_owner['cName'];
                $arrItem[$index]['G']  = $serialnum;
                $arrItem[$index]['I']  = $data_owner['cBaseZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $data_owner['cBaseAddr'];
                $arrItem[$index]['M']  = $data_owner['cTelArea1'] . '-' . $data_owner['cTelMain1'];
                $arrItem[$index]['N']  = $data_owner['cTelArea1'] . '-' . $data_owner['cTelMain1'];
                $arrItem[$index]['O']  = $data_owner['cTelArea1'] . '-' . $data_owner['cTelMain1'];
                $arrItem[$index]['S']  = $data_owner['cMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $data_owner['cBaseZip'];
                $arrItem[$index]['BF'] = $giver_num;

                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $data_owner['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $data_owner['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                } else {
                    $arrItem[$index]['BQ'] = $data_owner['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $data_owner['cName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }
                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_owner['cInvoiceDonate'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票新合約書賣方(2015-06-30)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOwner" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-30)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //其他賣方
            $_arr = array();

            $sql  = 'SELECT * FROM tContractOthers WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cIdentity="2"	ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            $_arr_max = count($_arr);
            for ($i = 0; $i < $_arr_max; $i++) {
                if ($_arr[$i]['cInvoiceMoney'] > 0) {
                    $giver_num = $_arr[$i]['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num) or preg_match("/^\w{7}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $_arr[$i]['cBaseAddr'] = $this->ConvertZipToAddr($_arr[$i]['cBaseZip'], $_arr[$i]['cBaseAddr']);
                    $_arr[$i]['cBaseZip']  = $this->zipfilter($_arr[$i]['cBaseZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($_arr[$i]['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $_arr[$i]['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $_arr[$i]['cBaseZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $_arr[$i]['cBaseAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = $_arr[$i]['cMobileNum'];
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $_arr[$i]['cBaseZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '0';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $_arr[$i]['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $_arr[$i]['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $_arr[$i]['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $_arr[$i]['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($_arr[$i]['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //發票新其他賣方(2015-06-30)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractOthersO" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '2' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-30)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }

                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //第一家仲介
            if ($data_res['cInvoiceMoney'] > 0) {
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

                $giver_num    = $info_branch['bSerialnum']; //客供商代號(統編或身分證字號)
                $_num         = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $invoice_kind = '3';

                $info_branch['bAddress'] = $this->ConvertZipToAddr($info_branch['bZip'], $info_branch['bAddress']);
                $info_branch['bZip']     = $this->zipfilter($info_branch['bZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($info_branch['bName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $info_branch['bName'];
                $arrItem[$index]['G']  = $info_branch['bSerialnum'] . ' ';
                $arrItem[$index]['I']  = $info_branch['bZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $info_branch['bAddress'];
                $arrItem[$index]['M']  = $info_branch['bTelArea'] . '-' . $info_branch['bTelMain'];
                $arrItem[$index]['N']  = $info_branch['bTelArea'] . '-' . $info_branch['bTelMain'];
                $arrItem[$index]['O']  = $info_branch['bTelArea'] . '-' . $info_branch['bTelMain'];
                $arrItem[$index]['S']  = $info_branch['bMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $info_branch['bZip'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $info_branch['bName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch['bName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                } else {
                    $arrItem[$index]['BQ'] = $info_branch['bName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch['bName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }

                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_res['cInvoiceDonate'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票第一家仲介的新增對象 (2015-06-30)
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
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);
            ##

            //第二家仲介
            if ($data_res['cInvoiceMoney1'] > 0) {
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

                $giver_num    = $info_branch1['bSerialnum']; //客供商代號(統編或身分證字號)
                $_num         = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $invoice_kind = '3';

                $info_branch1['bAddress'] = $this->ConvertZipToAddr($info_branch1['bZip'], $info_branch1['bAddress']);
                $info_branch1['bZip']     = $this->zipfilter($info_branch1['bZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($info_branch1['bName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $info_branch1['bName'];
                $arrItem[$index]['G']  = $info_branch1['bSerialnum'] . ' ';
                $arrItem[$index]['I']  = $info_branch1['bZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $info_branch1['bAddress'];
                $arrItem[$index]['M']  = $info_branch1['bTelArea'] . '-' . $info_branch1['bTelMain'];
                $arrItem[$index]['N']  = $info_branch1['bTelArea'] . '-' . $info_branch1['bTelMain'];
                $arrItem[$index]['O']  = $info_branch1['bTelArea'] . '-' . $info_branch1['bTelMain'];
                $arrItem[$index]['S']  = $info_branch1['bMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $info_branch1['bZip'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $info_branch1['bName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch1['bName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                } else {
                    $arrItem[$index]['BQ'] = $info_branch1['bName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch1['bName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }

                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_res['cInvoiceDonate1'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票第二家仲介的新增對象 (2015-06-30)
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
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);
            ##

            //第三家仲介
            if ($data_res['cInvoiceMoney2'] > 0) {
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

                $giver_num    = $info_branch2['bSerialnum']; //客供商代號(統編或身分證字號)
                $_num         = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $invoice_kind = '3';

                $info_branch2['bAddress'] = $this->ConvertZipToAddr($info_branch2['bZip'], $info_branch2['bAddress']);
                $info_branch2['bZip']     = $this->zipfilter($info_branch2['bZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($info_branch2['bName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $info_branch2['bName'];
                $arrItem[$index]['G']  = $info_branch2['bSerialnum'] . ' ';
                $arrItem[$index]['I']  = $info_branch2['bZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $info_branch2['bAddress'];
                $arrItem[$index]['M']  = $info_branch2['bTelArea'] . '-' . $info_branch2['bTelMain'];
                $arrItem[$index]['N']  = $info_branch2['bTelArea'] . '-' . $info_branch2['bTelMain'];
                $arrItem[$index]['O']  = $info_branch2['bTelArea'] . '-' . $info_branch2['bTelMain'];
                $arrItem[$index]['S']  = $info_branch2['bMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $info_branch2['bZip'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $info_branch2['bName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch2['bName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                } else {
                    $arrItem[$index]['BQ'] = $info_branch2['bName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch2['bName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }
                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_res['cInvoiceDonate2'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票第三家仲介的新增對象 (2015-06-30)
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
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }

                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);
            ##

            //第四家仲介
            if ($data_res['cInvoiceMoney3'] > 0) {
                //第四家仲介基本資料
                if (($data_res['cBranchNum3'] != '0') && ($data_res['cBranchNum3'] != '')) {
                    $in_branch3 = $info_branch3 = array();

                    $in_branch3 = $brand->GetBranch($data_res['cBranchNum3']);
                    if (!empty($in_branch3[0])) {
                        $info_branch3 = $in_branch3[0];
                    }
                    unset($in_branch3);
                }
                ##

                $giver_num    = $info_branch3['bSerialnum']; //客供商代號(統編或身分證字號)
                $_num         = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);
                $invoice_kind = '3';

                $info_branch3['bAddress'] = $this->ConvertZipToAddr($info_branch3['bZip'], $info_branch3['bAddress']);
                $info_branch3['bZip']     = $this->zipfilter($info_branch3['bZip']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($info_branch3['bName'], 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $info_branch3['bName'];
                $arrItem[$index]['G']  = $info_branch3['bSerialnum'] . ' ';
                $arrItem[$index]['I']  = $info_branch3['bZip'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $info_branch3['bAddress'];
                $arrItem[$index]['M']  = $info_branch3['bTelArea'] . '-' . $info_branch3['bTelMain'];
                $arrItem[$index]['N']  = $info_branch3['bTelArea'] . '-' . $info_branch3['bTelMain'];
                $arrItem[$index]['O']  = $info_branch3['bTelArea'] . '-' . $info_branch3['bTelMain'];
                $arrItem[$index]['S']  = $info_branch3['bMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = $info_branch3['bZip'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $info_branch3['bName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch3['bName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                } else {
                    $arrItem[$index]['BQ'] = $info_branch3['bName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $info_branch3['bName'] . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }
                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_res['cInvoiceDonate1'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票第四家仲介的新增對象
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
					AND a.cDBName="tContractRealestate3"
				ORDER BY
					a.cId
				ASC;
			';

            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr1 = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr1 as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '3' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = '';
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = $invoice_kind;
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-29)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['CN'] = '0'; //若是法人身分，則改為未稅 0
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';
                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }
                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }

            unset($_arr1);
            ##

            //合約書代書
            if ($data_invoice['cInvoiceScrivener'] > 0 and $data_sc['cInvoiceMoney'] > 0) {
                $_name = '';
                if ($data_sc['cInvoiceTo'] == '2') {
                    $invoice_kind = '3';
                    $giver_num    = $info_sc['sSerialnum']; //客供商代號(統編)
                    $serialnum    = $giver_num;
                    $_name        = $info_sc['sOffice'];
                } else {
                    $invoice_kind = '2';
                    $giver_num    = $info_sc['sIdentifyId']; //客供商代號(身分證字號)
                    $serialnum    = '';
                    $_name        = $info_sc['sName'];
                }
                $_num = $roc_date . $v['cCertifiedId'] . '4' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                $info_sc['sAddress'] = $this->ConvertZipToAddr($info_sc['sZip1'], $info_sc['sAddress']);
                $info_sc['sZip1']    = $this->zipfilter($info_sc['sZip1']);

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = mb_substr($_name, 0, 5, 'UTF-8');
                $arrItem[$index]['D']  = $_name;
                $arrItem[$index]['G']  = $serialnum;
                $arrItem[$index]['I']  = $info_sc['sZip1'];
                $arrItem[$index]['J']  = $address;
                $arrItem[$index]['K']  = $info_sc['sAddress'];
                $arrItem[$index]['M']  = $info_sc['sTelArea'] . '-' . $info_sc['sTelMain'];
                $arrItem[$index]['N']  = $info_sc['sTelArea'] . '-' . $info_sc['sTelMain'];
                $arrItem[$index]['O']  = $info_sc['sTelArea'] . '-' . $info_sc['sTelMain'];
                $arrItem[$index]['S']  = $info_sc['sMobileNum'];
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = '2';
                $arrItem[$index]['BB'] = $info_sc['sZip1'];
                $arrItem[$index]['BF'] = $giver_num;
                $arrItem[$index]['BR'] = $_num . '0';
                $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                //電子發票 (2015-06-29)
                $arrItem[$index]['CM'] = '1';
                $arrItem[$index]['CN'] = '1';

                if ($invoice_kind == '3') {
                    $arrItem[$index]['BQ'] = $_name . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $_name . ' 會計部收(' . substr($roc_date, 3) . ')';
                } else {
                    $arrItem[$index]['BQ'] = $_name . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                    $arrItem[$index]['L']  = $_name . ' ';
                    if (substr($giver_num, 1, 1) == '1') {
                        $arrItem[$index]['BQ'] .= '先生收';
                        $arrItem[$index]['L'] .= '先生收';
                    } else if (substr($giver_num, 1, 1) == '2') {
                        $arrItem[$index]['BQ'] .= '小姐收';
                        $arrItem[$index]['L'] .= '小姐收';
                    } else {
                        $arrItem[$index]['BQ'] .= '先生小姐收';
                        $arrItem[$index]['L'] .= '先生小姐收';
                    }

                    $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                }

                $arrItem[$index]['BN'] = 'D';
                if ($data_sc['cInvoiceDonate'] == '1') { //發票捐贈
                    $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                    $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                    $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                    $arrItem[$index]['BQ'] = '';
                    $arrItem[$index]['BR'] = $_num . '1';
                }
                ##

                $index++;
            }
            ##

            //發票新合約書代書 (2015-06-30)
            $_arr = array();

            $sql  = 'SELECT * FROM tContractInvoiceExt WHERE cCertifiedId="' . $v['cCertifiedId'] . '" AND cDBName="tContractScrivener" ORDER BY cId ASC;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $_arr = $stmt->fetchALL(PDO::FETCH_ASSOC);

            foreach ($_arr as $ka => $va) {
                if ($va['cInvoiceMoney'] > 0) {
                    $giver_num = $va['cIdentifyId']; //客供商代號(統編或身分證字號)
                    $_num      = $roc_date . $v['cCertifiedId'] . '4' . str_pad((++$total_index), 2, '0', STR_PAD_LEFT);

                    if (preg_match("/^\w{10}$/", $giver_num)) {
                        $invoice_kind = '2';
                        $serialnum    = $giver_num;
                    } else if (preg_match("/^\d{8}$/", $giver_num)) {
                        $invoice_kind = '3';
                        $serialnum    = $giver_num;
                    } else {
                        $invoice_kind = '';
                        $serialnum    = '';
                    }

                    $va['cInvoiceAddr'] = $this->ConvertZipToAddr($va['cInvoiceZip'], $va['cInvoiceAddr']);
                    $va['cInvoiceZip']  = $this->zipfilter($va['cInvoiceZip']);

                    $arrItem[$index]['A']  = $giver_num;
                    $arrItem[$index]['B']  = '2';
                    $arrItem[$index]['C']  = mb_substr($va['cName'], 0, 5, 'UTF-8');
                    $arrItem[$index]['D']  = $va['cName'];
                    $arrItem[$index]['G']  = $serialnum;
                    $arrItem[$index]['I']  = $va['cInvoiceZip'];
                    $arrItem[$index]['J']  = $address;
                    $arrItem[$index]['K']  = $va['cInvoiceAddr'];
                    $arrItem[$index]['M']  = '-';
                    $arrItem[$index]['N']  = '-';
                    $arrItem[$index]['O']  = '-';
                    $arrItem[$index]['S']  = '';
                    $arrItem[$index]['X']  = '100';
                    $arrItem[$index]['AA'] = '100';
                    $arrItem[$index]['AC'] = $roc_date;
                    $arrItem[$index]['AK'] = '2';
                    $arrItem[$index]['BB'] = $va['cInvoiceZip'];
                    $arrItem[$index]['BF'] = $giver_num;
                    $arrItem[$index]['BR'] = $_num . '2';
                    $arrItem[$index]['BS'] = '保證號碼:' . $v['cCertifiedId'];

                    //電子發票 (2015-06-30)
                    $arrItem[$index]['CM'] = '1';
                    $arrItem[$index]['CN'] = '1';

                    if ($invoice_kind == '3') {
                        $arrItem[$index]['BQ'] = $va['cName'] . ' 會計部收(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' 會計部收(' . substr($roc_date, 3) . ')';
                    } else {
                        $arrItem[$index]['BQ'] = $va['cName'] . '(' . $v['cCertifiedId'] . '-' . substr($roc_date, 3) . ')';
                        $arrItem[$index]['L']  = $va['cName'] . ' ';

                        if (substr($giver_num, 1, 1) == '1') {
                            $arrItem[$index]['BQ'] .= '先生收';
                            $arrItem[$index]['L'] .= '先生收';
                        } else if (substr($giver_num, 1, 1) == '2') {
                            $arrItem[$index]['BQ'] .= '小姐收';
                            $arrItem[$index]['L'] .= '小姐收';
                        } else {
                            $arrItem[$index]['BQ'] .= '先生小姐收';
                            $arrItem[$index]['L'] .= '先生小姐收';
                        }

                        $arrItem[$index]['L'] .= '(' . substr($roc_date, 3) . ')';
                    }

                    $arrItem[$index]['BN'] = 'D';
                    if ($va['cInvoiceDonate'] == '1') { //發票捐贈
                        $arrItem[$index]['BN'] = 'D'; //電子發票通知方式
                        $arrItem[$index]['BO'] = 'Y'; //發票預設捐贈
                        $arrItem[$index]['BP'] = $kindlyNo; //預設發票捐贈對象 20150825改用愛心碼
                        $arrItem[$index]['BQ'] = '';
                        $arrItem[$index]['BR'] = $_num . '1';
                    }
                    ##

                    $index++;
                }
            }
            unset($_arr);
            ##

            //合約書其他(創世基金會)
            if ($data_invoice['cInvoiceOther'] > 0) {
                $giver_num    = $v['cCertifiedId'] . $this->no2ascii(++$a_index);
                $invoice_kind = '2';

                $arrItem[$index]['A']  = $giver_num;
                $arrItem[$index]['B']  = '2';
                $arrItem[$index]['C']  = '創世基金會';
                $arrItem[$index]['D']  = '創世基金會';
                $arrItem[$index]['G']  = '';
                $arrItem[$index]['I']  = '100';
                $arrItem[$index]['J']  = $this->maskAddr('台北市北平東路28號4樓');
                $arrItem[$index]['K']  = '台北市北平東路28號4樓';
                $arrItem[$index]['L']  = '台北市北平東路28號4樓';
                $arrItem[$index]['M']  = '-';
                $arrItem[$index]['N']  = '-';
                $arrItem[$index]['O']  = '-';
                $arrItem[$index]['S']  = '';
                $arrItem[$index]['X']  = '100';
                $arrItem[$index]['AA'] = '100';
                $arrItem[$index]['AC'] = $roc_date;
                $arrItem[$index]['AK'] = $invoice_kind;
                $arrItem[$index]['BB'] = '100';
                $arrItem[$index]['BF'] = $v['cCertifiedId'];

                $index++;
            }
            ##
        }

        $this->mArrField = $arrItem;
    }

    public function GenerateFileName($prefix, $action = 'date')
    {
        $filename = $prefix . "_";
        if ($action == 'date') {
            $filename .= str_replace('-', '', $this->mArrRule['fds']) . "_";
            $filename .= str_replace('-', '', $this->mArrRule['fde']);
        } else {
            $filename .= $action;
        }
        return $filename;
    }

    //地址遮罩
    private function maskAddr($addr)
    {
        $patt = array('路', '道', '街');
        $chk  = 0;

        foreach ($patt as $k => $v) {
            if (preg_match("/$v/", $addr)) {
                $tmp  = array();
                $last = 0;
                $ch   = $v;

                $tmp = explode($v, $addr);
                if (count($tmp) <= 1) {
                    $tmp[0] = $addr;
                } else {
                    $last       = count($tmp) - 1;
                    $tmp[$last] = '*****';
                    $chk++;
                }

                $addr = implode($ch, $tmp);

                unset($tmp);
                break; //比對到一個字即跳出
            }
        }
        unset($patt);

        if ($chk == 0) {
            $patt = array('巷');
            foreach ($patt as $k => $v) {
                if (preg_match("/$v/", $addr)) {
                    $tmp  = array();
                    $last = 0;
                    $ch   = $v;

                    $tmp = explode($v, $addr);
                    if (count($tmp) <= 1) {
                        $tmp[0] = $addr;
                    } else {
                        $last       = count($tmp) - 1;
                        $tmp[$last] = '*****';
                        $chk++;
                    }

                    $addr = implode($ch, $tmp);

                    unset($tmp);
                    break; //比對到一個字即跳出
                }
            }
            unset($patt);
        }

        if ($chk == 0) {
            $patt = array('鄰', '村', '里');
            foreach ($patt as $k => $v) {
                if (preg_match("/$v/", $addr)) {
                    $tmp  = array();
                    $last = 0;
                    $ch   = $v;

                    $tmp = explode($v, $addr);
                    if (count($tmp) <= 1) {
                        $tmp[0] = $addr;
                    } else {
                        $last       = count($tmp) - 1;
                        $tmp[$last] = '*****';
                        $chk++;
                    }

                    $addr = implode($ch, $tmp);

                    unset($tmp);
                    break; //比對到一個字即跳出
                }
            }
            unset($patt);
        }

        return $addr;
    }

    //建物門牌路段後面截掉
    private function getAddress($cId)
    {
        $sql = 'SELECT
			(SELECT zCity FROM tZipArea AS z WHERE z.zZip=p.cZip) AS City,
			(SELECT zArea FROM tZipArea AS z WHERE z.zZip=p.cZip) AS Area,
			p.cAddr AS Addr
		FROM
			tContractProperty AS p
		WHERE
			p.cCertifiedId ="' . $cId . '"';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $property = $stmt->fetch(PDO::FETCH_ASSOC);

        return $property['City'] . $property['Area'] . mb_substr($property['Addr'], 0, 3, 'UTF-8') . '***';
    }
    ##
}
