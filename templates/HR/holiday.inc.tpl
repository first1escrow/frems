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
                                    <h1 style="text-align:left;">假日設定</h1>

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
        createHoliday(str);
    });
    
    calendar.on('eventClick', function(info) {
        getHoildayInfo(info.event.id);
    });
}

function createHoliday(date) {
    let el = `
        <div style="padding: 3px;">假日名稱: <input type="text" id="hName" value=""></div>
        <div style="padding: 3px;">假日類型: 
            <label><input type="radio" id="hMakeUpWorkday" name="hMakeUpWorkday" value="N" checked>&nbsp休假</label>&nbsp;
            <label><input type="radio" id="hMakeUpWorkday" name="hMakeUpWorkday" value="Y">&nbsp補班</label>
        </div>
        <div style="padding: 3px;">日期範圍: <input type="date" id="hFromDate" value="${date}" readonly></div>
        <div style="padding: 3px;">時間(起): <input type="text" id="hFromTime" value="00:00:00"></div>
        <div style="padding: 3px;">時間(迄): <input type="text" id="hToTime" value="23:59:59"></div>
        <div>`;
    $('#holiday-info').empty().html(el);

    $('#holiday-info').dialog({
        title: '新增假日',
        width: 400,
        height: 300,
        modal: true,
        resizable: false,
        buttons: {
            '確定': function() {
                let hName = $('#hName').val();
                if (!hName) {
                    alert('請輸入假日名稱');
                    $('#hName').focus().select();
                    return;
                }

                let hMakeUpWorkday = $('#hMakeUpWorkday:checked').val();
                if (!hMakeUpWorkday) {
                    alert('請選擇假日類型');
                    $('#hMakeUpWorkday').focus().select();
                    return;
                }

                let hFromDate = $('#hFromDate').val();
                if (!hFromDate) {
                    alert('請選擇日期');
                    $('#hFromDate').focus().select();
                    return;
                }

                let hFromTime = $('#hFromTime').val();
                if (!hFromTime) {
                    alert('請輸入時間(起)');
                    $('#hFromTime').focus().select();
                    return;
                }

                let hToTime = $('#hToTime').val();
                if (!hToTime) {
                    alert('請輸入時間(迄)');
                    $('#hToTime').focus().select();
                    return;
                }
                
                addHoliday({hName, hMakeUpWorkday, hFromDate, hFromTime, hToTime});
            },
            '取消': function() {
                $(this).dialog('close');
            }
        }
    });
}

function addHoliday(data) {
    $.ajax({
        type: 'POST',
        url: '/includes/HR/addHoliday.php',
        data: data,
        success: function() {
            // alert('新增成功');
            $('#holiday-info').dialog('close');
            updateEvent(monthStart, monthEnd);
        },
        error: function(xhr, status, error) {
            alert('發生錯誤: ' + xhr.responseText);
        }
    });
}

function updateEvent(from, to) {
    $.ajax({
        type: 'POST',
        url: '/includes/HR/getHoliday.php',
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

function getHoildayInfo(id) {
    $.ajax({
        type: 'POST',
        url: '/includes/HR/getHolidayInfo.php',
        data: {
            id: id
        },
        response: 'json',
        success: function(response) {
            showHoildayInfo(response);
        },
        error: function(xhr, status, error) {
            alert('發生錯誤: ' + xhr.responseText);
        }
    });
}

function showHoildayInfo(data) {
    let el = `
        <div style="padding: 3px;">假日名稱: <input type="text" id="hName" value="${data.hName}"></div>
        <div style="padding: 3px;">假日類型: 
            <label><input type="radio" id="hMakeUpWorkday" name="hMakeUpWorkday" value="N"`;
        
        if (data.hMakeUpWorkday == 'N') {
            el += ' checked';
        }
        el += `>&nbsp休假</label>&nbsp;
            <label><input type="radio" id="hMakeUpWorkday" name="hMakeUpWorkday" value="Y"`;
        
        if (data.hMakeUpWorkday == 'Y') {
            el += ' checked';
        }
        el += `>&nbsp補班</label>
        </div>
        <div style="padding: 3px;">日期範圍: <input type="date" id="hFromDate" value="${data.hFromDate}"></div>
        <div style="padding: 3px;">時間(起): <input type="text" id="hFromTime" value="${data.hFromTime}"></div>
        <div style="padding: 3px;">時間(迄): <input type="text" id="hToTime" value="${data.hToTime}"></div>
        <div>`;
    $('#holiday-info').empty().html(el);

    $('#holiday-info').dialog({
        title: '修改假日',
        width: 400,
        height: 300,
        modal: true,
        resizable: false,
        buttons: {
            '確定': function() {
                updateHoilday(data.hId);
            },
            '刪除': function() {
                if (confirm('確定要刪除嗎?')) {
                    updateHoilday(data.hId, 'delete');
                }
            },
            '取消': function() {
                $(this).dialog('close');
            }
        }
    });
}

function updateHoilday(id, action = 'update') {
    let hName = $('#hName').val();
    let hMakeUpWorkday = $('#hMakeUpWorkday:checked').val();
    let hFromDate = $('#hFromDate').val();
    let hFromTime = $('#hFromTime').val();
    let hToTime = $('#hToTime').val();

    $.ajax({
        type: 'POST',
        url: '/includes/HR/updateHolidayInfo.php',
        data: {
            hId: id,
            action,
            hName,
            hMakeUpWorkday,
            hFromDate,
            hFromTime,
            hToTime
        },
        success: function() {
            if (action == 'delete') {
                alert('刪除成功');
            } else {
                alert('修改成功');
            }

            $('#holiday-info').dialog('close');
            updateEvent(monthStart, monthEnd);
        },
        error: function(xhr, status, error) {
            alert('發生錯誤: ' + xhr.responseText);
        }
    });
}

</script>