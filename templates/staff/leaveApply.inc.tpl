<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>休假申請</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<!--Google icon-->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.css" integrity="sha512-4S7w9W6/qX2AhdMAAJ+jYF/XifUfFtrnFSMKHzFWbkE2Sgvbn5EhGIR9w4tvk0vfS1hKppFIbWt/vdVIFrIAKw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js" integrity="sha512-ux1VHIyaPxawuad8d1wr1i9l4mTwukRq5B3s8G3nEmdENnKF5wKfOV6MEUH0k/rNT4mFr/yL+ozoDiwhUQekTg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
table {
    text-align: center;
    font-size: 10pt;
}

.title {
    display: inline-block;
    width: 100px;
    text-align: right;
}

.specific-time {
    display: none;
}

.specific-attachment {
    display: none;
}

#myform div {
    padding: 2px;
}

.field-highlight {
    font-weight: bold;
    color: #ff0000;
}

.default-leave-table {
    margin:0px auto; 
    padding: 10px;
    max-width: 500px;
    border:1px solid #ccc;
    border-radius:10px;
    text-align:left;
}

.text-left {
    text-align: left;
}
.default-leave-zone {
    text-align:left;
    padding-right:10px;
}

.default-leave-zone-remark {
    /* border-bottom: 0.1rem dashed gray; */
    font-size: 0.9rem;
    color: red;
}

.leave-row {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
}

.leave-item {
    flex-basis: 33%;
    text-align: left;
}

.leave-remark-row {
    padding: 2px 0;
    text-align: left;
    margin-bottom: 10px;
}

</style>
</head>
<body>
    <h1>休假申請</h1>
    <div id="container">
        <div style="padding:10px;">
            <div class="default-leave-table">
                <{foreach $leaveTypes as $type}>
                    <div class="leave-row">
                        <div class="leave-item">假別：<{$type.leaveName}></div>
                        <div class="leave-item">有效時數：<{$type.sLeaveDefault}> 小時</div>
                        <div class="leave-item">剩餘時數：<{$type.sLeaveBalance}> 小時</div>
                    </div>
                    <{if $type.sLeaveRemark != ''}>
                    <div class="leave-remark-row">
                        <span class="default-leave-zone-remark">備註：<{$type.sLeaveRemark}></span>
                    </div>
                    <{/if}>
                <{/foreach}>
            </div>
            <div style="height:20px;">&nbsp;</div>
            <form id="myform" method="POST" enctype="multipart/form-data">
                <div style="margin:0px auto; padding: 20px; border:1px solid #ccc;border-radius:20px;">
                    <div>
                        <span class="title">申請人：</span>
                        <span><{$smarty.session.member_name}></span>
                        <input type="hidden" name="member_id" value="<{$smarty.session.member_id}>" />
                    </div>

                    <div>
                        <span class="title">假別：</span>
                        <span>
                        <{html_options style="width:160px;" name="leaveId" options=$leaveOptions onchange="leaveChange()"}>
                        </span>
                    </div>

                    <div class="specific-leavetype">
                        <span class="title">休假日期：</span>
                        <span>
                            <input type="text" name="date-from" class="datepicker" style="width:150px;" value="<{$from_date}>">&nbsp;(起)
                        </span>
                    </div>

                    <div class="specific-leavetype">
                        <span class="title"></span>
                        <span>
                            <input type="text" name="date-to" class="datepicker" style="width:150px;" value="<{$to_date}>">&nbsp;(迄)
                        </span>
                    </div>
                    
                    <div class="specific-leavetype">
                        <span class="title">休假時間：</span>
                        <span>
                            <label><input type="radio" name="date-all" id="all-day" checked onclick="checkDate('A')" value="A">&nbsp;全天</label>　　
                            <label><input type="radio" name="date-all" id="specific-hour" onclick="checkDate('S')" value="S">&nbsp;指定時間</label>
                        </span>
                    </div>

                    <div class="specific-time">
                        <span class="title">起迄時間：</span>
                        <span>
                            <select name="time-from" style="width:160px;">
                                <option value="">請選擇</option>
                                <option value="09:00:00">09:00</option>
                                <option value="09:30:00">09:30</option>
                                <option value="10:00:00">10:00</option>
                                <option value="10:30:00">10:30</option>
                                <option value="11:00:00">11:00</option>
                                <option value="11:30:00">11:30</option>
                                <option value="13:00:00">13:00</option>
                                <option value="13:30:00">13:30</option>
                                <option value="14:00:00">14:00</option>
                                <option value="14:30:00">14:30</option>
                                <option value="15:00:00">15:00</option>
                                <option value="15:30:00">15:30</option>
                                <option value="16:00:00">16:00</option>
                                <option value="16:30:00">16:30</option>
                            </select> (起)
                        </span>
                    </div>

                    <div class="specific-time">
                        <span class="title"></span>
                        <span>
                            <select name="time-to" style="width:160px;">
                                <option value="">請選擇</option>
                                <option value="10:00:00">10:00</option>
                                <option value="10:30:00">10:30</option>
                                <option value="11:00:00">11:00</option>
                                <option value="11:30:00">11:30</option>
                                <option value="12:00:00">12:00</option>
                                <option value="13:30:00">13:30</option>
                                <option value="14:00:00">14:00</option>
                                <option value="14:30:00">14:30</option>
                                <option value="15:00:00">15:00</option>
                                <option value="15:30:00">15:30</option>
                                <option value="16:00:00">16:00</option>
                                <option value="16:30:00">16:30</option>
                                <option value="17:00:00">17:00</option>
                                <option value="17:30:00">17:30</option>
                            </select> (迄)
                        </span>
                    </div>
                    
                    <div>
                        <span class="title">請假事由：</span>
                        <span>
                            <input type="text" style="width:280px;" name="apply-reason">
                        </span>
                    </div>

                    <div>
                        <span class="title">指定代理人：</span>
                        <span>
                            <{html_options style="width:160px;" name="agent" options=$agentOptions}>
                        </span>
                    </div>
                    
                    <div class="specific-attachment">
                        <span class="title">上傳附件：</span>
                        <span>
                            <input type="file" name="leaveAttachment" id="fileInput" accept="image/*">
                        </span>
                    </div>
                    
                    <div class="specific-attachment">
                        <span class="title"></span>
                        <span>
                            (僅限照片)
                        </span>
                    </div>
                    
                    <div>
                        <hr>
                    </div>
                    
                    <div style="text-align: center;">
                        <span>
                            <button type="button" style="padding: 5px 10px; margin: 10px 0;" onclick="apply()">送出申請</button>
                        </span>
                        <span>
                            <button type="button" style="padding: 5px 10px; margin: 10px 0;" onclick="list()">返回列表</button>
                        </span>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">
