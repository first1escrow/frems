<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link rel="stylesheet" type="text/css" href="/css/jquery.autocomplete.css" />
<{include file='meta.inc.tpl'}>
<script type="text/javascript" src="/js/jquery.autocomplete.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	getMarguee(<{$smarty.session.member_id}>) ;
	setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)

	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"300"});
	$('input[name="sn"]').focus() ;
	
	/* enter 輸入 */
	$(this).keypress(function(e) {
		if (e.keyCode == 13) {
			save() ;
		}
	}) ;
	////
	
	//姓名搜尋
	$('[name="buyer"]').autocomplete('data_buyer.php') ;
	$('[name="owner"]').autocomplete('data_owner.php') ;
	$('[name="buyer_agent"]').autocomplete('data_buyer_agent.php') ;
	$('[name="owner_agent"]').autocomplete('data_owner_agent.php') ;
	$('[name="scrivener"]').autocomplete('data_scrivener.php') ;
    var branchBrand;
    $("body").on("change","#branch_brand", function() {
        branchBrand = ($(this).val());
    });
    $('[name="branch"]').autocomplete('data_branch.php',{
            noCache: true,
            extraParams: {
                brand: function() {
                    return branchBrand;
                }
            }
    });
	
	<{$sms_window}>
});

function colorbx(url) {
	$.colorbox({href:url});
}

function save() {
	let url = 'buyerownerinquery_result.php' ;
	
	let sed = $('[name="enddate"]').val() ;
	let ssd = $('[name="signdate"]').val() ;
	let esd = $('[name="sign2date"]').val() ;
	
	let bk = $('[name="bank"]').val() ;
	let no = $('[name="sn"]').val() ;
	let uk = $('[name="undertaker"]').val() ;
	let by = $('[name="buyer"]').val() ;
	let ow = $('[name="owner"]').val() ;
	let sc = $('[name="scrivener"]').val() ;
	let bd = $('[name="brand"]').val() ;
	let bh = $('[name="branch"]').val() ;
	let st = $('[name="status"]').val() ;
	let zp = $('[name="zip"]').val() ;
	let ba = $('[name="buyer_agent"]').val() ;
	let oa = $('[name="owner_agent"]').val() ;

	let addr = $('[name="addr"]').val() ;
	let uid = $('[name="uid"]').val();
	
	$.post(url,
		{
            'bank':bk,'sn':no,'undertaker':uk,'buyer':by,'owner':ow,'scrivener':sc,'owner_agent':oa,
		    'brand':bd,'branch':bh,'signdate':ssd,'sign2date':esd,'status':st,'enddate':sed,'zip':zp,'buyer_agent':ba,'addr':addr,'uid':uid
        },
		function(txt) {
			let tmp = txt.split(',');

			if (tmp[0] == 1) {
				$("#form_edit").attr('action', '/escrow/formbuyowneredit.php');
				$('#id').val(tmp[1]);
				$("#form_edit").submit();
			} else {
				$('#container').html(txt) ;
			}
	}) ;
}

function cancel() {
	location.reload() ;
}

