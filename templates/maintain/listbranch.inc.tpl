<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>

        <script type="text/javascript">
            $(document).ready(function() {
                var aSelected = [];
                
				var brand = $("[name='brand']").val();
				var zip = $("[name='area']").val();
				var ss = $('#salesman').val() ;
                 var city = $("[name='country']").val();
                 // console.log(city);
                /* Init the table */
                $("#example").dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sServerMethod": "POST", 
                    "sAjaxSource": "/includes/maintain/branchlisttb.php?sBrand="+brand+"&sZip="+zip+'&salesman='+ss+'&city='+encodeURI(city),
                    "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
                        if ( jQuery.inArray(aData.DT_RowId, aSelected) !== -1 ) {
                            $(nRow).addClass('row_selected');
                        }
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
                    var tmp = this.id.replace('row_', '');
                    $('form[name=form_edit]').attr('action', '/maintain/formbranchedit.php');
                    $('form[name=form_edit] input[name=id]').val(tmp);
                    $('form[name=form_edit]').submit();
                } );

                $('#add').live('click', function () {
                    $('form[name=form_add]').attr('action', '/maintain/formbranchadd.php');
                    $('form[name=form_add]').submit();
                } );

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

                $("[name='brand']").live('change', function(event) {
					var brand = $("[name='brand']").val();
					$("[name='brand']").val(brand);
					var zip = $("[name='area']").val();
					var ss = $('#salesman').val() ;
                     var city = $("[name='country']").val();

					location.href= 'listbranch.php?sBrand='+brand+'&sZip='+zip+'&salesman='+ss+'&city='+encodeURI(city);
                });

                $("#salesman").live('change', function(event) {
					var brand = $("[name='brand']").val();
					var zip = $("[name='area']").val();
					var ss = $('#salesman').val() ;
                     var city = $("[name='country']").val();
					
					location.href= 'listbranch.php?sBrand='+brand+'&sZip='+zip+'&salesman='+ss+'&city='+encodeURI(city);
                });

                $("[name='area']").live('change', function(event) {
					var brand = $("[name='brand']").val();
					var zip = $("[name='area']").val();
					var ss = $('#salesman').val() ;
                     var city = $("[name='country']").val();

					location.href= 'listbranch.php?sBrand='+brand+'&sZip='+zip+'&salesman='+ss+'&city='+encodeURI(city);
                });

                $("[name='country']").live('change', function(event) {
                    var brand = $("[name='brand']").val();
                    var zip = $("[name='area']").val();
                    var ss = $('#salesman').val() ;
                    var city = $("[name='country']").val();

                    location.href= 'listbranch.php?sBrand='+brand+'&sZip='+zip+'&salesman='+ss+'&city='+encodeURI(city);
                });

                $('#add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#del').button( {
                    icons:{
                        primary: "ui-icon-locked"
                    }
                } );
                $('#loading').dialog('close');
            } );
        </script>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="certifyid" id="certifyid" value='' />
            <input type="hidden" name="id" id="id" value='' />
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
                                    <td colspan="3" align="right"><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <div id="mainNav">
                <table width="1000" border="0" cellpadding="0" cellspacing="0">
                    <tr>

                    </tr>
                </table>
            </div>
            <div id="content">
                    <div class="abgne_tab">
                        <{include file='menu1.inc.tpl'}>
                        <div class="tab_container">
                            <div id="menu-lv2">
                                                        
                                                        </div>
                                                    <br/> 
                            <div id="tab" class="tab_content">
                                <table width="980" border="0" cellpadding="4" cellspacing="1">
                                    <tr>
                                        <td bgcolor="#DBDBDB"><table width="100%" border="0" cellpadding="4" cellspacing="1">
                                                <tr>
                                                    <td height="17" bgcolor="#FFFFFF"><h3>&nbsp;</h3>
                                                        <div id="container">
                                                            <div id="dynamic">
                                                                <div id="container">
                                                                    <div id="dynamic">

                                                                        <button id="add">新增</button><br>
                                                                        <div  align="right"> 
                                                                            品牌：<{html_options name=brand options=$menu_brand selected=$search_brand}><!-- <input type="hidden" name="code" id="code" value="<{$search_brand}>"> --> 
                                                                            區域：<input type="hidden" name="zip" id="zip" value="<{$search_zip}>"> 

                                                                                    <input type="hidden" maxlength="6" name="zipF" id="zipF" readonly="readonly" />
                                                                                     <select class="input-text-big" name="country" id="country" >
                                                                                        <{$country}>
                                                                                    </select>
                                                                                    <span id="areaR">
                                                                                        <select class="input-text-big" name="area" id="area" >
                                                                                        <{$area}>
                                                                                        </select>
                                                                                               
                                                                                        
                                                                                    </span>
                                                                            業務：<select id="salesman"><{$salesman}></select>
																			</div>
                                                                       <br>
                                                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%">仲介店編號</th>
                                                                                    <th width="15%">仲介店<bR>品牌名稱</th>
                                                                                    <th width="20%">仲介店名</th>
                                                                                    <th width="25%">公司全名</th>
                                                                                    <th width="10%">合作契約書</th>
                                                                                    <th width="12%">服務費先行撥付同意書</th>
                                                                                    <th width="8%">狀態</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td colspan="5" class="dataTables_empty">Loading data from server</td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th colspan="7"></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                                </td>
                                                                </tr>
                                                                </table></td>
                                                                </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        </div></div>
                                                        <div id="footer">
                                                            <p>2012 第一建築經理股份有限公司 版權所有</p>
                                                        </div>
                                                        </body>
                                                        </html>