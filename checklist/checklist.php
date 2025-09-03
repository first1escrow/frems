<?php
    require_once dirname(__DIR__) . '/openadodb.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/tracelog.php';
    require_once dirname(__DIR__) . '/class/payByCase/payByCase.class.php';

    use First1\V1\PayByCase\PayByCase;

    $paybycase = new PayByCase;
    $company   = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/includes/company.json'), true);

    $cCertifiedId = $_REQUEST['cCertifiedId'];
    $ok           = ''; // 初始化隱藏欄位變數
                        //$cCertifiedId = '010318021' ;
                        //$cCertifiedId = '010315621' ;

    $tlog = new TraceLog();
    $tlog->selectWrite($_SESSION['member_id'], $cCertifiedId, '顯示點交表');

    $last_modify = date("Ymd.His");

    ###############################檢查返還###########################
    $checkReturn = 0;
    $sql         = "SELECT tObjKind2Item FROM tBankTrans WHERE tObjKind2 = '01' AND tObjKind2Item = '' AND tMemo= '" . $cCertifiedId . "'";
    $rs          = $conn->Execute($sql);
    $checkReturn = $rs->RecordCount();

    $sql = "SELECT tPayOk FROM tBankTrans WHERE tObjKind2 = '02' AND tMemo= '" . $cCertifiedId . "'";
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF && $rs->fields['tPayOk'] == 2) {
        $checkReturn++;
    }
    $tmp = null;unset($tmp);
    ##

    //======================= 買賣方 ===================================
    //讀取資料庫

    //讀取買賣方明細資料
    $sql    = 'SELECT * FROM tChecklist WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs     = $conn->Execute($sql);
    $detail = ($rs && ! $rs->EOF) ? $rs->fields : [];
    ##

    $checkSellerServiceFee = 0;

    //沒有出款賣方服務費記錄的案件,儲存點交單的時候要提醒"無服務費出款記錄"(非仲介成交的不用提醒)
    if ($detail['bBrand'] != '非仲介成交' && $detail['bBrand'] != '非仲介成交') {
        $sql                   = "SELECT tId FROM tBankTrans WHERE tMemo = '" . $cCertifiedId . "' AND (tTxt LIKE '%服務費%' AND tTxt LIKE '%賣方%' OR tSeller > 0)";
        $rs                    = $conn->Execute($sql);
        $checkSellerServiceFee = $rs->RecordCount();
    }
    ###

    //讀取扣繳憑單對象
    $sql                         = 'SELECT cTaxReceiptTarget FROM tContractInvoice WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs                          = $conn->Execute($sql);
    $detail['cTaxReceiptTarget'] = ($rs && ! $rs->EOF) ? $rs->fields['cTaxReceiptTarget'] : '';
    $tmp                         = null;unset($tmp);
    ##

    //讀取前台點交表紀錄
    $sql      = 'SELECT tId FROM tUploadFile WHERE tCertifiedId="' . $cCertifiedId . '";';
    $rs       = $conn->Execute($sql);
    $btn_str  = '發布';
    $front_ok = (! $rs->EOF) ? 1 : 0;
    ##

    //讀取賣方交易明細##日期為空的要排最後面
    $owner_income = $owner_expense = [];
    $ArrIn        = $ArrIn2        = $ArrExp        = $ArrExp2        = [];

    $sql = 'SELECT * FROM tChecklistOlist WHERE oCertifiedId="' . $cCertifiedId . '" ORDER BY oDate,oId,oKind ASC;'; //AND oIncome<>"0" AND oDate!=""
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        if ($rs->fields['oIncome'] != "0") {
            if ($rs->fields['oDate'] != '') {
                $ArrIn[] = $rs->fields;
            } else {
                $ArrIn2[] = $rs->fields;
            }
        } elseif ($rs->fields['oExpense'] != "0") {
            if ($rs->fields['oDate'] != '') {
                $ArrExp[] = $rs->fields;
            } else {
                $ArrExp2[] = $rs->fields;
            }
        }

        $rs->MoveNext();
    }

    $owner_income  = array_merge($ArrIn, $ArrIn2);
    $owner_expense = array_merge($ArrExp, $ArrExp2);
    ##

    //讀取買方交易明細 ##日期為空的要排最後面
    $buyer_income = $buyer_expense = [];
    $ArrIn        = $ArrIn2        = $ArrExp        = $ArrExp2        = [];

    $sql = 'SELECT * FROM tChecklistBlist WHERE bCertifiedId="' . $cCertifiedId . '" ORDER BY bDate,bId,bKind ASC;'; //AND oIncome<>"0" AND oDate!=""
    $rs  = $conn->Execute($sql);

    while (! $rs->EOF) {
        if ($rs->fields['bIncome'] > 0) {
            if ($rs->fields['bDate'] != '') {
                $ArrIn[] = $rs->fields;
            } else {
                $ArrIn2[] = $rs->fields;
            }
        } elseif ($rs->fields['bExpense'] > 0) {
            if ($rs->fields['bDate'] != '') {
                $ArrExp[] = $rs->fields;
            } else {
                $ArrExp2[] = $rs->fields;
            }
        }

        $rs->MoveNext();
    }

    $buyer_income  = array_merge($ArrIn, $ArrIn2);
    $buyer_expense = array_merge($ArrExp, $ArrExp2);

    $ArrIn = $ArrIn2 = null;
    unset($ArrIn, $ArrIn2);
    ##

    # 讀取經辦人員資料
    $sql = 'SELECT
            peo.pFaxNum as FaxNum,
            peo.pId as pId,
            peo.pExt as Ext
        FROM
            tBankCode AS bkc
        JOIN
            tScrivener AS scr ON scr.sId=bkc.bSID
        JOIN
            tPeopleInfo AS peo ON scr.sUndertaker1=peo.pId
        WHERE
            bkc.bAccount LIKE "%' . $cCertifiedId . '";';
    $rs = $conn->Execute($sql);

    $undertaker = ($rs && ! $rs->EOF) ? $rs->fields : [];
    if (isset($undertaker['FaxNum']) && $undertaker['FaxNum']) {
        $temp                 = $undertaker['FaxNum'];
        $undertaker['FaxNum'] = substr($temp, 0, 7) . '-' . substr($temp, 7);
        $temp                 = null;unset($temp);
    }
    //

    // ================================================================

    $int_money = 0;
    //讀取已分配的利息總金額(買方)
    $sql = 'SELECT cInterestMoney FROM tContractBuyer WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF && isset($rs->fields['cInterestMoney'])) {
        $int_money += floatval($rs->fields['cInterestMoney']);
    }
    ##

    //讀取已分配的利息總金額(賣方)
    $sql = 'SELECT cInterestMoney FROM tContractOwner WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);
    if ($rs && ! $rs->EOF && isset($rs->fields['cInterestMoney'])) {
        $int_money += floatval($rs->fields['cInterestMoney']);
    }
    ##

    //讀取已分配的利息總金額(其他買賣方)
    $sql = 'SELECT cInterestMoney FROM tContractOthers WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);
    while (! $rs->EOF) {
        $int_money += floatval($rs->fields['cInterestMoney']);
        $rs->MoveNext();
    }
    ##

    //讀取已分配的利息總金額(仲介)
    $sql = 'SELECT cInterestMoney,cInterestMoney1,cInterestMoney2,cInterestMoney3 FROM tContractRealestate WHERE cCertifyId="' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);
    $int_money += floatval($rs->fields['cInterestMoney']);
    $int_money += floatval($rs->fields['cInterestMoney1']);
    $int_money += floatval($rs->fields['cInterestMoney2']);
    $int_money += floatval($rs->fields['cInterestMoney3']);
    ##

    //讀取已分配的利息總金額(代書)
    $sql = 'SELECT cInterestMoney FROM tContractScrivener WHERE cCertifiedId="' . $cCertifiedId . '";';
    $rs  = $conn->Execute($sql);
    $int_money += floatval($rs->fields['cInterestMoney']);
    ##

    //檢查是否未收足是否有審核過
    $sql                 = "SELECT cCertifiedMoney,cTotalMoney,cFirstMoney,cStatus,cReasonCategory,cInspetor2 FROM tContractIncome WHERE cCertifiedId = '" . $cCertifiedId . "'";
    $rs                  = $conn->Execute($sql);
    $certifiedMoneyAudit = 1; // 0:不能開點交單 1 :可以開
    $realCertifiedMoney  = round((floatval($rs->fields['cTotalMoney']) - floatval($rs->fields['cFirstMoney'])) * 0.0006);
    $nowCertifiedMoney   = $rs->fields['cCertifiedMoney'];
    $cTotalMoney         = $rs->fields['cTotalMoney'];

    if (($nowCertifiedMoney + 10) < $realCertifiedMoney) { //未收足
                                                               //未收足審核狀態1:業務審核通過、 2 主管審核通過、3主管審核不通過
                                                               //預售屋直接通過
        if ($rs->fields['cInspetor2'] == 0 && $rs->fields['cReasonCategory'] == 0) {
            $certifiedMoneyAudit = 0;
        }
    }
    ##

    #檢查隨案付款業務或會計有沒有審核
    $paybycasePass = 1;
    $sql           = "SELECT `fSalesConfirmDate`, `fAccountantConfirmDate` FROM `tFeedBackMoneyPayByCase` WHERE fCertifiedId = '" . $cCertifiedId . "'";
    $rs            = $conn->Execute($sql);

    if (! $rs->EOF) {
        if (null == $rs->fields['fSalesConfirmDate'] or null == $rs->fields['fAccountantConfirmDate']) {
            $paybycasePass = 0;
        }
    }

    $pg = empty($_REQUEST['pg']) ? 0 : $_REQUEST['pg'];

    /**
     * 20230316 新增屢保費是否足額計算
     */

    //代扣二代健保總額
    $NHITax = 0;
    $NHITax = floatval($detail['cNHITax']) + floatval($detail['bNHITax']); //賣方 + 買方
                                                                           ##

    //代扣稅款總額
    $InterestTax = 0;
    $InterestTax = floatval($detail['cTax']) + floatval($detail['bTax']); //賣方 + 買方
                                                                          ##

    //地政士回饋金
    $feedBackMoney = 0;
    $pay_by_case   = $paybycase->getPayByCase($cCertifiedId);

    $feedBackMoney = empty($pay_by_case['detail']['total']) ? 0 : $pay_by_case['detail']['total']; //金額

    $feedBackNHITax = empty($pay_by_case['fNHI']) ? 0 : $pay_by_case['fNHI'];
    $feedBackTax    = empty($pay_by_case['fTax']) ? 0 : $pay_by_case['fTax'];

    $balance = (floatval($detail['cCertifiedMoney']) + floatval($detail['bCertifiedMoney'])) - floatval($detail['cInterest']) - $feedBackMoney + $NHITax + $InterestTax + $feedBackNHITax + $feedBackTax;
    ##
    //會計是否出款二代健保
    if (isset($pay_by_case['fNHIpay']) && $pay_by_case['fNHIpay'] == 'N') {
        $NHITax = 0;
    }

    //確認是否已確認過案件
    $sql = "SELECT * FROM tFeedBackMoney WHERE fCertifiedId ='" . $cCertifiedId . "' AND fDelete = 0";

    ##
    //確認賣方人數
    $sql   = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='2'";
    $rs    = $conn->Execute($sql);
    $owner = $rs->recordCount();

    $total_owner = $owner + 1;
    ##
    //確認買方人數
    $sql   = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='1'";
    $rs    = $conn->Execute($sql);
    $buyer = $rs->recordCount();
    ##

    //取得確認時間
    $res = $paybycase->getConfirmDate($cCertifiedId);
    ##

    //賣方5人以上(含)，且總價金額低於1000萬(含)時，顯示警告訊息 "請確認合約版本第六條履保費收費方式!"
    $certify_money_notice = '';
    if (($cTotalMoney <= 10000000) && ($total_owner >= 5)) {
        $fh                   = dirname(__DIR__) . '/includes/certifyRuleNotice.txt';
        $certify_money_notice = file_get_contents($fh);
    }

    if (false != $res) {
        if (null == $res['fSalesConfirmDate'] or null == $res['fAccountantConfirmDate']) {
            $hideAllBtn = 1;
        }
    }

    if ($balance < 0) {
        $hideAllBtn = 1;
    }

    $hideAllBtn = 0;

    $interestAlert = 0;
    if ($detail['cInterest'] > 20000) {
        $interestAlert = 1;
    }

