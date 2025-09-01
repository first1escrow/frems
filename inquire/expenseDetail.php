<?php
    include_once '../openadodb.php';
    include_once '../session_check.php';
    include_once '../tracelog.php';

    $eid = $_REQUEST['eid'] ?? '';
    $cid = $_REQUEST['cid'] ?? '';
    $op  = $_REQUEST['op'] ?? '';
    $str = '';

    if (($eid) && ($cid)) {
        $sid = $_REQUEST['sid'] ?? '';
        $tg  = $_REQUEST['tg'] ?? '';
        $it  = $_REQUEST['it'] ?? '';
        $mn  = $_REQUEST['mn'] ?? '';
        $ob  = $_REQUEST['ob'] ?? '';

        $tlog = new TraceLog();
        $tlog->updateWrite($_SESSION['member_id'], json_encode($_REQUEST), '入帳明細');
        ##

        //新增資料
        if ($op == 'add') {
            $sql = 'INSERT INTO tExpenseDetail (eCertifiedId, eExpenseId, eTarget, eItem, eMoney,eObjKind2) VALUES ("' . $cid . '", "' . $eid . '", "' . $tg . '", "' . $it . '", "' . $mn . '","' . $ob . '");';
            // echo $sql;
            if ($conn->Execute($sql)) {
                echo '<div style="font-weight:bold;color:blue;">已新增紀錄!!</div>';
                //$str = 'parent.location.reload() ;' ;
                checklist($cid);
            }
        }
        ##

        //資料更新
        if ($op == 'update') {
            $sql = 'UPDATE tExpenseDetail SET eTarget="' . $tg . '", eItem="' . $it . '", eMoney="' . $mn . '",eObjKind2 ="' . $ob . '" WHERE eId="' . $sid . '" AND eCertifiedId="' . $cid . '";';
            if ($conn->Execute($sql)) {
                echo '<div style="font-weight:bold;color:blue;">紀錄已更新!!</div>';
                //$str = 'parent.location.reload() ;' ;
                checklist($cid);
            }
        }
        ##

        //資料刪除
        if ($op == 'del') {
            $sql = 'DELETE FROM tExpenseDetail WHERE eId="' . $sid . '"; ';
            if ($conn->Execute($sql)) {
                echo '<div style="font-weight:bold;color:red;">紀錄已刪除!!</div>';
                //$str = 'parent.location.reload() ;' ;
                checklist($cid);
            }
        }
        ##

                    //取出資料
        $list = []; // 初始化陣列
        $sql  = '
		SELECT
			*
		FROM
			tExpenseDetail
		WHERE
			eExpenseId="' . $eid . '"
			AND eCertifiedId="' . $cid . '"
	; ';
        $rs = $conn->Execute($sql);
        $i  = 0;
        while (! $rs->EOF) {
            $list[$i++] = $rs->fields;
            $rs->MoveNext();
        }
        ##

        //取出用途類別選項
        $sql = "SELECT * FROM tCategoryExpense WHERE cName != '買方溢入款' ORDER BY cId ASC;";
        $rs  = $conn->Execute($sql);
        $i   = 0;
        while (! $rs->EOF) {
            $options[$i++] = $rs->fields;
            $rs->MoveNext();
        }
        ##

        //取出入款金額
        $ExpenseMoney = 0;

        $sql          = 'SELECT SUBSTR(eLender,1,13) as ExpenseMoney FROM tExpense WHERE id="' . $eid . '";';
        $rs           = $conn->Execute($sql);
        $ExpenseMoney = (int) $rs->fields['ExpenseMoney'];
        /*
        for ($i = 0 ; $i < count($list) ; $i ++) {
        $sql = 'SELECT SUBSTR(eLender,1,13) as ExpenseMoney FROM tExpense WHERE id="'.$list[$i]['eExpenseId'].'";' ;
        $rs = $conn->Execute($sql) ;
        $list[$i]['ExpenseMoney'] = (int)$rs->fields['ExpenseMoney'] ;
        }
         */
        ##
        //取增值稅
        $sql = "SELECT cAddedTaxMoney FROM tContractIncome WHERE cCertifiedId = '" . $cid . "'";
        $rs  = $conn->Execute($sql);

        $AddedTaxMoney = $rs->fields['cAddedTaxMoney'];
        //銀行判定

        $sql  = "SELECT cBank FROM tContractCase WHERE cCertifiedId = '" . $cid . "'";
        $rs   = $conn->Execute($sql);
        $Bank = $rs->fields['cBank'];
    ?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>款項明細</title>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	<?php echo $str ?>
	recalMoney() ;

	$('.recount').keyup(function() {
		recalMoney() ;
	}) ;
}) ;

