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
        "sAjaxSource": "getTaxGoverment.php",
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
    	var url = 'TaxGovermentEdit.php?id='+id+'&cat=2' ;
	    $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
	        location.href ='TaxGovermentList.php';
	    }}) ;
    });

   
	
	$('#searchBtn').button({
        icons:{
            primary: "ui-icon-document"
        }
    });
});

function add(){
	var url = 'TaxGovermentEdit.php?cat=1' ;
	$.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
	   location.href ='TaxGovermentList.php';
	}}) ;
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
 								<h1>稅捐稽徵處</h1><br>
                                    
									<div style="width:100%">
                                        <input type="button" value="新增" onclick="add()" class="btn">
										<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                            <thead>
                                                <tr>
                                                    <th width="10%">序號</th>
                                                    <th width="30%">名稱</th>
                                                    <th width="70%">管轄範圍</th>
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