<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<{include file='meta.inc.tpl'}>

<script type="text/javascript">

$(document).ready(function() {	
	
	setSortalbe('ItemArea');
	setContractCategory();
	setlistItme('item','listItem','no');
	
}) ;
function setContractCategory(){
	var category = $('[name="category"]:checked').val();
	// console.log(category);

	if (category == 1) {
		$(".category1").show();
		$(".category2").hide();

		
	}else{
		$(".category1").hide();
		$(".category2").show();
	}
	$("#fix4").hide();
	$("#c2_fix1").hide();
}
function setSortalbe(id){
	$( "#"+id ).sortable({
	  	beforeStop: function( event, ui ) {
	  		setlistItme('item','listItem','no');
	  	}
	});
}

function setlistItme(cat,selector1,selector2){
	// console.log(cat);console.log(selector1);console.log(selector2);
	var no =1;
	var no1 =1;
	var no2 =1;
	var check = 0;
	var check1 = 0;
	var category = $("[name='category']:checked").val();
	$("."+cat).each(function(index) {

		if ($(this).find("."+selector1+" option:selected").val() == 1) {
			if ($(this).find('.Indent option:selected').val() == 1) { //重新計算
				no = 1;
			}

			if (category == 1) {
				if (no == 4 ) {

					if ($(".fix4").length == 1) {
						$("#fix4").clone().show().insertBefore(this);
						$("#fix5").clone().show().insertBefore(this);
					}
					no++;
					no++;

					check1 = 1;
					// console.log(no);
				}
				if (no == 9 && $(".fix9_1").length == 1) { //9-1
					
					$("#fix9_1").clone().show().insertAfter(this);
					
				}
				
			}else if(category == 2){

				// if (no == 1 && $(".c2_fix1").length == 1) {

				// 	$("#c2_fix1").clone().show().insertAfter(this);
				// }

				if (no == 1) {
					no++;
				}

				
			}

			$(this).find('.'+selector2).text('第'+NumToCh2(no)+'條');
			no++;
			
		}else if($(this).find("."+selector1+" option:selected").val() == 2){
			if ($(this).find('.Indent option:selected').val() == 1) { //重新計算
				no1 = 1;
			}

			if (category == 1) {
				if (no == 10 && $(".fix9_1").length == 2 && check == 0) { //9-1
					no1 = 2;
					check = 1;
				}
			}else if(category == 2){

				if (no == 1 && $(".c2_fix1").length == 1) {
					
					$("#c2_fix1").clone().show().insertAfter(this);

				}

				if (no1 == 2) {
					no1++;
				}

				
			}
			

			$(this).find('.'+selector2).text(NumToCh2(no1)+'、');
			no1++;
			
		}else if($(this).find("."+selector1+" option:selected").val() == 3){
			if ($(this).find('.Indent option:selected').val() == 1) { //重新計算
				no2 = 1;
			}

			$(this).find('.'+selector2).text('('+no2+')');
			
				no2++;
			
		}else{
			$(this).find('.'+selector2).text('');
		}
		
	});
	
}
function NumToCh2(str){  

	str = str+'';
    var len = str.length-1;
    var idxs = ['','十','百','千','萬','十','百','千','億','十','百','千','萬','十','百','千','億'];
    var num = ['零','一','二','三','四','五','六','七','八','九'];
    return str.replace(/([1-9]|0+)/g,function( $, $1, idx, full) {
        var pos = 0;
        if( $1[0] != '0' ){
            pos = len-idx;
            if( idx == 0 && $1[0] == 1 && idxs[len-idx] == '十'){
                return idxs[len-idx];
            }
            return num[$1[0]] + idxs[len-idx];
        } else {
            var left = len - idx;
            var right = len - idx + $1.length;
            if( Math.floor(right/4) - Math.floor(left/4) > 0 ){
                pos = left - left%4;
            }
            if( pos ){
                return idxs[pos] + num[$1[0]];
            } else if( idx + $1.length >= len ){
                return '';
            }else {
                return num[$1[0]]
            }
        }
    });

   	return newStr;
   	  

}
function add(){
	// $().
	$( "#EditAreaChild1" ).sortable( "destroy" );//複製會有問題所以先移除之後再套用一次效果

	var clone = $(".item:first").clone(true);
	var no = $(".item").length;
	var newNo = (no+1);

	clone.find('#listItem'+no).attr('id', '#listItem'+newNo);
	clone.find(".delItem").show().attr('onclick', 'delItem("item'+newNo+'")');

	clone.find('.no').text('');
	clone.find(".xxx-textarea").text('');

	// console.log(clone.find(".xxx-textarea").text());
	//child
	//把複製多餘的刪掉
	clone.find('.itemChild'+no).each(function(index, el) {
		if (index > 0) {
			$(this).remove();
		}
	});
	
	// console.log(clone);
	//
	clone.insertAfter(".item:last").attr('id', 'item'+newNo);
	//套用效果
	
	
}

function delItem(name){
	// console.log(name);
	$("#"+name).remove();
}
</script>
<style>
select{
	width: 100%;
}
.btn {
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCCCCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #DDDDDD;
		    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}
