<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />

<{include file='meta.inc.tpl'}>

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    // $('.cmc_overlay').hide();
    // $('.cmc_overlay').show();
});

</script>
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
                                    <h1 style="text-align:left;">員工休假時數歷史紀錄</h1>

                                    <div style="text-align:right;">
                                        <span>姓名：<{html_options name="staff" options=$staffs selected=$staffSelected style="width:100px;"}></span>
                                        <span style="margin-left:30px;"><button type="button" onclick="query()" style="padding: 5px;">查詢</button></span>
                                    </div>

                                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                        <thead>
                                            <tr>
                                                <th>姓名</th>
                                                <th>假別</th>
                                                <th>上次設定日期</th>
                                                <th>本次設定日期</th>
                                                <th>上次預設時數</th>
                                                <th>上次剩餘時數</th>
                                                <th>本次預設時數</th>
                                                <th>本次剩餘時數</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan=8></th>
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
</body>
</html>
<script type="text/javascript">
const url = '/includes/HR/getStaffDefaultLeaveHistory.php';
const table = $('#example').DataTable({
    dom: 'rt<"bottom"ilp><"clear">',
    ajax: {
        url: url,
        type: 'POST',
        data: function (d) {
            d.pId = $('select[name="staff"]').val();
        }
    },
    pageLength: 20,
    order: [[ 0, "asc"], [1, "asc" ]],
    columnDefs: [
        { 
            className: "dt-center", 
            targets: 0 
        },
        {
            targets: 0,
            orderable: true,
            render: function (data, type, row, meta) {
                return row.staffName;
            }
        },
        {
            targets: 1,
            orderable: true,
            render: function (data, type, row, meta) {
                return '<a href="Javascript:void(0);" onclick="viewDetail(' + row.staffId + ', ' + row.leaveId + ')">' + row.leaveName + '</a>';
            }
        },
        {
            targets: 2,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.lastDate;
            }
        },
        {
            targets: 3,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.currentDate;
            }
        },
        {
            targets: 4,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.lastDefault;
            }
        },
        {
            targets: 5,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.lastBalance;
            }
        },
        {
            targets: 6,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.currentDefault;
            }
        },
        {
            targets: 7,
            orderable: false,
            render: function (data, type, row, meta) {
                return row.currentBalance;
            }
        }
    ],
    searching: false,
    paging: true,
    lengthChange: false,
    processing: true,
    language: {
        "processing": "",
        "loadingRecords": "載入中...",
        "lengthMenu": "顯示 _MENU_ 項結果",
        "zeroRecords": "沒有符合的結果",
        "info": "顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項",
        "infoEmpty": "顯示第 0 至 0 項結果，共 0 項",
        "infoFiltered": "(從 _MAX_ 項結果中過濾)",
        "infoPostFix": "",
        "search": "搜尋:",
        "emptyTable": "無資料",
        "paginate": {
            "first": "第一頁",
            "previous": "上一頁",
            "next": "下一頁",
            "last": "最後一頁"
        },
        "aria": {
            "sortAscending": ": 升冪排列",
            "sortDescending": ": 降冪排列"
        }
    }
});

$(document).ready(function() {     

});

function query() {
    table.ajax.reload();
}

function viewDetail(pStaffId, pLeaveId) {
    let url = '/HR/staffDefaultLeaveHistoryDetail.php?staffId=' + pStaffId + '&leaveId=' + pLeaveId;
    $.colorbox({iframe:true, width:"900px", height:"90%", href:url, onClosed: function() {
        table.ajax.reload();
    }});
}
</script>