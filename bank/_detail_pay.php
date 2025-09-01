<?php
require_once dirname(__DIR__) . '/openadodb.php';
require_once dirname(__DIR__) . '/session_check.php';

$sn = $_REQUEST["sn"];
$ts = $_REQUEST["ts"];
$tm = $_REQUEST["tm"];

$sql   = "select * from tBankTrans where tExport_nu='$sn' AND tObjKind2 = '02' ORDER BY tId ASC";
$rs    = $conn->Execute($sql);
$total = $rs->RecordCount();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>銀行付款確認</title>
<style type="text/css">
.font12 {
    font-size:13px;
}
</style>
<script>
var v= new Array();

function checkAll(action) {
    event.preventDefault();

	var allChkbox = $('input[name^="pay_"]');
	jQuery.each(allChkbox, function(i, singlecheckbox) {
        if (action == 'A') {
			_name = singlecheckbox.id;
			$('input[name="' + _name + '"]').attr("checked", true);
			v.push(singlecheckbox.value);
		} else if (action == 'N'){
			_name = singlecheckbox.id;
			$('input[name="' + _name + '"]').attr("checked", false);
		}
	});

    if (action == 'A') {
        $('#select_all').attr('onclick', "checkAll('N')");
        $('#select_all').empty().html('全不選');
    } else if (action == 'N'){
        v = [];
        $('#select_all').attr('onclick', "checkAll('A')");
        $('#select_all').empty().html('全選');
    }

    return ;
}

function get_checked() {
    v = [];

	var allChkbox = $('input[name^="pay_"]');
	jQuery.each(allChkbox, function(i, singlecheckbox) {
        if (singlecheckbox.checked) {
			v.push(singlecheckbox.value);
		}
	});

    // console.log(v);
}

function save_sms() {
    event.preventDefault();

    if (v.length > 0) {
        if (confirm('確認是否送出並發送簡訊?')) {
            var jsonText = JSON.stringify({ datas: v });

            url = '/bank/_module_paySms_save_json.php?sn=<?=$sn?>&json=' + jsonText;
            $.ajax({
                url: url,
                error: function(xhr) {
                    alert ("系統忙碌中！");
                    v = [];
                },
                success: function(response) {
                    alert("已全部更新!");
                    v = [];
                }
            });
        }
    } else {
        alert('請先選取審核與發送的案件');
    }
}

function save_check(){
    event.preventDefault();

    if (v.length > 0) {
        var jsonText = JSON.stringify({ datas: v});
        var url = '/bank/_module_pay_save.php';

        $.ajax({
            url: url,
            type: 'POST',
            data: {"json": jsonText, "ts": new Date().getTime()},
            error: function(xhr) {
                alert ("系統忙碌中！");
                v = [];
            },
            success: function(response) {
                if (response == 200) {
                    alert("已全部更新!");
                    v = [];
                } else {
                    if (response == 401) {
                        alert('查無審核紀錄');
                    } else if (response == 400) {
                        alert('系統異常');
                    } else {
                        alert('其他錯誤');
                    }
                }

                location.reload();
            }
        });
    } else {
        alert('請先選取審核的案件');
    }
}

function close_tip(x){
	var _obj = "#txt_" + x;
	var _obj2 = "#sms_txt_" + x;

	$(_obj).hide();
}

function show_tip(x,y){
	var _obj = "#txt_" + x;
	var _obj2 = "#sms_txt_" + x;

	var url = '/sms/test2.php?tid='+x + '&yn=' + y ;
	$.ajax({
        url: url,
        error: function(xhr) {
            alert ("系統忙碌中！");
        },
        success: function(response) {
            $(_obj2).html(response);
        }
    });

	$(_obj).show();
}

</script>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <table width="1016" border="0" cellpadding="1" cellspacing="1" class="font12" id="ttt">
    <tr>
      <td colspan="2"> <strong> 媒體檔匯出時間:</strong> <font color=red><?php echo $ts; ?></font> , 金額共 <font color=red><?php echo $tm; ?></font> 元整.(<?php echo $sn; ?>)<input name="save" type="hidden" id="save" value="ok" /></td>
      <td width="188">[<a href="_quit_pay.php?sn=<?php echo $sn; ?>&ts=<?php echo $ts; ?>&tm=<?php echo $tm; ?>&p=ok">媒體檔退回</a>]</td>
      <td width="161">&nbsp;</td>
      <td width="174">&nbsp;</td>
      <td width="68">&nbsp;</td>
      <td width="39">&nbsp;</td>
    </tr>
    <?php

