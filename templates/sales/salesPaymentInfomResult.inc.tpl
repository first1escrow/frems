<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=9" />
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css" rel="stylesheet"
        type="text/css">

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.print.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#example').DataTable({
            searching: false,
            dom: 'l<"cmc-css1"B>frtip',
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            buttons: [
                'excel'
            ],
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
            },
            columnDefs: [
                {
                    "targets": [1],
                    "visible": false,
                },{
                    "targets": [8],
                    "visible": false,
                },{
                    "targets": [9],
                    "visible": false,
                },
            ],
        });

        parent.$('#menuBar').show();
        parent.$('.cmc_overlay').hide();
    }) ;

    function downloadPDF(id) {
        $('[name="dl"]').attr('action','/accounting/pdf/pdfPrint_2020.php');
        $('[name="id"]').val(id);
        $('[name="dl"]').submit();
    }

    </script>
<style>
.xxx-button {
	color:#FFFFFF;
	font-size:14px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:40px;
	
	background-color: #a63c38;
    border: 1px solid #a63c38;
    border-radius: 0.35em;
    font-weight: bold;
    padding: 0 20px;
    margin: 5px auto 5px auto;
}

.xxx-button:hover {
	background-color:#333333;
	border:1px solid #333333;
}

.xxx-button2 {
	color:#FFFFFF;
	font-size:14px;
	font-weight:normal;
	
	text-align: center;
	white-space:nowrap;
	height:20px;
	
	background-color: #a63c38;
    border: 1px solid #a63c38;
    border-radius: 0.35em;
    font-weight: bold;
    padding: 0 20px;
    margin: 5px auto 5px auto;
}

.xxx-button:hover {
	background-color:#333333;
	border:1px solid #333333;
}

.tb {
	padding:5px;
	margin-bottom: 20px;
	background-color:#FFFFFF;

}

.tb th{
	padding: 5px;
	border: 1px solid #CCC;
	background-color: #CFDEFF;
	font-size: 12px;
}

.tb td{
	text-align: center;
	padding: 5px;
	border: 1px solid #CCC;
	font-size: 12px;
}

.cmc-css1{
    float: right;
}
</style>
</head>
<body>
    <center>
        <div>
            <table id="example" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>編號</th>
                        <th>店家品牌</th>
                        <th>店家名稱</th>
                        <th>請款方式</th>
                        <th>結算時間</th>
                        <th>狀態</th>
                        <th>回饋金額</th>
                        <th>PDF</th>
                        <th>地址</th>
                        <th>業務</th>
                    </tr>
                </thead>
                <tbody>
                        <{foreach from=$list key=key item=item}>
                        <tr class="Row">
                            <td><{$item.code}></td>
                            <td><{$item.brand}></td>
                            <td><{$item.sStoreName}></td>
                            <td><{$item.method}></td>
                            <td><{$item.sEndTime}>~<{$item.sEndTime2}></td>
                            <td><{$item.status}></td>
                            <td><span class="m"><{$item.sFeedBackMoneyTotal|number_format}></span></td>
                            <td><input type="button" value="PDF" class="xxx-button2" onclick="downloadPDF(<{$item.sId}>)"></td>
                            <td><{$item.addr}></td>
                            <td><{$item.sales}></td>
                        </tr>
                        <{/foreach}>
                </tbody>
                <tfoot>
                    <tr>
                        <th>編號</th>
                        <th>店家品牌</th>
                        <th>店家名稱</th>
                        <th>請款方式</th>
                        <th>結算時間</th>
                        <th>狀態</th>
                        <th>回饋金額</th>
                        <th>PDF</th>
                        <th>地址</th>
                        <th>業務</th>
                    </tr>
                </tfoot>
            </table>

            <div style="padding-top: 20px;">
                <input type="button" value="返回" class="xxx-button" onclick="javascript:parent.location.href='salesPaymentInform.php'">
            </div>

            <form action="" name="dl" method="POST" target="_blank">
                <input type="hidden" name="id" value="">
            </form>
        </div>
    </center>
</body>
</html>