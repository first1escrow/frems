<?php
    require_once dirname(__DIR__) . '/configs/config.class.php';
    require_once dirname(__DIR__) . '/openadodb.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/includes/writelog.php';
    require_once __DIR__ . '/create_register.php';

    if ($_SESSION['member_addcertifty'] == 0) {
        header('Location: http://www.first1.com.tw');
        exit;
    }

    //檢查是否使用過(後九碼)
    function CheckNoValid($_conn, $no, $sp)
    {
        global $conBank;

        $_NG_Code = ['444'];

        $_pattren1 = substr($no, 5, 9); //一銀台新格式
        $_pattren2 = substr($no, 6, 8); //永豐格式
        $_rs_num   = 0;

        $code = [];
        foreach ($conBank as $v) {
            $_len = strlen($v['cBankVR']);
            if ($_len == 5) {
                $code[] = $v['cBankVR'] . $_pattren1;
            } else if ($_len == 6) {
                $code[] = $v['cBankVR'] . $_pattren2;
            }
        }

        if (empty($code)) {
            $_sql = 'SELECT * FROM tBankCode WHERE bAccount LIKE "%' . $_pattren1 . '";';
        } else {
            $_sql = 'SELECT * FROM tBankCode WHERE bAccount IN ("' . implode('","', $code) . '");';
        }

        $_rs = $_conn->Execute($_sql);
        while (! $_rs->EOF) {
            $_rs_num++;

            $_rs->MoveNext();
        }

        if ($_rs_num > 0) {
            $_used = 1;
        } else {
            $_used = 0;

            //檢查是否為厭惡碼
            foreach ($_NG_Code as $key => $val) {
                if (preg_match("/$val/", $no)) {
                    $_used = 1;
                }
            }
        }

        if ($sp == 1) {
            if (substr($no, -1) != 8) {
                $_used = 1;
            }
        } elseif ($sp == 2) {
            if (substr($no, -1) == 4) {
                $_used = 1;
            }
        } elseif ($sp == 3) {
            $len = mb_strlen($no);
            for ($i = 0; $i < $len; $i++) {
                if (mb_substr($no, $i, 1) == 4) {
                    $_used = 1;
                }
            }
        }

        return $_used;
    }
    ##

    $save = $_POST["save"];

    $Lnum = $_POST['Lnum'] + 1 - 1; //土地申請數量
    $Bnum = $_POST['Bnum'] + 1 - 1; //建物申請數量
    $Snum = $_POST['Snum'] + 1 - 1; //建物申請數量
    $num  = $Lnum + $Bnum + $Snum;  //總數量
    $bank = $_POST["bank"];
    $man  = $_POST["man"];

    if ($_POST['bBrand'] == '請選擇') {
        $_POST['bBrand'] = '';
    }

    $bBrand   = $_POST['bBrand'];
    $bVersion = $_POST['ver'];
    $_total   = 0;

    //取得銀行暱稱alias
    $sql = 'SELECT * FROM tBankNum ORDER BY id ASC;';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $bank_name[$rs->fields['id']] = $rs->fields['bank'];
        $rs->MoveNext();
    }
    unset($rs);
    ##

    //取得合約銀行資訊
    $sql = 'SELECT * FROM tContractBank ORDER BY cId ASC;';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $conBank[$rs->fields['cId']] = $rs->fields;
        $rs->MoveNext();
    }
    unset($rs);
    ##

    $bd  = [1 => '加盟', 2 => '直營', 3 => '非仲介成交'];
    $msg = "確認KEY" . $_POST['key'] . "-" . $_SESSION['key'];

    if ($save == 'ok' && $_POST['key'] == $_SESSION['key'] && ! empty($num)) {
        // 從地政士郵遞區號取出一碼
        $sql = "select * from tScrivener,tZipArea where sId='$man' and sCpZip1 = zZip";

        $rs3   = $conn->Execute($sql);
        $_city = $rs3->fields["zCity"];
        $_area = $rs3->fields["zArea"];
        $_nid  = $rs3->fields["nid"]; // 區域代碼
        $sp    = $rs3->fields['sBankCodeSp'];

        if ($sp == 3) {
            //台中彰化代碼nid=4 所以出來的號碼都有4
            $_nid = 8;
        }

        if (! $_nid) {
            echo "請先選擇地政士郵遞區號，然後再重新申請!!";
            exit;
        }

        //取得地政士類型
        if ($_POST['bBrand'] == 1 && $rs3->fields['sCategory'] == 1) {
            $bCategory = 1; // (1:加盟、2:直營
        } elseif ($_POST['bBrand'] == 1 && $rs3->fields['sCategory'] == 2) {
            $bCategory = 2;
        } else {
            $bCategory = 3;
        }
        ##

        $sql           = 'SELECT * FROM tBankNum WHERE id="' . $bank . '";';
        $rs            = $conn->Execute($sql);
        $_start_num    = $rs->fields["number"];   //目前數量 1 ~ 999
        $_now_m_number = $rs->fields["m_number"]; //千分位
        $_total_nums   = $_start_num + $num;      //從起始號碼加上申請號碼數量

        //溢位檢查
        if ($_total_nums > 999) {
            $_number = $_now_m_number + 1;

            if (($_number > 999) && ($bank_name[$bank] == 'taishin')) { //台新 m_number 不可超過 999
                echo "系統錯誤!!請通知...你知道的!!!( m_number<<$_now_m_number>> over flow.)<br>\n";
                exit;
            }
            if (($_number > 99) && (($bank_name[$bank] == 'first') || ($bank_name[$bank] == 'far') || ($bank_name[$bank] == 'sinopac'))) {
                echo "系統錯誤!!請通知...你知道的!!!( m_number<<$_now_m_number>> over flow.)<br>\n";
                exit;
            }
            if (($_number > 9) && ($bank_name[$bank] == 'tcb')) { //台中銀 m_number 不可超過 9
                echo "系統錯誤!!請通知...你知道的!!!( m_number<<$_now_m_number>> over flow.)<br>\n";
                exit;
            }
        }
        ##

        //銀行專案代號
        $_bank_num = $conBank[$bank]['cBankVR'];
        ##

        switch ($bank) {
            case "1": //一銀桃園 60001
            case "7": //一銀城東 55006
                          //---------------------------------------------------------------------------
                for ($j = 0; $j < $num; $j++) {
                    $i     = $_start_num++;  //起始數字
                    $_mult = $_now_m_number; //千分位
                    if ($i > 999) {
                        $_start_num -= 1001;
                        $i = $_start_num++;

                        $_now_m_number += 1;
                        $_mult = $_now_m_number;
                    }

                    $_Y       = substr(date("Y") - 1911, -2);
                    $_M       = $_mult;
                    $_new_num = $_bank_num . $_Y . str_pad($_M, 2, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT) . $_nid;
                    $n1       = substr($_new_num, 0, 1) * 3;
                    $n2       = substr($_new_num, 1, 1) * 7;
                    $n3       = substr($_new_num, 2, 1) * 9;
                    $n4       = substr($_new_num, 3, 1) * 3;
                    $n5       = substr($_new_num, 4, 1) * 7;
                    $n6       = substr($_new_num, 5, 1) * 9;
                    $n7       = substr($_new_num, 6, 1) * 3;
                    $n8       = substr($_new_num, 7, 1) * 7;
                    $n9       = substr($_new_num, 8, 1) * 9;
                    $n10      = substr($_new_num, 9, 1) * 3;
                    $n11      = substr($_new_num, 10, 1) * 7;
                    $n12      = substr($_new_num, 11, 1) * 9;
                    $n13      = substr($_new_num, 12, 1) * 3;
                    $_k       = ($n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $n7 + $n8 + $n9 + $n10 + $n11 + $n12 + $n13) % 11;

                    if ($_k == 0) {$_t = 0;} else if ($_k == 1) {$_t = 1;} else { $_t = 11 - $_k;}

                    $_ok = CheckNoValid($conn, $_new_num . $_t, $sp);
                    if ($_ok == '0') {                  //若虛擬帳號不存在且非厭惡碼
                        $_bank_account[] = $_new_num . $_t; //則產生
                    } else {                            //若虛擬帳號已存在或為厭惡碼
                        $num++;                             //則略過此帳號並再多產生一筆帳號
                    }
                }

                $_sql = 'UPDATE tBankNum SET number="' . $_start_num . '", m_number="' . $_mult . '" WHERE id="' . $bank . '"';
                $conn->Execute($_sql);
                //---------------------------------------------------------------------------
                break;

            case "4": //永豐西門 999850
            case "6": //永豐城中 999860
                          //---------------------------------------------------------------------------
                for ($j = 0; $j < $num; $j++) {
                    $i     = $_start_num++;  //起始數字
                    $_mult = $_now_m_number; //千分位
                    if ($i > 999) {
                        $_start_num -= 1001;
                        $i = $_start_num++;

                        $_now_m_number += 1;
                        $_mult = $_now_m_number;
                    }

                    $_Y       = substr(date("Y") - 1911, -2);
                    $_M       = $_mult;
                    $_new_num = $_bank_num . $_Y . str_pad($_M, 2, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $n1       = substr($_new_num, 0, 1) * 9;
                    $n2       = substr($_new_num, 1, 1) * 8;
                    $n3       = substr($_new_num, 2, 1) * 7;
                    $n4       = substr($_new_num, 3, 1) * 6;
                    $n5       = substr($_new_num, 4, 1) * 5;
                    $n6       = substr($_new_num, 5, 1) * 4;
                    $n7       = substr($_new_num, 6, 1) * 3;
                    $n8       = substr($_new_num, 7, 1) * 2;
                    $n9       = substr($_new_num, 8, 1) * 1;
                    $n10      = substr($_new_num, 9, 1) * 2;
                    $n11      = substr($_new_num, 10, 1) * 3;
                    $n12      = substr($_new_num, 11, 1) * 4;
                    $n13      = substr($_new_num, 12, 1) * 5;
                    $_t       = ($n1 + $n2 + $n3 + $n4 + $n5 + $n6 + $n7 + $n8 + $n9 + $n10 + $n11 + $n12 + $n13) % 10;

                    $_ok = CheckNoValid($conn, $_new_num . $_t, $sp);
                    if ($_ok == '0') {                  //若虛擬帳號不存在且非厭惡碼
                        $_bank_account[] = $_new_num . $_t; //則產生
                    } else {                            //若虛擬帳號已存在或為厭惡碼
                        $num++;                             //則略過此帳號並再多產生一筆帳號
                    }
                }

                $_sql = 'UPDATE tBankNum SET number="' . $_start_num . '", m_number="' . $_mult . '" WHERE id="' . $bank . '"';
                $conn->Execute($_sql);
                //---------------------------------------------------------------------------
                break;

            case "5": //台新建北 96988
                          //---------------------------------------------------------------------------
                for ($j = 0; $j < $num; $j++) {
                    $i     = $_start_num++;  //起始數字
                    $_mult = $_now_m_number; //千分位
                    if ($i > 999) {
                        $_start_num -= 1001;
                        $i = $_start_num++;

                        $_now_m_number += 1;
                        $_mult = $_now_m_number;
                    }
                    $_Y = substr(date("Y") - 1911, -2);
                    $_M = $_mult;

                    $_new_num = $_bank_num . $_Y . str_pad($_M, 3, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $n14      = substr($_new_num, 0, 1);
                    $n13      = substr($_new_num, 1, 1);
                    $n12      = substr($_new_num, 2, 1);
                    $n11      = substr($_new_num, 3, 1);
                    $n10      = substr($_new_num, 4, 1);
                    $n9       = substr($_new_num, 5, 1);
                    $n8       = substr($_new_num, 6, 1);
                    $n7       = substr($_new_num, 7, 1);
                    $n6       = substr($_new_num, 8, 1);
                    $n5       = substr($_new_num, 9, 1);
                    $n4       = substr($_new_num, 10, 1);
                    $n3       = substr($_new_num, 11, 1);
                    $n2       = substr($_new_num, 12, 1);

                    $even_no = ($n14 + $n12 + $n10 + $n8 + $n6 + $n4 + $n2) * 3; //偶數碼相加*3
                    $odd_no  = $n13 + $n11 + $n9 + $n7 + $n5 + $n3;              //奇數碼相加
                    $_k      = substr(($even_no + $odd_no), -1);                 //取得個位數碼
                    if ($_k == 0) {
                        $_t = $_k; //若個位數為0、
                    } else {
                        $_t = 10 - $_k; //取 10 的補數
                    }

                    $_ok = CheckNoValid($conn, $_new_num . $_t, $sp);
                    if ($_ok == '0') {                  //若虛擬帳號不存在且非厭惡碼
                        $_bank_account[] = $_new_num . $_t; //則產生
                    } else {                            //若虛擬帳號已存在或為厭惡碼
                        $num++;                             //則略過此帳號並再多產生一筆帳號
                    }
                }

                $_sql = 'UPDATE tBankNum SET number="' . $_start_num . '", m_number="' . $_mult . '" WHERE id="' . $bank . '"';
                $conn->Execute($_sql);
                //---------------------------------------------------------------------------
                break;

        }

        //所有已申請保證號碼總數
        $_total = count($_bank_account);
        ##

        // 統一確定 escrowBank 值（使用第一個生成的帳號來決定）
        $escrowBank = '';
        if (! empty($_bank_account)) {
            $escrowBank = preg_match("/^9998[5|6]?/", $_bank_account[0]) ? substr($_bank_account[0], 0, 6) : substr($_bank_account[0], 0, 5);
        }

        $msg .= "土地:";

        if (($Lnum + $Bnum + $Snum) > 0) {
            $showNo = addForm2($man, ($Lnum + $Bnum + $Snum), $bBrand, $bCategory, $bank, $Snum);
        }

        $bankRegister = new CreateBankRegister($conn);

        for ($i = 0; $i < $Lnum; $i++) {
            $sql_insert = '
                INSERT INTO
                    tBankCode
                    (
                        bSID,
                        bAccount,
                        bBrand,
                        bCategory,
                        bApplication,
                        bCreatePerson,
                        bVersion,
                        bFormNo,
                        bFormNo2
                    )
                VALUES
                    (
                        "' . $man . '",
                        "' . $_bank_account[$i] . '",
                        "' . $bBrand . '",
                        "' . $bCategory . '",
                        "1",
                        "' . $_SESSION['member_id'] . '",
                        "' . $bVersion . '",
                        "' . $formId . '",
                        "' . $showNo['id'] . '"
                    ) ;
		    ';
            $conn->Execute($sql_insert);

            $msg .= $_bank_account[$i] . ";";
            $Llist[$i] = $_bank_account[$i];
        }

        if (! empty($Lnum) && ! empty($_POST['aId'])) {
            $bankRegister->applyBabkCode($_POST['aId'], $showNo['id'], $man, $bBrand, $bCategory, '1', $escrowBank, $Lnum, $_SESSION['member_id']);
        }

        $msg .= "建物:";

        //建物保證號碼數量指定分配
        $j = 0;
        for ($i = $Lnum; $i < ($Lnum + $Bnum); $i++) {
            $sql_insert = '
                INSERT INTO
                    tBankCode
                    (
                        bSID,
                        bAccount,
                        bBrand,
                        bCategory,
                        bApplication,
                        bCreatePerson,
                        bVersion,
                        bFormNo,
                        bFormNo2
                    )
                VALUES
                    (
                        "' . $man . '",
                        "' . $_bank_account[$i] . '",
                        "' . $bBrand . '",
                        "' . $bCategory . '",
                        "2",
                        "' . $_SESSION['member_id'] . '",
                        "' . $bVersion . '",
                        "' . $formId . '",
                        "' . $showNo['id'] . '"
                    ) ;
            ';
            $conn->Execute($sql_insert);

            $msg .= $_bank_account[$i] . ";";
            $Blist[$j++] = $_bank_account[$i];
        }

        if (! empty($Bnum) && ! empty($_POST['aId'])) {
            $bankRegister->applyBabkCode($_POST['aId'], $showNo['id'], $man, $bBrand, $bCategory, '2', $escrowBank, $Bnum, $_SESSION['member_id']);
        }

        //名稱,帳號,IP,時間,地政士,KEY,保號
        write_log($man . ',' . $msg . ',', 'create_code');

        $msg .= "預售屋買賣契約";

        //預售屋買賣契約保證號碼數量指定分配
        $j = 0;
        for ($i = ($Lnum + $Bnum); $i < ($Lnum + $Bnum + $Snum); $i++) {
            $sql_insert = '
                INSERT INTO
                    tBankCode
                    (
                        bSID,
                        bAccount,
                        bBrand,
                        bCategory,
                        bApplication,
                        bCreatePerson,
                        bVersion,
                        bFormNo,
                        bFormNo2
                    )
                VALUES
                    (
                        "' . $man . '",
                        "' . $_bank_account[$i] . '",
                        "' . $bBrand . '",
                        "' . $bCategory . '",
                        "3",
                        "' . $_SESSION['member_id'] . '",
                        "' . $bVersion . '",
                        "' . $formId . '",
                        "' . $showNo['id'] . '"
                    ) ;
            ';
            $conn->Execute($sql_insert);

            $msg .= $_bank_account[$i] . ";";
            $Slist[$j++] = $_bank_account[$i];
        }

        if (! empty($Snum) && ! empty($_POST['aId'])) {
            $bankRegister->applyBabkCode($_POST['aId'], $showNo['id'], $man, $bBrand, $bCategory, '3', $escrowBank, $Snum, $_SESSION['member_id']);
        }

        // 發送申請完成的彙總通知
        // $applications = [];
        // if ($Lnum > 0) {
        //     $applications[] = ['type' => 1, 'num' => $Lnum];
        // }
        // if ($Bnum > 0) {
        //     $applications[] = ['type' => 2, 'num' => $Bnum];
        // }
        // if ($Snum > 0) {
        //     $applications[] = ['type' => 3, 'num' => $Snum];
        // }

        // if (! empty($applications)) {
        //     $bankRegister->sendApplySummaryNotification($man, $bBrand, $bCategory, $escrowBank, $applications, $_SESSION['member_id']);
        // }

        //名稱,帳號,IP,時間,地政士,KEY,保號
        write_log($man . ',' . $msg . ',', 'create_code');
    }

    function addForm($sId, $cat, $count, $brand, $category)
    {
        global $conn;

        $sql = "INSERT INTO
				tBankCodeForm
			SET
				bSID = '" . $sId . "',
				bApplication = '" . $cat . "',
				bBrand = '" . $brand . "',
				bCategory = '" . $category . "',
				bCount = '" . $count . "',
				bApplicant = '" . $_SESSION['member_id'] . "',
				bDate = '" . date('Y-m-d') . "',
				bEditor = '" . $_SESSION['member_id'] . "',
				bEditeTime = '" . date('Y-m-d H:i:s') . "'
		";
        $conn->Execute($sql);

        return $conn->Insert_ID();
    }

    function addForm2($sId, $count, $brand, $category, $bank, $type)
    {
        global $conn;

        //流水號yyyymmdd-000
        $sql = "SELECT bId FROM tBankCodeForm2 WHERE bDate = '" . date('Y-m-d') . "'";
        $rs  = $conn->Execute($sql);
        $max = $rs->RecordCount() + 1;
        $no  = date('Ymd') . "-" . str_pad($max, 3, 0, STR_PAD_LEFT);

        //20211101 預售屋顯示
        $sql = "INSERT INTO
				tBankCodeForm2
			SET

				bSID = '" . $sId . "',
				bNo = '" . $no . "',
				bCount = '" . $count . "',
				bBank = '" . $bank . "',
				bBrand = '" . $brand . "',
				bCategory = '" . $category . "',
				bApplicant = '" . $_SESSION['member_id'] . "',
				bDate = '" . date('Y-m-d') . "',
				bEditor = '" . $_SESSION['member_id'] . "',
				bEditeTime = '" . date('Y-m-d H:i:s') . "'
		";
        $conn->Execute($sql);

        $arrNo['code'] = $no;
        $arrNo['id']   = $conn->Insert_ID();

        return $arrNo;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<title>申請專屬帳號</title>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<!-- <script type="text/javascript" src='codebase/message.js'></script> -->
<script type="text/javascript" src='../js/combobox.js'></script>
<!-- <link rel="stylesheet" type="text/css" href="codebase/themes/message_default.css"> -->
<style>
.ui-combobox {
		position: relative;
		display: inline-block;
}
.ui-combobox-toggle {
		position: absolute;
		top: 0;
		bottom: 0;
		margin-left: -1px;
		padding: 0;
		/* adjust styles for IE 6/7 */
		*height: 1.5em;
		*top: 0.1em;
}
.ui-combobox-input {
	margin: 0;
	padding: 0.1em;
}
.ui-autocomplete-input {
	width:120px;
}
.ui-autocomplete {
	width:150px;
	max-height: 150px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 20px;
}

/* IE 6 doesn't support max-height
 * we use height instead, but this forces the menu to always be this tall
 */
* html .ui-autocomplete {
	height: 100px;
}

#showB td {
	padding-left: 5px;
}
</style>
<script>
$(document).ready(function() {
	window.resizeTo(1100,550) ;

	$.widget( "ui.combobox", {
                _create: function() {
                    var input,
                        self = this,
                        select = this.element.hide(),
                        selected = select.children( ":selected" ),
                        value = selected.val() ? selected.text() : "",
                        wrapper = this.wrapper = $( "<span>" )
                            .addClass( "ui-combobox" )
                            .insertAfter( select );

                    input = $( "<input>" )
                        .appendTo( wrapper )
                        .val( value )
                        .addClass( "ui-state-default ui-combobox-input" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: function( request, response ) {
                                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                response( select.children( "option" ).map(function() {
                                    var text = $( this ).text();
                                    if ( this.value && ( !request.term || matcher.test(text) ) )
                                        return {
                                            label: text.replace(
                                                new RegExp(
                                                    "(?![^&;]+;)(?!<[^<>]*)(" +
                                                    $.ui.autocomplete.escapeRegex(request.term) +
                                                    ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                ), "<strong>$1</strong>" ),
                                            value: text,
                                            option: this
                                        };
                                }) );
                            },
                            select: function( event, ui ) {
                                ui.item.option.selected = true;
                                self._trigger( "selected", event, {
                                    item: ui.item.option
                                });

                                bankval();
                                showQ();
                            },
                            change: function( event, ui ) {
                                if ( !ui.item ) {
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                        valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
                                            return false;
                                        }
                                    });
                                    if ( !valid ) {
                                        // remove invalid value, as it didn't match anything
                                        $( this ).val( "" );
                                        select.val( "" );
                                        input.data( "autocomplete" ).term = "";
                                        return false;
                                    }
                                }
                            }
                        })
                        .addClass( "ui-widget ui-widget-content ui-corner-left" );

                    input.data( "autocomplete" )._renderItem = function( ul, item ) {
                        return $( "<li></li>" )
                            .data( "item.autocomplete", item )
                            .append( "<a>" + item.label + "</a>" )
                            .appendTo( ul );
                    };

                    $( "<a>" )
                        .attr( "tabIndex", -1 )
                        .attr( "title", "Show All Items" )
                        .appendTo( wrapper )
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ui-corner-right ui-combobox-toggle" )
                        .click(function() {
                            // close if already visible
                            if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                                input.autocomplete( "close" );
                                return;
                            }

                            // work around a bug (likely same cause as #5265)
                            $( this ).blur();

                            // pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                            input.focus();
                        });
                },

                destroy: function() {
                    this.wrapper.remove();
                    this.element.show();
                    $.Widget.prototype.destroy.call( this );
                }
            });

	$( "#Cbank" ).combobox() ;
	$( "#bBrand" ).combobox() ;

	 $.widget( "ui.combobox", {
                _create: function() {
                    var input,
                        self = this,
                        select = this.element.hide(),
                        selected = select.children( ":selected" ),
                        value = selected.val() ? selected.text() : "",
                        wrapper = this.wrapper = $( "<span>" )
                            .addClass( "ui-combobox" )
                            .insertAfter( select );

                    input = $( "<input>" )
                        .appendTo( wrapper )
                        .val( value )
                        .addClass( "ui-state-default ui-combobox-input" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: function( request, response ) {
                                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                response( select.children( "option" ).map(function() {
                                    var text = $( this ).text();
                                    if ( this.value && ( !request.term || matcher.test(text) ) )
                                        return {
                                            label: text.replace(
                                                new RegExp(
                                                    "(?![^&;]+;)(?!<[^<>]*)(" +
                                                    $.ui.autocomplete.escapeRegex(request.term) +
                                                    ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                ), "<strong>$1</strong>" ),
                                            value: text,
                                            option: this
                                        };
                                }) );
                            },
                            select: function( event, ui ) {
                                ui.item.option.selected = true;
                                self._trigger( "selected", event, {
                                    item: ui.item.option
                                });
                              CatchScrivener() ;
                              showQ();
                            },
                            change: function( event, ui ) {
                                if ( !ui.item ) {
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                        valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
                                            return false;
                                        }
                                    });
                                    if ( !valid ) {
                                        // remove invalid value, as it didn't match anything
                                        $( this ).val( "" );
                                        select.val( "" );
                                        input.data( "autocomplete" ).term = "";
                                        return false;
                                    }
                                }
                            }
                        })
                        .addClass( "ui-widget ui-widget-content ui-corner-left" );

                    input.data( "autocomplete" )._renderItem = function( ul, item ) {
                        return $( "<li></li>" )
                            .data( "item.autocomplete", item )
                            .append( "<a>" + item.label + "</a>" )
                            .appendTo( ul );
                    };

                    $( "<a>" )
                        .attr( "tabIndex", -1 )
                        .attr( "title", "Show All Items" )
                        .appendTo( wrapper )
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ui-corner-right ui-combobox-toggle" )
                        .click(function() {
                            // close if already visible
                            if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                                input.autocomplete( "close" );
                                return;
                            }

                            // work around a bug (likely same cause as #5265)
                            $( this ).blur();

                            // pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                            input.focus();
                        });
                },

                destroy: function() {
                    this.wrapper.remove();
                    this.element.show();
                    $.Widget.prototype.destroy.call( this );
                }
            });

	$("#sinopac").hide();
	$( "#man" ).combobox() ;

	$("[name='button']").on('click', function() {
        let va = $("#Cbank").val();
        let va2 = $("[name='bankB']:checked").val();
        let b_brand = $('[name="bBrand"] :selected').val();
        let b_num = $('[name="Bnum"]').val();

		if (va == 4 && va2 == undefined) {
			alert('請選擇分行');
		}else{
            let sid = $('#man option:selected').val() ;

            if (va == 1) {
                alert('合約書已用盡！請洽詢分機 127');
                return false;
            }

            $("[name='button']").attr("disabled", true);
            $("#form1").submit();
		}
	});

	$("[name='bankB']").click(function() {
		$('[name="bank"]').val($(this).val());
		showQ();
	});
});

