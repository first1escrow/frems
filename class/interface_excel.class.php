<?php

require_once __DIR__ . '/base.class.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel.php';
require_once dirname(__DIR__) . '/libs/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php';

abstract class ExcelBase extends Base
{

    protected $mObjPHPExcel     = null;
    protected $mArrRule         = null;
    protected $mArrTitle        = null;
    protected $mArrBg           = null;
    protected $mArrField        = null;
    const PARAM_EXCEL_EXCEL2003 = 1;
    const PARAM_EXCEL_EXCEL2007 = 2;

    public function __construct()
    {
        parent::__construct();
        $this->mObjPHPExcel = new PHPExcel();
    }

    public function GenerateMeta($name = null)
    {
        $name = ($name == null) ? 'First Real Estate Management Co.,Ltd.' : $name;
//        $this->mObjPHPExcel->getProperties()->sethistCreator($name);
        $this->mObjPHPExcel->getProperties()->setLastModifiedBy($name);
        $this->mObjPHPExcel->getProperties()->setTitle($name);
        $this->mObjPHPExcel->getProperties()->setSubject($name);
        $this->mObjPHPExcel->getProperties()->setDescription($name);
    }

    abstract public function GenerateTitle();

    abstract public function GenerateField();

    public function PutTitleInto($title = null)
    {
        $title = ($title == null) ? $this->mArrTitle : $title;
        $this->mObjPHPExcel->setActiveSheetIndex(0);
        foreach ($title as $k => $v) {
            $this->mObjPHPExcel->getActiveSheet()->SetCellValue($k, $v);
        }
    }

    public function PutBgInto()
    {
        $this->mObjPHPExcel->setActiveSheetIndex(0);
        foreach ($this->mArrBg as $k => $v) {
            $this->mObjPHPExcel->getActiveSheet()->getStyle($k)->applyFromArray($v);
        }
    }

    public function PutDataInto($data = null)
    {
        $arr_prefix = $this->GetExcelTitleNum();
        $data       = ($data == null) ? $this->mArrField : $data;
        $index      = 0;

        $this->mObjPHPExcel->setActiveSheetIndex(0);
        foreach ($this->mArrField as $k => $v) {
            foreach ($arr_prefix as $prefix) {
                if (isset($v[$prefix])) {
                    $this->mObjPHPExcel->getActiveSheet()->getCell($prefix . ($k))
                        ->setValueExplicit($v[$prefix], PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $this->mObjPHPExcel->getActiveSheet()->getCell($prefix . ($k))->
                        setValueExplicit('', PHPExcel_Cell_DataType::TYPE_STRING);
                }
            }
        }

        foreach ($arr_prefix as $prefix) {
            $this->mObjPHPExcel->getActiveSheet()->getColumnDimension($prefix)->setWidth(20);
        }
    }

    public function OutPutBrower($filename, $version = self::PARAM_EXCEL_EXCEL2003)
    {
        switch ($version) {
            case self::PARAM_EXCEL_EXCEL2003:
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->mObjPHPExcel, 'Excel5');
                break;
            case self::PARAM_EXCEL_EXCEL2007:
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->mObjPHPExcel, 'Excel2007');
                break;
        }
        $objWriter->save("php://output");
    }

    public function GetExcelTitleNum()
    {
        $index      = 0;
        $arr_head   = array();
        $cnt        = count($this->mArrTitle) - 1;
        $arr_prefix = range('A', 'Z');
        array_unshift($arr_prefix, '');
        foreach ($arr_prefix as $prefix) {
            foreach (range('A', 'Z') as $word) {
                $arr_head[] = $prefix . $word;
                $index++;
                if ($index > $cnt) {
                    break;
                }
            }
            if ($index > $cnt) {
                break;
            }
        }
        return $arr_head;
    }

    protected function GetInvoiceTarget($data_invoice)
    {
        $se = 0;
        foreach ($data_invoice as $k => $v) {
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

    protected function ConvertToROCYear(&$year, $date)
    {
        if (strlen($date) >= 10) {
            $year = substr($date, 0, 4);
            $year = $year - 1911;
            return true;
        } else {
            return false;
        }
    }

    protected function ConvertToRocDate(&$roc_date, $ad_date)
    {
        $year = '102';
        if (strlen($ad_date) >= 10) {
            $year     = substr($ad_date, 0, 4);
            $year     = $year - 1911;
            $month    = substr($ad_date, 5, 2);
            $day      = substr($ad_date, 8, 2);
            $roc_date = $year . $month . $day;
            return true;
        } else {
            return false;
        }
        return $year;
    }

}
