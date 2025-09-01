<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // $('.cmc_overlay').hide();
    // $('.cmc_overlay').show();
});

function colorbx(url) {
	$.colorbox({href:url});
}

</script>
<style>
.select-width {
    width: 150px;
}

.excel-download {
    position: relative;
    top: 5px;
}

#block1, #block2, #block3, #block4 {
    display: flex;
    justify-content: space-between;
    margin: 0 auto;
    padding: 10px;
    width: 500px;
}

#blocks-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    width: 100%;
}

#block1, #block2, #block3, #block4 {
    width: calc(50% - 40px);
    margin: 0;
}

.focus-on-block {
    border: 5px solid #3dda4a;
    border-radius: 15px;
}

.focus-off-block {
    border: 1px solid #CCC;
}

.btn {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 12px;
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.3s;
}

.btn:hover {
    background-color: #45a049;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

.btn:active {
    background-color: #3e8e41;
    box-shadow: 0 2px 4px 0 rgba(0,0,0,0.2);
    transform: translateY(2px);
}

.quitJob {
    /* color: red; */
    color: white;
    background-color: #cccccc;
}
</style>
</head>
<body id="dt_example">
    <div class="cmc_overlay" style="display:none;">
        <div class="cmc_overlay__inner">
            <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
        </div>
    </div>

    <div id="wrapper">
        <div id="header">
            <table width="1000" border="0" cellpadding="2" cellspacing="2">
                <tr>
                    <td width="233" height="72">&nbsp;</td>
                    <td width="753">
                        <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                            <tr>
                                <td colspan="3" align="right">
                                    <div id="abgne_marquee" style="display:none;">
                                        <ul>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">
                                    <h1><{include file='welcome.inc.tpl'}></h1>
                                </td>
                            </tr>
                            <tr>
                                <td width="81%" align="right"></td>
                                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                            </tr>
                        </table>
                    </td>
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
                                <div id="container">
                                    <h1 style="text-align:left;">假勤報表<{if $smarty.session.member_id|in_array: [13, 129] }><span style="margin-left:20px;"><a href="Javascript:void(0);" style="font-size:12px" onclick="showLockDate()">（鎖定報表日期）</a></span><{/if}></h1>
                                    <div>
                                        <form id="form1" method="post" target="_blank">
                                            <input type="hidden" name="report">
                                            <div id="blocks-container">
                                                <div id="block1" class="focus-off-block">
                                                    <label>
                                                        <div style="padding-bottom:15px;">指定起訖日期：</div>
                                                        <div>
                                                            <input type="date" name="from" value="<{$from}>"> ~ <input type="date" name="to" value="<{$to}>">
                                                        </div>
                                                        <div style="margin-top: 10px;">
                                                            對象：<select class="select-width" name="staff">
                                                                <option value="0">所有在職人員</option>
                                                                <{foreach from=$staffOptions item=staff}>
                                                                    <option value="<{$staff.pId}>" <{if $staff.pJob != 1}>class="quitJob"<{/if}><{if $staffSelected == $staff.pId}>selected<{/if}>><{$staff.pName}></option>
                                                                <{/foreach}>
                                                            </select>
                                                        </div>
                                                    </label>
                                                </div>
                                                <div id="block2" class="focus-off-block">
                                                    <label>
                                                        <div style="padding-bottom:15px;">指定日期：</div>
                                                        <span>
                                                            日期：<input type="date" name="date" value="<{$date}>">
                                                        </span>
                                                        
                                                    </label>
                                                </div>
                                                <{if $smarty.session.member_id|in_array:[129, 13]}>
                                                <div id="block3" class="focus-off-block">
                                                    <label>
                                                        <div style="padding-bottom:15px;">補打卡統計起訖日期：</div>
                                                        <span>
                                                            <input type="date" name="applyCheckFrom" value="<{$from}>"> ~ <input type="date" name="applyCheckTo" value="<{$to}>">
                                                        </span>

                                                    </label>
                                                </div>
                                                <div id="block4" class="focus-off-block">
                                                    <label>
                                                        <div style="padding-bottom:15px;">員工假別剩餘時數統計(即時)：</div>
                                                        <span>
                                                            對象：<select class="select-width" name="staff_b4">
                                                                <option value="0">所有在職人員</option>
                                                                <{foreach from=$staffOptions item=staff}>
                                                                    <option value="<{$staff.pId}>" <{if $staff.pJob != 1}>class="quitJob"<{/if}><{if $staffSelected == $staff.pId}>selected<{/if}>><{$staff.pName}></option>
                                                                <{/foreach}>
                                                            </select>
                                                        </span>
                                                    </label>
                                                </div>
                                                <{/if}>
                                            </div>
                                            <div style="text-align:center; margin-top: 20px;">
                                                <button type="button" id="download" class="btn" title="下載 Excel">下載報表</button>
                                            </div>
                                        </form>
                                    </div>
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

    
    <div id="colorbox" style="display:none;width:100%;padding:20px;text-align:center;">
        <div>
            鎖定 <input type="date" id="lockDate" value="<{$lockDate}>"> (含)前的紀錄。
        </div>
        <div style="margin-top:20px;">
            <button type="button" id="lockButton" style="padding:5px;" onclick="lockSubmit()">鎖定</button>
        <div>
    </div>
</body>
</html>
<script type="text/javascript">
$(document).ready(function() {
    $('#block1').click(function() {
        $('#block1').removeClass('focus-off-block').addClass('focus-on-block');
        $('#block2').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block3').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block4').removeClass('focus-on-block').addClass('focus-off-block');

        $('#block1 div').css('font-weight', 'bold');
        $('#block2 div').css('font-weight', 'normal');
        $('#block3 div').css('font-weight', 'normal');
        $('#block4 div').css('font-weight', 'normal');
        
        $('#block1 input').prop('disabled', false);
        $('#block1 select').prop('disabled', false);
        $('#block2 input').prop('disabled', true);
        $('#block3 input').prop('disabled', true);
        $('#block4 select').prop('disabled', true);

        $('[name="report"]').val(1);
    });

    $('#block2').click(function() {
        $('#block1').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block2').removeClass('focus-off-block').addClass('focus-on-block');
        $('#block3').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block4').removeClass('focus-on-block').addClass('focus-off-block');

        $('#block1 div').css('font-weight', 'normal');
        $('#block2 div').css('font-weight', 'bold');
        $('#block3 div').css('font-weight', 'normal');
        $('#block4 div').css('font-weight', 'normal');

        $('#block1 input').prop('disabled', true);
        $('#block1 select').prop('disabled', true);
        $('#block2 input').prop('disabled', false);
        $('#block3 input').prop('disabled', true);
        $('#block4 select').prop('disabled', true);

        $('[name="report"]').val(2);
    });

    <{if $smarty.session.member_id|in_array:[129, 13]}>
    $('#block3').click(function() {
        $('#block1').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block2').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block3').removeClass('focus-off-block').addClass('focus-on-block');
        $('#block4').removeClass('focus-on-block').addClass('focus-off-block');

        $('#block1 div').css('font-weight', 'normal');
        $('#block2 div').css('font-weight', 'normal');
        $('#block3 div').css('font-weight', 'bold');
        $('#block4 div').css('font-weight', 'normal');
        
        $('#block1 input').prop('disabled', true);
        $('#block1 select').prop('disabled', true);
        $('#block2 input').prop('disabled', true);
        $('#block3 input').prop('disabled', false);
        $('#block4 select').prop('disabled', true);

        $('[name="report"]').val(3);
    });

    $('#block4').click(function() {
        $('#block1').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block2').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block3').removeClass('focus-on-block').addClass('focus-off-block');
        $('#block4').removeClass('focus-off-block').addClass('focus-on-block');

        $('#block1 div').css('font-weight', 'normal');
        $('#block2 div').css('font-weight', 'normal');
        $('#block3 div').css('font-weight', 'normal');
        $('#block4 div').css('font-weight', 'bold');
        
        $('#block1 input').prop('disabled', true);
        $('#block1 select').prop('disabled', true);
        $('#block2 input').prop('disabled', true);
        $('#block3 input').prop('disabled', true);
        $('#block4 select').prop('disabled', false);

        $('[name="report"]').val(4);
    });
    <{/if}>

    $('#block1').click();

    $('#download').click(function() {
        let report = $('[name="report"]').val();
        if (report == 1) {
            let from = $('[name="from"]').val();
            let to = $('[name="to"]').val();

            if (from == '' || to == '') {
                alert('請選擇起訖日期與對象');
                return;
            }
        } 
        
        if (report == 2) {
            let BEGINDATE = "<{$BEGINDATE}>";
            let date = $('[name="date"]').val();
            if (date == '') {
                alert('請選擇日期');
                $('[name="date"]').focus();
                return;
            }

            if (date < BEGINDATE) {
                alert('日期不可小於 ' + BEGINDATE);
                $('[name="date"]').focus();
                return;
            }
        }

        <{if $smarty.session.member_id|in_array:[129, 13]}>
        if (report == 3) {
            let from = $('[name="applyCheckFrom"]').val();
            let to = $('[name="applyCheckTo"]').val();

            if (from == '' || to == '') {
                alert('請選擇起訖日期與對象');
                return;
            }
        } 

        if (report == 4) {
            let staff = $('[name="staff_b4"]').val();
            if (staff == '') {
                alert('請選擇對象');
                return;
            }
        }
        <{/if}>
        
        if (!report || [1, 2, 3, 4].includes(report)) {
            alert('請選擇報表類型');
            return;
        }

        let el = document.createElement('input');
        el.type = 'hidden';
        el.name = 'download';
        el.value = 1;

        document.getElementById('form1').appendChild(el);
        $('#form1').submit();
    });
});


function showLockDate() {
    $('#colorbox').show();
    $.colorbox({
        inline:true, 
        width:"400px", 
        height:"300px", 
        href:"#colorbox", 
        onClosed:function() {
            $('#colorbox').hide();
        }
    });
}

function lockSubmit() {
    let date = $('#lockDate').val();
    if (date == '') {
        alert('請選擇日期');
        $('#lockDate').focus();
        return;
    }

    let url = '/includes/staff/setLockDate.php';
    $.post(url, {'date': date}, function(response) {
        alert(response);
        $.colorbox.close();
    }, 'text').fail(function(xhr, status, error) {
        alert('鎖定失敗(' + xhr.responseText + ')');
    });
}
</script>