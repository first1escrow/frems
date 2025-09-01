<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<{include file='meta2.inc.tpl'}>
<script type="text/javascript">
$(document).ready(function() {	
	var tb = $('#table').DataTable( {
		 "ajax": 'getMemberTable.php',
		 "order": [[ 0, "asc" ]],
         "columns": [
          { "data": "id" },
          { "data": "name" },
          { "data": "account" },
          { "data": "gender" },
          { "data": "dep" },
          { "data": "ext" },
          { "data": "faxNum" },
          { "data": "hiFaxAccount" },
          { "data": "mobile" },
          { "data": "job" }
        ],
        "columnDefs": [
		    { "visible": false, "targets": 0 }
        ],
        "rowCallback": function( row, data ) {
            if (data['job'] == '已停用') {
                $('td', row).css('color', '#C0C0C0');
            }
        }
    } );

   $('#table tbody').on('dblclick', 'tr', function () {
        var data = tb.row( this ).data();
        location.href = 'memberEdit.php?cat=mod&id='+data['id'];
    } );
}) ;
/* 編輯個人權限 */
function edit(no) {
	$('[name="id"]').val(no) ;
	$('form[name="members"]').submit() ;
}
////

/* 新增帳戶 */
function newMember() {
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
							<h1>帳號管理</h1>
							<form name="membersNew" method="POST" action="memberEdit.php?cat=add">
							</form>

							<form name="members" method="POST" action="memberDetail.php">
								<input type="hidden" name="id" value="">
							</form>
							<div style="font-size:10pt;width:100%;margin:10px;"><a href="#" onclick="newMember()">新增帳戶</a></div>
														
							<form name="formList" method="POST">
								<table cellspacing="0" id="table" style="width:100%;" >
									<thead>
										<tr>
											<td class="memberTB">編號</td>
											<td class="memberTB" width="15%">使用者</td>
											<td class="memberTB" width="10%">帳號</td>
											<td class="memberTB" width="10%">性別</td>
											<td class="memberTB" width="10%">部門</td>
											<td class="memberTB" width="10%">分機</td>
											<td class="memberTB" width="10%">傳真</td>
											<td class="memberTB" width="15%">HiFax</td>
											<td class="memberTB" width="10%">手機</td>
											<td class="memberTB" width="10%">是否有效</td>
											
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