function bankval() {
	let va = $("#Cbank").val();
	let sid = $('#man option:selected').val() ;
	$('[name="bank"]').val(va);
	$( "#bBrand" ).combobox("destroy");
	$.post('create_check.php', {'sid': sid,'bank':va}, function(txt) {
		$( "#show" ).html(txt);
		$.widget( "ui.combobox", {
                _create: function() {
                    var input,
                        self = this,
                        select = this.element.hide(),
                        selected = select.children( ":selected" ),
                        value = selected.val() ? selected.text() : "",
                        wrapper = this.wrapper = $( "<span>" )
                            .addClass( "ui-combobox" )
                            .insertAfter( select );

                    input = $( "<input>" )
                        .appendTo( wrapper )
                        .val( value )
                        .addClass( "ui-state-default ui-combobox-input" )
                        .autocomplete({
                            delay: 0,
                            minLength: 0,
                            source: function( request, response ) {
                                var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                response( select.children( "option" ).map(function() {
                                    var text = $( this ).text();
                                    if ( this.value && ( !request.term || matcher.test(text) ) )
                                        return {
                                            label: text.replace(
                                                new RegExp(
                                                    "(?![^&;]+;)(?!<[^<>]*)(" +
                                                    $.ui.autocomplete.escapeRegex(request.term) +
                                                    ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                ), "<strong>$1</strong>" ),
                                            value: text,
                                            option: this
                                        };
                                }) );
                            },
                            select: function( event, ui ) {
                                ui.item.option.selected = true;
                                self._trigger( "selected", event, {
                                    item: ui.item.option
                                });
                                showCategory();
                            },
                            change: function( event, ui ) {
                                if ( !ui.item ) {
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                        valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
                                            return false;
                                        }
                                    });
                                    if ( !valid ) {
                                        // remove invalid value, as it didn't match anything
                                        $( this ).val( "" );
                                        select.val( "" );
                                        input.data( "autocomplete" ).term = "";
                                        return false;
                                    }
                                }
                                disableSnum();
                            }
                        })
                        .addClass( "ui-widget ui-widget-content ui-corner-left" );

                    input.data( "autocomplete" )._renderItem = function( ul, item ) {
                        return $( "<li></li>" )
                            .data( "item.autocomplete", item )
                            .append( "<a>" + item.label + "</a>" )
                            .appendTo( ul );
                    };

                    $( "<a>" )
                        .attr( "tabIndex", -1 )
                        .attr( "title", "Show All Items" )
                        .appendTo( wrapper )
                        .button({
                            icons: {
                                primary: "ui-icon-triangle-1-s"
                            },
                            text: false
                        })
                        .removeClass( "ui-corner-all" )
                        .addClass( "ui-corner-right ui-combobox-toggle" )
                        .click(function() {
                            // close if already visible
                            if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                                input.autocomplete( "close" );
                                return;
                            }

                            // work around a bug (likely same cause as #5265)
                            $( this ).blur();

                            // pass empty string as value to search for, displaying all results
                            input.autocomplete( "search", "" );
                            input.focus();
                        });
                },

                destroy: function() {
                    this.wrapper.remove();
                    this.element.show();
                    $.Widget.prototype.destroy.call( this );
                }
            });

		$( "#bBrand" ).combobox();
	});

	if (va == 4) {
		$("#sinopac").show();
	}else{
		$("#sinopac").hide();
	}
    if (va == 1 || va == 7) {
        $('#presale').hide();
    } else {
        $('#presale').show();
    }
}

