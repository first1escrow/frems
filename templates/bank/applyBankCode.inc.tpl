<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html>
<head>
<link rel="stylesheet" href="colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type="text/javascript" src="js/jquery.autocomplete.js"></script>
<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
var oTable;

$(document).ready(function() {
    let aSelected = [];

    /* Init the table */
    oTable = $("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": 'POST', 
        "sAjaxSource": '/includes/bank/getApplyBankCode.php',
        "bSort": false,
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                $(nRow).addClass('row_selected');
            }
        },
        "aoColumns": [
            { "sName": "scrivenerId", "sClass": "align-center" },
            { "sName": "scrivenerName" },
            { "sName": "aApplyDateTime", "sClass": "align-center" },
            { "sName": "aContractVersion", "sClass": "no-wrap" },
            { "sName": "aContractCategory", "sClass": "align-center" },
            { "sName": "aQuantity", "sClass": "align-right" },
            { "sName": "cUndertaker1", "sClass": "align-center" },
            { "sName": "aProcessed", "sClass": "align-center no-wrap" }
        ],
    });

    $('#example tbody tr').live('dblclick', function () {
        let tmp = this.id.replace('row_', '');

        $.colorbox({
            iframe:true, 
            width:"600px", 
            height:"80%", 
            href: '/bank/editBankCodeRecord.php?id=' + tmp, 
            title: '地政士申請紀錄',
            onClosed: function() {
                oTable.fnDraw();
            }
        });
    });

});

</script>
<style>
.small_font {
	font-size: 9pt;
	line-height:1;
}
input.bt4 {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
}
.ui-autocomplete-input {
	width:150px;
}
#dialog {
    background-image:url("/images/animated-overlay.gif") ;
    background-repeat: repeat-x;
    margin: 0px auto;
}
.button_style{
	padding: 5px 5px 5px 5px;
}

td.no-wrap {
    white-space: nowrap;
}
td.align-center {
    text-align: center;
}
td.align-right {
    text-align: right;
}
</style>
</head>
<body id="dt_example">

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
					<td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
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
					<div id="menu-lv2"></div>
					<br/> 
					<h3>&nbsp;</h3>
					<div id="container">
						<div id="dialog"></div>
						
						<div>
                            <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                <thead>
                                    <tr>
                                        <th width="">地政士編號</th>
                                        <th width="">姓名</th>
                                        <th width="">申請日期</th>
                                        <th width="" nowrap>合約版本</th>
                                        <th width="">合約類別</th>
                                        <th width="">數量</th>
                                        <th width="">承辦人</th>
                                        <th width="">狀態</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="8" class="dataTables_empty">資料讀取中</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan=8></th>
                                    </tr>
                                </tfoot>
                            </table>
						</div>
						
					</div>
					<div id="footer" style="height:50px;">
						<p>2012 第一建築經理股份有限公司 版權所有</p>
					</div>
					</td>
				</tr>
			</table>
			</td>
		</tr>
	</table>
	
</div>
</body>
</html>