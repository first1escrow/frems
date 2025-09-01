<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>

        <script type="text/javascript">
            $(document).ready(function() {
                var aSelected = [];
                 // setInterval("RemindFaileCase()", 60000) ; //出款簡訊提醒
                 $('.chgyr').change(function() {
                    $('#chYear').submit() ;
                }) ;

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
                    "sAjaxSource": "paymenttb.php?t="+$("[name='t']").val()+'&f='+$("[name='f']").val(),
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
                    $('form[name=form_edit]').attr('action', 'paymentCaseDetail.php');
                    $('form[name=form_edit] input[name=id]').val(tmp);
                    $('form[name=form_edit]').submit();
                    // var url = 'paymentCaseDetail.php?id='+tmp ;
                    // $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
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
            function RemindFaileCase(){
                $.ajax({
                    url: 'remindFaileCase.php',
                    type: 'POST',
                    dataType: 'html',
                })
                .done(function(txt) {
                    // 
                    // console.log(txt);
                    if (txt != '') {
                        alert(txt);   
                    }
      
                });
                
            }

        </script>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='' />
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
                                                                       <br>
                                                                       <table width="980" border="0" cellpadding="4" cellspacing="1">
                                                                            <tr style="padding:5px;background-color:white;">
                                                                                <td style="padding: 15px;">
                                                                                    <form method="POST" id="chYear">
                                                                                    年度：<select name="f" class="chgyr"><{$yr.f}></select> 年度 ~ <select name="t" class="chgyr"><{$yr.t}></select> 年度
                                                                                    </form>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                        <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th width="10%">案件編號</th>
                                                                                    <th width="10%">撥款時間</th>
                                                                                    <th width="10%">收費方式</th>
                                                                                    <th width="20%"> 金額</th>
                                                                                    <th width="20%">款項狀態</th>
                                                                                    <th width="20%">狀態</th>
                                                                                    
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