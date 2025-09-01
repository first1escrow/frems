<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                var type ="<{$type}>";

                if (type=='add') {
                    $('[name="add"]').hide();
                     $('#show1').hide();
                    $('#show2').show();
                }else
                {
                    $('#show1').show();
                    $('#show2').hide();
                }

                $('#cancel').live('click', function () {
                     $('#form_back').submit();

                 });
                $('#save').live('click', function () {
                    $('#form_land').submit();
                    alert('已儲存');
                });
              
              $('#check1').live('click', function () {
                   var check =$('#check1:checked').val();

                   if(check!=1)
                   {
                   		$('[name="new_cOwnerType"]').removeAttr('checked');
                   }

                   if (check!=2) {

                    $('[name="new_cOwner"]').removeAttr('checked');
                   }

                });



             

             $('[name="add"]').live('click', function () {
                   
                $('#show2').show();
                 $('[name="add"]').hide();

                });

           
                $('#save').button( {
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
        </script>
        <style type="text/css">
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
                            <form id="form_land" action="formcarsave.php" method="POST" >
                                <div id="tabs">

                                    <div id="tabs-contract"> 

                                            <{foreach from=$data key=key item=item}>
    	                                    <table border="0" width="100%" id="show1">
    	                                    	<tr>
    	                                        	<td colspan="2" align="left" class="tb-title">
    	                                        		停車位標示
    	                                        		<input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>">
    	                                        		<input type="hidden" name="cId[]" value="<{$item.cId}>">
                                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                         <{if $cSignCategory == 1}>
                                                            <a href="formcardelete.php?cid=<{$item.cId}>&id=<{$cCertifiedId}>">刪除</a>
                                                        <{/if}>
    	                                        	</td>
    	                                        </tr>
    	                                       <!--  <tr>
    	                                        	<th>停車位數：</td>
    	                                        	<td><input type="input" size="5" value="<{$item.cTotal}>" name='cTotal'>位</td>
    	                                        </tr> -->
    	                                         <tr>
    	                                        	<th>樓層：</td>
    	                                        	<td> 
                                                         <{if $item.cGround==1}>
                                                                 <input type="radio" name="cGround<{$item.cId}>" value="1" checked="checked">地上
                                                                <input type="radio" name="cGround<{$item.cId}>" value="2">地下
                                                            <{else}>
                                                                <input type="radio" name="cGround<{$item.cId}>" value="1">地上
                                                                <input type="radio" name="cGround<{$item.cId}>" value="2" checked="checked">地下
                                                            <{/if}>

    	                                        	
    	                                        	<input type="text" name="cFloor[]" value="<{$item.cFloor}>" size="5">層
    	                                        	</td>
    	                                        </tr>
    	                                         <tr>
    	                                        	<th>編號：</td>
    	                                        	<td>	                                        		
    	                                        		<input type="text" name="cNo[]" value="<{$item.cNo}>">
    	                                        	</td>
    	                                        </tr>
    	                                        <tr>
    	                                        	<th>停車場類別：</td>
    	                                        	<td>
                                                        <{if $item.cCategory==1 }>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="1" checked>坡道平面式
                                                        <{else}>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="1">坡道平面式
                                                        <{/if}>

    	                                        	    <{if $item.cCategory==2 }>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="2" checked>昇降平面式
                                                        <{else}>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="2">昇降平面式
                                                        <{/if}>

                                                         <{if $item.cCategory==3 }>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="3" checked>坡道機械式
                                                        <{else}>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="3">坡道機械式
                                                        <{/if}>

                                                         <{if $item.cCategory==4 }>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="4" checked>昇降機械式
                                                        <{else}>
                                                        <input type="radio" name="cCategory<{$item.cId}>" value="4">昇降機械式
                                                        <{/if}>

    	                                        	</td>
    	                                        </tr>
    	                                        <tr>
    	                                        	<th>權屬：</td>
    	                                        	<td>
                                                        <{if $item.cBelong == 1}>
                                                        <input type="checkbox" name="old<{$item.cId}>[]" value="1" checked>所有權：
                                                        <{else}>
                                                        <input type="checkbox" name="old<{$item.cId}>[]" value="1" >所有權：
                                                        <{/if}>
                                                        
                                                            <{if $item.cOwnerType==1}>
                                                                 <input type="radio" name="cOwnerType<{$item.cId}>[]" value="1" checked="checked" onclick="btnCheck(cOwnerType<{$item.cId}>[])">有獨立權狀
                                                                <input type="radio" name="cOwnerType<{$item.cId}>[]" value="2" onclick="btnCheck(cOwnerType<{$item.cId}>[])">持分併入公共設施<br>
                                                            <{else}>
                                                                <input type="radio" name="cOwnerType<{$item.cId}>[]" value="1" onclick="btnCheck(cOwnerType<{$item.cId}>[])">有獨立權狀
                                                                <input type="radio" name="cOwnerType<{$item.cId}>[]" value="2" checked="checked" onclick="btnCheck(cOwnerType<{$item.cId}>[])">持分併入公共設施<br>
                                                            <{/if}>

                                                        <{if $item.cBelong == 2}>
                                                        <input type="checkbox" name="old<{$item.cId}>" value="2" checked>僅有使用權：
                                                        <{else}>
                                                        <input type="checkbox" name="old<{$item.cId}>" value="2">僅有使用權：
                                                        <{/if}>
                                                        
                                                            <{if $item.cOwner==3 }>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="3" checked>須承租繳租金
                                                            <{else}>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="3">須承租繳租金
                                                            <{/if}>

                                                            <{if $item.cOwner==4 }>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="4" checked>需定期抽籤
                                                            <{else}>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="4">需定期抽籤
                                                            <{/if}>

                                                            <{if $item.cOwner==5 }>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="5" checked>需排隊等候
                                                            <{else}>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="5">需排隊等候
                                                            <{/if}>
                                                            
                                                            <{if $item.cOwner==6 && $item.cOther!='' }>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="6" checked>其他
                                                            <{else}>
                                                            <input type="radio" name="cOwner<{$item.cId}>" value="6">其他
                                                            <{/if}>

                                                        <!--  <{html_radios name=cOwnerType options=$Ownertype selected=$item.cOwnerType}><br> -->
    	                                        		<!-- <{html_checkboxes name=cOwner options=$owner selected=$item.cOwner}> -->

                                                        <input type="txt" name="cOther[]" value="<{$item.cOther}>">
    	                                        	</td>
    	                                        </tr>
                                               
    	                                    </table>
                                            <{/foreach}>

                                    </div>
                                    <div id="tabs-contract"> 

                                            <table border="0" width="100%" id="show2">
                                                <tr>
                                                    <td colspan="2" align="left" class="tb-title">
                                                        停車位標示
                                                        <input type="hidden" name="cCertifiedId" value="<{$cCertifiedId}>">
                                                        <!-- <input type="hidden" name="cId" value="<{$item.cId}>"> -->
                                                    </td>
                                                </tr>
                                               <!--  <tr>
                                                    <th>停車位數：</td>
                                                    <td><input type="input" size="5" value="<{$item.cTotal}>" name='cTotal'>位</td>
                                                </tr> -->
                                                 <tr>
                                                    <th>樓層：</td>
                                                    <td>
                                                    <{html_radios name=new_cGround options=$Ground }>
                                                    
                                                    <input type="text" name="new_cFloor" value="" size="5">層
                                                    </td>
                                                </tr>
                                                 <tr>
                                                    <th>編號：</td>
                                                    <td>                                                    
                                                        <input type="text" name="new_cNo" value="">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>停車場類別：</td>
                                                    <td>
                                                         <{html_radios name=new_cCategory options=$Category}>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>權屬：</td>
                                                    <td>
                                                        <input type="checkbox" name="new[]"  value="1" id="check1">所有權： <{html_radios name=new_cOwnerType options=$Ownertype }><br>

                                                         <input type="checkbox" name="new[]"  value="2" id="check1">僅有使用權：<{html_radios name=new_cOwner options=$owner }>

                                                        <input type="txt" name="new_cOther" value="">
                                                    </td>
                                                </tr>
                                               
                                            </table>
      
                                    </div>
                                    <input type="button" name="add" value="新增欄位">
                                </div>
                            </form>
                            <center>
                                <br/>
                                <{if $cSignCategory == 1}>
                                    <button id="save">儲存</button>
                                    
                                <{/if}>
                                <button id="cancel">取消</button>
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










