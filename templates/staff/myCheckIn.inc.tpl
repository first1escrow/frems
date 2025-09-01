<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>

<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<!--Google icon-->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/css/colorbox.css" />
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js"></script>
<style>
table {
    text-align: center;
    font-size: 10pt;
}

.dt-body-left {
    text-align: left;
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
                                    <h1 style="text-align:left;">出勤記錄</h1>

                                    <input type="hidden" id="year" value="<{$thisYear}>">
                                    <input type="hidden" id="month" value="<{$thisMonth}>">
                                    
                                    <table id="example" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="20%">日期</th>
                                                <th width="20%">姓名</th>
                                                <th width="20%">上班簽到</th>
                                                <th width="20%">下班簽退</th>
                                                <th width="20%">備註</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="dataTables_empty">讀取資料中...</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
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

    <div id="colorbox" style="display:none;width:400px;">
        <input type="hidden" id="date">
        <input type="hidden" id="type">

        <table>
            <tr>
                <td>請說明原因：</td><td style="text-align:left;"><input type="text" id="desc" style="width:200px"></textarea></td>
            </tr>
            <tr>
                <td>補打卡時間：</td><td style="text-align:left;"><input type="time" id="time" style="width:200px"></td>
            </tr>
        </table>

        <div style="text-align: center;margin-top:20px;">
            <button type="button" id="applySubmitButton" style="padding:10px;" onclick="applySubmit()">送出</button>
        <div>
    </div>
</body>
</html>
<script type="text/javascript">
const url = '/includes/staff/checkInOutList.php';
const table = $('#example').DataTable({
    ajax: {
        url,
        type: 'POST',
        data: function(d){
            d.year = $('#year').val(),
            d.month = $('#month').val()
        }
    },
    columnDefs: [
        // { className: "dt-head-center", targets: [ 0, 1, 2, 3, 4 ] },
        { className: "dt-body-left", targets: [ 0, 1, 2, 3, 4 ] },
        {
            "targets": 0,
            "render": function (data, type, row, meta) {
                return row.sDate + ' (' + row.weekName + ')';
            }
        },
        {
            "targets": 1,
            "render": function (data, type, row, meta) {
                return row.pName;
            }
        },
        {
            "targets": 2,
            "render": function (data, type, row, meta) {
                let position = '';
                // if (row.inLatitude && row.inLongitude) {
                //     let url = encodeURI('https://www.google.com.tw/maps/place/' + row.inLatitude + ',' + row.inLongitude);
                //     // position = '<a href="' + url + '" target="_blank" title="顯示地圖"><span class="material-icons material-symbols-outlined">public</span></a>';
                //     position = '<a href="Javascript:void(0);" title="顯示地圖" onclick="window.open(\'' + url+ '\', \'map\', \'location=yes,height=600px,width=800px,scrollbars=yes,status=yes\')"><span class="material-icons material-symbols-outlined">public</span></a>';
                // }

                if (row.leave) {
                    return '';
                }

                if (row.leaveAM) {
                    return '';
                }

                // let show_text = row.sIn + ' ' + position;
                let show_text = row.sIn;

                return show_text;
            }
        },
        {
            "targets": 3,
            "render": function (data, type, row, meta) {
                let position = '';
                // if (row.outLatitude && row.outLongitude) {
                //     let url = encodeURI('https://www.google.com.tw/maps/place/' + row.outLatitude + ',' + row.outLongitude);
                //     // position = '<a href="' + url + '" target="_blank" title="顯示地圖"><span class="material-icons material-symbols-outlined">public</span></a>';
                //     position = '<a href="Javascript:void(0);" title="顯示地圖" onclick="window.open(\'' + url+ '\', \'map\', \'location=yes,height=600px,width=800px,scrollbars=yes,status=yes\')"><span class="material-icons material-symbols-outlined">public</span></a>';
                // }

                if (row.leave) {
                    return '';
                }

                if (row.leavePM) {
                    return '';
                }

                // let show_text = row.sOut + ' ' + position;
                let show_text = row.sOut;

                return show_text;
            }
        },
        {
            "targets": 4,
            "render": function (data, type, row, meta) {
                return '<span style="font-size:9pt;">' + row.remark + '</span>';
            }
        }
    ],
    createdRow: function( row, data, dataIndex ) {
        $(row).css({'background-color':data.css});
    },
    info: false,
    paging: false,
    searching: false,
    lengthChange: false,
    processing: true,
    ordering: false,
    language: {
        "processing": "",
        "loadingRecords": "載入中...",
        "emptyTable": "無資料",
        "paginate": {
            "first": "第一頁",
            "previous": "上一頁",
            "next": "下一頁",
            "last": "最後一頁"
        }
    }
});

$(document).ready(function() {

});

function locationOn(lat, lng) {
    let url = encodeURI('https://www.google.com.tw/maps/place/' + row.inLatitude + ',' + row.inLongitude);
    window.open(url, '_blank');
}

function apply(date, type) {
    $('#date').val(date);
    $('#type').val(type);

    $('#colorbox').show();
    $.colorbox({inline:true, width:"400px", height:"300px", href:"#colorbox", onClosed:function(){
        $('#colorbox').hide();
        $('#desc').val('');
        $('#time').val('');

        table.ajax.reload();
    }}) ;
}

function applySubmit() {
    $('#applySubmitButton').prop('disabled', true);

    let desc = $('#desc').val();
    let time = $('#time').val();
    let date = $('#date').val();
    let type = $('#type').val();

    if (!desc) {
        alert('未填寫申請理由');
        $('#applySubmitButton').prop('disabled', false);
        return;
    }

    if (!time) {
        alert('未填寫時間');
        $('#applySubmitButton').prop('disabled', false);
        return;
    }

    if (time < '09:00' || time > '18:00') {
        alert('時間需介於09:00~18:00');
        $('#applySubmitButton').prop('disabled', false);
        return;
    }

    let url = '/includes/staff/checkInOutApply.php';
    $.ajax({
        url,
        type: 'POST',
        data: {
            date,
            time,
            type,
            desc
        },
        success: function (data) {
            alert(data);
            $('#applySubmitButton').prop('disabled', false);
            table.ajax.reload();
        },
        error: function (xhr, status, error) {
            $('#applySubmitButton').prop('disabled', false);
            alert('申請失敗(' + xhr.responseText + ')');
        }
    });
}

function leaveApply(from_date='', to_date='') {
    let url = '/staff/leaveApply.php';

    let params = '';
    if (from_date) {
        params = 'from_date=' + from_date;
    }

    if (to_date) {
        params += params ? '&' : '';
        params += 'to_date=' + to_date;
    }
    params = params ? '?' + params : '';
    
    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url+params});
}

function showLeave(id) {
    let url = '/staff/leaveList.php?id=' + id;
    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url});
}

function overtime(id) {
    let url = '/staff/overtimeList.php?id=' + id;
    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url});
}
</script>