<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <{include file='meta.inc.tpl'}>
    <script type="text/javascript">
    $(document).ready(function() {
        if ("<{$disabled}>" == 1) {
            var array = "input,select,textarea";
                   
            $(".tb").find(array).each(function() {
                $(this).attr('disabled', true); 
            }); 
        }

        // $('.bank').combobox();  
        if ("<{$disabled}>" != 1) {
              setBankAutoComplete('bank');   
        }    
        
         
        $('#add').button( {
            icons:{
                primary: "ui-icon-info"
            }
        });
        $('#del').button({
            icons:{
                primary: "ui-icon-locked"
            }
        });
         $('#loading').dialog('close');
    });
    function getBankBranch(){
        var x = $('[name="bank3"]').val();
        var _number = Math.random();
        var kind = 'b4';
        var url = "../bank/_bank_select.php?i=" + _number + "&bank3=" + x;//+"&b4="+b2
          //alert(url);
          $.ajax({
            url: url,
            error: function (xhr) {
              //alert(xhr);
              alert("error!!");
            },
            success: function (response) {
              $(".b4").empty();
              $(".b4").append(response);
              setBankAutoComplete(kind);
            }
        });
    }
    function setBankAutoComplete(kind){
        $.widget( "ui.combobox", {
            _create: function() {
                var input,
                    self = this,
                    select = this.element.hide(),
                    selected = select.children( ":selected" ),
                    value = selected.val() ? selected.text() : "",
                    wrapper = this.wrapper = $( "<span>" )
                        .addClass( "ui-combobox" )
                        .insertAfter( select );

                input = $( "<input>" )
                    .appendTo( wrapper )
                    .val( value )
                    .addClass( "ui-state-default ui-combobox-input" )
                    .autocomplete({
                        delay: 0,
                        minLength: 0,
                        source: function( request, response ) {
                            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                            response( select.children( "option" ).map(function() {
                                var text = $( this ).text();
                                if ( this.value && ( !request.term || matcher.test(text) ) )
                                    return {
                                        label: text.replace(
                                            new RegExp(
                                                "(?![^&;]+;)(?!<[^<>]*)(" +
                                                $.ui.autocomplete.escapeRegex(request.term) +
                                                ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                            ), "<strong>$1</strong>" ),
                                        value: text,
                                        option: this
                                    };
                            }) );
                        },
                       select: function( event, ui ) {
                            ui.item.option.selected = true;
                            self._trigger( "selected", event, {
                                item: ui.item.option
                            });
                            select.trigger("change");                            
                        },
                        autocomplete : function(value) {
                            console.log(value);
                            this.element.val(value);
                            this.input.val(value);
                        },
                        change: function( event, ui ) {
                            if ( !ui.item ) {
                                var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                    valid = false;
                                select.children( "option" ).each(function() {
                                    if ( $( this ).text().match( matcher ) ) {
                                        this.selected = valid = true;
                                        $("[name='']")
                                        return false;
                                    }
                                });
                                if ( !valid ) {
                                    // remove invalid value, as it didn't match anything
                                    $( this ).val( "" );
                                    select.val( "" );
                                    input.data( "autocomplete" ).term = "";
                                    return false;
                                }
                            }
                            
                           
                            
                        }
                    })
                    .addClass( "ui-widget ui-widget-content ui-corner-left" );

                input.data( "autocomplete" )._renderItem = function( ul, item ) {
                    return $( "<li></li>" )
                        .data( "item.autocomplete", item )
                        .append( "<a>" + item.label + "</a>" )
                        .appendTo( ul );
                };

                $( "<a>" )
                    .attr( "tabIndex", -1 )
                    .attr( "title", "Show All Items" )
                    .appendTo( wrapper )
                    .button({
                        icons: {
                            primary: "ui-icon-triangle-1-s"
                        },
                        text: false
                    })
                    .removeClass( "ui-corner-all" )
                    .addClass( "ui-corner-right ui-combobox-toggle" )
                    .click(function() {
                        // close if already visible
                        if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
                            input.autocomplete( "close" );
                            return;
                        }

                        // work around a bug (likely same cause as #5265)
                        $( this ).blur();

                        // pass empty string as value to search for, displaying all results
                        input.autocomplete( "search", "" );
                        input.focus();
                    });
            },

            destroy: function() {
                this.wrapper.remove();
                this.element.show();
                $.Widget.prototype.destroy.call( this );
            }
        });
        $("." + kind).combobox();

    }
    function save(){
        if ($('[name="bank4"]').val() == '' || $('[name="bank3"]').val() == '') {
            alert('請填寫解匯行');
            return false;
        }else if($('[name="t_name"]').val() == ''){
            alert('請填寫戶名');
            return false;
        }else if($('[name="t_account"]').val() == ''){
             alert('請填寫帳號');
            return false;
        }

        $("[name='formsave']").submit();
        
    }
    function back(){
        location.href ="paymentList.php";
    }
    function setCode2(){

        // console.log() ;
        $("[name='code2']").val($("[name='export']").find(':selected').text());
        // $("[name='export']").text();
    }    
    </script>
    <style>
        .tb {
            width: 70%;
            margin-left:auto; 
            margin-right:auto;
            border: 1px solid #999;
        }

        .tb th {
            text-align:right;
            background: #E4BEB1;
            padding-top:10px;
            padding-bottom:10px;
            width: 40%;
             border: 1px solid black;
        }
        .tb td {
            padding-left: 10px;
            padding-top:10px;
            padding-bottom:10px;
            border: 1px solid black;
        }
        .tb-title{
            font-size: 18px;
            padding-left:15px; 
            padding-top:10px; 
            padding-bottom:10px; 
            background: #E4BEB1;
            font-weight:bold;
            text-align:center;
        }
        .ui-combobox-input {
            margin: 0;
            padding: 0.1em;
            width:200px;
        }
        .ui-autocomplete {
            width:200px;
            max-height: 300px;
            overflow-y: auto;
            /* prevent horizontal scrollbar */
            overflow-x: hidden;
            /* add padding to account for vertical scrollbar */
            padding-right: 20px;
        }

        .ui-autocomplete-input {
            width:200px;
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
                <div id="menu-lv2"></div><br/> 
                    <div id="tab" class="tab_content">
                        <table width="980" border="0" cellpadding="4" cellspacing="1">
                            <tr>
                                <td bgcolor="#DBDBDB">
                                    <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                        <tr>
                                            <td height="17" bgcolor="#FFFFFF"><h3>&nbsp;</h3>
                                                <div id="container">
                                                     <div id="dynamic">
                                                        <center>
                                                        
                                                        <form action="" name="formsave" method="POST">
                                                        <table  border="0" cellpadding="0" cellspacing="0" class="tb">
                                                            <tr>
                                                                <td colspan="2" class="tb-title">
                                                                    出款建檔
                                                                    <input type="hidden" name="caseNo" value="<{$dataExpense.eDepAccount}>">
                                                                    <input type="hidden" name="id" value="<{$id}>">
                                                                    <input type="hidden" name="bank" value="<{$dataExpense.bank}>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>角色：</th>
                                                                <td width="132">
                                                                    <label for="target"></label>
                                                                    *
                                                                     <{html_options name=target options=$menu_target selected="$data.tKind"}>
                                                                  <!--   <select name="target" id="target"  >
                                                                        <option value="">角色選擇</option>
                                                                        <option value="賣方">賣方</option>
                                                                        <option value="買方">買方</option>
                                                                        <option value="地政士">地政士</option>
                                                                        <option value="仲介">仲介</option>
                                                                        <option value="保證費">保證費</option>
                                                                    </select> -->
                                                                  
                                                                </td>
                                                            </tr> 
                                                            <tr>
                                                                <th>交易類別：</th>
                                                                <td>
                                                                    *
                                                                    <input type="hidden" name="code2" id="code2" value="聯行轉帳" />
                                                                    <select name="export" id="export" onchange="setCode2()">
                                                                        <option value="01">聯行轉帳</option>
                                                                        <option value="01">虛轉虛</option>
                                                                        <option value="02">跨行代清償</option>
                                                                        <option value="03">聯行代清償</option>
                                                                        <!-- <option value="04">大額繳稅</option>
                                                                        <option value="05">臨櫃開票</option>
                                                                        <option value="05">臨櫃領現</option> -->
                                                                    </select>
                                                                    <br />
                                                                    *
                                                                    <{html_options name=objKind options=$menu_kind selected=$data.tObjKind}>
                                                                    <!-- <select name="objKind" id="objKind" class="objKind">
                                                                        <option value="" selected="selected" >項目</option>
                                                                        <option value="其他">其他</option>
                                                                        <option value="賣方先動撥">賣方先動撥</option>
                                                                        <option value="仲介服務費">仲介服務費</option>
                                                                        <option value="扣繳稅款">扣繳稅款</option>
                                                                        <option value="代清償">代清償</option>
                                                                        <option value="點交(結案)">點交(結案)</option>
                                                                        <option value="調帳">調帳</option>
                                                                        <option value="解除契約">解約/終止履保</option>
                                                                        <option value="保留款撥付">保留款撥付</option>
                                                                        <option value="建經發函終止">建經發函終止</option>
                                                                    </select> -->
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th> *解匯行：</th>
                                                                <td>
                                                                    
                                                                   
                                                                    <label for="bank3"></label>
                                                                
                                                                    <{html_options name=bank3 options=$menu_bank class="bank" selected=$data.bank  onchange="getBankBranch()"}>
                                                                    <br><br>

                                                                    
                                                                    <select name="bank4"  style="width:130px;" class="bank b4" onchange="" >
                                                                        <option value="" >選擇分行</option>
                                                                        <{$menu_bank_branch}>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                           
                                                            <tr>
                                                                <th> *戶名：</th>
                                                                 <td>
                                                                    
                                                                   
                                                                    <label for="t_name"></label>
                                                                    <input name="t_name" type="text" id="t_name" size="14" value="<{$data.tAccountName}>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>*帳號：</th>
                                                                <td>    
                                                                    <br />
                                                                    
                                                                    <label for="t_account"></label>
                                                                    <input name="t_account" type="text" size="14" maxlength="14" value="<{$data.tAccount}>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>金額NT$</th>
                                                                <td><input type="text" name="t_money" id="t_money" value="<{$data.tMoney}>"></td>
                                                            </tr>
                                                            <tr> 
                                                                <th>證號：</th>
                                                                <td>
                                                                   
                                                                    <label for="pid"></label>
                                                                    <input name="pid" type="text" id="pid" size="10" value="<{$data.tAccountId}>" />
                                                               
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>EMail：</th>
                                                                <td> <label for="email"></label>
                                                                    <input name="email" type="text" id="email" size="13" value="<{$data.tEmail}>" /></td>
                                                            </tr>
                                                            <tr>
                                                                <th>FAX：</th>
                                                                <td>  <label for="fax"></label>
                                                                    <input name="fax" type="text" id="fax[]" size="15" value="<{$data.tFax}>" />
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th>*附言(勿按ENTER換行)：</th>
                                                                <td width="167"><br />
                                                                   
                                                                    <label for="t_txt"></label>
                                                                    <textarea name="t_txt" id="t_txt" cols="20" class="t_txt" rows="5" ><{$data.tTxt}></textarea>
                                                                </td>
                                                            </tr>
                                                           <!--  <tr>
                                                                <td width="130" align="center" colspan="2">
                                                                    不發送簡訊<br>
                                                                    <input type="checkbox" name="tSend" id="tSend" value="1" checked="checked" />

                                                                </td>
                                                                
                                                            </tr> -->
                                                            
                                                          </table>   
                                                          <div>
                                                            <{if $disabled != 1 }>
                                                            <input type="button" value="送出" onclick="save()"> &nbsp;&nbsp;
                                                            <{/if}>
                                                            <input type="button" value="返回" onclick="back()">  </div>     
                                                                 
                                                        </form>
                                                        </center>
                                                    </div>
                                               </div>
                                            </td>
                                        </tr>
                                   </table>
                                </td>
                            </tr>
                        </table>
                    </div>
            </div>
        </div>
</div>
<div id="footer">
    <p>2012 第一建築經理股份有限公司 版權所有</p>
</div>
</body>
</html>