<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<{include file='meta.inc.tpl'}>
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$("link[href='/libs/datatables/media/css/demo_table.css']").remove();

$(document).ready(function() {
	
    $('#example').DataTable({
        "searching": false,
        "order": [
            [4, "desc"],
            [0, "desc"]
        ],
        "language": {
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
        }
    });
});

function detail(id) {
    $('[name="sId"]').val(id);
    $('#form1').submit();
}

function addnew() {
    location.replace('salesReportPromoDetail.php');
}
</script>
<style>
	.btn {
		padding:10px 20px 10px 20px ;
		color:#212121 ;
		background-color:#F8ECE9 ;
		margin:2px ;
		border:1px outset #F8ECE0 ;
		cursor:pointer ;
	}
	.btn:hover {
		padding:10px 20px 10px 20px ;
		color:#212121 ;
		background-color:#EBD1C8 ;
		margin:2px;
		border:1px outset #F8ECE0;
		cursor:pointer;
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
                                    <form id="form1" method="GET" action="salesReportPromoDetail.php">
                                        <input type="hidden" name="sId" value="">
                                    </form>
                                </div>
                                <br/> 
                                <h3></h3>
                                <h1>課程推廣</h1>

                                <div style="padding: 20px;">
                                    <div style="margin-top: -20px;padding: 5px;text-align:  right;">
                                        <a href="Javascript:void(0)" onclick="addnew()">新增登錄</a>
                                    </div>
                                    <table id="example" class="display" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>日期</th>
                                                <th>負責業務</th>
                                                <th>對象</th>
                                                <th>店名稱</th>
                                                <th>是否確認</th>
                                                <th>詳細內容</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <{foreach from=$data key=key item=item}>
                                            <tr>
                                                <td><{$item.date}></td>
                                                <td><{$item.sales}></td>
                                                <td><{$item.identity}></td>
                                                <td><{$item.store}></td>
                                                <td><{$item.confirm}></td>
                                                <td><{$item.detail}></td>
                                            </tr>
                                        <{/foreach}>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>日期</th>
                                                <th>負責業務</th>
                                                <th>對象</th>
                                                <th>店名稱</th>
                                                <th>是否確認</th>
                                                <th>詳細內容</th>
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
