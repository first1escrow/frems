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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/superfish/1.7.10/js/superfish.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.4/jquery.colorbox-min.js"></script>
<style>
table {
    text-align: center;
    font-size: 10pt;
}

.page {
    padding: 10px 15px;
    vertical-align: middle;
    margin: 10px 10px;
    cursor: pointer;

    border: 1px solid #a09898;
    border-radius: 5px;
    color: #C0C0C0;
}

#page-btn-refresh {
    color: #000;
}
.page-active {
    background-color: #e90303;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

/* DataTables buttons styling */
.dt-buttons {
    margin-bottom: 10px;
    text-align: left;
}

.dt-button {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
}

.dt-button:hover {
    background-color: #45a049;
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
                                <div id="menu-lv2">
                                
                                </div>

                                <div id="container">
                                    <div id="page-leave" style="display: ;">
                                        <div style="text-align:right;">
                                            <span style="float:left;">申請人：<{html_options name="staffId" options=$staffs selected=$staffSelected style="width:100px;"}></span>
                                            <span style="float:right;">請假日期範圍：<input type="date" id="fromDate" name="fromDate" value="<{$fromDate}>"> ~ <input type="date" id="toDate" name="toDate" value="<{$toDate}>"><span>
                                            <span style="float:right;"><button type="button" onclick="query()" style="margin-left:20px;padding:5px;">查詢</button></span>
                                        </div>

                                        <table id="leave-table" class="display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>申請時間</th>
                                                    <th>申請人</th>
                                                    <th>假別</th>
                                                    <th>請假日期</th>
                                                    <th>總時數</th>
                                                    <th>進度</th>
                                                    <th>狀態</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="7" class="dataTables_empty">讀取資料中...</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="7"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
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
</body>
</html>
<script type="text/javascript">

const leaveTable = $('#leave-table').DataTable({
    ajax: {
        url: '/includes/HR/getStaffLeaveData.php',
        type: 'POST',
        data: function(d) {
            d.staffId = $('select[name="staffId"]').val();
            d.fromDate = $('input[name="fromDate"]').val();
            d.toDate = $('input[name="toDate"]').val();
        }
    },
    order: [[ 6, "desc" ], [3, "asc"]],
    columns: [
        { "data": "sCreatedAt" },
        { "data": "applicantName" },
        { "data": "leaveName" },
        { "data": "leaveDateTime" },
        { "data": "sTotalHoursOfLeave" },
        { "data": "processing" },
        { "data": "status" },
    ],
    columnDefs: [
        { 
            className: "dt-center", 
            targets: "_all" 
        }
    ],
    dom: 'Bfrtip',
    buttons: [
        {
            extend: 'excel',
            text: '匯出 Excel',
            filename: function() {
                var today = new Date();
                var dd = String(today.getDate()).padStart(2, '0');
                var mm = String(today.getMonth() + 1).padStart(2, '0');
                var yyyy = today.getFullYear();
                return '員工請假記錄_' + yyyy + mm + dd;
            },
            title: '員工請假記錄',
            exportOptions: {
                columns: ':visible'
            }
        }
    ],
    searching: false,
    paging: true,
    lengthChange: true,
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
    leaveTable.ajax.reload();
    return;
}

</script>