function interest_invoice_download() {
    let url = 'interestInvoiceDownload.php';
    $.colorbox({
        iframe: true,
        width: "400", 
        height: "300", 
        href: url, 
        onClosed: function() {
            // location.replace(location.href);
        }
    });
}
</script>
<style>
.small_font {
	font-size: 9pt;
	line-height:1;
}
input.bt4 {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background: #F8EDEB;border:1px #727272 dotted;color:font-size:12px;margin-left:2px
}
input.bt4:hover {
	padding:4px 4px 1px 4px;
	vertical-align: middle;
	background:  #EBD1C8;border:1px #727272 dotted;font-size:12px;margin-left:2px;cursor:pointer
}
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
    <form name="form_edit" id="form_edit" method="POST">
        <input type="hidden" name="id" id="id" value='3' />
    </form>
    <form name="form_add" id="form_add" method="POST">
    </form>
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
                                <div id="menu-lv2"></div>

                                <br/> 
                                <h3>&nbsp;</h3>

                                <div id="container">
                                    <div>
                                        <div>
                                            <{if $smarty.session.member_id|in_array:[1, 6, 12] || $smarty.session.member_pDep == 5}>
                                            <a href="Javascript:interest_invoice_download();">利息與發票下載</a>
                                            <{/if}>
                                        </div>
                                        <form name="mycal" autocomplete="off">
                                            <table cellspacing="0" cellpadding="0" style="width:900px;padding:20px;">
                                                <tr>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        系統別*　&nbsp;
                                                        <select name="bank" size="1" style="width:160px;">
                                                            <option value="">所有銀行</option>
                                                            <{$contract_bank}>
                                                        </select>
                                                    </td>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        保證號碼　
                                                        <input type="number" name="sn" style="width:160px;" maxlength="9">
                                                    </td>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        承辦人　　&nbsp;
                                                        <select name="undertaker" size="1" style="width:160px;">
                                                            <option value=""></option>
                                                            <{$undertaker}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        買方　　　
                                                        <input type="text" name="buyer" style="width:160px;font-size:8pt;height:20px;">
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        賣方　　　
                                                        <input type="text" name="owner" style="width:160px;font-size:8pt;height:20px;">
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        地政士姓名&nbsp;
                                                        <input type="text" name="scrivener" style="width:160px;font-size:8pt;height:20px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        買方代理人
                                                        <input type="text" name="buyer_agent" style="width:160px;height:20px;">
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        賣方代理人
                                                        <input type="text" name="owner_agent" style="width:160px;height:20px;">
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        身分證字號&nbsp;
                                                        <input type="text" name="uid" style="width:160px;height:20px;" maxlength="10">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        仲介品牌　
                                                        <select name="brand" size="1" style="width:160px;" id="branch_brand">
                                                        <option value="">全部</option>
                                                        <{$brand}>
                                                        </select>
                                                    </td>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        仲介店名　
                                                        <input type="text" name="branch" style="width:160px;font-size:8pt;height:20px;">
                                                    </td>
                                                    <td style="width:300px;background-color:#E4BEB1;padding:4px;">
                                                        簽約日期　&nbsp;
                                                        <input type="text" name="signdate" class="calender datepickerROC" style="width:160px;" readonly>
                                                        <span class="small_font">(起)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        案件狀態　
                                                        <select name="status" size="1" style="width:160px;">
                                                            <option value="">全部</option>
                                                            <{$status}>
                                                        </select>
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        狀態日期　
                                                        <input type="text" name="enddate" class="calender datepickerROC" style="width:160px;" readonly>
                                                    </td>
                                                    <td style="width:300px;background-color:#F8ECE9;padding:4px;">
                                                        簽約日期　&nbsp;
                                                        <input type="text" name="sign2date" class="calender datepickerROC" style="width:160px;" readonly>
                                                        <span class="small_font">(迄)</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="width:900px;background-color:#F8ECE9;padding:4px;" colspan="3">
                                                        案件地址　&nbsp;
                                                        <input type="hidden" name="zip" id="zip" />
                                                        <input type="hidden" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" readonly="readonly" />
                                                        <select class="input-text-big" name="s_country" id="s_country" class="keyin2b" onchange="getArea('s_country','s_area','zip')">
                                                        <option>縣市</option>
                                                            <{$country}>
                                                        </select>
                                                        <span id="s_areaR">
                                                        <select class="input-text-big" name="s_area" id="s_area" onchange="getZip('s_area','zip')">
                                                            <option>鄉鎮市區</option>
                                                        </select>
                                                        </span>
                                                        <input type="text" name="addr" style="width:300px;font-size:8pt;height:20px;">
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="padding:20px;text-align:center;">
                                                <center>
                                                    <span onclick="save()" class="btn">查詢</span>
                                                    <span onclick="cancel()" class="btn">取消</span>
                                                </center>
                                            </div>
                                        </form>
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