?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>點交表</title>
    <link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css"
        rel="Stylesheet" />
    <script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="/js/calender_limit.js"></script>
    <link href="/css/combobox.css" rel="stylesheet">
    <script src="/js/lib/comboboxNormal.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        setComboboxNormal('newBankMainBuyer', 'name');
        setComboboxNormal('newBankBranchBuyer', 'name');
        setComboboxNormal('newBankMain', 'name');
        setComboboxNormal('newBankBranch', 'name');

        <?php if ($certifiedMoneyAudit == 0): ?>
        alert('未收足需主管審核通過才可開點交單');
        <?php endif?>

        <?php if ($checkReturn > 0): ?>
        alert("尚未返還公司代墊");
        <?php endif?>
<?php if ($hideAllBtn == 1): ?>
        $('all_btn').hide();
        $(':input:button').hide();
        <?php endif?>
<?php if ($interestAlert == 1): ?>
        alert("利息超過兩萬 請至利息分配資訊 確認代扣金額");
        <?php endif?>

        <?php if ($paybycasePass == 0): ?>
        alert("隨案回饋金額尚未確認 無法製作點交單");
        <?php endif?>

        <?php if (! empty($certify_money_notice)): ?>
        alert("<?php echo $certify_money_notice ?>");
        <?php endif?>

        $('#int_total', window.parent.document).html(
            'NT$<?php echo number_format((floatval($detail['bInterest']) + floatval($detail['cInterest']))) . "元" ?>');
        $('[name="int_total"]', window.parent.document).val(<?php echo(floatval($detail['bInterest']) + floatval($detail['cInterest'])) ?>);
        $('#int_money', window.parent.document).html('(已分配：<?php echo $int_money ?>元)');
        $('[name="int_money"]', window.parent.document).val(<?php echo $int_money ?>);

        $('#save_btn').button({
            icons: {
                primary: "ui-icon-document"
            }
        });

        $('#save_this_btn').button({
            icons: {
                primary: "ui-icon-disk"
            }
        });

        $('#view_btn').button({
            icons: {
                primary: "ui-icon-script"
            }
        });

        $('#new_view_btn').button({
            icons: {
                primary: "ui-icon-script"
            }
        });
        $('#new_preview_btn').button({
            icons: {
                primary: "ui-icon-script"
            }
        });

        $('#preview_btn').button({
            icons: {
                primary: "ui-icon-search"
            }
        });

        $('#default_btn').button({
            icons: {
                primary: "ui-icon-refresh"
            }
        });

        $('#delete_uploadfile').button({

        });
        $('#del_tab').each(function() {
            $('#del_tab').button({
                icons: {
                    primary: "ui-icon-trash"
                }
            });
        });

        $('#tabs').tabs({
            selected:                                                                                                          <?php echo $pg ?>
        });
    });

    function checkSellerFee() {
        let check =                                                                                                <?php echo $checkSellerServiceFee ?>;
        return (check > 0) ? true : false;
    }

    function save() {
        if (!checkSellerFee()) {
            alert("無賣方服務費出款記錄");
        }

        if (confirm('是否儲存點交表資料?') == false) {
            return false;
        } else {
            $('form[name="chkform"]').attr('action', 'checklist_save.php');
            $('form[name="chkform"]').one('submit');
        }
    }

    function save_this_f() {
        if (!checkSellerFee()) {
            alert("無賣方服務費出款記錄");
        }

        if (confirm('是否儲存點交表資料?') == false) {
            return false;
        } else {
            $('form[name="chkform"]').attr('action', 'checklist_save.php');
            $('form[name="chkform"] input[name="save_this"]').val('ok');

            $('form[name="chkform"]').one('submit');
        }
    }

    function view() {
        if (!checkSellerFee()) {
            alert("無賣方服務費出款記錄");
        }

        if (confirm('是否儲存並預覽點交表?') == false) {
            return false;
        } else {
            $('[name="preview"]').val(1);
            $('form[name="chkform"]').attr('action', 'checklist_save.php');

            $('form[name="chkform"]').one('submit');
        }
    }

    function view_new() {
        if (!checkSellerFee()) {
            alert("無賣方服務費出款記錄");
        }
        if (confirm('是否儲存並預覽點交表?') == false) {
            return false;
        } else {
            $('[name="preview"]').val(1);
            $('form[name="chkform"]').attr('action', 'checklist_save_new.php');

            $('form[name="chkform"]').one('submit');
        }
    }

    function sleep(milliseconds) {
        var start = new Date().getTime();
        for (var i = 0; i < 1e7; i++) {
            if ((new Date().getTime() - start) > milliseconds) {
                break;
            }
        }
    }

    function preview_pdf() {
        window.open("list_pdf.php?cCertifiedId=<?php echo $detail['cCertifiedId'] ?>&iden=b", "buyer",
            "width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no");
        sleep(1000);

        window.open("list_pdf.php?cCertifiedId=<?php echo $detail['cCertifiedId'] ?>&iden=o", "owner",
            "width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no");
    }

    function preview_pdf_new(target) {
        window.open("list_pdf_new.php?cCertifiedId=<?php echo $detail['cCertifiedId'] ?>&target=buyer", "buyer_new",
            "width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no");

        setTimeout(function() {
            window.open("list_pdf_new.php?cCertifiedId=<?php echo $detail['cCertifiedId'] ?>&target=owner", "owner_new",
                "width=800px,height=1200px,status=yes,scrollbars=yes,location=no,menubar=no,location=no");
        }, 1000);

    }

    function set_default() {
        if (confirm('確認是否重新製作點交表?') == false) {
            return false;
        } else {
            $('form[name="chkform"]').attr('action', 'checklist_default.php');
            $('form[name="chkform"]').one('submit');
        }
    }

    function del_buyer_trans(sn) {
        var url = 'buyer_trans.php';

        if (confirm('請確認是否將此筆資料從點交表中移除?') == false) {
            return false;
        } else {
            $.post(url, {
                'del': sn
            }, function(txt) {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
            });
        }
    }

    function del_tax(id) {
        var url = 'checklist_tax.php';

        if (confirm('請確認是否將此筆資料從點交表中移除?') == false) {
            return false;
        } else {
            $.post(url, {
                'type': 'delete',
                'id': id
            }, function(txt) {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
            });
        }
    }

    function add_tax(i) {
        var url = 'checklist_tax.php';

        var cid = $('[name="cCertifiedId"]').val();
        var title = $('[name="new_TaxTitle"]').val();
        var tax = $('[name="new_Tax"]').val();
        var tr = $('[name="new_TaxRemark"]').val();

        var otitle = $('[name="new_oTaxTitle"]').val();
        var otax = $('[name="new_oTax"]').val();
        var otr = $('[name="new_oTaxRemark"]').val();

        if (i == 1 && title == '' && tax == '' && tr == '') {
            alert('買方結清撥付款項明細摘要禁止空值');
            return false;
        } else if (i == 2 && otitle == '' && otax == '' && otr == '') {
            alert('賣方結清撥付款項明細摘要禁止空值');
            return false;
        }

        $.post(url, {
            'type': 'add',
            'identity': i,
            'cCertifiedId': cid,
            'title': title,
            'tax': tax,
            'taxRemark': tr,
            'otitle': otitle,
            'otax': otax,
            'otaxRemark': otr
        }, function(txt) {
            window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
        });
    }

    function add_remark(i) {
        var url = 'checklist_remark.php';
        var cid = $('[name="cCertifiedId"]').val();
        var remark;
        var pg;

        if (i == 1) {
            remark = $('[name="new_other_remark_buyer"]').val();
            if (remark == '') {
                alert('買方結清撥付款項明細摘要其他禁止空值');
                return false;
            }
            pg = 0;
        } else {
            remark = $('[name="new_other_remark"]').val();
            if (remark == '') {
                alert('賣方結清撥付款項明細摘要其他禁止空值');
                return false;
            }
            pg = 1;
        }

        $.post(url, {
            'type': 'add',
            'identity': i,
            'cCertifiedId': cid,
            'remark': remark
        }, function(txt) {
            window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=' + pg;
        });
    }

    function del_remark(id) {
        var url = 'checklist_remark.php';

        if (confirm('請確認是否將此筆資料從點交表中移除?') == false) {
            return false;
        } else {
            $.post(url, {
                'type': 'delete',
                'id': id
            }, function(txt) {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
            });
        }
    }

    function add_buyer_trans() {
        var url = 'buyer_trans.php';

        var cd = $('[name="cCertifiedId"]').val();
        var kd = $('[name="bDate_new"]').val();
        var kn = $('[name="bKind_new"]').val();
        var bin = $('[name="bIncome_new"]').val();
        var en = $('[name="bExpense_new"]').val();
        var rn = $('[name="bRemark_new"]').val();

        $.post(url, {
            'add': '1',
            'cCertifiedId': cd,
            'bDate_new': kd,
            'bKind_new': kn,
            'bIncome_new': bin,
            'bExpense_new': en,
            'bRemark_new': rn
        }, function(txt) {
            if (bin > 0) {
                url = 'owner_trans.php';
                $.post(url, {
                    'add': '1',
                    'cCertifiedId': cd,
                    'oDate_new': kd,
                    'oKind_new': kn,
                    'oIncome_new': bin,
                    'oExpense_new': en,
                    'oRemark_new': rn
                }, function(txt) {
                    window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
                });
            } else {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
            }
        });
    }

    function del_owner_trans(sn) {
        var url = 'owner_trans.php';

        if (confirm('請確認是否將此筆資料從點交表中移除?') == false) {
            return false;
        } else {
            $.post(url, {
                'del': sn
            }, function(txt) {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=1';
            });
        }
    }

    function int_del(cat) {
        $.ajax({
                url: 'int_hidden.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    cat: cat,
                    cId: "<?php echo $cCertifiedId ?>"
                },
            })
            .done(function(msg) {
                alert(msg);
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=1';
            });
    }

    function realestateBalance_hide(cat) {
        $.ajax({
                url: 'realestateBalance_hidden.php',
                type: 'POST',
                dataType: 'html',
                data: {
                    cat: cat,
                    cId: "<?php echo $cCertifiedId ?>"
                },
            })
            .done(function(msg) {
                var btnVal = $("#" + cat + "Realestate").val();
                var pg = 0;

                if (cat == 'b') {
                    pg = 0;
                } else {
                    pg = 1;
                }

                alert(msg);
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=' + pg;
            });
    }

    function add_owner_trans() {
        var url = 'owner_trans.php';

        var cd = $('[name="cCertifiedId"]').val();
        var kd = $('[name="oDate_new"]').val();
        var kn = $('[name="oKind_new"]').val();
        var bin = $('[name="oIncome_new"]').val();
        var en = $('[name="oExpense_new"]').val();
        var rn = $('[name="oRemark_new"]').val();

        $.post(url, {
            'add': '1',
            'cCertifiedId': cd,
            'oDate_new': kd,
            'oKind_new': kn,
            'oIncome_new': bin,
            'oExpense_new': en,
            'oRemark_new': rn
        }, function(txt) {
            if (bin > 0) {
                url = 'buyer_trans.php';
                $.post(url, {
                    'add': '1',
                    'cCertifiedId': cd,
                    'bDate_new': kd,
                    'bKind_new': kn,
                    'bIncome_new': bin,
                    'bExpense_new': en,
                    'bRemark_new': rn
                }, function(txt) {
                    window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=1';
                });
            } else {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=1';
            }
        });
    }

    //手動新增帳戶資料
    function newBank(b) {
        var url = 'updateBankList.php';
        var type = '';
        if (b == 'b') {
            b = 'Buyer';
            type = 1;
        } else {
            type = 2;
        }

        var nb = 'ok';
        var ncId = $('[name="new_cIdentity' + b + '"]').val();
        var bm = $('[name="newBankMain' + b + '"] option:selected').val();
        var bb = $('[name="newBankBranch' + b + '"] option:selected').val();
        var ano = $('[name="newAccountNo' + b + '"]').val();
        var ann = $('[name="newAccountName' + b + '"]').val();
        var money = $('[name="newAccountMoney' + b + '"]').val();
        var sn = '<?php echo $cCertifiedId ?>';

        if (ncId != "") {
            $.post(url, {
                    newbank: nb,
                    new_cIdentity: ncId,
                    newBankMain: bm,
                    newBankBranch: bb,
                    newAccountNo: ano,
                    newAccountName: ann,
                    cCertified: sn,
                    newAccountMoney: money,
                    type: type
                },
                function(txt) {
                    if (b == 'Buyer') {
                        $("#act_bank_buy").empty();
                        $("#act_bank_buy").html(txt);

                    } else {
                        $("#act_bank_owner").empty();
                        $("#act_bank_owner").html(txt);
                    }
                    alert('銀行帳戶已加入!!');
                });
        } else {
            alert('請選取價金帳戶建立對象!!');
            $('[name="new_cIdentity' + b + '"]').focus();
            return false;
        }
    }

    //隱藏銀行
    function hideBank(sn, b, val) {
        var url = 'updateBankList.php';
        var cid = '<?php echo $cCertifiedId ?>';

        type = (b == 1) ? 1 : 2;

        if (confirm('確認是否要隱藏本筆資料?') == true) {
            $.post(url, {
                id: sn,
                hide: 'ok',
                cCertified: cid,
                new_cIdentity: b,
                type: type,
                val: val
            }, function(txt) {
                if (b == 1) {
                    $("#act_bank_buy").empty();
                    $("#act_bank_buy").html(txt);

                } else {
                    $("#act_bank_owner").empty();
                    $("#act_bank_owner").html(txt);
                }
            });
        }
    }

    //刪除銀行帳戶資料欄位
    function delBank(sn, b) {
        var url = 'updateBankList.php';
        var cid = '<?php echo $cCertifiedId ?>';

        type = (b == 1) ? 1 : 2;

        if (confirm('確認是否要刪除本筆資料?') == true) {
            $.post(url, {
                id: sn,
                del: 'ok',
                cCertified: cid,
                new_cIdentity: b,
                type: type
            }, function(txt) {
                if (b == 1) {
                    $("#act_bank_buy").empty();
                    $("#act_bank_buy").html(txt);
                } else {
                    $("#act_bank_owner").empty();
                    $("#act_bank_owner").html(txt);
                }
            });
        }
    }

    //修改銀行帳戶資料欄位
    function modBank(sn, b) {
        var url = 'updateBankList.php';
        var cid = '<?php echo $cCertifiedId ?>';
        var money = $("[name='bankMoney" + b + sn + "']").val();

        type = (b == 1) ? 1 : 2;

        $.post(url, {
            id: sn,
            mod: 'ok',
            cCertified: cid,
            new_cIdentity: b,
            type: type,
            money: money
        }, function(txt) {
            if (b == 1) {
                $("#act_bank_buy").empty();
                $("#act_bank_buy").html(txt);
            } else {
                $("#act_bank_owner").empty();
                $("#act_bank_owner").html(txt);
            }

            if (txt != '') {
                alert('修改成功');
            };
        });
    }

    //手動新增銀行帳號
    function newbankChange(b) {
        var url = 'getBranch.php';

        if (b == 'b') {
            b = 'Buyer';
        }

        var bb = $('[name="newBankMain' + b + '"] option:selected').val();

        $.post(url, {
            m: bb
        }, function(txt) {
            $('[name="newBankBranch' + b + '"]').empty().html(txt);
        });
    }

    //依據總行改變分行清單
    function bankChange(sn) {
        var url = 'getBranch.php';
        var bb = $('#bMain' + sn + ' option:selected').val();

        $.post(url, {
            m: bb
        }, function(txt) {
            $('#bBranch' + sn).empty().html(txt);
        });
    }

    //更新空白欄位銀行資訊
    function addBank(sn) {
        var url = 'updateBankList.php';

        var ide = $('#bIde' + sn + ' option:selected').val(); //對象
        var mb = $('#bMain' + sn + ' option:selected').val(); //總行
        var bb = $('#bBranch' + sn + ' option:selected').val(); //分行
        var accNo = $('#bAccNo' + sn).val(); //帳號
        var accName = $('#bAccName' + sn).val(); //戶名

        $.post(
            url, {
                id: sn,
                iden: ide,
                bMain: mb,
                bBranch: bb,
                aNo: accNo,
                aName: accName,
                mod: 'ok'
            },
            function() {
                window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>';
            }
        );
    }

    function ownerMoney(cat) {
        var val1 = $("[name='" + cat + "Compensation2']").val();
        var val2 = $("[name='" + cat + "Compensation3']").val();

        val1 = parseInt(val1);
        val2 = parseInt(val2);

        $("[name='" + cat + "Compensation4']").val((val1 + val2));
    }

    function deleteUploadFileRecord(sn) {
        var url = 'deletetUploadFile.php';

        if (confirm('確定收回發布?') == false) {
            return false;
        } else {
            $.post(url, {
                'certifiedId': sn
            }, function(txt) {
                if (txt == 200) {
                    window.location = 'checklist.php?cCertifiedId=<?php echo $cCertifiedId ?>&pg=0';
                }
            });
        }
    }
    </script>
    <style>
    input {
        background-color: #FFFFFD;
    }

    #tabs {
        width: 850px;
        margin-left: auto;
        margin-right: auto;
        font-size: 9pt;
    }

    #tabs input {
        font-size: 9pt;
    }

    #tabs table {
        font-size: 9pt;
    }

    .gap {
        padding: 5px 2px 5px 2px;
    }

    .dollars {
        text-align: right;
    }

    .ui-autocomplete-input {
        width: 50px;
    }

    .ui-autocomplete {
        width: 160px;
        max-height: 300px;
        overflow-y: auto;
        /* prevent horizontal scrollbar */
        overflow-x: hidden;
        /* add padding to account for vertical scrollbar */
        padding-right: 20px;
    }
    </style>
