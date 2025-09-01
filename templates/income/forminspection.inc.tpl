<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <script src="/js/jquery.colorbox.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                uploadFileList();

                $(".inline").colorbox({
                    iframe:true, 
                    width:"1000px", 
                    height:"100%"
                });

				getMarguee(<{$smarty.session.member_id}>);

				setInterval(function() {
                    getMarguee2(<{$smarty.session.member_id}>);
                }, 180000);

                $("#banktran").hide();

				$('#dialog').dialog({
					autoOpen: false,
					modal: true,
					buttons: {
						"是": function() {
							$(this).dialog("close") ;
							let ii = $('#getId').val() ;
							let cc = $('[name="cCertifiedId"]').val() ;
							
							$.post('formsms.php',{'id':ii,'cCertifiedId':cc},function(txt) {
								alert(txt) ;
								$('#form_back').submit();
							}) ;
						},
						"否": function() {
							$(this).dialog("close") ;
							$('#form_back').submit();
						}
					}
				}) ;
				
				let request = $.ajax({  
                    url: "/includes/scrivener/bankcodesearch.php",
                    type: "POST",
                    data: {
                        id:$('[name=scrivener_id]').val()
                    },
                    dataType: "json"
                });

                request.done(function (data) {
                    $.each(data, function(key,item) {
                        if (key == 'sOffice') {
                            $('[name=scrivener_office]').val(item);
                        }

                        if (key == 'sMobileNum') {
                            $('[name=scrivener_mobilenum]').val(item);
                        }

                        if (key == 'sTelArea') {
                            $('[name=scrivener_telarea]').val(item);
                        }

                        if (key == 'sTelMain') {
                            $('[name=scrivener_telmain]').val(item);
                        }

                        if (key == 'sFaxArea') {
                            $('[name=scrivener_faxarea]').val(item);
                        }

                        if (key == 'sFaxMain') {
                            $('[name=scrivener_faxmain]').val(item);
                        }

                        if (key == 'sZip1') {
                            $('[name=scrivener_zip]').val(item);
                        }

                        if (key == 'sAddress') {
                            $('[name=scrivener_addr]').val(item);
                        }
                    });
                });

                $('[name=scrivener_id]').live('change', function () {
                    let request = $.ajax({  
                        url: "/includes/scrivener/bankcodesearch.php",
                        type: "POST",
                        data: {
                            id:$('[name=scrivener_id]').val()
                        },
                        dataType: "json"
                    });

                    request.done(function(data) {
                        $.each(data, function(key,item) {
                            if (key == 'sOffice') {
                                $('[name=scrivener_office]').val(item);
                            }

                            if (key == 'sMobileNum') {
                                $('[name=scrivener_mobilenum]').val(item);
                            }

                            if (key == 'sTelArea') {
                                $('[name=scrivener_telarea]').val(item);
                            }

                            if (key == 'sTelMain') {
                                $('[name=scrivener_telmain]').val(item);
                            }

                            if (key == 'sFaxArea') {
                                $('[name=scrivener_faxarea]').val(item);
                            }

                            if (key == 'sFaxMain') {
                                $('[name=scrivener_faxmain]').val(item);
                            }

                            if (key == 'sZip1') {
                                $('[name=scrivener_zip]').val(item);
                            }

                            if (key == 'sAddress') {
                                $('[name=scrivener_addr]').val(item);
                            }
                        });
                    });
                });

                $('#add').live('click', function () {
                    let input = $('input');
                    let textarea = $('textarea');
                    let select = $('select');
                    let arr_input = new Array();

                    $.each(select, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(textarea, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(input, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    let obj_input = $.extend({}, arr_input);
                    let request = $.ajax({  
                        url: "/includes/escrow/contractadd.php",
                        type: "POST",
                        data: obj_input,
                        dataType: "html"
                    });

                    request.done( function( msg ) {
                        alert(msg);
                    });
                });

                $('#save').live('click', function () {
                    let checkChecklist = <{$checkChecklist}>;
                    if (checkChecklist > 0) {
                        alert("點交單已製作 請留意是否需要重新製作");
                    }

					let input = $('input');
                    let textarea = $('textarea');
                    let select = $('select');
                    let arr_input = new Array();

                    $.each(select, function(key,item) {
                        if ($(item).attr("name") == 'otherTitle[]') {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }

                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        } else if ($(item).attr("name") == 'otherT[]') {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }

                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        } else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                    });

                    $.each(textarea, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });

                    $.each(input, function(key,item) {
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }

                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        } else {
                            if ($(item).attr("name") == 'otherMoney[]') {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }

                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            } else if ($(item).attr("name") == 'otherM[]') {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }

                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            } else if ($(item).attr("name") == 'otherId[]') {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }

                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            } else {
                                arr_input[$(item).attr("name")] = $(item).attr("value");
                            }
                        }
                    });

                    let ck = checkTotal('');
                    if (ck == 1) {
                        alert("買方服務費不等於下方輸入的金額");
                        return false;
                    } else if (ck == 2) {
                        alert("買方溢入款不等於下方輸入的金額");
                        return false;
                    } else if (ck == 3) {
                        alert("金額明細加總不等於入帳金額");
                        return false;
                    }
                    
                    $('#save').prop('disabled',true) ;

                    let obj_input = $.extend({}, arr_input);
                    let request = $.ajax({  
                        url: "/includes/income/formsave.php",
                        type: "POST",
                        data: obj_input,
                        dataType: "html"
                    });

                    request.done(function (msg) {
                        let url='<{$sms_path}>sms_list.php?id=<{$data_income.id}>&cid=<{$data_income.CertifiedId}>';
                        $.colorbox({iframe:true, width:"1000px", height:"100%", href:url,onClosed:function(){location.href='listinspection.php'; }}) ;
                    });
                });
                
                $('#toadd').live('click', function () {
                    alert('系統未有此筆案件，請新增案件!');
                    window.location = "/escrow/formbuyowneradd.php";
                });
                
                $( "#tabs" ).tabs({
                    selected: 0
                });

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
                                change: function( event, ui ) {
                                    checkTotal('');
                                    $('[name="ctext"]').val($( this ).val());
                                    if ( !ui.item ) {
                                        var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                            valid = false;
                                        select.children( "option" ).each(function() {
                                            if ( $( this ).text().match( matcher ) ) {
                                                this.selected = valid = true;
                                                return false;
                                            }
                                        });

                                        if (!valid) {
                                            return false;
                                        }
                                    }
                                }
                            })
                            .addClass( "ui-widget ui-widget-content ui-corner-left" );

                        input.data("autocomplete")._renderItem = function(ul, item) {
                            return $("<li></li>")
                                .data( "item.autocomplete", item )
                                .append( "<a>" + item.label + "</a>" )
                                .appendTo( ul );
                        };

                        $("<a>")
                            .attr("tabIndex", -1)
                            .attr("title", "Show All Items")
                            .appendTo(wrapper)
                            .button({
                                icons: {
                                    primary: "ui-icon-triangle-1-s"
                                },
                                text: false
                            })
                            .removeClass( "ui-corner-all" )
                            .addClass( "ui-corner-right ui-combobox-toggle" )
                            .click(function() {
                                if (input.autocomplete("widget").is(":visible")) {
                                    input.autocomplete("close");
                                    return;
                                }

                                $(this).blur();

                                input.autocomplete("search", "");
                                input.focus();
                            });
                    },

                    destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                        $.Widget.prototype.destroy.call(this);
                    }
                });

                $('[name=cc]').combobox();
                $(".ot").combobox();
                
                checkTotal('1');

                $('#save').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#toadd').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#add').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#buyer_edit').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#owner_edit').button({
                    icons:{
                        primary: "ui-icon-info"
                    }
                });
            });

            function checkTotal(cat) {
                let total = "<{$data_income.eLender}>";
                let sum = 0;
                let show = 0;
                let BMoney = parseInt($("[name='eBuyerMoney']").val().replace(',',''));
                let EMoney = parseInt($("[name='eExtraMoney']").val().replace(',',''));
                let ServiceFee = $("[name='ServiceFee']").val();
                let ExtraMoney = $("[name='ExtraMoney']").val();

                if ($("#eChangeMoney").val() > 0) {
                    total = $("#eChangeMoney").val();
                }

                if (ServiceFee =='') {
                    ServiceFee = 0;
                }

                if (ExtraMoney =='') {
                    ExtraMoney = 0;
                }

                $(".sum").each(function() {
                    if ($(this).val() != '') {
                        sum += parseInt($(this).val());
                    }
                });

                show = total - sum;

                $("#show1").text(sum);
                $("#show2").text(show);

                if (cat =='' && $("[name='eRemarkContentMark']").val() == '') {
                    setRemarkContent();
                }
            
                if ((BMoney != parseInt(ServiceFee)) && sum != 0) { 
                    //買方服務費不等於輸入金額(有輸入明細在判斷是否一樣，沒輸入就走原本寄送模式)
                    return 1;
                } else if ((EMoney != parseInt(ExtraMoney)) && sum != 0) {
                    //買方溢入款不等於輸入金額(有輸入明細在判斷是否一樣，沒輸入就走原本寄送模式)
                    return 2;
                } else if ((total != sum) && sum != 0) { 
                    //總計額不等於輸入金額
                    return 3;
                } else {
                    return 4;
                }
            }

            function setRemarkContent() {
                let txt = '';
                let txt2 = '';
                let status = $("[name='eStatusRemark']").val();

                if ($("[name='SignMoney']").val() > 0 && status != 1) { // 簽約
                    txt += '+簽約款';
                    txt2 += '+簽約款' + $('[name="SignMoney"]').val();
                } else if ($("[name='SignMoney']").val() > 0) {
                    txt2 += '+簽約款' + $('[name="SignMoney"]').val();
                }

                if ($("[name='AffixMoney']").val() > 0 && status != 2) { //用印
                    txt += '+用印款';
                    txt2 += '+用印款' + $('[name="AffixMoney"]').val();
                } else if($("[name='AffixMoney']").val() > 0) {
                    txt2 += '+用印款' + $('[name="AffixMoney"]').val();
                }

                if ($("[name='DutyMoney']").val() > 0 && status != 3) { //完稅
                    txt += '+完稅款';
                    txt2 += '+完稅款'+$('[name="DutyMoney"]').val();
                } else if ($("[name='DutyMoney']").val() > 0) {
                    txt2 += '+完稅款' + $('[name="DutyMoney"]').val();
                }

                if ($("[name='EstimatedMoney']").val() > 0 && status != 4) { //尾款
                    txt += '+尾款';
                    txt2 += '+尾款' + $('[name="EstimatedMoney"]').val();
                } else if ($("[name='EstimatedMoney']").val() > 0) {
                    txt2 += '+尾款' + $('[name="EstimatedMoney"]').val();
                }

                if ($("[name='EstimatedMoney2']").val() > 0 && status != 5) { //尾款差額
                    txt += '+尾款差額';
                    txt2 += '+尾款差額' + $('[name="EstimatedMoney2"]').val();
                } else if ($("[name='EstimatedMoney2']").val() > 0) {
                    txt2 += '+尾款差額' + $('[name="EstimatedMoney2"]').val();
                }

                if ($("[name='CompensationMoney']").val() > 0 && status != 6) { //代償後餘額
                    txt += '+代償後餘額';
                    txt2 += '+代償後餘額' + $('[name="CompensationMoney"]').val();
                } else if ($("[name='CompensationMoney']").val() > 0) {
                    txt2 += '+代償後餘額' + $('[name="CompensationMoney"]').val();
                }

                if ($("[name='ServiceFee']").val() > 0) { //買方仲介服務費
                    txt += '+買方仲介服務費';
                    txt2 += '+買方仲介服務費' + $("[name='ServiceFee']").val();
                } else if ($("[name='ServiceFee']").val() > 0) {
                    txt2 += '+買方仲介服務費' + $("[name='ServiceFee']").val();
                }

                if ($("[name='ExtraMoney']").val() > 0) { //買方溢入款
                    txt += '+買方溢入款';
                    txt2 += '+買方溢入款' + $("[name='ExtraMoney']").val();
                } else if ($("[name='ExtraMoney']").val() > 0) {
                    txt2 += '+買方溢入款' + $("[name='ExtraMoney']").val();
                }

                if ($("[name='ExchangeMoney']").val() > 0) { //換約款
                    txt += '+換約款';
                    txt2 += '+換約款' + $("[name='ExchangeMoney']").val();
                } else if ($("[name='ExchangeMoney']").val() > 0) {
                    txt2 += '+換約款' + $("[name='ExchangeMoney']").val();
                }

                $(".cp").each(function() {
                    let title = $(this).find("[name='otherT[]']").val();
                    let money = $(this).find('[name="otherM[]"]').val();

                    if (title != undefined && title != ''&& money > 0) {
                        txt += '+'+title;
                    }

                    if (money != undefined && money > 0) { 
                        txt2 += '+'+title+money;
                    }

                    //新增
                    title = $(this).find("[name='otherTitle[]']").val();
                    money = $(this).find('[name="otherMoney[]"]').val();
                    if (title != undefined && title != ''&& money > 0) {
                        txt += '+' + title;
                    }

                    if (money != undefined && money > 0) { 
                        txt2 += '+' + title+money;
                    }
                });

                //入帳金額明細沒有KEY
                if (txt2 == '') {
                    txt2 +=  $("[name='eStatusRemark']").find("option:selected").text() + '';
                }

                $("[name='eRemarkContent']").val(txt);
                $("[name='eRemarkContentSp']").val(txt2);
            }

            function checkBuyerMoney() {
                let status = $("[name='eStatusRemark']").val();

                if(status == 12) {
                    const signMoney = parseMoneyInput("[name='SignMoney']");
                    const affixMoney = parseMoneyInput("[name='AffixMoney']");
                    const dutyMoney = parseMoneyInput("[name='DutyMoney']");
                    const estimatedMoney = parseMoneyInput("[name='EstimatedMoney']");
                    const estimatedMoney2 = parseMoneyInput("[name='EstimatedMoney2']");
                    const compensationMoney = parseMoneyInput("[name='CompensationMoney']");
                    const exchangeMoney = parseMoneyInput("[name='ExchangeMoney']");

                    const notBuyerTotal = signMoney + affixMoney + dutyMoney + estimatedMoney + estimatedMoney2 + compensationMoney + exchangeMoney;

                    if(notBuyerTotal > 0) {
                        alert('買方相關費用不能填寫簽約款、用印款、完稅款、尾款、尾款差額、代償後餘額、換約款');
                        $("[name='eStatusRemark']").val(0);
                        return false;
                    }
 
                    $("[name='SignMoney']").attr("disabled", true);
                    $("[name='AffixMoney']").attr("disabled", true);
                    $("[name='DutyMoney']").attr("disabled", true);
                    $("[name='EstimatedMoney']").attr("disabled", true);
                    $("[name='EstimatedMoney2']").attr("disabled", true);
                    $("[name='CompensationMoney']").attr("disabled", true);
                    $("[name='ExchangeMoney']").attr("disabled", true);
                } else {
                    $("[name='SignMoney']").attr("disabled", false);
                    $("[name='AffixMoney']").attr("disabled", false);
                    $("[name='DutyMoney']").attr("disabled", false);
                    $("[name='EstimatedMoney']").attr("disabled", false);
                    $("[name='EstimatedMoney2']").attr("disabled", false);
                    $("[name='CompensationMoney']").attr("disabled", false);
                    $("[name='ExchangeMoney']").attr("disabled", false);
                }
            }

            function parseMoneyInput(selector) {
                const raw = $(selector).val();
                const cleaned = raw ? raw.replace(/[^\d]/g, '') : '0';
                const parsed = parseInt(cleaned, 10);
                return isNaN(parsed) ? 0 : parsed;
            }

            function AddRow() {
                let num = parseInt($("[name='countRow']").val()) + 1;
                let val = $("[name='ctext']").val();

                $("#copy").clone().insertAfter('.cp:last');
                $('.cp:last').attr('id', 'n'+num);
                $( ".cp:first [name='cc']" ).combobox("destroy");
                $('.cp:first [name="cc"]').attr("value","");  
                $(".cp:first [name='cc']").combobox();
                $('.cp:last .ui-combobox').remove();
                $("[name='ctext']").val('');

                //檢查是否有重複的選項
                let ck = 0;
                $('.cp:last [name="cc"] option').each(function() {
                    if ($(this).val() == val) {
                        ck = 1;
                    }
                });
                
                if (ck == 0) {
                    $('.cp:last [name="cc"]').append('<option value="'+val+'">'+val+'</option>');
                }
                
                $('.cp:last [name="cc"]').val(val);
                $('.cp:last [name="cc"]').attr('class', 'oTxt');
                $('.cp:last [name="cc"]').attr('name', 'otherTitle[]');
                $('.cp:last [name="otherTitle[]"]').combobox();

                $('.cp:last [name="dd"]').attr('onkeyup', 'checkTotal("")');
                $('.cp:last [name="dd"]').attr('class', 'text-right sum');
                $('.cp:last [name="dd"]').attr('name', 'otherMoney[]');

                $(".cp:last #ar").attr({
                    "value": '刪除',
                    "onClick": 'del(\"n' + num + '\")'
                });

                $("[name='countRow']").val((num++));
                $('#copy [name="cc"]').val('');
                $('#copy [name="dd"]').val('');

                checkTotal('');//新增後再次檢察金額
            }

            function del(name) {
                $("#"+name).remove();
                let t = name.substr(0,1);
                let id = name.substr(1);

                if (t == 'o') {
                    $.ajax({
                        url: 'formOtherSmsDel.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"id": id},
                    })
                    .done(function(txt) {
                        if (txt=='OK') {
                            alert('刪除成功');
                        }
                    });
                }

                checkTotal('');//刪除後再次檢察金額
            }

            function setRemarkContentMark() {
                if ($("[name='eRemarkContent']").val() != '') {
                    $("[name='eRemarkContentMark']").val('x');
                } else {
                    $("[name='eRemarkContentMark']").val('');
                }
            }

            function uploadFileList(){
                $.ajax({
                    url: '../escrow/uploadContractFileList.php?id=' + $('[name=cCertifiedId]').val() + '&s=' + $("#upload_list_sort").val() + '&cat=view',
                    type: 'GET',
                    dataType: 'html',
                })
                .done(function(html) {
                    $(".upload_list_content").html(html);
                });
            }
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

            .ui-combobox-input {
                margin: 0;
                padding: 0.1em;
                width:100px;
            }

            .ui-autocomplete-input {
                width:150px;
            }

            .upload_list_title{
                margin-top: 2em;
                font-size: 1.3em;
                font-weight: normal;
                line-height: 1.6em;
                color: #4E6CA3;
                border-bottom: 1px solid #B0BED9;
                clear: both;
            }

            .table-title{
                padding: 5px;
                border: 1px solid #CCC;
                background-color: #CFDEFF;
            }

            .table-content{
                padding: 5px;
                border: 1px solid #CCC;
                background-color: #FFF;
            }
        </style>
    </head>
    <body id="dt_example">
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>

        <form name="form_add" id="form_add" method="POST"></form>

        <div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753">
                            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right">
										<div id="abgne_marquee" style="display:none;">
											<ul>
											</ul>
										</div>
									</td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"></td>
                                    <td width="14%" align="center">
                                        <h2> 登入者 <{$smarty.session.member_name}></h2>
                                    </td>
                                    <td width="5%" height="30" colspan="2">
                                        <h3><a href="/includes/member/logout.php">登出</a></h3>
                                    </td>
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
                        <div id="menu-lv2"></div>
                        <br/>   
                        <div id="tab" class="tab_content">
                            <div id="tabs">
                                <ul>
                                    <li><a href="#tabs-contract">入帳資料</a></li>
                                    <{if $smarty.session.member_pDep == 5 || $smarty.session.member_pDep == 6 || $smarty.session.member_pDep == 1}>
                                    <li><a href="#tabs-uploadList">檔案上傳列表</a></li>
                                    <{/if}>
                                </ul>

                                <div id="tabs-contract">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                入帳資料
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>銀行別︰</th>
                                            <td>
                                            <input type="hidden" name="id" id="getId" value="<{$data_income.id}>">
                                                <{html_options name=cBank options=$menu_categorybank selected=$data_case.cBank disabled="disabled"}>
                                            </td>
                                            <th>存匯入戶名︰</th>
                                            <td>
                                                <input type="text" name="" maxlength="10" class="input-text-big" value="<{$data_income.ePayTitle}>" disabled="disabled" />
                                            </td>
                                            <th>交易狀態︰</th>
                                            <td>
                                                <input type="text" name="" maxlength="10" class="input-text-big" value="<{$data_income.eTradeStatusName}>" disabled="disabled" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>存匯入日期︰</th>
                                            <td>
                                                <input type="text" maxlength="20" class="input-text-big" value="<{$data_income.eTradeDate}>" disabled="disabled" />
                                            </td>
                                            <th>
                                                買方仲介服務費︰
                                            </th>
                                            <td>
                                                NT$<input type="text" name="eBuyerMoney" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.eBuyerMoney}>" />
                                            </td>
                                            <th>買方溢入款︰</th>
                                            
                                            <td>NT$<input type="text" name="eExtraMoney" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.eExtraMoney}>" /><td>
                                        </tr>
                                        <tr>
                                            <th>存匯入金額︰</th>
                                            <td>
                                                NT$<input type="text" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.eLender}>" disabled="disabled" />
                                            </td>
                                            <th>承辦地政士︰</th>
                                            <td>
                                                <{html_options name='s' options=$menu_scrivener selected=$data_scrivener.cScrivener  disabled="disabled"}>
                                            </td>
                                            <td><td>
                                            <td><td>
                                        </tr>
                                        <tr>
                                            <th>入帳狀態︰</th>
                                            <td>
                                                <{html_options  name='eStatusIncome' options=$menu_statusincome selected=$data_income.eStatusIncome}>
                                            </td>
                                            <th>調帳金額︰</th>
                                            <td>
                                                <span style="font-size:10px">
                                                    <{foreach from=$ChangeExpense key=key item=item}>
                                                    <{$item.tMemo}> / <{$item.tMoney}>元
                                                    <{/foreach}>
                                                </span>
                                            </td>
                                            <th>調帳後餘額︰</th>
                                            <td>
                                                NT$<input type="text" id="eChangeMoney" name="eChangeMoney" maxlength="16" size="12" class="text-right" value="<{$data_income.eChangeMoney}>" disabled="disabled" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>承辦人︰</th>
                                            <td>
                                                <{html_options  name='c' options=$menu_peoplelist selected=$data_case.cUndertakerId disabled="disabled"}>
                                            </td>
                                            <th>保證號碼︰</th>
                                            <td>
                                                <input type="hidden" name="cCertifiedId" value="<{$data_income.CertifiedId}>">
                                                <input type="text"  maxlength="10" class="input-text-big" value="<{$data_income.CertifiedId}>" disabled="disabled" />
                                            </td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th>摘要類別︰</th>
                                            <td>
                                                <{html_options  name='eStatusRemark' options=$menu_categoryincome selected=$data_income.eStatusRemark onchange="setRemarkContent();checkBuyerMoney()"}>
                                            </td>
                                            <th>摘要附註︰</th>
                                            <td colspan="4">
                                                <input type="text" name="eRemarkContent" maxlength="255" class="input-text-per" value="<{$data_income.eRemarkContent}>" onkeyup="setRemarkContentMark()"/>
                                                <input type="hidden" name="eRemarkContentMark">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>案件進度︰</th>
                                            <td>
                                                <{html_options  name='cCaseProcessing' options=$menu_categroyprocession selected=$data_case.cCaseProcessing}>
                                            </td>
                                            <th>&nbsp;</th>
                                            <td colspan="3">&nbsp;</td>
                                        </tr> 
                                        <tr>
                                            <th><span class="th_title_sml">官網摘要附註顯示<br>(僅顯示代書、仲介畫面)︰</span></th>
                                            <td colspan="5"><input type="text" name="eRemarkContentSp" value="<{$data_income.eRemarkContentSp}>" style="width: 100%"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                <div style="display:inline;line-height:40px;">
                                                    入帳金額明細<input type="hidden" name="edsId" value="<{$data_income_sms.eId}>">
                                                <span style="color:red">(有填寫金額會顯示於簡訊)<{$data_income_smsCount}></span>
                                                <input type="hidden" name="countRow" value="0">
                                                </div>

                                                <div style="float:right;padding-right:10px;border:1px solid #999">
                                                    <font size="15" color="red"><b>已分配金額:NT$<span id="show1"><{$data_income.eLender}></span>元</b></font><br>
                                                    <font size="15" color="red"><b>未分配金額:NT$<span id="show2"></span>元</b></font> 
                                                </div>
                                            </td>
                                        </tr>   
                                        <tr>
                                            <th>簽約款︰</th>
                                            <td>NT$<input type="text" name="SignMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eSignMoney}>" onkeyup="checkTotal('')"></td>
                                            <th>用印款︰</th>
                                            <td>NT$<input type="text" name="AffixMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eAffixMoney}>"  onkeyup="checkTotal('')"></td>
                                            <th>完稅款︰</th>
                                            <td>NT$<input type="text" name="DutyMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eDutyMoney}>"  onkeyup="checkTotal('')"></td>
                                        </tr>
                                        <tr>
                                            <th>尾款︰</th>
                                            <td>NT$<input type="text" name="EstimatedMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eEstimatedMoney}>"  onkeyup="checkTotal('')"></td>
                                            <th>尾款差額︰</th>
                                            <td>NT$<input type="text" name="EstimatedMoney2" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eEstimatedMoney2}>"  onkeyup="checkTotal('')"></td>
                                            <th>代償後餘額︰</th>
                                            <td>NT$<input type="text" name="CompensationMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eCompensationMoney}>"  onkeyup="checkTotal('')"></td>
                                        </tr>
                                        <tr>
                                            <th>買方仲介服務費</th>
                                            <td>NT$<input type="text" name="ServiceFee" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eServiceFee}>" onkeyup="checkTotal('')"></td>
                                            <th>買方溢入款</th>
                                            <td>NT$<input type="text" name="ExtraMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eExtraMoney}>" onkeyup="checkTotal('')"></td>
                                            <th>換約款︰</th>
                                            <td>NT$<input type="text" name="ExchangeMoney" size="12" maxlength="16" class="text-right sum" value="<{$data_income_sms.eExchangeMoney}>" onkeyup="checkTotal('')"></td>
                                        </tr>
                                        <tr class="cp" id="copy">
                                            <th>其他:</th>
                                            <td colspan="5">
                                                項目名稱: 
                                                <{html_options  name='cc' options=$menu_title }> 
                                                <input type="hidden" name="ctext">  
                                                <div style="display:inline;padding-left:50px;">
                                                金額:NT$<input type="text"  size="12" maxlength="16"  class="text-right" name="dd" onkeyup=""></div>
                                                <div style="display:inline;"><input type="button" value="新增" onclick="AddRow()" id="ar"></div>
                                            </td>
                                        </tr>
                                        <{foreach from=$data_income_sms_other key=key item=item}>
                                        <tr class="cp" id="o<{$item.eId}>">
                                            <th>其他:</th>
                                            <td colspan="5">
                                                項目名稱: 
                                                <input type="hidden" name="otherId[]" value="<{$item.eId}>">
                                                <{html_options name='otherT[]' class="ot" options=$item.menu selected=$item.eTitle}> 
                                                <div style="display:inline;padding-left:50px;">
                                                金額:NT$<input type="text" size="12" maxlength="16" class="text-right sum" name="otherM[]"  onkeyup="checkTotal('')" value="<{$item.eMoney}>"></div>
                                                <div style="display:inline;"><input type="button" value="刪除" onclick="del('o<{$item.eId}>')" id="ar"></div>
                                            </td>
                                        </tr>
                                        <{/foreach}>
                                        
                                        <tr class="cp">
                                            <td colspan="6" style="border-bottom:1px solid #CCC"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                備註
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <textarea name="eExplain" class="input-text-per"><{$data_income.eExplain}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="sign-red">*</span>最後修改者︰</th>
                                            <td>
                                                <input type="text" name="" maxlength="10" class="input-text-big" value="<{$data_income.eLastEditer}>" disabled="disabled"/>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <th>最後修改時間︰</th>
                                            <td>
                                                <input type="text" name="" maxlength="10" class="input-text-per" value="<{$data_income.eLastTime|date_format:"%Y-%m-%d %H:%M"}>" disabled="disabled" />
                                            </td>
                                        </tr>
                                    </table>

                                    <center>
                                        <br/>
                                        <div style="background-color: #FFF">
                                            <{if $has_case AND $trade_code ne '1912' AND $trade_code ne '1920' AND $trade_code ne '1560'}>
                                            <button id="toadd">儲存</button>
                                            <{else}>
                                            <button id="save">儲存</button>
                                            <{/if}>
                                        </div>
                                    </center>
                                </div>
           
                                <div id="tabs-uploadList">
                                    <div class="upload_list">
                                        <div class="upload_list_title">
                                            上傳檔案列表
                                            <div style="float:right;padding-right:10px;">
                                                排序
                                                <select id="upload_list_sort" onchange="uploadFileList()">
                                                    <option value="0" selected>檔名</option>
                                                    <option value="1">時間</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="upload_list_content"></div>                 
                                    </div>
                                </div>
                            </div>
   
                            <form name="form_back" id="form_back" method="POST"  action="listinspection.php">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>

            <div id="dialog" title="請確認!!"></div>
        </div>
    </body>
</html>