const needAttachmentLeave = <{$needAttachmentLeave}>; // 需要附件的假別
const ONE_HOUR = 60 * 60 * 1000;

$(document).ready(function() {
    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
    });

    // Sync table widths
    var topTableWidth = $(".default-leave-table").width();
    $("#myform > div").width(topTableWidth);
});

function list() {
    parent.$.colorbox.close();
}

function leaveChange() {
    if (needAttachment()) {
        $('.specific-attachment').show();
    } else {
        $('input[name="sLeaveAttachment"]').val('');
        $('.specific-attachment').hide();
    }

    checkDateType();
}

function checkDateType() {
    let leaveId = $('select[name="leaveId"]').val();
    $('#all-day').prop('disabled', false);
    $('#specific-hour').prop('disabled', false);
    if (leaveId == '8') {
        checkDate('A');
        $('#all-day').prop('checked', true).prop('disabled', false);
        $('#specific-hour').prop('disabled', true);
    }

    $(".specific-leavetype").show();
    if (leaveId == '20') {
        $(".specific-leavetype").hide();
    }
}

function needAttachment() {
    let leaveId = $('select[name="leaveId"]').val();
    if (jQuery.inArray(leaveId, needAttachmentLeave) !== -1) {
        return true;
    }

    return false;
}

function checkDate(type) {
    if (type === 'A') {
        $('.specific-time').hide();
        $('[name="time-from"]').val('09:00:00');
        $('[name="time-to"]').val('17:30:00');
    } else {
        $('[name="time-from"]').val('');
        $('[name="time-to"]').val('');
        $('.specific-time').show();
    }
}

function checkFile(type) {
    if (type === 'YES') {
        $('.specific-attachment').show();
    } else {
        $('#fileInput').val('');
        $('.specific-attachment').hide();
    }
}