/* 重新計算金額 */
function recalMoney() {
	var tt = 0 ;
	$('.recount').each(function() {
		var tm = $(this).val() ;
		re = /^[0-9]+$/ ;
		if (re.test(tm)) {
			tt = tt + parseInt(tm) ;
			$('#recal').html(tt) ;
		}
	}) ;
}
//

/* 更新明細紀錄 */
function update(sid) {
	var t = $('[name="target_' + sid +'"]').val() ;
	var i = $('[name="item_' + sid +'"]').val() ;
	var m = $('[name="money_' + sid +'"]').val() ;
	var bank =	           <?php echo $Bank ?>;

	if ((t == '')||(i == '')||(m == '')) {
		alert('輸入資料有誤、不可為空白!!請確認輸入資訊!!') ;
		return false ;
	}
	else {
		var re = /^[0-9]+$/ ;
		if (!re.test(m)) {
			alert('輸入金額非數字!!請重新輸入。') ;
			$('[name="money_' + sid +'"]').focus() ;
			$('[name="money_' + sid +'"]').select() ;
			return false ;
		}
		else {
			$('[name="op"]').val('update') ;
			$('[name="sid"]').val(sid) ;
			$('[name="tg"]').val(t) ;
			$('[name="it"]').val(i) ;
			$('[name="mn"]').val(m) ;
			if (bank == '68') {
				$('[name="ob"]').val($('[name="ObjKind2_' + sid +'"]').val());
			}
			$('[name="myform"]').submit() ;
		}
	}
}
////

/* 刪除明細紀錄 */
function del(sid) {
	$('[name="op"]').val('del') ;
	$('[name="sid"]').val(sid) ;

	if (confirm("請再次確認是否要刪除該筆紀錄?")==true) {
		$('[name="myform"]').submit() ;
	}
}
////

/* 新增明細紀錄 */
function addNew() {
	var t = $('[name="newTarget"]').val() ;
	var i = $('[name="newItem"]').val() ;
	var m = $('[name="newMoney"]').val() ;
	var bank =	           <?php echo $Bank ?>;

	if ((t == '')||(i == '')||(m == '')) {
		alert('相關欄位不可為空白!!請確認輸入!!') ;
		return false ;
	}
	else {
		var re = /^[0-9]+$/ ;
		if (!re.test(m)) {
			alert('輸入金額非數字!!請重新輸入。') ;
			$('[name="newMoney"]').focus() ;
			$('[name="newMoney"]').select() ;
			return false ;
		}
		else {
			$('[name="op"]').val('add') ;
			$('[name="tg"]').val(t) ;
			$('[name="it"]').val(i) ;
			$('[name="mn"]').val(m) ;
			// $('[name="ob"]').val($('[name="ob"]').val());
			if (bank == '68') {
				$('[name="ob"]').val($('[name="newObjKind2"]').val());
			}

			$('[name="myform"]').submit() ;
		}
	}
}
function checkobjkind(n){

	var bank =	           <?php echo $Bank ?>;

	if (bank == 68) {
		if (n =='new') {

			var val = $("[name='"+n+"Target']").val();
			var name = n+'ObjKind2';
		}else{
			var val = $("[name='item_"+n+"']").val();
			var name = 'ObjKind2'+n;
		}

		// console.log(val+'_'+name);

		if (val == 2) {
			//obItem
			//台新暫時不抓值皆出代書的帳戶，開放不代墊
			// $('[name="'+name+'"]').html("<option value=\"01\">申請公司代墊</option>");
			$('[name="'+name+'"]').html("<option value=\"03\">不用代墊</option><option value=\"01\">申請公司代墊</option>");
		}else{
			$('[name="'+name+'"').html("<option value=\"03\">不用代墊</option><option value=\"01\">申請公司代墊</option>");

		}

	}



}
////
</script>
</head>
<body>
<form method="POST" name="closeform" action="buyerowner_detail.php">
	<input type="hidden" name="sn" value="<?php echo $cid ?>">