</head>

<body>
    <div id="tabs">
        <form name="chkform" method="post">
            <input type="hidden" name="db_tbl" value="<?php echo $ok ?>" />
            <input type="hidden" name="preview">
            <input type="hidden" name="save_this">
            <div style="padding-bottom: 20px;">
                <span style="float:left;">

                    履保費(<?php echo floatval($detail['cCertifiedMoney']) + floatval($detail['bCertifiedMoney']) ?>)
                    - 利息(<?php echo $detail['cInterest'] ?>) + 代扣利息二代健保(<?php echo $NHITax ?>) + 代扣利息稅額(<?php echo $InterestTax ?>)
                    - 地政士回饋金(<?php echo $feedBackMoney ?>) + 代扣地政士回饋金二代健保(<?php echo $feedBackNHITax ?>) + 代扣地政士回饋金稅額(<?php echo $feedBackTax ?>)
                    =
                    <?php
                        echo($balance >= 0) ? $balance : '<span style="font-size: 12pt;font-weight: bold;color: #C11C05;">' . $balance . '</span>';
                    ?>
                </span>
                <?php
                    if (! $front_ok) {
                        echo '<span style="float: right;font-size:8pt;color:red">*&nbsp;前台點交表未上線</span>';
                    }
                ?>
            </div>

            <ul>
                <li><a href='#page-1'>買方點交表</a></li>
                <li><a href='#page-2'>賣方點交表</a></li>
            </ul>
            <div id='page-1'>
                <table cellspacing="0" cellspadding="0" style="width:800px;">
                    <tr>
                        <td style="text-align:center;font-size:20pt;">第一建築經理(股)公司</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">履保專戶收支明細表暨點交確認單(買方)</td>
                    </tr>
                </table>
                <table cellspacing="0" cellspadding="0" style="width:800px;">
                    <tr>
                        <td style="width:50%">案件基本資料</td>
                        <td style="width:50%;text-align:right;font-size:9px;">(<?php echo $detail['last_modify'] ?>)</td>
                    </tr>
                </table>
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">項目
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">名稱
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">項目
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">名稱
                        </td>
                    </tr>
                    <tr>
                        <td style="width:200px;">保證號碼：</td>
                        <td style="width:200px;"><input type="text" name='bCertifiedId' readonly
                                value="<?php echo $detail['cCertifiedId'] ?>">
                            <?php if ($detail['cCertifiedId'] == '090020924'): ?>
                            (080146177)
                            <?php endif?>
                        </td>
                        <td style="width:200px;">特約地政士：</td>
                        <td style="width:200px;"><input type="text" name="bScrivener"
                                value="<?php echo $detail['bScrivener'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">買方姓名：</td>
                        <td style="width:200px;"><input type="text" name="bBuyer" value="<?php echo $detail['bBuyer'] ?>"></td>
                        <td style="width:200px;">買方統一編號：</td>
                        <td style="width:200px;"><input type="text" name="bBuyerId"
                                value="<?php echo strtoupper($detail['bBuyerId']) ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">賣方姓名：</td>
                        <td style="width:200px;"><input type="text" name="bOwner" value="<?php echo $detail['bOwner'] ?>"></td>
                        <td style="width:200px;">賣方統一編號：</td>
                        <td style="width:200px;"><input type="text" name="bOwnerId"
                                value="<?php echo strtoupper($detail['bOwnerId']) ?>"></td>
                    </tr>
                    <?php if (! $detail['bMoreStore']): ?>
                    <tr>
                        <td style="width:200px;">仲介單位：</td>
                        <td style="width:200px;"><input type="text" name="bBrand" value="<?php echo $detail['bBrand'] ?>"></td>
                        <td style="width:200px;">仲介店名：</td>
                        <td style="width:200px;"><input type="text" name="bStore" value="<?php echo $detail['bStore'] ?>"></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td style="width:200px;">仲介店：<font color="red">(店中間區隔以","為主)</font>
                        </td>
                        <td colspan="3"><input type="text" name="bMoreStore" value="<?php echo $detail['bMoreStore'] ?>"
                                style="width:100%"></td>
                    </tr>
                    <?php endif?>

                    <tr>
                        <td style="width:200px;">買賣總金額：</td>
                        <td style="width:200px;"><input type="text" name="bTotalMoney"
                                value="<?php echo $detail['bTotalMoney'] ?>"></td>
                        <td style="width:200px;">專戶代償金額：</td>
                        <td style="width:200px;"><input type="text" name="bCompensation2"
                                value="<?php echo $detail['bCompensation2'] ?>" onblur="ownerMoney('b')"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">未入專戶：</td>
                        <td style="width:200px;"><input type="text" name="bNotIntoMoney"
                                value="<?php echo $detail['bNotIntoMoney'] ?>"></td>
                        <td style="width:200px;">買方銀行代償：</td>
                        <td style="width:200px;"><input type="text" name="bCompensation3"
                                value="<?php echo $detail['bCompensation3'] ?>" onblur="ownerMoney('b')"></td>
                    </tr>

                    <tr>
                        <td style="width:200px;">買賣總金額備註：</td>
                        <td style="width:200px;"><input type="text" name="bTotalMoneyNote"
                                value="<?php echo $detail['bTotalMoneyNote'] ?>"></td>
                        <td style="width:200px;">代償總金額：</td>
                        <td style="width:200px;">
                            <?php
                                if ($detail['bCompensation4'] == 0) {
                                    $detail['bCompensation4'] = floatval($detail['bCompensation2']) + floatval($detail['bCompensation3']);
                                }
                            ?>
                            <input type="text" name="bCompensation4" value="<?php echo $detail['bCompensation4'] ?>">
                        </td>
                    </tr>
                    <?php
                        $sql = "
			SELECT
				(SELECT zCity FROM  tZipArea WHERE cZip=zZip) AS city,
				(SELECT zArea FROM  tZipArea WHERE cZip=zZip) AS area,
				cAddr
			FROM
				tContractProperty
			WHERE
			 cCertifiedId ='" . $cCertifiedId . "'";
                        $rs = $conn->Execute($sql);
                    while (! $rs->EOF) {?>
                    <tr>
                        <td style="width:200px;">買賣標的物：</td>
                        <td colspan="3" style="width:600px;">
                            <?php echo $rs->fields['city'] . $rs->fields['area'] . $rs->fields['cAddr']; ?>
                            <!-- <input type="text" name="bProperty" style="width:545px;" value="<?php echo $detail['bProperty'] ?>"> -->
                        </td>
                    </tr>

                    <?php	$rs->MoveNext();}?>

                </table>
                <div style="height:30px;width:800px;"></div>
                買賣價金收支明細
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:83px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">日期
                        </td>
                        <td style="width:120px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">摘要
                        </td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            收入金額</td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            支出金額</td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">小計
                        </td>
                        <td style="border-top-style:double;border-bottom-style:double;border-color:#ccc;">備註</td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            執行動作</td>
                    </tr>
                    <tr>
                        <td colspan="7">【專戶收款】</td>
                    </tr>
                    <?php
                        $total     = 0;
                        $buyer_max = count($buyer_income);
                        for ($i = 0; $i < $buyer_max; $i++) {
                            $total += floatval($buyer_income[$i]['bIncome']);
                            $showIncome = '';

                            if ($i == ($buyer_max - 1)) {
                                $showIncome = $total;
                            }

                            echo '
		<tr>
			<td><input type="text" style="width:83px;" name="bDate[]" value="' . $buyer_income[$i]['bDate'] . '"></td>
			<td><input type="text" style="width:120px;" name="bKind[]" value="' . $buyer_income[$i]['bKind'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" name="bIncome[]" value="' . $buyer_income[$i]['bIncome'] . '"></td>
			<td><input type="text" style="width:100px;color:#A0A0A0;" class="dollars" readonly name="bExpense[]" value="' . $buyer_income[$i]['bExpense'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" disabled="disabled" value="' . $showIncome . '"></td>
			<td><input type="text" style="width:150px;" name="bRemark[]" value="' . $buyer_income[$i]['bRemark'] . '"></td>
			<td style="width:100px;text-align:center;">
				<input type="hidden" name="bId[]" value="' . $buyer_income[$i]['bId'] . '">
				<input type="button" onclick="del_buyer_trans(' . $buyer_income[$i]['bId'] . ')" value="刪除">
			</td>
		</tr>
	';
                        }
                        $total += floatval($detail['bInterest']);
                    ?>
                    <tr>
                        <td colspan="7">【專戶出款】</td>
                    </tr>
                    <?php
                        $expense     = 0;
                        $buyer_max_e = count($buyer_expense);
                        for ($i = 0; $i < $buyer_max_e; $i++) {
                            $total -= floatval($buyer_expense[$i]['bExpense']);
                            $expense += floatval($buyer_expense[$i]['bExpense']);

                            $showExpense = '';
                            if ($i == ($buyer_max_e - 1)) {
                                $showExpense = $expense;
                            }

                            echo '
		<tr>
			<td><input type="text" style="width:83px;" name="bDate[]" value="' . $buyer_expense[$i]['bDate'] . '"></td>
			<td><input type="text" style="width:120px;" name="bKind[]" value="' . $buyer_expense[$i]['bKind'] . '"></td>
			<td><input type="text" style="width:100px;color:#A0A0A0;" class="dollars" readonly name="bIncome[]" value="' . $buyer_expense[$i]['bIncome'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" name="bExpense[]" value="' . $buyer_expense[$i]['bExpense'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" disabled="disabled" value="' . $showExpense . '"></td>
			<td><input type="text" style="width:150px;" name="bRemark[]" value="' . $buyer_expense[$i]['bRemark'] . '"></td>
			<td style="width:100px;text-align:center;">
				<input type="hidden" name=bId[] value="' . $buyer_expense[$i]['bId'] . '">
				<input type="button" onclick="del_buyer_trans(' . $buyer_expense[$i]['bId'] . ')" value="刪除">
			</td>
		</tr>
	';
                        }
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>

                        <td>專戶收支餘額：</td>

                        <td><input type="text" class="dollars" style="width:100px;" disabled value="<?php echo $total ?>"></td>
                        <td><input type="text" style="width:150px;" disabled value="(收入-支出)"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="7">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:83px;font-size:8pt;">(日期)</td>
                        <td style="width:120px;font-size:8pt;">(摘要)</td>
                        <td style="width:100px;font-size:8pt;">(收入金額)</td>
                        <td style="width:100px;font-size:8pt;">(支出金額)</td>
                        <td style="width:100px;font-size:8pt;">(小計)</td>
                        <td style="font-size:8pt;">(備註)</td>
                        <td style="style=width:120px;font-size:8pt;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type="text" style="width:83px;" name="bDate_new"
                                onclick="show_calendar('chkform.bDate_new')" value=""></td>
                        <td><input type="text" style="width:120px;" name="bKind_new" value=""></td>
                        <td><input type="text" class="dollars" style="width:100px;" name="bIncome_new" value=""></td>
                        <td><input type="text" class="dollars" style="width:100px;" name="bExpense_new" value=""></td>
                        <td><input type="text" style="width:100px;" disabled value=""></td>
                        <td><input type="text" style="width:150px;" name="bRemark_new" value=""></td>
                        <td style="text-align:center;"><input type="button" onclick="add_buyer_trans()" value="新增"></td>
                    </tr>
                </table>
                <div style="height:30px;width:800px;"></div>

                結清撥付款項明細
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">摘要
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">金額
                        </td>
                        <td style="width:300px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">備註
                        </td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            執行動作</td>
                    </tr>
                    <tr style="display:;">
                        <td style="width:200px;">*本案件專戶餘額</td>
                        <td style="width:200px;"><input type="text" class="dollars" disabled="disabled"
                                value="<?php echo $total ?>"></td>
                        <td style="width:400px;" colspan="2">即專收款扣除專戶出款 </td>

                    </tr>
                    <tr>
                        <td style="width:200px;">*應付仲介服務費餘額</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="bRealestateBalance"
                                value="<?php echo $detail['bRealestateBalance'] ?>"></td>
                        <td style="width:300px;">買方應付仲介服務費 </td>
                        <td style="width:100px;"><input type="button"
                                value="<?php echo($detail['bRealestateBalanceHide'] == 0) ? "隱藏" : "顯示"; ?>"
                                onclick="realestateBalance_hide('b')" /></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*應付履約保證費</td>
                        <td style="width:200px;">
                            <input type="text" class="dollars" name="bCertifiedMoney"
                                value="<?php echo $detail['bCertifiedMoney'] ?>">
                        </td>

                        <td style="width:400px;" colspan="2">
                            <input type="text" name="bcertify_remark" style="width:350px;"
                                value="<?php echo $detail['bcertify_remark'] ?>">
                        </td>
                    </tr>
                    <?php if ($buyer > 0): ?>