$sql     = "select * from tBankTrans where tExport_nu='$sn' ORDER BY tId ASC";
$rs      = $conn->Execute($sql);
$_total  = $rs->RecordCount();
$_error  = 0;
$_pay_ok = 0;
$_pay_no = 0;
while (!$rs->EOF) {
    $_target = $rs->fields["tBank_kind"];
    //echo $_target;
    //------
    if ($rs->fields["tOk"] != '1') {$_error++;}
    //------
    switch ($rs->fields["tCode"]) {
        case "01":
            $_title = "聯行轉帳";
            break;
        case "02":
            $_title = "跨行代清償";
            break;
        case "03":
            $_title = "聯行代清償";
            break;
        case "04":
            $_title = "大額繳稅";
            break;
        case "05":
            $_title = "臨櫃開票";
            break;
        case "06":
            $_title = "利息";
            break;
    }
    $bank3 = substr($rs->fields["tBankCode"], 0, 3);
    $bank4 = substr($rs->fields["tBankCode"], 3, 4);

    $sql         = "select * from tBank where bBank3='$bank3' and bBank4='' limit 1";
    $rs1         = $conn->Execute($sql);
    $_bank_title = $rs1->fields["bBank4_name"];
    //
    $sql = "select * from tBank where bBank3='$bank3' and bBank4='$bank4'  limit 1";
    // echo $sql;
    $rs2           = $conn->Execute($sql);
    $_bank_cotitle = $rs2->fields["bBank4_name"];

    if ($rs->fields["tPayOk"] == '1') {$_pay_ok++;}
    if ($rs->fields["tPayOk"] == '2') {$_pay_no++;}
    ?>
    <tr id="tr_pos">
        <td width="163"><label for="target[]"></label>
            <strong>類別</strong>  <?php echo $rs->fields["tKind"]; ?> <br />
            <strong>交易類別</strong> <?php echo $_title; ?><br />
            <strong>項目</strong> <?php echo $rs->fields["tObjKind"]; ?>
        </td>

        <td width="201"><strong>解匯行</strong> <?php echo str_replace("　", "", $_bank_title); ?> <br />
            <strong>分行別</strong> <?php echo str_replace("　", "", $_bank_cotitle); ?>
        </td>
        <td>
            <strong>戶名</strong> <?php echo $rs->fields["tAccountName"]; ?> <br />
            <strong>證號</strong> <?php echo $rs->fields["tAccountId"]; ?>
        </td>
        <td>
            <strong>帳號</strong> <?php echo $rs->fields["tAccount"]; ?>
        </td>
        <td>
            <strong>金額</strong> NT$ <font color="red"><?php echo $rs->fields["tMoney"]; ?></font>元(不含匯費)
        </td>
        <td>
            <label for="check[]"></label>
            <?php
if ($rs->fields["tPayOk"] != '1') {
        ?>

            <input name="pay_<?=$rs->fields["tId"]?>" type="checkbox" id="pay_<?=$rs->fields["tId"]?>" onclick="get_checked()" value="<?=$rs->fields["tId"]?>" <?php if ($rs->fields["tPayOk"] == '1') {echo 'checked="checked"';}?> />
            <?php
} else {
        echo "已確認出帳!";
        if ($rs->fields["tPayTxt"] != "") {
            echo "(<font color=red>" . $rs->fields["tPayTxt"] . "</font>)";
        }
    }
    ?>

            <label for="pay"></label>
            <input name="bid[]" type="hidden" id="bid[]" value="<?php echo $rs->fields["tId"]; ?>" />
        </td>
        <td >
            <?php
if (trim($rs->fields["tObjKind"]) != '其他') {
        ?>

            <a href="Javascript: show_tip('<?=$rs->fields["tId"]?>','n');">預覽簡訊</a>
            <?php
}
    ?>
        </td>
    </tr>
    <tr>
      <td><strong>附言(備註)</strong></td>
      <td colspan="2"><?php echo $rs->fields["tTxt"]; ?></td>
      <td><strong>保證帳號</strong> <font color="red"><?php echo $rs->fields["tVR_Code"]; ?></font></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td >&nbsp;</td>
    </tr>
     <tr bgcolor="#FFFF00" id="txt_<?php echo $rs->fields["tId"]; ?>" style="display:none">
      <td colspan="7" align="right">預定簡訊發送內容: <span id="sms_txt_<?php echo $rs->fields["tId"]; ?>"></span></td>
    </tr>
    <tr>
      <td height="19" colspan="7"><hr /></td>
    </tr>
    <?php
$rs->MoveNext();
}
//echo $_target;
if ($_target == '遠東') {
    $_js = "open_w2('" . $vr_code . "');";
} else {
    $_js = "open_w('" . $vr_code . "');";
}
?>
  <tr>
      <td height="19" colspan="7" align="right">
	  <?php if ($_total != $_pay_ok) {?>
        <span style="margin-right: 50px;">
            <button id="select_all" style="width:60px;" onclick="checkAll('A')">全選</button>
        </span>
        <span style="margin-right: 10px;">
            <button style="width:100px;" onclick="save_check()">審核</button>
        </span>
        <span>
            <button style="width:100px;" onclick="save_sms()">審核並發簡訊</button>
        </span>
	  <?php }?>

      </td>
    </tr>
  </table>
</form>
</body>
</html>
