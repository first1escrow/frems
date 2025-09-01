<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>

<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='/js/invertColor.class.js'></script>
<style>

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
                                <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                                <div id="menu-lv2">
                                
                                </div>

                                <div id="container">
                                    <h1 style="text-align:left;">員工休假日曆</h1>

                                    <div id='calendar'></div>
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
    <div id="holiday-info"></div>
</body>
</html>
<script type="text/javascript">
var calendar;
var monthStart;
var monthEnd;

$(document).ready(function() {
    // $('.cmc_overlay').hide();
    // $('.cmc_overlay').show();
    fullcalender();
});

function colorbx(url) {
	$.colorbox({href:url});
}

function fullcalender() {
    let calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        headerToolbar: {
            left: 'prevYear prev today next nextYear',
            center: 'title',
            right: 'dayGridMonth'
        },
        buttonText: {
            month: '月',
            week: '週',
            today: '今天',
        },
        allDayText: '全天',
        initialView: 'dayGridMonth',
        locale: 'zh-tw',
        editable: true,
        datesSet: function(viewInfo) {
            const start = viewInfo.startStr; // 当前视图开始的日期
            const end = viewInfo.endStr; // 当前视图结束的日期
            monthStart = viewInfo.startStr;
            monthEnd = viewInfo.endStr;
            updateEvent(start, end);
        },
        events: []
    });
    
    calendar.render();
    
    calendar.on('dateClick', function(info) {
        let str = info.dateStr;
    });
    
    <{if $smarty.session.pHRCalender > 1}>
    calendar.on('eventClick', function(info) {
        getStaffLeaveInfo(info.event.id);
    });
    <{/if}>
}

function updateEvent(from, to) {
    $.ajax({
        type: 'POST',
        url: '/includes/HR/getStaffLeave.php',
        data: {
            from: from,
            to: to
        },
        response: 'json',
        success: function(response) {
            calendar.removeAllEvents(); // 清除当前事件
            calendar.addEventSource(response); // 添加新事件源

            calendar.render();
        },
        error: function(xhr, status, error) {
            alert('發生錯誤: ' + xhr.responseText);
        }
    });
}

function getStaffLeaveInfo(id) {
    $.ajax({
        type: 'POST',
        url: '/includes/HR/getStaffLeaveInfo.php',
        data: {
            id: id
        },
        response: 'json',
        success: function(response) {
            showLeaveInfo(response);
        },
        error: function(xhr, status, error) {
            alert('發生錯誤: ' + xhr.responseText);
        }
    });
}

function showLeaveInfo(data) {
    let el = `
        <div style="padding: 3px;">申請人：${data.applicantName}</div>
        <div style="padding: 3px;">假別：${data.leaveName}</div>
        <div style="padding: 3px;">事由：<pre>${data.sApplyReason}</pre></div>
        <div style="padding: 3px;">代理人：${data.agentName}</div>
        <div style="padding: 3px;">時間(起)：${data.sLeaveFromDateTime}</div>
        <div style="padding: 3px;">時間(迄)：${data.sLeaveToDateTime}</div>
        <div style="padding: 3px;">附件：${data.attachment}</div>
        <div>`;
    $('#holiday-info').empty().html(el);

    $('#holiday-info').dialog({
        title: '明細',
        width: 400,
        height: 350,
        modal: true,
        resizable: false,
        buttons: {
            '關閉': function() {
                $(this).dialog('close');
            }
        }
    });
}

function attachment(code) {
    let url = 'leaveAttachment.php?code=' + code;
    window.open(url, '_blank');
}
</script>