<?php (null == $detail['bCertifyQue']) ? $bCertifyQue         = '履保費發票金額是否均分' : $bCertifyQue         = $detail['bCertifyQue']; ?>
<?php (null == $detail['bCertifyOption1']) ? $bCertifyOption1 = '是' : $bCertifyOption1 = $detail['bCertifyOption1']; ?>
<?php (null == $detail['bCertifyOption2']) ? $bCertifyOption2 = '否' : $bCertifyOption2 = $detail['bCertifyOption2']; ?>
                    <tr>
                        <td style="width:200px;"></td>
                        <td style="width:200px;"></td>
                        <td style="width:400px;" colspan="2">
                            <input type="text" style="width:120px;" class="" name="bCertifyQue"
                                value="<?php echo $bCertifyQue ?>">
                            <input type="radio" name="bCertifyAns" value="1"
                                <?php if ($detail['bCertifyAns'] == '1'): ?> checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="bCertifyOption1"
                                value="<?php echo $bCertifyOption1 ?>">
                            <input type="radio" name="bCertifyAns" value="0"
                                <?php if ($detail['bCertifyAns'] == '0'): ?> checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="bCertifyOption2"
                                value="<?php echo $bCertifyOption2 ?>">
                            說明<input type="text" style="width:48px;" class="" name="bCertifyDesc"
                                value="<?php echo $detail['bCertifyDesc'] ?>">
                        </td>
                    </tr>
                    <?php endif?>
                    <tr>
                        <td style="width:200px;">*應付代書費用及代支費</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="bScrivenerMoney"
                                value="<?php echo $detail['bScrivenerMoney'] ?>"></td>
                        <td style="width:400px;" colspan="2"><input type="text" name="scrivener_remark2" maxlength="255"
                                style="width:300px;" value="<?php echo $detail['scrivener_remark2'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*代扣健保補充保費</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="bNHITax"
                                value="<?php echo $detail['bNHITax'] ?>"></td>
                        <td style="width:400px;" colspan="2">代買方扣繳2.11%補充保費</td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*代扣利息所得稅<input type="hidden" name="bTaxTitle" value="代扣利息所得稅"></td>
                        <td style="width:200px;"><input type="text" class="dollars" name="bTax"
                                value="<?php echo $detail['bTax'] ?>"></td>
                        <td style="width:400px;" colspan="2">
                            <?php
                                if (preg_match("/[A-Za-z]{2}/", $detail['bBuyerId'])) { // 判別是否為外國人(兩碼英文字母者) 外國人20%
                                    echo '代買方扣繳20%利息所得稅';
                                } else {
                                    echo '代買方扣繳10%利息所得稅';
                                }
                            ?>
                            <input type="hidden" name="bTaxRemark" maxlength="24" style="width:350px;"
                                value="代買方扣繳利息所得稅">
                        </td>
                    </tr>
                    <?php
                        //取得額外結清撥付款項明細
                        $sql = "SELECT * FROM tChecklistOther WHERE cCertifiedId ='" . $cCertifiedId . "' AND cIdentity=1 ORDER BY cId ASC";
                        $rs  = $conn->Execute($sql);
                    while (! $rs->EOF) {?>
                    <tr>
                        <td style="width:200px;">*<input type="text" name="TaxTitle[]"
                                value="<?php echo $rs->fields['cTaxTitle'] ?>"><input type="hidden" name="tax_id[]"
                                value="<?php echo $rs->fields['cId'] ?>"></td>
                        <td style="width:200px;"><input type="text" class="dollars" name="Tax[]"
                                value="<?php echo $rs->fields['cTax'] ?>"></td>
                        <td style="width:400px;" colspan="2"><input type="text" name="TaxRemark[]" maxlength="255"
                                style="width:300px;" value="<?php echo $rs->fields['cTaxRemark'] ?>"><input type="button"
                                onclick="del_tax(<?php echo $rs->fields['cId'] ?>)" value="刪除"></td>
                    </tr>

                    <?php $rs->MoveNext();}?>

                    <tr>
                        <td colspan="4" style="width:800px;">&nbsp;&nbsp;<input type="text" name="other_remark_buyer"
                                style="width:700px;" value="<?php echo $detail['other_remark_buyer'] ?>"></td>
                    </tr>
                    <?php
                        $sql = "SELECT cId,cRemark FROM tChecklistRemark WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity=1 ORDER BY cId ASC";
                        $rs  = $conn->Execute($sql);
                    while (! $rs->EOF) {?>
                    <tr>
                        <td colspan="3" style="width:700px;"><input type="text" name="data_other_remark[]"
                                style="width:700px;" value="<?php echo $rs->fields['cRemark'] ?>"><input type="hidden"
                                name="data_other_remark_id[]" value="<?php echo $rs->fields['cId'] ?>" />
                        </td>
                        <td><input type="button" onclick="del_remark(<?php echo $rs->fields['cId'] ?>)" value="刪除"></td>
                    </tr>

                    <?php	$rs->MoveNext();}?>

                    <tr>
                        <td colspan="4">
                            <hr>
                        </td>
                    </tr>
                    <!--新增-->
                    <tr>
                        <td style="width:200px;">摘要</td>
                        <td style="width:200px;">金額</td>
                        <td style="width:300px;">備註</td>
                        <td style="width:100px;"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*<input type="text" name="new_TaxTitle" value=""></td>
                        <td style="width:200px;"><input type="text" class="dollars" name="new_Tax" value=""></td>
                        <td style="width:300px;"><input type="text" name="new_TaxRemark" maxlength="255"
                                style="width:300px;" value=""></td>
                        <td style="width:100px;"><input type="button" onclick="add_tax(1)" value="新增"></td>
                    </tr>
                    <tr>
                        <td colspan="4" id="test">其他</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width:700px;">
                            <input type="text" name="new_other_remark_buyer" style="width:700px;" value="">
                        </td>
                        <td><input type="button" onclick="add_remark(1)" value="新增"></td>
                    </tr>
                </table>
                <div style="height:30px;width:800px;"></div>
                <div>指定收受價金之帳戶</div>
                <div style="width:800px;border:1px 0 1px 0 solid #ccc;">
                    <div style="float:left;width:80px;background-color:#E4BEB1;padding:5px 2px 5px 2px;">對象</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 2px 5px 2px;">解匯行/分行</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 1px 5px 1px;">帳號</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 1px 5px 1px;">戶名</div>
                    <div style="float:left;width:90px;background-color:#E4BEB1;padding:5px 2px 5px 1px;">金額</div>
                    <div style="background-color:#E4BEB1;padding:5px 2px 5px 2px;">執行動作</div>

                    <div class="gap" style="float:left;width:60px;">
                        <select name="new_cIdentityBuyer">
                            <option selected="selected" value="" style="width:70px;"></option>
                            <option value="1">買方</option>
                            <option value="33">仲介</option>
                            <option value="43">地政士</option>
                            <option value="53">其他</option>
                        </select>
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <select name="newBankMainBuyer" style="width:80px;" onchange="newbankChange('b')">
                            <option value="" selected="selected"></option>
                            <?php
                                $sql = 'SELECT * FROM tBank WHERE bBank4="" AND bBank3<>"000" ORDER BY bBank3 ASC;';
                                $rs  = $conn->Execute($sql);
                                while (! $rs->EOF) {
                                    echo '		<option value="' . $rs->fields['bBank3'] . '">' . $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank3'] . ')</option>' . "\n";

                                    $rs->MoveNext();
                            }?>
                        </select>

                        <span style="margin-left: 30px;">/</span>
                        <select name="newBankBranchBuyer" style="width:80px;">

                        </select>
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <input type="text" name="newAccountNoBuyer" maxlength="16">
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <input type="text" name="newAccountNameBuyer">
                    </div>
                    <div class="gap" style="float:left;width:80px;">
                        <input type="text" name="newAccountMoneyBuyer" style="width:70px" />
                    </div>
                    <div class="gap" style="">
                        <input type="button" value="加入" onclick="newBank('b')">
                    </div>
                    <hr style="clear:both;">
                    <div id="act_bank_buy">
                        <?php
                            $sql = '
			SELECT
				*,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="") as BankMain,
				(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch
			FROM
				tChecklistBank AS a
			WHERE
				cCertifiedId="' . $detail['cCertifiedId'] . '"
				AND cIdentity IN ("1","33","43","53")
			ORDER BY
				cOrder,cId
			ASC;
		';
                            $rs = $conn->Execute($sql);
                            while (! $rs->EOF) {
                                switch ($rs->fields['cIdentity']) {
                                    case '1':
                                        $rs->fields['cIdentity'] = '買方';
                                        break;
                                    case '33':
                                        $rs->fields['cIdentity'] = '仲介';
                                        break;
                                    case '43':
                                        $rs->fields['cIdentity'] = '地政士';
                                        break;
                                    case '53':
                                        $rs->fields['cIdentity'] = '其他';
                                        break;
                                    default:
                                        $rs->fields['cIdentity'] = '';
                                        break;
                                }
                                ##

                                //結合總分行顯示
                                if ($rs->fields['cBankMain'] && $rs->fields['cBankBranch']) {
                                    $rs->fields['BankMain'] = str_replace('（農金資中心所屬會員）', '', $rs->fields['BankMain']);
                                    $rs->fields['bank']     = $rs->fields['BankMain'] . '/' . $rs->fields['BankBranch'];
                                } else {
                                    // 確保bank欄位有值，避免未定義錯誤
                                    $rs->fields['bank'] = isset($rs->fields['BankMain']) ? $rs->fields['BankMain'] : '';
                                }
                                $style = ($rs->fields['cHide'] == 1) ? 'background-color:#999' : '';

                            ?>

                        <div class="gap" style="float:left;width:60px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cIdentity'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['bank'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cBankAccountNo'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cBankAccountName'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:80px;">
                            <input type="text" value="<?php echo $rs->fields['cMoney'] ?>"
                                name='bankMoney1<?php echo $rs->fields['cId'] ?>' style="width:70px" />
                        </div>
                        <div class="gap">
                            <input type="button" value="修改" onclick="modBank('<?php echo $rs->fields['cId'] ?>',1)" />
                            <?php
                                if ($rs->fields['cIdentity'] == '仲介' || $rs->fields['cIdentity'] == '地政士') {
                                    if ($rs->fields['cHide'] == 1) {?>
                            <input type="button" value="顯示" onclick="hideBank('<?php echo $rs->fields['cId'] ?>',1,0)" />
                            <?php	} else {?>
                            <input type="button" value="隱藏" onclick="hideBank('<?php echo $rs->fields['cId'] ?>',1,1)" />
                            <?php	}
                                    ?>

                            <?php
                            } else {?>

                            <input type="button" value="刪除" onclick="delBank('<?php echo $rs->fields['cId'] ?>',1)">
                            <?php	}
                                ?>
                        </div>
                        <div style="clear:both"></div>
                        <?php
                            $rs->MoveNext();
                            }
                        ?>
                    </div>
                    <hr />
                    <?php
                        $bchecked = ''; // 初始化變數
                        if ($detail['bNote'] == 1) {
                            $bchecked = 'checked';
                        }
                    ?>
                    <div style="height:30px;width:800px;"><label><input type="checkbox" name="bNote" value="1"
                                <?php echo $bchecked ?> />加入預售屋換約備註事項</label></div>
                </div>
            </div>

            <div id='page-2'>
                <table cellspacing="0" cellspadding="0" style="width:800px;">
                    <tr>
                        <td style="text-align:center;font-size:20pt;">第一建築經理(股)公司</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">履保專戶收支明細表暨點交確認單(賣方)</td>
                    </tr>
                </table>
                <table cellspacing="0" cellspadding="0" style="width:800px;">
                    <tr>
                        <td style="width:50%">案件基本資料</td>
                        <td style="width:50%;text-align:right;font-size:9px;">(<?php echo $detail['last_modify'] ?>)</td>
                    </tr>
                </table>
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">項目
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">名稱
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">項目
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">名稱
                        </td>
                    </tr>
                    <tr>
                        <td style="width:200px;">保證號碼：</td>
                        <td style="width:200px;"><input type="text" name='cCertifiedId' readonly
                                value="<?php echo $detail['cCertifiedId'] ?>">
                            <?php if ($detail['cCertifiedId'] == '090020924'): ?>
                            (080146177)
                            <?php endif?>
                        </td>
                        <td style="width:200px;">特約地政士：</td>
                        <td style="width:200px;"><input type="text" name="cScrivener"
                                value="<?php echo $detail['cScrivener'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">買方姓名：</td>
                        <td style="width:200px;"><input type="text" name="cBuyer" value="<?php echo $detail['cBuyer'] ?>"></td>
                        <td style="width:200px;">買方統一編號：</td>
                        <td style="width:200px;"><input type="text" name="cBuyerId"
                                value="<?php echo strtoupper($detail['cBuyerId']) ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">賣方姓名：</td>
                        <td style="width:200px;"><input type="text" name="cOwner" value="<?php echo $detail['cOwner'] ?>"></td>
                        <td style="width:200px;">賣方統一編號：</td>
                        <td style="width:200px;"><input type="text" name="cOwnerId"
                                value="<?php echo strtoupper($detail['cOwnerId']) ?>"></td>
                    </tr>
                    <?php if (! $detail['cMoreStore']): ?>
                    <tr>
                        <td style="width:200px;">仲介單位：</td>
                        <td style="width:200px;"><input type="text" name="cBrand" value="<?php echo $detail['cBrand'] ?>"></td>
                        <td style="width:200px;">仲介店名：</td>
                        <td style="width:200px;"><input type="text" name="cStore" value="<?php echo $detail['cStore'] ?>"></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td style="width:200px;">仲介店：<font color="red">(店中間區隔以","為主)</font>
                        </td>
                        <td colspan="3"><input type="text" name="cMoreStore" value="<?php echo $detail['cMoreStore'] ?>"
                                style="width:100%"></td>
                    </tr>
                    <?php endif?>

                    <tr>
                        <td style="width:200px;">買賣總金額：</td>
                        <td style="width:200px;"><input type="text" name="cTotalMoney"
                                value="<?php echo $detail['cTotalMoney'] ?>"></td>
                        <td style="width:200px;">專戶代償金額：</td>
                        <td style="width:200px;"><input type="text" name="cCompensation2"
                                value="<?php echo $detail['cCompensation2'] ?>" onblur="ownerMoney('c')"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">未入專戶：</td>
                        <td style="width:200px;"><input type="text" name="cNotIntoMoney"
                                value="<?php echo $detail['cNotIntoMoney'] ?>"></td>
                        <td style="width:200px;">買方銀行代償：</td>
                        <td style="width:200px;"><input type="text" name="cCompensation3"
                                value="<?php echo $detail['cCompensation3'] ?>" onblur="ownerMoney('c')"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">買賣總金額備註：</td>
                        <td style="width:200px;"><input type="text" name="cTotalMoneyNote"
                                value="<?php echo $detail['cTotalMoneyNote'] ?>"></td>
                        <td style="width:200px;">代償總金額</td>
                        <td style="width:200px;">
                            <?php
                                if ($detail['cCompensation4'] == 0) {
                                    $detail['cCompensation4'] = floatval($detail['cCompensation2']) + floatval($detail['cCompensation3']);
                                }
                            ?>
                            <input type="text" name="cCompensation4" value="<?php echo $detail['cCompensation4'] ?>">
                        </td>
                    </tr>

                    <?php
                        $sql = "
			SELECT
				(SELECT zCity FROM  tZipArea WHERE cZip=zZip) AS city,
				(SELECT zArea FROM  tZipArea WHERE cZip=zZip) AS area,
				cAddr
			FROM
				tContractProperty
			WHERE
			 cCertifiedId ='" . $cCertifiedId . "'";
                        $rs = $conn->Execute($sql);
                    while (! $rs->EOF) {?>
                    <tr>
                        <td style="width:200px;">買賣標的物：</td>
                        <td colspan="3" style="width:600px;">
                            <?php echo $rs->fields['city'] . $rs->fields['area'] . $rs->fields['cAddr']; ?>
                            <!-- <input type="text" name="bProperty" style="width:545px;" value="<?php echo $detail['bProperty'] ?>"> -->
                        </td>
                    </tr>

                    <?php	$rs->MoveNext();}?>

                </table>
                <div style="height:30px;width:800px;"></div>
                買賣價金收支明細
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:83px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">日期
                        </td>
                        <td style="width:120px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">摘要
                        </td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            收入金額</td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            支出金額</td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">小計
                        </td>
                        <td style="border-top-style:double;border-bottom-style:double;border-color:#ccc;">備註</td>
                        <td style="width:100pxborder-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            執行動作</td>
                    </tr>
                    <tr>
                        <td colspan="7">【專戶收款】</td>
                    </tr>
                    <?php
                        $total     = 0;
                        $owner_max = count($owner_income);
                        for ($i = 0; $i < $owner_max; $i++) {
                            $total += floatval($owner_income[$i]['oIncome']);
                            $showIncome = '';

                            if ($i == ($owner_max - 1)) {
                                $showIncome = $total;
                            }

                            echo '
		<tr>
			<td><input type="text" style="width:83px;" name="oDate[]" value="' . $owner_income[$i]['oDate'] . '"></td>
			<td><input type="text" style="width:120px;" name="oKind[]" value="' . $owner_income[$i]['oKind'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" name="oIncome[]" value="' . $owner_income[$i]['oIncome'] . '"></td>
			<td><input type="text" style="width:100px;color:#A0A0A0;" class="dollars" readonly name="oExpense[]" value="' . $owner_income[$i]['oExpense'] . '"></td>
			<td><input type="text" style="width:100px;color:#A0A0A0;" class="dollars" disabled="disabled" value="' . $showIncome . '"></td>
			<td><input type="text" style="width:150px;" name="oRemark[]" value="' . $owner_income[$i]['oRemark'] . '"></td>
			<td style="width:100px;text-align:center;">
				<input type="hidden" name="oId[]" value="' . $owner_income[$i]['oId'] . '">
				<input type="button" onclick="del_owner_trans(' . $owner_income[$i]['oId'] . ')" value="刪除">
			</td>
		</tr>
	';
                        }

                    ?>

                    <?php if ($detail['cInterestHidden'] == 0): $total += floatval($detail['cInterest']); ?>
<?php (null == $detail['cAvgQue']) ? $cAvgQue         = '利息扣繳憑單金額是否均分' : $cAvgQue         = $detail['cAvgQue']; ?>
<?php (null == $detail['cAvgOption1']) ? $cAvgOption1 = '是' : $cAvgOption1 = $detail['cAvgOption1']; ?>
<?php (null == $detail['cAvgOption2']) ? $cAvgOption2 = '否' : $cAvgOption2 = $detail['cAvgOption2']; ?>
						                    <tr>
						                        <td><input type="text" style="width:83px;" disabled value=""></td>
						                        <td><input type="text" style="width:120px;" disabled value="利息"></td>
						                        <td><input type="text" class="dollars" style="width:100px;" name="cInterest"
						                                value="<?php echo $detail['cInterest'] ?>"></td>
						                        <td><input type="text" class="dollars" style="width:100px;" disabled value="0"></td>
						                        <?php if ($owner > 0): ?>
						                        <td colspan="2">
						                            <input type="text" style="width:108px;" class="" name="cAvgQue" value="<?php echo $cAvgQue ?>">
						                            <input type="radio" name="cAvgAns" value="1"						                                                                        					                                                                        				                                                                        			                                                                        		                                                                        	                                                                         <?php if ($detail['cAvgAns'] == '1'): ?>
						                                checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="cAvgOption1"
                                value="<?php echo $cAvgOption1 ?>">
                            <input type="radio" name="cAvgAns" value="0"                                                                                                                                                                                                                                                                                                                                                                                                                                                 <?php if ($detail['cAvgAns'] == '0'): ?>
                                checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="cAvgOption2"
                                value="<?php echo $cAvgOption2 ?>">
                            說明<input type="text" style="width:18px;" class="" name="cAvgDesc"
                                value="<?php echo $detail['cAvgDesc'] ?>">
                        </td>
                        <?php else: ?>
                        <td><input type="text" class="dollars" style="width:100px;" disabled value="<?php echo $total ?>"></td>
                        <td><input type="text" style="width:150px;" disabled value=""></td>
                        <?php endif?>
                        <td style="width:100px;text-align:center;">
                            <input type="button" value="刪除" onclick="int_del('c')" />
                        </td>
                    </tr>
                    <?php endif?>
                    <tr>
                        <td colspan="4">【專戶出款】</td>
                        <?php if ($owner > 0): ?>
                        <td colspan="2"><input type="text" class="dollars" style="width:100px;" disabled
                                value="<?php echo $total ?>"></td>
                        <?php endif?>
                    </tr>
                    <?php
                        $expense     = 0;
                        $owner_max_e = count($owner_expense);
                        for ($i = 0; $i < $owner_max_e; $i++) {
                            $total -= floatval($owner_expense[$i]['oExpense']);
                            $expense += floatval($owner_expense[$i]['oExpense']);

                            $showExpense = '';
                            if ($i == ($owner_max_e - 1)) {
                                $showExpense = $expense;
                            }

                            echo '
		<tr>
			<td><input type="text" style="width:83px;" name="oDate[]" value="' . $owner_expense[$i]['oDate'] . '"></td>
			<td><input type="text" style="width:120px;" name="oKind[]" value="' . $owner_expense[$i]['oKind'] . '"></td>
			<td><input type="text" style="width:100px;color:#A0A0A0;" class="dollars" readonly name="oIncome[]" value="' . $owner_expense[$i]['oIncome'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" name="oExpense[]" value="' . $owner_expense[$i]['oExpense'] . '"></td>
			<td><input type="text" style="width:100px;" class="dollars" disabled="disabled" value="' . $showExpense . '"></td>
			<td><input type="text" style="width:150px;" name="oRemark[]" value="' . $owner_expense[$i]['oRemark'] . '"></td>
			<td style="width:100px;text-align:center;">
				<input type="hidden" name=oId[] value="' . $owner_expense[$i]['oId'] . '">
				<input type="button" onclick="del_owner_trans(' . $owner_expense[$i]['oId'] . ')" value="刪除">
			</td>
		</tr>
	';
                        }

                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>

                        <td>專戶收支餘額：</td>

                        <td><input type="text" class="dollars" style="width:100px;" disabled value="<?php echo $total ?>"></td>
                        <td><input type="text" style="width:150px;" disabled value="(收入-支出)"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="7">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="7">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:83px;font-size:8pt;">(日期)</td>
                        <td style="width:120px;font-size:8pt;">(摘要)</td>
                        <td style="width:100px;font-size:8pt;">(收入金額)</td>
                        <td style="width:100px;font-size:8pt;">(支出金額)</td>
                        <td style="width:100px;font-size:8pt;">(小計)</td>
                        <td sttyle="font-size:8pt;">(備註)</td>
                        <td style="style=width:120px;font-size:8pt;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td><input type="text" style="width:83px;" name="oDate_new"
                                onclick="show_calendar('chkform.oDate_new')" value=""></td>
                        <td><input type="text" style="width:120px;" name="oKind_new" value=""></td>
                        <td><input type="text" style="width:100px;" class="dollars" name="oIncome_new" value=""></td>
                        <td><input type="text" style="width:100px;" class="dollars" name="oExpense_new" value=""></td>
                        <td><input type="text" style="width:100px;" readonly style="color:#A0A0A0;" value=""></td>
                        <td><input type="text" style="width:150px;" name="oRemark_new" value=""></td>
                        <td style="text-align:center;"><input type="button" onclick="add_owner_trans()" value="新增"></td>
                    </tr>
                </table>
                <div style="height:30px;width:800px;"></div>
                結清撥付款項明細
                <table cellspacing="0" cellspadding="2" style="width:800px;">
                    <tr style="background-color:#E4BEB1;">
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">摘要
                        </td>
                        <td style="width:200px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">金額
                        </td>
                        <td style="width:300px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">備註
                        </td>
                        <td style="width:100px;border-top-style:double;border-bottom-style:double;border-color:#ccc;">
                            執行動作</td>
                    </tr>
                    <tr style="display:;">
                        <td style="width:200px;">*本案件專戶餘額</td>
                        <td style="width:200px;"><input type="text" class="dollars" disabled="disabled"
                                value="<?php echo $total ?>"></td>
                        <td style="width:400px;" colspan="2"><input type="text" name="balance_remark"
                                style="width:300px;" value="<?php echo $detail['balance_remark'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*應付仲介服務費餘額</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="cRealestateBalance"
                                value="<?php echo $detail['cRealestateBalance'] ?>"></td>
                        <td style="width:300px;"><input type="text" name="realty_remark" style="width:300px;"
                                value="<?php echo $detail['realty_remark'] ?>"></td>
                        <td style="width:100px;"><input type="button"
                                value="<?php echo($detail['cRealestateBalanceHide'] == 0) ? "隱藏" : "顯示"; ?>"
                                onclick="realestateBalance_hide('c')" /></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*應付履約保證費</td>
                        <td style="width:200px;">
                            <input type="text" class="dollars" name="cCertifiedMoney"
                                value="<?php echo $detail['cCertifiedMoney'] ?>">
                        </td>
                        <?php (null == $detail['cCertifyQue']) ? $cCertifyQue = '履保費發票金額是否均分' : $cCertifyQue = $detail['cCertifyQue']; ?>
<?php (null == $detail['cCertifyOption1']) ? $cCertifyOption1                 = '是' : $cCertifyOption1                 = $detail['cCertifyOption1']; ?>
<?php (null == $detail['cCertifyOption2']) ? $cCertifyOption2                 = '否' : $cCertifyOption2                 = $detail['cCertifyOption2']; ?>
<?php if ($owner > 0): ?>
                        <td colspan="2">
                            <input type="text" style="width:120px;" class="" name="cCertifyQue"
                                value="<?php echo $cCertifyQue ?>">
                            <input type="radio" name="cCertifyAns" value="1"
                                <?php if ($detail['cCertifyAns'] == '1'): ?> checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="cCertifyOption1"
                                value="<?php echo $cCertifyOption1 ?>">
                            <input type="radio" name="cCertifyAns" value="0"
                                <?php if ($detail['cCertifyAns'] == '0'): ?> checked<?php endif?>>
                            <input type="text" style="width:10px;" class="" name="cCertifyOption2"
                                value="<?php echo $cCertifyOption2 ?>">
                            說明<input type="text" style="width:48px;" class="" name="cCertifyDesc"
                                value="<?php echo $detail['cCertifyDesc'] ?>">
                        </td>
                        <?php else: ?>
                        <td style="width:400px;" colspan="2">
                            <input type="text" name="certify_remark" style="width:300px;"
                                value="<?php echo $detail['certify_remark'] ?>">
                        </td>
                        <?php endif?>
                    </tr>
                    <tr>
                        <td style="width:200px;">*代扣買方履約保證費</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="cCertifiedMoney2"
                                value="<?php echo $detail['cCertifiedMoney2'] ?>"></td>
                        <td style="width:400px;" colspan="2"><input type="text" name="certify_remark2"
                                style="width:300px;" value="<?php echo $detail['certify_remark2'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*應付代書費用及代支費</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="cScrivenerMoney"
                                value="<?php echo $detail['cScrivenerMoney'] ?>"></td>
                        <td style="width:400px;" colspan="2"><input type="text" name="scrivener_remark" maxlength="255"
                                style="width:300px;" value="<?php echo $detail['scrivener_remark'] ?>"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*代扣健保補充保費</td>
                        <td style="width:200px;"><input type="text" class="dollars" name="cNHITax"
                                value="<?php echo $detail['cNHITax'] ?>"></td>
                        <td style="width:400px;" colspan="2">代賣方扣繳2.11%補充保費</td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*代扣利息所得稅
                            <!-- <input type="hidden" name="cTaxTitle" value="代扣利息所得稅"> -->
                        </td>
                        <td style="width:200px;"><input type="text" class="dollars" name="cTax"
                                value="<?php echo $detail['cTax'] ?>"></td>
                        <td style="width:400px;" colspan="2">

                            <?php

                                $sql = "SELECT * FROM tContractOthers WHERE cCertifiedId = '" . $cCertifiedId . "' AND cIdentity='2'";
                                $rs  = $conn->Execute($sql);
                                while (! $rs->EOF) {
                                    if (preg_match("/[A-Za-z]{2}/", $rs->fields['cIdentifyId'])) { //
                                        $detail['cOwnerId'] = $rs->fields['cIdentifyId'];
                                    }

                                    $rs->MoveNext();
                                }

                                if (preg_match("/[A-Za-z]{2}/", $detail['cOwnerId']) || preg_match("/^[a-zA-z]{1}[8|9]{1}[0-9]{8}$/", $detail['cOwnerId'])) { // 判別是否為外國人(兩碼英文字母者) 外國人20%
                                    echo '代賣方扣繳20%利息所得稅';
                                } else {
                                    echo '代賣方扣繳10%利息所得稅';
                                }
                            ?>
                            <!-- <input type="hidden" name="cTaxRemark" maxlength="24" style="width:350px;" value="代買方扣繳利息所得稅"> -->
                        </td>
                    </tr>

                    <?php
                        //取得額外結清撥付款項明細
                        $sql = "SELECT * FROM tChecklistOther WHERE cCertifiedId ='" . $cCertifiedId . "' AND cIdentity=2 ORDER BY cId ASC";
                        $rs  = $conn->Execute($sql);
                    while (! $rs->EOF) {?>
                    <tr>
                        <td style="width:200px;">*<input type="text" name="TaxTitle[]"
                                value="<?php echo $rs->fields['cTaxTitle'] ?>"><input type="hidden" name="tax_id[]"
                                value="<?php echo $rs->fields['cId'] ?>"></td>
                        <td style="width:200px;"><input type="text" class="dollars" name="Tax[]"
                                value="<?php echo $rs->fields['cTax'] ?>"></td>
                        <td style="width:300px;"><input type="text" name="TaxRemark[]" maxlength="255"
                                style="width:300px;" value="<?php echo $rs->fields['cTaxRemark'] ?>"></td>
                        <td><input type="button" onclick="del_tax(<?php echo $rs->fields['cId'] ?>)" value="刪除"></td>
                    </tr>

                    <?php	$rs->MoveNext();}?>

                    <tr>
                        <td colspan="4" style="width:800px;">&nbsp;&nbsp;<input type="text" name="other_remark"
                                style="width:700px;" value="<?php echo $detail['other_remark'] ?>"></td>
                    </tr>

                    <?php
                        $sql = "SELECT cId,cRemark FROM tChecklistRemark WHERE cCertifiedId='" . $cCertifiedId . "' AND cIdentity=2 ORDER BY cId ASC";
                        $rs  = $conn->Execute($sql);

                    while (! $rs->EOF) {?>
                    <tr>
                        <td colspan="3" style="width:700px;">&nbsp;
                            <input type="text" name="data_other_remark[]" style="width:700px;"
                                value="<?php echo $rs->fields['cRemark'] ?>">
                            <input type="hidden" name="data_other_remark_id[]" value="<?php echo $rs->fields['cId'] ?>" />
                        </td>
                        <td><input type="button" onclick="del_remark(<?php echo $rs->fields['cId'] ?>)" value="刪除"></td>
                    </tr>


                    <?php	$rs->MoveNext();}?>

                    <tr>
                        <td colspan="4">
                            <hr>
                        </td>
                    </tr>

                    <!--新增地方 -->
                    <tr>
                        <td style="width:200px;">摘要</td>
                        <td style="width:200px;">金額</td>
                        <td style="width:300px;">備註</td>
                        <td style="width:100px;"></td>
                    </tr>
                    <tr>
                        <td style="width:200px;">*<input type="text" name="new_oTaxTitle" value=""></td>
                        <td style="width:200px;"><input type="text" class="dollars" name="new_oTax" value=""></td>
                        <td style="width:300px;"><input type="text" name="new_oTaxRemark" maxlength="255"
                                style="width:300px;" value=""></td>
                        <td style="width:100px;"><input type="button" onclick="add_tax(2)" value="新增"></td>
                    </tr>
                    <!--新增地方 -->

                    <tr>
                        <td colspan="4" id="test">其他</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="width:700px;">
                            <input type="text" name="new_other_remark" style="width:720px;" value="">
                        </td>
                        <td><input type="button" onclick="add_remark(2)" value="新增"></td>
                    </tr>

                </table>
                <div style="height:30px;width:800px;"></div>

                <div>指定收受價金之帳戶</div>
                <div style="width:800px;border:1px 0 1px 0 solid #ccc;">
                    <div style="float:left;width:80px;background-color:#E4BEB1;padding:5px 2px 5px 2px;">對象</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 2px 5px 2px;">解匯行/分行</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 1px 5px 1px;">帳號</div>
                    <div style="float:left;width:180px;background-color:#E4BEB1;padding:5px 1px 5px 1px;">戶名</div>
                    <div style="float:left;width:90px;background-color:#E4BEB1;padding:5px 2px 5px 1px;">金額</div>
                    <div style="background-color:#E4BEB1;padding:5px 2px 5px 2px;">執行動作</div>

                    <div class="gap" style="float:left;width:60px;">
                        <select name="new_cIdentity">
                            <option selected="selected" value="" style="width:70px;"></option>
                            <option value="2">賣方</option>
                            <option value="31">買方</option>
                            <option value="32">仲介</option>
                            <option value="42">地政士</option>
                            <option value="52">其他</option>
                        </select>
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <select name="newBankMain" style="width:80px;" onchange="newbankChange('')">
                            <option value="" selected="selected"></option>
                            <?php
                                $sql = 'SELECT * FROM tBank WHERE bBank4="" AND bBank3<>"000" ORDER BY bBank3 ASC;';
                                $rs  = $conn->Execute($sql);
                                while (! $rs->EOF) {
                                    echo '		<option value="' . $rs->fields['bBank3'] . '">' . $rs->fields['bBank4_name'] . '(' . $rs->fields['bBank3'] . ')</option>' . "\n";

                                    $rs->MoveNext();
                                }
                            ?>
                        </select>
                        <span style="margin-left: 30px;">/</span>
                        <select name="newBankBranch" style="width:80px;">

                        </select>
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <input type="text" name="newAccountNo" maxlength="16">
                    </div>
                    <div class="gap" style="float:left;width:180px;">
                        <input type="text" name="newAccountName">
                    </div>
                    <div class="gap" style="float:left;width:60px;">
                        <input type="text" name="newAccountMoney" style="width:80px;">
                    </div>
                    <div class="gap" style="">
                        <input type="button" value="加入" onclick="newBank('')">
                    </div>
                    <hr style="clear: both;">
                    <div id="act_bank_owner">
                        <?php
                            $sql = '
	SELECT
		*,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4="" AND bOK = 0) as BankMain,
		(SELECT bBank4_name FROM tBank WHERE bBank3=a.cBankMain AND bBank4=a.cBankBranch) as BankBranch
	FROM
		tChecklistBank AS a
	WHERE
		cCertifiedId="' . $detail['cCertifiedId'] . '"
		AND cIdentity IN ("2","31","32","42","52")
	ORDER BY
		cOrder,cId
	ASC;
