<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	var tb = $('#table').DataTable( {
		 "ajax": 'getScrivenerBlackList.php',
		 "order": [[ 0, "asc" ]],
         "columns": [
         	{"data":"id"},
           	{ "data": "name" },
          	{ "data": "office" },
          	{ "data": "sIdentifyId" },
          	{ "data": "address" }
        ],
        "columnDefs": [
		    { "visible": false, "targets": 0 }
		  ]
    } );

   $('#table tbody').on('dblclick', 'tr', function () {
        var data = tb.row( this ).data();
        // console.log(id);
        location.href = 'formScrivenerBlackListEdit.php?cat=mod&id='+data['id'];
    } );
}) ;
/* 編輯個人權限 */
function edit(no) {
	$('[name="id"]').val(no) ;
	$('form[name="members"]').submit() ;
}
////

/* 新增帳戶 */
function add() {
	$('form[name="membersNew"]').submit() ;
}

</script>
<style>

.memberTB {
	border: 1px solid #ccc;
	padding: 5px;
	font-size: 10pt;
	font-weight: bold;
	text-align: center;
	background-color: #EEE0E5 ;
}
.memberCell {
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
}

#table tbody td{
	padding: 5px;
	font-size: 9pt;
	text-align: center;
	border: 1px solid #ccc;
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
						<div id="menu-lv2">
                                                        
						</div>
						<br/> 
						<h3>&nbsp;</h3>
						<div id="container">
							<h1>地政士黑名單</h1>
							<form name="membersNew" method="POST" action="formScrivenerBlackListEdit.php?cat=add">
							</form>

							<form name="members" method="POST" action="formScrivenerBlackListEdit.php">
								<input type="hidden" name="id" value="">
							</form>
							<div style="font-size:10pt;width:100%;margin:10px;"><a href="#" onclick="add()">新增</a></div>
														
							<form name="formList" method="POST">
								<table cellspacing="0" id="table" style="width:100%;" >
									<thead>
										<tr>
											<td class="memberTB">id</td>
											<td class="memberTB" width="15%">姓名</td>
											<td class="memberTB" width="20%">事務所</td>
											<td class="memberTB" width="15%">身分證字號</td>
											<td class="memberTB" width="50%">地址</td>
											
										</tr>
									</thead>
								</table>
								
							</form>
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