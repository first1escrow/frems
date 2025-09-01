<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
	<meta http-equiv="X-UA-Compatible" content="IE=11"/>
<{include file='meta2.inc.tpl'}> 
<script type="text/javascript">
$(document).ready(function() {
	$('#dialog').dialog('close');
	
	
	
	
	
	
	$(".ajax").colorbox({width:"400",height:"100"});
	
	// $( "#branch_search" ).combobox() ;
	// $( "#scrivener_search" ).combobox() ;
	
	$('#citys').combobox({
                onChange: function (newValue, oldValue) {
                	// alert(newValue);
                	if (newValue == 0) {

                	}
                    var url = '../includes/getZip.php?city='+newValue+'&type=json';
            		$('#areas').combobox('reload', url);
            		$('#areas').combobox('setValue', '0');
                }
    });

	$('#areas').combobox({
                onChange: function (newValue, oldValue) {
                   $('#zip').val(newValue) ;
                }
    });
});


function search(url) {
	$( "#dialog" ).dialog("open") ;

	var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))
	var input = $('input');
    var textarea = $('textarea');
    var select = $('select');
    var arr_input = new Array();



    $.each(select, function(key,item) {
    	// console.log($(item).attr("name"));
        if (reg.test($(item).attr("name"))) {                        
            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                arr_input[$(item).attr("name")] = new Array();            
            }                           
            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();                    
        }else{
        	 arr_input[$(item).attr("name")] = $(item).val();
                       
        }
                    
    });
               
    $.each(textarea, function(key,item) {
        arr_input[$(item).attr("name")] = $(item).attr("value");
                    
    });
               
                

    $.each(input, function(key,item) {

        if(reg.test($(item).attr("name"))){
            if ($(item).is(':checkbox')) {
                if ($(item).is(':checked')) {
                    if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                        arr_input[$(item).attr("name")] = new Array();
                    }
                                
                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                                
                 }

                           
            }else{
                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                    arr_input[$(item).attr("name")] = new Array();
                                
                }
                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                           
            }
        }else if ($(item).is(':checkbox')) {
            if ($(item).is(':checked')) {
                arr_input[$(item).attr("name")] = '1';
            }
            else {
                arr_input[$(item).attr("name")] = '0';
            }
                        
        }else if ($(item).is(':radio')) {
            if ($(item).is(':checked')) {
                arr_input[$(item).attr("name")] = $(item).val();
            }
                       
        }else {
            arr_input[$(item).attr("name")] = $(item).attr("value");
                       
        }
                    
    });


     var obj_input = $.extend({}, arr_input);

	// $('[name="form"]').submit();
	$.ajax({
		url: 'analysiscase_result.php',
		type: 'POST',
		dataType: 'html',
		data: obj_input
	})
	.done(function(msg) {
		$("#showData").html(msg);

		$('#dialog').dialog('close');
	});
	

}
function showTimeArea(val){
	// console.log(val);

	$(".timeArea").each(function() {
		$(this).hide();
	});

	if (val == 's') {
		$("[name='showSeason']").show();
	}else if(val == 'm'){
		$("[name='showMonth']").show();
	}
}
function add(cat){

	if (cat == 'b') {
		var val = $('[name="branchMenu"]').val();
		
		var text = $('#branchMenu option[value="'+val+'"]').text(); 
		$("#showBrach").append('<div id="'+val+'" class="addStore bStore"><input type="hidden" name="branch[]" value="'+val+'"><a href="#" onClick="del('+val+')" >(刪除)</a>'+text+'</div>');
	}else if(cat == 's'){
		var val = $('[name="scrivenerMenu"]').val();
		var text = $('#scrivenerMenu option[value="'+val+'"]').text(); 
		
		$("#showSctivener").append('<div id="'+val+'" class="addStore sStore"><input type="hidden" name="scrivener[]" value="'+val+'"><a href="#" onClick="del('+val+')">(刪除)</a>'+text+'</div>');
		
	}
}
function del(id){
	$("#"+id).remove();
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
		
		#dialog {
			background-image:url("/images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
			width: 300px; 
			height: 30px;
		}
		.easyui-combobox{
			width: 300px;
		}
		.block{
			border: 1px solid #CCC;
			/*background-color: #999;*/
		}
		.row_title{
			background-color: #8F0000;
			color:white;
			padding: 5px;
		}
		.row_contant{
			padding: 5px;
			border: 1px solid  #999;
		}
		.row_contant_left{
			padding: 0px;
			float: left;
			display:inline;
			width: 50%;
			border: 1px solid  #999;
		}
		.row_contant_right{
			float: left;
			display:inline;
			width: 49%;
			border: 1px solid  #999;
			padding: 0px;
		}

		/*button*/
		.xxx-button {
			color:#FFFFFF;
		    font-size:12px;
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
		ul.tabs {
            width: 100%;
            height: auto;
            border-left: 0px solid #999;
            border-bottom: 1px solid #D99888;
           
        }  
        ul.tabs li {
             margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
            height: auto;
        }

		.tb{
		    /*padding: 10px;*/
		    border:solid #CCC 1px;
		}

		.tb th{
		    color: #FFF;
		    background-color: #8F0000;
		    padding: 5px;
		    border: 1px solid #fff;
		}

		.tb td{
		   color: #000;
		    background-color: #FFF;
		    padding: 5px;
		    border: 1px solid #CCC;
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
                                                        
										</div>
										<br/> 
										<h3>&nbsp;</h3>
										<div id="container">
										
<div>
<form name="form" method="POST">
	<h1>統計表</h1>
	<div id="dialog" class="easyui-dialog" style="display:none"></div>
	<div>※根據仲介頁籤的仲介店數去平分</div>
	<div class="block"> 
		<div class="row_title">時間</div>
		<div class="row_contant">
			<{html_radios name=timeCategory options=$menu_timeCategory selected=$timeCategory onclick="showTimeArea(this.value)"}> 
			<br>
			<{html_radios name=dateCategory options=$menu_dateCategory selected=$dateCategory}> 

			年度<{html_options name="startYear" options=$menu_Year selected=$startYear}> 年
					
				<span name="showSeason"  style="display: none"  class="timeArea">
					<select name="s_season" style="width:80px;">
						<option value="">請選擇</option>
						<option value="S1">第一季</option>
						<option value="S2">第二季</option>
						<option value="S3" >第三季</option>
						<option value="S4">第四季</option>
							
					</select>季
						
				</span>
					<span name="showMonth" style="display: none" class="timeArea">
						
						<select name="s_month" style="width:80px;">
							<option value="">請選擇</option>
							<option value="01">1月份</option>
							<option value="02">2月份</option>
							<option value="03">3月份</option>
							<option value="04">4月份</option>
							<option value="05">5月份</option>
							<option value="06">6月份</option>
							<option value="07">7月份</option>
							<option value="08">8月份</option>
							<option value="09">9月份</option>
							<option value="10">10月份</option>
							<option value="11">11月份</option>
							<option value="12">12月份</option>
						</select>
						月
					</span>
					(起)
					～
					
					<{html_options name="endYear" options=$menu_Year selected=$endYear}> 年
					<span name="showSeason" style="display: none" class="timeArea">
						<select name="e_season" style="width:80px;">
							<option value="">請選擇</option>
							<option value="S1">第一季</option>
							<option value="S2">第二季</option>
							<option value="S3" >第三季</option>
							<option value="S4">第四季</option>
							
						</select>季
						
					</span>
					
					
					<span name="showMonth"  style="display: none" class="timeArea">
						
						<select name="e_month" style="width:80px;">
							<option value="">請選擇</option>
							<option value="01">1月份</option>
							<option value="02">2月份</option>
							<option value="03">3月份</option>
							<option value="04">4月份</option>
							<option value="05">5月份</option>
							<option value="06">6月份</option>
							<option value="07">7月份</option>
							<option value="08">8月份</option>
							<option value="09">9月份</option>
							<option value="10">10月份</option>
							<option value="11">11月份</option>
							<option value="12">12月份</option>
						</select>月
					</span>
					(迄)
			<!-- 日期(起)
			<input type="text" name="sEndDate" class="datepickerROC" style="width:100px;">
		

			日期(迄)
			<input type="text" name="eEndDate" class="datepickerROC" style="width:100px;"> -->
		</div>
	</div>

	<div class="block">
		<div class="row_title">
			統計類別
		</div>
		<div class="row_contant">
			<{html_radios name=tab options=$menu_tab selected=$tab}>
		</div>

	</div>
	<br>
	<div class="block">
		<div class="row_title">搜尋條件</div>
		<div class="row_contant">
			<div class="row_contant_left">
				品牌
				<select name="brand" id="brand" class="easyui-combobox" data-options="
	                    valueField: 'id',
	                    textField: 'text'
	                    " style="width:300px;">
					<{foreach from=$menu_brand key=key item=item}>
						<option value="<{$key}>"><{$item}></option>
					<{/foreach}> 
					
				</select> 
			</div>
			<div class="row_contant_right">
				
				店家、地政士地區
				<select name="city" id="citys"  style="width:100px;" >
					<!-- <{$menu_city}> -->
					<{foreach from=$menu_city key=key item=item}>
							<option value="<{$key}>"><{$item}></option>
					<{/foreach}> 
				</select>
				<select name="area" id="areas"  style="width:100px;" data-options="valueField:'id',textField:'text'">
					<option value="">全部</option>
				</select>
				<input type="hidden" name="zip">
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="row_contant">
			<div class="row_contant_left">
				銀行
				<select name="bank" id="bank" class="easyui-combobox"  style="width:300px;">
				<{foreach from=$menu_bank key=key item=item}>
					<option value="<{$key}>"><{$item}></option>
				<{/foreach}> 
				
				</select> 
			</div>
			<div class="row_contant_right">
				<!-- 仲介類別
				<select name="brandCategory" id="brandCategory" class="easyui-combobox" data-options="
	                    valueField: 'id',
	                    textField: 'text'
	                    " style="width:300px;">
					<{foreach from=$menu_brandCategory key=key item=item}>
							<option value="<{$key}>"><{$item}></option>
					<{/foreach}> 
				</select> -->
			</div>
			<div style="clear:both;"></div>
		</div>
		
		
		<div class="row_contant">
			<div class="row_contant_left">
				店家
				<select name="branchMenu" id="branchMenu" class="easyui-combobox" style="width:300px;">
				<{foreach from=$menu_branch key=key item=item}>
					<option value="<{$key}>"><{$item}></option>
				<{/foreach}> 
				
				</select> 
				<input type="button" value="增加" onclick="add('b')" class="xxx-button"><br>
				<font color="red">※查詢店家請務必按下增加</font>
				<div id="showBrach" style="padding-left:20px"></div>
				
			</div>
			<div class="row_contant_right">
				地政士

					<select name="scrivenerMenu" id="scrivenerMenu" class="easyui-combobox" style="width:300px;">
						<{foreach from=$menu_scrivener key=key item=item}>
							<option value="<{$key}>"><{$item}></option>
						<{/foreach}> 
					</select> 
					<input type="button" value="增加" onclick="add('s')" class="xxx-button"><br>
					<font color="red">※查詢地政士請務必按下增加</font>

					<div id="showSctivener" style="padding-left:20px;"></div>
					<div style="clear: both;"></div>
				
			</div>
			<div style="clear:both;"></div>

		
			


			
		</div>
		<div class="rows_contant">
			
		</div>
	
		
	</div>
	<center>
		<div >
			<input type="button" value="查詢" onclick="search()" class="xxx-button">
		</div>
	</center>
	<hr>
	<div id="showData">

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