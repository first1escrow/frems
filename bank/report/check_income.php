<?php
require_once dirname(dirname(__DIR__)) . '/openadodb.php';
require_once dirname(dirname(__DIR__)) . '/session_check.php';

$sql = "SELECT * FROM tExpense WHERE eStatusIncome = '1' AND ePayTitle NOT LIKE '%網路整批%' AND eDepAccount != '0000000000000000';";
$rs  = $conn->Execute($sql);
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Untitled Document</title>
</head>

<body>
    (所有銀行)
    <table width="376" border="1">
        <tr>
            <td width="109">保證號碼</td>
            <td width="87">存入</td>
            <td width="75">支出</td>
            <td width="77">狀態</td>
        </tr>
        <?php
$i = 1;
while (!$rs->EOF) {
    $_account = substr($rs->fields["eDepAccount"], 2);

    $sql = "SELECT tContractCase.cCertifiedId FROM tContractCase WHERE cEscrowBankAccount = '$_account';";
    $rs1 = $conn->Execute($sql);
    $_t  = $rs1->RecordCount();

    $_status = ($_t == 0) ? '未建檔！' : '';
    $_money1 = (int) substr($rs->fields["eLender"], 0, -2);
    $_money2 = (int) substr($rs->fields["eDebit"], 0, -2);
    $_total1 += $_money1;
    if ($_status == '未建檔！') {
        $_total1_1 = $_total1_1 + $_money1;
    }
    $_total2 += $_money2;
    ?>
        <tr>
            <td>&nbsp;<?php echo $_account; ?></td>
            <td>&nbsp;<?php echo $_money1; ?></td>
            <td>&nbsp;<?php echo $_money2; ?></td>
            <td>&nbsp;<?php echo $_status; ?></td>
        </tr>
        <?php
$rs1 = null;unset($rs1);

    $rs->MoveNext();
    $i++;
}
?>
    </table>
    <p> 共計 <?php echo number_format($i - 1); ?>筆
        <?php if ($_total1 != 0) {echo number_format($_total1);} else if ($_total2 != 0) {echo number_format($_total2);}?>
        / 未建檔共計 <?php echo number_format($_total1_1); ?>。</p>
</body>

</html>