function apply() {
    if (($('select[name="leaveId"]').val() === '') || ($('select[name="leaveId"]').val() === "0")) {
        alert('請選擇假別');
        $('select[name="leaveId"]').focus();
        return;
    }

    if (($('input[name="date-from"]').val() === '') && ($('select[name="leaveId"]').val() != '20')) {
        alert('請選擇起始日期');
        $('input[name="date-from"]').focus();
        return;
    }

    if (($('input[name="date-to"]').val() === '') && ($('select[name="leaveId"]').val() != '20')) {
        alert('請選擇結束日期');
        $('input[name="date-to"]').focus();
        return;
    }

    if ($('input[name="date-all"]:checked').val() === 'S') {
        if ($('select[name="time-from"]').val() === '') {
            alert('請選擇起始時間');
            $('select[name="time-from"]').focus();
            return;
        }

        if ($('select[name="time-to"]').val() === '') {
            alert('請選擇結束時間');
            $('select[name="time-to"]').focus();
            return;
        }

        if ($('input[name="date-from"]').val() == $('input[name="date-to"]').val()) {
            let ts_from = new Date($('input[name="date-from"]').val() + ' ' + $('select[name="time-from"]').val());
            let ts_to = new Date($('input[name="date-to"]').val() + ' ' + $('select[name="time-to"]').val());
            let noon_from = new Date($('input[name="date-from"]').val() + ' 12:00:00');
            let noon_to = new Date($('input[name="date-to"]').val() + ' 13:00:00');

            if (ts_from >= ts_to) {
                alert('結束時間需大於起始時間');
                $('select[name="time-to"]').focus();
                return;
            }

            let diff = ts_to - ts_from;

            diff = calculateHourTime(diff, ts_from, ts_to, noon_from, noon_to); // 計算時數

            if ((diff / ONE_HOUR < 1) && !['9', '10'].includes($('select[name="leaveId"]').val())) { // 限制一小時但假別為公差(9)或公假(10)不限制
                alert('請假時間須大於 1 小時');
                $('select[name="time-to"]').focus();
                return;
            }
        }

        let _time_from = $('select[name="time-from"]').val();
        let _time_to = $('select[name="time-to"]').val();
        if (_time_from == '09:00:00' && _time_to == '17:30:00') {
            $('#all-day').click();
            alert('請假時間為 09:00 - 17:30，系統將自動選擇全天');
        }
    }

    if ($('input[name="apply-reason"]').val() === '') {
        alert('請填寫請假事由');
        $('input[name="apply-reason"]').focus();
        return;
    }

   let fileInput = document.getElementById('fileInput');
    if (needAttachment() && (fileInput.files.length <= 0)) {
        alert('請上傳附件');
        $('#fileInput').focus();
        return;
    }
    
    checkValidHours();
}

function calculateHourTime(diff, ts_from, ts_to, noon_from, noon_to) {
    //上午(不跨午休)
    if ((ts_from < noon_from) && (ts_to <= noon_from)) {
        // console.log('上午(不跨午休)');
        diff -= 0;
        return diff;
    }

    //下午(不跨午休)
    if ((ts_from >= noon_to) && (ts_to > noon_to)) {
        // console.log('下午(不跨午休)');
        diff -= 0;
        return diff;
    }
    
    //跨午休
    if ((ts_from < noon_from) && (ts_to > noon_to)) {
        console.log('跨午休');
        diff -= ONE_HOUR;
        return diff;
    }

    //午休內
    if ((ts_from >= noon_from) && (ts_to <= noon_to)) {
        alert('時間範圍異常');
        $('select[name="time-to"]').focus();
        throw new Error('時間範圍異常');
    }

    //上午到午休
    if ((ts_from < noon_from) && (ts_to <= noon_to)) {
        // console.log('上午到午休');
        diff -= (ts_to - noon_from);
        return diff;
    }

    //午休到下午
    if ((ts_from >= noon_from) && (ts_to > noon_to)) {
        // console.log('午休到下午');
        diff -= (noon_to - ts_from);
        return diff;
    }

    return diff;
}

function checkValidHours() {
    let leaveId = $('select[name="leaveId"]').val();
    let dateFrom = $('input[name="date-from"]').val();
    let dateTo = $('input[name="date-to"]').val();
    let timeFrom = $('select[name="time-from"]').val();
    let timeTo = $('select[name="time-to"]').val();
    let member_id = $('input[name="member_id"]').val();
    let dateAll = $('input[name="date-all"]:checked').val();
    let reason = $('input[name="apply-reason"]').val();

    let data = {
        leaveId: leaveId,
        dateFrom: dateFrom,
        dateTo: dateTo,
        timeFrom: timeFrom,
        timeTo: timeTo,
        member_id: member_id,
        dateAll: dateAll,
        reason: reason
    };

    $.ajax({
        url: '/includes/staff/checkLeaveHours.php',
        type: 'POST',
        async: false,
        data: data,
        success: function(response) {
            console.log(response);
            if (response === 'OK') {
                $('#myform').submit();
                return true;
            }

            alert('請假時數不足');
            return false;
        },
        error: function(xhr, status, error) {
            alert('異常錯誤，請聯絡管理員');
            return false;
        }
    });
}
</script>