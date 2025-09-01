<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<!-- <link rel="stylesheet" href="colorbox.css" />
<script src="/js/jquery-1.7.2.min.js"></script>
<script src="/js/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" /> -->

<{include file='meta.inc.tpl'}> 		
<script type="text/javascript">
$(document).ready(function() {
	var aSelected = [];
	
	$( "#dialog" ).dialog({
		autoOpen: false,
		modal: true,
		minHeight:50,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
	$(".ui-dialog-titlebar").hide() ;
	
	$('#loading').dialog('close');
	$(".ajax").colorbox({width:"400",height:"100"});
	
	$( "#branch_search" ).combobox() ;
	$( "#scrivener_search" ).combobox() ;
	
	$('#citys').change(function() {
		cityChange() ;
	}) ;
	
	$('#areas').change(function() {
		areaChange() ;
	}) ;
});

/* 取得縣市區域資料 */
function cityChange() {
	var url = 'zipArea.php' ;
	var _city = $('#citys :selected').val() ;
	$.post(url,{'c':_city,'op':'1'},function(txt) {
		$('#areas').html(txt) ;
	}) ;
}
////

/* 取得區域郵遞區號 */
function areaChange() {
	var _area = $('#areas :selected').val() ;
	$('#zip').val(_area) ;
}
////
function first() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	if (current_page <= 1) { return false ; }
	else { current_page = 1 ; }

	$('[name="current_page"]').val(current_page);
	postData(0);
}
function back() {
	var current_page = parseInt($('[name="current_page"]').val()) - 1 ;

	if (current_page <= 0) { return false ; }
	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function next() {
	var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
	var total_page = parseInt($('[name="total_page"]').val()) ;
	
	if (current_page > total_page) { return false ; }

	$('[name="current_page"]').val(current_page);
	postData(0);

}
function last() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function direct() {
	var current_page = parseInt($('[name="current_page"]').val()) ;
	var total_page = parseInt($('[name="total_page"]').val()) ;

	if (current_page >= total_page) { current_page = total_page ; }
	else if (current_page <= 0) { current_page = 1 ; }

	$('[name="current_page"]').val(current_page);
	postData(0);
	
}
function show_limit() {
	var current_page = parseInt($('[name="current_page"]').val()) ;

	$('[name="current_page"]').val(current_page);
	postData(0);
}

function postData(cat) {

	var d1 = $("[name='sEndDate']").val();
	var d2 = $("[name='eEndDate']").val();
	
	if (d1 == '' || d2 == '') {
		
		alert('請先選擇結案日期');
		return false;
	}
	
	// console.log($('[name="current_page"]').val());
	if (cat == '1') { //報表
		$("[name='xls']").val('ok');
		$("[name='ok']").val('ok');
	}else{
		$("[name='xls']").val('');
		$("[name='ok']").val('ok');
	}
	
	$('[name="form"]').submit();
}

function clearFrom(){




	$("[name='sEndDate']").val('');
	$("[name='eEndDate']").val('');
	$("[name='ok']").val('');
	$("[name='xls']").val('');
	$('[name="current_page"]').val('');
	$("[name='sales']").val('');
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
			background: #F8EDEB;border:1px #727272 outset;color:font-size:12px;margin-left:2px
		}
		input.bt4:hover {
			padding:4px 4px 1px 4px;
			vertical-align: middle;
			background:  #EBD1C8;border:1px #727272 outset;font-size:12px;margin-left:2px;cursor:pointer
		}
		.ui-autocomplete-input {
			width:300px;
		}
		#dialog {
			background-image:url("/images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
		.row{
			background-color:#FFFFFF;padding-top:5px;padding-left:5px;
		}
		.tb th{
			background-color:#E4BEB1;padding:4px;
		}
		.tb td{
			background-color:#F8ECE9;padding:4px;padding-left:5px;
		}
		.tb{
			border: 1px solid #CCC;
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
                                    <td width="81%" align="right"></td>
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
                                                        
										</div>
										<br/> 
										<h3>&nbsp;</h3>
										<div id="container">
										<div id="dialog"></div>
<div>
<form name="form" method="POST">
<input type="hidden" name="xls">
<input type="hidden" name="ok">
<h1>回饋金報表</h1>
<center>
<table cellspacing="0" cellpadding="0" style="width:70%;border:1px solid #CCC">

<tr>
	<td style="width:20%;background-color:#E4BEB1;padding:4px;">類別</td>
	<td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
        <{html_radios name=cat options=$arrayCategory selected=$cat}>
		

	</td>
</tr>

<tr>
	<td style="width:20%;background-color:#E4BEB1;padding:4px;">
        
        履保費出款日
       
        <!-- 結案日期 -->
       
	       

	</td>
	<td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
	<input type="text" name="sEndDate" class="datepickerROC" style="width:100px;" value="<{$sEndDate}>">(起)~
	<input type="text" name="eEndDate" class="datepickerROC" style="width:100px;" value="<{$eEndDate}>">
	(迄)
	
	
	</td>
	
</tr>
<{if $smarty.session.member_id == 6 || $smarty.session.member_id == 1 || $smarty.session.member_id == 3 || $smarty.session.member_id == 12}>
<tr>
	<td style="width:20%;background-color:#E4BEB1;padding:4px;">
	業務
	
	
	</td>
	<td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
		<select name="sales" size="1" style="width:130px;">
		<option value="">全部</option>
		<{$salesList}>
		</select>
	</td>
	
	
	
	
</tr>
<tr>
    <td style="width:20%;background-color:#E4BEB1;padding:4px;">
    另附報表
    
    
    </td>
    <td style="background-color:#F8ECE9;padding:4px;padding-left:5px;">
       <{html_checkboxes name=sp options=$arrayCategory2 selected=$sp}>
        
    </td>
    
    
    
    
</tr>
<{/if}>

</table>
</center>
<div style="padding:20px;text-align:center;">
<input type="button" value="查詢" onclick="postData(0);" class="bt4" style="display:;width:100px;height:35px;">
<input type="button" value="清除" class="bt4" style="display:;width:100px;height:35px;" onclick="clearFrom()">
<{if $max > 0}>
<input type="button" value="下載EXCEL" class="bt4" style="display:;width:100px;height:35px;" onclick="postData(1);">
<{/if}>
</div>

</div>
<div id="data">
	<{foreach from=$list key=key item=item}>
		<table width="100%" border="0" class="tb">
			<tr>
                <th colspan="6" >&nbsp;</th>
            </tr>
            <tr>
            	<th width="15%">案號︰</th>
            	<td width="20%"><{$item.cCertifiedId}></td>
            	<th width="10%">地政士︰</th>
            	<td width="20%"><{$item.sOffice}></td>
            	<th width="10%">總價金︰</th>
            	<td width="20%"><{$item.cTotalMoney|number_format:0}></td>
            	

            </tr>
            <tr>
            	<th width="15%">保證費金額:</th>
            	<td colspan="4"><{$item.cCertifiedMoney|number_format:0}></td>
            	<td>
            		<{if $item.cCaseFeedBackModifier !=''}>
            		<div style="color:red;font-size:20px;">已手動更改</div>
					<{/if }>		

            		<{if $item.ScrivenerSPFeedMoney == 0 && $item.cSpCaseFeedBackMoneyMark == 'x'}>
					<div style="color:blue;font-size:20px;">請檢查地政士特殊回饋</div>
            		<{/if }>
            	</td>
            </tr>
            <tr>
                <th colspan="6" >回饋對象</th>
            </tr>
         	<tr>
             	<th width="15%">仲介店名︰</th>
             	<td colspan="5">
             	<{if $item.BrandName =='非仲介成交'}>
             		<{$item.BrandName}></td>
             	<{else}>
             		<{$item.BrandName}>&nbsp;&nbsp;<{$item.BranchName}>
             	<{/if}>
         	</tr>
            <tr>
               	<th width="15%">案件回饋︰</th>
               	<td>
               		<span style="background-color:#CCC;">
               		<{if $item.cCaseFeedback == 0}>
               			回饋，金額：<{$item.cCaseFeedBackMoney|number_format:0}>元
               		<{else}>
               			不回饋
               		<{/if}> 
               		</span>
               	</td>
                
                <th>回饋對象︰</th>
                <td colspan="3">
                	<{if $item.cFeedbackTarget == 1}>
                		仲介
                	<{else}>
               			地政士
               		<{/if}> 

                </td>
                
                                           
            </tr>
            <{if $item.BranchName1 != ''}>
            <tr >
                <th>仲介店名︰</th>
                <td colspan="5"><{$item.BrandName1}>&nbsp;&nbsp;<{$item.BranchName1}></td>
            </tr>
            <tr >
             	<th>案件回饋︰</th>
             	<td>
             		<span style="background-color:#CCC;">
             		<{if $item.cCaseFeedback1 == 0}>
               			回饋，金額：<{$item.cCaseFeedBackMoney1|number_format:0}>元
               		<{else}>
               			不回饋
               		<{/if}> 
               		</span>
                 	
             	</td>
             	
                <th>回饋對象︰</th>
                <td colspan="3">
                	<{if $item.cFeedbackTarget1 == 1}>
                		仲介
                	<{else}>
               			地政士
               		<{/if}> 
                   
                </td>
                                            
                                            
            </tr>
            <{/if}>
            <{if $item.BranchName2 != ''}>
            <tr >
                <th>仲介店名︰</th>
                <td colspan="5"><{$item.BrandName2}>&nbsp;&nbsp;<{$item.BranchName2}></td>
            </tr>
            <tr >
                <th>案件回饋︰</th>
                <td>
                	<span style="background-color:#CCC;">
                	<{if $item.cCaseFeedback2 == 0}>
               			回饋，金額：<{$item.cCaseFeedBackMoney2|number_format:0}>元
               		<{else}>
               			不回饋
               		<{/if}> 
               		</span>

                   
                </td>
               
                <th>回饋對象︰</th>
                <td colspan="3">
                	<{if $item.cFeedbackTarget2 == 1}>
                		仲介
                	<{else}>
               			地政士
               		<{/if}> 
                    
                </td>
                                            
                                            
            </tr>
            <{/if}>
			<{if $item.BranchName3 != ''}>
			<tr >
				<th>仲介店名︰</th>
				<td colspan="5"><{$item.BrandName3}>&nbsp;&nbsp;<{$item.BranchName3}></td>
			</tr>
			<tr >
				<th>案件回饋︰</th>
				<td>
                	<span style="background-color:#CCC;">
                	<{if $item.cCaseFeedback3 == 0}>
               			回饋，金額：<{$item.cCaseFeedBackMoney3|number_format:0}>元
               		<{else}>
               			不回饋
               		<{/if}>
               		</span>


				</td>

				<th>回饋對象︰</th>
				<td colspan="3">
					<{if $item.cFeedbackTarget3 == 1}>
					仲介
					<{else}>
					地政士
					<{/if}>

				</td>


			</tr>
			<{/if}>
         	<tr id="sp_show_mpney" style="display:<{$item.sSpRecall}>;"> 
             	<th>地政士事務所</th>
             	<td colspan="2"><{$item.sOffice}></td>
             	<th>特殊回饋︰</td>
             	<td colspan="3">
             		<span style="background-color:#CCC;"><{$item.ScrivenerSPFeedMoney|number_format:0}>元</span>
             	</td>
         	</tr>
         	<{if $item.otherFeedCount > 0}>
         	<tr>
                <th colspan="6">其他回饋對象</th>                              
            </tr>
            <{foreach from=$item.otherFeed key=key item=item2}>
            <tr>
                <th>回饋對象：</th>
                <td>
                 	<{if $item2.fType == 1}>
                 		地政士
                 	<{else}>
                 		仲介
                 	<{/if}>
                </td>
                <th>店名：</th>
                <td><{$item2.store}></td>
                <th>回饋金：</th>
                <td>
                	<span style="background-color:#CCC;"><{$item2.fMoney|number_format:0}>元</span>
                </td>
            </tr>
            <{/foreach}>
             <{/if}>
            
        </table>
		<div class="row">&nbsp;</div>
	<{/foreach}>
	<br>
	<{if $max > 0}>
	<center>
	<span style="font-size:9pt;">
	<select name="record_limit" size="1" onchange="show_limit()" style="font-size:9pt;width:48;">
	<{$record_limit}>
	</select>

	<span onclick="first()" style="cursor:pointer;"><img src="/images/first.jpg" style="border:0px;"></span>
	<span onclick="back()" style="cursor:pointer;"><img src="/images/backward.jpg" style="border:0px;"></span>

	第&nbsp;<input type="text" name="current_page" onchange="direct()" value="<{$current_page}>" style="font-size:9pt;text-align:right;width:30px;">&nbsp;頁
	／共&nbsp;<{$total_page}>&nbsp;頁


	<span onclick="next()" style="cursor:pointer;"><img src="/images/forward.jpg" style="border:0px;"></span>
	<span onclick="last()" style="cursor:pointer;"><img src="/images/last.jpg" style="border:0px;"></span>

	顯示第&nbsp;<{$i_begin}>&nbsp;筆到第&nbsp;<{$i_end}>&nbsp;筆的紀錄，共&nbsp;<{$max}>&nbsp;筆紀錄
	

	</span>
	</center>
	<{/if}>
</div>

</div>
</form>
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