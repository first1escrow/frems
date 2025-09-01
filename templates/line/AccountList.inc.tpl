<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>


<script src="../js/jquery-1.7.2.min.js"></script>
<{include file='meta.inc.tpl'}> 
<script src="../js/jquery.colorbox.js"></script>		
<script type="text/javascript">
$(document).ready(function() {
	var aSelected = [];
    var aId = $('[name="identity"]').val();



	$("#example").dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST", 
        "sAjaxSource": "getAccount.php?identity="+aId,
        "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
            if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                $(nRow).addClass('row_selected');
            }
        }
    });
    
    /* Click event handler */
    $(document).on('click','#example tbody tr', function () {
        var id = this.id;
        var index = jQuery.inArray(id, aSelected);
        if ( index === -1 ) {
            aSelected.push( id );
        } else {
            aSelected.splice( index, 1 );
        }
        $('#totalCount').html(aSelected.length);
        $(this).toggleClass('row_selected');
    });
    $(document).on('dblclick','#example tbody tr', function () {
       	var id = this.id.replace('row_', '');
    	var url = 'AccountEdit.php?id='+id+'&cat=2' ;
	    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
	        location.href ='AccountList.php?identity=<{$identity}>';
	    }}) ;
    });

   
	
	$('#searchBtn').button({
        icons:{
            primary: "ui-icon-document"
        }
    });
});

function Add(){
	var url = 'AccountEdit.php?cat=1' ;
	$.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
	   location.href ='AccountList.php';
	}}) ;
}
function Add2(){
    
    var url = 'AccountSales.php' ;
    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
       location.href ='AccountList.php';
    }}) ;
}
function tab2(v,name)
{
    $(".focus_page li").attr('class', '');
        $("#e1").hide();
        $("#e2").hide();

    if (v == 1) {
        $("#e1").show();

        // $("#e3").hide();
    }else if(v==2){

        $("#e2").show();      
        // $("#e3").hide();   
    }else if(v ==4){
        $("#e4").show();  
        // $("#e3").show(); 
    }
    $("[name='identity']").val(v);

    // $("#"+name).attr('class', 'focus_end');

    location.href=  "AccountList.php?identity="+v;
    
}
function getGuest(){
    var url = "GuestList.php";
    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
       location.href ='AccountList.php';
    }}) ;
}
function LineMsg(){
    window.open("LineFirstBotMsg.php");
}
</script>
<style>
.btn {
    color: #000;
    font-family: Verdana;
    font-size: 12px;
    font-weight: bold;
    line-height: 12px;
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
    font-size: 12px;
    font-weight: bold;
    line-height: 12px;
    background-color: #CCCCCC;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
    /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
}

.focus_page {
    vertical-align: top;
    display:inline-block;
}
.focus_page li {
    float:left;
    margin: 0 3px 0 3px;
}
.focus_page li a {
    color: #b48400;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFFFFF;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
}
.focus_page li a:hover {
    color: #FFFFFF;
    font-size:16px;
    background-color: #FFFF96;
    border: 1px solid #FFFF96;
}
.focus_page li.focus_end a {
    color: #FFFFFF;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #FFFF96;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #FFFF96;
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
	                <td width="81%" align="right"></td>
	                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3>
	                </td>
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
						<div id="menu-lv2"></div><br/>
                        <h3>&nbsp;</h3>
						<div id="container">
							<div id="dialog"></div>
							<div>
 								<h1>LINE帳號管理</h1><br>
                                <{if $smarty.session.member_pDep != 5 || $smarty.session.member_id == 1}>
                                    <div style="float:left;padding-bottom:20px;">
                                        <ul class="focus_page">
                                            <li class="<{$class}>" onclick="tab2(1,'em1')" id="em1"><a href="#">地政士</a></li>
                                            <li class="<{$class1}>" onclick="tab2(2,'em2')" id="em2"><a href="#" >仲介店</a></li>
                                            <li class="<{$class3}>" onclick="tab2(4,'em4')" id="em4"><a href="#" >經紀人</a></li>
                                            <!-- <li class="<{$class2}>" onclick="tab2(3,'em3')" id="em3"><a href="#" >其他</a></li> -->
                                        </ul>
                                        <input type="hidden" name="identity" value="<{$identity}>">
                                    </div>
                                <{/if}>
 									<div style="padding-bottom:20px; float:right"> 
                                           <a href="#" onclick="Add2()">未有案件業務(註冊用)</a> 
                                        <{if $smarty.session.member_id == 6}>
                                          &nbsp;|&nbsp;  <a href="#" onclick="getGuest()">訪客紀錄</a>
                                          &nbsp;|&nbsp;  <a href="#" onclick="LineMsg()">發送訊息</a>
                                        <{/if}>
 										<!-- <input type="button" value="新增" class="btn" onclick="Add()"> -->
 									</div>
									<div style="width:100%">
										<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                            <thead>
                                                <tr>
                                                    <th width="10%">序號</th>
                                                    <th width="10%">身分別</th>
                                                    <th width="10%">姓名</th>
                                                    <th width="10%">手機</th>
                                                    <th width="10%">店編號</th>    
                                                    <th width="10%">第一階段認證</th>
                                                    <th width="10%">第二階段認證</th>
                                                    <th width="10%">有效</th>
                                                    <th width="10%">修改時間</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="9" class="dataTables_empty">Loading data from server</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="9"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
									</div>
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