';

                            $rs = $conn->Execute($sql);
                            while (! $rs->EOF) {
                                switch ($rs->fields['cIdentity']) {
                                    case '2':
                                        $rs->fields['cIdentity'] = '賣方';
                                        break;
                                    case '31':
                                        $rs->fields['cIdentity'] = '買方';
                                        break;
                                    case '32':
                                        $rs->fields['cIdentity'] = '仲介';
                                        break;
                                    case '42':
                                        $rs->fields['cIdentity'] = '地政士';
                                        break;
                                    case '52':
                                        $rs->fields['cIdentity'] = '其他';
                                        break;
                                    default:
                                        $rs->fields['cIdentity'] = '';
                                        break;
                                }
                                ##

                                //結合總分行顯示
                                if ($rs->fields['cBankMain'] && $rs->fields['cBankBranch']) {
                                    $rs->fields['bank'] = $rs->fields['BankMain'] . '/' . $rs->fields['BankBranch'];
                                } else {
                                    // 確保bank欄位有值，避免未定義錯誤
                                    $rs->fields['bank'] = isset($rs->fields['BankMain']) ? $rs->fields['BankMain'] : '';
                                }

                            ?>

                        <div class="gap" style="float:left;width:60px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cIdentity'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['bank'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cBankAccountNo'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:180px;">
                            <input type="text" disabled="disabled" value="<?php echo $rs->fields['cBankAccountName'] ?>">
                        </div>
                        <div class="gap" style="float:left;width:80px;">
                            <input type="text" value="<?php echo $rs->fields['cMoney'] ?>"
                                name='bankMoney2<?php echo $rs->fields['cId'] ?>' style="width:70px;">
                        </div>
                        <div class="gap" style="">
                            <input type="button" value="修改" onclick="modBank('<?php echo $rs->fields['cId'] ?>',2)" />
                            <?php

                                    if ($rs->fields['cIdentity'] == '仲介' || $rs->fields['cIdentity'] == '地政士') {
                                    if ($rs->fields['cHide'] == 1) {?>
                            <input type="button" value="顯示" onclick="hideBank('<?php echo $rs->fields['cId'] ?>',2,0)" />
                            <?php	} else {?>
                            <input type="button" value="隱藏" onclick="hideBank('<?php echo $rs->fields['cId'] ?>',2,1)" />
                            <?php	}
                                    ?>

                            <?php
                            } else {?>

                            <input type="button" value="刪除" onclick="delBank('<?php echo $rs->fields['cId'] ?>',2)">
                            <?php	}
                                ?>
                            <div style="clear:both"></div>
                        </div>

                        <?php

                                $rs->MoveNext($sql);
                            }
                        ?>
                    </div>
                    <hr />
                    <?php
                        $cchecked = ''; // 初始化變數
                        if ($detail['cNote'] == 1) {
                            $cchecked = 'checked';
                        }
                    ?>
                    <div style="height:30px;width:800px;"><label><input type="checkbox" name="cNote" id="" value="1"
                                <?php echo $cchecked ?> />加入預售屋換約備註事項</label></div>
                </div>
            </div>

            <div style="height:30px;width:800px;">　連絡電話：<?php echo $company['tel'] ?>
                Ext.<?php echo $undertaker['Ext'] ?>　傳真電話：<?php echo $undertaker['FaxNum'] ?></div>

            <div style="width:800px;" class="all_btn">
                <center>
