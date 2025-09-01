<?php

function tDate_check($_date, $_dateForm='ymd', $_dateType='r', $_delimiter='', $_minus=0, $_sat=0) {
    $_aDate[0] = substr($_date,0,4) ;
    $_aDate[1] = substr($_date,4,2) ;
    $_aDate[2] = substr($_date,6) ;

    //是否遇六日要延後(六延兩天、日延一天)
    if ($_sat == '1') {
        $_ss = 0 ;
        $_ss = date("w",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;
        if ($_ss == '0') {      //如果是星期日的話，則延後一天
            $weekend = 1;
        } else if ($_ss == '6') {   //如果是星期六的話，則延後兩天
            $weekend = 2;
        }
    }

    $_minus = $_minus + $weekend;//傳進來的日期必須加上遇到加日延後的日期
    $_t = date("Y-m-d",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;    //設定日期為 t+1 天
    unset($_aDate) ;

    $_aDate = explode('-',$_t) ;

    if ($_dateType == 'r') {    //若要回覆日期格式為"民國"
        $_aDate[0] = $_aDate[0] - 1911 ;
    } else {            //若要回覆日期格式為"西元"
    }

    //決定回覆日期格式
    switch ($_dateForm) {
        case 'y':       //年
            return $_aDate[0] ;
            break ;
        case 'm':       //月
            return $_aDate[1] ;
            break ;
        case 'd':       //日
            return $_aDate[2] ;
            break ;
        case 'ym':        //年月
            return $_aDate[0].$_delimiter.$_aDate[1] ;
            break ;
        case 'md':        //月日
            return $_aDate[1].$_delimiter.$_aDate[2] ;
            break ;
        case 'ymd':       //年月日
            return $_aDate[0].$_delimiter.$_aDate[1].$_delimiter.$_aDate[2] ;
            break ;
        default:
            break ;
    }
}

function dataFormate($oneBank, $v, $data, $type) {
    $_tVR = $v['tVR_Code'] ;
    if($type == 1) {
        $v['endDate'] = ($v['endDate'] != '0000-00-00 00:00:00') ? (substr($v['endDate'], 0,4)-1911) . substr($v['endDate'], 4,6) : '000-00-00';
    }
    if($type == 2) {
        $v['endDate'] = (substr($v['endDate'], 0,4)-1911) . substr($v['endDate'], 4,10);
    }
    if (preg_match($oneBank['cInterestAccount'], $_tVR)) {
        $v["tBank_kind"] = '利息' ;		//利息帳戶
    }
    $v['bk'] = $oneBank['cId'];

    $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]] = $v;
    $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]]['tVR_Code'] = $v["tVR_Code"];
    $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]]['Total'] = $v["Total"];
    $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]]['count'] = $v['C'];
    $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]]['tDate'] = $v["tDate"];
    $data[$oneBank['cBankVR']][$v['tOwner']][$v['tVR_Code']]['tExport_time'] = $v['tExport_time'];
    if($type == 1) {
        $data[$oneBank['cBankVR']][$v['tOwner']][$v["tVR_Code"]]['tVR_Code'] = $v["tVR_Code"];
        $data[$oneBank['cBankVR']][$v['tOwner']][$v['tVR_Code']]['endDate'] = $v['endDate'];
    }


    return $data;
}