</form>
<form method="POST" name="myform">
	<input type="hidden" name="op">
	<input type="hidden" name="eid" value="<?php echo $eid ?>">
	<input type="hidden" name="cid" value="<?php echo $cid ?>">

	<input type="hidden" name="sid">
	<input type="hidden" name="tg">
	<input type="hidden" name="it">
	<input type="hidden" name="mn">
	<input type="hidden" name="ob">
</form>
<div style="height:50px;">
	入款總金額：<span id="totalcount" style="font-weight:bold;font-size:14pt;color:red;"><?php echo number_format($ExpenseMoney) ?></span> 元整、
	已分配金額：<span id="recal" style="font-weight:bold;font-size:14pt;color:blue;">0</span> 元整<br>
	預估一般增值稅： <span style="font-weight:bold;font-size:14pt;color:green;"><?php echo number_format($AddedTaxMoney) ?></span>元
</div>
<table>
	<tr>
		<td>對象</td><td>用途類別</td><td>金額</td><td>&nbsp;</td>
	</tr>
<?php
    if (is_array($list) && count($list) > 0) {
            for ($i = 0; $i < count($list); $i++) {
                $disabled = '';
                if ($list[$i]['eOK'] != '') {
                    $disabled = ' disabled="disabled"';
                }
            ?>
	<tr>
		<td>
			<!-- <select name="target_<?php echo $list[$i]['eId'] ?>"<?php echo $disabled ?> style="width:100px;"> -->
			<select name="target_<?php echo $list[$i]['eId'] ?>" style="width:100px;">
			<?php
                echo '<option value="2"';
                            if ($list[$i]['eTarget'] == '2') {
                                echo ' selected="selected"';
                            }
                            echo ">賣方</option>\n";
                            echo '<option value="3"';
                            if ($list[$i]['eTarget'] == '3') {
                                echo ' selected="selected"';
                            }
                            echo ">買方</option>\n";
                        ?>
			</select>
		</td>
		<td>
			<select name="item_<?php echo $list[$i]['eId'] ?>"<?php echo $disabled ?> style="width:120px;" onchange="checkobjkind('<?php echo $list[$i]['eId'] ?>')">
			<?php
                for ($j = 0; $j < count($options); $j++) {
                                echo '<option value="' . $options[$j]['cId'] . '"';
                                if ($options[$j]['cId'] == $list[$i]['eItem']) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $options[$j]['cName'] . "</option>\n";
                            }
                        ?>
			</select>
		</td>
		<?php if ($Bank == 68):
                            // $list[$i]['eObjKind2'] = '03';
                        ?>
					<td>
						<select name="ObjKind2_<?php echo $list[$i]['eId'] ?>" id="">
						<option value="03"						                   <?php echo($list[$i]['eObjKind2'] == '03') ? 'selected=selected' : '' ?>>不用代墊</option>
						<option value="01"						                   <?php echo($list[$i]['eObjKind2'] == '01') ? 'selected=selected' : '' ?>>申請公司代墊</option>

				    	</select>
					</td>
				<?php endif?>
		<td>
			<input type="text" style="width:150px;text-align:right;"<?php echo $disabled ?> class="recount" name="money_<?php echo $list[$i]['eId'] ?>" value="<?php echo $list[$i]['eMoney'] ?>">
		</td>
		<td style="text-align:center;">
			<input type="button" value="更新" onclick="update('<?php echo $list[$i]['eId'] ?>')">
			<input type="button"<?php echo $disabled ?> value="刪除" onclick="del('<?php echo $list[$i]['eId'] ?>')">
		</td>
	</tr>
<?php
    }
        } // 結束 if (is_array($list) && count($list) > 0) 檢查
    ?>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4"><hr></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>
			<select name="newTarget" style="width:100px;" onchange="checkobjkind('new')">
				<option value="" selected="selected">請選擇...</option>
				<option value="2">賣方</option>
				<option value="3">買方</option>
			</select>
		</td>
		<td>
			<select name="newItem" style="width:120px;">
			<option value="" selected="selected">請選擇...</option>
			<?php
                for ($j = 0; $j < count($options); $j++) {
                        echo '<option value="' . $options[$j]['cId'] . '"';
                        //if ($j == 0) {
                        //    echo ' selected="selected"' ;
                        //}
                        echo '>' . $options[$j]['cName'] . "</option>\n";
                    }
                ?>
			</select>
		</td>
		<?php if ($Bank == 68): ?>
			<td>
				<select name="newObjKind2" id="">
					<option value="">代墊項目</option>
				</select>

			</td>
		<?php endif?>
		<td>
			<input type="text" style="width:150px;text-align:right;" class="recount" name="newMoney" value="">
		</td>
		<td style="text-align:center;">
			<input type="button" style="width:80px;" value="新增" onclick="addNew()">
		</td>
	</tr>
