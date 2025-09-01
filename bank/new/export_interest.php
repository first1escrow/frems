<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/web_addr.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$bank_code = $_POST["bank_code"];
$save      = $_POST["save"];

//若無預設銀行，則指定預設銀行為"永豐西門"
if (!$bank_code) {
    $bank_code = 4;
}
##

//合約銀行基本資料
$sql = 'SELECT * FROM tContractBank WHERE cShow="1" ORDER BY cId ASC;';
$rs  = $conn->Execute($sql);
while (!$rs->EOF) {
    $conBank[] = $rs->fields;

    if ($rs->fields['cId'] == $bank_code) {
        $cBank = $rs->fields; //指定的合約銀行
    }

    $rs->MoveNext();
}
$rs = null;unset($rs);
##

//利息打包出款動作
if ($save == 'ok') {
    $sql    = 'SELECT * FROM tContractBank WHERE cBankAccount="' . $_POST['bankAccount'] . '" LIMIT 1;';
    $rs     = $conn->Execute($sql);
    $saving = $rs->fields;
    $rs     = null;unset($rs);

    if (!in_array($_POST['bank_code'], [1, 7])) { //不是一銀
        $tId        = $_POST['tId'];
        $targetBank = $_POST['bankAccount'];

        $bank_kind = $saving['cBankName'];

        //取得利息資料
        $sql = 'SELECT * FROM tExpense WHERE id="' . $tId . '" ;';
        $rsT = $conn->Execute($sql);

        $_expense_acc = $rsT->fields['eAccount']; // 取得利息紀錄的信託帳號
        $_total_money = substr($rsT->fields['eLender'], 0, -2) + 1 - 1; // 取得利息金額
        ##

        //取得對應利息之所得稅資料
        $_tax_date = $rsT->fields['eTradeDate'];
        if ($targetBank != '20680100151828') {
            $sql  = 'SELECT * FROM tExpense WHERE eTradeDate="' . $_tax_date . '" AND eTradeCode="1920" AND eBank ="' . $rsT->fields['eBank'] . '" ;'; //只算同一個銀行20160621    // 1920 = 所得稅
            $rsT1 = $conn->Execute($sql);

            if ($rsT1->RecordCount() > 0) {
                $_total_money -= substr($rsT1->fields['eDebit'], 0, -2) + 1 - 1; // 將利息金額減去所得稅
            }
        }
        ##

        $tkind    = '利息'; // 匯出類別(自編)
        $tcode    = '06'; // 交易類別(06、利息)
        $tobjkind = '利息匯出'; // 匯出科目(自編)

        $tbankcode    = $saving['cBankMain'] . $saving['cBankBranch']; // 收款銀行號碼
        $taccount     = $saving['cBankAccount']; // 活儲帳號
        $taccountname = $saving['cAccountName']; // 收款人戶名

        //20221221 因永豐有分行問題，因此調整虛擬帳號取得方式
        $sql          = 'SELECT cBankVR, cBankAlias FROM tContractBank WHERE cBankTrustAccount = "' . $rsT->fields['eAccount'] . '";';
        $expense_bank = $conn->Execute($sql)->fields;

        $vr_code = $expense_bank['cBankVR'];
        if ($expense_bank['cBankAlias'] == 'taishin') {
            $vr_code .= '000000008'; // 台新銀行利息帳戶
        } else {
            $vr_code = str_pad($vr_code, 14, '0', STR_PAD_RIGHT); // 其他銀行利息帳戶
        }
        $expense_bank = null;unset($expense_bank);
        ##

        $_email = '';
        $_fax   = '';

        $ttxt = '利息';
        if ($bank_kind == '永豐') {
            if ($bank_code == 4) {
                $ttxt = '西門利息';
            } else if ($bank_code == 6) {
                $ttxt = '城中利息';
            }
        }

        if ($_total_money > 0) {
            $sql = '
				INSERT INTO tBankTrans
					(tVR_Code,tBank_kind,tKind,tCode,tObjKind,tBankCode,tAccount,tAccountName,tMoney,tChangeExpense,tMemo,tTxt,tFax,tEmail,tOwner)
				VALUES
					("' . $vr_code . '","' . $bank_kind . '","' . $tkind . '","' . $tcode . '","' . $tobjkind . '","' . $tbankcode . '","' . $taccount . '","' . $taccountname . '",
					"' . $_total_money . '","' . $tId . '","' . substr($vr_code, -9) . '","' . $ttxt . '","' . $_fax . '","' . $_email . '","' . $_SESSION['member_name'] . '") ;
			';
            $conn->Execute($sql);

            if ($targetBank == '20680100151828') {
                $sql = '
					UPDATE
						tExpense
					SET
						eClose="1"
					WHERE
						eTradeCode="1912"
						AND eTradeDate="' . $_tax_date . '"
						AND eAccount="' . $_expense_acc . '"
				';
            } else {
                $sql = '
					UPDATE
						tExpense
					SET
						eClose="1"
					WHERE
						eTradeCode IN (1912,1920)
						AND eTradeDate="' . $_tax_date . '"
						AND eAccount="' . $_expense_acc . '"
				';
            }
            $conn->Execute($sql);

            echo '
			<script>
				alert("建檔完成!!") ;
			</script>
			';
        }
        ##
    } else {
        //一銀
        $sql = "UPDATE tExpense SET eClose = 1 WHERE id = '" . $_POST['tId'] . "'";
        $conn->Execute($sql);

        $sql = "SELECT eLender, eTradeDate FROM tExpense WHERE id = '" . $_POST['tId'] . "'";
        $rs  = $conn->Execute($sql);

        $money = (int) substr($rs->fields['eLender'], 0, -2);
        $_bank = ($_POST['bank_code'] == 1) ? '' : 7;
        $tax   = getBankIntTax($rs->fields['eTradeDate'], $_bank);
        $money = $money - $tax;

        $_bank = null;unset($_bank);

        $sql = "INSERT INTO
					tBankTrankBook
				SET
					bCertifiedId = '" . $saving['cBankVR'] . '000000000' . "',
					bStatus = 0,
					bBank = " . $_POST['bank_code'] . ",
					bCategory = 13,
					bMoney = '" . $money . "',
					bCreatorId = '" . $_SESSION['member_id'] . "',
					bCreatName = '" . $_SESSION['member_name'] . "',
					bCreatTime = '" . date('Y-m-d H:i:s') . "'";
        $conn->Execute($sql);
        $id = $conn->Insert_ID();

        header("Location:../instructions/IBookEdit.php?id=" . $id);
        exit;
    }
}
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>利息出款</title>
<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.21.custom.min.js"></script>
<link rel="stylesheet" href="../colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="../js/jquery.colorbox.js"></script>
<script>
function check_form() {
	var tid = $('[name="tId"]:checked').val() ;

	if (!tid) {
		alert('請選擇欲出款的利息...') ;
		event.returnValue = false ;
	}
}