.btn:hover {
	color: #000;
	font-size:12px;
	background-color: #999999;
	border: 1px solid #CCCCCC;
}
.btn.focus_end{
	color: #000;
	font-family: Verdana;
	font-size: 14px;
	font-weight: bold;
	line-height: 14px;
	background-color: #CCCCCC;
	text-align:center;
	display:inline-block;
	padding: 8px 12px;
	border: 1px solid #FFFF96;
	/*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}

		
/*input*/
.xxx-input {
	color:black;
	font-size:16px;
	font-weight:normal;
	background-color:#FFFFFF;
	text-align:left;
	height:20px;
	padding:0 2px;
	border:1px solid #999;			
}
.xxx-input:focus{
	border-color: rgba(82, 168, 236, 0.8) !important;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
	-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
	outline: 0 none;
}

.xxx-textarea {
	color:#666666;
	font-size:16px;
	font-weight:normal;
	line-height:normal;
	background-color:#FFFFFF;
	text-align:left;
	padding:5px 5px;
	border:1px solid #CCCCCC;
	width: 90%;
	height: 63px;
	margin-top: 5px;
	
}
.xxx-textarea:focus {
	border-color: rgba(82, 168, 236, 0.8) !important;
	box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
	-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(82, 168, 236, 0.6);
	outline: 0 none;
}

input[type="radio"]{
	width:15px;
	height:15px;
	margin:0px 4px 0 0;
	vertical-align:-4px;
}
input[type="checkbox"]{
	width:15px;
	height:15px;
	margin:0px 4px 0 0;
	vertical-align:-4px;
}
.tb{
       
    border: solid 1px #ccc;
    width: 80%
}
.tb th{
    width:20%;
    background-color:#E4BEB1;
    text-align: right;
    padding-bottom: 2px;
    padding-top:2px;
    /*border: solid 1px #CCC;*/
}
.tb td{
    width:80%;
    padding: 5px;
    
}

span{
	color:black;
}

.title{
	text-align: center;
	font-size: 18px;
}
.contract{
	margin: 10px 10px 10px 10px ;
}
.contract-title, .contract-textbox, .contract-ctrl{
	margin-top: 5px;
	margin-bottom: 5px;
}
.contract-ctrl{
	text-align: right;
}
.contract-content{
	border:1px solid #999;
	padding: 2px 2px 2px 2px; 
	width: 100%;
}

.item{
	border: 1px solid #999;
	/*width: 99%;*/
	background-color: #F8ECE9;
	
	
}
.item-block-no,.item-block-left,.item-block-right{
	float: left;
	height: 85px; 
	line-height:  75px;
	text-align:  center;
}

.item-block-no{
	/*border-left: 1px solid red;*/
	width: 10%;
	line-height:  30px;
	
}
.item-block-left{
	/*border-left: 1px solid green;*/
	width: 78%;
	
}
.item-block-right{
	/*border-left: 1px solid blue;*/
	width: 10%;

}


.block-title{
	/*border: 1px solid #999;*/
	color:black;
	font-family: 微軟正黑體, serif;
	padding: 0.2em;
	background-color: #E4BEB1;	
	width:auto;	
}
.contract-fixBlock{
	border: 1px solid #CCC;
	background-color: #FFF0D4;
	color:black;
	margin-top: 2px;
	margin-bottom: 2px;
	padding: 5px;
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
						<h1>合約書新增</h1>
							
		
	
	
	<form  method="POST" id="NewsForm" >
		<input type="hidden" name="id" value="<{$id}>">
		<input type="hidden" name="cat" value="<{$cat}>">
		<div class="contract-title">
			<center>
			<table cellpadding="0" cellspacing="0" class="tb">
				<tr>
					<th colspan="2" style="background-color: ;text-align: center">
						<{html_radios name=category options=$menuContract selected=$data.eContract onClick="setContractCategory()"}>
					</th>
				</tr>
				<tr>
					<th>名稱:</th>
					<td><input type="text" name="name" value="<{$data.eName}>" placeholder="名稱" class="xxx-input" style="width: 80%"></td>
				</tr>
				
				<tr class="category2">
					<th>類型:</th>
					<td><{html_radios name=category2 options=$menuCategory selected=$data.eApplication}></td>
				</tr>
				
				<tr>
					<th>是否上架:</th>
					<td><{html_checkboxes name=sendIden options=$menusendIden selected=$data.eSendIden}></td>
				</tr>
			</table>
			</center>
			<div class="contract-textbox"> <hr> </div>
		</div>
		<div class="contract-content">
			<div class="contract-q">
				<{include file='contractApply.inc.tpl'}>
			</div>
			
			
		</div>
		
		<center>
		<div style="margin-top: 10px;">
			<div style="padding-left:30px;float:center;display:inline;text-align: center;width: 100%">
				<input type="submit" value="送出" class="btn">
				<input type="button" value="返回" class="btn" onclick="javascript:location.href='contractlist.php'">
			</div>
			
			<input type="hidden" name="id" value="<{$data.qId}>">
			<input type="hidden" name="cat" value="<{$cat}>">

		</div>
		</center>
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