function CatchScrivener(){
	let sid = $('#man option:selected').val() ;

	$.ajax({
		url: 'create_bank.php',
		type: 'POST',
		dataType: 'html',
		data: {sid: sid},
	}).done(function(html) {
		$("#Cbank option").remove();
		$("#Cbank").html(html);
		// $( "#Cbank" ).combobox('destroy').eq(0).prop('selected', true).combobox();

		bankval();
	});
}

function showQ(){
    setTimeout(function() {
        let scr = $("#man").val();
        let bank = $('[name="bank"]').val();

        $.ajax({
            url: 'bankCodeCount.php',
            type: 'POST',
            dataType: 'html',
            data: {"id": scr,"bank":bank},
        }).done(function(txt) {
            $("#showB").html(txt);
        });
    }, 500);
}

function showCategory(){
	let bank = $("#Cbank").val();
	let brand = $("[name='bBrand']").val();

	if (bank == 5 && brand == 72) {
		$('.brand72').hide();
	}else{
		$('.brand72').show();
	}

    $('#presale').hide();
    $('[name="Snum"]').val('');
    if (brand == 2) {
        $('#presale').show();
    }
    if(bank == 1 || bank == 7) {
        $('#presale').hide();
    }
}

function disableSnum(){
    if($('#bBrand').val() == 1) {
        $("input[name='Snum']").prop("disabled", true);
    } else {
        $("input[name='Snum']").prop("disabled", false);
    }
}
</script>
</head>

