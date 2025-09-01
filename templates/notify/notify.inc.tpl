<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<!-- <link rel="stylesheet" href="colorbox.css" /> -->
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	/* Init the table */
	var oTable = $("#example").dataTable({
		"searching": false,
		"lengthChange": false,
		"order": [[5, "desc"]],
		"columnDefs": [{
			"targets": [0, 3, 4],
			"orderable": false
		}],
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

	$("#link").button({
		icons:{
			primary: "ui-icon-link"
		}
	});
});

function Auth() {
	let desc = prompt('請簡短描述申請用途');

	if ((desc != '') && (desc != undefined) && (desc != null)) {
		let URL = 'https://notify-bot.line.me/oauth/authorize?';

		URL += 'response_type=code';
		URL += '&client_id=<{$line_notify.client_id}>';
		URL += '&redirect_uri=<{$line_notify.redirect_url}>';
		URL += '&scope=notify';
		URL += '&state=' + desc;

		window.location.href = URL;
	}
}

function revoke(id) {
	if (confirm('確認要撤銷嗎?') == true) {
		var url = 'revoke.php';
		$.post(url, {'sn': id}, function(txt) {
			if (txt == 'OK') {
				alert('已撤銷');
				location.replace('/notify/notify.php');
			} else if (txt == 'NG2') {
				alert('系統異常');
			} else if (txt == 'NG3') {
				alert('Line 操作系統異常!!');
			} else if (txt == 'NG4') {
				alert('查無此紀錄');
			} else {
				alert('未知錯誤');
			}
		});
	}
}

function colorbx(url) {
	$.colorbox({href:url});
}

</script>
<style>
.button {
	background-color: #E4BEB1;
	border: none;
	padding: 10px 15px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	border-radius: 10px;
	box-shadow: 10px 10px 5px lightblue;
}

.button:hover {
	background-color: #F8ECE9;
	font-weight: bold;
	color: #0063DC;
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
								<br/> 
								<h3>&nbsp;</h3>
								<div id="container">

									<div style="padding-bottom: 20px;">
										<span>
											<{if $show == '1'}>
											<!-- <button class="button" onclick="Auth()"><i class="fas fa-link"></i>新增綁定</button> -->
											<input type="button" id="link" onclick="Auth()" value="新增綁定">
											<{/if}>
										</span>
										<span style="font-size:10pt;font-weight:bold;color:red;margin-left:10px;vertical-align:bottom;">
											*綁定前請先確認已將 <a href="https://line.me/R/ti/p/@linenotify" target="_blank">Line Notify</a> 加入好友，以透過 <a href="https://line.me/R/ti/p/@linenotify" target="_blank">Line Notify</a> 接收通知。
										</span>
									</div>
									
									<table id="example" class="display" style="width:100%">
										<thead>
											<tr>
												<th style="text-align: center;">操作設定</th>
												<th style="text-align: center;">用途說明</th>
												<th style="text-align: center;">通知對象／群組</th>
												<th style="text-align: center;">通知對象型態</th>
												<th style="text-align: center;">建立人</th>
												<th style="text-align: center;">建立日期</th>
											</tr>
										</thead>
										<tbody>
										<{foreach from=$data key=k item=v}>
											<tr>
												<td style="text-align: center;"><{$v.operation}></td>
												<td style="text-align: center;"><{$v.lDescription}></td>
												<td style="text-align: center;"><{$v.lNotifyTarget}></td>
												<td style="text-align: center;"><{$v.lNotifyTargetType}></td>
												<td style="text-align: center;"><{$v.lStaffId}></td>
												<td style="text-align: center;"><{$v.lCreatedAt}></td>
											</tr>
										<{/foreach}>
										</tbody>
									</table>
									
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