</table>

</body>
</html>



<?php

    } else {
        echo "非預期錯誤!!";
    }

    function checklist($cid)
    {
        global $conn;

        $sql = 'SELECT
				c.cCertifiedId,
				cc.cEscrowBankAccount
			FROM tChecklist AS c
			LEFT JOIN
				tContractCase AS cc ON c.cCertifiedId=cc.cCertifiedId
			WHERE c.cCertifiedId="' . $cid . '";';
        $rs_checklist = $conn->Execute($sql);

        //確認點交單是否發布
        $checkChecklist = 0;
        $sql            = "SELECT tId FROM tUploadFile WHERE tCertifiedId = '" . $cid . "'";
        $rs_upload      = $conn->Execute($sql);
        $checkChecklist = $rs_upload->RecordCount();

        if (! $rs_checklist->EOF && $rs_checklist->fields['cCertifiedId'] and $checkChecklist == 0) { //如果有開立過點交表 & 尚未發布

            $sql = "DELETE FROM tChecklistBlist  WHERE bCertifiedId ='" . $cid . "'";
            $conn->Execute($sql);
            // echo $sql."<br>";

            checklistBlist($cid, $rs_checklist->fields['cEscrowBankAccount']);
            // echo "<hr>";
            $sql = "DELETE FROM tChecklistOlist  WHERE oCertifiedId ='" . $cid . "'";
            $conn->Execute($sql);

            // echo $sql."<br>";
            checklistOlist($cid, $rs_checklist->fields['cEscrowBankAccount']);

        }

    }

    function checklistBlist($cid, $tVR_Code)
    {
        global $conn;
        $sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eStatusIncome,
			eBuyerMoney,
			eRemarkContent
		FROM
			tExpense AS a
		WHERE
			eDepAccount="00' . $tVR_Code . '"
			AND eTradeStatus="0"
			AND ePayTitle<>"網路整批"
		ORDER BY
			eTradeDate,eTradeNum
		ASC;
	';

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $_money1 = (int) substr($rs->fields["eLender"], 0, 13);
            $_money2 = (int) substr($rs->fields["eDebit"], 0, 13);
            $_y      = substr($rs->fields["eTradeDate"], 0, 3) + 1911;
            $_m      = substr($rs->fields["eTradeDate"], 3, 2);
            $_d      = substr($rs->fields["eTradeDate"], 5, 2);
            $_date   = $_y . "/" . $_m . "/" . $_d;

            if ($rs->fields["eStatusIncome"] != "3") { // 調帳交易不顯示
                $arr[] = [
                    'date'        => $_date,
                    'money1'      => $_money1,
                    'money2'      => $_money2,
                    'kind'        => $rs->fields['sName'],
                    'txt'         => $rs->fields['eRemarkContent'],
                    'eBuyerMoney' => $rs->fields['eBuyerMoney'],
                    'expId'       => $rs->fields['id'],
                ];
            }

            $rs->MoveNext();
        }

        //設定變更出款日期
        $sql = 'SELECT tBankLoansDate AS tExport_time FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="扣繳稅款";';
        $rs  = $conn->Execute($sql);

        $tmp_date = explode("-", substr($rs->fields['tExport_time'], 0, 10));
        if (count($tmp_date) > 0) {
            $exp_date = implode('/', $tmp_date);
        }
        unset($tmp_date);
        ##

        //
        foreach ($arr as $k => $v) {
            if (! $exp_date) {
                $exp_date = $v['date'];
            }

            //取出款項明細
            $sql = '
			SELECT
				*,
				(SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind
			FROM
				tExpenseDetail AS a
			WHERE
				eExpenseId="' . $v['expId'] . '"
				AND eTarget="3";
		';
            $rs = $conn->Execute($sql);

            while (! $rs->EOF) {
                $tmp_date                     = explode("-", substr($rs->fields['tBankLoansDate'], 0, 10));
                $rs->fields['tBankLoansDate'] = $tmp_date[0] . "/" . $tmp_date[1] . "/" . $tmp_date[2];

                $c[] = [
                    'date'   => $rs->fields['tBankLoansDate'],
                    'money1' => 0,
                    'money2' => $rs->fields['eMoney'],
                    'kind'   => $rs->fields['kind'],
                    'expId'  => $v['eExpenseId'],
                ];

                unset($tmp_date);

                $rs->MoveNext();
            }

            //主要入款紀錄
            $c[] = [
                'date'   => $v['date'],
                'money1' => $v['money1'],
                'money2' => $v['money2'],
                'kind'   => $v['kind'],
                'txt'    => $v['txt'],
                'expId'  => $v['expId'],
            ];
            ##
        }
        unset($arr);
        ##

        //
        $sql = 'SELECT * FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="仲介服務費" AND tBuyer<>"" AND tPayOk="1";';

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $rs->fields['tBankLoansDate'] = implode('/', explode('-', substr($rs->fields['tBankLoansDate'], 0, 10)));

            $c[] = [
                'date'   => $rs->fields['tBankLoansDate'],
                'money1' => 0,
                'money2' => $rs->fields['tBuyer'],
                'kind'   => $rs->fields['tObjKind'],
                'txt'    => $rs->fields['tTxt'],
                'expId'  => '',
            ];

            $rs->MoveNext();
        }

        //依日期進行排序(由小到大)
        usort($c, function ($x1, $x2) {
            $y1 = strtotime($x1['date']);
            $y2 = strtotime($x2['date']);
            return $v1 - $v2;
        });
        ##

        // 寫入資料庫
        ## 寫入 tChecklistBlist
        if (! empty($c)) {
            foreach ($c as $k => $v) {
                $sql = '
				INSERT INTO	tChecklistBlist
				(
					bCertifiedId,
					bDate,
					bKind,
					bIncome,
					bExpense,
					bRemark
				)
				VALUES
				(
					"' . $cid . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';
                // echo $sql."<br>";
                $conn->Execute($sql);
            }
        }
        ##
        //
    }

    function checklistOlist($cid, $tVR_Code)
    {
        global $conn;

        $b   = [];
        $sql = '
		SELECT
			tVR_Code,
			tObjKind,
			tTxt,
			tMoney,
			tSeller,
			tExport_time
		FROM
			tBankTrans
		WHERE
			tVR_Code="' . $tVR_Code . '" ;
	';

        // echo $sql."<bR>";
        // die;

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $list[] = $rs->fields;

            $rs->MoveNext();
        }

        // echo "<pre>";
        // print_r($list);
        // echo "</pre>";

        for ($i = 0; $i < count($list); $i++) {
            $_money2 = (int) $list[$i]["tMoney"];
            $_total  = $_money2;
            $_name   = $list[$i]["tTxt"];

            $_y    = substr($list[$i]["tExport_time"], 0, 4);
            $_m    = substr($list[$i]["tExport_time"], 5, 2);
            $_d    = substr($list[$i]["tExport_time"], 8, 2);
            $_date = $_y . "/" . $_m . "/" . $_d;

            if ($list[$i]["tObjKind"] == '仲介服務費') {
                $_name   = '';
                $_money2 = (int) $list[$i]['tSeller'];
            }

            if ($list[$i]["tObjKind"] == '扣繳稅款') {
                $sql = 'SELECT * FROM tExpenseDetail WHERE eCertifiedId="' . substr($tVR_Code, 5, 9) . '";';

                $rs = $conn->Execute($sql);

                $total = $rs->RecordCount();

                if ($total < 1) {
                    $b[] = [
                        'date'   => $_date,
                        'money1' => '0',
                        'money2' => $_money2,
                        'kind'   => $list[$i]["tObjKind"],
                        'txt'    => $_name,
                        'expId'  => $rs->fields['expId'],
                    ];

                }

            } else if ($list[$i]["tObjKind"] != '調帳') {
                //主要入款紀錄
                $b[] = [
                    'date'   => $_date,
                    'money1' => '0',
                    'money2' => $_money2,
                    'kind'   => $list[$i]["tObjKind"],
                    'txt'    => $_name,
                    'expId'  => $rs->fields['expId'],
                ];

                ##
            }
        }

        // 寫入資料庫
        ## 寫入 tCheckOlist (出款)
        if (! empty($b)) {
            foreach ($b as $k => $v) {
                $sql = '
				INSERT INTO	tChecklistOlist
				(
					oCertifiedId,
					oDate,
					oKind,
					oIncome,
					oExpense,
					oRemark
				)
				VALUES
				(
					"' . $cid . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';
                // echo $sql."<br>";
                $conn->Execute($sql);
            }
        }

        unset($list);unset($b);
        ##

        $sql = '
		SELECT
			id,
			eTradeDate,
			eDebit,
			eLender,
			eChangeMoney,
			eStatusIncome,
			eBuyerMoney,
			eDepAccount,
			(SELECT sName FROM tCategoryIncome WHERE sId=a.eStatusRemark) as sName,
			eRemarkContent
		FROM
			tExpense AS a
		WHERE
			eDepAccount="00' . $tVR_Code . '"
			AND eTradeStatus="0"
			AND ePayTitle<>"網路整批"
		ORDER BY
			eTradeDate,eTradeNum
		ASC;
	';

        $rs = $conn->Execute($sql);

        while (! $rs->EOF) {
            $_money1 = (int) substr($rs->fields["eLender"], 0, 13);     // 存入
            $_money2 = (int) substr($rs->fields["eDebit"], 0, 13);      // 支出
            $_buyer  = (int) substr($rs->fields["eBuyerMoney"], 0, 13); // 扣除買方服務費

            if ($_buyer > 0) {$_money1 = $_money1 - $_buyer;}
            $_total = $_money1 - $_money2;
            $_y     = substr($rs->fields["eTradeDate"], 0, 3) + 1911;
            $_m     = substr($rs->fields["eTradeDate"], 3, 2);
            $_d     = substr($rs->fields["eTradeDate"], 5, 2);
            $_date  = $_y . "/" . $_m . "/" . $_d;

                                                       //if ($tmp["sName"] != '----' && $tmp["eStatusIncome"] !="3" ) {                 // kind 不明不顯示
            if ($rs->fields["eStatusIncome"] != "3") { // 調帳交易不顯示
                $arr[] = [
                    'date'   => $_date,
                    'money1' => $_money1,
                    'money2' => $_money2,
                    'total'  => $_total,
                    'kind'   => $rs->fields['sName'],
                    'txt'    => $rs->fields['eRemarkContent'],
                    'expId'  => $rs->fields['id'],
                ];
            }

            $rs->MoveNext();
        }

        //設定 tExpenseDetail 變更出款日期
        $sql = 'SELECT tExport_time FROM tBankTrans WHERE tVR_Code="' . $tVR_Code . '" AND tObjKind="扣繳稅款";';

        $rs = $conn->Execute($sql);

        $tmp_date = explode("-", substr($rs->fields['tExport_time'], 0, 10));
        if (count($tmp_date) > 0) {
            $exp_date = implode('/', $tmp_date);
        }
        unset($tmp_date);
        ##

        foreach ($arr as $k => $v) {
            //$totalM = 0 ;

            //取得明細部分買方分配總金額並將賣方入帳金額扣除買方支出
            $sql = 'SELECT SUM(eMoney) as M FROM tExpenseDetail WHERE eExpenseId="' . $v['expId'] . '" AND eTarget="3"; ';

            $rs = $conn->Execute($sql);
            $v['money1'] -= (int) $rs->fields['M']; //扣除買方明細加總金額
            unset($tmp);
            ##

            //取出賣方明細部分出款
            $sql = '
			SELECT
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as kind,
				(SELECT tBankLoansDate FROM  tBankTrans WHERE tId=a.eOK) AS tBankLoansDate
			FROM
				tExpenseDetail AS a
			WHERE
				eExpenseId="' . $v['expId'] . '"
				AND eTarget="2";
		';

            $rs = $conn->Execute($sql);

            while (! $rs->EOF) {
                $money2 = (int) $rs->fields['eMoney'];

                if (! $exp_date) {
                    $exp_date = $v['date'];
                }

                $tmp_date                     = explode("-", substr($rs->fields['tBankLoansDate'], 0, 10));
                $rs->fields['tBankLoansDate'] = $tmp_date[0] . "/" . $tmp_date[1] . "/" . $tmp_date[2];
                unset($tmp_date);

                $a[] = [
                    'date'   => $rs->fields['tBankLoansDate'],
                    'money1' => 0,
                    'money2' => $money2,
                    'kind'   => $rs->fields['kind'],
                    'expId'  => $v['eExpenseId'],
                ];

                $rs->MoveNext();
            }

            //主要入款紀錄
            //$v['money1'] -= $totalM ;
            $a[] = [
                'date'   => $v['date'],
                'money1' => $v['money1'],
                'money2' => $v['money2'],
                'kind'   => $v['kind'],
                'txt'    => $v['txt'],
                'expId'  => $v['expId'],
            ];
            ##
        }
        unset($arr);
        ##

        // 寫入資料庫
        ## 寫入 tCheckOlist (收款)
        if (! empty($a)) {
            foreach ($a as $k => $v) {
                $sql = '
				INSERT INTO	tChecklistOlist
				(
					oCertifiedId,
					oDate,
					oKind,
					oIncome,
					oExpense,
					oRemark
				)
				VALUES
				(
					"' . $cid . '",
					"' . $v['date'] . '",
					"' . $v['kind'] . '",
					"' . $v['money1'] . '",
					"' . $v['money2'] . '",
					"' . n_to_w($v['txt']) . '"
				) ;
			';
                // echo $sql."<br>";
                $conn->Execute($sql);
            }
        }
        ##
        //
    }
                                           //半形<=>全形
    function n_to_w($strs, $types = '0')
    { // narrow to wide , or wide to narrow
        $nt = [
            "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
            "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
            "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
            "^", "_",
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
            "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
            "u", "v", "w", "x", "y", "z",
            "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
            "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
            "U", "V", "W", "X", "Y", "Z",
            " ",
        ];
        $wt = [
            "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
            "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
            "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
            "︿", "＿",
            "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
            "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
            "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
            "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
            "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
            "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
            "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
            "　",
        ];

        if ($types == '0') { //半形轉全形
                                 // narrow to wide
            $strtmp = str_replace($nt, $wt, $strs);
        } else { //全形轉半形
                     // wide to narrow
            $strtmp = str_replace($wt, $nt, $strs);
        }
        return $strtmp;
    }
##
?>