<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>加班申請</title>
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
    /* display: none; */
}


#myform div {
    padding: 2px;
}

.field-highlight {
    font-weight: bold;
    color: #ff0000;
}

</style>
</head>
<body>
    <h1>加班申請</h1>
    <div id="container">
        <div style="padding:10px;">
            <form id="myform" method="POST" enctype="multipart/form-data">
                <div style="margin:0px auto; padding: 20px;width: 400px; border:1px solid #ccc;border-radius:20px;">
                    <div>
                        <span class="title">申請人：</span>
                        <span><{$smarty.session.member_name}></span>
                        <input type="hidden" name="member_id" value="<{$smarty.session.member_id}>" />
                    </div>

                    <div class="specific-leavetype">
                        <span class="title">加班類別：</span>
                        <span>
                            <label><input type="radio" name="overtime-type" id="holiday" onclick="checkDate('H')" value="H">&nbsp;假日加班</label>　　
                            <label style="display: none;><input type="radio" name="overtime-type" id="working" onclick="checkDate('W')" value="W">&nbsp;平日加班</label>
                        </span>
                    </div>

                    <div class="specific-leavetype">
                        <span class="title">加班日期：</span>
                        <span>
                            <input type="text" name="overtime-date" class="datepicker" style="width:150px;" value="">
                        </span>
                    </div>

                    <div class="specific-time">
                        <span class="title">起迄時間：</span>
                        <span>
                            <select name="time-from" style="width:160px;">
                                <option value="">請選擇</option>
                            </select> (起)
                        </span>
                    </div>

                    <div class="specific-time">
                        <span class="title"></span>
                        <span>
                            <select name="time-to" style="width:160px;">
                                <option value="">請選擇</option>
                            </select> (迄)
                        </span>
                    </div>
                    
                    <div>
                        <span class="title">加班事由：</span>
                        <span>
                            <textarea name="apply-reason" row="4" cols="50" style="width: 390px;height: 100px;"></textarea>
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
const ONE_HOUR = 60 * 60 * 1000;

$(document).ready(function() {
    $(".datepicker").datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
    });

   $('#holiday').click();
});

function list() {
    parent.$.colorbox.close();
}

function checkDate(type) {
    if (type === 'H') {
        setHolidayOptions();
    }
    
    if (type === 'W') {
        setWorkdayOptions();
    }

    $('.specific-time').show();
}

function setHolidayOptions() {
    $('select[name="time-from"]').empty().html(getHolidayOptions());
    $('select[name="time-to"]').empty().html(getHolidayOptions());
}

function getHolidayOptions() {
    return `<option value="">請選擇</option>
            <option value="09:00:00">09:00</option>
            <option value="09:30:00">09:30</option>
            <option value="10:00:00">10:00</option>
            <option value="10:30:00">10:30</option>
            <option value="11:00:00">11:00</option>
            <option value="11:30:00">11:30</option>
            <option value="12:00:00">12:00</option>
            <option value="13:00:00">13:00</option>
            <option value="13:30:00">13:30</option>
            <option value="14:00:00">14:00</option>
            <option value="14:30:00">14:30</option>
            <option value="15:00:00">15:00</option>
            <option value="15:30:00">15:30</option>
            <option value="16:00:00">16:00</option>
            <option value="16:30:00">16:30</option>
            <option value="17:00:00">17:00</option>
            <option value="17:30:00">17:30</option>`;
}

function setWorkdayOptions() {
    $('select[name="time-from"]').empty().html(getWorkdayOptions());
    $('select[name="time-to"]').empty().html(getWorkdayOptions());
}

function getWorkdayOptions() {
    return `<option value="">請選擇</option>
            <option value="18:30:00">18:30</option>
            <option value="19:00:00">19:00</option>
            <option value="19:30:00">19:30</option>
            <option value="20:00:00">20:00</option>
            <option value="20:30:00">20:30</option>
            <option value="21:00:00">21:00</option>
            <option value="21:30:00">21:30</option>
            <option value="22:00:00">22:00</option>
            <option value="22:30:00">22:30</option>
            <option value="23:00:00">23:00</option>
            <option value="23:30:00">23:30</option>`;
}

function apply() {
    if ($('input[name="overtime-date"]').val() === '') {
        alert('請選擇加班申請日期');
        $('input[name="overtime-date"]').focus();
        return;
    }

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

        if (ts_from >= ts_to) {
            alert('結束時間需大於起始時間');
            $('select[name="time-to"]').focus();
            return;
        }
    }

    if ($('input[name="apply-reason"]').val() === '') {
        alert('請填寫加班事由');
        $('input[name="apply-reason"]').focus();
        return;
    }

    $('#myform').submit();
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

</script>