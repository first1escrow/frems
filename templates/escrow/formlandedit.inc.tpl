<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#cancel').live('click', function () {
                     $('#form_back').submit();
                 });
                $('#save').live('click', function () {
                    $('#form_land').submit();
                });
                $('#count').live('click', function () {
                    $('tbody#item');
                });

               
                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
                $('#count').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
               $('#cancel').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );


            });
            function AddRow(){
                var count = $('[name="new_cCategory[]"]').length+1;
                var cloneRow = $('#copy').clone().attr("class",'row_'+count);

                cloneRow.find('[name*="new_cCategory[]"]').attr('onchange', 'setPower("new",'+count+')');
                cloneRow.find('input[name*="new_cCategory[]"]').val("");
                cloneRow.find('input[name*="new_cLevelUse[]"]').val("");
                cloneRow.find('input[name*="new_cMeasureTotal[]"]').val("");
                cloneRow.find('input[name*="new_cPower1[]"]').val("");
                cloneRow.find('input[name*="new_cPower2[]"]').val("");
                cloneRow.find('input[name*="new_cMeasureMain[]"]').val("");
                cloneRow.appendTo('#row');
                   
            }

            function setPower(cat,id){

                var txt ='';

                if (cat == 'new') {
                    txt = 'new_';
                }

              
                if (cat != 'new') {
                    if ($(".row_"+id+" [name='"+txt+"cPower1[]']").val() == 0 && $(".row_"+id+" [name='"+txt+"cPower2[]']").val() == 0) {

                        if ($(".row_"+id+" [name='"+txt+"cCategory[]']").val() == 1 || $(".row_"+id+" [name='"+txt+"cCategory[]']").val() == 2) {
                            $(".row_"+id+" [name='"+txt+"cPower1[]").val(1);
                            $(".row_"+id+" [name='"+txt+"cPower2[]").val(1);
                        }else{
                            $(".row_"+id+" [name='"+txt+"cPower1[]").val(0);
                            $(".row_"+id+" [name='"+txt+"cPower2[]").val(0);
                        }
                        
                    }

                }else{
                    if ($(".row_"+id+" [name='"+txt+"cCategory[]']").val() == 1 || $(".row_"+id+" [name='"+txt+"cCategory[]']").val() == 2) {
                            $(".row_"+id+" [name='"+txt+"cPower1[]").val(1);
                            $(".row_"+id+" [name='"+txt+"cPower2[]").val(1);
                    }else{
                            $(".row_"+id+" [name='"+txt+"cPower1[]").val(0);
                            $(".row_"+id+" [name='"+txt+"cPower2[]").val(0);
                    }
                }

               


            }
        </script>
        <style type="text/css">
            .add {
                padding:5px 10px 5px 10px ;
                color:#212121 ;
                background-color:#FFD78C ;
                margin:2px ;
                border:1px outset #F8ECE0 ;
                cursor:pointer ;
                font-size: 15px;
            }
            .add:hover {
                padding:5px 10px 5px 10px ;
                color:#212121 ;
                background-color:orange;
                margin:2px;
                border:1px outset #F8ECE0;
                cursor:pointer;
                font-size: 15px;
            }
            #tabs {
                width:980px;
                margin-left:auto; 
                margin-right:auto;
            }

            #tabs table th {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
            }

            #tabs table th .sml {
                text-align:right;
                background: #E4BEB1;
                padding-top:10px;
                padding-bottom:10px;
                font-size: 10px;
            }

            #users {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #detail {
                margin-left:auto; 
                margin-right:auto;
                width:750px;
            }

            #ec_money{
                text-align:right;
            }

            #pay_income{
                text-align:right;
            }

            #pay_spend {
                text-align:right;
            }

            #pay_total {
                text-align:right;
            }

            .input-text-per{
                width:96%;
            }

            .input-text-big {
                width:120px;
            }

            .input-text-mid{
                width:80px;
            }

            .input-text-sml{
                width:36px;
            }

            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }

            .no-border {
                border-top:0px ;
                border-left:0px ;
                border-right:0px ;
            }

            .tb-title {
                font-size: 18px;
                padding-left:15px; 
                padding-top:10px; 
                padding-bottom:10px; 
                background: #E4BEB1;
            }
            .th_title_sml {
                font-size: 10px;
            }
            .sign-red{
                color: red;
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                            <div id="tabs">
                                <div id="tabs-contract">
                                    <div> </div>
                                    <form id="form_land" action="formlandsave.php" method="POST" >
                                    <table border="0" width="100%">
                                        <tr>
                                            <th width="14%"><center>項目</center></th>
                                            <th><center>樓層用途</center></th>
                                            <th width="14%"><center>總面積</center></th>
                                            <th width="14%"><center>權利分子</center></th>
                                            <th width="14%"><center>權利分母</center></th>
                                            <th width="14%"><center>面積</center></th>
                                        </tr>
                                        <tbody id="item">
                                        
                                            <input type="hidden" name="bitem" value="<{$bitem}>">
                                            <input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>">
                                            <input type="hidden" name="new_item" value="<{$total}>">
                                                <{foreach from=$data key=key item=item}>
                                            <tr class="row_<{$item.cId}>">
                                                <td><{html_options name="cCategory[]" onchange="setPower('old',<{$item.cId}>)" class="input-text-per" options=$menu_category selected=$item.cCategory}></td>
                                                <td>
                                                    <input type="hidden" name="cId[]" value="<{$item.cId}>">
                                                    <input type="text" class="input-text-per" name="cLevelUse[]" value="<{$item.cLevelUse}>" />
                                                </td>
                                                <td><input type="text" class="input-text-per" name="cMeasureTotal[]" value="<{$item.cMeasureTotal}>" /></td>
                                                <td><input type="text" class="input-text-per" name="cPower1[]" value="<{$item.cPower1}>" /></td>
                                                <td><input type="text" class="input-text-per "name="cPower2[]" value="<{$item.cPower2}>" /></td>
                                                <td><input type="text" class="input-text-per "name="cMeasureMain[]" value="<{$item.cMeasureMain|number_format:2:'.':''}>" readonly /></td>
                                            </tr>
                                                <{/foreach}>
                                           
                                            </tbody>
                                        
                                    </table>
                                    
                                    <table border="0" width="100%" id="row">
                                        <tr id="copy" class="row_0">
                                                <td width="14%"><{html_options name="new_cCategory[]" class="input-text-per" onchange="setPower('new',0)" options=$menu_category}></td>
                                                <td>
                                                    <input type="text" class="input-text-per" name="new_cLevelUse[]" value="" />
                                                </td>
                                                <td width="14%"><input type="text" class="input-text-per" name="new_cMeasureTotal[]" value="" /></td>
                                                <td width="14%"><input type="text" class="input-text-per" name="new_cPower1[]" value="" /></td>
                                                <td width="14%"><input type="text" class="input-text-per "name="new_cPower2[]" value="" /></td>
                                                <td width="14%"><input type="text" class="input-text-per "name="new_cMeasureMain[]" value="" readonly /></td>
                                        </tr>
                                    </table>
                                    </form>
                                </div>
                            </div>
                            <center>
                                <br/>
                                <{if $cSignCategory == 1}>
                                <button id="save">儲存</button>
                                <{/if}>
                                <button id="cancel">取消</button>

                                <input type="button" value="增加一列" onclick="AddRow()" name="add" class="add">
                            </center>
                            <form name="form_back" id="form_back" method="POST"  action="formbuyowneredit.php">
                             <input type="hidden" name="id" value="<{$cCertifiedId}>">
                            </form>
                        </div>
                    </div>
                </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>