<!--                    <button id="preview_btn" type="button" onclick="preview_pdf()">預覽點交表(分開)</button>-->
                    <button id="new_preview_btn" type="button" onclick="preview_pdf_new()">新版預覽(分開)</button>
                    <?php if ($certifiedMoneyAudit == 1 and $paybycasePass == 1): ?>
                    <button id="save_this_btn" onclick="save_this_f()">儲存點交表</button>
                    <button id="save_btn" onclick="save()"><?php echo $btn_str ?>點交表</button>
<!--                    <button id="view_btn" onclick="view()">--><?php //=$btn_str?><!--並預覽(合併)</button>-->
                    <button id="new_view_btn" onclick="view_new()">新版<?php echo $btn_str ?>並預覽(合併)</button>
                    <?php endif?>
<?php if ($front_ok == 1): ?>
                    <button id="delete_uploadfile"
                        onclick="deleteUploadFileRecord(<?php echo $detail['cCertifiedId'] ?>)">收回發布</button>
                    <?php endif?>
                    <button id="default_btn" onclick="set_default()">回復預設值</button>

                </center>
            </div>
        </form>
        <form action="list_pdf.php?cCertifiedId=<?php echo $detail['cCertifiedId'] ?>&iden=o" method="GET" name="pdf_o"
            target="_blank">
            <input type="hidden" name="cCertifiedId" value="<?php echo $detail['cCertifiedId'] ?>"><input type="hidden"
                name="iden" value="o">
        </form>
    </div>
</body>

</html>