function sync_bank() {
	var bk = $('select[name="bank_code"] option:selected').val() ;

	if (bk == '台新') {
		$('#taishin_tag').prop('checked',true) ;
	} else {
		$('#sinopac_tag').prop('checked',true) ;
	}
}

function chg_bank() {
	var str = $('select[name="bank_code"] option:selected').val() ;

	$('#form2').val(str) ;
	$('[name="form2"]').submit() ;
}
</script>
<style>
.font9 {
	font-size: 9pt;
}
</style>
</head>

<body>
<div style="width:1290px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <font color=red><strong>利息出款</strong></font> </div>
</div>
<form name="form2" method="post">
<input type="hidden" id="form2" name="bank_code">
</form>

<form id="form1" name="form1" method="post" onsubmit="check_form()">
選擇銀行：
<select id="bk" name="bank_code" onchange="chg_bank()">
<?php
for ($i = 0; $i < count($conBank); $i++) {
    $cb = $conBank[$i]['cBankName'] . $conBank[$i]['cBranchName'];

    echo '
		<option value="' . $conBank[$i]['cId'] . '"';

    if ($bank_code == $conBank[$i]['cId']) {
        echo ' selected="selected"';
    }

    echo '>' . $cb . "</option>\n";
}
?>
</select>
<div>
<table border="0">
<tr>
<td width="300px;" valign="top">
	<ul style="list-style-type:none;">
<?php
//當為台新帳戶時，指定利息帳號
$acc = '';
if ($cBank['cBankAlias'] == 'taishin') {
    $acc = ' AND eDepAccount="0096988000000008" ';
}

if (in_array($bank_code, [1, 7])) {
    $str = ' AND (eTradeCode="1912" OR ePayTitle ="活存息")';
} else {
    $str = ' AND eTradeCode="1912"';
}
##

//取出所屬銀行利息資訊列表
$sql = '
	SELECT
		*
	FROM
		tExpense
	WHERE
		eClose="2"
		AND eAccount="' . $cBank['cBankTrustAccount'] . '"
		' . $str . '
		' . $acc . '
	ORDER BY
		eTradeDate
	DESC ;
';
$rs = $conn->Execute($sql);

$cIndex = 0;
while (!$rs->EOF) {
    if (!preg_match("/利息/", $rs->fields['ePayTitle'])) {
        $rs->fields['ePayTitle'] = '利息存入';
    }

    if ($rs->fields['eTradeDate']) {
        $tmp[0]                   = substr($rs->fields['eTradeDate'], 0, 3);
        $tmp[1]                   = substr($rs->fields['eTradeDate'], 3, 2);
        $tmp[2]                   = substr($rs->fields['eTradeDate'], 5);
        $rs->fields['eTradeDate'] = $tmp[0] . '/' . $tmp[1] . '/' . $tmp[2];
        unset($tmp);
    }

    if ($c_index == 0) {
        $chk = ' checked="checked"';
    } else {
        $chk = '';
    }

    $rs->fields['eAccount'] = $rs->fields['eTradeDate'] . ' ' . $rs->fields['ePayTitle'];
    echo '	<li><input type="radio" name="tId"' . $chk . ' value="' . $rs->fields['id'] . '">' . $rs->fields['eAccount'] . '</li>' . "\n";

    $c_index++;

    $rs->MoveNext();
}
##

function getBankIntTax($date, $bank = '')
{
    global $conn;

    $money = 0;

    $sql = "SELECT eDebit FROM tExpense WHERE ePayTitle = '所得稅' AND eBank = '" . $bank . "' AND eTradeDate = '" . $date . "'";
    $rs  = $conn->Execute($sql);
    while (!$rs->EOF) {
        $money += (int) substr($rs->fields['eDebit'], 0, -2);
        $rs->MoveNext();
    }

    return $money;
}
?>
	</ul>
</td>
<td width="300px;" valign="top">
	<?php if (!in_array($_POST['bank_code'], [1, 7])): ?>

	<ul style="list-style-type:none;">
		<li><input type="radio" id="sinopac_tag" name="bankAccount" <?php if (($bank_code == '4') || ($bank_code == '6')) {
    echo 'checked="checked" ';
}
?>value="10401810001889">匯入永豐銀行</li>
		<li><input type="radio" id="taishin_tag" name="bankAccount" <?php if ($bank_code == '5') {
    echo 'checked="checked" ';
}
?>value="20680100151828">匯入台新銀行</li>
	</ul>
	<?php endif?>
</td>
</tr>
</table>
</div>
<input type="hidden" name="save" value="ok">
<input type="submit" value="匯出">
</form>

</body>
</html>
