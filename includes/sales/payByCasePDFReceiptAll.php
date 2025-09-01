<?php
require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
require_once dirname(dirname(__DIR__)) . '/class/payByCase/payByCase.class.php';
require_once dirname(dirname(__DIR__)) . '/class/traits/CharactorWidthConvert.traits.php';
require_once dirname(dirname(__DIR__)) . '/checklist/fpdf/chinese-unicode.php';
require_once dirname(dirname(__DIR__)) . '/openadodb.php';

//設定線條為實、虛線
class PDF1 extends PDF_Unicode
{
    use First1\V1\Util\CharactorWidthConvert;

    public function SetDash($black = false, $white = false)
    {
        if ($black and $white) {
            $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
        } else {
            $s = '[] 0 d';
        }

        $this->_out($s);
    }
}
##

//取得買賣方資訊
function getBuyerOwner($cCertifiedId, $target)
{
    global $conn;

    $name  = [];
    $table = '';

    if ($target == 'B') {
        $target = 1;
        $table  = 'tContractBuyer';
    } else if ($target == 'O') {
        $target = 2;
        $table  = 'tContractOwner';
    } else {
        throw new \Exception('Invaild buyer/owner target');
    }

    $sql = 'SELECT cName FROM ' . $table . ' WHERE cCertifiedId  = "' . $cCertifiedId . '"
                    UNION
                SELECT cName FROM tContractOthers WHERE cCertifiedId = "' . $cCertifiedId . '" AND cIdentity = "' . $target . '";';
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        $name[] = $rs->fields['cName'];
        $rs->MoveNext();
    }

    return implode(',', $name);
}
##

//取得負責經辦資訊
function getUndertaker($sId)
{
    global $conn;

    if (!preg_match("/^\d+$/", $sId)) {
        throw new \Exception('Invalid scrivener id');
    }

    $staff = [
        'Ext'    => '',
        'FaxNum' => '',
    ];

    $sql = 'SELECT
                b.pExt AS Ext,
                b.pFaxNum AS FaxNum
            FROM
                tScrivener AS a
            JOIN
                tPeopleInfo AS b ON a.sUndertaker1 = b.pId
            WHERE a.sId = ' . $sId . ';';
    $rs = $conn->Execute($sql);
    if (!$rs->EOF) {
        $staff['Ext']    = $rs->fields['Ext'];
        $staff['FaxNum'] = $rs->fields['FaxNum'];

        $rs->MoveNext();
    }

    return $staff;
}
##

$pdf = new PDF1(); // 建立 FPDF

$pdf->SetAuthor('First'); // 設定作者
$pdf->SetAutoPageBreak(1, 2); // 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(10, 8, 10); // 設定顯示邊界 (左、上、右)
$pdf->SetTitle('回饋金報表', true);
$pdf->AddUniCNShwFont('Uni'); // 設定為 UTF-8 顯示輸出
$pdf->SetFont("Uni");

foreach ($cIds as $key => $cCertifiedId) {
    $targetId = $fTargetIds[$key];
    require __DIR__ . '/payByCasePDFReceiptData.php'; // 取得資料
}

$pdf->Output();
