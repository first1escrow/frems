<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
			$(document).ready(function() {
                var aSelected = [];
				$('#id2scr').draggable() ;
				
				/*$('#scr_cid').keyup(function() {
				//$('#scr_click').click(function() {
					var url = 'id2scrivener.php' ;
					var id = $('#scr_cid').val() ;
					//alert(id) ;
					if (!id.match(/[0-9]{9}/)) {
						$('#scr_here').html('查無資料...') ;
					}
					else {
						$.post(url,{'cid':id},function(txt) {
							$('#scr_here').html(txt) ;
						}) ;
					}
				}) ;*/
				
                /* Init the table */
				/*
                $("#example").dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "POST", 
                    "sAjaxSource": "/includes/escrow/caselisttb.php",
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                            $(nRow).addClass('row_selected');
                        }
                    }
                });
				*/
				
				var dd = $('#timeLimit').val() ;
                /* Init the table */
                $("#example").dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "POST", 
                    "sAjaxSource": "/includes/escrow/caselisttb.php" ,
					"fnServerParams": function( aoData ) {
						aoData.push({"name":"time_limit","value":dd}) ;
					}
                });
				
                /* Click event handler */
                $('#example tbody tr').live('click', function () {
                    var id = this.id;
                    var index = jQuery.inArray(id, aSelected);
                    if ( index === -1 ) {
                        aSelected.push( id );
                    } else {
                        aSelected.splice( index, 1 );
                    }
                    $('#totalCount').html(aSelected.length);
                    $(this).toggleClass('row_selected');
                } );
                $('#example tbody tr').live('dblclick', function () {
                    var id = this.id.replace('row_', '');
                    $('form[name=form_edit]').attr('action', '/escrow/formbuyowneredit.php');
                    $('form[name=form_edit] input[name=id]').val(id);
                    $('form[name=form_edit]').submit();
                } );

                $('#add').live('click', function () {
                    $('form[name=form_add]').attr('action', '/escrow/formbuyowneradd.php');
                    $('form[name=form_add]').submit();
                } );
				
				$('#tool1').live('click',function() {
					var url = '/escrow/searchscrivener.php' ;
					window.open(url,'id2scr','width=300px,height=200px;') ;
				}) ;

                $('#del').live('click', function () {
//                    var request = $.ajax({
//                        url: "/escrow/formdel.php",
//                        type: "POST",
//                        data: {
//                            ids:aSelected
//                        },
//                        dataType: "html"
//                    });
//                    request.done(function( msg ) {
////                        alert(msg);
//                        window.location='/escrow/buyownersearch.php';
//                    });
                } );

                $('#add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#tool1').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#del').button( {
                    icons:{
                        primary: "ui-icon-locked"
                    }
                } );
                $('#scr_click').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#loading').dialog('close');
            } );
			function tl() {
				//window.reload() ;
				var tt = $('#timeLimit').val() ;
				$('#tt').val(tt) ;
				$('form[name="t_l"]').submit() ;
			}
        </script>
    </head>
    <body id="dt_example">
		<form name="t_l" method="POST">
			<input type="hidden" name="time_limit" id="tt" value="">
		</form>
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
											
                                            <button id="add" class="btnsearch">新增</button>
                                            <button id="tool1" class="btnsearch">保號->代書</button>
											　　資料顯示月份
											<{$time_limit}>　
											<span style="font-weight:bold;color:red;">(*結果不包含已結案與帳戶餘額為零之保證號碼帳戶資料!!)</span>
                                            <div id="dynamic">
                                                
												<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                    <thead>
                                                        <tr>
                                                            <th width="15%">銀行別</th>
                                                            <th width="15%">保證號碼</th>
                                                            <th width="12%">買方姓名</th>
                                                            <th width="15%">賣方姓名</th>
                                                            <th width="13%">地政士姓名</th>
                                                            <th width="15%">承辦人</th>
                                                            <th width="15%">建檔日期</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="5" class="dataTables_empty">Loading data from server</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan=7></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>