<?php
require_once __DIR__ . '/advance.class.php';
require_once __DIR__ . '/contract.class.php';
require_once __DIR__ . '/scrivener.class.php';
require_once __DIR__ . '/brand.class.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

class ExcelInvoiceCS extends Advance
{
    private $mArrTitle    = null;
    private $mObjPHPExcel = null;

    public function __construct()
    {
        parent::__construct();

        $this->mObjPHPExcel = new PHPExcel();
//        $this->mObjPHPExcel->getProperties()->sethistCreator("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setLastModifiedBy("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setTitle("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setSubject("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setDescription("台灣房屋");

        $this->mArrTitle       = array();
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
    }

    public function PutColumnTtile()
    {
        $this->mObjPHPExcel->setActiveSheetIndex(0);
        foreach ($this->mArrTitle as $k => $v) {
            $this->mObjPHPExcel->getActiveSheet()->SetCellValue($k, $v);
        }
    }

    public function PutDataItem($list_case)
    {
        $contract = new Contract();
        $sc       = new Scrivener();
        $brand    = new Brand();
        $index    = 2;

        foreach ($list_case as $k => $v) {
            $giver_num        = ""; //客戶供應商代號
            $arrItem          = array();
            $data_buyer       = $contract->GetBuyer($v['cCertifiedId']);
            $data_owner       = $contract->GetOwner($v['cCertifiedId']);
            $data_invoice     = $contract->GetIncome($v['cCertifiedId']);
            $data_expenditure = $contract->GetExpenditure($v['cCertifiedId']);
            $data_property    = $contract->GetProperty($v['cCertifiedId']);
            $data_res         = $contract->GetRealstate($v['cCertifiedId']);
            $data_sc          = $contract->GetScrivener($v['cCertifiedId']);
            $data_income      = $contract->GetIncome($v['cCertifiedId']);

            $info_sc     = $sc->GetScrivenerInfo($data_sc['cScrivener']);
            $info_branch = $brand->GetBranch($data_res['cBranchNum']);
            $info_branch = $info_branch[0];

            if (($v['target'] & (1 << 1)) > 0) {
                $giver_num = $v['cCertifiedId'] . "1";
                if ($data_buyer['cCategoryIdentify'] == '1') {
                    $invoice_kind = '2';
                } else {
                    $invoice_kind = '3';
                }
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($data_buyer['cName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $data_buyer['cName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_buyer['cIdentifyId']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $data_buyer['cMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, $invoice_kind);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }
            if (($v['target'] & (1 << 2)) > 0) {
                $giver_num = $v['cCertifiedId'] . "2";
                if ($data_owner['cCategoryIdentify'] == '1') {
                    $invoice_kind = '2';
                } else {
                    $invoice_kind = '3';
                }
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($data_owner['cName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $data_owner['cName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_owner['cIdentifyId']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $data_owner['cMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, $invoice_kind);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }

            if (($v['target'] & (1 << 3)) > 0) {
                $giver_num = $v['cCertifiedId'] . "3";
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($info_branch['bName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $info_branch['bName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_res['cSerialNumber']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, $info_branch['bFaxArea'] . "-" . $info_branch['bFaxMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $info_branch['bMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, '3');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }
            if (($v['target'] & (1 << 4)) > 0) {
                $giver_num = $v['cCertifiedId'] . "4";
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($info_sc['sOffice'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $info_sc['sOffice']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $info_sc['sSerialnum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $info_sc['sMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, '3');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);
                $index++;
            }
        }
    }

    public function OutPut($filename, $version)
    {
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');

        $objWriter = PHPExcel_IOFactory::createWriter($this->mObjPHPExcel, 'Excel2007');
        $objWriter->save("php://output");
    }

    public function GetCaseList($rule)
    {
        $sql = "SELECT
                    *
                FROM
                    `tContractCase`
                WHERE
                    cCaseStatus = '3'
                    AND cEndDate between '" . $rule['fds'] . "' AND '" . $rule['fde'] . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetInvoice($id)
    {
        $sql  = "SELECT  * FROM tContractInvoice WHERE cCertifiedId = '" . $id . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetGiverTarget($list_data)
    {
        $se = 0;
        foreach ($list_data as $k => $v) {
            if ($k == 'cSplitBuyer' && $v == '1') {
                $se += (1 << 1);
            }
            if ($k == 'cSplitOwner' && $v == '1') {
                $se += (1 << 2);
            }
            if ($k == 'cSplitRealestate' && $v == '1') {
                $se += (1 << 3);
            }
            if ($k == 'cSplitScrivener' && $v == '1') {
                $se += (1 << 4);
            }
            if ($k == 'cSplitOther' && $v == '1') {
                $se += (1 << 5);
            }
        }
        return $se;
    }

    public function GetFileName($prefix, $rule)
    {
        $rule['fds'] = str_replace('-', '', $rule['fds']);
        $rule['fde'] = str_replace('-', '', $rule['fde']);
        $filename    = $prefix . "_" . $rule['fds'] . "_" . $rule['fde'];
        return $filename;
    }
}

class ExcelPurchase extends Advance
{
    public function __construct()
    {
        parent::__construct();

        $this->mObjPHPExcel = new PHPExcel();
//        $this->mObjPHPExcel->getProperties()->sethistCreator("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setLastModifiedBy("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setTitle("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setSubject("台灣房屋");
        $this->mObjPHPExcel->getProperties()->setDescription("台灣房屋");

        $this->mArrTitle       = array();
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

    public function GetCaseList($rule)
    {
        $sql = "SELECT
                    *
                FROM
                    `tContractCase`
                WHERE
                    cCaseStatus = '3'
                    AND cEndDate between '" . $rule['fds'] . "' AND '" . $rule['fde'] . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetInvoice($id)
    {
        $sql  = "SELECT  * FROM tContractInvoice WHERE cCertifiedId = '" . $id . "'; ";
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetGiverTarget($list_data)
    {
        $se = 0;
        foreach ($list_data as $k => $v) {
            if ($k == 'cSplitBuyer' && $v == '1') {
                $se += (1 << 1);
            }
            if ($k == 'cSplitOwner' && $v == '1') {
                $se += (1 << 2);
            }
            if ($k == 'cSplitRealestate' && $v == '1') {
                $se += (1 << 3);
            }
            if ($k == 'cSplitScrivener' && $v == '1') {
                $se += (1 << 4);
            }
            if ($k == 'cSplitOther' && $v == '1') {
                $se += (1 << 5);
            }
        }
        return $se;
    }

    public function PutColumnTtile()
    {
        $this->mObjPHPExcel->setActiveSheetIndex(0);
        foreach ($this->mArrTitle as $k => $v) {
            $this->mObjPHPExcel->getActiveSheet()->SetCellValue($k, $v);
        }
    }

    public function PutDataItem($list_case)
    {
        $contract = new Contract();
        $sc       = new Scrivener();
        $brand    = new Brand();
        $index    = 2;

        foreach ($list_case as $k => $v) {
            $giver_num        = ""; //客戶供應商代號
            $arrItem          = array();
            $data_buyer       = $contract->GetBuyer($v['cCertifiedId']);
            $data_owner       = $contract->GetOwner($v['cCertifiedId']);
            $data_invoice     = $contract->GetIncome($v['cCertifiedId']);
            $data_expenditure = $contract->GetExpenditure($v['cCertifiedId']);
            $data_property    = $contract->GetProperty($v['cCertifiedId']);
            $data_res         = $contract->GetRealstate($v['cCertifiedId']);
            $data_sc          = $contract->GetScrivener($v['cCertifiedId']);
            $data_income      = $contract->GetIncome($v['cCertifiedId']);

            $info_sc     = $sc->GetScrivenerInfo($data_sc['cScrivener']);
            $info_branch = $brand->GetBranch($data_res['cBranchNum']);
            $info_branch = $info_branch[0];

            if (($v['target'] & (1 << 1)) > 0) {
                $giver_num = $v['cCertifiedId'] . "1";
                if ($data_buyer['cCategoryIdentify'] == '1') {
                    $invoice_kind = '2';
                } else {
                    $invoice_kind = '3';
                }
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($data_buyer['cName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $data_buyer['cName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_buyer['cIdentifyId']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $data_buyer['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $data_buyer['cTelArea1'] . "-" . $data_buyer['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $data_buyer['cMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, $invoice_kind);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $data_buyer['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }
            if (($v['target'] & (1 << 2)) > 0) {
                $giver_num = $v['cCertifiedId'] . "2";
                if ($data_owner['cCategoryIdentify'] == '1') {
                    $invoice_kind = '2';
                } else {
                    $invoice_kind = '3';
                }
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($data_owner['cName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $data_owner['cName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_owner['cIdentifyId']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $data_owner['cBaseAddr']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $data_owner['cTelArea1'] . "-" . $data_owner['cTelMain1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $data_owner['cMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, $invoice_kind);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $data_owner['cBaseZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }

            if (($v['target'] & (1 << 3)) > 0) {
                $giver_num = $v['cCertifiedId'] . "3";
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($info_branch['bName'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $info_branch['bName']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $data_res['cSerialNumber']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $info_branch['bAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $info_branch['bTelArea'] . "-" . $info_branch['bTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, $info_branch['bFaxArea'] . "-" . $info_branch['bFaxMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $info_branch['bMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, '3');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $info_branch['bZip']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);

                $index++;
            }
            if (($v['target'] & (1 << 4)) > 0) {
                $giver_num = $v['cCertifiedId'] . "4";
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('A' . $index, $giver_num);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('B' . $index, '2');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('C' . $index, mb_substr($info_sc['sOffice'], 0, 5, 'UTF-8'));
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('D' . $index, $info_sc['sOffice']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('E' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('F' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('G' . $index, $info_sc['sSerialnum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('H' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('I' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('J' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('K' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('L' . $index, $info_sc['sCpAddress']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('M' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('N' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('O' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('P' . $index, $info_sc['sTelArea'] . "-" . $info_sc['sTelMain']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Q' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('R' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('S' . $index, $info_sc['sMobileNum']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('T' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('U' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('V' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('W' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('X' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Y' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('Z' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AA' . $index, '100');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AB' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AC' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AF' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AG' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AH' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AJ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AK' . $index, '3');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AL' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AM' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AN' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AO' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AP' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AQ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AR' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AS' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AT' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AU' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AV' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AW' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AX' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AY' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('AZ' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BA' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BB' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BC' . $index, $info_sc['sCpZip1']);
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BD' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BE' . $index, '');
                $this->mObjPHPExcel->getActiveSheet()->SetCellValue('BF' . $index, $v['cCertifiedId']);
                $index++;
            }
        }
    }

    public function OutPut($filename)
    {
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename . '.xlsx');

        $objWriter = PHPExcel_IOFactory::createWriter($this->mObjPHPExcel, 'Excel2007');
        $objWriter->save("php://output");
    }
}