<body>
<center>
<?php
    $_SESSION['key'] = mt_rand(1, 1000);
?>
<?php if ($_total == 0) {?>
<form id="form1" name="form1" method="post" action="">
<input type="hidden" name="key" value="<?php echo $_SESSION['key'] ?>" />
  <table width="960" border="0" cellpadding="1" cellspacing="1" style="border:1px dotted #CCCCCC;">
    <tr>
      <td width="320">地政士：
        <label for="man"></label>
        <?php
            $sql = "select * from tScrivener WHERE sStatus=1 ORDER BY sId";
                $rs1 = $conn->Execute($sql);

            ?>
        <select name="man" id="man" >
			<option></option>
        <?php while (! $rs1->EOF) {?>
			<option value="<?php echo $rs1->fields["sId"]; ?>"><?php echo 'SC' . str_pad($rs1->fields["sId"], 4, 0, STR_PAD_LEFT) . $rs1->fields["sName"]; ?></option>
        <?php
            $rs1->MoveNext();}
            ?>
		</select>
	  </td>
      <td width="320">
		銀行別：
		<label for="bank"></label>
		<input type="hidden" name="bank" value="" />
		<select  id="Cbank">
			<option>請選擇</option>
		</select>
	  </td>
      <td width="320">
		合約版本：
		<label for="bBrand"></label>
		<span id="show"><select name="bBrand" id="bBrand"><option>請選擇</option></span>

	  </td>
	</tr>
  </table>
  <div style="height:20px;"></div>
  <table width="660" border="0" cellpadding="1" cellspacing="1" style="border:1px dotted #CCCCCC;">
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr id="sinopac">
		<td width="132" style="text-align:right;">分行別：</td>
		<td >
			<input type="radio" name="bankB" value="4"  />西門分行
		</td>
		<td colspan="3">
			<input type="radio" name="bankB" value="6" />城中分行
		</td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;<input type="hidden" name="ver" id="" value="A" /></td>
    </tr>
	<tr>
		<td width="150" style="text-align:right;">申請數量：</td>
		<td colspan="4">
			<span class="brand72">土地
				<input type="text" style="width:60px;" name="Lnum" value="">
				組
			</span>

			建物
			<input type="text" style="width:60px;" name="Bnum" value="">
			組
			<span class="brand72" id="presale">
				預售屋
				<input type="text" style="width:60px;" name="Snum" value="">
				組
			</span>
		</td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5" style="text-align:center;">
		<input name="save" type="hidden" id="save" value="ok" />
		<input type="button" name="button" id="button" value="確定" />
		<input type="reset" name="button2" id="button2" value="取消" />
	  </td>
    </tr>
	<tr>
		<td colspan="5">&nbsp;</td>
    </tr>
  </table>
</form>
<?php }?>
<p>
  <?php
  ?>
</p>
<div id="showB">

</div>
<?php
    if ($_total > 0) {
        $_GET['no'] = $showNo['code'];
        include_once 'form.php';

    ?><br />
<br />
<br /><br /><br /><br /><br />
<span style="text-align:right;">【<a href="create.php?ts=<?php echo mktime() ?>">回上一頁</a>】</span>
<?php }?>
<p>&nbsp;</p>
</center>
<script>
    $(document).ready(function() {
        //預設值合約是台屋就disabled
        $("input[name='Snum']").on('click', function (e){
            if($('#bBrand').val() == 1) {
                $("input[name='Snum']").prop("disabled", true);
            } else {
                $("input[name='Snum']").prop("disabled", false);
            }
        });
    });
</script>
</body>
</html>
