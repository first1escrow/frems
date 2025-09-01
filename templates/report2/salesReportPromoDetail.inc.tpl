<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<link rel="stylesheet" href="../css/colorbox.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery.autocomplete.css" />
<script src="../js/jquery-1.7.2.min.js"></script>
<script src="../js/jquery.colorbox.js"></script>
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	
});

function save() {
    let id = $('[name="sId"]').val();
    let date = $('[name="sDate"]').val();
    let store = $('[name="sStore"]').val();
    let media = $('[name="sMedia"]').val();

    if (!date) {
        alert('請輸入日期');
        return;
    }

    if (!store) {
        alert('請輸入店編號');
        return;
    }

    if (!media && !id) {
        alert('請選取欲上傳的檔案');
        return;
    }

    $('#update').hide();
    $('#del').hide();
    $('#check').hide();
    $('#go_back').hide();

    $('#form1').submit();
}

function back() {
    location.replace('salesReportPromo.php'); 
}

function delA(id) {
    if (confirm('確認是否刪除?') == true) {
        $.ajax({
            type:   "POST",
            url:    "salesReportPromoDelete.php",
            data:   {"id": id},
            success: function (response) {
                alert('已刪除');
                back();
            },
            error: function (request, error) {
                console.log ("ERROR:" + error);
                alert('系統異常！請稍後再試');
            }
        });
    }

    return false;
}

<{if $smarty.session.member_id|in_array:[1,3,6]}>
function checkA(id) {
    let _confirm = 'R';
    if (confirm('確認是否確認通過?') === true) {
        _confirm = 'Y';
    } else {
        _confirm = 'R';
    }
    
    $.ajax({
        type:   "POST",
        url:    "salesReportPromoConfirm.php",
        data:   {"id": id, "confirm": _confirm},
        success: function (response) {
            alert('完成');
            location.replace(location.href);
        },
        error: function (request, error) {
            console.log ("ERROR:" + error);
            alert('系統異常！請稍後再試');
            location.replace(location.href);
        }
    });
}
<{/if}>
</script>
<style>
	.btn {
		padding:5px 10px 5px 10px ;
		color:#212121 ;
		background-color:#F8ECE9 ;
		margin:2px ;
		border:1px outset #F8ECE0 ;
		cursor:pointer ;
	}

	.btn:hover {
		padding:5px 10px 5px 10px ;
		color:#212121 ;
		background-color:#EBD1C8 ;
		margin:2px;
		border:1px outset #F8ECE0;
		cursor:pointer;
	}

    #tbl1 tr:nth-child(odd) {
        background: #F8ECE9;
    }

    #tbl1 tr {
        height: 40px;
    }
</style>
</head>
<body id="dt_example">
    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                            </tr>
                            <tr>
                                <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                            </tr>
                        </table></td>
                </tr>
            </table> 
        </div>
        <{include file='menu1.inc.tpl'}>
        <table width="1000" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <td bgcolor="#DBDBDB">
                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                        <tr>
                            <td height="17" bgcolor="#FFFFFF">
                                <div id="menu-lv2">
                                                
                                </div>
                                <br/> 
                                <h3></h3>
                                    <h1>課程推廣登錄</h1>

                                    <form method="POST" id="form1" enctype="multipart/form-data">
                                        <div style="margin: 0px auto;width: 500px;">
                                            <input type="hidden" name="sId" value="<{$sId}>">
                                            <div style="margin: 10px; border: 1px solid #CCC; padding: 5px;">
                                                <table id="tbl1" style="width:98%;">
                                                    <tr>
                                                        <th>日期</th>
                                                        <td><input type="date" name="sDate" value="<{$data.sDate}>"></td>
                                                        <th>業務</th>
                                                        <td><{html_options name="sSales" options=$menu_sales selected=$data.sSales}></td>
                                                    </tr>
                                                    <tr>
                                                        <th>店編號</th>
                                                        <td colspan="3">
                                                            <input list="stores" name="sStore" style="width: 96%;" value="<{$data.sStore}>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>備註</th>
                                                        <td colspan="3"><textarea name="sMemo" rows=5 style="width: 94%;margin:10px;"><{$data.sMemo}></textarea></td>
                                                    </tr>
                                                    <tr>
                                                        <th>確認狀態</th>
                                                        <td colspan="3">
                                                            <{if $data.sConfirmed == 'Y'}>
                                                            已確認
                                                            <{elseif $data.sConfirmed == 'R'}>
                                                            已駁回
                                                            <{else}>
                                                            待確認
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>上傳檔案</th>
                                                        <td colspan="3">
                                                            <span style="text-align: left;"><input type="file" name="sMedia" accept="image/*, video/*"></span>
                                                            <{if $data.sMedia}>
                                                            <span style="text-align: right;"><a href="/public/<{$data.sMedia}>" target="_blank"><img style="width:25px;vertical-align: middle;" src="/images/multimedia.png" title="點我查看多媒體檔案"></a></span>
                                                            <{/if}>
                                                        </td>
                                                    </tr>
                                                    <tr style="background-color: #FFFFFF;">
                                                        <td colspan="4">
                                                            <div style="text-align: center;padding-top: 20px;">
                                                                <{if $data.sConfirmed == 'N'}>
                                                                <input type="button" id="update" class="btn" value="儲存" onclick="save()">　
                                                                    <{if $sId}>
                                                                    <input type="button" id="del" class="btn" value="刪除" onclick="delA('<{$sId}>')">　
                                                                    <{/if}>
                                                                    <{if $smarty.session.member_id|in_array:[1,3,6]}>
                                                                    <input type="button" id="check" class="btn" value="審核" onclick="checkA('<{$sId}>')">　
                                                                    <{/if}>
                                                                <{/if}>
                                                                <input type="button" id="go_back" class="btn" value="返回" onclick="back()">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                </table>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
            
        <div id="footer" style="height:50px;">
        <p>2012 第一建築經理股份有限公司 版權所有</p>
        </div>

    </div>

    <datalist id="stores">
<{foreach from=$stores key=key item=item}>
    <option value="<{$item.name}>(<{$item.id}>)">
<{/foreach}>
    </datalist>
</body>
</html>
<{$alert}>