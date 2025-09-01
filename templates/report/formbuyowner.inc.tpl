<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <script src="/js/jquery.colorbox.js"></script>
        <script src="/js/IDCheck.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
				getMarguee(<{$smarty.session.member_id}>) ;
				setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
				
                if ("<{$data_case.cSignCategory}>" == 2) {

                    var array = "input,select,textarea";
                   
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                       
                    }); 

                }

                //feedm member_pFeedBackModify
                if ("<{$smarty.session.member_pFeedBackModify}>" != 1) {
                    var array = "input,select,textarea";

                    $(".feedm").find(array).each(function() {
                         $(this).attr('disabled', true);
                    });
                }

                var dep = "<{$smarty.session.member_pDep}>";

                if ("<{$data_case.cInvoiceClose}>" == 'Y' && ( dep != 9 && dep != 10 && dep != 1)) {

                    $(".invoice").each(function() {
                        $(this).attr('disabled', true);
                    });

                }

                //   //回饋金鎖定
                // if ("<{$data_case.cCaseStatus}>" != 2 && ( dep != 9 && dep != 10 && dep != 1)) {

                //     $(".feedbackClose").each(function() {
                //         $(this).attr('disabled', true);
                //     });

                // }
                // $(".feedbackClose1").hide();


                $("#build").hide();


                if ("<{$data_owner.cOtherName}>"=='') {
                    $('#owner_ciden').hide();//統一編號用法定代理人(賣)
                }
               
               if ("<{$data_buyer.cOtherName}>"=='') {
                    $('#buy_ciden').hide();//統一編號用法定代理人(買)
               }

                $(".iframe").colorbox({iframe:true, width:"1200px", height:"90%"}) ;
                
                $('#dialog').dialog({
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        "OK": function() {
                            $('#dialog').dialog('close') ;
                        }
                    }
                }) ;
                
                checkID('o') ;              
                checkID('b') ;
                
                /* 檢核是否輸入賣方身分證字號 */
                $('[name="owner_identifyid"]').keyup(function() {
                    checkID('o') ;

                    getCustomer('owner',$(this).val());
                   
                }) ;
                ////
                
                /* 檢核是否輸入買方身分證字號 */
                $('[name="buy_identifyid"]').keyup(function() {
                    checkID('b') ;
                    getCustomer('buy',$(this).val());
                }) ;
                ////
                
                /*CatchBranchCategory();*/
                CatchBank();
                
                $('[name=owner_bankkey]').live('change', function () {
                    GetBankBranchList($('[name=owner_bankkey]'),
                                        $('[name=owner_bankbranch]'),
                                        null);
                });
                $('[name=buyer_bankkey]').live('change', function () {
                    GetBankBranchList($('[name=buyer_bankkey]'),
                                        $('[name=buyer_bankbranch]'),
                                        null);
                });


            // 設定模糊選擇與下拉選擇並存功能
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
                        .attr("name",'checkScr')
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
                                
                                CatchScrivener() ;
                                
                            },
                            change: function( event, ui ) {
                                if ( !ui.item ) {
                                    var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
                                        valid = false;
                                    select.children( "option" ).each(function() {
                                        if ( $( this ).text().match( matcher ) ) {
                                            this.selected = valid = true;
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
                                
                                CatchScrivener() ;
                                
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
    
            $( "#scrivener_id").combobox() ;
            //$( ".realty_branch").combobox1() ;
            ///////////////////////////////////////////////////////////////////////////////
        
                $('[name=scrivener_id]').live('change', CatchScrivener);

                $('[name=property_measuremain]').live('change', SubArea);
                $('[name=property_measureext]').live('change', SubArea);
                $('[name=property_measurecommon]').live('change', SubArea);

                $('[name=invoice_splitowner]').live('click', SplitInvoice);
                $('[name=invoice_splitbuyer]').live('click', SplitInvoice);
                $('[name=invoice_splitrealestate]').live('click', SplitInvoice);
                $('[name=invoice_splitscrivener]').live('click', SplitInvoice);
                $('[name=invoice_splitother]').live('click',SplitInvoice);
               
                $('#finishform').live('click', function () {
                    var id = $('[name=certifiedid]').val();
                    window.open ('/bank/report/excel_save.php?id='+id, 'newwindow', 'height=200, width=400, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no');
                });
                $('#ctrlform').live('click', function () {
                    var id = $('[name=certifiedid]').val();
                    window.open ('/bank/report/control_report.php?id='+id, 'newwindow', 'height=200, width=400, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no');
                });
                $('#save').live('click', function () {

                    if (!checkInvoiceClose()) {

                       alert('此案件發票已在開立階段，頁面資料已過期請重新整理');
                        return false;
                    }

                    

                    /* 儲存前先檢查買方是否為非本國人並警示 */
                    if (checkSaveF('b')) {

                        alert('買方外國人身分未選擇是否已住滿 "183天" !!') ;
                    }
                    ////
                    
                    /* 儲存前先檢查買方是否為非本國人並警示 */
                    if (checkSaveF('o')) {
                        alert('賣方外國人身分未選擇是否已住滿 "183天" !!') ;
                    }
                    ////
                    // var  inv_owner_total = $("[name='invoice_invoiceowner']").val(); //賣方發票總額
                    // var  inv_buyer_total = $("[name='invoice_invoicebuyer']").val(); //買方發票總額

                        if ($("[name='branch_staus']").val()==2) {
                             alert('第一間店已關店');
                             $("#tabs-realty").click();
                             return false;

                        }else if ($("[name='branch_staus1']").val()==2) {
                            alert('第二間店已關店');
                             $("#tabs-realty").click();
                             return false;
                        }else if ($("[name='branch_staus2']").val()==2) {
                            alert('第三間店已關店');
                             $("#tabs-realty").click();
                             return false;
                        }

                        if ($('[name="check_End"]').val() == 1 && "<{$data_case.cCaseFeedBackModifier}>" == '') {
                            feedback_money();
                        };

                     var st = $('[name="case_status"]').val() ;
                    if (st==3) {

                        // invoice_dealing2();
                        var invoice =  invoice_dealing2();
                        var interest = interest_dealing() ;
                        // console.log(invoice);
                        if (invoice=='0') {
                            alert('請檢查發票是否有分配完全');
                            return false;
                        }
                        // return false;
                        if (interest =='0') {

                            alert('請檢查利息是否有分配正確');
                            return false;
                        }
                        
                        var buy_identifyid = $("[name='buy_identifyid']").val();
                        var owner_identifyid = $("[name='owner_identifyid']").val();

                            // alert(buyer_passport);
                        var pat = /[a-zA-Z]{2}/ ;
                
                        if (pat.test(buy_identifyid)){
                            if ($("[name='buyer_passport']").val() =='') {
                                alert("請填寫買方護照號碼");
                                $("#li-buyer").click();
                                $("[name='buyer_passport']").focus();
                                return false;
                            }
                        }

                        if (pat.test(owner_identifyid)){
                            if ($("[name='owner_passport']").val() =='') {
                                alert("請填寫賣方護照號碼");
                                $("#li-owner").click();
                                $("[name='owner_passport']").focus();
                                return false;
                            }
                        }

                        //其他買賣方防呆
                        $.ajax({
                            url: '/includes/escrow/check_other.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {cid: "<{$data_case.cCertifiedId}>",type:'others'},
                        })
                        .done(function(txt) {
                            
                           var  check = txt;
                            if (check == 1 ) {
                                // var msg = ;
                                alert('其他買方資料不齊全，請檢查');
                                $("#li-buyer").click();
                                return false;

                            }else if(check ==2){

                                alert('其他賣方資料不齊全，請檢查');
                                $("#li-owner").click();
                                return false;
                            }else{
                                 if (CheckField()) {
                                    CatchData('save');
                                }
                            }

   
                        });

                    }else{
                            CatchData('save');
                    }
                   
                   
                    
                });
                
                $('#add').live('click', function () {
                        CatchData('add');
                });

                var ck = $("#ecs").attr('disabled');

                if (ck!='disabled') {
                    $("#ecs").live('click', function() {

                        var cid =$('[name="certifiedid"]').val();

                        // alert(cid);

                        $.ajax({
                            url: '../includes/escrow/contractchange.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'cid': cid},
                        })
                        .done(function(txt) {
                            alert(txt);

                            $('form[name=form_edit] input[name=id]').val(cid);
                            $('form[name=form_edit]').submit();
                        });

                        
                    });
                }else{
                    $("#unecs").live('click', function() {

                        var cid =$('[name="certifiedid"]').val();
                        $.ajax({
                            url: '../includes/escrow/DeleteCategory.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'cid': cid},
                        })
                        .done(function(txt) {
                            alert(txt);

                            $('form[name=form_edit] input[name=id]').val(cid);
                            $('form[name=form_edit]').submit();
                        });
                    }); 
                }


                
                $("#servicefee").live('click', function(event) {


                    var cid =$('[name="certifiedid"]').val();
                    var cat = "<{$scr_sCategory}>";
                    // console.log(cat);
                    var type = "<{$data_bankcode.bApplication}>";
                   <{if $data_realstate.cBrand == 69 || $data_realstate.cBrand1 == 69 || $data_realstate.cBrand2 == 69}>
                   $('form[name=form_fee] input[name=brand]').val(69);
                   <{/if}>

                    $('form[name=form_fee] input[name=cid]').val(cid);
                    $('form[name=form_fee] input[name=cat]').val(cat);
                    $('form[name=form_fee] input[name=type]').val(type);

                    $('form[name=form_fee]').attr('action', 'formservicefee.php');

                    $('form[name=form_fee]').submit();

                });
                
                // jQuery UI Tabs 初始化
                try {
                    var selectedTab = parseInt(<{$_tabs}>) || 0;
                    if ($("#tabs").length > 0) {
                        $( "#tabs" ).tabs({
                            selected: selectedTab
                        });
                    }
                } catch(e) {
                    console.log('Tabs initialization error:', e);
                    // 嘗試使用默認設置
                    if ($("#tabs").length > 0) {
                        $( "#tabs" ).tabs();
                    }
                }

                 $('#sync_owneraddr').live('change', function () {
                     if ($('#sync_owneraddr').attr('checked') == 'checked') {
                         $('[name=owner_basecountry]').val($('[name=owner_registcountry]').val());
                         $('[name=owner_basearea]').html($('[name=owner_registarea]').html());
                         $('[name=owner_basearea]').val($('[name=owner_registarea]').val());
                         $('[name=owner_baseaddr]').val($('[name=owner_registaddr]').val());
                         $('[name=owner_basezip]').val($('[name=owner_registzip]').val());
                         $('[name=owner_basezipF]').val($('[name=owner_registzipF]').val());
                     }
                });
                $('#sync_buyeraddr').live('change', function () {
                     if ($('#sync_buyeraddr').attr('checked') == 'checked') {
                         $('[name=buyer_basecountry]').val($('[name=buyer_registcountry]').val());
                         $('[name=buyer_basearea]').html($('[name=buyer_registarea]').html());
                         $('[name=buyer_basearea]').val($('[name=buyer_registarea]').val());
                         $('[name=buyer_baseaddr]').val($('[name=buyer_registaddr]').val());
                         $('[name=buyer_basezip]').val($('[name=buyer_registzip]').val());
                         $('[name=buyer_basezipF]').val($('[name=buyer_registzipF]').val());
                     }
                });
                
                 $('[name=income_signmoney]').live('blur', function () {
                     var tmp = $('[name=income_signmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_signmoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });
                $('[name=income_affixmoney]').live('blur', function () {
                     var tmp = $('[name=income_affixmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_affixmoney]').val('0');
                     }
                     CatchIncome();
                      SplitInvoice();
                });
                $('[name=income_dutymoney]').live('blur', function () {
                     var tmp = $('[name=income_dutymoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_dutymoney]').val('0');
                     }
                     CatchIncome();
                      SplitInvoice();
                });
                $('[name=income_estimatedmoney]').live('blur', function () {
                     var tmp = $('[name=income_estimatedmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_estimatedmoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });
                
                $('[name=realestate_branch]').live('change', function () {
                   ChangeBranch();
                   CatchBranch();
                   $("[name='checkCase3']").val("1");//有更改仲介店需確認該仲介案件是否低於三件
                });
                
                $('[name=realestate_branch1]').live('change', function () {
                   ChangeBranch_A();
                   CatchBranch_A();
                   $("[name='checkCase3']").val("1");//有更改仲介店需確認該仲介案件是否低於三件

                });
                      
                $('[name=realestate_branch2]').live('change', function () {
                   ChangeBranch_A(2);
                   CatchBranch_A(2);
                   $("[name='checkCase3']").val("1");//有更改仲介店需確認該仲介案件是否低於三件
                });
                
                /*第一組仲介*/
                $('[name=realestate_brand]').live('change', function () {
                    var aaa = $('[name=realestate_brand]').val()
                    if (aaa=='2') {     // 選擇無仲介時的處理
                        $('[name=realestate_branchcategory]').each(function() {
                            $(this).val(3);
                            if ($(this).val() == '3') {

                                $(this).attr("selected",true) ;
                            }
                        }) ;
                        
                        $('#add').css({'display':''}) ;
                    }else{
                        $('[name=realestate_branchcategory]').val(0);
                    }
                    CatchBrand();

                    //品牌回饋地政士
                    // var ttt = $("[name='scr_brand']").val();
                    $.ajax({
                        url: '../includes/escrow/getBrandForScr.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"brand": aaa,"scrivner":$("[name='scrivener_id']").val(),"cat":'recall'},
                    })
                    .done(function(txt) {
                        //JSON.stringify
                        if (txt != false) {
                            // console.log(txt);
                            var obj = JSON.parse(txt);
                            
                            $("[name='scrivener_BrandScrRecall']").val(obj.recall);
                            $("[name='scrivener_BrandRecall']").val(obj.reacllBrand);
                        }else{
                            $("[name='scrivener_BrandScrRecall']").val(0);
                             $("[name='scrivener_BrandRecall']").val(0);
                        }
                        
                       //

                    });
                    
                   
                    

                    setTimeout("ChangeBranch()",500) ;
                    setTimeout("CatchBranch()",500) ;
                });
                
                /*第二組仲介*/
                $('[name=realestate_brand1]').live('change', function () {
                    var aaa = $('[name=realestate_brand1]').val()
                    if (aaa=='2') {     // 選擇無仲介時的處理
                        $('[name=realestate_branchcategory1]').each(function() {
                             $(this).val(3);
                            if ($(this).val() == '3') {
                                $(this).attr("selected",true) ;
                            }
                        }) ;
                        
                        $('#add').css({'display':''}) ;
                    }else{
                        $('[name=realestate_branchcategory1]').val(0);
                    }
                    CatchBrand_A();
                     //品牌回饋地政士
                    $.ajax({
                        url: '../includes/escrow/getBrandForScr.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"brand": aaa,"scrivner":$("[name='scrivener_id']").val()},
                    })
                    .done(function(txt) {
                        //JSON.stringify
                        if (txt != false) {
                            // console.log(txt);
                            var obj = JSON.parse(txt);
                            // console.log(obj.brand);
                            $("[name='scrivener_BrandScrRecall1']").val(obj.recall);
                            $("[name='scrivener_BrandRecall1']").val(obj.reacllBrand);
                        }else{
                            $("[name='scrivener_BrandScrRecall1']").val(0);
                            $("[name='scrivener_BrandRecall1']").val(0);
                        }
                        
                       //

                    });

                    

                    setTimeout("ChangeBranch_A()",500) ;
                    setTimeout("CatchBranch_A()",500) ;
                });
                
                /*第三組仲介*/
                $('[name=realestate_brand2]').live('change', function () {
                    var aaa = $('[name=realestate_brand2]').val()
                    if (aaa=='2') {     // 選擇無仲介時的處理
                        $('[name=realestate_branchcategory2]').each(function() {
                            $(this).val(3);
                            if ($(this).val() == '3') {
                                $(this).attr("selected",true) ;
                            }
                        }) ;
                        
                        $('#add').css({'display':''}) ;
                    }else{
                        $('[name=realestate_branchcategory2]').val(0);
                    }
                    CatchBrand_A(2);
                    //品牌回饋地政士
                    $.ajax({
                        url: '../includes/escrow/getBrandForScr.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"brand": aaa,"scrivner":$("[name='scrivener_id']").val()},
                    })
                    .done(function(txt) {
                        //JSON.stringify
                        if (txt != false) {
                            // console.log(txt);
                            var obj = JSON.parse(txt);
                            // console.log(obj.brand);
                            $("[name='scrivener_BrandScrRecall2']").val(obj.recall);
                        }else{
                            $("[name='scrivener_BrandScrRecall2']").val(0);
                        }
                        
                       //

                    });
                    

                    setTimeout("ChangeBranch_A(2)",500) ;
                    setTimeout("CatchBranch_A(2)",500) ;
                });
                
                $('[name=realestate_branchcategory]').live('change', function () {
                    CatchBrand();
                    setTimeout("ChangeBranch()",500) ;
                    setTimeout("CatchBranch()",500) ;
                    $('#add').css({'display':''}) ;
                });
                $('[name=realestate_branchcategory1]').live('change', function () {
                    CatchBrand_A();
                    setTimeout("ChangeBranch_A()",500) ;
                    setTimeout("CatchBranch_A()",500) ;
                    $('#add').css({'display':''}) ;
                });
                $('[name=realestate_branchcategory2]').live('change', function () {
                    CatchBrand_A(2);
                    setTimeout("ChangeBranch_A(2)",500) ;
                    setTimeout("CatchBranch_A(2)",500) ;
                    $('#add').css({'display':''}) ;
                });

                $("[name='expenditure_scrivenermoney']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });

                 $("[name='expenditure_scrivenermoney_buyer']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });

                $("[name='owner_money1']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });

                $("[name='owner_money2']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });

                $("[name='owner_money3']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });

                $("[name='owner_money4']").live('blur', function(event) {
                   $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                });
                
                $('[name=expenditure_realestatemoney]').live('blur', function () {
                    CountDelMoney();
                });
                $('[name=expenditure_advancemoney]').live('blur', function () {
                    CountDelMoney();
                });
                
                $('[name=expenditure_realestatemoney_buyer]').live('blur', function () {
                    CountDelMoney();
                });
                $('[name=expenditure_advancemoney_buyer]').live('blur', function () {
                    CountDelMoney();
                });
                
                $('#upload').live('click', function () {
                    $('#form_upload').submit();
                });
                
                $('#checklist').live('click', function () {
                    var url = '/checklist/form_list_db.php?cCertifiedId=<{$data_case.cCertifiedId}>' ;
                    $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
                }) ;
                
                $('[name="income_certifiedmoney"]').change(function() {
                    //feedback_money(1,$('[name=realestate_bRecall]').val()) ;
                    feedback_money() ;
                }) ;

                //如果出租情形選擇無會把欄位清掉
                $('[name=property_rentstatus]').live('click', function () {
                    var val = $('[name=property_rentstatus]:checked').val();
                    if (val==2) 
                    {
                        $("[name=property_rentdate]").val('');
                        $("[name=property_rent]").val('');
                        $("[name=property_finish]").removeAttr('checked');
                    }
                    
                });
                //建物編輯
                $('#new_build').live('click', function () {
                    var v = parseInt($("[name='buildcount']").val());


                    $.ajax({
                        url: '../includes/escrow/getBuildTpl.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {item: v,limit:<{$limit_show}>},
                    })
                    .done(function(html) {
                        $(html).insertAfter('.newP:last');
                    });
                    
                    $("[name='buildcount']").val((v+1));
                    // 
                    // $("#build").clone().insertAfter('.newP:last').attr("id","build"+v) ;
                    // $("#build"+v).show();


                    // $("[name='buildcount']").val(v);//buildcount

                    // $("#new_build").hide();
                    

                    // $("[name='new_property_Item']").attr('value', v);
                    // $("[name='new_build_edit']").attr('onClick','build_edit('+v+')' );

                 });
                //
                    //賣方國際代碼
                $('[name=ocountry]').live('change', function () {
                    var v = $('[name=ocountry]').val();

                    $("[name='owner_country']").val(v);
                });
                //買方國際代碼
                $('[name=bcountry]').live('change', function () {
                    var v = $('[name=bcountry]').val();

                    $("[name='buyer_country']").val(v);
                });

                 $('#trans_build').live('click', function () {
                   
                   var url= "/bank/new/out1.php?vr=<{$data_case.cEscrowBankAccount}>";

                   window.open(url,'export');
                        // $('form[name="myform"]').submit() ;
                    }) ;

                  $('[name="buy_categoryidentify"]').live('click', function () {
                        var v = $('[name="buy_categoryidentify"]:checked').val();
                        if (v==2) {
                            $('#buy_ciden').show();
                        }
                    
                    }) ;
                  $('[name="owner_categoryidentify"]').live('click', function () {
                        var v = $('[name="owner_categoryidentify"]:checked').val();
                        if (v==2) {
                            $('#owner_ciden').show();
                        }
                    
                    }) ;
                  $("[name='income_firstmoney']").live('focus', function() {
                      
                      if (confirm("填入金額後會影響保證費及回饋金，確定要輸入?")) {
                            $("[name='income_firstmoney']").live('blur', function() {

                                certifiedmoneyCount();
                            });
                      }else{
                        $("#unfo").focus();
                      }
                  });


                //設定按鍵icon
                
                $('#finishform').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#ctrlform').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#servicefee').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#save').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#add').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#copy').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#ecs').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#unecs').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#land_edit').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#sms').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#build_edit').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#upload').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $('#checklist').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                 $('#trans_build').button( {
                    icons:{
                        primary: "ui-icon-transfer-e-w"
                    }
                } );
                
            });
            
            function checkAddr(name){
                var zip = '';
                var addr = '';
                var count = 0;
               
               
                // $(".pAddr").each(function() {
                //     addr[count] = $(this).val();
                //     count++;
                // });
                // count = 0;

                // $(".pZip").each(function(txt) {
                //      zip[count] = $(this).val();
                //     count++;
                // });

                if (name == 'new') {
                    
                    // addr = $("[name='new_property_addr']").val();
                    // zip = $("[name='new_property_zip']").val();
                    addr = $("#property_addr"+name).val();
                    zip = $("#property_zip"+name).val();

                
                }else{
                    addr = $("[name='property_addr"+name+"']").val();
                    zip = $("[name='property_zip"+name+"']").val();
                }

               
               
               
               $.ajax({
                   url: '../includes/escrow/check_other.php',
                   type: 'POST',
                   dataType: 'html',
                   data: {"zip":zip,"addr":addr,"type":'checkaddr',"cid":"<{$data_case.cCertifiedId}>","num":name},
               })
               .done(function(msg) {
                msg = msg.trim();
                

                    if (msg > 9) {
                        alert("已有重複地址");
                        
                        if ($("[name='case_status']").val() == 2) {
                            if (name == 0) {
                                //[name='new_import']
                               $("#showSameAddr"+name).html('重複號碼:'+msg); //showSameAddr0
                            }

                        //     if (name == 0) {
                        //         //[name='new_import']
                        //         $("[name='import"+name+"']").attr('style','');
                        //         $("[name='import"+name+"']").attr({
                        //             style: '',
                        //             "onClick": 'importData("'+msg+'")'
                        //         });
                        //     }else if(name == 'new'){
                        //         $("[name='new_import']").attr({
                        //             style: '',
                        //             "onClick": 'importData("'+msg+'")'
                        //         });
                        //     }else{
                        //         $("[name='import"+name+"']").attr('style','display:none');
                        //     }
                        }
                            
                            
                            // property_addr0
                        
                    }
                   
               });
               

            }
            
            function getCountryCode(cat){
                var val ='';

                if (cat == 'b') {
                    val = $("[name='buyer_country']").val();
                }else if(cat == 'o'){
                     val = $("[name='owner_country']").val();
                }

                $("[name='"+cat+"country']").val(val);
            }

            function closingday(name){
               $("[name='"+name+"']").val('');
            }

            function checkInvoiceClose(){
                var close = "<{$data_case.cInvoiceClose}>";
                var check = 1;

                $.ajax({
                    async: false, //同步處理
                    url: '/includes/escrow/check_other.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cid': "<{$data_case.cCertifiedId}>",'type':'invoiceclose','close':close},
                })
                .done(function(txt) {
                   check = txt ;
                   // 
                });
                    // alert(check);
                if (check == 'error') {
                    return false;
                }else{
                    return true;
                }
               

            }


            
            function sms_edit() {
                var scid = $('[name=scrivener_id]').val() ;
                var certified_id = $('[name=certifiedid]').val() ;
                var cSignCategory = "<{$data_case.cSignCategory}>";

                var url = '/escrow/formcasesms.php?scid=' + scid + '&certified_id=' + certified_id + '&cSignCategory='+ cSignCategory+'';

                if (scid == 0 || certified_id == '') {
                    $('#dialog').html('請先儲存案件，再編輯簡訊對象!!') ;
                    $('#dialog').dialog('open') ;
                    return ;
                } 
                
                $.colorbox({iframe:true, width:"750px", height:"700px", href:url}) ;
                //window.open("/escrow/formcasesms.php?scid="+scid+"&certified_id="+certified_id,"Updating","height=500,width=450;",false);
                //win.location.href = "/escrow/formcasesms.php?scid="+scid+"&certified_id="+certified_id;
            }
            
            /* 仲介簡訊編輯 */
            function sms_realty_edit(b,i) {
                var c = $('[name=certifiedid]').val() ;
                var url = "formcasesmsrealty.php?bid=" + b + "&cid=" + c + "&in=" + i ;
                
                if (b == 0 || b == '' || c == '') {
                    $('#dialog').html('請先儲存案件，再編輯簡訊對象!!') ;
                    $('#dialog').dialog('open') ;
                    return ;
                }
                
                $.colorbox({iframe:true, width:"550px", height:"600px", href:url}) ;
                //alert('b='+b+',c='+c+',i='+i) ;
            }
            ////
            function phone_edit(v) {
                var c = $('[name=certifiedid]').val() ;
                var url = 'formphonedit.php?t='+ v +'&cid=' + c+"&cSignCategory=<{$data_case.cSignCategory}>";

                
                $.colorbox({iframe:true, width:"750px", height:"700px", href:url}) ;

            }
            
            function land_edit() {
                var sign = "<{$data_case.cSignCategory}>";
                     if ($('[name=is_edit]').val() == '1') {
                        if (sign==1) {
                            CatchData2('edit');
                        }

                         
                         setTimeout(function() {
                            $('#form_land [name=id]').val($('[name=certifiedid]').val());
                            $('#form_land').submit();
                         }, 1000);
                     } else {
                         $( "#dialog-confirm11" ).dialog({
                             resizable: false,
                             height:200,
                             modal: true,
                             buttons: {
                                "編輯土地資料": function() {
                                        if ($('[name=scrivener_bankaccount]').val() == null) {
                                            alert('請選擇保證證號!!');
                                            return;
                                        } else {
                                            CatchData2('add');
                                            var bkacc = $('[name=scrivener_bankaccount]').val();
                                            $('#form_land [name=id]').val(bkacc.substring(14, 5));
                                            $('#form_land').submit();
                                        }
                                },
                                "取消": function() {
                                        $( this ).dialog( "close" );
                                }
                            }
                        });
                     }
                 }

            function build_edit(item) {
                      var sign = "<{$data_case.cSignCategory}>";

                      if ($('[name=is_edit]').val() == '1') {
                        if (sign==1) {CatchData2('edit');}
                         $('#form_build  [name=bitem]').val(item);
                         // alert($('#form_build  [name=bitem]').val());
                         setTimeout(function() {
                            $('#form_build [name=id]').val($('[name=certifiedid]').val());
                            
                            $('#form_build').submit();
                         }, 1000);
                     } else {
                         $( "#dialog-confirm11" ).dialog({
                             resizable: false,
                             height:200,
                             modal: true,
                             buttons: {
                                "編輯建物資料": function() {
                                        if ($('[name=scrivener_bankaccount]').val() == null) {
                                            alert('請選擇保證證號!!');
                                            return;
                                        } else {
                                            CatchData2('add');
                                            var bkacc = $('[name=scrivener_bankaccount]').val();
                                            $('#form_build  [name=bitem]').val(item);
                                            setTimeout(function(){
                                                $('#form_build [name=id]').val(bkacc.substring(14, 5));

                                                $('#form_build').submit();
                                            }, 300);
                                        }
                                },
                                "取消": function() {
                                        $( this ).dialog( "close" );
                                }
                            }
                        });
                     }
                 }

              function car_edit() {
                     var sign = "<{$data_case.cSignCategory}>";
                      
                      if ($('[name=is_edit]').val() == '1') {

                         if (sign==1) {CatchData2('edit');}
                         setTimeout(function() {
                            $('#form_car [name=id]').val($('[name=certifiedid]').val());
                            $('#form_car').submit();
                         }, 1000);
                     } else {
                         $( "#dialog-confirm11" ).dialog({
                             resizable: false,
                             height:200,
                             modal: true,
                             buttons: {
                                "停車位標示": function() {
                                        if ($('[name=scrivener_bankaccount]').val() == null) {
                                            alert('請選擇保證證號!!');
                                            return;
                                        } else {
                                            CatchData2('add');
                                            var bkacc = $('[name=scrivener_bankaccount]').val();
                                            setTimeout(function(){
                                                $('#form_car [name=id]').val(bkacc.substring(14, 5));
                                                $('#form_car').submit();
                                            }, 300);
                                        }
                                },
                                "取消": function() {
                                        $( this ).dialog( "close" );
                                }
                            }
                        });
                     }
                 }

            function CatchBank() {
                var request = $.ajax({  
                            url: "/includes/scrivener/bankcodesearch.php",
                            type: "POST",
                            data: {
                                id:$('[name=scrivener_id]').val()
                            },
                            dataType: "json"
                        });
                        request.done( function( data ) {
                            $.each(data, function(key,item) {
                                if (key == 0) {
                                    $.each(item, function(key2,item2) {
                                        if (key2 == 'sOffice') {
                                            $("[name=scrivener_office]").attr("disabled", false);
                                            $('[name=scrivener_office]').val(item2);
                                            $("[name=scrivener_office]").attr("disabled", true);
                                        }
                                        if (key2 == 'sMobileNum') {
                                            $("[name=scrivener_mobilenum]").attr("disabled", false);
                                            $('[name=scrivener_mobilenum]').val(item2);
                                            $("[name=scrivener_mobilenum]").attr("disabled", true);
                                        }
                                        if (key2 == 'sTelArea') {
                                            $("[name=scrivener_telarea]").attr("disabled", false);
                                            $('[name=scrivener_telarea]').val(item2);
                                            $("[name=scrivener_telarea]").attr("disabled", true);
                                        }    
                                        if (key2 == 'sTelMain') {
                                            $("[name=scrivener_telmain]").attr("disabled", false);
                                            $('[name=scrivener_telmain]').val(item2);
                                            $("[name=scrivener_telmain]").attr("disabled", true);
                                        }    
                                        if (key2 == 'sFaxArea') {
                                            $("[name=scrivener_faxarea]").attr("disabled", false);
                                            $('[name=scrivener_faxarea]').val(item2);
                                            $("[name=scrivener_faxarea]").attr("disabled", true);
                                        }    
                                        if (key2 == 'sFaxMain') {
                                            $("[name=scrivener_faxmain]").attr("disabled", false);
                                            $('[name=scrivener_faxmain]').val(item2);
                                            $("[name=scrivener_faxmain]").attr("disabled", true);
                                        } 
                                        if (key2 == 'sZip1') {
                                            $("[name=scrivener_zip]").attr("disabled", false);
                                            $('[name=scrivener_zip]').val(item2);
                                            $("[name=scrivener_area]").attr("disabled", true);
                                            $("[name=scrivener_country]").attr("disabled", true);
                                            $("[name=scrivener_zip]").attr("disabled", true);
                                        }  
                                        if (key2 == 'sAddress') {
                                            $('[name=scrivener_addr]').val(item2);
                                        } 
                                        if (key2 == 'sRecall') {
                                            $('[name=sRecall]').val(item2);
                                        }
                                     });
                                }
                                if (key == 1) {
                                    $('[name=scrivener_bankaccount]').children().remove().end();
                                     $.each(item, function(key2,item2) {
                                         $('[name=scrivener_bankaccount]').append('<option value="'+key2+'">'+item2+'</option>');
                                     });
                                }
                            });
                        });
            }
            
            function CatchScrivener() {
                
                var scr = $('[name="scrivener_bankaccount"] option:selected').val() ;
                  
                var request = $.ajax({  
                            url: "/includes/scrivener/bankcodesearch.php",
                            type: "POST",
                            data: {
                                id:$('[name=scrivener_id]').val(),
                                bc:$('[name=case_bank]').val()
                            },
                            dataType: "json"
                        });
                        request.done( function( data ) {
                            var zipNo = '' ;
                            $.each(data, function(key,item) {
                                if (key == 0) {
                                    $.each(item, function(key2,item2) {
                                        if (key2 == 'sOffice') {
                                            $('[name=scrivener_office]').val(item2);
                                        }
                                        if (key2 == 'sMobileNum') {
                                            $('[name=scrivener_mobilenum]').val(item2);
                                        }
                                        if (key2 == 'sTelArea') {
                                            $('[name=scrivener_telarea]').val(item2);
                                        }    
                                        if (key2 == 'sTelMain') {
                                            $('[name=scrivener_telmain]').val(item2);
                                        }    
                                        if (key2 == 'sFaxArea') {
                                            $('[name=scrivener_faxarea]').val(item2);
                                        }    
                                        if (key2 == 'sFaxMain') {
                                            $('[name=scrivener_faxmain]').val(item2);
                                        } 
                                        if (key2 == 'sZip1') {
                                            $('[name=scrivener_zip]').val(item2);
                                            zipNo = item2 ;
                                            $('[name=scrivener_zipF]').val(item2.substr(0,3));
                                        }
                                        if (key2 == 'sCity') {
                                            $('#scrivener_countryR').empty() ;
                                            $('#scrivener_countryR').html('<select class="input-text-big" name="scrivener_country" id="scrivener_country" disabled="disabled"><option value="' + item2 + '" selected="selected">' + item2 + '</option></select>') ;
                                        }
                                        if (key2 == 'sArea') {
                                            $('#scrivener_areaR').empty() ;
                                            $('#scrivener_areaR').html('<select class="input-text-big" name="scrivener_area" id="scrivener_area" disabled="disabled"><option value="' + zipNo + '" selected="selected">' + item2 + '</option></select>') ;
                                        }                                       
                                        if (key2 == 'sAddress') {
                                            $('[name=scrivener_addr]').val(item2);
                                        } 
                                        if (key2 == 'sRecall') {
                                            $('[name=sRecall]').val(item2);
                                        }
                                        if (key2 == 'sSpRecall') {
                                            $('[name=sSpRecall]').val(item2);
                                            $('[name=scrivener_sSpRecall]').val(item2);
                                            
                                        }
                                        if (key2 == 'sSpRecall2') {
                                            // $('[name=sSpRecall]').val(item2);
                                            $('[name=cScrivenerSpRecall2]').val(item2);
                                            
                                        }
                                        if (key2 == 'sales') {
                                            $("#showSalseS").text(item2);
                                        }
                                     });
                                }
                                if (key == 1) {
                                    $('[name=scrivener_bankaccount]').children().remove().end();
                                     $.each(item, function(key2,item2) {
                                        var sel = '' ;
                                        if (key2 == scr) { var sel = ' selected="selected"' ; }
                                        if (item2.bVersion == 'A') {
                                            if (item2.branch == 6) { //城中
                                                $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'" class="ver2">'+key2+'</option>');
                                            }else{
                                                $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'" class="ver">'+key2+'</option>');
                                            }
                                            
                                        }else{
                                            $('[name=scrivener_bankaccount]').append('<option' + sel + ' value="'+key2+'">'+key2+'</option>');
                                        }

                                         
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
                                                            .attr("name",'checkAcc')
                                                            .addClass( "ui-state-default ui-combobox-input" )
                                                            .autocomplete({
                                                                delay: 0,
                                                                minLength: 0,
                                                                source: function( request, response ) {
                                                                    // alert($.ui.autocomplete.escapeRegex(request.term));
                                                                    var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                                                                    response( select.children( "option" ).map(function() {
                                                                        var text = $( this ).text();
                                                                        
                                                                       
                                                                        if ( this.value && ( !request.term || matcher.test(text) ) )
                                                                        {
                                                                            if ($(this).attr('class') == 'ver' || $(this).attr('class') == 'ver2') {

                                                                               return {
                                                                                    label: text.replace(
                                                                                        new RegExp(
                                                                                            "(?![^&;]+;)(?!<[^<>]*)(" +
                                                                                            $.ui.autocomplete.escapeRegex(request.term) +
                                                                                            ")(?![^<>]*>)(?![^&;]+;)", "gi"
                                                                                        ), "<strong>$1</strong>" ),
                                                                                    value: text,
                                                                                    option: this,
                                                                                    ck:$(this).attr('class')
                                                                                };
                                                                                
                                                                            }else{
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
                                                                            }
                                                                           
                                                                             

                                                                        }

                                                                           

                                                                            // return 1;

                                                                    }) );
                                                                },
                                                               select: function( event, ui ) {
                                                               
                                                                    ui.item.option.selected = true;
                                                                    self._trigger( "selected", event, {
                                                                        item: ui.item.option
                                                                    });
                                                                    select.trigger("change");
                                                                   
                                                                     var tmp = ui.item.value;  //下拉的值
                                                                      $('[name=case_bankaccount]').val(tmp);
                                                                       $('[name=scrivener_bankaccount]').val(tmp);

                                                                         tmp = tmp.substring(5, 14);
                                                                         $('[name=certifiedid_view]').val(tmp);

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

                                                           

                                                            if (item.ck == 'ver' || item.ck == 'ver2') {
                                                                 return $( "<li></li>" )
                                                                .data( "item.autocomplete", item )
                                                                .append( "<a>" + item.label + "</a>" )
                                                                .appendTo( ul )
                                                                .addClass(item.ck);
                                                                
                                                            }else{
                                                                 return $( "<li></li>" )
                                                                .data( "item.autocomplete", item )
                                                                .append( "<a>" + item.label + "</a>" )
                                                                .appendTo( ul );
                                                            }
                                                           
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
                                        
                                     $('[name=scrivener_bankaccount]').combobox();
                                     var id = $('[name="checkcId"]').val() ;

                                     if (id != '') {
                                        $("[name='checkAcc']").val($('[name="checkcId2"]').val());
                                        $("[name='scrivener_bankaccount']").val($('[name="checkcId2"]').val());
                                     }
                                }
                            });
                        });

                    
                    // console.log($("[name='scrivener_id']").val());
                    $.ajax({
                        url: '../includes/escrow/getBrandForScr.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"scrivner":$("[name='scrivener_id']").val(),"cat":'recall2'},
                    })
                    .done(function(txt) {
                        //JSON.stringify
                        if (txt != false) {
                            // console.log(txt);
                            var obj = JSON.parse(txt);
                            var html = '';
                            for (var i = 0; i < obj.length; i++) {
                                // var msg = JSON.parse(obj[i]);
                                html += obj[i].BrandName+":"+obj[i].sReacllBrand+"%(仲介)、"+obj[i].sRecall+"%(地政士)；";
                                 // console.log(obj[i].sRecall);
                                 //$rs->fields['BrandName'].":".$rs->fields['sReacllBrand']."%(仲介)、".$rs->fields['sRecall']."%(地政士)";
                            }

                            $("#ScrivenerFeedSpTxt").html(html);
                            // $("[name='scrivener_BrandScrRecall']").val(obj.recall);
                            // $("[name='scrivener_BrandRecall']").val(obj.reacllBrand);
                        }else{
                            $("[name='scrivener_BrandScrRecall']").val(0);
                             $("[name='scrivener_BrandRecall']").val(0);
                        }
                        
                       //

                    });
                
            }
            
            function ChangeBranch() {
                var value = $('[name=realestate_branch] option:selected').val();
                $('[name=realestate_branchnum]').val(value);    
                
            }
            function ChangeBranch_A(no) {
                no = no || 1 ;
                var value = $('[name=realestate_branch'+no+'] option:selected').val();
                $('[name=realestate_branchnum'+no+']').val(value);
            }
            function CatchBrand() {
                var request = $.ajax({  
                    url: "/includes/maintain/brandsearch.php",
                    type: "POST",
                    data: {
                        id:$('[name=realestate_brand]').val(), 
                        category:$('[name=realestate_branchcategory]').val()
                    },
                    dataType: "json"
                });
                request.done( function( data ) {
                    $.each(data, function(key,item) {
                        if (key == 0) {
                            $('#realestate_branchR').empty() ;
                            var selTxt = '<select class="realty_branch" name="realestate_branch">' ;
                            if ($('[name=realestate_branchcategory]').val() != 3) {
                                selTxt = selTxt +'<option value="0"></option>';
                            }
                           
                            $.each(item, function(key2,item2) {
                                var bId =  '';
                                var bName = '';
                                $.each(item2, function(key3,item3) {
                                   if (key3 == 'bId') {
                                       bId = item3;
                                   }
                                   if (key3 == 'bStore') {
                                       bName = item3;
                                   }
                                });
                                selTxt = selTxt + '<option value="' + bId + '">' + bName + '</option>' ;
                             });
                             selTxt = selTxt + '</select>' ;
                             $('#realestate_branchR').html(selTxt) ;
                        }
                        if (key == 1) {
                            var bWholeName;
                            var bSerialnum;
                            $.each(item, function(key2,item2) {
                              

                               if (key2 == 'bName') {
                                    $('[name=realestate_name]').val(item2)
                               }
                             });
                        }
                    });
                });
            }
            function CatchBrand_A(no) {
                no = no || 1 ;
                var request = $.ajax({  
                    url: "/includes/maintain/brandsearch.php",
                    type: "POST",
                    data: {
                        id:$('[name=realestate_brand'+no+']').val(), 
                        category:$('[name=realestate_branchcategory'+no+']').val()
                    },
                    dataType: "json"
                });
                request.done( function( data ) {
                    $.each(data, function(key,item) {
                        if (key == 0) {
                            $('#realestate_branch'+no+'R').empty() ;
                            var selTxt = '<select class="realty_branch'+no+'" name="realestate_branch'+no+'">' ;
                            if ($('[name=realestate_branchcategory'+no+']').val()) {
                                 selTxt = selTxt +'<option value="0"></option>';
                            }
                           
                            $.each(item, function(key2,item2) {
                                var bId =  '';
                                var bName = '';
                                $.each(item2, function(key3,item3) {
                                   if (key3 == 'bId') {
                                       bId = item3;
                                   }
                                   if (key3 == 'bStore') {
                                       bName = item3;
                                   }
                                });
                                selTxt = selTxt + '<option value="'+bId+'">'+bName+'</option>' ;
                             });
                             selTxt = selTxt + '</select>' ;
                             $('#realestate_branch'+no+'R').html(selTxt) ;
                        }
                        if (key == 1) {
                            var bWholeName;
                            var bSerialnum;
                            $.each(item, function(key2,item2) {
                               

                               if (key2 == 'bName') {
                                    $('[name=realestate_name'+no+']').val(item2)
                               }
                            });
                        }
                    });
                });
            }
            function ClearBranch(num){
                if (num == 0) {
                    $("[name='realestate_name']").val('');
                    $("[name='realestate_serialnumber']").val('');
                    $("[name='realestate_telarea']").val('');
                    $("[name='realestate_telmain']").val('');
                    $('[name=realestate_faxarea]').val('');
                    $('[name=realestate_faxmain]').val('');
                    $('[name=realestate_zip]').val('');
                    $('[name=realestate_zipF]').val('');
                    $('#realestate_countryR').html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
                    $('#realestate_areaR').html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
                    $('[name=realestate_addr]').val('');
                    $('#bt').text('');
                    $('[name=realestate_name]').val('');
                    $('#promissory1').html('');
                    $('[name=realestate_bRecall]').val('');
                    $("#rea_bRecall").text('');
                    $('[name=realestate_bScrRecall]').val('');
                    $("#rea_bScrRecall").text('');
                    $('[name=branch_staus]').val('');
                    $("#branchFeedData").html('');
                    $('[name=realestate_serialnumber]').val('');
                    $('[name=Feedback_CashierOrderMemo]').val('');
                    $('[name="data_feedData"]').val('');
                    $('#showSalseB').text('');
                }else{
                   
                     $("[name='realestate_name"+num+"']").val('');
                    $("[name='realestate_serialnumber"+num+"']").val('');
                    $("[name='realestate_telarea"+num+"']").val('');
                    $("[name='realestate_telmain"+num+"']").val('');
                    $('[name=realestate_faxarea'+num+']').val('');
                    $('[name=realestate_faxmain'+num+']').val('');
                    $('[name=realestate_zip'+num+']').val('');
                    $('[name=realestate_zipF'+num+']').val('');
                    $('#realestate_countryR'+num).html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
                    $('#realestate_areaR'+num).html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
                    $('[name=realestate_addr'+num+']').val('');
                    $('#bt'+num).text('');
                    $('[name=realestate_name'+num+']').val('');
                    $('#promissory'+(num+1)).html('');
                    $('[name=realestate_bRecall'+num+']').val('');
                    $("#rea_bRecall"+num).text('');
                    $('[name=realestate_bScrRecall'+num+']').val('');
                    $("#rea_bScrRecall"+num+"").text('');
                    $('[name=branch_staus'+num+']').val('');
                    $("#branchFeedData"+num).html('');
                    $('[name=realestate_serialnumber'+num+']').val('');
                    $('[name=Feedback_CashierOrderMemo'+num+']').val('');
                    $('[name="data_feedData'+num+'"]').val('');
                    $('#showSalseB'+num).text('');
                }
                
            }
            
            function CatchBranch() {
                ClearBranch(0)

                $.ajax({  
                    url: "/includes/maintain/branchsearch.php",
                    type: "POST",
                    cache: false,
                    data: {
                        id:$('[name=realestate_branchnum]').val()
                    },
                    dataType: "json"
                }).done( function( data ) {
                    // console.log(data);
                    var zipNo = '' ;
                    $.each(data, function(key,item) {
                        if (key == 0) {
                            $.each(item, function(key2,item2) {
                                if (key2 == 0) {
                                    $.each(item2, function(key3,item3) {
                                        if (key3 == 'bTelArea') {
                                            $('[name=realestate_telarea]').val(item3);
                                        }
                                        if (key3 == 'bTelMain') {
                                            $('[name=realestate_telmain]').val(item3);
                                        }
                                        if (key3 == 'bFaxArea') {
                                            $('[name=realestate_faxarea]').val(item3);
                                        }
                                        if (key3 == 'bFaxMain') {
                                            $('[name=realestate_faxmain]').val(item3);
                                        }
                                        if (key3 == 'bZip') {
                                            $('[name=realestate_zip]').val(item3);
                                            zipNo = item3 ;
                                            $('[name=realestate_zipF]').val(item3.substr(0,3));
                                        }
                                        if (key3 == 'bCity') {
                                            $('#realestate_countryR').empty() ;
                                            if (item3 == null) {
                                                $('#realestate_countryR').html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
                                            }
                                            else {
                                                $('#realestate_countryR').html('<select class="input-text-big" name="realestate_country" id="realestate_country" disabled="disabled"><option value="' + item3 + '" selected="selected">' + item3 + '</option></select>') ;
                                            }
                                        }
                                        if (key3 == 'bArea') {
                                            $('#realestate_areaR').empty() ;
                                            if (item3 == null) {
                                                $('#realestate_areaR').html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
                                            }
                                            else {
                                                $('#realestate_areaR').html('<select class="input-text-big" name="realestate_area" id="realestate_area" disabled="disabled"><option value="' + zipNo + '" selected="selected">' + item3 + '</option></select>') ;
                                            }
                                        }
                                        if (key3 == 'bAddress') {
                                            $('[name=realestate_addr]').val(item3);
                                        }
                                        //仲介店1
                                        if (key3 == 'bStore') {
                                            $('#bt').text();

                                            $('#bt').text(item3);
                                        }
                                        //
                                        if (key3 == 'bName') {
                                            $('[name=realestate_name]').val(item3);
                                        }
                                        if (key3 == 'bServiceOrderHas') {
                                            if (item3=='1') {
                                                item3 = '有' ;
                                            }
                                            else {
                                                item3 = '無' ;
                                            }
                                            $('#promissory1').html(item3);
                                        }
                                        if (key3 == 'bId') {
                                            $("[name=realestate_branch]").children().each( function(key4, item4){
                                                if ($(item4).val() == item3){
                                                    $(item4).attr("selected","true"); 
                                                    /* 若為非仲介成交店家 */
                                                    if (item3 == 505) {     //非仲介成交選擇時，將回饋對象改勾選為地政士
                                                        $('#FBT2').prop('checked',true) ;
                                                    }
                                                    else {
                                                        $('#FBT1').prop('checked',true) ;
                                                    }
                                                    ////
                                                }
                                            });
                                        }
                                        if (key3 == 'bRecall') {
                                            $('[name=realestate_bRecall]').val(item3);
                                            $("#rea_bRecall").text(item3);
                                        }

                                        if (key3 == 'bScrRecall') {
                                            $('[name=realestate_bScrRecall]').val(item3);
                                            $("#rea_bScrRecall").text(item3);
                                        }

                                        if (key3 =='bStatus') {
                                            $('[name=branch_staus]').val(item3);
                                        }
                                        
                                        if (key3 == 'feedBackData') {
                                            // alert('1');
                                            $("#branchFeedData").html(item3);
                                        }
                                        if (key3 == 'bSerialnum') {
                                           $('[name=realestate_serialnumber]').val(item3);
                                        }
                                        if (key3 == 'bCashierOrderMemo') {
                                            $('[name=Feedback_CashierOrderMemo]').val(item3);
                                        }
                                        if (key3 == 'bCooperationHas') {
                                            $('[name="data_feedData"]').val(item3);
                                        }

                                        if (key3 == 'sales') {
                                            $('#showSalseB').text(item3);
                                        }
                                     
                                });
                                feedback_money() ;
                                }
                             });
                        }
                        if (key == 1) { 
                            $.each(item, function(key2,item2) {
                                if (key2 == 'bCategory') {
                                    $('[name=realestate_branchcategory]').val(item2);
                                }
                            });
                        }
                    });
                });

            }
            
            function CatchBranch_A(no) {
                
                no = no || 1 ;
                 ClearBranch(no)
                $.ajax({  
                    url: "/includes/maintain/branchsearch.php",
                    type: "POST",
                    cache: false,
                    data: {
                        id:$('[name=realestate_branchnum'+no+']').val()
                    },
                    dataType: "json"
                }).done( function( data ) {
                    var zipNo = '' ;
                    $.each(data, function(key,item) {
                        if (key == 0) {
                            $.each(item, function(key2,item2) {
                                if (key2 == 0) {
                                    $.each(item2, function(key3,item3) {
                                        if (key3 == 'bTelArea') {
                                            $('[name=realestate_telarea'+no+']').val(item3);
                                        }
                                        if (key3 == 'bTelMain') {
                                            $('[name=realestate_telmain'+no+']').val(item3);
                                        }
                                        if (key3 == 'bFaxArea') {
                                            $('[name=realestate_faxarea'+no+']').val(item3);
                                        }
                                        if (key3 == 'bFaxMain') {
                                            $('[name=realestate_faxmain'+no+']').val(item3);
                                        }
                                        if (key3 == 'bZip') {
                                            $('[name=realestate_zip'+no+']').val(item3);
                                            zipNo = item3 ;
                                            $('[name=realestate_zip'+no+'F]').val(item3.substr(0,3));

                                        }
                                        if (key3 == 'bCity') {
                                            $('#realestate_country'+no+'R').empty() ;
                                            if (item3 == null) {
                                                $('#realestate_country'+no+'R').html('<select class="input-text-big" name="realestate_country'+no+'" id="realestate_country'+no+'" disabled="disabled"><option value="0" selected="selected">縣市</option></select>') ;
                                            }
                                            else {
                                                $('#realestate_country'+no+'R').html('<select class="input-text-big" name="realestate_country'+no+'" id="realestate_country'+no+'" disabled="disabled"><option value="' + item3 + '" selected="selected">' + item3 + '</option></select>') ;
                                            }
                                        }
                                        if (key3 == 'bArea') {
                                            $('#realestate_area'+no+'R').empty() ;
                                            if (item3 == null) {
                                                $('#realestate_area'+no+'R').html('<select class="input-text-big" name="realestate_area'+no+'" id="realestate_area'+no+'" disabled="disabled"><option value="0" selected="selected">鄉鎮市區</option></select>') ;
                                            }
                                            else {
                                                $('#realestate_area'+no+'R').html('<select class="input-text-big" name="realestate_area'+no+'" id="realestate_area'+no+'" disabled="disabled"><option value="' + zipNo + '" selected="selected">' + item3 + '</option></select>') ;
                                            }
                                        }
                                         //仲介店1
                                        if (key3 == 'bStore') {
                                            $('#bt'+no+'').text();
                                            // alert('#bt'+no+'');
                                            $('#bt'+no+'').text(item3);
                                        }
                                        //
                                        if (key3 == 'bAddress') {
                                            $('[name=realestate_addr'+no+']').val(item3);
                                        }
                                        if (key3 == 'bName') {
                                            $('[name=realestate_name'+no+']').val(item3);
                                        }
                                        if (key3 == 'bServiceOrderHas') {
                                            if (item3=='1') {
                                                item3 = '有' ;
                                            }
                                            else {
                                                item3 = '無' ;
                                            }
                                            $('#promissory'+(no+1)).html(item3);
                                        }
                                        if (key3 == 'bId') {
                                            $("[name=realestate_branch"+no+"]").children().each( function(key4, item4){
                                                if ($(item4).val() == item3){
                                                    $(item4).attr("selected","true"); 
                                                }
                                            });
                                        }
                                        if (key3 == 'bRecall') {
                                            $('[name=realestate_bRecall'+no+']').val(item3);
                                            $("#rea_bRecall"+no).text(item3);
                                        }

                                        if (key3 == 'bScrRecall') {
                                            $('[name=realestate_bScrRecall'+no+']').val(item3);
                                            $("#rea_bScrRecall"+no).text(item3);
                                        }

                                        if (key3 =='bStatus') {
                                            $('[name=branch_staus'+no+']').val(item3)
                                        }

                                        if (key3 == 'feedBackData') {
                                            // alert('1');
                                            $("#branchFeedData"+no).html(item3);
                                        }
                                         if (key3 == 'bSerialnum') {
                                           $('[name=realestate_serialnumber'+no+']').val(item3)
                                        }

                                        if (key3 == 'bCashierOrderMemo') {
                                            $('[name=Feedback_CashierOrderMemo'+no+']').val(item3);
                                        }
                                         if (key3 == 'bCooperationHas') {
                                            $('[name="data_feedData'+no+'"]').val(item3);
                                        }
                                         if (key3 == 'sales') {
                                            $('#showSalseB'+no+'').text(item3);
                                        }

                                    });
                                    feedback_money() ;
                                }
                             });
                        }
                        if (key == 1) { 
                            $.each(item, function(key2,item2) {
                                if (key2 == 'bCategory') {
                                    $('[name=realestate_branchcategory'+no+']').val(item2);
                                }
                            });
                       }
                    });
                });

            }
            
            function CatchIncome() {
                var total = 0;
                var sign = $('[name=income_signmoney]').val();
                var affix = $('[name=income_affixmoney]').val();
                var duty = $('[name=income_dutymoney]').val();
                var estimate = $('[name=income_estimatedmoney]').val();
                sign = sign.replace(/\,/g, '');
                affix = affix.replace(/\,/g, '');
                duty = duty.replace(/\,/g, '');
                estimate = estimate.replace(/\,/g, '');
                
                if (!(/^[0-9]+$/).test(sign)) {
                    sign = 0;
                } 
                if (!(/^[0-9]+$/).test(affix)) {
                    affix = 0;
                } 
                if (!(/^[0-9]+$/).test(duty)) {
                    duty = 0;
                } 
                if (!(/^[0-9]+$/).test(estimate)) {
                    estimate = 0;
                } 
                sign = parseInt(sign);
                affix = parseInt(affix);
                duty = parseInt(duty);
                estimate = parseInt(estimate);
                total += sign;
                total += affix;
                total += duty;
                total += estimate;
                $('[name=income_totalmoney]').val(total);
                $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                certifiedmoneyCount() ;
            }
            
            /* 計算保證費金額 */
            function certifiedmoneyCount() {
                var first = $("[name='income_firstmoney']").val();
                var total = $('[name=income_totalmoney]').val();
                var cer = (total-first) * 0.0006 ;
                cer = Math.round(cer) ;
                if (cer<=600) {
                    cer =600; 
                }
                $('[name=income_certifiedmoney]').val(cer) ;
                feedback_money() ;                                              //計算回饋金
            }
            
            function CatchData(type) {
                var status = $('[name="case_status"]').val() ;
                var url_submit = '';
                var input = $('input');
                var textarea = $('textarea');
                var select = $('select');
                var arr_input = new Array();
                
                var valscid = $('[name=scrivener_id] option:selected').val();
                var valba = $('[name=scrivener_id] option:selected').val();

                // $('#save').hide();//禁止使用者多按
               
                if (typeof(valscid) == 'undefined' || valscid == '0' || typeof(valba) == 'undefined') {
                    alert('請選擇地政士和帳號');
                    return;
                }

                // OtherRecall('1');

                // SpRecall();
                if (type == 'add') {
                    url_submit = '/includes/escrow/contractadd.php';
                } else {
                    url_submit = '/includes/escrow/contractsave.php';

                }
                
                var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))

                $.each(select, function(key,item) {
                    if (reg.test($(item).attr("name"))) {
                        
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();
                        }
                            
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        
                    }else{
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    }
                    
                });
                
                $.each(textarea, function(key,item) {
                    arr_input[$(item).attr("name")] = $(item).attr("value");
                });
               
                

                $.each(input, function(key,item) {

                    if ($(item).attr("name") == 'scrivener_print' || $(item).attr("name") == 'realestate_print' || $(item).attr("name") == 'realestate_print1' || $(item).attr("name") == 'realestate_print2' || $(item).attr("name") == 'buyer_print' || $(item).attr("name") == 'owner_print') {
                       if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = 'Y';
                        }
                        else {
                            arr_input[$(item).attr("name")] = 'N';
                        }
                    }else if(reg.test($(item).attr("name"))){
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                 if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }

                           
                        }else{
                             if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    }else if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = '1';
                        }
                        else {
                            arr_input[$(item).attr("name")] = '0';
                        }
                    }else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }
                    }else {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    }
                    
                });

                

                // cServiceTarget
                
                var obj_input = $.extend({}, arr_input);
                $.ajax({
                        
                        url: '/includes/escrow/check_other.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {
                            cid: "<{$data_case.cCertifiedId}>",
                            type:'case3',
                            branch:$("[name='realestate_branchnum']").val(),
                            branch1:$("[name='realestate_branchnum1']").val(),
                            branch2:$("[name='realestate_branchnum2']").val()
                        },
                    }).done(function(showmsg) {
                            // console.log(showmsg);
                        if (showmsg !=' ') {
                            
                            // $('#dialog').html(txt) ;
                            // $('#dialog').dialog('open') ;
                           alert(showmsg);  
                           // console.log(txt)  ;
                        }
                       
                    });
                
                    
                var request = $.ajax({
                    url: url_submit,
                    type: "POST",
                    data: obj_input,
                    dataType: "html"
                });



                request.done( function( msg ) {
                    
                    // $("#test").html(msg);
                    alert(msg);
                    //$('#form_back').submit();
                    if (type == 'add') {
                        var id = $('[name="scrivener_bankaccount"]').val() ;
                        id = id.substr(5,9) ;
                    }
                    else {
                        var id = $('[name="certifiedid"]').val() ;
                    }
                    //alert(id) ;
                    var modify_check = "<{$smarty.session.member_modifycase}>";


                    if (modify_check==1) {
                        $('form[name=form_edit]').attr('action', '/escrow/formbuyowneredit.php');
                        $('form[name=form_edit] input[name=id]').val(id);
                        $('form[name=form_edit]').submit();
                    }else{

                        $('form[name=form_edit]').attr('action', '/others/welcome.php');
                        $('form[name=form_edit]').submit();

                    }

                   
                });

                
            }
            
            function CatchData2(type) {
                var url_submit = '';
                var input = $('input');
                var textarea = $('textarea');
                var select = $('select');
                var arr_input = new Array();
                if (type == 'add') {
                    url_submit = '/includes/escrow/contractadd.php';
                } else {
                    url_submit = '/includes/escrow/contractsave.php';
                }

                $('#save').hide();//禁止使用者多按
                var reg = /.*\[]$/ ;
                    $.each(select, function(key,item) {
                        if (reg.test($(item).attr("name"))) {
                            
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                                
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            
                        }else{
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                        
                    });
                
                    $.each(textarea, function(key,item) {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    });


                    $.each(input, function(key,item) {

                        if ($(item).attr("name") == 'scrivener_print' || $(item).attr("name") == 'realestate_print' || $(item).attr("name") == 'realestate_print1' || $(item).attr("name") == 'realestate_print2' || $(item).attr("name") == 'buyer_print' || $(item).attr("name") == 'owner_print') {
                           if ($(item).is(':checked')) {
                                arr_input[$(item).attr("name")] = 'Y';
                            }
                            else {
                                arr_input[$(item).attr("name")] = 'N';
                            }
                        }else if(reg.test($(item).attr("name"))){
                            if ($(item).is(':checkbox')) {
                                if ($(item).is(':checked')) {
                                     if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                        arr_input[$(item).attr("name")] = new Array();
                                    }
                                    
                                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                                }

                               
                            }else{
                                 if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        }else if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                arr_input[$(item).attr("name")] = '1';
                            }
                            else {
                                arr_input[$(item).attr("name")] = '0';
                            }
                        }else if ($(item).is(':radio')) {
                            if ($(item).is(':checked')) {
                                arr_input[$(item).attr("name")] = $(item).val();
                            }
                        }else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                        
                    });
                    var obj_input = $.extend({}, arr_input);
                    var request = $.ajax({  
                            url: url_submit,
                            type: "POST",
                            data: obj_input,
                            dataType: "html"
                        });
                        request.done( function( msg ) {
                        });
            }
            
            function SplitInvoice() {
                var status = $('[name="case_status"]').val() ;
                $('[name=invoice_invoiceowner]').val(0);
                $('[name=invoice_invoicebuyer]').val(0);
                $('[name=invoice_invoicerealestate]').val(0);
                $('[name=invoice_invoicescrivener]').val(0);
                $('[name=invoice_invoiceother]').val(0);
                //$("[name=income_totalmoney]").attr("disabled", false);
                //var total = $('[name=income_totalmoney]').val();
                var total = $('[name="income_certifiedmoney"]').val() ;
                total = total.replace(/,/g, '');
                
                     var money = 0;
                     var p = 0;
                     if ( (/^[0-9]+$/).test(total) ) {
                        //money = (total * 6) / 10000;
                        money += total ;
                        if ($('[name=invoice_splitowner]').is(':checked')) {
                            p++;
                        }  
                        if ($('[name=invoice_splitbuyer]').is(':checked')) {
                            p++;
                        }
                         if ($('[name=invoice_splitrealestate]').is(':checked')) {
                            p++;
                        }  
                         if ($('[name=invoice_splitscrivener]').is(':checked')) {
                            p++;
                        }  
                         if ($('[name=invoice_splitother]').is(':checked')) {
                            p++;
                        }  
                        if ( p > 0 ) {
                            money = money / p;
                            money = parseInt(money);
                        if ($('[name=invoice_splitowner]').is(':checked')) {
                            $('[name=invoice_invoiceowner]').val(money);
                        }  
                        if ($('[name=invoice_splitbuyer]').is(':checked')) {
                            $('[name=invoice_invoicebuyer]').val(money);
                        }
                         if ($('[name=invoice_splitrealestate]').is(':checked')) {
                            $('[name=invoice_invoicerealestate]').val(money);
                        }  
                         if ($('[name=invoice_splitscrivener]').is(':checked')) {
                            $('[name=invoice_invoicescrivener]').val(money);
                        }  
                        if ($('[name=invoice_splitother]').is(':checked')) {
                            $('[name=invoice_invoiceother]').val(money);
                        }  
                        }
                     }
                     $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                     //$("[name=income_totalmoney]").attr("disabled", true);
                    
                    if (p > 0) {
                        $('#save').css({'display':''}) ;
                    }
                    if (p==0 && status==3) {
                        $('#save').css({'display':'none'}) ;
                    }
            }    
            function SubArea() {
                var total = 0;
                    var main = $('[name=property_measuremain]').val();
                    var common = $('[name=property_measurecommon]').val();
                    var ext = $('[name=property_measureext]').val();
                    if ( (/^[0-9]+\.?[0-9]{0,5}$/).test(main) ) {
                        main = Math.floor(parseFloat(main)*100) /100;
                        $('[name=property_measuremain]').val(main);
                    }
                    if (  (/^[0-9]+\.?[0-9]{0,5}$/).test(common) ) {
                        common = Math.floor(parseFloat(common)*100) /100;
                        $('[name=property_measurecommon]').val(common);
                    }
                    if (  (/^[0-9]+\.?[0-9]{0,5}$/).test(ext) ) {
                        ext = Math.floor(parseFloat(ext)*100) /100;
                        $('[name=property_measureext]').val(ext);
                    }
                    total = (main*100) + (common*100) + (ext*100);
                    total = Math.floor( total) /100;
                    $('[name=property_measuretotal]').val(total);
            }
            
            function CountDelMoney() {
                var rsm = $('[name=expenditure_realestatemoney]').val();
                var adm = $('[name=expenditure_advancemoney]').val();
                var dm = 0;
                
                rsm = rsm.replace(/,/g, '');
                adm = adm.replace(/,/g, '');
                
                if (  (/^[0-9]+$/).test(rsm) &&  (/^[0-9]+$/).test(adm)) {
                        rsm = parseInt(rsm);
                        adm = parseInt(adm);
                        if (rsm > adm) {
                            dm = rsm - adm;
                        }
                }
                $('[name=expenditure_dealmoney]').val(dm)
                $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                
                var rsm = $('[name=expenditure_realestatemoney_buyer]').val();
                var adm = $('[name=expenditure_advancemoney_buyer]').val();
                var dm = 0;
                
                rsm = rsm.replace(/,/g, '');
                adm = adm.replace(/,/g, '');
                
                if (  (/^[0-9]+$/).test(rsm) &&  (/^[0-9]+$/).test(adm)) {
                        rsm = parseInt(rsm);
                        adm = parseInt(adm);
                        if (rsm > adm) {
                            dm = rsm - adm;
                        }
                }
                $('[name=expenditure_dealmoney_buyer]').val(dm)
                $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                
            }
            
            function CatchBranchCategory() {
                var request = $.ajax({  
                    url: "/includes/maintain/branchcategory.php",
                    type: "POST",
                    data: {
                        id:$('[name=realestate_branchnum]').val()
                    },
                    dataType: "json"
                });
                request.done( function( data ) {
                    $.each(data, function(key,item) {
                        if (key == 'bCategory') {
                            $("[name=realestate_branchcategory]").children().each(function(kk, vv) {
                                if ($(vv).val() == item) {
                                    $(vv).attr("selected","true");
                                }
                            });
                        }
                    });
                });
            }

            function CheckColor(name,color){    
                $('[name='+name+']').focus();
                $('[name='+name+']').css('background-color', color);
            }
            
            function CheckField() {
                var msg = new Array();
                var index = 0;
                if ($('[name=case_status]').val() == '3' || $('[name=case_status]').val()=='4') {
                   
                    if ($('[name=buy_name]').val() == '') {
                        CheckColor('buy_name','#E4BEB1');
                        
                        msg[index] = '買方姓名';
                        index++;
                    }else{
                        CheckColor('buy_name','#FFFFFF');
                    }

                    if ($('[name=buy_identifyid]').val() == '') {

                        CheckColor('buy_identifyid','#E4BEB1');
                        msg[index] = '買方帳號';
                        index++;
                    }else{
                        CheckColor('buy_identifyid','#FFFFFF');
                    }


                    if ($('[name=buyer_registzip]').val() == '' || $('[name=buyer_registaddr]').val() == '') {

                        CheckColor('buyer_registaddr','#E4BEB1');
                        CheckColor('buyer_registcountry','#E4BEB1');
                        CheckColor('buyer_registarea','#E4BEB1');
                        
                        msg[index] = '買方戶籍地址';
                        index++;
                    }else{
                        CheckColor('buyer_registaddr','#FFFFFF');
                        CheckColor('buyer_registcountry','#FFFFFF');
                        CheckColor('buyer_registarea','#FFFFFF');
                    }


                    if ($('[name=buyer_basezip]').val() == '' || $('[name=buyer_baseaddr]').val() == '') {
                        CheckColor('buyer_basecountry','#E4BEB1');
                        CheckColor('buyer_basearea','#E4BEB1');
                        CheckColor('buyer_baseaddr','#E4BEB1');
                        
                        msg[index] = '買方通訊地址';
                        index++;
                    }else{
                        CheckColor('buyer_basecountry','#FFFFFF');
                        CheckColor('buyer_basearea','#FFFFFF');
                        CheckColor('buyer_baseaddr','#FFFFFF');
                    }

                    if ($('[name=owner_name]').val() == '') {

                        CheckColor('owner_name','#E4BEB1');
                        msg[index] = '賣方姓名';
                        index++;
                    }else{
                        CheckColor('owner_name','#FFFFFF');
                    }


                    if ($('[name=owner_identifyid]').val() == '') {

                        CheckColor('owner_identifyid','#E4BEB1');
                        msg[index] = '賣方帳號';
                        index++;
                    }else{
                        CheckColor('owner_identifyid','#FFFFFF');
                    }  

                    if ($('[name=owner_registzip]').val() == '' || $('[name=owner_registaddr]').val() == '') {

                        CheckColor('owner_registcountry','#E4BEB1');
                        CheckColor('owner_registarea','#E4BEB1');
                        CheckColor('owner_registaddr','#E4BEB1');

                        msg[index] = '賣方戶籍地址';
                        index++;
                    }else{
                        CheckColor('owner_registcountry','#FFFFFF');
                        CheckColor('owner_registarea','#FFFFFF');
                        CheckColor('owner_registaddr','#FFFFFF');

                    }

                    if ($('[name=owner_basezip]').val() == '' || $('[name=owner_baseaddr]').val() == '') {

                        CheckColor('owner_basecountry','#E4BEB1');
                        CheckColor('owner_basearea','#E4BEB1');
                        CheckColor('owner_baseaddr','#E4BEB1');
                        msg[index] = '賣方通訊地址';
                        index++;
                    }else{

                        CheckColor('owner_basecountry','#FFFFFF');
                        CheckColor('owner_basearea','#FFFFFF');
                        CheckColor('owner_baseaddr','#FFFFFF');
                    }
                     // if ($('[name=realestate_branchcategory]').val() == '0') {
                    
                    //     msg[index] = '仲介商'; 
                    //     index++;
                    // }
                    // if ($('[name=realestate_branchnum]').val() == '') {
                    //     msg[index] = '仲介店名'; 
                    //     index++;
                    // }
                    // if ($('[name=certifiedid]').val() == null) {
                    //     msg[index] = '專屬帳號';
                    //     index++;
                    // }

                     // if (typeof($('[name=owner_categoryidentify]:checked').val()) == 'undefined') {
                    //     msg[index] = '賣方帳號類型';
                    //     index++;
                    // }


                    // if (typeof($('[name=buy_categoryidentify]:checked').val()) == 'undefined') {
                    //     msg[index] = '買方帳號類型';
                    //     index++;
                    // }
                    
                    // if ($('[name=buy_mobilenum]').val() == '') {
                    //     msg[index] = '買方行動電話';
                    //     index++;
                    // }
                  
                    
                    var show = '儲存請先確定以下資料是否有填齊全︰\n';
                    show +=  msg.join('、');
                    show += '\n';
                    if (msg.length == 0) {


                        return true;
                    } else {
                        alert(show);
                        return false;
                    }
                } 
            }
             function CheckDate() {
                    var show = '';
                    var msg = new Array();
                    var index = 0;
                    
                    if (!DateInspection($('[name=case_signdate]').val(),'b')) {
                        msg[index] = '簽約日期';
                        index++;
                    }
                    if (!DateInspection($('[name=case_cEndDate]').val(),'b')) {
                        msg[index] = '實際點交日期';
                        index++;
                    }
                    if (!DateInspection($('[name=land_movedate]').val(),'a')) {
                        msg[index] = '前次移轉現值或原規定地價';
                        index++;
                    }
                    if (!DateInspection($('[name=owner_birthdayday]').val(),'b')) {
                        msg[index] = '賣方出生日期';
                        index++;
                    }
                    if (!DateInspection($('[name=buy_birthdayday]').val(),'b')) {
                        msg[index] = '買方出生日期';
                        index++;
                    }
                   
                    
                    show += '請確定日期格式是否正確，例︰ 101-06-11 或 101-06︰\n';
                    show += msg.join('、');
                    
                     if (msg.length == 0) {
                        return true;
                    } else {
                        alert(show);
                        return false;
                    }
                }
            function chk_status() {
                var status = $('[name="case_status"]').val() ;
                var dd = '<{$data_case.cEndDate}>' ;
                
                if (dd == '0000-00-00 00:00:00') {
                    dd = '' ;
                }
                
                var today = new Date() ;
                var now_day = (today.getFullYear() - 1911) + '-' + (today.getMonth()+1) + '-' + today.getDate() ;
                
                if (status==3) {
                    //if ($('[name=invoice_splitowner]').is(':checked')||$('[name=invoice_splitbuyer]').is(':checked')||$('[name=invoice_splitrealestate]').is(':checked')||$('[name=invoice_splitscrivener]').is(':checked')||$('[name=invoice_splitother]').is(':checked')||$('[name=cCaseFeedback]').is(':checked')) {
                    /* 發票金額與利息分配檢核 */
                    if ((invoice_dealing() == 1)&&(interest_dealing() == 1)) {
                        if (dd != '') { $('[name=case_cEndDate]').val(dd) ; }
                        else { $('[name=case_cEndDate]').val(now_day) ; }
                        //$('#save').css({'display':''}) ;
                        $('#save').show() ;
                    }
                    else {
                        //$('#save').css({'display':'none'}) ;
                        $('#save').hide() ;
                        if (dd != '') { $('[name=case_cEndDate]').val(dd) ; }
                        else { $('[name=case_cEndDate]').val(now_day) ; }

                        alert('請確認發票對象或利息金額是否正確分配!?') ;
                    }


                    //判斷幸福家是否有選賣方仲介店
                    var brand = $("[name='realestate_brand']").val();
                    var brand1 = $("[name='realestate_brand1']").val();
                    var brand2 = $("[name='realestate_brand2']").val();
                    var ServiceTarget = $("[name='cServiceTarget']:checked").val();
                    var ServiceTarget1 = $("[name='cServiceTarget1']:checked").val();
                    var ServiceTarget2 = $("[name='cServiceTarget2']:checked").val();

                    if (brand == 69 || brand1 == 69 || brand2 == 69) {
                        //1.買賣方、2.賣方、3.買方
                        if ( ServiceTarget != 2 && ServiceTarget1 != 2 && ServiceTarget2 != 2) {
                            alert('幸福家請選賣方仲介店');
                            // return false;
                        }
                    }
                    $('[name="check_End"]').val(1); //確認是否改為結案
                    //
                    //進度表
                    for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                        $('#ps'+i).addClass('step_class') ;
                    }
                    
                }
                else if ((status==4)||(status==5)||(status==7)||(status==8)) {
                    if (dd != '') { $('[name=case_cEndDate]').val(dd) ; }
                    else { $('[name=case_cEndDate]').val(now_day) ; }
                    $('#save').css({'display':''}) ;

                     for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                      
                    }

                }
                else {
                    if (dd != '') { $('[name=case_cEndDate]').val(dd) ; }
                    else { $('[name=case_cEndDate]').val('') ; }

                    if (status==2) {
                        $('[name=case_cEndDate]').val('') ;
                    }

                    $('#save').css({'display':''}) ;
                    for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                        $('#ps1').addClass('step_class') ;
                    }
                }

            }

            function chk_other () {
                 if ($("[name='buy_identifyid']").val()=='') {

                        alert("買方身分證未填寫");
                        return 2;

                    }else if ($("[name='buyer_registzipF']").val()==''||$("[name='buyer_registaddr']").val()=='') {

                        alert("買方戶藉地址未填寫");
                        return 2;
                    }else if ($("[name='buyer_basezipF']").val()==''||$("[name='buyer_baseaddr']").val()=='') {

                        alert("買方通訊地址未填寫");
                        return 2;
                    }else if ($("[name='owner_identifyid']").val()=='') {

                        alert("賣方身分證未填寫");
                        return 2;
                    }else if ($("[name='owner_registzipF']").val()==''||$("[name='owner_registzipF']").val()=='') {
                        alert("賣方戶藉地址未填寫");
                        return 2;
                    }else if ($("[name='owner_basezipF']").val()==''||$("[name='owner_baseaddr']").val()=='') {
                        alert("賣方通訊地址未填寫");
                        return 2;
                        
                    }else{
                        return 1;
                    }


            }
            
            /* 檢核發票金額是否分配完成 */
            function invoice_dealing() {
                var tot = parseInt($('[name="income_certifiedmoney"]').val()) ;             //履保費總額
                var vOwner = parseInt($('[name="invoice_invoiceowner"]').val()) ;           //賣方發票金額
                var vBuyer = parseInt($('[name="invoice_invoicebuyer"]').val()) ;           //買方發票金額
                var vRealty = parseInt($('[name="invoice_invoicerealestate"]').val()) ;     //仲介發票金額
                var vScr = parseInt($('[name="invoice_invoicescrivener"]').val()) ;         //代書發票金額
                var vOther = parseInt($('[name="invoice_invoiceother"]').val()) ;           //其他發票金額

                // alert($('[name="invoice_invoiceother"]').val());
                if ($('[name="invoice_invoiceother"]').val()==undefined) {
                    vOther=0;
                }
                
                var all_inv = vOwner + vBuyer + vRealty + vScr + vOther ;
              
                if (tot == all_inv) {
                    return 1 ;
                }
                else {
                    return 0 ;
                }
            }
            //
             /* 檢核發票金額是否分配完成 (細項分配)*/
            function invoice_dealing2() {

                // 
                var tot = $('[name="income_certifiedmoney"]').val() ;             //履保費總額
                var vOwner = parseInt($('[name="invoice_invoiceowner"]').val()) ;           //賣方發票金額
                var vBuyer = parseInt($('[name="invoice_invoicebuyer"]').val()) ;           //買方發票金額
                var vRealty = parseInt($('[name="invoice_invoicerealestate"]').val()) ;     //仲介發票金額
                var vScr = parseInt($('[name="invoice_invoicescrivener"]').val()) ;         //代書發票金額
                var vOther = parseInt($('[name="invoice_invoiceother"]').val()) ;           //其他發票金額



                if ($('[name="invoice_invoiceother"]').val()==undefined) {
                    vOther=0;
                }

                var  all_inv= vOwner + vBuyer + vRealty + vScr + vOther ;


                var part_total = 0;

                
               
                    
                //賣方部分
                if (vOwner !=0) {
                    
                    $(".inv_show_owner").each(function() {
                        part_total = part_total+parseInt($(this).val());
                    });

                    if (part_total==0 || (part_total != vOwner)) {
                       return '0';
                    }
                }
                
                //買方部分
                part_total = 0;

                if (vBuyer !=0) {
                    
                    $(".inv_show_buyer").each(function() {
                        
                        part_total = part_total+parseInt($(this).val());
                       
                    });

                    if ((part_total== 0) || (part_total != vBuyer)) {

                       return '0';
                    }
                }

                //仲介部分
                part_total = 0;

                if (vRealty !=0) {

                    $(".inv_show_branch").each(function() {
                        part_total = part_total+parseInt($(this).val());
                    });

                    if (part_total==0 || (part_total != vRealty)) {
                       return '0';
                    }
                }

                 //地政士部分
                part_total = 0;

                if (vScr !=0) {

                    $(".inv_show_scrivener").each(function() {
                        part_total = part_total+parseInt($(this).val());
                    });

                    if (part_total==0 || (part_total != vScr)) {
                       return '0';
                    }
                }

               
                // alert(tot +"!="+ all_inv);
                // return '1';
                if (tot != all_inv) {
                    return '0' ;
                }
                else {
                    return '1' ;
                }
            }
            
            /* 檢核利息是否分配完成 */
            function interest_dealing() {
                var intA = $('[name="int_total"]').val() ;
                var intB = $('[name="int_money"]').val() ;
                //alert('A='+intA+',B='+intB) ;
                
                if (intA == intB) {
                    return 1 ;
                }
                else {
                    return 0 ;
                }
            }
            //
            
            function unlock() {
                var no = $('[name="certifiedid"]').val() ;
                var url = 'formbuyowner_chg_status.php' ;
                $('form[name="form_edit"]').attr('action',url) ;
                $('form[name=form_edit] input[name=id]').val(no);
                
                if(confirm('是否確認要將"'+no+'"的狀態改為"進行中"!?')) {
                    $('[name="form_edit"]').submit() ;
                }
                else {
                    return false ;
                }
            }
            function addBranchList(i) {
                $('#addBranch'+i).html('') ;
                var item = '.show_' + i + '_realty' ;
                $(item).show() ;
            }
            
            function feedback_money() {
                //清空回饋金
                $('[name="cCaseFeedBackMoney"]').val(0) ;
                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                $('input:radio[name="cFeedbackTarget"]').filter('[value="1"]').attr('checked',true) ;

                var _val = parseFloat(33.33/100) ;//預設回饋比率為萬分之2 //2013-10-09 依據政耀要求只要是配件案件，一律回饋萬分之2 //2015/01/22改為百分之33 //20150204配件以最小的比率為主 //2015/04/28 預設回饋比率改為百分之33.33
               
                var cer_real = parseInt($('[name="income_certifiedmoney"]').val(),10) ;     //實收保證費
                var _total = parseInt($('[name="income_totalmoney"]').val()) ;              //總價金
                var first  =parseInt($('[name="income_firstmoney"]').val());                //降保

                // alert($('[name="income_firstmoney"]').val());
                if ($('[name="income_firstmoney"]').val()=='') {
                    $('[name="income_firstmoney"]').val(0);
                }
                var cer_title =( _total-first) * 0.0006 ;       //總價金萬分之六的應收保證費
                // alert(cer_real+"<"+cer_title);
                if ((cer_real + 10) < cer_title) {                                          //實收(+10元誤差)小於應收 ※不回饋
                    $('[name="cCaseFeedBackMoney"]').val(0) ;
                    $('[name="cCaseFeedBackMoney1"]').val(0) ;
                    $('[name="cCaseFeedBackMoney2"]').val(0) ;
                    $('[name="cSpCaseFeedBackMoney"]').val(0) ;

                    var branchbook = $("[name='data_feedData']").val();//合作契約書
                    var branchbook1 = $("[name='data_feedData1']").val();//合作契約書
                    var branchbook2 = $("[name='data_feedData2']").val();//合作契約書
                    $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                  
                    if (branchbook == '' || branchbook == 0 || branchbook == undefined) {
                         $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                    }else

                    if (branchbook1 == '' || branchbook1 == 0 || branchbook1 == undefined) {
                         $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ;
                    }

                    if (branchbook2 == '' || branchbook2 == 0 || branchbook2 == undefined) {
                         $('input:radio[name="cFeedbackTarget2"]').filter('[value="2"]').attr('checked',true) ;
                    }
                   

                }else{
                    //回饋
                    $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;

                    var tg1 = $('[name="cFeedbackTarget"]:checked').val() ;
                    var tg2 = $('[name="cFeedbackTarget1"]:checked').val() ;
                    var tg3 = $('[name="cFeedbackTarget2"]:checked').val() ;
                    var realty1 = $('[name="realestate_branchnum"]').val() ;
                    var realty2 = $('[name="realestate_branchnum1"]').val() ;
                    var realty3 = $('[name="realestate_branchnum2"]').val() ;
                    var brandScr1 = $("[name='scrivener_BrandScrRecall']").val();
                    var brandScr2 = $("[name='scrivener_BrandScrRecall1']").val();
                    var brandScr3 = $("[name='scrivener_BrandScrRecall2']").val();
                    var brand = $("[name='realestate_brand']").val();
                    var brand1 = $("[name='realestate_brand1']").val();
                    var brand2 = $("[name='realestate_brand2']").val();
                    var branchbook = $("[name='data_feedData']").val();//合作契約書
                    var branchbook1 = $("[name='data_feedData1']").val();//合作契約書
                    var branchbook2 = $("[name='data_feedData2']").val();//合作契約書
                    var scrrecall =new Array();
                    var brecall =new Array();
                    var bcount  = 0;
                    var type = 0;//0正常,1重複比率
                    var scrFeedMoney = 0;
                    var _feedbackMoney = 0;
                    var scrpart = 0;
                    var scrpartsp =new Array();
                    var casecheck = 0;//判斷是否回饋給代書(特殊回饋高於正常回饋%數)
                    var sSpRecall=  parseFloat("<{$scrivener_sSpRecall}>")/100;
                    //仲介回饋 
                   
                    //比率
                       if (realty1 > 0) {
                            if (tg1 == 2) {//回饋對象為代書
                                brecall[0] = parseFloat($('[name="sRecall"]').val())/100;

                            }else{
                                brecall[0] = parseFloat($('[name=realestate_bRecall]').val())/100;      
                            }
                            //仲介回饋地政士
                            if ($('[name="realestate_bScrRecall"]').val() != '') {
                                scrrecall[0] = parseFloat($('[name="realestate_bScrRecall"]').val())/100;
                                scrpart = scrrecall[0];
                            }
                            
                             //品牌回饋代書(優先算)
                            if ( brandScr1 != '0' && brandScr1 != '') {
                               brecall[0] = parseFloat($('[name=scrivener_BrandRecall]').val())/100;      
                               scrpart = parseFloat($('[name="scrivener_BrandScrRecall"]').val())/100;
                               scrpartsp[0] = parseFloat($('[name="scrivener_BrandScrRecall"]').val())/100;

                            }

                            //怕有空值，一律以預設%數為主
                            if (brecall[0] == '') {brecall[0] = _val};
                             
                             bcount++;
                       }

                       if(realty2 > 0){
                            
                            if (tg2 == 2) {//回饋對象為代書
                                brecall[1] = parseFloat($('[name="sRecall"]').val())/100;                        
                            }else{
                                brecall[1] = parseFloat($('[name=realestate_bRecall1]').val())/100;                       
                            }
                            

                            //仲介回饋地政士
                            if ($('[name="realestate_bScrRecall1"]').val() != 0) {
                                scrrecall[1] = parseFloat($('[name="realestate_bScrRecall1"]').val())/100;
                                scrpart = scrrecall[1];
                            }

                            //品牌回饋代書(優先算)
                            if (brandScr2 != '0' && brandScr2 != '') {
                               brecall[1] = parseFloat($('[name=scrivener_BrandRecall1]').val())/100;      
                               scrpart = parseFloat($('[name="scrivener_BrandScrRecall1"]').val())/100;
                               scrpartsp[1] = parseFloat($('[name="scrivener_BrandScrRecall1"]').val())/100;
                            }

                            //比對比率是否一樣 ()
                            if (brecall[0] == brecall[1]) {
                                type = 1;
                            }

                            //怕有空值，一律以預設%數為主
                            if (brecall[1] == '') {brecall[1] = _val};

                              bcount++;

                       }

                       if(realty3 > 0){
                            if (tg3 == 2) {//回饋對象為代書
                                brecall[2] = parseFloat($('[name="sRecall"]').val())/100;
                                
                            }else{
                                brecall[2] = parseFloat($('[name=realestate_bRecall2]').val())/100;
                               
                            }

                            //仲介回饋地政士
                            if ($('[name="realestate_bScrRecall2"]').val() != '') {
                                scrrecall[2] = parseFloat($('[name="realestate_bScrRecall2"]').val())/100;
                                scrpart = scrrecall[2];
                                $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ;
                            }

                             //品牌回饋代書(優先算)
                            if (brandScr3 != '0' && brandScr3 != '') {
                               brecall[2] = parseFloat($('[name=scrivener_BrandRecall2]').val())/100;     
                               scrpart = parseFloat($('[name="scrivener_BrandScrRecall2"]').val())/100;
                               scrpartsp[2] = parseFloat($('[name="scrivener_BrandScrRecall2"]').val())/100;
                            }
                            //比對比率是否一樣 ()
                            if (brecall[2] == brecall[1] || brecall[2] == brecall[0]) {
                                type = 1;
                            }

                            //怕有空值，一律以預設%數為主
                            if (brecall[2] == '') {brecall[2] = _val};
                           
                             bcount++;
                       }

                    ///////////////////////////幸福家配件要各自算(20170112)//////////
                    //賣方    幸福家
                    //買方    不管品牌
                    //回饋金  給賣方

                    //賣方  他牌
                    //買方  幸福家
                    // 1.按照一般算法算(含保證書[基本資料維護有回饋資料]) 
                    // 2.給幸福家(不含保證書[基本資料維護有回饋資料])
                    ////////////////////////////////////////////////////////////////////
                    
                    var ownerbrand = '';
                    var ownercol = '';
                    var ownerRecall ='';
                    var ownercheck = 0;
                    var o = 0;
                    var buyerbrand = '';
                    var buyercol = '';
                    var buyerRecall = '';
                    var buyercheck = 0;
                       
                    if (bcount > 1 && (brand == 69 || brand1 == 69 || brand2 ==69) ) {
                        
                        if ($("[name='cServiceTarget']:checked").val() == 2) {
                            ownerbrand = brand;
                            ownercol = 'cCaseFeedBackMoney';
                            ownerRecall = brecall[0];
                            ownercheck = branchbook;
                            o++;
                        }else{
                            buyerbrand = brand;
                            buyercol = 'cCaseFeedBackMoney';
                            buyerRecall = brecall[0];
                            buyercheck = branchbook;
                        }

                        if (brand1 > 0 && realty2 > 0) {
                            if ($("[name='cServiceTarget1']:checked").val() == 2) {
                                ownerbrand = brand1;
                                ownercol = 'cCaseFeedBackMoney1'; 
                                ownerRecall = brecall[1];
                                ownercheck = branchbook1;
                                o++;
                            }else{
                                buyerbrand = brand1;
                                buyercol = 'cCaseFeedBackMoney1';
                                buyerRecall = brecall[1];
                                buyercheck = branchbook1;
                            }
                        }
                        
                        if (brand2 > 0 && realty3 > 0) {
                            if ($("[name='cServiceTarget2']:checked").val() == 2) {
                                ownerbrand = brand2;
                                ownercol = 'cCaseFeedBackMoney2';  
                                ownerRecall = brecall[2]; 
                                ownercheck = branchbook2;  
                                o++; 
                            }else{
                                buyerbrand = brand2;
                                buyercol = 'cCaseFeedBackMoney2';
                                buyerRecall = brecall[2];
                                buyercheck = branchbook2;   
                            }
                        }

                        if (o == 0) {//沒有選定賣方則從買賣方選一個
                            if ($("[name='cServiceTarget']:checked").val() == 1) {

                                ownerbrand = brand;
                                ownercol = 'cCaseFeedBackMoney';
                                ownerRecall = brecall[0];

                            }else if($("[name='cServiceTarget1']:checked").val() == 1 && brand1 > 0) {

                                ownerbrand = brand1;
                                ownercol = 'cCaseFeedBackMoney1';
                                ownerRecall = brecall[1];

                            }else if($("[name='cServiceTarget2']:checked").val() == 1 && brand2 > 0) {

                                ownerbrand = brand2;
                                ownercol = 'cCaseFeedBackMoney2';
                                ownerRecall = brecall[2];
                            }
                        }
                        // console.log(ownerbrand);
                        if (ownerbrand == 69) {

                            _feedbackMoney = Math.round(ownerRecall*cer_real);
                            // console.log(ownerRecall+"_"+cer_real);
                            $("[name='"+ownercol+"']").val(_feedbackMoney);
                            $("[name='"+buyercol+"']").val(0);


                        }else if (ownerbrand != 69) {
                            if (ownercheck > 0) {//他牌有合作契約書
                                if (bcount == 3 && (branchbook2 == '' || branchbook2 == 0 || branchbook2 == undefined)) {
                                    bcount = bcount -1;
                                }
                                if (type == 1) {
                                    _feedbackMoney = Math.round(brecall[0]*cer_real);
                                    var _fdMod = _feedbackMoney % bcount ;
                                    _feedbackMoney = Math.floor(_feedbackMoney / bcount) ;
                        
                                    $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney+_fdMod) ;
                                    $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
                                    $('[name="cCaseFeedBackMoney2"]').val(0) ;

                                    if (bcount == 3) {
                                        $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                    }
                                }else{
                                    
                                    _feedbackMoney = Math.round((brecall[0]*cer_real)/bcount);
                                    $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                 
                                    _feedbackMoney = Math.round((brecall[1]*cer_real)/bcount);
                                    $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
                                    if (bcount == 3) {
                                        _feedbackMoney = Math.round((brecall[2]*cer_real)/bcount);
                                        $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                    }
                                    
                                }
                                
                            }else{
                                _feedbackMoney = Math.round(ownerRecall*cer_real);
                                $("[name='"+buyercol+"']").val(_feedbackMoney);
                                $("[name='"+ownercol+"']").val(0);

                            }
                        }
                        
                    }else{

                        //目前仲介回饋給地政士都是同比率
                        scrpartsp.sort(); //取一個就好



                        if (bcount == 1) { //只有一間店
                          

                            //無合作契約書(給代書)
                           
                            if ((branchbook == 0 || branchbook == undefined) && (brand != 1 && brand!=49)) {
                                $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                                // if (true) {};
                                // if ($('[name="sRecall"]').val() < ) {};
                                
                                casecheck = 1;
                                
                                
                            }else{
                                // console.log('branchbook');
                                $('input:radio[name="cFeedbackTarget"]').filter('[value="1"]').attr('checked',true) ;
                            }
                             // console.log(casecheck);

                            if ((scrrecall[0] != 0 || scrpartsp[(bcount-1)] !=0 ) && casecheck == 0) { 
                                scrFeedMoney = Math.round(scrpart*cer_real) ;

                                $('#sp_show_mpney').show();
                                $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;

                            }else{
                                $('[name="cSpCaseFeedBackMoney"]').val(0) ;
                            }

                            _feedbackMoney = Math.round(brecall[0]*cer_real);
                            

                            $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                            $('[name="cCaseFeedBackMoney1"]').val(0) ;
                            $('[name="cCaseFeedBackMoney2"]').val(0) ;

                            if (casecheck == 1 && (brand != 69 || brand != 1 || brand != 49)) {//如果回饋給地政士且不等於台屋
                                var tmp = brecall[0];
                                if (sSpRecall > tmp) { //如果地政士特殊回饋大於仲介回饋
                                    tmp = sSpRecall;
                                }
                                tmp = tmp;
                                _feedbackMoney = Math.round(tmp*cer_real);
                                
                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                            }

                                // if (tg1 == 2) {
                                //     $('[name="cSpCaseFeedBackMoney"]').val(0) ;
                                // }
                        }else if(bcount > 1){
                            //預設仲介

                            $('input:radio[name="cFeedbackTarget"]').filter('[value="1"]').attr('checked',true) ;
                            $('input:radio[name="cFeedbackTarget1"]').filter('[value="1"]').attr('checked',true) ;
                            $('input:radio[name="cFeedbackTarget2"]').filter('[value="1"]').attr('checked',true) ;

                            var col = '';
                            var col1 = '';
                            var col2 = '';
                            var tmp_c = 0;
                            
                            //是否為台屋優美有合作契約書
                            if ((brand == 1 || brand ==49 || branchbook > 0)) { 
                                col = 'cCaseFeedBackMoney';
                                tmp_c++;
                            }

                            if ((brand1 == 1 || brand1 ==49 || branchbook1 > 0) && realty2 > 0) {
                                col1 = 'cCaseFeedBackMoney1';
                                tmp_c++;
                            }

                            if ((brand2 == 1 || brand2 ==49 || branchbook2 > 0) && realty3 > 0) {
                                
                                col2 = 'cCaseFeedBackMoney2';
                                tmp_c++;
                            }

                                    
                            //是否為台屋優美有合作契約書
                               
                                if (type == 1) { //一樣比率照舊的算法算 ( (保證費*回饋對象%數)/店家數)

                                    
                                    
                                    if (tmp_c > 1) {
                                        // console.log(tmp_c);
                                        // console.log('保證費:'+cer_real);
                                        _feedbackMoney = Math.round(brecall[0]*cer_real);
                                         // console.log('總回饋金:'+_feedbackMoney);
                                        var _fdMod = _feedbackMoney % tmp_c ;
                                        // console.log('餘:'+_fdMod);
                                        _feedbackMoney = Math.floor(_feedbackMoney / tmp_c) ;
                                         // console.log('平分回饋金:'+_feedbackMoney);

                                       

                                        

                                        if (col != '') {
                                            $('[name="'+col+'"]').val(_feedbackMoney+_fdMod) ;
                                            _fdMod = 0;
                                        }

                                        if (col1 != '') {
                                            $('[name="'+col1+'"]').val(_feedbackMoney+_fdMod) ;
                                            _fdMod = 0;
                                        }

                                        if (col2 !='') {
                                            $('[name="'+col2+'"]').val(_feedbackMoney+_fdMod) ;
                                            _fdMod = 0;
                                        }
                                    
                                    }else if(tmp_c == 1){ //指有一間店有合作契約書
                                        _feedbackMoney = Math.round(brecall[0]*cer_real);
                                        if (col != '') {
                                            $('[name="'+col+'"]').val(_feedbackMoney) ;
                                        }

                                        if (col1 != '') {
                                            $('[name="'+col1+'"]').val(_feedbackMoney) ;
                                        }

                                        if (col2 !='') {
                                            $('[name="'+col2+'"]').val(_feedbackMoney) ;
                                        }
                                    }else{
                                        //都沒有合作契約書
                                        // console.log($('[name="sRecall"]').val());
                                        var s = parseFloat($('[name="sRecall"]').val())/100;
                                        _feedbackMoney = Math.round(s*cer_real);

                                        var _fdMod = _feedbackMoney % bcount ;
                                        _feedbackMoney = Math.floor(_feedbackMoney / bcount) ;
                                        $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney+_fdMod) ;
                                        $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;


                                        $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                                        $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ;

                                        if (bcount == 3) {
                                            $('input:radio[name="cFeedbackTarget2"]').filter('[value="2"]').attr('checked',true) ;
                                            $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                        }

                                        
                                    }
                                    
                                    
                              
                                }else{

                                    if (col != '') {
                                        _feedbackMoney = Math.round((brecall[0]*cer_real)/tmp_c);
                                        $('[name="'+col+'"]').val(_feedbackMoney) ;
                                    }

                                    if (col1 != '') {
                                        _feedbackMoney = Math.round((brecall[1]*cer_real)/tmp_c);
                                        $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
                                    }

                                    if (col2 !='') {
                                        _feedbackMoney = Math.round((brecall[2]*cer_real)/tmp_c);
                                        $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                    }
                                      
                                    

                                     
                                         
                                    // if (bcount == 3) {
                                    //     _feedbackMoney = Math.round((brecall[2]*cer_real)/bcount);
                                    //     $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                    // }
                                }  
                            


                           
                                  
                            //仲介地政士回饋
                            // sort(scrrecall);
                            if ((scrrecall[0] !='' && scrrecall[0] != undefined) && (brandScr1 =="0" || brandScr1 =='')) {
                                $('#sp_show_mpney').show();
                                scrFeedMoney = Math.round(scrrecall[0]*cer_real) ;
                                $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;
        
                            }
                               
                            if ((scrrecall[1] !='' && scrrecall[1] != undefined) && (brandScr2 =="0" ||  brandScr2=='')) {
                                $('#sp_show_mpney').show();
                                scrFeedMoney = Math.round(scrrecall[1]*cer_real) ;
                                     
                                $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;
                                    
                            }

                            if ((scrrecall[2] !='' && scrrecall[2] != undefined) && (brandScr3 =='' || brandScr3 =="0") ) {
                                $('#sp_show_mpney').show();
                                scrFeedMoney = Math.round(scrrecall[2]*cer_real) ;
                                $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;
                                    
                            }
                                //品牌回饋代書
                              

                            var cou = scrpartsp.length;


                            if (scrpartsp[(cou-1)] !='' && scrpartsp[(cou-1)] != undefined) {
                                    
                                $('#sp_show_mpney').show();
                                scrFeedMoney = Math.round(scrpartsp[(cou-1)]*cer_real) ;
                                $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;
                            }

                                
                                
                                // if (scrFeedMoney == 0) {
                                //     $('#sp_show_mpney').show();
                                //     $('[name="cSpCaseFeedBackMoney"]').val(0) ;
                                // }

                                
                        }
                    }
                    // console.log(ownercheck+'_'+brand69);
                    

                       
                       ////////////////////////////////////////////////////////
                        if (scrFeedMoney == 0 || scrFeedMoney == '') {
                               
                            $('[name="cSpCaseFeedBackMoney"]').val(0) ;

                        }else{
                            $('#sp_show_mpney').show();
                        }

                        //如果有回饋給地政士 特殊回饋不回饋
                           var tg1 = $('[name="cFeedbackTarget"]:checked').val() ;
                            var tg2 = $('[name="cFeedbackTarget1"]:checked').val() ;
                            var tg3 = $('[name="cFeedbackTarget2"]:checked').val() ;
                            var ss = 0;
                        if (tg1 == 2 || tg2 == 2 || tg3 == 2) {
                            ss = 1;
                        }
                            
                        if ((scrpart == undefined || scrpart == 0 || scrpart == '') && casecheck == 0 && ss == 0) { 
                        //如果仲介品牌有回饋給地政士 特殊回饋不回饋
                            
                            SpRecall();
                        }
                        
                     $('[name="cCaseFeedBackModifier"]').val('') ;
                     $('[name="cCaseFeedBackModifyTime"]').val('') ;
                }
                    // OtherRecall('');//幸福家的卓英傑回饋(201706-15 作廢)
            }

            function OtherRecall(c){
                // console.log('GO');
                var brand = $("[name='realestate_brand']").val();

                var brand1 = $("[name='realestate_brand1']").val();

                var brand2 = $("[name='realestate_brand2']").val();

                // console.log('GO'+brand+'-brand1:'+brand1);
                var certifiedmoney = parseInt($("[name='income_certifiedmoney']").val());

                $.ajax({
                     url: '../includes/escrow/setOtherFeed.php',
                     type: 'POST',
                     dataType: 'html',
                     data: {'brand': brand,'brand1':brand1,'brand2':brand2,'cId':"<{$data_case.cCertifiedId}>",'c':c},
                 }).done(function(txt) {
                    // console.log(txt);
                   
                    var arr = txt.split(',');
                    if (arr[0] == 'del') {
                        
                        if (c=='1') {
                            for (var i = 1; i < arr.length; i++) {
                               
                                    delfeedmoney('',arr[i],'cat');
                                    $("#DOtherFeed"+arr[i]).remove();
                         
                            }
                        }else { //if("<{$is_edit}>" == 0)
                            $("#OtherFeedcopy0").attr('class','dis otherf');//dis otherf 
                            $("#OtherFeedcopy0 [name='newotherFeedCheck[]']").val('');
                        }    

                        
                       
                        
                    }else{
                        for (var i = 0; i < arr.length; i++) {
                            var no = parseInt($("[name='addOFeed']").val());
                            var arr2 = arr[i].split('_');
                            var ck = 0;
                            var ckId = '';


                            if (arr2[0] != '') {
                                
                                $(".newfeedcheckStore1").each(function() {
                                    
                                    // console.log($(this".newfeedcheckType").val());
                                    if ($(this).val() == arr2[0] ) {
                                        ck =1;
                                        ckId = $(this).attr('alt');
                                    }
                          
                                });
                                // console.log(ck+'_'+ckId);

                                if (ck == 0) {//未重複就新增
                                    addOtherFeed();

                                    $("#OtherFeedcopy"+no+" [name='newotherFeedstoreId"+no+"']").val(arr2[0]);
                                   
                                    $("#OtherFeedcopy"+no+" #newotherFeedMoney"+no).val(Math.round(arr2[1]*certifiedmoney));
                                }else{
                                    //alt newotherFeedMoney35 
                                    if ("<{$is_edit}>" == 0 || ckId == 0) { //save
                                        $("#OtherFeedcopy0").attr('class','otherf');//dis otherf 
                                        $("#newotherFeedMoney0").val(Math.round(arr2[1]*certifiedmoney));    
                                    }else{
                                        otherFeedCg(ckId);
                                        $("#otherFeedMoney"+ckId).val(Math.round(arr2[1]*certifiedmoney));
                                    }
                                    
                                    // console.log($("#otherFeedMoney"+ckId).val());newotherFeedMoney0
                                }

                                 // console.log(ck+$("#otherFeedMoney"+ckId).val());
                            }
                            
                            
                        };
                    }
                    
                    
                   

                 });

            }

            function SpRecall(){ //特殊回饋金

                var _total = parseInt($('[name="income_totalmoney"]').val()) ;  
                var brand = $("[name='realestate_brand']").val();
                var brand1 = $("[name='realestate_brand1']").val();
                var brand2 = $("[name='realestate_brand2']").val();
                var realty1 = $('[name="realestate_branchnum"]').val() ;
                var realty2 = $('[name="realestate_branchnum1"]').val() ;
                var realty3 = $('[name="realestate_branchnum2"]').val() ;
                var sSpRecall= "<{$scrivener_sSpRecall}>";
                var check = 0;
                var spMoney;
                var cMoney = parseFloat($('[name="income_certifiedmoney"]').val()) ;
                var branchRecall = parseFloat($('[name=realestate_bRecall]').val());
                var branchRecall1 = parseFloat($('[name=realestate_bRecall1]').val());
                var branchRecall2 = parseFloat($('[name=realestate_bRecall2]').val());
                var branchMoney = $("[name='cCaseFeedBackMoney']").val();
                var branchMoney1 = $("[name='cCaseFeedBackMoney1']").val();
                var branchMoney2 = $("[name='cCaseFeedBackMoney2']").val();
                var obj = jQuery.parseJSON($("[name='cScrivenerSpRecall2']").val());  //cScrivenerSpRecall2
                var branchcountCheck = 0;
                var brachMoneyCheck = 0;
                var scrSpRecall =new Array();
                // var obj = '<{$data_scrivener.sSpRecall2}>';      
                // console.log('T:'+obj);
                
                //確認是否非台屋、優美、非仲介成交
                if (brand != 1 && brand !=49 && brand !=2) {
                    check = 1;
                }else if (brand1 != 1 && brand1 !=49 && brand1 !=0 && brand1 !=2) {
                    check = 1;
                }else if (brand2 != 1 && brand2 !=49 && brand2 !=0 && brand2 !=2) {
                    check = 1;
                }
               
                if (brand1 > 0 && realty2 > 0) { //配件
                    branchcountCheck++;
                }

                  // console.log('E:'+$("[name='cScrivenerSpRecall2']").val());
                if ($("[name='cScrivenerSpRecall2']").val() != '') { //針對不同仲介比例的特殊回饋
                    //有特約+沒特約 配件，代書固定都是17%
                    //沒有符合比例的就空直(單筆)
                      
                    if (branchcountCheck == 0) { //單筆
                        
                        
                        $.each(obj, function(key, val) {
                            // console.log('D:'+branchRecall+'_'+$('[name=realestate_bRecall]').val());
                            if (parseFloat(key) == branchRecall) {
                                sSpRecall = val;
                                
                            }
                        });
                       // console.log('D'+branchRecall);

                    }else{ //配件
                        // console.log('C');
                        //檢查是否回饋
                        if (branchMoney > 0) {
                            brachMoneyCheck++;
                        }

                        if (branchMoney1 > 0) {
                            brachMoneyCheck++;
                        }
                        if (branchMoney2 > 0) {
                            brachMoneyCheck++;
                        }
                        var cc = 0;
                        $.each(obj, function(key, val) {
                            if (parseFloat(key) == branchRecall || parseFloat(key) == branchRecall1 || parseFloat(key) == branchRecall2) {
                                scrSpRecall[cc] = val;
                                cc++;
                            }
                            
                        });
                        // console.log(cc+'_'+branchMoney);
                        // 
                        scrSpRecall.sort(); //
                        // console.log(scrSpRecall);
                        // console.log('B:'+brachMoneyCheck);
                        if (brachMoneyCheck > 1) {

                            sSpRecall = scrSpRecall[0];
                            // console.log('C:'+cc);
                            if (brachMoneyCheck != cc) { //其中有一個沒有符合條件
                                sSpRecall = '';
                            }
                        }else{
                            // sSpRecall = scrSpRecall[(scrSpRecall.length-1)];
                            sSpRecall = 17;
                        }
                       
                    }
                     

                }else if (sSpRecall=='') {
                    sSpRecall= $("[name='sSpRecall']").val();
                }
                
                // console.log('A'+sSpRecall); //cSpCaseFeedBackMoneyMark
                
                
               // console.log('A'+sSpRecall);
                 
                if ( check == 1 && (sSpRecall != 0 || $("[name='cScrivenerSpRecall2']").val() != '')){
                    
                    
                                                // sSpRecall=sSpRecall/ 10000 ;
                    sSpRecall=sSpRecall/ 100; //20150121

                    spMoney= Math.round(cMoney * sSpRecall); //20150122
                            
                    $('#sp_show_mpney').show();
                    if (_total!=''){
                        $('[name="cSpCaseFeedBackMoney"]').val(spMoney) ;

                        if (sSpRecall == 0) {
                            $('[name="cSpCaseFeedBackMoneyMark"]').val('x');
                            $('[name="cSpCaseFeedBackMoney"]').val('') ;
                        }
                    }

                }else{
                    $('[name="cSpCaseFeedBackMoney"]').val('0') ;
                    $('#sp_show_mpney').hide();
                        
                }
                   
            }

            function checkfeed(){
                var cerMoney = $("[name='income_certifiedmoney']").val();
                var feed = $("[name='cCaseFeedBackMoney']").val();
                var feed1 = $("[name='cCaseFeedBackMoney1']").val();
                var feed2 = $("[name='cCaseFeedBackMoney2']").val();
                var feedsp = $("[name='cSpCaseFeedBackMoney']").val();

                //25 //8

                //配件加起來算，因配件目前是平分

                // if (true) {};

                $.ajax({
                    url: '../includes/escrow/checkFeedback.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'feed': feed,'feed1':feed1,'feed2':feed2,'feedsp':feedsp,'cCertifiedId':"<{$data_case.cCertifiedId}>"},
                })
                .done(function(txt) {
                    if (txt != 1) {
                        alert(txt);
                    }
                                
                });
                            

            }
            function setMark(){
                var check = $('[name="cSpCaseFeedBackMoneyMark"]').val();
                // console.log(check+"_"+$("[name='cSpCaseFeedBackMoney']").val());
                if (check == 'x') {
                    $('[name="cSpCaseFeedBackMoneyMark"]').val('o');
                }
            }
            ////
            
            /* 同步回饋金 radio */
            function sync_radio() {
                /*
                var _index = $('input:radio[name="cCaseFeedback"]:checked').val() ;
                if (_index == '0') {
                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;
                    $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>')
                }
                else {
                    $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                    $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>')
                }
                */
                $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>') ;
                $('[name="cCaseFeedBackModifyTime"]').val('time') ;
            }
           
            ////
            
            /* 建立多組買賣方 */
            function more(ch) {
                var url = 'buyerownerlist.php?iden=' + ch + '&cCertifyId=' + $('[name="case_bankaccount"]').val() ;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url}) ;
                //window.open(url,"BOL","width=1180px,height=800px,scrollbars=yes,location=no,menubar=no,status=yes,resizable=yes") ;
            }
            ////
            
            /* 利息分配編輯 */
            function int_arrange() {
                var url = 'int_dealing.php?cCertifiedId=' + $('[name="case_bankaccount"]').val() ;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
                      var id = $('[name="certifiedid"]').val();
                       $.ajax({
                           url: 'int_table.php',
                           type: 'POST',
                           dataType: 'html',
                           data: {'id': id},
                       })
                       .done(function(txt) {
                            $("#int_target").empty();
                            $("#int_target").html(txt);
                            $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                       });


                 }}) ;
            }
            ////
            
            /* 發票對象編輯 */
            function invoice_check() {
                var url = 'inv_dealing.php?cCertifiedId=' + $('[name="case_bankaccount"]').val() +"&cSignCategory=<{$data_case.cSignCategory}>" ;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
                      var id = $('[name="certifiedid"]').val();
                    //******************
                       $.ajax({
                           url: 'inv_table.php',
                           type: 'POST',
                           dataType: 'html',
                           data: {'id': id},
                       })
                       .done(function(txt) {
                            $("#invoice_target").empty();
                            $("#invoice_target").html(txt);
                            $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                       });


                 }}) ;

             
            }
            ////
            
            /* 仲介店輸入搜尋 */
            function branch_get() {
                var _brand = $('[name="realestate_brand"] option:selected').val() ;
                var _category = $('[name="realestate_branchcategory"] option:selected').val() ;
                var url = 'branch_get.php?' ;
                $.colorbox({href:url}) ;
            }
            ////
            
            /* 身份證字號查核以決定是否顯示非本國籍選單 */
            function checkID(tg) {
                if (tg == 'b') {        //買方
                    var chkInput = $('[name="buy_identifyid"]') ;
                    var _id = $('#bid') ;
                    var _cat = $('[name="buy_categoryidentify"]') ;
                }
                
                if (tg == 'o') {        //賣方
                    var chkInput = $('[name="owner_identifyid"]') ;
                    var _id = $('#oid') ;
                    var _cat = $('[name="owner_categoryidentify"]') ;
                }
                
                var str = chkInput.val() ;
                
                /* 自動選取身分類別 */
                if (str) {              
                    if (str.length == 8) {          //統一編號
                        _cat.get(1).checked = true ;
                    }
                    else {                          //身分證字號
                        _cat.get(0).checked = true ;
                    }
                }
                ////
                
                /* 檢核顯示外國人選項 */
                var pat = /[a-zA-Z]{2}/ ;
                if (pat.test(str)) {
                    $('#foreign'+tg).show() ;
                }
                else {
                    $('#foreign'+tg).hide() ;
                }
                ////
                
                /* 檢核身分證字號或統一編號合法性 */
                if (str) {
                    if (checkUID(str)) {
                        _id.html('<img src="/images/ok.png">') ;
                         
                    }
                    else {
                        _id.html('<img src="/images/ng.png">') ;
                    }
                }
                ////
            }
            ////
            
            /* 檢查身分證字號是否為外國人 */
            function checkSaveF(tg) {
                if (tg == 'b') {
                    var chkInput = $('[name="buy_identifyid"]') ;
                    var rd = $('[name="buyer_resident_limit"]:checked') ;
                }
                
                if (tg == 'o') {
                    var chkInput = $('[name="owner_identifyid"]') ;
                    var rd = $('[name="owner_resident_limit"]:checked') ;
                }
                
                var str = chkInput.val() ;
                var rsd = rd.val() ;
                var pat = /[a-zA-Z]{2}/ ;
                
                if (pat.test(str) && (rsd == undefined)) {
                    return true ;
                }
                else {
                    return false ;
                }   
            }
            ////

            function add(bid,num,target) {
                var url = 'bsales_list.php' ;
                var CertifiedId='<{$data_case.cCertifiedId}>';
                var sales = $('[name="option_sales'+num+'"] :selected').val() ;
            
                if (sales == '0') {
                    return false ;
                }
                else {
                    $.post(url,{'bid':bid,'sales':sales,'num':num,'act':'add','cid':CertifiedId,'target':target},function(txt) {
                        $('#salesList'+num).html(txt+'&nbsp;') ;
                        $('[name="option_sales'+num+'"]').val(0) ;
                        //alert('已新增!!') ;
                    }) ;
                }
            }
            function del(num,sales,bid) {
                var url = 'bsales_list.php' ;
                var CertifiedId='<{$data_case.cCertifiedId}>';
    
                $.post(url,{'num':num,'cid':CertifiedId,'sales':sales,'bid':bid,'act':'del'},function(txt) {
                    $('#salesList'+num).html(txt+'&nbsp;') ;
                    $('[name="option_sales"]'+num).val(0) ;
                    //alert('已刪除!!') ;
                }) ;
            }

           
            //刪除建物資料
            function del_build(item) { 
                var url = 'delete_build.php' ;
                var CertifiedId='<{$data_case.cCertifiedId}>';

                $("[name='ditem']").val(item);
            
                $("[name='form_build_del']").submit();
                
                // $.post(url,{'item':item,'cid':CertifiedId},function(txt) {
                   
                //    alert('已刪除!!');
                //     //alert('已刪除!!') ;
                // }) ;
            }
            function processing(step) {
                var _original = "<{$data_case.status}>" ;
                $('[name="cCaseProcessing"]').val(step) ;

                var today = new Date() ;
                var now_day = (today.getFullYear() - 1911) + '-' + (today.getMonth()+1) + '-' + today.getDate() ;

                if (step==6) {
                    // case_cEndDate
                     $('[name="case_cEndDate"]').val(now_day);
                }else if (step==1) 
                {
                    $('[name="case_status"]').val(2);
                    $('[name="case_cEndDate"]').val('');
                }
                else{
                     $('[name="case_cEndDate"]').val('');
                }

                for (var i = 1 ; i < 7 ; i ++) {
                    if (i <= step) {
                        $('#ps'+i).addClass('step_class') ;
                    }
                    else {
                        $('#ps'+i).removeClass('step_class') ;
                    }
                }
                
                var _last = $('[name="case_status"]').val() ;
                if (_last!='3') {
                    $('[name="case_status"]').children().each(function() {
                        if (step=='6') {
                            if ($(this).text()=="已結案") {
                                $(this).attr("selected","true") ;
                            }
                        }
                    }) ;
                }
                else {
                    $('[name="case_status"]').children().each(function() {
                        if (step!='6') {
                            if ($(this).val()==_original) {
                                $(this).attr("selected","true") ;
                            }
                        }
                    }) ;
                }

            }

            //計算總價金*6%>(買+賣)服務費
            function check_money () {

                var buy_tmp  = $("[name='expenditure_realestatemoney_buyer']").val();
                var owner_tmp = $("[name='expenditure_realestatemoney']").val();
                var sum = 0;
                var total ="<{$data_income.cTotalMoney}>"*0.06;
                    //   expenditure_realestatemoney
                buy_tmp = buy_tmp.replace(',','');
                owner_tmp = owner_tmp.replace(',','');

                sum = parseInt(buy_tmp)+parseInt(owner_tmp);

                // alert(buy_tmp+'+'+owner_tmp);
                
                if (total<sum) {
                        alert('服務費大於總價金的6%');
                }
                
            }

            //動態新增帳戶
            function addBankList(type){

                if (type == 'buyer') {
                    var no = parseInt($("[name='buyer_bank_count']").val());
                    if (no < 2) { no = 2;}
                    // alert(no);
                    var no2 = no+1;
                    $("[name='buyer_bank_count']").val(no2);
                }else if (type == 'owner'){
                    var no = parseInt($("[name='owner_bank_count']").val());
                    if (no < 2) { no = 2;}
                    var no2 = no+1;
                    $("[name='owner_bank_count']").val(no2);
                }
                // alert(type);
               

                $('.'+type+'copy2').clone().insertAfter('.'+type+'copy'+no+':last').attr('class', ''+type+'copy'+no2+' del'+type+'copy');

                $('.'+type+'copy'+no2+' span[name="'+type+'text"]').text('('+no2+')');


                $('.'+type+'copy'+no2+' #'+type+'_bankkey2').attr('id', type+'_bankkey'+no2);
                $('.'+type+'copy'+no2+' #'+type+'_bankkey'+no2+'').attr('onchange', 'Bankchange("'+type+'",'+no2+')');
                $('.'+type+'copy'+no2+' #'+type+'_bankbranch2').attr('id', type+'_bankbranch'+no2);

                $('.'+type+'copy'+no2+' #'+type+'_bankaccnumber2').attr('id', type+'_bankaccnumber'+no2);
                $('.'+type+'copy'+no2+' #'+type+'_bankaccname2').attr('id', type+'_bankaccname'+no2);

                $('.'+type+'copy'+no2+' [name="'+type+'_bankkey2[]"]').attr('name', 'new'+type+'_bankkey2[]');
                $('.'+type+'copy'+no2+' [name="'+type+'_bankbranch2[]"]').attr('name', 'new'+type+'_bankbranch2[]');
                $('.'+type+'copy'+no2+' [name="'+type+'_bankaccnumber2[]"]').attr('name', 'new'+type+'_bankaccnumber2[]');
                $('.'+type+'copy'+no2+' [name="'+type+'_bankaccname2[]"]').attr('name', 'new'+type+'_bankaccname2[]');
                $('.'+type+'copy'+no2+' [name="'+type+'_bankid2[]"]').attr('name', 'new'+type+'_bankid2[]');
                
                
                $('.'+type+'copy'+no2+' input' ).val('');
                $('.'+type+'copy'+no2+' select' ).val('');
                
            }

            function Bankchange(type,no){
                //buyer_bankkey1

                GetBankBranchList($('#'+type+'_bankkey'+no+''),
                                        $('#'+type+'_bankbranch'+no+''),
                                        null);
            }
             
            function addOtherFeed(){

                var no = parseInt($("[name='addOFeed']").val());
                
                if (no == 0) { //
                    $("#OtherFeedcopy0").attr('class', 'otherf');
                    $("[name='newotherFeedCheck[]']").val(1);
                }else{
                    $("[name='newotherFeedCheck[]']").val(1);
                     $("#OtherFeedcopy0").clone().insertAfter(".otherf:last").attr({'id': 'OtherFeedcopy'+no,'class': 'otherf'});
                     var one = $("[name='newotherFeedType0']:checked").val();
                     // console.log(one);
                    $("#OtherFeedcopy"+no+" [name='newotherFeedType0']").attr({
                        'name': 'newotherFeedType'+no,
                        'onClick': "ChangeFeedStore('new','"+no+"')"
                    });

                    if (one == 1) {
                        $("[name='newotherFeedType0']").filter('[value="1"]').attr('checked',true);
                    }else{
                        $("[name='newotherFeedType0']").filter('[value="2"]').attr('checked',true);
                    }

                    $("#OtherFeedcopy"+no+" #OtherFeedDel0").attr({'id':'OtherFeedDel'+no,'onClick':'delfeedmoney("new","OtherFeedcopy'+no+'","")'});
                   
                    $("#OtherFeedcopy"+no+" [name='newotherFeedstoreId0']").attr({'name': 'newotherFeedstoreId'+no});
                    $("#OtherFeedcopy"+no+" #newotherFeedMoney0").val('');
                    $("#OtherFeedcopy"+no+" #newotherFeedMoney0").attr('id', 'newotherFeedMoney'+no);
                    // newotherFeedMoney0
                }

                $("[name='addOFeed']").val((no+1));
                
            }
            function ChangeFeedStore(cat,i){
                if (cat == '') {
                     otherFeedCg(i);
                }
               
                var type = $("[name='"+cat+"otherFeedType"+i+"']:checked").val();
                $("[name='"+cat+"otherFeedstoreId"+i+"']").attr('class', 'newfeedcheckStore'+type);

                $.ajax({
                    url: '../includes/escrow/feedBackMoneyAjax.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'type': type,'act':'st'},
                })
                .done(function(txt) {
                    // console.log(txt);
                    $("[name='"+cat+"otherFeedstoreId"+i+"'] option").remove();
                    $("[name='"+cat+"otherFeedstoreId"+i+"']").html(txt);
                });
                
            }
            function otherFeedCg(i){
                $("#otherFeedCheck"+i).attr('value', '1');
            }
            function delfeedmoney(type,tg,cat){
                if (cat == '') {
                    if (confirm('確認是否要刪除?')) {
                        var no = parseInt($("[name='addOFeed']").val());
                        if (type == 'new') {
                            if (tg != 'OtherFeedcopy0') {
                                $("#"+tg).remove();
                                if (no > 0) {
                                     no = no-1;
                                     $("[name='addOFeed']").val(no);
                                }
                               
                            }else{
                                $("#"+tg).attr('class', 'dis');
                                $("[name='addOFeed']").val(0);
                            }
                            
                        }else{
                             $.ajax({
                                url: '../includes/escrow/feedBackMoneyAjax.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {'type': type,'act':'del','id':tg},
                            })
                            .done(function(txt) {
                               if (cat == '') {
                                alert('刪除成功');
                                $("#reload").submit();
                               }
                               
                               
                            });
                            
                        }
                    }else{
                        return false;
                    }
                }else{
                    
                }
           
            }
            function AddServiceMsg(){
                var cid = $("#reload [name='id']").val();
                var date = $("[name='service_date']").val();
                var hour = $("[name='service_hour']").val();
                var min = $("[name='service_minute']").val();
                var man = $("[name='service_undertaker']").val();
                var note = $("[name='service_note']").val();

                if (cid == '') {
                    alert('請先建立合約書後再新增客服紀錄內容');
                    return false;
                }

                if(note==''){
                     alert('請輸入服務內容');
                    return false;
                }

                $.ajax({
                    url: '../includes/escrow/service_msg.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cid':cid,'date':date,'hour':hour,'min':min,'man':man,'note':note,'type':'add'},
                }).done(function(txt) {
                    //ser_msg
                    $(".ser_msg").html('');
                    $(".ser_msg").html(txt);
                    
                });
         
            }
            function DelServiceMsg(id){
                var cid = $("#reload [name='id']").val();
                 $.ajax({
                    url: '../includes/escrow/service_msg.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cid':cid,'id':id,'type':'del'},
                }).done(function(txt) {
                    //ser_msg
                    $(".ser_msg").html('');
                    $(".ser_msg").html(txt);
                    
                });
            }
            function ser_show(){
                //
                var val = $('[name="ser"]').val();
                if (val == 1) {
                    $(".ser_msg2").hide("slow");
                    $('[name="ser"]').val(0);
                }else{

                    $(".ser_msg2").show("slow");
                    $('[name="ser"]').val(1);
                }

            }
            function build_age(name,name2){
                var val = $("[name='"+name+"']").val();
                var arr =  val.split("-");
                var Today=new Date();
                var year = Today.getFullYear();
                var age = 0;

                if (val == '' || val =='000-00-00') {
                    $("[name='"+name2+"']").val(age);
                    return false;
                }

                arr[0] = parseInt(arr[0])+1911;

                age = year-arr[0];

                $("[name='"+name2+"']").val(age);
               
            }

            function OwnerMoney(){
                var val1 = $("[name='owner_money3']").val();
                var val2 = $("[name='owner_money4']").val();

                val1 = val1.replace(/\,/g, '');
                if ( !(/^[0-9]+$/).test(val1) ) {
                   val1 = 0;
                }

                val2 = val2.replace(/\,/g, '');
                if ( !(/^[0-9]+$/).test(val2) ) {
                   val2 = 0;
                }

                val1 = parseInt(val1);
                val2 = parseInt(val2);
                // console.log(val1+"_"+val2);


                $("[name='owner_money5']").val((val1+val2));

            }
			
			function newImg(b, c) {
				var url = "showcIdStamp.php?bId=" + b + "&cId=" + c ;
				window.open(url, "", config="scrollbars=yes,resizable=yes") ;
			}

            function download(cat,id){
                var cId = "<{$data_case.cCertifiedId}>";
                $('#print [name="cat"]').val(cat);
                $("#print").submit();
                
            }

            function checkCalTax(){
                $("[name='changeLand']").val(1);
                // $.ajax({
                //     url: '../includes/escrow/check_other.php',
                //     type: 'POST',
                //     dataType: 'html',
                //     data: {"type": 'AddedTaxMoney',"cid":"<{$data_case.cCertifiedId}>"},
                // })
                // .done(function(txt) {
                //     $("[name='income_addedtaxmoney']").val(txt);
                //     // console.log(txt);
                // });
                
            }
            function checkCertifiedId(){
                var url = 'id_conv_scr.php' ;
                var id = $('[name="checkcId"]').val() ;
                var check='<{$smarty.session.member_bankcheck}>';
                $.post(url,{'cid':id},function(txt) {
                    if (txt.match('ng')) { //
                        var arr = txt.split('_') ;
                        $('#showcheckId').html(arr[1]) ;
                        
                    }else{
                        var arr = txt.split('_') ;

                        $('#showcheckId').html(arr[3]+'&nbsp;<span style="color:#000080;font-weight:bold;">'+arr[2]+'</span>&nbsp;<span style="color:#FF0000;font-weight:bold;">'+arr[4]+'</span>') ;
                        if (arr[0] == 'ok') {
                            // console.log(arr[6]);
                            //
                            $("[name='case_bank']").val(arr[7]);
                            $("[name='checkScr']").val(arr[2]);
                            $("[name='scrivener_id']").val(arr[1]);
                           $('[name="checkcId2"]').val(arr[6]) ;
                           $("[name='certifiedid_view']").val(id);
                                    
                            //       // scrivener_bankaccount;
                            CatchScrivener(); 
                            feedback_money();
                        };
                       
                    }
                    // console.log(txt);
                }) ;
            }

            function casmsg(cat){
                var url = "contractNote.php?cCertifyId=<{$data_case.cCertifiedId}>&cat="+cat;
                // console.log(casmsg);
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
                    $.ajax({
                        url: 'contractNoteTable.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"cat": cat,"cId":"<{$data_case.cCertifiedId}>"},
                    })
                    .done(function(msg) {
                        // console.log("success");
                        $("#casmsg"+cat).html(msg);
                    });
                    
                }}) ;
            }

            function copyCase(){
                // var url = "copyCase.php";//作廢
                if ("<{$is_edit}>" == 0) {
                    var cid = $('[name="checkAcc"]', window.parent.document).val();
                }else{
                    var cid = $('[name="scrivener_bankaccount2"]', window.parent.document).val();
                }

                 var url = "copyCase.php?id="+cid+"&limit=<{$limit_show}>&edit=<{$is_edit}>";
                $.colorbox({iframe:true, width:"50%", height:"50%", href:url}) ;
            }

            function importData(cid){
                var zip = $("[name='property_zip0F']").val();
                var addr = $("[name='property_addr0']").val();
                var url = "ImportCaseData.php?id="+cid+"&limit=<{$limit_show}>&edit=<{$is_edit}>&zip="+zip+"&addr="+encodeURI(addr);

                $.colorbox({iframe:true, width:"50%", height:"50%", href:url}) ;
            }


            function getCustomer(iden,id){

                var check = 0;
                
               
                     if (iden == 'buy' && "<{$data_buyer.cIdentifyId}>" != '') {
                        check++;
                    }else if(iden == 'owner' && "<{$data_owner.cIdentifyId}>" != ''){
                        check++;
                    }

                    if (check > 0 && checkUID(id)) {
                        if (!confirm("已有資料存在，是否要取代?")) {
                            return false;
                        }
                    }
               

               
               
               $.ajax({
                   url: '../includes/escrow/getCustomer.php',
                   type: 'POST',
                   dataType: 'html',
                   data: {id: id,cId:"<{$data_case.cCertifiedId}>",iden:iden},
               })
               .done(function(msg) {
                    // console.log(msg);
                    var obj = JSON.parse(msg); //buy_name
                    // console.log(obj.msg);
                    if (obj.msg == 'ok') {
                        $('[name="'+iden+'_name"]').val(obj.name);
                        $('[name="'+iden+'_birthdayday"]').val(obj.birthday);

                        $('[name="'+iden+'_mobilenum"]').val(obj.mobile);

                        if (iden =='buy') {
                            iden = 'buyer';
                        };

                        
                        $('[name="'+iden+'_registcountry"]').val(obj.city);//buyer_registcountry

                        $('[name="'+iden+'_registarea"] option').remove() ; //buyer_registarea
                
                        $.post('listArea.php',{"city":obj.city},function(txt) {
                           
                            $('[name="'+iden+'_registarea"]').append(txt) ;
                            $('[name="'+iden+'_registarea"]').val(obj.zip);
                        }) ;

                        $('[name="'+iden+'_registaddr"]').val(obj.addr);//buyer_registaddr
                        $('[name="'+iden+'_registzip"]').val(obj.zip);//buyer_registzip
                        $('[name="'+iden+'_registzipF"]').val(obj.zip);

                        if ($('[name="'+iden+'_bank_count"]').val() > 2) {
                            $(".del"+iden+'copy').remove();
                            $('[name="'+iden+'_bank_count"]').val(1);
                        }

                       
                            $('#'+iden+'_bankkey2').val(0); //buyer_bankkey2
                            $('#'+iden+'_bankbranch2').val(0);
                            // GetBankBranchList($('#'+iden+'_bankkey2'),$('#'+iden+'_bankbranch2'),"0");//owner_bankbranch2
                            $('#'+iden+'_bankaccnumber2').val(''); //buyer_bankaccnumber2
                            $('#'+iden+'_bankaccname2').val('');
                        
                        
                            


                      
                        var count = 2;
                        for (var i = 0; i < obj.bank.length; i++) {
                         
                            if (i == 0) {

                                $('[name="'+iden+'_bankkey"]').val(obj.bank[i].bankcode); //buyer_bankkey
                                GetBankBranchList($('[name='+iden+'_bankkey]'),$('[name='+iden+'_bankbranch]'),obj.bank[i].bankbranch);
                                
                                $('[name="'+iden+'_bankaccnumber"]').val(obj.bank[i].bankaccnumber); //buyer_bankaccnumber
                                $('[name="'+iden+'_bankaccname"]').val(obj.bank[i].bankaccname); //buyer_bankaccname
                               
                            }else if(i == 1){
                                // console.log('#'+iden+'_bankkey'+count);
                                // console.log(obj.bank[i].bankcode+"_"+obj.bank[i].bankbranch);
                                $('#'+iden+'_bankkey'+count).val(obj.bank[i].bankcode); //buyer_bankkey2
                                GetBankBranchList($('#'+iden+'_bankkey'+count),$('#'+iden+'_bankbranch'+count),obj.bank[i].bankbranch);
                                $('#'+iden+'_bankaccnumber'+count).val(obj.bank[i].bankaccnumber); //buyer_bankaccnumber2
                                $('#'+iden+'_bankaccname'+count).val(obj.bank[i].bankaccname);

                            }else{
                                addBankList(iden);
                                count = $('[name="'+iden+'_bank_count"]').val();
                                $('#'+iden+'_bankkey'+count).val(obj.bank[i].bankcode);
                                GetBankBranchList($('#'+iden+'_bankkey'+count),$('#'+iden+'_bankbranch'+count),obj.bank[i].bankbranch);
                                $('#'+iden+'_bankaccname'+count).val(obj.bank[i].bankaccname); //buyer_bankaccnumber2 
                                $('#'+iden+'_bankaccnumber'+count).val(obj.bank[i].bankaccnumber);
                            }
                            
                        }

                        // $('[name="buyer_bankbranch"]').val('0163');
                        //
                    }
                    
               });
               
            }
        </script>
        <style type="text/css">
            .dis{
                display:none; 
            }
            #tabs {
                width:980px;
                margin-left:auto; 
                margin-right:auto;
            }
            
            /* 修復 jQuery UI Tabs CSS 衝突 */
            #tabs > ul {
                width: auto !important;
                height: auto !important;
                border: none !important;
                text-align: left !important;
                background: none !important;
            }
            
            #tabs > ul li {
                float: none !important;
                height: auto !important;
                line-height: normal !important;
                border: none !important;
                background: none !important;
                margin-bottom: 0 !important;
                position: static !important;
                overflow: visible !important;
                display: inline-block !important;
            }
            
            #tabs > ul li a {
                border: none !important;
                padding: 0.5em 1em !important;
                color: #000 !important;
                display: block !important;
                text-decoration: none !important;
            }
            
            #tabs > ul li a:hover {
                background: #ddd !important;
            }
            
            #tabs > ul li.ui-tabs-active,
            #tabs > ul li.ui-tabs-selected {
                background: #fff !important;
                border-bottom: none !important;
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
                /*width:120px;*/
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
                padding-right: 3px;
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
                font-weight:bold;
            }

            .tb-title2 {

                background: #E4BEB1;
                font-weight:bold;
            }
            .ser_msg td{
                border:1px solid #999;
                padding-top:3px; 
                padding-bottom:3px; 
            }

            .th_title_sml {
                font-size: 10px;
            }

            .sign-red{
                color: red;
            }
            
            input.bt4 {
                padding:4px 4px 1px 4px;
                vertical-align: middle;
                background: #F8EDEB;border:1px #727272 dotted;color:font-size:12px;margin-left:2px
            }
            input.bt4:hover {
                padding:4px 4px 1px 4px;
                vertical-align: middle;
                background:  #EBD1C8;border:1px #727272 dotted;font-size:12px;margin-left:2px;cursor:pointer
            }

            #trans_build  {

                 background: #FFAC55;

            }

            #trans_build:hover{
                      background: #FF8F19 ;
            }
            
            .ui-combobox {
                    position: relative;
                    display: inline-block;
            }
            .ui-combobox-toggle {
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    margin-left: -1px;
                    padding: 0;
                    /* adjust styles for IE 6/7 */
                    *height: 1.5em;
                    *top: 0.1em;
            }
            .ui-combobox-input {
                margin: 0;
                padding: 0.1em;
                width:160px;
            }
            .ui-autocomplete {
                width:160px;
                max-height: 300px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }

            .ui-autocomplete-input {
                width:120px;
            }
            .countrycode{
                width: 150px;
                font-size:9pt;

            }
            .step_class {
                    background-color: red;

                }
          #tabs-detail .detail_row td {
             border-width:1px ;
             border-style:solid ;
             border-color:#ccc ;
             font-size:12pt;
             padding-top:10px;
             padding-bottom:10px;
         } 
          #tabs-detail .detail_row th{
            text-align: center;
          }
            /* IE 6 doesn't support max-height
             * we use height instead, but this forces the menu to always be this tall
             */
            * html .ui-autocomplete {
                height: 150px;
            }

            fieldset {
                border-radius: 6px;
            }

            .ver{
                background-color: #f05033;
            }

            .ver2{
                background-color: #FFBA3B;
            }
            .btnD{
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
            .btnD:hover{
               color: #0000FF;
                font-size:16px;
                background-color: #FFFF96;
                border: 1px solid #FFFF96;
            }
        </style>
    </head>
    <body id="dt_example">
    <div id="test"></div>
        <div id="dialog-confirm11" title="編輯" style=" display:none;">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>請先填入保證證號或檢查詢保證證號是否正確?</p>
        </div>
         <form name="reload" id="reload" method="POST">
            <input type="hidden" name="id" value="<{$data_case.cCertifiedId}>" />
         </form>
        <form name="form_edit" id="form_edit" method="POST">
            <input type="hidden" name="id" id="id" value='3' />
        </form>
        <form name="form_add" id="form_add" method="POST">
        </form>
        <form name="form_build" id="form_build" method="POST" action='formlandedit.php'>
            <input type="hidden" name="id" value="<{$data_case.cCertifiedId}>" />
            <input type="hidden" name="bitem" value="" />
             <input type="hidden" name="cSignCategory" value="<{$data_case.cSignCategory}>">
        </form>
        <form name="form_land" id="form_land" method="POST" action='formland2edit.php'>
            <input type="hidden" name="id" value="<{$data_case.cCertifiedId}>" />
            <input type="hidden" name="cSignCategory" value="<{$data_case.cSignCategory}>" />
        </form>
        <form name="form_car" id="form_car" method="POST" action='formcaredit.php'>
            <input type="hidden" name="id" value="<{$data_case.cCertifiedId}>" />
            <input type="hidden" name="cSignCategory" value="<{$data_case.cSignCategory}>">
        </form>
        <form name="form_build_del" id="form_build_del" method="POST" action="delete_build.php">
            <input type="hidden" name="id" value="<{$data_case.cCertifiedId}>" />
            <input type="hidden" name="ditem" >
        </form>
  <!--        <form method="POST" name="myform" action="../bank/new/out1.php" target="_blank">
            <input type="hidden" name="vr" value="<{$data_case.cEscrowBankAccount}>">
            </form>  -->
        <form method="POST" name="form_fee" >
            <input type="hidden" name="cid"  />
            <input type="hidden" name="type" >
            <input type="hidden" name="cat" >
           
            <input type="hidden" name="brand">
           
        </form>

        <form action="process.php" id="print" method="POST" target="_blank">
            <input type="hidden" name="cCertifiedId" value="<{$data_case.cCertifiedId}>">
            <input type="hidden" name="cat" >
        </form>
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
                                    <td colspan="3" align="right"><h1>歡迎登入 第一建經履保價金系統 官網網址：<a href="http://www.first1.com.tw">www.first1.com.tw</a> 今天是 <{$smarty.now|date_format:"%Y-%m-%d (%A)"}></h1></td>
                                </tr>
                                <tr>
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('/bank/create.php', '_blank', config='height=100,width=650');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
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
                        <td></td>
                    </tr>
                </table>
            </div>
            <div id="content" >
            <div id="show"></div>
                <div class="abgne_tab">
                    <{include file='menu1.inc.tpl'}>
                    <div class="tab_container">
                            <div id="menu-lv2"></div>
                            <br/>                           
                    
                            <form name='form_case'>
                            <div id="tabs">
                                <ul>
                                    <li><a href="#tabs-detail">案件明細表</a></li>
                                    <li><a href="#tabs-contract">合約書</a></li>
                                    <li><a href="#tabs-land">土地</a></li>
                                    <li><a href="#tabs-build">建物</a></li>
                                    <li><a href="#tabs-scrivener">地政士</a></li>
                                    <li><a href="#tabs-realty">仲介</a></li>
                                    
                                    <{if $is_edit == '1'}>                  
                                        <li><a href="#tabs-buyer" id="li-buyer">買方</a></li>
                                        <li><a href="#tabs-owner" id="li-owner">賣方</a></li>
                                                        
                                        <{if $smarty.session.pBusinessView == '1' || $smarty.session.pBusinessEdit == '1'}>
                                            <li><a href="#tabs-sales">業務管理</a></li> 
                                        <{/if}>
                                    <{else}>
                                            <li><a href="">買方</a></li>
                                            <li><a href="">賣方</a>
                                        <{if $smarty.session.pBusinessView == '1' || $smarty.session.pBusinessEdit == '1'}>
                                            <li><a href="">業務管理</a></li>
                                        <{/if}>
                                    <{/if}>
                                    
                                    <li><a href="#tabs-backmoney">回饋金</a></li>
                                   
                                </ul>
                                <div id="tabs-detail">
                                    <table  width="100%"  border="0">
                                        <tr>
                                            <td colspan="7" class="tb-title">
                                                客服紀錄
                                                <div style="float:right;padding-right:10px;">
                                                   
                                                <a href="javascript:void(0)" onclick="ser_show()">縮放</a>&nbsp;
                                                <input type="hidden" name="ser" value="1">
                                                </div>
                                            </td>                                     
                                        </tr>
                                    </table>
                                    <table  width="100%"  border="0" class="ser_msg2">
                                        <tr>
                                            <td colspan="7" class="ser_msg">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0"> 
                                                    <tr >
                                                        <td width="5%" align="center" class="tb-title2">序號</td>
                                                        <td width="20%" align="left" class="tb-title2">日期/時間</td>
                                                        <td width="10%" align="left" class="tb-title2">承辦</td>
                                                        <td width="60%"align="left" class="tb-title2">內容</td>
                                                        <td width="5%"align="center" class="tb-title2">刪除</td>
                                                    </tr>
                                                    <{foreach from=$data_service key=key item=item}>
                                                   <tr>
                                                       <td width="5%" align="center"><{$item.no}></td>
                                                       <td width="20%"><{$item.cDateTime}></td>
                                                       <td width="10%"><{$item.cName}></td>
                                                       <td width="60%"><{$item.cNote}></td>
                                                       <td width="5%" align="center">
                                                            <a href="javascript:void(0)" onclick="DelServiceMsg(<{$item.cId}>)">刪除</a>
                                                        </td>
                                                   </tr>
                                                    <{/foreach}>
                                     
                                                </table>
                                            </td>
                                            
                                        </tr>
                                        <tr><td colspan="7"><hr></td></tr>
                                        <tr>
                                            <!-- <th width="10%">日期/時間</th> -->
                                           <!--  <td width="25%">
                                                <input type="text" name="service_date" class="datepickerROC" style="width:80px">
                                               <select  style="width:50px;" name="service_hour">
                                                    <option value="00">00</option>
                                                    <{$saMend = 23}>
                                                    <{for $sa=1 to $saMend }>
                                                    <option value="<{$sa|string_format:"%02d"}>"><{$sa|string_format:"%02d"}></option>
                                                    <{/for}>
                                                </select>點
                                                <select  style="width:50px;" name="service_minute">
                                                     <option value="00">00</option>
                                                    <{$saMend = 59}>
                                                    <{for $sa=1 to $saMend }>
                                                    <option value="<{$sa|string_format:"%02d"}>"><{$sa|string_format:"%02d"}></option>
                                                    <{/for}>
                                                </select>分
                                                
                                            </td>
                                            <th width="5%">承辦</th>
                                            <td width="10%"><{html_options name=service_undertaker options=$menu_Undertaker}></td> -->
                                            <th width="15%">客服內容</th>
                                            <td width="85%">
                                                <input type="text" name="service_note" style="width:90%">
                                                <input type="button" value="新增" onclick="AddServiceMsg()">
                                            </td>
                                        </tr>
                                    </table>
                                    <table  width="100%"  border="0">
                                        <tr>
                                            <td colspan="7" class="tb-title">進度圖</td>                                         
                                        </tr>
                                        <tr>
                                            <th width="">進度圖</th>
                                            <{$processing}>
                                        </tr>
                                        <tr align="center">
                                            <th>步驟</th>
                                            <td width="14%">簽約</td>
                                            <td width="14%">用印</td>
                                            <td width="14%">完稅</td>
                                            <td width="14%">過戶</td>
                                            <td width="14%">代償</td>
                                            <td width="14%">點交(結案)</td>
                                        </tr>
                                         
                                    </table>
                                    <table width="100%"  border="0">
                                        <tr><td colspan="4" class="tb-title">基本資料</td></tr>
                                       <tr>
                                           <th width="20%">承辦人︰</th>
                                           <td><{$undertaker}></td>
                                           <th width="20%">地政士︰</th>
                                           <td><{$ScrivenerName}></td>
                                       </tr> 
                                       <tr>
                                           <th>案件狀態︰</th>
                                           <td><{$cCaseStatus}></td>
                                           <th>簽約日期︰</th>
                                           <td>
                                                <{if $data_case.cSignDate !='0000-00-00 00:00:00'}>
                                                    <{$data_case.cSignDate}>
                                                <{/if}>
                                                
                                            </td>
                                       </tr>
                                       <tr>
                                           <th>保證號碼︰</th>
                                           <td><{$data_case.cCertifiedId}></td>
                                           <th>專屬帳號︰</th>
                                           <td><{$data_case.cEscrowBankAccount}></td>
                                       </tr>
                                       <tr>
                                            <th>賣方姓名︰</th>
                                            <td>
                                                <{$data_owner.cName}>
                                            <{if $data_owner.count > 1}>
                                                等<{$data_owner.count}>人
                                            <{/if}>
                                            </td>
                                            <th>賣方ID︰</th>
                                            <td><{$data_owner.cIdentifyId}></td>
                                        </tr>
                                        <tr>
                                            <th>賣方代理人︰</th>
                                            <td colspan="3">
                                            <{foreach from=$data_owner1 key=key item=item}>
                                                <{$item.cName}>,
                                            <{/foreach}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>買方姓名︰</th>
                                            <td>
                                                <{$data_buyer.cName}>
                                                <{if $data_buyer.count > 1}>
                                                    等<{$data_buyer.count}>人
                                                <{/if}>
                                            </td>
                                            <th>買方ID︰</th>
                                            <td><{$data_buyer.cIdentifyId}></td>
                                        </tr>
                                        <tr>
                                            <th>買方代理人︰</th>
                                            <td >
                                             <{foreach from=$data_buyer1 key=key item=item}>
                                                <{$item.cName}>,
                                             <{/foreach}>
                                                
                                            </td>
                                            <th>登記名義人</th>
                                            <td><{foreach from=$data_buyer5 key=key item=item}>
                                                <{$item.cName}>,
                                             <{/foreach}></td>
                                        </tr>
                                        <tr>
                                            <th>授權對象︰</th>
                                            <td><{$data_buyer.cAuthorized}></td>
                                        </tr>
                                        <tr>
                                            <th>仲介品牌︰</th>
                                            <td><{$brand_type1}></td>
                                            <th>仲介店名︰</th>
                                            <td><{$branch_type1}></td>
                                        </tr>
                                        <tr>
                                            <th>仲介品牌︰</th>
                                            <td><{$brand_type2}></td>
                                            <th>仲介店名︰</th>
                                            <td><{$branch_type2}></td>
                                        </tr>
                                        <tr>
                                            <th>仲介品牌︰</th>
                                            <td><{$brand_type3}></td>
                                            <th>仲介店名︰</th>
                                            <td><{$branch_type3}></td>
                                        </tr>
                                         <{foreach from=$data_property key=key item=item}>
                                        <tr>
                                            <th>標的物座落︰</th>
                                            <td colspan="3"><{$item.cAddr_country}><{$item.cAddr}> </td>
                                        </tr>
                                        <{/foreach}>
                                        <tr>
                                            <td colspan="4" class="tb-title">各期價款</td>
                                        </tr>
                                        <tr>
                                            <th>簽約款︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cSignMoney}></span></td>
                                            <th>用印款︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cAffixMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <th>完稅款︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cDutyMoney}></span></td>
                                            <th>尾款︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cEstimatedMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <th>買賣總價金︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cTotalMoney}></span></td>
                                            <th>保證費金額︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_income.cCertifiedMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" class="tb-title">代收款項(買方)</td>
                                            <td colspan="2" class="tb-title">代收款項(賣方)</td>
                                        </tr>
                                        <tr>
                                            <th>應付仲介費總額︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cRealestateMoneyBuyer}></span></td>
                                            <th>應付仲介費總額︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cRealestateMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <th>先行收受仲介費︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cAdvanceMoneyBuyer}></span></td>
                                            <th>先行收受仲介費︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cAdvanceMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <th>應付仲介費餘額︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cDealMoneyBuyer}></span></td>
                                            <th>應付仲介費餘額︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cDealMoney}></span></td>
                                        </tr>
                                        <tr>
                                            <th>地政士費︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cScrivenerMoneyBuyer}></span></td>
                                            <th>地政士費︰</th>
                                            <td><span class="currency-money1 text-right"><{$data_expenditure.cScrivenerMoney}></span></td>
                                        </tr>
                                    </table>
                                    <div id="tran_show">
                                        <table  width="100%"  class="detail_row" cellspacing="0">
                                            <tr>
                                                <td colspan="7" class="tb-title">帳務收支明細</td>                                         
                                            </tr>
                                            <tr>
                                                <th style="width:110px;" >日期</th>
                                                <th style="width:150px;">帳款摘要</th>
                                                <th style="width:110px;">收入</th>
                                                <th style="width:90px;">支出</th>
                                                <th style="width:90px;">小計</th>
                                                <th style="width:170px;" >備註</th>
                                                <!-- <th style="width:50px;" >不顯示<br>在官網</th> -->
                                              
                                            </tr>
                                            <{$tbl}>
                                            <tr >
                                                <th style="width:110px;" colspan="7">&nbsp;</th>
                                              
                                            </tr>
                                            <tr style="background-color:#FFFFFF;">
                                                <td>&nbsp;</td>
                                                <td style="text-align:right;">專戶收支餘額：</td>
                                                <td colspan="3" style="text-align:right;"><{$total}>&nbsp; 
                                                    <{if $minus_money > 0}>
                                                    <font color="red">(NT$<{$minus_money}>不可動用)</font>
                                                    <{/if}>
                                                </td>
                                                <td colspan="2">(收入-支出)&nbsp;</td>
                                                   
                                                
                                            </tr>
                                           
                                        </table>

                                    </div>
                                    <div style="background-color:#FFFFFF;text-align:center;padding-bottom:10px;padding-top:10px;">
                                        <{if $is_edit == 1}>
                                            <{if $data_case.cSignCategory != 2}>
                                                <input type="button" id="trans_build" value="出款建檔" class="trans_build" >

                                            <{/if}>
                                        <{/if}>
                                    </div>
                                    <input type="hidden" name="cCaseProcessing" value="<{$data_case.cCaseProcessing}>">
                                   

                                </div>
                                <div id="tabs-contract">
                                    <input type="hidden" name="is_edit" value="<{$is_edit}>">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                            <td width="14%"></td>
                                            <td width="19%"></td>
                                        </tr>
                                        <{if $is_edit == 0}>
                                        <tr>
                                            <th>檢查號碼</th>
                                            <td ><input type="text" name="checkcId" id="" onkeyup="checkCertifiedId()" maxlength="9"><input type="hidden" name="checkcId2"></td>
                                            <td colspan="4" id="showcheckId"></td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <th colspan="6">
                                                &nbsp;
                                            </th>
                                        </tr>
                                        <tr>
                                            <th>銀行別︰</th>
                                            <td>
                                                <{if $is_edit == 1}>
                                                <{html_options name=case_bank options=$menu_categorybank_twhg selected=$data_case.cBank disabled="disabled"}>
                                                <{else}>
                                                     <{html_options name=case_bank options=$menu_categorybank_twhg selected=$data_case.cBank }>
                                                <{/if}>
                                            </td>
                                            <th>保證號碼︰</th>
                                            <td>
                                                <input type="hidden" name="certifiedid"  maxlength="10" value="<{$data_case.cCertifiedId}>" disabled="disabled"/>
                                                <input type="text" name="certifiedid_view"  maxlength="10" class="input-text-big" value="<{$data_case.cCertifiedId}>" disabled="disabled"/>
                                            </td>
                                            <th>委託書編號︰</th>
                                            <td>
                                                <input type="text" name="case_dealid" class="input-text-big" value="<{$data_case.cDealId}>" />
                                            </td>   
                                        </tr>
                                        <tr>
                                            <th>案件狀態︰</th>
                                            <td>
                                                <{if $limit_show == '0'}>
                                                    <{html_options name="case_status" onchange="chk_status()" options=$menu_statuscontract selected=$data_case.cCaseStatus}>
                                                <{else}>
                                                    <{html_options name="case_status" options=$menu_statuscontract selected=$data_case.cCaseStatus disabled="disabled"}>
                                                    <a hred="#" onclick="unlock()" style="font-size:9pt;cursor:pointer;display:none;">*解除案件狀態</a>
                                                <{/if}>
                                            </td>
                                            
                                            <th>實際點交日︰</th>
                                            <td>
                                                <{if $data_case.cEndDate == '0000-00-00 00:00:00' || $data_case.cCaseStatus==2}>
                                                    <input type="text" name="case_cEndDate" maxlength="10" class="input-text-big" value="" onclick="showdate(form_case.case_cEndDate)" readonly />
                                                <{else}>
                                                    <input type="text" name="case_cEndDate" maxlength="10" class="input-text-big" value="<{$data_case.cEndDate}>" onclick="showdate(form_case.case_cEndDate)" readonly/>
                                                <{/if}>
                                                <input type="hidden" name="check_End">
                                            </td>
                                            <th>異常原因︰</th>
                                            <td>
                                                <{html_options name="case_exception" options=$menu_categroyexception selected=$data_case.cExceptionStatus}>
                                            </td>     
                                        </tr>
                                        <tr>
                                            <th>簽約日期︰</th>
                                            <td>
                                            <{if $data_case.cSignDate == '0000-00-00 00:00:00' }>
                                                <input type="text" name="case_signdate" onclick="showdate(form_case.case_signdate)" maxlength="10" class="calender input-text-big" value="" />
                                            <{else}>
                                                <input type="text" name="case_signdate" onclick="showdate(form_case.case_signdate)" maxlength="10" class="calender input-text-big" value="<{$data_case.cSignDate}>" />
                                            <{/if}>
                                            </td>
                                             <th>預計點交日期︰</th>
                                            <td>
                                               <{if $data_case.cFinishDate2 == '0000-00-00' }>
                                                    <input type="text" name="case_finishdate2" onclick="showdate(form_case.case_finishdate2)" maxlength="10" class="calender input-text-big" value="" readonly/>
                                                <{else}>
                                                    <input type="text" name="case_finishdate2" onclick="showdate(form_case.case_finishdate2)" maxlength="10" class="calender input-text-big" value="<{$data_case.cFinishDate2}>" readonly />
                                                <{/if}>
                                            </td>
                                            <th>
                                                履保費出款日︰
                                            </th>
                                            <td>
                                                <input type="text"   maxlength="10" class="input-text-big" value="<{$CertifyDate}>" disabled />
                                            </td>
                                           <!-- <th>實際點交日期︰</th>
                                            <td>
                                               <{if $data_case.cFinishDate == '0000-00-00 00:00:00' }>
                                                    <input type="text" name="case_finishdate" onclick="showdate(form_case.case_finishdate)" maxlength="10" class="calender input-text-big" value="" readonly/>
                                                <{else}>
                                                    <input type="text" name="case_finishdate" onclick="showdate(form_case.case_finishdate)" maxlength="10" class="calender input-text-big" value="<{$data_case.cFinishDate}>" readonly />
                                                <{/if}>
                                            </td>          -->                                 
                                        </tr>
                                        <tr>
                                            <th>建檔日期︰</th>
                                            <td>
                                                <input type="text" name="case_applydate" maxlength="10" class="input-text-big" value="<{$data_case.cApplyDate}>" disabled="disabled" />
                                            </td>
                                            <th><span class="th_title_sml">第一期付款日期︰</span></th>
                                            <td>
                                                 <{if $data_case.cFirstDate == '0000-00-00' }>
                                                    <input type="text" name="case_firstdate" onclick="showdate(form_case.case_firstdate)" maxlength="10" class="calender input-text-big" value="" readonly/>
                                                <{else}>
                                                    <input type="text" name="case_firstdate" onclick="showdate(form_case.case_firstdate)" maxlength="10" class="calender input-text-big" value="<{$data_case.cFirstDate}>" readonly />
                                                <{/if}>
                                            </td>
                                            <th>用印日期︰</th>
                                            <td>
                                                <{if $data_case.cAffixDate == '0000-00-00' }>
                                                    <input type="text" name="case_affixdate" onclick="showdate(form_case.case_affixdate)" maxlength="10" class="calender input-text-big" value="" readonly/>
                                                <{else}>
                                                    <input type="text" name="case_affixdate" onclick="showdate(form_case.case_affixdate)" maxlength="10" class="calender input-text-big" value="<{$data_case.cAffixDate}>" readonly />
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                本買賣標的出租情形
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>出租情形︰</th>
                                            <td colspan="5">
                                            <{if $data_rent.cRent!='0'||$data_rent.cRentDate!=''||$data_rent.cFinish!='0'}>
                                                 <input type="radio" name="property_rentstatus" checked value="1"> 本買賣標的有出租情形 <input type="radio" name="property_rentstatus" value="2"> 本買賣標的無出租情形
                                             <{else}>
                                                <input type="radio" name="property_rentstatus" value="1"> 本買賣標的有出租情形 <input type="radio" name="property_rentstatus" checked value="2"> 本買賣標的無出租情形

                                            <{/if}>
                                            </td>   
                                        </tr>   
                                        <tr>
                                            
                                            <th>租期至︰</th>
                                            <td>
                                               <input type="text" maxlength="10" name="rent_rentdate" style="width:120px;" onclick="showdate(form_case.rent_rentdate)" class="calender input-text-big" value="<{$data_rent.cRentDate}>" readonly/>&nbsp;&nbsp;
                                               
                                            </td>
                                             <th>租金︰</th>
                                            <td><input type="text" name="rent_rent" value="<{$data_rent.cRent}>" style="width:120px;">元/月</td>
                                            <td> </td> 
                                            <td></td>
                                        </tr>
                                        <tr>

                                            <th>點交前︰</th> 
                                            <td colspan="5">
                                               <{html_radios name=rent_finish options=$property_finish selected=$data_rent.cFinish}>
                                            </td> 
                                        </tr>
                                        <{if $data_bankcode.bApplication==1 && ($data_bankcode.bCategory==1 || $data_bankcode.bCategory==3)}>
                                        <th>&nbsp;</th>
                                            <td colspan="5">
                                                租金分算以點交日為準或第三人佔用者，由
                                                <{html_radios name=rent_cOther options=$property_other selected=$data_rent.cOther}>於點交前排除之
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                契約書
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>契約書</th>
                                            <td colspan="5">

                                                <{if $data_bankcode.bApplication==1}>
                                                土地買賣契約
                                                <{else if $data_bankcode.bApplication==2}>
                                                房地買賣契約
                                                <{else if $data_bankcode.bApplication==3}>
                                                    預售屋權利買賣
                                                    <input type="hidden" name="contract_sale" value="1">
                                                <{/if}>
                                                <!-- (
                                            <{if $data_case.cOnSales == 1}>
                                                <input type="checkbox" name="contract_sale" value="1" checked>預售屋權利買賣
                                            <{else}>
                                                <input type="checkbox" name="contract_sale" value="1" >預售屋權利買賣
                                            <{/if}>) -->
                                            </td>
                                        </tr>
                                        <tr>

                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                版本
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>版本</th>
                                             <td colspan="5">
                                                                                            
                                                <{if $data_bankcode.bBrand==1 && $data_bankcode.bCategory==2 }>                                                    
                                                    台灣房屋直營店 &nbsp;&nbsp;&nbsp;
                                                   
                                                <{elseif $data_bankcode.bBrand==1 && $data_bankcode.bCategory==1}>
                                                    台灣房屋加盟店
                                                <{elseif $data_bankcode.bBrand==2 && $data_bankcode.bCategory==3 }>
                                                    第一建經(其他品牌或非仲介成交)
                                                <{/if}>
                                                
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                停車位
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>車位︰</th>
                                            <td>
                                                <{html_radios name=property_hascar options=$menu_categorycar selected=$parking separator=' '}>
                                                <input type="button" onclick="car_edit()" value="停車位標示" class="bt4" style="display:;width:100px;height:40px;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                增建或占用部分
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td colspan="5">
                                                <{if $check_object !=''}>
                                                    <input type="radio" name="PropertyObject"> 本買賣標的建物無增建部分<br><br>
                                                    <input type="radio" name="PropertyObject" checked> 本買賣標的建物有增建部分 
                                                <{else}>
                                                    <input type="radio" name="PropertyObject" checked> 本買賣標的建物無增建部分<br><br>
                                                    <input type="radio" name="PropertyObject"> 本買賣標的建物有增建部分 
                                                <{/if}>
                                                <{html_checkboxes name="property_cPropertyObject" options=$object_option selected=$data_property.cPropertyObject}>
                                                <input type="text" name="property_cObjectOther" value="<{$data_property.cObjectOther}>" size="5px">
                                            </td>  
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                給付方式
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>現金</th>
                                            <td>
                                                <input type="text" name="income_paycash" maxlength="16" size="12" value="<{$data_income.cPayCash}>" class="currency-money1 text-right">元整
                                            </td>  
                                            <th>支票</th>
                                            <td>
                                                <input type="text" name="income_ticket" maxlength="16" size="12" value="<{$data_income.cPayTicket}>" class="currency-money1 text-right">元整
                                            </td>
                                            <th>商業本票</th>
                                            <td><input type="text" name="income_paycommercialpaper" maxlength="16" size="12" value="<{$data_income.cPayCommercialPaper}>" class="currency-money1 text-right">元整</td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                各期價款
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>簽約款︰</th>
                                            <td>
                                                NT$<input name="income_signmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose" value="<{$data_income.cSignMoney}>" />元
                                            </td>
                                            <th>用印款︰</th>
                                            <td>
                                                NT$<input name="income_affixmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose" value="<{$data_income.cAffixMoney}>" />元
                                            </td>
                                            <th>完稅款︰</th> 
                                            <td>
                                                NT$<input name="income_dutymoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose" value="<{$data_income.cDutyMoney}>" />元
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>尾款︰</th>
                                            <td>
                                                NT$<input name="income_estimatedmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose" value="<{$data_income.cEstimatedMoney}>" />元
                                            </td>
                                            <th class="th_title_sml"><div id="unfo" tabindex="-1"></div>降保(履保費要少收才需填入金額)</th>
                                            <td>NT$<input name="income_firstmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose" value="<{$data_income.cFirstMoney}>" />元</td>
                                            <th>保證費金額︰</th> 
                                            <td>
                                                NT$<input name="income_certifiedmoney" type="text" maxlength="16" class="feedbackClose" size="12" style="text-align:right;"<{$certifiedchg}>  value="<{$data_income.cCertifiedMoney}>"/>元
                                            </td>
                                           
                                        </tr>
                                        <tr>
                                            <th>買賣總價金︰</th>
                                            <td colspan="3" >
                                                NT$<input name="income_totalmoney" type="text" maxlength="16" style="text-align:right;" size="12" value="<{$data_income.cTotalMoney}>" disabled='disabled' class="feedbackClose"/>元 
                                                (含車位價款<input name="income_parking" type="text" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.cParking}>" />元，如未分開計價者免填)
                                            </td>
                                             <!-- <td></td>
                                            <td></td> -->
                                            <th>未入專戶︰</th>
                                            <td>
                                                NT$<input name="income_nointomoney" type="text" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.cNotIntoMoney}>"/>元
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>一般增值稅︰</th>
                                            <td>
                                                NT$<input name="income_addedtaxmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right" value="<{$data_income.cAddedTaxMoney}>"/>元
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                開發票資訊
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>對象︰</th> 
                                            <td colspan="5">
                                                <div id="invoice_target" style="padding-left:5px;">
                                                    <div style="border:1px solid #E8EcE9;"> 
                                                        <table width="100%"  cellpadding="1" cellspacing="1" >
                                                            <tr >
                                                                <td colspan="5" bgcolor="#E8C1B4"> 賣方 NT$<span id="invoice_invoiceowner" class="currency-money1 text-right"><{$data_invoice.cInvoiceOwner}></span>元 
                                                                      <input type="hidden" name="invoice_invoiceowner" value="<{$data_invoice.cInvoiceOwner}>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
                                                                <td width="15%" class="tb-title2 th_title_sml">金額</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
                                                                <td align="left" class="tb-title2 th_title_sml">指定對象</td>
                                                            </tr>
                                                            <tr class="th_title_sml">
                                                                <td ><{$data_owner.cName}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_owner1" class="currency-money1 text-right"><{$data_owner.cInvoiceMoney}></span>元
                                                                    <input type="hidden" class="inv_show_owner" name="inv_show_owner1" value="<{$data_owner.cInvoiceMoney}>">
                                                                </td>
                                                                <td>
                                                                    <span id="owner_invdonate1">
                                                                    <{if $data_owner.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <{if $data_owner.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                                </td>
                                                                <td>
                                                                    <{assign var='j' value=$owner_other_count}>

                                                                    <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractOwner'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_owner<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="owner_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{assign var='i' value='2'}>
                                                            <{foreach from=$data_owner_other key=key item=item}>
                                                            <{assign var='id' value=$item.cId}>
                                                            <tr>
                                                                <td><{$item.cName}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_owner<{$i}>" class="currency-money1 text-right">NT$<{$item.cInvoiceMoney}></span>元 
                                                                    <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$i}>" value="<{$item.cInvoiceMoney}>">
                                                                </td>
                                                                <td> <span id="owner_invdonate<{$i++}>">
                                                                    <{if $item.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                                </td>
                                                                <td>
                                                                    <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractOthersO' && $item.cTBId ==$id}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_owner<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="owner_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{/foreach}>
                                                        </table>
                                                    </div>
                                                    <div style="border:1px solid #E8EcE9;">
                                                        <table width="100%"  cellpadding="1" cellspacing="1" >
                                                            <tr >
                                                                <td colspan="5" bgcolor="#E8C1B4"> 買方 NT$<span id="invoice_invoicebuyer" class="currency-money1 text-right"><{$data_invoice.cInvoiceBuyer}></span>元
                                                            <input type="hidden" name="invoice_invoicebuyer" value="<{$data_invoice.cInvoiceBuyer}>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
                                                                <td width="15%" class="tb-title2 th_title_sml">金額</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
                                                                <td align="left" class="tb-title2 th_title_sml">指定對象</td>
                                                            </tr>
                                                            <tr class="th_title_sml">
                                                                <td ><{$data_buyer.cName}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_buyer1" class="currency-money1 text-right"><{$data_buyer.cInvoiceMoney}></span>元
                                                                    <input type="hidden" class="inv_show_buyer" name="inv_show_buyer1" value="<{$data_buyer.cInvoiceMoney}>">
                                                                </td>
                                                                <td>
                                                                    <span id="buyer_invdonate1">
                                                                    <{if $data_buyer.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <{if $data_buyer.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                                </td>
                                                                <td>
                                                                   <{assign var='j' value=$buyer_other_count}>

                                                                    <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractBuyer'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_buyer<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="buyer_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{assign var='i' value='2'}>
                                                            <{foreach from=$data_buyer_other key=key item=item}>
                                                            <{assign var='id' value=$item.cId}>
                                                            <tr>
                                                                <td><{$item.cName}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_buyer<{$i}>"  class="currency-money1 text-right">$<{$item.cInvoiceMoney}></span>元 
                                                                    <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$i}>" value="<{$item.cInvoiceMoney}>">
                                                                </td>
                                                                <td> <span id="buyer_invdonate<{$i++}>">
                                                                    <{if $item.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                                </td>
                                                                <td>
                                                                    <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractOthersB' && $item.cTBId ==$id}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_buyer<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_buyer" name="inv_show_buyer<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="buyer_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{/foreach}>
                                                        </table>

                                                    </div>
                                                    <div style="border:1px solid #E8EcE9;">
                                                        <table width="100%"  cellpadding="1" cellspacing="1" >
                                                        <tr >
                                                            <td colspan="5" bgcolor="#E8C1B4"> 仲介 NT$<span id="invoice_invoicerealestate" class="currency-money1 text-right"><{$data_invoice.cInvoiceRealestate}></span>元
                                                                <input type="hidden" name="invoice_invoicerealestate" value="<{$data_invoice.cInvoiceRealestate}>">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                                <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
                                                                <td width="15%" class="tb-title2 th_title_sml">金額</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
                                                                <td align="left" class="tb-title2 th_title_sml">指定對象</td>
                                                        </tr>
                                                        <{assign var='i' value='1'}>
                                                        <tr class="th_title_sml">
                                                            <td ><{$branch_type1}></td>
                                                            <td>
                                                               NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney}></span>元
                                                               <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney}>">
                                                            </td>
                                                            <td>
                                                               <span id="real_invdonate<{$i++}>">
                                                                <{if $data_realstate.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                <{/if}>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                    <{if $data_realstate.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                            </td>
                                                            <td>
                                                                <{assign var='j' value=$branch_count}>
                                                                <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractRealestate'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="real_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                <{/foreach}>                                                   
                                                            </td>
                                                        </tr>
                                                        <{if $branch_type2 !=''}>
                                                        <tr class="th_title_sml">
                                                            <td ><{$branch_type2}></td>
                                                            <td>
                                                                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney1}></span>元
                                                               <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney1}>">
                                                            </td>
                                                            <td>
                                                               <span id="real_invdonate<{$i++}>">
                                                                    <{if $data_realstate.cInvoiceDonate1==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                    <{if $data_realstate.cInvoicePrint1=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                            </td>
                                                            <td>
                                                                 <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractRealestate1'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="real_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                <{/foreach}>                                                        
                                                            </td>
                                                        </tr>
                                                        <{/if}>
                                                        <{if $branch_type3 !=''}>
                                                        <tr class="th_title_sml">
                                                            <td ><{$branch_type3}></td>
                                                            <td>
                                                                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney2}></span>元
                                                               <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney2}>">
                                                            </td>
                                                            <td>
                                                               <span id="real_invdonate<{$i++}>">
                                                                    <{if $data_realstate.cInvoiceDonate2==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                    <{if $data_realstate.cInvoicePrint2=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                            </td>
                                                            <td>
                                                                <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractRealestate2'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="real_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                <{/foreach}>                                                         
                                                            </td>
                                                        </tr>
                                                        <{/if}>  
                                                            
                                                        </table>
                                                    </div>
                                                    <div style="border:1px solid #E8EcE9;">
                                                        <table width="100%"  cellpadding="1" cellspacing="1" >
                                                        <tr>
                                                            <td colspan="5" bgcolor="#E8C1B4"> 地政士 NT$<span id="invoice_invoicescrivener" class="currency-money1 text-right"><{$data_invoice.cInvoiceScrivener}></span>元
                                                                <input type="hidden"  name="invoice_invoicescrivener" value="<{$data_invoice.cInvoiceScrivener}>">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                                <td width="25%" align="left" class="tb-title2 th_title_sml">姓名</td>
                                                                <td width="15%" class="tb-title2 th_title_sml">金額</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否捐贈</td>
                                                                <td width="10%" align="left" class="tb-title2 th_title_sml">是否列印</td>
                                                                <td align="left" class="tb-title2 th_title_sml">指定對象</td>
                                                        </tr>
                                                        <tr class="th_title_sml">
                                                            <td ><{$data_scrivener.sName}></td>
                                                            <td>
                                                                NT$<span id="inv_show_scrivener1" class="currency-money1 text-right"><{$data_scrivener.cInvoiceMoney}></span>元
                                                               <input type="hidden" class="inv_show_scrivener" name="inv_show_scrivener1" value="<{$data_scrivener.cInvoiceMoney}>">

                                                            </td>
                                                            <td>
                                                               <span id="scr_invdonate1">
                                                                    <{if $data_scrivener.cInvoiceDonate==1}>
                                                                        <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                    <{if $data_scrivener.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                    <{/if}>
                                                            </td>   
                                                            <td>
                                                                <{assign var='j' value='2'}>
                                                                <{foreach from=$data_invoice_another key=key item=item}>

                                                                    <{if $item.cDBName =='tContractScrivener'}>
                                                                         <{$item.cName}>
                                                                        NT$<span id="inv_show_scrivener<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                        <input type="hidden" class="inv_show_scrivener" name="inv_show_scrivener<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                        <span id="scr_invdonate<{$j++}>">
                                                                            <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        </span>
                                                                        ;
                                                                    <{/if}>
                                                                <{/foreach}>                                                     
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        </div>
                                                        <{if $data_invoice.cInvoiceOther !=0}>
                                                            <div>
                                                            捐創世基金會
                                                            NT$<span id="invoice_invoiceother" class="currency-money1 text-right"><{$data_invoice.cInvoiceOther}></span>元
                                                            <input type="hidden" name="invoice_invoiceother" value="<{$data_invoice.cInvoiceOther}>">
                                                            </div>
                                                        <{/if}>
                                                    
                                                </div>

                                                <input type="button" onclick="invoice_check()" style="font-size:11pt;margin-left:5px;" value="　編輯　" <{$add_disabled}>>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                利息分配資訊
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>利息分配︰</th>
                                            <td colspan="5" style="padding-left:5px;">

                                            <div id="int_target">
                                                <table width="100%"  cellpadding="1" cellspacing="1" >
                                                    <tr>
                                                        <td colspan="3"><div id="int_tag"><{$int_total}></div></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" bgcolor="#E8C1B4">賣方</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                        <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                        <td class="tb-title2 th_title_sml">指定對象</td>
                                                    </tr>
                                                    <tr class="th_title_sml">
                                                        <td><{$data_owner.cName}></td>
                                                        <td>NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_owner.cInterestMoney}></span>元</td>
                                                        <td>
                                                            <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractOwner'}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>
                                                     <{foreach from=$data_owner_other key=key item=item}>
                                                     <{assign var='id' value=$item.cId}>
                                                     <tr class="th_title_sml">
                                                        <td><{$item.cName}></td>
                                                        <td>
                                                            NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                        </td>
                                                        <td>
                                                            <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractOthersO' && $item.cTBId ==$id}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>  
                                                    </tr>                 
                                                    <{/foreach}>

                                                    <tr>
                                                        <td colspan="3" bgcolor="#E8C1B4">買方</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                        <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                        <td class="tb-title2 th_title_sml">指定對象</td>
                                                    </tr>
                                                    <tr class="th_title_sml">
                                                        <td><{$data_buyer.cName}></td>
                                                        <td>NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_buyer.cInterestMoney}></span>元</td>
                                                        <td>
                                                            <{foreach from=$data_int_another key=key item=item}>
                                                                
                                                                <{if $item.cDBName =='tContractBuyer' }>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>
                                                     <{foreach from=$data_buyer_other key=key item=item}>
                                                     <{assign var='id' value=$item.cId}>
                                                     <tr class="th_title_sml">
                                                        <td><{$item.cName}></td>
                                                        <td>
                                                            NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                        </td>
                                                        <td>
                                                            <{foreach from=$data_int_another key=key item=item}>
                                                                
                                                                <{if $item.cDBName =='tContractOthersB' && $item.cTBId ==$id}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>  
                                                    </tr>                 
                                                    <{/foreach}>
                                                    <tr>
                                                        <td colspan="3" bgcolor="#E8C1B4">仲介</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                        <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                        <td class="tb-title2 th_title_sml">指定對象</td>
                                                    </tr>
                                                    <tr class="th_title_sml">
                                                        <td><{$branch_type1}></td>
                                                        <td>
                                                            NT$<span id="int_show_branch1" class="currency-money1 text-right"><{$data_realstate.cInterestMoney}></span>元
                                                        </td>
                                                        <td>
                                                           <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractRealestate'}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>
                                                    <{if $branch_type2 !=''}>
                                                    <tr class="th_title_sml">
                                                        <td><{$branch_type2}></td>
                                                        <td>
                                                            NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney1}></span>元
                                                        </td>
                                                        <td>
                                                           <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractRealestate1'}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>
                                                    <{/if}> 

                                                    <{if $branch_type3 !=''}>
                                                    <tr class="th_title_sml">
                                                        <td><{$branch_type3}></td>
                                                        <td>
                                                            NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney2}></span>元
                                                        </td>
                                                        <td>
                                                           <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractRealestate2'}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>
                                                    <{/if}>
                                                    <tr>
                                                        <td colspan="3" bgcolor="#E8C1B4">地政士</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" width="25%">姓名</td>
                                                        <td class="tb-title2 th_title_sml" width="15%">金額</td>
                                                        <td class="tb-title2 th_title_sml">指定對象</td>
                                                    </tr> 
                                                    <tr class="th_title_sml">
                                                        <td><{$data_scrivener.sName}></td>
                                                        <td>
                                                            NT$<span id="int_show_scrivener" class="currency-money1 text-right"><{$data_scrivener.cInterestMoney}></span>元
                                                        </td>
                                                        <td>
                                                            <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractScrivener'}>
                                                                    <{$item.cName}> 
                                                                    NT$<span class="currency-money1 text-right"><{$item.cInterestMoney}></span>元;
                                                                <{/if}>
                                                            <{/foreach}>
                                                        </td>
                                                    </tr>

                                                </table>
                                            </div>
                                            <input type="button" onclick="int_arrange()" style="font-size:11pt;" value="　編輯　" <{$add_disabled}>>
                                            </td>
                                        </tr>
                                       
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                契稅之歸屬
                                            </td>
                                        </tr>
                                        <{if $data_bankcode.bBrand==1 && $data_bankcode.bCategory==2 }>
                                            <tr>
                                                    <th>地政士簽約費</th>
                                                    <td colspan="5" >
                                                        NT$<input type="text" name="income_scrivenermoney" value="<{$data_income.cSrivenerMoney}>" maxlength="16" size="12" class="currency-money1 text-right">元
                                                    </td>
                                            </tr> 
                                            <tr>
                                                <th></th>
                                                <td colspan="5">
                                                    除本契約有特別約定以外，各項稅捐、水、電、瓦斯、電話費、管理費等雜項費用，應以點交日為準，由買賣雙 方按比例負擔。房屋契稅由買方負擔。土地增值稅由賣方負擔，以 <{html_radios name=ascription_contribute options=$ascription_option2 selected=$data_ascription.cContribute}>辦理繳納，如賣方依自用稅率申報，不符規定時，賣方同意應逕按一般稅率繳納。
                                                </td>
                                            </tr>                                                           
                                        <{else}>
                                            <tr>
                                                <th></th>
                                                <td colspan="5" style="height:40px">
                                                    契稅由買方負擔，土地增值稅由賣方負擔，以&nbsp;&nbsp;
                                                    <{html_radios name=ascription_contribute options=$ascription_option2 selected=$data_ascription.cContribute}>
                                                    <!-- <input type="radio" name="ascription_contribute" value="1">一般稅率&nbsp;&nbsp;<input type="radio" name="ascription_contribute" value="2">自用住宅優惠稅率 -->
                                                    &nbsp;&nbsp;辦理繳納。
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="height:40px"><span class="th_title_sml"> 買方所負擔之費用：</span></th>
                                                <td colspan="5">
                                                <{html_checkboxes name=ascription_buy options=$ascription_option selected=$data_ascription.cBuyer}>
                                                <br><br>
                                                <{if $data_ascription.cBuyerOther==''}>
                                                    <input type="checkbox">
                                                <{else}>
                                                    <input type="checkbox" checked>
                                                <{/if}>
                                                其他
                                                    <input type="text" name="ascription_buyerother" size="8" value="<{$data_ascription.cBuyerOther}>">
                                                <br>&nbsp;
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="height:40px"><span class="th_title_sml">賣方所負擔之費用：</span></th>
                                                <td colspan="5">
                                                    <{html_checkboxes name=ascription_owner options=$ascription_option selected=$data_ascription.cOwner}>
                                                    <br><br>
                                                    <{if $data_ascription.cOwnerOther==''}>
                                                        <input type="checkbox">
                                                    <{else}>
                                                        <input type="checkbox" checked>
                                                    <{/if}>其他
                                                    <input type="text" name="ascription_ownerother" size="8" value="<{$data_ascription.cOwnerOther}>">
                                                </td>
                                            </tr>
                                                    
                                        <{/if}> 
                                        <tr>
                                            <th style="height:40px">賣方就本買賣標的</th>
                                            <td colspan="5">
                                                
                                                <{if $data_case.cProperty==1}>
                                                <input type="radio" name="case_property" value="1" checked>保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，否則買方得無條件解約。<br>
                                                <input type="radio" name="case_property" value="2">已告知買方為受農地套繪管制，買方仍同意依約履行。
                                                <{else if  $data_case.cProperty==2}>
                                                <input type="radio" name="case_property" value="1">保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，否則買方得無條件解約。<br>
                                                <input type="radio" name="case_property" value="2"  checked>已告知買方為受農地套繪管制，買方仍同意依約履行。
                                                <{else}>
                                                <input type="radio" name="case_property" value="1">保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，否則買方得無條件解約。<br>
                                                <input type="radio" name="case_property" value="2" >已告知買方為受農地套繪管制，買方仍同意依約履行。
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                其他
                                                <div style="float:right;padding-right:10px;"> <a href="contract_special.php?cCertifyId=<{$data_case.cCertifiedId}>&sign=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修特約事項</a> </div>
                                               
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <th>備註︰</th>
                                            <td colspan="5">
                                                <textarea rows="5" name="invoice_remark" class="input-text-per"><{$data_invoice.cRemark}></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                7日內未入帳說明
                                                <{if $is_edit == 1}>
                                                <div style="float:right;padding-right:10px;"> <a href="javascript:void(0)" style="font-size:9pt;" onclick="casmsg(1)">編輯</a> </div>
                                                <{/if}>
                                            </td>
                                            
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="6" id="casmsg1">
                                                <{if $is_edit == 1}>
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" colspan="5" width="80%">內容</td>
                                                        <td class="tb-title2 th_title_sml">時間</td>
                                                    </tr>
                                                    <{foreach from=$contractNote[1] key=key item=item}>
                                                    <tr>
                                                        <td colspan="5" style="border:1px solid #CCC"><{$item.cNote}></td>
                                                        <td style="border:1px solid #CCC"><{$item.cModify_Time}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                </table>
                                                 <{/if}>
                                            </td>
                                        </tr>
                                       
                                        
                                        <!-- <tr>
                                            <th>7日內&nbsp;&nbsp;&nbsp;<br>未入帳說明︰</th>
                                            <td colspan="5">
                                                <textarea rows="5" name="cNoIncome" class="input-text-per"><{$data_case.cNoIncome}></textarea>
                                            </td>
                                        </tr> -->
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                2個月未結案之案件
                                                <{if $is_edit == 1}>
                                                <div style="float:right;padding-right:10px;"> <a href="javascript:void(0)" onclick="casmsg(2)" style="font-size:9pt;">編輯</a> </div>
                                                <{/if}>
                                               
                                            </td>
                                            
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="6" id="casmsg2">
                                                <{if $is_edit == 1}>
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" colspan="5" width="80%">內容</td>
                                                        <td class="tb-title2 th_title_sml">時間</td>
                                                    </tr>
                                                    <{foreach from=$contractNote[2] key=key item=item}>
                                                    <tr>
                                                        <td colspan="5" style="border:1px solid #CCC"><{$item.cNote}></td>
                                                        <td style="border:1px solid #CCC"><{$item.cModify_Time}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                </table>
                                                 <{/if}>
                                            </td>
                                        </tr>
                                       
                                        <!-- <tr>
                                            <th style="padding: 5px;">2個月內&nbsp;&nbsp;&nbsp;<br>未結案說明︰</th>
                                            <td colspan="5">
                                                <textarea rows="5" name="cNoClosing" class="input-text-per"><{$data_case.cNoClosing}></textarea>
                                            </td>
                                        </tr> -->
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                超過點交日尚未結案
                                                <{if $is_edit == 1}> 
                                                <div style="float:right;padding-right:10px;"> <a href="javascript:void(0)" onclick="casmsg(3)" style="font-size:9pt;">編輯</a> </div>
                                                <{/if}>
                                            </td>
                                            
                                        </tr>
                                        <tr>
                                            <td colspan="6" id="casmsg3">
                                                <{if $is_edit == 1}>
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td class="tb-title2 th_title_sml" colspan="5" width="80%">內容</td>
                                                        <td class="tb-title2 th_title_sml">時間</td>
                                                    </tr>
                                                    <{foreach from=$contractNote[3] key=key item=item}>
                                                    <tr>
                                                        <td colspan="5" style="border:1px solid #CCC"><{$item.cNote}></td>
                                                        <td style="border:1px solid #CCC"><{$item.cModify_Time}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                </table>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6">
                                                <br/>  
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>建檔︰</th>
                                            <td>
                                                <{if $data_bankcode.bFrom==2}>
                                                    <input type="text"  maxlength="10" class="input-text-mid" value="地政士" disabled="disabled"/>
                                                <{else}>
                                                    <input type="hidden" name="case_undertakerid" maxlength="10" class="input-text-mid" value="<{$data_case.cUndertakerId}>" readonly/>
                                                    <input type="text"  maxlength="10" class="input-text-mid" value="<{$case_undertaker.pName}>" disabled="disabled"/>
                                                <{/if}>
                                                
                                            </td>
                                            <th>最後修改者︰</th>
                                            <td>
                                                <input type="text" name="case_lasteditor" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                            </td>
                                            <th>最後修改時間︰</th>
                                            <td>
                                                <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div id="tabs-land">
                                    <table border="0" width="100%">                                     
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                產品資料 > 土地標示
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>土地坐落︰</th>
                                            <td colspan="5">
                                                <input type="hidden" name="land_zip" id="land_zip" value="<{$data_land.cZip}>" />
                                                <input type="text" maxlength="6" name="land_zipF" id="land_zipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_land.cZip|substr:0:3}>" />
                                                <select class="input-text-big" name="land_country" id="land_country" onchange="getArea('land_country','land_area','land_zip')">
                                                    <{$land_country}>
                                                </select>
                                                <span id="land_areaR">
                                                <select class="input-text-big" name="land_area" id="land_area" onchange="getZip('land_area','land_zip')">
                                                    <{$land_area}>
                                                </select>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>段︰</th>
                                            <td>
                                                <input type="text" name="land_land1" maxlength="16" class="input-text-big" value="<{$data_land.cLand1}>"/>
                                            </td>
                                            <th>小段︰</th>
                                            <td>
                                                <input type="text" name="land_land2" maxlength="16" class="input-text-big" value="<{$data_land.cLand2}>"/>
                                            </td>
                                            <th>地號︰</th>
                                            <td>
                                                <input type="text" name="land_land3" maxlength="12" class="input-text-big" value="<{$data_land.cLand3}>"/> 
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><!-- 地目︰ --></th>
                                            <td>
                                                <!--  <{html_options name=land_land4 options=$menu_categoryland selected=$data_land.cLand4 }> -->
                                            </td>
                                            <th>面積︰</th>
                                            <td>
                                                <input type="text" name="land_measure" maxlength="10" size="12" class="text-right" value="<{$data_land.cMeasure}>" onKeyup="checkCalTax()"/>M<sup>2</sup>
                                                <input type="hidden" name="changeLand">
                                            </td>
                                            <th>使用分區︰</th>
                                            <td>
                                                <{html_options name=land_area options=$menu_categoryarea selected=$data_land.cCategory}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>公告土地現值︰</th>
                                            <td>
                                                <input type="text" name="land_money" maxlength="16" size="13" class="currency-money1 text-right" value="<{$data_land.cMoney}>" onKeyup="checkCalTax()"/>元/M<sup>2</sup>
                                            </td>
                                            <th>權利範圍︰</th>
                                            <td colspan="3">
                                                <input type="text" name="land_power1" size="10" class="text-right" value="<{$data_land.cPower1}>" onKeyup="checkCalTax()"/> / 
                                                <input type="text" name="land_power2" size="10" class="text-right" value="<{$data_land.cPower2}>" onKeyup="checkCalTax()"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">前次移轉現值或原規定地價︰</th>
                                            <td colspan="3">
                                                <input type="text" name="land_movedate" onclick="showdate_m(form_case.land_movedate)" maxlength="7" size="10" class="calender date-field text-right" value="<{$data_land.cMoveDate}>" onKeyup="checkCalTax()"/>    
                                                <input type="text" name="land_landprice" maxlength="13" size="13" class="calender currency-money2 text-right" value="<{$data_land.cLandPrice}>" onKeyup="checkCalTax()"/>元/M<sup>2</sup>
                                                <input type="hidden" name="land_landprice_check">
                                            </td>
                                            <td colspan="1">
                                                <{if $limit_show == '0'}>
                                                 <input type="button" onclick="land_edit()" value="編輯土地資料" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th colspan="2">買賣標的如為農地，雙方約定按</th>
                                            <td colspan="4">
                                            <{if $data_land.cFarmLand==1 || $data_land.cFarmLand==0}>
                                                <input type="radio" name="land_farmland" value="1" checked>一般稅率申報
                                                <input type="radio" name="land_farmland" value="2" >申請不課徵土地徵值稅
                                            <{else}>
                                                <input type="radio" name="land_farmland" value="1">一般稅率申報
                                                <input type="radio" name="land_farmland" value="2" checked>申請不課徵土地徵值稅
                                            <{/if}>
                                                
                                            </td>
                                        </tr>
                                    </table>   
                                </div>
                                <div id="tabs-build">
                                    <div style="float:right;padding-right:10px;">
                                        <a href="#" style="font-size:9pt;" id="new_build">新增物件</a>
                                        <input type="hidden" name="buildcount" value="<{$data_property_count}>">
                                    </div>
                                    <{foreach from=$data_property key=key item=item}>
                                        <table border="0" width="100%" >
                                            <tr>
                                                <td colspan="6" class="tb-title" >
                                                    產品資料 > 建物標示 
                                                    <div style="float:right;padding-right:10px;">
                                                    <{if $item.cItem!=0 && $data_case.cSignCategory==1}>
                                                        <a href="#" style="font-size:9pt;"  onclick="del_build(<{$item.cItem}>)">刪除</a>
                                                    <{/if}>
                                                </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>建物門牌︰</th>
                                                <td colspan="5">
                                                   <input type="hidden" id="property_Item<{$item.cItem}>" name="property_Item[]" value="<{$item.cItem}>"> 
                                                    <input type="hidden" name="property_zip<{$item.cItem}>" id="property_zip<{$item.cItem}>" value="<{$item.cZip}>" />
                                                    <input type="text" maxlength="6" name="property_zip<{$item.cItem}>F" id="property_zip<{$item.cItem}>F" class="input-text-sml text-center pZip" readonly="readonly" value="<{$item.cZip|substr:0:3}>" />
                                                    <select class="input-text-big" name="property_country<{$item.cItem}>" id="property_country<{$item.cItem}>" onchange="getArea('property_country<{$item.cItem}>','property_area<{$item.cItem}>','property_zip<{$item.cItem}>')">
                                                        <{if $is_edit == 0}>
                                                            <{$property_country}>
                                                        <{else}>
                                                            <{$item.property_country}>
                                                         <{/if}>
                                                    </select>
                                                    <span id="property_area<{$item.cItem}>R">
                                                    <select class="input-text-big" name="property_area<{$item.cItem}>" id="property_area<{$item.cItem}>" onchange="getZip('property_area<{$item.cItem}>','property_zip<{$item.cItem}>')">
                                                                                                                
                                                        <{if $is_edit==0}>
                                                        <{$property_area}>
                                                        <{else}>
                                                            <{$item.property_area}>
                                                         <{/if}>
                                                    </select>
                                                    </span>
                                                    <input style="width:330px;" class="pAddr" name="property_addr<{$item.cItem}>" value="<{$item.cAddr}>" onkeyup="checkAddr(<{$item.cItem}>)"/>

                                                    <{if $item.cItem == 0}>
                                                    <span id="showSameAddr<{$item.cItem}>"></span>
                                                    <input type="button" name="import<{$item.cItem}>" value="匯入資料" style="display:none">
                                                    <{/if}>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>主要用途︰</th>

                                                <td colspan="5" id="build_ck"> <!--{$variable|cat:"要合併的字串"}-->
                                                                <{html_checkboxes name=property_objuse|cat:$item.cItem options=$menu_objuse selected=$item.cObjUse }>

                                                                <{if $item.cIsOther == '1'}>
                                                    <input type="checkbox" name="property_cIsOther<{$item.cItem}>" value="1" checked />
                                                                <{else}>
                                                    <input type="checkbox" name="property_cIsOther<{$item.cItem}>" value="1" />
                                                                <{/if}>其它
                                                    <input type="text" name="property_cOther<{$item.cItem}>" class="input-text-big"  value="<{$item.cOther}>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>建號︰</th>
                                                <td>
                                                    <input type="text" name="property_buildno<{$item.cItem}>" maxlength="16" class="input-text-big" value="<{$item.cBuildNo}>"/>
                                                </td>
                                                <th><span class="th_title_sml">建築完成日期︰</span></th>
                                                <td>
                                                    <{if $item.cBuildDate == '0000-00-00 00:00:00' || $item.cBuildDate==''}>
                                                    <input type="text" name="property_builddate<{$item.cItem}>" onclick="showdate(form_case.property_builddate<{$item.cItem}>,form_case.property_buildage<{$item.cItem}>)" maxlength="10" class="calender input-text-big" value="" onChange="build_age('property_builddate<{$item.cItem}>','property_buildage<{$item.cItem}>')"/>
                                                    <{else}>
                                                    <input type="text" name="property_builddate<{$item.cItem}>" onclick="showdate(form_case.property_builddate<{$item.cItem}>,form_case.property_buildage<{$item.cItem}>)" maxlength="10" class="calender input-text-big" value="<{$item.cBuildDate}>" onChange="build_age('property_builddate<{$item.cItem}>','property_buildage<{$item.cItem}>')"/>
                                                    <{/if}>
                                                </td>
                                                <th>主要建材︰</th>
                                                <td>
                                                    <{html_options name=property_budmaterial|cat:$item.cItem options=$menu_material selected=$item.cBudMaterial}>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>樓層/總樓層︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="property_levelnow<{$item.cItem}>" maxlength="6" class="input-text-mid" value="<{$item.cLevelNow}>"/> /
                                                    <input type="text" name="property_levelhighter<{$item.cItem}>" maxlength="3" class="input-text-sml" value="<{$item.cLevelHighter}>"/>
                                                    &nbsp;&nbsp;
                                                    <{if $item.cTownHouse == '1'}> 
                                                    <input type="checkbox" name='property_housetown<{$item.cItem}>' value='1' checked/> 透天厝
                                                    <{else}>
                                                    <input type="checkbox" name='property_housetown<{$item.cItem}>' value='1'  /> 透天厝
                                                    <{/if}>
                                                </td>
                                                <th>出賣權利範圍</th>
                                                <td>
                                                    <input type="text" name="property_power1<{$item.cItem}>" value="<{$item.cPower1}>" size="8" >&nbsp;/&nbsp;<input type="text" name="property_power2<{$item.cItem}>" value="<{$item.cPower2}>" size="8" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>產品面積︰</th>
                                                <td>
                                                    <input type="text" name="property_measuretotal<{$item.cItem}>" maxlength="10" size="10" class="input-text-big text-right" value="<{$item.cMeasureTotal}>" readonly/>M<sup>2</sup>
                                                </td>
                                               <th><span class="th_title_sml">隨同主建物轉移<br>共同使用部分︰</span></th>
                                                <td colspan="3">面積：<input type="text" name="property_publicmeasuretotal<{$item.cItem}>" size="8" value="<{$item.cPublicMeasureTotal}>">&nbsp;持分<input type="text" name="property_publicmeasuremain<{$item.cItem}>" size="8" value="<{$item.cPublicMeasureMain}>"></td>

                                            </tr>
                                            <tr>
                                                <th>主要類型︰</th>
                                                <td>
                                                    <{html_options name=property_objkind|cat:$item.cItem options=$menu_objkind selected=$item.cObjKind}>
                                                </td>
                                                <td></td>
                                                <td></td>
                                              <!--   <th>車位︰</th>
                                                <td>
                                                    <{html_radios name=property_hascar|cat:$item.cItem options=$menu_categorycar selected=$item.cHasCar separator=' '}>
                                                    <input type="button" onclick="car_edit()" value="停車位標示" class="bt4" style="display:;width:100px;height:40px;">
                                                </td> -->
                                                <th>房/廳/衛︰</th>
                                                <td>
                                                    <input type="text" name="property_room<{$item.cItem}>" maxlength="2" size="3" class="input-text-sml text-right" value="<{$item.cRoom}>" /> /
                                                    <input type="text" name="property_parlor<{$item.cItem}>" maxlength="2" size="3" class="input-text-sml text-right" value="<{$item.cParlor}>" /> /
                                                    <input type="text" name="property_toilet<{$item.cItem}>" maxlength="2" size="3" class="input-text-sml text-right" value="<{$item.cToilet}>" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>屋齡︰</th>
                                                <td>
                                                    <input type="text" name="property_buildage<{$item.cItem}>" maxlength="3" size="3" class="input-text-sml text-right" value="<{$item.cBuildAge}>" />年
                                                </td>
                                                <th>交屋日︰</th>
                                                <td>
                                                    <input type="text" name="property_closingday<{$item.cItem}>" onclick="showdate(form_case.property_closingday<{$item.cItem}>)" maxlength="10" class="calender date-field input-text-big" value="<{$item.cClosingDay}>" style="width:100px;" id="property_closingday<{$item.cItem}>"/>
                                                    <a href="#property_closingday<{$item.cItem}>" onclick="closingday('property_closingday<{$item.cItem}>')">
                                                        <img src="/images/ng.png" title="清除">
                                                    </a>
                                                    
                                                </td>
                                                <td colspan="2">
                                                <{if $limit_show == '0'}>
                                                <input type="button" onclick="build_edit(<{$item.cItem}>)" value="編輯建物資料" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                                </td>
                                            </tr>
                                        </table>
                                   <{/foreach}>
                                   <div class="newP"></div>
                                   
                                </div>
                                <div id="tabs-scrivener">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                地政士資料
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="sign-red">*</span>地政士姓名︰</th>
                                            <td>
                                                                <{if $is_edit == 1}>
                                                                    <{html_options name=scrivener_id options=$menu_scrivener selected=$data_scrivener.cScrivener  disabled="disabled"}>
                                                                <{else}>
                                                                    <{html_options id=scrivener_id name=scrivener_id options=$menu_scrivener selected=$data_scrivener.cScrivener}>
                                                                <{/if}>
                                            </td>
                                            <th>事務所名稱︰</th>
                                            <td>
                                                <input type="text" name="scrivener_office" maxlength="16" class="input-text-per" value="" disabled="disabled"/>
                                            </td>
                                            <th>行動電話︰</th>
                                            <td>
                                                <input type="text" name="scrivener_mobilenum" maxlength="10" class="input-text-pre" value="" disabled="disabled"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>地政士助理︰</th>
                                            <td>
                                                <input type="text" name="scrivener_assistant" maxlength="10" class="input-text-mid" value="<{$data_scrivener.cAssistant}>"/>
                                            </td>
                                            <th>電話︰</th> 
                                            <td>
                                                <input type="text" name="scrivener_telarea" maxlength="3" class="input-text-sml" value="" disabled="disabled"/> -
                                                <input type="text" name="scrivener_telmain" maxlength="10" class="input-text-mid" value="" disabled="disabled"/>
                                            </td>
                                            <th>傳真︰</th>
                                            <td>
                                                <input type="text" name="scrivener_faxarea" maxlength="3" class="input-text-sml" value="" disabled="disabled"/> -
                                                <input type="text" name="scrivener_faxmain" maxlength="10" class="input-text-mid" value="" disabled="disabled"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="sign-red">*</span>專屬帳號︰</th>
                                            <td>
                                                <{if $is_edit == 1}>
                                                    <input type="text" class="input-text-pre" value="<{$data_case.cEscrowBankAccount}>" disabled="disabled" name="scrivener_bankaccount2">
                                                <{else}>
                                                    <select name="scrivener_bankaccount">
                                                        <option value="0"> -- </option>
                                                    </select>
                                                <{/if}>
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseS"><{$scrivener_sales}></span></td>
                                            <td></td>
                                            <td >
                                                <{if $limit_show == '0'}>
                                                    <{if $is_edit == 1}>
                                                    <input type="button" onclick="sms_edit()" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                                                    <{/if}>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr>
                                            <th>回饋比率：</th>
                                            <td><input type="text" class="input-text-pre" style="width:100px;" name="sRecall" value="<{$data_case.cScrivenerRecall}>"  disabled="disabled">%

                                            
                                            </td>

                                            <th>特殊回饋比率：</th>
                                            <td>
                                                <input type="hidden" name="sSpRecall" value="">
                                                <input type="text" style="width:100px;" name="scrivener_sSpRecall" value="<{$data_case.cScrivenerSpRecall}>" disabled="disabled">%
                                            </td>

                                        </tr>
                                        <tr>
                                            <th class="th_title_sml">品牌回饋代書比率：</th>
                                            <td colspan="5">
                                                <span id="ScrivenerFeedSpTxt"><{$ScrivenerFeedSpTxt}></span>
                                                
                                                
                                                <input type="hidden" name="scrivener_BrandScrRecall" value="<{$data_case.cBrandScrRecall}>">
                                                <input type="hidden" name="scrivener_BrandScrRecall1" value="<{$data_case.cBrandScrRecall1}>">
                                                <input type="hidden" name="scrivener_BrandScrRecall2" value="<{$data_case.cBrandScrRecall2}>">

                                                <input type="hidden" name="scrivener_BrandRecall" value="<{$data_case.cBrandRecall}>">
                                                <input type="hidden" name="scrivener_BrandRecall1" value="<{$data_case.cBrandRecall1}>">
                                                <input type="hidden" name="scrivener_BrandRecall2" value="<{$data_case.cBrandRecall2}>">
                                                
                                            </td>
                                        </tr>

                                        <{else}>
                                        <tr>
                                            <td><input type="hidden" class="input-text-pre" style="width:100px;" name="sRecall" value="<{$data_case.cScrivenerRecall}>"  disabled="disabled">
                                            <input type="hidden" name="sSpRecall" value="">
                                                <input type="hidden" style="width:100px;" name="scrivener_sSpRecall" value="<{$data_case.cScrivenerSpRecall}>" disabled="disabled">
                                                <input type="hidden" name="scrivener_BrandScrRecall" value="<{$data_case.cBrandScrRecall}>">
                                                <input type="hidden" name="scrivener_BrandScrRecall1" value="<{$data_case.cBrandScrRecall1}>">
                                                <input type="hidden" name="scrivener_BrandScrRecall2" value="<{$data_case.cBrandScrRecall2}>">
                                                 <input type="hidden" name="scrivener_BrandRecall" value="<{$data_case.cBrandRecall}>">
                                                <input type="hidden" name="scrivener_BrandRecall1" value="<{$data_case.cBrandRecall1}>">
                                                <input type="hidden" name="scrivener_BrandRecall2" value="<{$data_case.cBrandRecall2}>">
                                            </td>
                                            
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <th>地址︰</th>
                                            <td colspan="5">
                                                <input type="hidden" name="scrivener_zip" id="scrivener_zip" disabled="disabled" value="<{$scrivener_zip}>"/>
                                                <input type="text" maxlength="6" name="scrivener_zipF" id="scrivener_zipF" class="input-text-sml text-center" disabled="disabled" value="<{$scrivener_zip|substr:0:3}>"/>
                                                <span id="scrivener_countryR">
                                                <select class="input-text-big" name="scrivener_country" id="scrivener_country" onchange="getArea('scrivener_country','scrivener_area','scrivener_zip')" disabled="disabled">
                                                    <{$scrivener_country}>
                                                </select>
                                                </span>
                                                <span id="scrivener_areaR">
                                                <select class="input-text-big" name="scrivener_area" id="scrivener_area" onchange="getZip('scrivener_area','scrivener_zip')" disabled="disabled">
                                                    <{$scrivener_area}>
                                                </select>
                                                </span>
                                                <input style="width:330px;" name="scrivener_addr" value="" disabled="disabled" />
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="6">
                                                <br/>  
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>建檔︰</th>
                                            <td>
                                                <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$case_undertaker.pName}>" disabled="disabled"/>
                                            </td>
                                            <th>最後修改者︰</th>
                                            <td>
                                                <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                            </td>
                                            <th>最後修改時間︰</th>
                                            <td>
                                                <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                            </td>
                                        </tr>      
                                    </table>
                                </div>
                                <div id="tabs-realty">
                                    <table border="0" width="100%">
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4}>
                                                    仲介資料 <{html_radios name="cServiceTarget" options=$STargetOption selected=$cServiceTarget seperator=' ' onClick = "feedback_money()"}>
                                                    <input type="hidden" name="checkCase3">
                                                <{else}>
                                                     仲介資料 <{html_radios name="cServiceTarget" options=$STargetOption selected=$cServiceTarget seperator=' '  disabled=disabled}>
                                                    <input type="hidden" name="checkCase3">
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                             <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4}>
                                                <th><span class="sign-red">*</span>仲介品牌︰</th>
                                                <td>
                                                    <{html_options name="realestate_brand" options=$menu_brand selected=$data_realstate.cBrand }>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介商類型︰</th>
                                                <td> <{html_options name="realestate_branchcategory" options=$menu_categoryrealestate selected=$data_realstate.bCategory }></td>
                                                <th><span class="sign-red">*</span>仲介店名︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_branchnum" value="<{$data_realstate.cBranchNum}>" />
                                                   
                                                    <input type="hidden" name="branch_staus" value="<{$realestate_status}>">
                                                
                                                    <span id="realestate_branchR">
                                                    <select class="realty_branch" name="realestate_branch">
                                                        <{$branch_options}>
                                                    </select>
                                                    </span>
                                                   
                                                </td>
                                            <{else}>
                                                <th><span class="sign-red">*</span>仲介品牌︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_brand" value="<{$data_realstate.cBrand}>" >
                                                    <input type="text" value="<{$brand_type1}>" disabled=disabled>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介商類型︰</th>
                                                <td> 
                                                     <input type="hidden" name="realestate_branchcategory" value="<{$data_realstate.bCategory}>">
                                                    <input type="text" value="<{$branch_cat1}>" disabled=disabled>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介店名︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_branchnum" value="<{$data_realstate.cBranchNum}>" />
                                                   
                                                    <input type="text" value="<{$branch_type1}>" disabled=disabled>
                                                   
                                                </td>
                                            <{/if}>
                                        </tr>
                                        <tr>
                                            <th>仲介公司名稱︰</th>
                                            <td>
                                                <input type="text" name="realestate_name" maxlength="10" class="input-text-pre" value="<{$data_realstate.cName}>" disabled='disabled' />
                                            </td>
                                            <th>統一編號︰</th>
                                            <td>
                                                <input type="text" name="realestate_serialnumber" maxlength="10" class="input-text-big" value="<{$rel1.bSerialnum}>" disabled="disabled" />
                                            </td>
                                            <th>電話︰</th> 
                                            <td>
                                                <input type="text" name="realestate_telarea" maxlength="3" class="input-text-sml" value="<{$data_realstate.cTelArea}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_telmain" maxlength="10" class="input-text-mid" value="<{$data_realstate.cTelMain}>" disabled='disabled' />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>傳真︰</th>
                                            <td>
                                                <input type="text" name="realestate_faxarea" maxlength="3" class="input-text-sml" value="<{$data_realstate.cFaxArea}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_faxmain" maxlength="10" class="input-text-mid" value="<{$data_realstate.cFaxMain}>" disabled='disabled' />
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseB"><{$branchnum_data_sales}></span></td>
                                            <td>
                                            <{if $limit_show == '0'}>
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum}>','1')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                            <{/if}>
                                            </td>
                                            
						<{if $imgStampEdit == 1}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								<div id="showImg" style="margin-top:5px;width:246px;height:145px;border: 1px solid #CCC;padding:2px;">
									<{$imgStamp}>
								</div>
							</td>
							
						<{else}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								&nbsp;
							</td>
							
						<{/if}>
											
                                        </tr>
                                        <tr>
                                            <th>地址︰</th>
                                            <td colspan="4">
                                                <input type="hidden" name="realestate_zip" id="realestate_zip" value="<{$data_realstate.cZip}>" disabled='disabled'  />
                                                <input type="text" maxlength="6" name="realestate_zipF" id="realestate_zipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_realstate.cZip|substr:0:3}>" disabled='disabled'  />
                                                <span id="realestate_countryR">
                                                <select class="input-text-big" name="realestate_country" id="realestate_country" onchange="getArea('realestate_country','realestate_area','realestate_zip')" disabled='disabled' >
                                                    <{$realestate_country}>
                                                </select>
                                                </span>
                                                <span id="realestate_areaR">
                                                <select class="input-text-big" name="realestate_area" id="realestate_area" onchange="getZip('realestate_area','realestate_zip')" disabled='disabled' >
                                                    <{$realestate_area}>
                                                </select>
                                                </span>
                                                <input style="width:330px;" name="realestate_addr" value="<{$data_realstate.cAddress}>" disabled='disabled'  />
                                            </td>
                                        </tr>
                                     
                                        <tr>
                                            <th>服務費先行撥付同意書︰</th>
                                            <td >
                                                <span id="promissory1"><{$promissory1}></span>
                                                <input type="hidden" name="realestate_bRecall" value="<{$data_realstate.bRecall}>" />
                                                <input type="hidden" name="realestate_bScrRecall" value="<{$data_realstate.bScrRecall}>" />
                                            </td>
                                             <{if $smarty.session.member_pFeedBackModify!='0'}>
                                            <th>回饋比率</th>
                                            <td>
                                                <span id="rea_bRecall"><{$data_case.cBranchRecall}></span> %
                                            </td>
                                            <th>代書回饋比率</th>
                                            <td ><span id="rea_bScrRecall" ><{$data_case.cBranchScrRecall}></span>%</td>
											<{else}>
                                            <th>&nbsp;</th>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <th>&nbsp;</th>
                        
                                            <{/if}>
                                        </tr>
                                      
                                        <tr>
                                            <th>本票備註︰</th>
                                            <td colspan="5">
                                                <input type="text" name="Feedback_CashierOrderRemark" maxlength="255" class="input-text-per" value="<{$rel1.bCashierOrderRemark}>" disabled="disabled">
                                            </td>
                                        </tr>
                                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr>
                                            <th>回饋金備註︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_Renote" rows="5" class="input-text-per" disabled="disabled"><{$rel1.bRenote}></textarea>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <th>備註說明︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_CashierOrderMemo" rows="5" class="input-text-per" disabled="disabled"><{$rel1.bCashierOrderMemo}></textarea>
                                                <input type="hidden" name="data_feedData" value="<{$data_feedDataCount1}>">
                                            </td>
                                        </tr>
                                        </table>
                                        <div id="branchFeedData">
                                            
                                       
                                            <table border="0" width="100%" >
                                            <{foreach from=$data_feedData1 key=key item=item}>
                                            <tr>
                                                <th>回饋金對象資料</th>
                                                <td colspan="5">
                                                    <table width="98%">
                                                        
                                                        <tr>
                                                            <td width="10%" align="center" class="tb-title2 th_title_sml">回饋<br>方式</td>
                                                            <td>
                                                                
                                                                 <{html_options name="fFeedBack" options=$menu_categoryrecall selected=$item.fFeedBack disabled="disabled"}>
                                                            </td>
                                                            <td width="10%" align="center" class="tb-title2 th_title_sml">姓名/<br>抬頭</td>
                                                            <td>
                                                                <input type="text" maxlength="15" class="input-text-big th_title_sml" value="<{$item.fTitle}>" disabled="disabled" style="width:200px"/>
                                                            </td>
                                                            <td width="10%" align="center" class="th_title_sml tb-title2">店長行<br>動電話</td>
                                                            <td><input type="text" maxlength="10" class="input-text-big" value="<{$item.fMobileNum}>" disabled="disabled"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">身份別</td>
                                                            <td>
                                                               <{html_options name="fIdentity" options=$menu_categoryidentify selected=$item.fIdentity disabled="disabled"}>
                                                            </td>
                                                            <td align="center" class="th_title_sml tb-title2">證件<br>號碼</td>
                                                            <td>
                                                                <input type="text" maxlength="15" class="input-text-big" value="<{$item.fIdentityNumber}>" disabled="disabled"/>
                                                                
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">聯絡<br>地址</td>
                                                            <td colspan="5">
                                                               
                                                                <input type="text" maxlength="6" value="<{$item.fZipC}>" class="input-text-sml text-center" readonly="readonly" value="" disabled="disabled"/>
                                                                <span id="FeedBaseC">
                                                                    <select class="input-text-big" disabled="disabled" name="FeedBack_basecity">
                                                                       <{$item.countryC}>
                                                                    </select>
                                                                </span>
                                                                <span id="FeedBaseA">
                                                                <select class="input-text-big" disabled="disabled" ame="FeedBack_basearea">
                                                                    <{$item.areaC}>
                                                                </select>
                                                                </span>
                                                                <input style="width:500px;" value="<{$item.fAddrC}>" name="FeedBack_baseaddr" disabled="disabled"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">戶藉<br>地址</td>
                                                            <td colspan="5">
                                                                
                                                                <input type="text" maxlength="6" class="input-text-sml text-center" name="FeedBack_regzip" readonly="readonly" value="<{$item.fZipR}>" disabled="disabled"/>
                                                                <span id="FeedRegistC">
                                                                    <select class="input-text-big" disabled="disabled" name="FeedBack_regcity">
                                                                        <{$item.countryR}>
                                                                    </select>
                                                                </span>
                                                                <span id="FeedRegistA">
                                                                <select class="input-text-big" disabled="disabled" name="FeedBack_regarea">
                                                                    <{$item.areaR}>
                                                                </select>
                                                                </span>
                                                                <input style="width:500px;"  value="<{$item.fAddrR}>"  disabled="disabled"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">電子<br>郵件</td>
                                                            <td colspan="3">
                                                                <input type="text" maxlength="255" class="input-text-per" value="<{$item.fEmail}>" disabled="disabled"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">總行</td>
                                                            <td>
                                                                <{html_options name="d" options=$menu_bank selected=$item.fAccountNum disabled="disabled" style="width:250px;"}>
                                                            </td>
                                                            <td align="center"  class="th_title_sml tb-title2">分行</td>
                                                            <td>
                                                                <span id="Feed_branch">
                                                                    <select class="input-text-per" disabled="disabled" name="FeedBack_bankbranch">
                                                                    <{$item.bank_branch}>
                                                                    </select>
                                                                </span>
                                                            </td>
                                                            <td align="center" class="th_title_sml tb-title2">指定<br>帳號</td>
                                                            <td>
                                                                <input type="text" maxlength="14" class="input-text-per" name="FeedBack_acc" value="<{$item.fAccount}>" disabled="disabled"/>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" class="th_title_sml tb-title2">戶名</td>
                                                            <td>
                                                                <input type="text" maxlength="20" class="input-text-per" name="FeedBack_accname" value="<{$item.fAccountName}>" disabled="disabled"/>
                                                            </td>
                                                            <th>發票種類︰</th>
                                                            <td><input type="text"  value="<{$item.fNote}>" disabled="disabled"></td>
                                                            <td></td>
                                                            <td></td>
                                                        </tr>
                                                        <tr><td colspan="6"><hr></td></tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <{/foreach}>
                                            </table>
                                         </div>
                                        <table border="0" width="100%" class="show_2_realty" >
                                        <{if $data_realstate.cBranchNum1 == 0}>
                                        <tr><td colspan="6" style="text-align:right;"><span id="addBranch2"><a href="#" style="font-size:9pt;" onclick="addBranchList('2')">(第二組仲介)</a></span></td></tr>
                                        <{/if}>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <td colspan="6" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4}>
                                                仲介資料 <{html_radios name="cServiceTarget1" options=$STargetOption selected=$cServiceTarget1 seperator=' ' onClick = "feedback_money()"}>
                                                <{else}>
                                                仲介資料 <{html_radios name="cServiceTarget1" options=$STargetOption selected=$cServiceTarget1 seperator=' ' disabled=disabled}>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4}>
                                                <th><span class="sign-red">*</span>仲介品牌︰</th>
                                                <td>
                                                    <{html_options name="realestate_brand1" options=$menu_brand selected=$data_realstate.cBrand1 }>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介商類型︰</th>
                                                <td> <{html_options name="realestate_branchcategory1" options=$menu_categoryrealestate selected=$data_realstate.bCategory1 }></td>
                                                <th><span class="sign-red">*</span>仲介店名︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_branchnum1" value="<{$data_realstate.cBranchNum1}>" />
                                                   
                                                    <input type="hidden" name="branch_status1" value="<{$realestate_status1}>">
                                                   
                                                    <span id="realestate_branch1R">
                                                    <select class="realty_branch" name="realestate_branch1">
                                                        <{$branch_options1}>
                                                    </select>
                                                    </span>
                                                </td>
                                            <{else}>
                                                <th><span class="sign-red">*</span>仲介品牌︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_brand1" value="<{$data_realstate.cBrand1}>">
                                                    <input type="text" value="<{$brand_type2}>" disabled=disabled>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介商類型︰</th>
                                                <td> 
                                                     <input type="hidden" name="realestate_branchcategory1" value="<{$data_realstate.bCategory1}>">
                                                    <input type="text" value="<{$branch_cat2}>" disabled=disabled>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介店名︰</th>
                                                <td>
                                                    <input type="hidden" name="realestate_branchnum1" value="<{$data_realstate.cBranchNum1}>" />
                                                    <input type="text" value="<{$branch_type2}>" disabled=disabled>
                                                </td>
                                            <{/if}>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>仲介公司名稱︰</th>
                                            <td>
                                                <input type="text" name="realestate_name1" maxlength="10" class="input-text-pre" value="<{$data_realstate.cName1}>" disabled='disabled' />
                                            </td>
                                            <th>統一編號︰</th>
                                            <td>
                                                <input type="text" name="realestate_serialnumber1" maxlength="10" class="input-text-big" value="<{$rel2.bSerialnum}>" disabled="disabled" />
                                            </td>
                                            <th>電話︰</th> 
                                            <td>
                                                <input type="text" name="realestate_telarea1" maxlength="3" class="input-text-sml" value="<{$data_realstate.cTelArea1}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_telmain1" maxlength="10" class="input-text-mid" value="<{$data_realstate.cTelMain1}>" disabled='disabled' />
                                            </td>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>傳真︰</th>
                                            <td>
                                                <input type="text" name="realestate_faxarea1" maxlength="3" class="input-text-sml" value="<{$data_realstate.cFaxArea1}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_faxmain1" maxlength="10" class="input-text-mid" value="<{$data_realstate.cFaxMain1}>" disabled='disabled' />
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseB1"><{$branchnum_data_sales1}></span></td>
                                            <td>
                                            <{if $limit_show == '0'}>
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum1}>','2')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                            <{/if}>
                                            </td>
                                            
						<{if $imgStampEdit1 == 1}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								<div id="showImg1" style="margin-top:5px;width:246px;height:145px;border: 1px solid #CCC;padding:2px;">
									<{$imgStamp1}>
								</div>
							</td>
							
						<{else}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								&nbsp;
							</td>
							
						<{/if}>
											
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>地址︰</th>
                                            <td colspan="4">
                                                <input type="hidden" name="realestate_zip1" id="realestate_zip1" value="<{$data_realstate.cZip1}>" disabled='disabled' />
                                                <input type="text" maxlength="6" name="realestate_zip1F" id="realestate_zip1F" class="input-text-sml text-center" readonly="readonly" value="<{$data_realstate.cZip1|substr:0:3}>" disabled='disabled'  />
                                                <span id="realestate_country1R">
                                                <select class="input-text-big" name="realestate_country1" id="realestate_country1" onchange="getArea('realestate_country1','realestate_area1','realestate_zip1')" disabled='disabled' >
                                                    <{$realestate_country1}>
                                                </select>
                                                </span>
                                                <span id="realestate_area1R">
                                                <select class="input-text-big" name="realestate_area1" id="realestate_area1" onchange="getZip('realestate_area1','realestate_zip1')" disabled='disabled' >
                                                    <{$realestate_area1}>
                                                </select>
                                                </span>
                                                <input style="width:330px;" name="realestate_addr1" value="<{$data_realstate.cAddress1}>" disabled='disabled'  />
                                            </td>
                                        </tr>
                                        
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>服務費先行撥付同意書︰</th>
                                            <td >
                                                　<span id="promissory2"><{$promissory2}></span>
                                                <input type="hidden" name="realestate_bRecall1" value="<{$data_realstate.bRecall1}>" />
                                                <input type="hidden" name="realestate_bScrRecall1" value="<{$data_realstate.bScrRecall1}>" />
                                            </td>
                                           <{if $smarty.session.member_pFeedBackModify!='0'}>
                                            <th>回饋比率</th>       
                                            <td>
                                                <span id="rea_bRecall1"><{$data_case.cBranchRecall1}></span>%
                                            </td>
                                            <th>代書回饋比率<br></th>
                                            <td><span id="rea_bScrRecall1"><{$data_case.cBranchScrRecall1}></span>%</td>
											<{else}>
                                            <th>&nbsp;</th>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <th>&nbsp;</th>

                                           <{/if}> 
                                        </tr>
                                         <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>本票備註︰</th>
                                            <td colspan="5">
                                                <input type="text" name="Feedback_CashierOrderRemark1" maxlength="255" class="input-text-per" value="<{$rel2.bCashierOrderRemark}>" disabled="disabled">
                                            </td>
                                        </tr>
                                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>回饋金備註︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_Renote1" rows="5" class="input-text-per" disabled="disabled"><{$rel2.bRenote}></textarea>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>備註說明︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_CashierOrderMemo1" rows="5" class="input-text-per" disabled="disabled"><{$rel2.bCashierOrderMemo}></textarea>
                                                 <input type="hidden" name="data_feedData1" value="<{$data_feedDataCount2}>">
                                            </td>
                                        </tr>
                                        </table>
                                        <div id="branchFeedData1">
                                            <table border="0" width="100%"  class="show_2_realty" style="display:<{$second_branch}>;">
                                                <{foreach from=$data_feedData2 key=key item=item}>
                                                <tr>
                                                    <th>回饋金對象資料</th>
                                                    <td colspan="5">
                                                        <table width="98%">
                                                            
                                                            <tr>
                                                                <td width="10%" align="center" class="tb-title2 th_title_sml">回饋<br>方式</td>
                                                                <td>
                                                                   

                                                                     <{html_options name="fFeedBack" options=$menu_categoryrecall selected=$item.fFeedBack disabled="disabled"}>
                                                                </td>
                                                                <td width="10%" align="center" class="tb-title2 th_title_sml">姓名/<br>抬頭</td>
                                                                <td>
                                                                    <input type="text" maxlength="15" class="input-text-big th_title_sml" value="<{$item.fTitle}>" disabled="disabled" style="width:200px"/>
                                                                </td>
                                                                <td width="10%" align="center" class="th_title_sml tb-title2">店長行<br>動電話</td>
                                                                <td><input type="text" maxlength="10" class="input-text-big" value="<{$item.fMobileNum}>" disabled="disabled"/></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">身份別</td>
                                                                <td>
                                                                   <{html_options name="fIdentity" options=$menu_categoryidentify selected=$item.fIdentity disabled="disabled"}>
                                                                </td>
                                                                <td align="center" class="th_title_sml tb-title2">證件<br>號碼</td>
                                                                <td>
                                                                    <input type="text" maxlength="15" class="input-text-big" value="<{$item.fIdentityNumber}>" disabled="disabled"/>
                                                                    
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">聯絡<br>地址</td>
                                                                <td colspan="5">
                                                                   
                                                                    <input type="text" maxlength="6" value="<{$item.fZipC}>" class="input-text-sml text-center" readonly="readonly" value="" disabled="disabled"/>
                                                                    <span id="FeedBaseC">
                                                                        <select class="input-text-big" disabled="disabled" name="FeedBack_basecity">
                                                                           <{$item.countryC}>
                                                                        </select>
                                                                    </span>
                                                                    <span id="FeedBaseA">
                                                                    <select class="input-text-big" disabled="disabled" ame="FeedBack_basearea">
                                                                        <{$item.areaC}>
                                                                    </select>
                                                                    </span>
                                                                    <input style="width:500px;" value="<{$item.fAddrC}>" name="FeedBack_baseaddr" disabled="disabled"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">戶藉<br>地址</td>
                                                                <td colspan="5">
                                                                    
                                                                    <input type="text" maxlength="6" class="input-text-sml text-center" name="FeedBack_regzip" readonly="readonly" value="<{$item.fZipR}>" disabled="disabled"/>
                                                                    <span id="FeedRegistC">
                                                                        <select class="input-text-big" disabled="disabled" name="FeedBack_regcity">
                                                                            <{$item.countryR}>
                                                                        </select>
                                                                    </span>
                                                                    <span id="FeedRegistA">
                                                                    <select class="input-text-big" disabled="disabled" name="FeedBack_regarea">
                                                                        <{$item.areaR}>
                                                                    </select>
                                                                    </span>
                                                                    <input style="width:500px;"  value="<{$item.fAddrR}>"  disabled="disabled"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">電子<br>郵件</td>
                                                                <td colspan="3">
                                                                    <input type="text" maxlength="255" class="input-text-per" value="<{$item.fEmail}>" disabled="disabled"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">總行</td>
                                                                <td>
                                                                    <{html_options name="d" options=$menu_bank selected=$item.fAccountNum disabled="disabled" style="width:250px;"}>
                                                                </td>
                                                                <td align="center"  class="th_title_sml tb-title2">分行</td>
                                                                <td>
                                                                    <span id="Feed_branch">
                                                                        <select class="input-text-per" disabled="disabled" name="FeedBack_bankbranch">
                                                                        <{$item.bank_branch}>
                                                                        </select>
                                                                    </span>
                                                                </td>
                                                                <td align="center" class="th_title_sml tb-title2">指定<br>帳號</td>
                                                                <td>
                                                                    <input type="text" maxlength="14" class="input-text-per" name="FeedBack_acc" value="<{$item.fAccount}>" disabled="disabled"/>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td align="center" class="th_title_sml tb-title2">戶名</td>
                                                                <td>
                                                                    <input type="text" maxlength="20" class="input-text-per" name="FeedBack_accname" value="<{$item.fAccountName}>" disabled="disabled"/>
                                                                </td>
                                                                <th>發票種類︰</th>
                                                                <td><input type="text"  value="<{$item.fNote}>" disabled="disabled"></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                            <tr><td colspan="6"><hr></td></tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <{/foreach}>
                                            </table>
                                        </div>
                                        
                                        <table border="0" width="100%" class="show_3_realty" >
                                        <{if $data_realstate.cBranchNum2 == 0 && $data_realstate.cBranchNum1 != 0}>
                                        <tr class="show_2_realty"><td colspan="6" style="text-align:right;"><span id="addBranch3"><a href="#" style="font-size:9pt;" onclick="addBranchList('3')">(第三組仲介)</a></span></td></tr>
                                        <{/if}>
                                        <tr class="show_3_realty"  style="display:<{$third_branch}>;">
                                            <td colspan="6" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4}>
                                                仲介資料 <{html_radios name="cServiceTarget2" options=$STargetOption selected=$cServiceTarget2 seperator=' ' onClick = "feedback_money()"}>
                                                <{else}>
                                                仲介資料 <{html_radios name="cServiceTarget2" options=$STargetOption selected=$cServiceTarget2 seperator=' ' disabled=disabled}>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                        <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4}>
                                            <th><span class="sign-red">*</span>仲介品牌︰</th>
                                            <td>
                                                <{html_options name="realestate_brand2" options=$menu_brand selected=$data_realstate.cBrand2 }>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介商類型︰</th>
                                            <td> <{html_options name="realestate_branchcategory2" options=$menu_categoryrealestate selected=$data_realstate.bCategory2 }></td>
                                            <th><span class="sign-red">*</span>仲介店名︰</th>
                                            <td>
                                                <input type="hidden" name="realestate_branchnum2" value="<{$data_realstate.cBranchNum2}>" />
                                               
                                                 <input type="hidden" name="branch_status2" value="<{$realestate_status2}>">
                                                <span id="realestate_branch2R">
                                                <select class="realty_branch" name="realestate_branch2">
                                                    <{$branch_options2}>
                                                </select>
                                                </span>
                                            </td>
                                        <{else}>
                                            <th><span class="sign-red">*</span>仲介品牌︰</th>
                                            <td>
                                                 <input type="hidden" name="realestate_brand2" value="<{$data_realstate.cBrand2}>" >
                                                    <input type="text" value="<{$brand_type3}>" disabled=disabled>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介商類型︰</th>
                                            <td> 
                                                <input type="hidden" name="realestate_branchcategory2" value="<{$data_realstate.bCategory2}>">
                                                <input type="text" value="<{$branch_cat3}>" disabled=disabled>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介店名︰</th>
                                            <td>
                                                 <input type="hidden" name="realestate_branchnum2" value="<{$data_realstate.cBranchNum2}>" />
                                                
                                                    <input type="text" value="<{$branch_type3}>" disabled=disabled>
                                                   
                                            </td>
                                        <{/if}>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>仲介公司名稱︰</th>
                                            <td>
                                                <input type="text" name="realestate_name2" maxlength="10" class="input-text-pre" value="<{$data_realstate.cName2}>" disabled='disabled' />
                                            </td>
                                            <th>統一編號︰</th>
                                            <td>
                                                <input type="text" name="realestate_serialnumber2" maxlength="10" class="input-text-big" value="<{$rel3.bSerialnum}>" disabled="disabled" />
                                            </td>
                                            <th>電話︰</th> 
                                            <td>
                                                <input type="text" name="realestate_telarea2" maxlength="3" class="input-text-sml" value="<{$data_realstate.cTelArea2}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_telmain2" maxlength="10" class="input-text-mid" value="<{$data_realstate.cTelMain2}>" disabled='disabled' />
                                            </td>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>傳真︰</th>
                                            <td>
                                                <input type="text" name="realestate_faxarea2" maxlength="3" class="input-text-sml" value="<{$data_realstate.cFaxArea2}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_faxmain2" maxlength="10" class="input-text-mid" value="<{$data_realstate.cFaxMain2}>" disabled='disabled' />
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseB2"><{$branchnum_data_sales2}></span></td>
                                            <td>
                                            <{if $limit_show == '0'}>
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum2}>','3')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                            <{/if}>
                                            </td>
                                            
						<{if $imgStampEdit2 == 1}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								<div id="showImg2" style="margin-top:5px;width:246px;height:145px;border: 1px solid #CCC;padding:2px;">
									<{$imgStamp2}>
								</div>
							</td>
							
						<{else}>
							
							<td rowspan="2" valign="top" style="text-align:center;">
								&nbsp;
							</td>
							
						<{/if}>
											
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>地址︰</th>
                                            <td colspan="4">
                                                <input type="hidden" name="realestate_zip2" id="realestate_zip2" value="<{$data_realstate.cZip2}>" disabled='disabled'  />
                                                <input type="text" maxlength="6" name="realestate_zip2F" id="realestate_zip2F" class="input-text-sml text-center" readonly="readonly" value="<{$data_realstate.cZip2|substr:0:3}>" disabled='disabled'  />
                                                <span id="realestate_country2R">
                                                <select class="input-text-big" name="realestate_country2" id="realestate_country2" onchange="getArea('realestate_country2','realestate_area2','realestate_zip2')" disabled='disabled' >
                                                    <{$realestate_country2}>
                                                </select>
                                                </span>
                                                <span id="realestate_area2R">
                                                <select class="input-text-big" name="realestate_area2" id="realestate_area2" onchange="getZip('realestate_area2','realestate_zip2')" disabled='disabled' >
                                                    <{$realestate_area2}>
                                                </select>
                                                </span>
                                                <input style="width:330px;" name="realestate_addr2" value="<{$data_realstate.cAddress2}>" disabled='disabled'  />
                                            </td>
                                        </tr>
                                       
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>服務費先行撥付同意書︰</th>
                                            <td>
                                                <span id="promissory3"><{$promissory3}></span>
                                                <input type="hidden" name="realestate_bRecall2" value="<{$data_realstate.bRecall2}>" />
                                                <input type="hidden" name="realestate_bScrRecall2" value="<{$data_realstate.bScrRecall2}>" />
                                            </td>
                                            <{if $smarty.session.member_pFeedBackModify!='0'}>
											
                                            <th>回饋比率</th>
                                            <td><span id="rea_bRecall2"><{$data_case.cBranchRecall2}></span>%</td>
                                            <th>代書回饋比率<br></th>
											<td><span id="rea_bScrRecall2"><{$data_case.cBranchScrRecall2}></span>%</td>
											<{else}>
											
                                            <th>&nbsp;</th>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <th>&nbsp;</th>

                                            <{/if}>
                                        </tr>
                                         <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>本票備註︰</th>
                                            <td colspan="5">
                                                <input type="text" name="Feedback_CashierOrderRemark2" maxlength="255" class="input-text-per" value="<{$rel3.bCashierOrderRemark}>" disabled="disabled">
                                            </td>
                                        </tr>
                                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>回饋金備註︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_Renote2" rows="5" class="input-text-per" disabled="disabled"><{$rel3.bRenote}></textarea>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>備註說明︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_CashierOrderMemo2" rows="5" class="input-text-per" disabled="disabled"><{$rel3.bCashierOrderMemo}></textarea>
                                                <input type="hidden" name="data_feedData2" value="<{$data_feedDataCount3}>">
                                            </td>
                                        </tr>
                                        </table>
                                        <div id="branchFeedData3">
                                        <table border="0" width="100%" class="show_3_realty" style="display:<{$third_branch}>;">
                                        <{foreach from=$data_feedData3 key=key item=item}>
                                        <tr>
                                            <th>回饋金對象資料</th>
                                            <td colspan="5">
                                                <table width="98%">
                                                    
                                                    <tr>
                                                        <td width="10%" align="center" class="tb-title2 th_title_sml">回饋<br>方式</td>
                                                        <td>
                                                            
                                                             <{html_options name="fFeedBack" options=$menu_categoryrecall selected=$item.fFeedBack disabled="disabled"}>
                                                        </td>
                                                        <td width="10%" align="center" class="tb-title2 th_title_sml">姓名/<br>抬頭</td>
                                                        <td>
                                                            <input type="text" maxlength="15" class="input-text-big th_title_sml" value="<{$item.fTitle}>" disabled="disabled" style="width:200px"/>
                                                        </td>
                                                        <td width="10%" align="center" class="th_title_sml tb-title2">店長行<br>動電話</td>
                                                        <td><input type="text" maxlength="10" class="input-text-big" value="<{$item.fMobileNum}>" disabled="disabled"/></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">身份別</td>
                                                        <td>
                                                           <{html_options name="fIdentity" options=$menu_categoryidentify selected=$item.fIdentity disabled="disabled"}>
                                                        </td>
                                                        <td align="center" class="th_title_sml tb-title2">證件<br>號碼</td>
                                                        <td>
                                                            <input type="text" maxlength="15" class="input-text-big" value="<{$item.fIdentityNumber}>" disabled="disabled"/>
                                                            
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">聯絡<br>地址</td>
                                                        <td colspan="5">
                                                           
                                                            <input type="text" maxlength="6" value="<{$item.fZipC}>" class="input-text-sml text-center" readonly="readonly" value="" disabled="disabled"/>
                                                            <span id="FeedBaseC">
                                                                <select class="input-text-big" disabled="disabled" name="FeedBack_basecity">
                                                                   <{$item.countryC}>
                                                                </select>
                                                            </span>
                                                            <span id="FeedBaseA">
                                                            <select class="input-text-big" disabled="disabled" ame="FeedBack_basearea">
                                                                <{$item.areaC}>
                                                            </select>
                                                            </span>
                                                            <input style="width:500px;" value="<{$item.fAddrC}>" name="FeedBack_baseaddr" disabled="disabled"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">戶藉<br>地址</td>
                                                        <td colspan="5">
                                                            
                                                            <input type="text" maxlength="6" class="input-text-sml text-center" name="FeedBack_regzip" readonly="readonly" value="<{$item.fZipR}>" disabled="disabled"/>
                                                            <span id="FeedRegistC">
                                                                <select class="input-text-big" disabled="disabled" name="FeedBack_regcity">
                                                                    <{$item.countryR}>
                                                                </select>
                                                            </span>
                                                            <span id="FeedRegistA">
                                                            <select class="input-text-big" disabled="disabled" name="FeedBack_regarea">
                                                                <{$item.areaR}>
                                                            </select>
                                                            </span>
                                                            <input style="width:500px;"  value="<{$item.fAddrR}>"  disabled="disabled"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">電子<br>郵件</td>
                                                        <td colspan="3">
                                                            <input type="text" maxlength="255" class="input-text-per" value="<{$item.fEmail}>" disabled="disabled"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">總行</td>
                                                        <td>
                                                            <{html_options name="d" options=$menu_bank selected=$item.fAccountNum disabled="disabled" style="width:250px;"}>
                                                        </td>
                                                        <td align="center"  class="th_title_sml tb-title2">分行</td>
                                                        <td>
                                                            <span id="Feed_branch">
                                                                <select class="input-text-per" disabled="disabled" name="FeedBack_bankbranch">
                                                                <{$item.bank_branch}>
                                                                </select>
                                                            </span>
                                                        </td>
                                                        <td align="center" class="th_title_sml tb-title2">指定<br>帳號</td>
                                                        <td>
                                                            <input type="text" maxlength="14" class="input-text-per" name="FeedBack_acc" value="<{$item.fAccount}>" disabled="disabled"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="center" class="th_title_sml tb-title2">戶名</td>
                                                        <td>
                                                            <input type="text" maxlength="20" class="input-text-per" name="FeedBack_accname" value="<{$item.fAccountName}>" disabled="disabled"/>
                                                        </td>
                                                        <th>發票種類︰</th>
                                                        <td><input type="text"  value="<{$item.fNote}>" disabled="disabled"></td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr><td colspan="6"><hr></td></tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <{/foreach}>
                                        </table>
                                        </div>
                                        <table border="0" width="100%">
                                        <tr>
                                            <td colspan="6">
                                                <br/>  
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>建檔︰</th>
                                            <td>
                                                <{if $data_bankcode.bFrom==2}>
                                                     <input type="text" name="" maxlength="10" class="input-text-mid" value="地政士" disabled="disabled"/>
                                                <{else}>
                                                     <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$case_undertaker.pName}>" disabled="disabled"/>
                                                <{/if}>
                                               
                                            </td>
                                            <th>最後修改者︰</th>
                                            <td>
                                                <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                            </td>
                                            <th>最後修改時間︰</th>
                                            <td>
                                                <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                            </td>
                                        </tr>        
                                    </table>
                                </div>
                                
                                <{if $is_edit == '1'}>                  
                                    <div id="tabs-owner">
                                        <table border="0" width="100%">
                                            <tr>
                                                <td width="14%"></td>
                                                <td width="19%"></td>
                                                <td width="14%"></td>
                                                <td width="19%"></td>
                                                <td width="16%"></td>
                                                <td width="17%"></td>
                                            </tr>
                                            <tr>
                                                <th>承辦人︰</th>
                                                <td>
                                                    <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$undertaker}>" disabled="disabled"/>
                                                </td>
                                                <th>最後修改者︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                                </td>
                                                <th>最後修改時間︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>專屬帳號︰</th>
                                                <td>
                                                    <input type="text" name="case_bankaccount" maxlength="16" class="input-text-pre" value="<{$data_case.cEscrowBankAccount}>" disabled="disabled"/>
                                                </td>
                                                <th>成交編號︰</th>
                                                <td>
                                                    <input type="text"  maxlength="10" class="input-text-big" value="<{$data_case.cDealId}>" disabled="disabled"/>
                                                </td>
                                                <td>&nbsp;</td>
                                                <td align="right">
                                                    <input type="button" value="列印賣方案件資料" class="btnD" onclick="download(2,'<{$data_owner.cIdentifyId}>')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方資料<div style="float:right;padding-right:10px;">
                                                    <!-- |&nbsp;<a href="buybehalflist.php?iden=o&cCertifyId=<{$data_case.cEscrowBankAccount}>" class="iframe" style="font-size:9pt;">編修登記名義人</a> &nbsp; -->|&nbsp;
                                                    <a href="buycontractlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修代理人</a>&nbsp;|&nbsp;
                                                        <a href="buyerownerlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修多組賣方</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>賣方帳號︰</th>
                                                <td colspan="2">
                                                    <input type="text" name="owner_identifyid" maxlength="10" style="width:120px;" class="input-text-big invoice" value="<{$data_owner.cIdentifyId}>" />
                                                    <div id="oid" style="display:inline;"></div>
                                                    <{html_radios name='owner_categoryidentify' options=$menu_categorycertifyid selected=$data_owner.cCategoryIdentify separator=' ' class="invoice"}>
                                                </td>
                                                <td colspan="3">
                                                    <div id="owner_ciden">
                                                        法定代理人<input type="text" name="owner_othername" value="<{$data_owner.cOtherName}>">
                                                    </div>
                                                    <fieldset id="foreigno" style="font-size:9pt;">
                                                        <legend style="font-size:9pt;">非本國籍身份資料</legend>
                                                        <table border="0" style="padding-left:10px;">
                                                        <tr>
                                                            <td style="font-size:9pt;">
                                                                國籍代碼：<!-- <input type="text" style="width:40px;" name="owner_country" value="<{$data_owner.cCountryCode}>">　 -->
                                                                 <input type="text" name="owner_country" style="width:35px" value="<{$data_owner.cCountryCode}>" onkeyup="getCountryCode('o')">
                                                                <{html_options name="ocountry" options=$menu_countrycode selected=$data_owner.cCountryCode class="countrycode invoice"}> 
                                                                
                                                            </td>
                                                            <td style="font-size:9pt;" >
                                                                <label style="font-size:9pt;">
                                                                    　已住滿183天?&nbsp;
                                                                    <{html_radios name="owner_resident_limit" options=$owner_resident_option selected=$owner_resident_seledted separator='　' class="invoice"}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="font-size:9pt;">
                                                                租稅協定代碼：<input type="text" style="width:40px;" name="owner_taxtreaty" value="<{$data_owner.cTaxtreatyCode}>">
                                                                給付日期：<input type="text" name="owner_payment_date" style="width:70px;" onclick="showdate(form_case.owner_payment_date)" value="<{$data_owner.cPaymentDate}>">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <{if $data_owner.cNHITax == '1'}>
                                                        <{$owner_NHI_check = ' checked="checked"'}>
                                                        <{/if}>
                                                            <td style="font-size:9pt;">
                                                                護照號碼：<input type="text" name="owner_passport" style="width:120px" value="<{$data_owner.cPassport}>">
                                                                

                                                            </td>
                                                            <td style="font-size:9pt;">
                                                                <label style="font-size:9pt;">
                                                                    　已加入健保?&nbsp;　
                                                                    <input type="checkbox" name="owner_NHITax" <{$owner_NHI_check}>>&nbsp;是
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </fieldset>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>賣方姓名︰</th>
                                                <td>
                                                    <input type="text" name="owner_name" class="input-text-per invoice" value="<{$data_owner.cName}>"/>
                                                </td>
                                                <th>出生日期︰</th>
                                                <td>
                                                    <{if $data_owner.cBirthdayDay == '0000-00-00' }>
                                                        <input type="text" name="owner_birthdayday" onclick="showdate(form_case.owner_birthdayday)" name="owner_birth" maxlength="10" class="calender input-text-big" value=""  />
                                                    <{else}>
                                                        <input type="text" name="owner_birthdayday" onclick="showdate(form_case.owner_birthdayday)" name="owner_birth" maxlength="10" class="calender input-text-big" value="<{$data_owner.cBirthdayDay}>"  />
                                                    <{/if}>

                                                </td>
                                                
                                                <td> <!-- 代理人︰ --> </td>
                                                  <td>
                                                   <!-- <input type="text" name="owner_contactname" maxlength="16" class="input-text-big" value="<{$data_owner.cContactName}>"/>  -->
                                                </td>
                                            </tr>
                                            
                                            
                                            
                                            <tr>
                                                <th><span class="sign-red">*</span>行動電話︰</th>
                                                <td>
                                                    <input type="text" name="owner_mobilenum"  maxlength="10" class="input-text-big invoice" value="<{$data_owner.cMobileNum}>" style="width:150px"/>
                                                     <input type="button" onclick="phone_edit(2)" value="更多電話" class="bt4" style="display:;width:100px;height:40px;">
                                                </td>
                                                <th>電話(1)︰</th> 
                                                <td>
                                                    <input type="text" name="owner_telarea1" maxlength="3" class="input-text-sml" value="<{$data_owner.cTelArea1}>" /> -
                                                    <input type="text" name="owner_telmain1" maxlength="10" class="input-text-mid" value="<{$data_owner.cTelMain1}>"/>
                                                </td>
                                                <th>電話(2)︰</th>
                                                <td>
                                                    <input type="text" name="owner_telarea2" maxlength="3" class="input-text-sml" value="<{$data_owner.cTelArea2}>" /> -
                                                    <input type="text" name="owner_telmain2" maxlength="10" class="input-text-mid" value="<{$data_owner.cTelMain2}>"/>
                                                </td>
                                            </tr>
                                             
                                            <tr>
                                                <th>戶藉地址︰</th>
                                                <td colspan="5">
                                                    <div style="float:left;width:60px;">&nbsp;</div>
                                                    <input type="hidden" name="owner_registzip" id="owner_registzip" value="<{$data_owner.cRegistZip}>" />
                                                    <input type="text" maxlength="6" name="owner_registzipF" id="owner_registzipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_owner.cRegistZip|substr:0:3}>" />
                                                    <select class="input-text-big invoice" name="owner_registcountry" id="owner_registcountry" onchange="getArea('owner_registcountry','owner_registarea','owner_registzip')" class="invoice">
                                                        <{$owner_registcountry}>
                                                    </select>
                                                    <span id="owner_registareaR">
                                                    <select class="input-text-big invoice" name="owner_registarea" id="owner_registarea" onchange="getZip('owner_registarea','owner_registzip')" class="invoice">
                                                        <{$owner_registarea}>
                                                    </select>
                                                    </span>
                                                    <input style="width:330px;" name="owner_registaddr" value="<{$data_owner.cRegistAddr}>" class="invoice"/>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th><span class="sign-red">*</span>通訊地址︰</th>
                                                <td colspan="5">
                                                    <div style="float:left;width:60px;">
                                                    <{if $data_owner.cRegistZip == $data_owner.cBaseZip && $data_owner.cRegistAddr == $data_owner.cBaseAddr && $data_owner.cBaseZip != '' && $data_owner.cBaseAddr != ''}>                                  
                                                        <input type="checkbox" id="sync_owneraddr" class="invoice" checked > 同上  
                                                    <{else}>
                                                        <input type="checkbox" id="sync_owneraddr" class="invoice"> 同上
                                                    <{/if}>
                                                    </div>
                                                    <input type="hidden" name="owner_basezip" id="owner_basezip" value="<{$data_owner.cBaseZip}>" />
                                                    <input type="text" maxlength="6" name="owner_basezipF" id="owner_basezipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_owner.cBaseZip|substr:0:3}>" />
                                                    <select class="input-text-big invoice" name="owner_basecountry" id="owner_basecountry" onchange="getArea('owner_basecountry','owner_basearea','owner_basezip')">
                                                        <{$owner_basecountry}>
                                                    </select>
                                                    <span id="owner_baseareaR">
                                                    <select class="input-text-big invoice" name="owner_basearea" id="owner_basearea" onchange="getZip('owner_basearea','owner_basezip')">
                                                        <{$owner_basearea}>
                                                    </select>
                                                    </span>
                                                    <input style="width:330px;" name="owner_baseaddr" value="<{$data_owner.cBaseAddr}>" class="invoice"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>前順位金額︰</th>
                                                <td>
                                                    NT$<input type="text" name="owner_money1" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_owner.cMoney1}>" />元
                                                </td>
                                               <!--  <th>代償總金額︰</th>
                                                <td>
                                                    NT$<input type="text" name="owner_money2" maxlength="255" size="12" class="currency-money1 text-right" value="<{$data_owner.cMoney2}>" />元
                                                </td> -->
                                                <th class="th_title_sml">專戶代償金額︰</th>
                                                <td>
                                                    NT$<input type="text" name="owner_money3" maxlength="255" size="10" class="currency-money1 text-right" value="<{$data_owner.cMoney3}>" onblur="OwnerMoney()"/>元
                                                </td>
                                                <th class="th_title_sml">買方銀行代償金額︰</th>
                                                <td>NT$<input type="text" name="owner_money4" maxlength="255" size="6" class="currency-money1 text-right" value="<{$data_owner.cMoney4}>" onblur="OwnerMoney()"/>元</td>
                                               
                                            </tr>
                                            <tr>
                                                <th>代償總金額︰</th>
                                                <td colspan="5">NT$<input type="text" name="owner_money5" maxlength="255" size="10" class="currency-money1 text-right" value="<{$data_owner.cMoney5}>" readonly/>元</td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方解匯資料<div style="float:right;padding-right:10px;">
                                                      <a href="#" onclick="addBankList('owner')">新增</a>  
                                                      <input type="hidden" name="owner_bank_count" value="<{$owner_bank_count+1}>">    
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯總行(1)︰</th>
                                                <td>
                                                    <{html_options name=owner_bankkey options=$menu_bank selected=$data_owner.cBankKey2 class="invoice"}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行(1)︰</th>
                                                <td colspan="3">
                                                    <select name="owner_bankbranch" class="input-text-per invoice">
                                                    <{$owner_menu_branch}>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯帳號(1)︰</th>
                                                <td><input type="text" name="owner_bankaccnumber" style="width:200px;" class="input-text-big invoice" value="<{$data_owner.cBankAccNumber}>" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶(1)︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="owner_bankaccname" class="input-text-big invoice" style="width:400px;" value="<{$data_owner.cBankAccName}>" />
                                                </td>

                                            </tr>
                                            <!--動態 -->
                                            
                                            <tr class="ownercopy2 delownercopy">
                                                <td colspan="6"><hr><input type="hidden" name="owner_bankid2[]" value="<{$owner_bank[0].cId}>"></td>
                                            </tr>

                                            <tr class="ownercopy2">
                                                <th><span class="sign-red">*</span>指定解匯總行<span name="ownertext">(2)</span>︰</th>
                                                <td>
                                                    <{html_options name="owner_bankkey2[]" options=$menu_bank class="invoice" id="owner_bankkey2" onchange="Bankchange('owner',2)" selected=$owner_bank[0].cBankMain}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行<span name="ownertext">(2)</span>︰</th>
                                                <td colspan="3">
                                                    <select name="owner_bankbranch2[]" class="input-text-per invoice" id="owner_bankbranch2">
                                                    <{$owner_bank[0].menu_branch}>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr class="ownercopy2">
                                                <th><span class="sign-red">*</span>指定解匯帳號<span name="ownertext">(2)</span>︰</th>
                                                <td><input type="text" name="owner_bankaccnumber2[]" style="width:200px;" class="input-text-big invoice" value="<{$owner_bank[0].cBankAccountNo}>" id="owner_bankaccnumber2" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶<span name="ownertext">(2)</span>︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="owner_bankaccname2[]" style="width:400px;" class="input-text-big invoice" value="<{$owner_bank[0].cBankAccountName}>" id="owner_bankaccname2"/>
                                                </td>
                                            </tr>
                                            <{foreach from=$owner_bank key=key item=item}>
                                                <{if $key > 0 }>
                                                <tr class="ownercopy<{$item.num}>">
                                                    <td colspan="6"><hr><input type="hidden" name="owner_bankid2[]" value="<{$item.cId}>"></td>
                                                </tr>

                                                
                                                <tr class="ownercopy<{$item.num}> delownercopy">
                                                    <th><span class="sign-red">*</span>指定解匯總行<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td>
                                                        <{html_options name="owner_bankkey2[]" options=$menu_bank class="invoice" id="owner_bankkey<{$item.num}>" onchange="Bankchange('owner',<{$item.num}>)" selected=$item.cBankMain}>
                                                    </td>
                                                    <th><span class="sign-red">*</span>指定解匯分行<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td colspan="3">
                                                        <select name="owner_bankbranch2[]" class="input-text-per invoice" id="owner_bankbranch<{$item.num}>">
                                                        <{$item.menu_branch}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                
                                                <tr class="ownercopy<{$item.num}> delownercopy">
                                                    <th><span class="sign-red">*</span>指定解匯帳號<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td><input type="text" name="owner_bankaccnumber2[]" style="width:200px;" class="input-text-big invoice" value="<{$item.cBankAccountNo}>" maxlength="14"/></td>
                                                    <th><span class="sign-red">*</span>指定解匯帳戶<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td colspan="3">
                                                        <input type="text" name="owner_bankaccname2[]" style="width:400px;" class="input-text-big invoice" value="<{$item.cBankAccountName}>" />
                                                    </td>
                                                </tr>
                                                <{/if}>
                                            <{/foreach}>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方經紀人
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(1)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentname1" maxlength="10" class="input-text-big" value="<{$data_owner.sAgentName1}>"　/>
                                                </td>
                                                <th>經紀人手機(1)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile1" maxlength="10" class="input-text-per" value="<{$data_owner.sAgentMobile1}>"/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(2)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentname2" maxlength="10" class="input-text-big" value="<{$data_owner.sAgentName2}>"　/>
                                                </td>
                                                <th>經紀人手機(2)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile2" maxlength="10" class="input-text-per" value="<{$data_owner.sAgentMobile2}>"/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(3)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentname3" maxlength="10" class="input-text-big" value="<{$data_owner.sAgentName3}>"　/>
                                                </td>
                                                <th>經紀人手機(3)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile3" maxlength="10" class="input-text-per" value="<{$data_owner.sAgentMobile3}>"/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(4)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentname4" maxlength="10" class="input-text-big" value="<{$data_owner.sAgentName4}>"　/>
                                                </td>
                                                <th>經紀人手機(4)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile4" maxlength="10" class="input-text-per" value="<{$data_owner.sAgentMobile4}>"/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                             <tr>
                                                <td colspan="6" class="tb-title">
                                                    代收款項<!--  (賣方) -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>地政士費︰</th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_scrivenermoney" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cScrivenerMoney}>"/>元
                                                <th><span class="th_title_sml">應付仲介費總額︰</span></th> 
                                                <td>
                                                    NT$<input type="text" name="expenditure_realestatemoney" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cRealestateMoney}>" onblur="check_money()"/>元
                                                </td>
                                                <th><span class="th_title_sml">先行收受仲介費︰</span></th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_advancemoney"  maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cAdvanceMoney}>"/>元
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="th_title_sml">應付仲介費餘額︰</span></th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_dealmoney" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cDealMoney}>" disabled='disabled'/>元
                                                </td>
                                                <th>折讓原因︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="expenditure_reason" maxlength="255" class="input-text-per" value="<{$data_expenditure.cReason}>"/>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方願意付贈買方之設備項目
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>燈飾︰</th>
                                                <td>
                                                    <input type="text" name="furniture_lamp" value="<{$furniture.cLamp}>" size="3">組
                                                </td>
                                                    
                                                <th>床組︰</th> 
                                                <td>
                                                    <input type="text" name="furniture_bed" value="<{$furniture.cBed}>" size="3">組
                                                </td>                                                  
                                                <th>梳妝台︰</th>
                                                <td>
                                                    <input type="text" name="furniture_dresser" value="<{$furniture.cDresser}>" size="3">座
                                                </td>  
                                            </tr>
                                            <tr>
                                                <th>熱水器︰</th>
                                                <td>
                                                    <input type="text" name="furniture_geyser" value="<{$furniture.cGeyser}>" size="3">台
                                                </td>
                                                    
                                                <th>電話︰</th> 
                                                <td>
                                                    <input type="text" name="furniture_telephone" value="<{$furniture.cTelephone}>" size="3">線
                                                </td>                                                  
                                                <th>洗衣機︰</th>
                                                <td>
                                                    <input type="text" name="furniture_washer" value="<{$furniture.cWasher}>" size="3">台
                                                </td>  
                                            </tr>
                                            <tr>
                                                <th>瓦斯爐︰</th>
                                                <td>
                                                    <input type="text" name="furniture_gasStove" value="<{$furniture.cGasStove}>" size="3">台
                                                </td>
                                                    
                                                <th>沙發︰</th> 
                                                <td>
                                                    <input type="text" name="furniture_sofa" value="<{$furniture.cSofa}>" size="3">張
                                                </td>                                                  
                                                <th>冷氣︰</th>
                                                <td>
                                                    <input type="text" name="furniture_air" value="<{$furniture.cAir}>" size="3">台
                                                </td>  
                                            </tr>
                                            <tr>
                                                <th>抽油煙機︰</th>
                                                <td>
                                                    <input type="text" name="furniture_machine" value="<{$furniture.cMachine}>" size="3">台
                                                </td>
                                                    
                                                <th>電視︰</th> 
                                                <td>
                                                    <input type="text" name="furniture_tv" value="<{$furniture.cTv}>" size="3">台
                                                </td>                                                  
                                                <th>其他︰</th>
                                                <td><input type="text" name="furniture_other" value="<{$furniture.cOther}>" size="10"></td>  
                                            </tr>
                                
                                            </table>
                                            </div>
                                             <div id="tabs-buyer">
                                            <table border="0" width="100%">
                                            <tr>
                                                <td width="14%"></td>
                                                <td width="19%"></td>
                                                <td width="14%"></td>
                                                <td width="19%"></td>
                                                <td width="16%"></td>
                                                <td width="17%"></td>
                                            </tr>
                                            <tr>
                                                <th>承辦人︰</th>
                                                <td>
                                                    <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$undertaker}>" disabled="disabled"/>
                                                </td>
                                                <th>最後修改者︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                                </td>
                                                <th>最後修改時間︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>專屬帳號︰</th>
                                                <td>
                                                    <input type="text" name="case_bankaccount" maxlength="16" class="input-text-pre" value="<{$data_case.cEscrowBankAccount}>" disabled="disabled"/>
                                                </td>
                                                <th>成交編號︰</th>
                                                <td>
                                                    <input type="text"  maxlength="10" class="input-text-big" value="<{$data_case.cDealId}>" disabled="disabled"/>
                                                </td>
                                                <td>&nbsp;</td>
                                                <td align="right"><input type="button" value="列印買方案件資料" class="btnD" onclick="download(1,'<{$data_buyer.cIdentifyId}>')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    買方資料<div style="float:right;padding-right:10px;">
                                                    |&nbsp;<a href="buybehalflist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修登記名義人</a> &nbsp;|&nbsp;
                                                        <a href="buycontractlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修代理人</a>&nbsp;|&nbsp;
                                                        <a href="buyerownerlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修多組買方</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>買方帳號︰</th>
                                                <td colspan="2">
                                                    <input type="text" name="buy_identifyid" maxlength="10" style="width:120px;" class="input-text-big invoice" value="<{$data_buyer.cIdentifyId}>"/>
                                                    <div id="bid" style="display:inline;"></div>
                                                      <{html_radios name='buy_categoryidentify' options=$menu_categorycertifyid selected=$data_buyer.cCategoryIdentify separator=' ' class='invoice'}>
                                                </td>
                                                <td colspan="3">
                                                <div id="buy_ciden">
                                                    法定代理人<input type="text" name="buyer_othername" value="<{$data_buyer.cOtherName}>">
                                                </div>
                                                   <fieldset id="foreignb" style="font-size:9pt;">
                                                        <legend style="font-size:9pt;">非本國籍身份資料</legend>
                                                        <table = border="0" style="padding-left:10px;">
                                                        <tr>
                                                            <td style="font-size:9pt;">
                                                                國籍代碼：
                                                                <input type="text" name="buyer_country" style="width:35px" value="<{$data_buyer.cCountryCode}>" onkeyup="getCountryCode('b')">
                                                                <{html_options name="bcountry" options=$menu_countrycode selected=$data_buyer.cCountryCode class="countrycode invoice" }>
                                                                <!-- <input type="text" style="width:40px;" name="buyer_country" value="<{$data_buyer.cCountryCode}>">　 -->
                                                                
                                                            </td>
                                                            <td style="font-size:9pt;">
                                                                <label style="font-size:9pt;">
                                                                    　已住滿183天?&nbsp;
                                                                    <{html_radios name="buyer_resident_limit" options=$buyer_resident_option selected=$buyer_resident_seledted separator='　' class="invoice"}>
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" style="font-size:9pt;">
                                                                租稅協定代碼：<input type="text" style="width:40px;" name="owner_taxtreaty" value="<{$data_owner.cTaxtreatyCode}>">
                                                                給付日期：<input type="text" name="buyer_payment_date" style="width:70px;" onclick="showdate(form_case.buyer_payment_date)" value="<{$data_buyer.cPaymentDate}>">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                        <{if $data_buyer.cNHITax == '1'}>
                                                        <{$buyer_NHI_check = ' checked="checked"'}>
                                                        <{/if}>
                                                            <td style="font-size:9pt;">
                                                                護照號碼：<input type="text" name="buyer_passport" style="width:120px" value="<{$data_buyer.cPassport}>">
                                                                
                                                            </td>
                                                            <td style="font-size:9pt;">
                                                                <label style="font-size:9pt;">
                                                                    　已加入健保?&nbsp;　
                                                                    <input type="checkbox" name="buyer_NHITax" <{$buyer_NHI_check}>>&nbsp;是
                                                                </label>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </fieldset>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>買方姓名︰</th>
                                                <td>
                                                    <input type="text" name="buy_name" class="input-text-per invoice" value="<{$data_buyer.cName}>" />
                                                </td>
                                                <th>出生日期︰</th>
                                                <td>
                                                    <{if $data_buyer.cBirthdayDay == '0000-00-00' }>
                                                                        <input type="text" name="buy_birthdayday" onclick="showdate(form_case.buy_birthdayday)" maxlength="10" class="calender input-text-big" value=""  />
                                                    <{else}>
                                                                        <input type="text" name="buy_birthdayday" onclick="showdate(form_case.buy_birthdayday)" maxlength="10" class="calender input-text-big" value="<{$data_buyer.cBirthdayDay}>"  />
                                                    <{/if}>

                                                </td>
                                                <th>授權對象</th>
                                                <td><input type="text" name="buyer_authorized" value="<{$data_buyer.cAuthorized}>"></td>
                                               
                                            </tr>
                                            
                                            

                                            <tr>
                                                <th><span class="sign-red">*</span>行動電話︰</th>
                                                <td>
                                                    <input type="text" name="buy_mobilenum" maxlength="10" class="input-text-big invoice" value="<{$data_buyer.cMobileNum}>" style="width:150px"/>
                                                    <input type="button" onclick="phone_edit(1)" value="更多電話" class="bt4" style="display:;width:100px;height:40px;">


                                                </td>
                                                <th>電話(1)︰</th> 
                                                <td>
                                                    <input type="text" name="buy_telarea1" maxlength="3" class="input-text-sml" value="<{$data_buyer.cTelArea1}>" /> -
                                                    <input type="text" name="buy_telmain1" maxlength="10" class="input-text-mid" value="<{$data_buyer.cTelMain1}>" />
                                                </td>
                                                <th>電話(2)︰</th>
                                                <td>
                                                    <input type="text" name="buy_telarea2" maxlength="3" class="input-text-sml" value="<{$data_buyer.cTelArea2}>"/> -
                                                    <input type="text" name="buy_telmain2" maxlength="10" class="input-text-mid" value="<{$data_buyer.cTelMain2}>"/>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th>戶藉地址︰</th>
                                                <td colspan="5">
                                                    <div style="float:left;width:60px;">&nbsp;</div>
                                                    <input type="hidden" name="buyer_registzip" id="buyer_registzip" value="<{$data_buyer.cRegistZip}>" />
                                                    <input type="text" maxlength="6" name="buyer_registzipF" id="buyer_registzipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_buyer.cRegistZip|substr:0:3}>" />
                                                    <select class="input-text-big invoice" name="buyer_registcountry" id="buyer_registcountry" onchange="getArea('buyer_registcountry','buyer_registarea','buyer_registzip')" >
                                                        <{$buyer_registcountry}>
                                                    </select>
                                                    <span id="buyer_registareaR">
                                                    <select class="input-text-big invoice" name="buyer_registarea" id="buyer_registarea" onchange="getZip('buyer_registarea','buyer_registzip')" class="invoice">
                                                        <{$buyer_registarea}>
                                                    </select>
                                                    </span>
                                                    <input style="width:330px;" name="buyer_registaddr" value="<{$data_buyer.cRegistAddr}>" class="invoice"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>通訊地址︰</th>
                                                <td colspan="5">
                                                    <div style="float:left;width:60px;">
                                                    <{if $data_buyer.cRegistZip == $data_buyer.cBaseZip && $data_buyer.cRegistAddr == $data_buyer.cBaseAddr && $data_buyer.cBaseZip != '' && $data_buyer.cBaseAddr != ''}>
                                                        <input type="checkbox" id="sync_buyeraddr" class="invoice" checked> 同上
                                                    <{else}>
                                                        <input type="checkbox" id="sync_buyeraddr" class="invoice"> 同上
                                                    <{/if}>
                                                    </div>
                                                    <input type="hidden" name="buyer_basezip" id="buyer_basezip" value="<{$data_buyer.cBaseZip}>" />
                                                    <input type="text" maxlength="6" name="buyer_basezipF" id="buyer_basezipF" class="input-text-sml text-center" readonly="readonly" value="<{$data_buyer.cBaseZip|substr:0:3}>" />
                                                    <select class="input-text-big invoice" name="buyer_basecountry" id="buyer_basecountry" onchange="getArea('buyer_basecountry','buyer_basearea','buyer_basezip')" >
                                                        <{$buyer_basecountry}>
                                                    </select>
                                                    <span id="buyer_baseareaR">
                                                    <select class="input-text-big invoice" name="buyer_basearea" id="buyer_basearea" onchange="getZip('buyer_basearea','buyer_basezip')" >
                                                        <{$buyer_basearea}>
                                                    </select>
                                                    </span>
                                                    <input style="width:330px;" name="buyer_baseaddr" value="<{$data_buyer.cBaseAddr}>" class="invoice"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    買方解匯資料<div style="float:right;padding-right:10px;">
                                                      <a href="#" onclick="addBankList('buyer')">新增</a>  
                                                      <input type="hidden" name="buyer_bank_count" value="<{$buyer_bank_count+1}>">    
                                                    </div>
                                                </td>
                                            </tr>
                                           
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯總行(1)︰</th>
                                                <td>
                                                    <{html_options name=buyer_bankkey options=$menu_bank selected=$data_buyer.cBankKey2 class="invoice"}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行(1)︰</th>
                                                <td colspan="3">
                                                    <select name="buyer_bankbranch" class="input-text-per invoice">
                                                    <{$buyer_menu_branch}>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr >
                                                <th><span class="sign-red">*</span>指定解匯帳號(1)︰</th>
                                                <td><input type="text" name="buyer_bankaccnumber" style="width:200px;" class="input-text-big invoice" value="<{$data_buyer.cBankAccNumber}>" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶(1)︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="buyer_bankaccname" style="width:400px;" class="input-text-big invoice" value="<{$data_buyer.cBankAccName}>" />
                                                </td>
                                            </tr>
                                            <!--動態 -->
                                            
                                            <tr class="buyercopy2 ">
                                                <td colspan="6"><hr><input type="hidden" name="buyer_bankid2[]" value="<{$buyer_bank[0].cId}>"></td>
                                            </tr>

                                            <tr class="buyercopy2 ">
                                                <th><span class="sign-red">*</span>指定解匯總行<span name="buyertext">(2)</span>︰</th>
                                                <td>
                                                    <{html_options name="buyer_bankkey2[]" options=$menu_bank class="invoice" id="buyer_bankkey2" onchange="Bankchange('buyer',2)" selected=$buyer_bank[0].cBankMain}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行<span name="buyertext">(2)</span>︰</th>
                                                <td colspan="3">
                                                    <select name="buyer_bankbranch2[]" class="input-text-per invoice" id="buyer_bankbranch2">
                                                    <{$buyer_bank[0].menu_branch}>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr class="buyercopy2 ">
                                                <th><span class="sign-red">*</span>指定解匯帳號<span name="buyertext">(2)</span>︰</th>
                                                <td><input type="text" name="buyer_bankaccnumber2[]" style="width:200px;" class="input-text-big invoice" value="<{$buyer_bank[0].cBankAccountNo}>" maxlength="14" id="buyer_bankaccnumber2"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶<span name="buyertext">(2)</span>︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="buyer_bankaccname2[]" style="width:400px;" class="input-text-big invoice" value="<{$buyer_bank[0].cBankAccountName}>" id="buyer_bankaccname2" />
                                                </td>
                                            </tr>
                                            <{foreach from=$buyer_bank key=key item=item}>
                                                <{if $key > 0 }>
                                                <tr class="buyercopy<{$item.num}>">
                                                    <td colspan="6"><hr><input type="hidden" name="buyer_bankid2[]" value="<{$item.cId}>"></td>
                                                </tr>

                                                
                                                <tr class="buyercopy<{$item.num}> delbuyercopy">
                                                    <th><span class="sign-red">*</span>指定解匯總行<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td>
                                                        <{html_options name="buyer_bankkey2[]" options=$menu_bank class="invoice" id="buyer_bankkey<{$item.num}>" onchange="Bankchange('buyer',<{$item.num}>)" selected=$item.cBankMain}>
                                                    </td>
                                                    <th><span class="sign-red">*</span>指定解匯分行<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td colspan="3">
                                                        <select name="buyer_bankbranch2[]" class="input-text-per invoice" id="buyer_bankbranch<{$item.num}>">
                                                        <{$item.menu_branch}>
                                                        </select>
                                                    </td>
                                                </tr>
                                                
                                                <tr class="buyercopy<{$item.num}> delbuyercopy">
                                                    <th><span class="sign-red">*</span>指定解匯帳號<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td><input type="text" name="buyer_bankaccnumber2[]" style="width:200px;" class="input-text-big invoice" value="<{$item.cBankAccountNo}>" maxlength="14"/></td>
                                                    <th><span class="sign-red">*</span>指定解匯帳戶<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td colspan="3">
                                                        <input type="text" name="buyer_bankaccname2[]" style="width:400px;" class="input-text-big invoice" value="<{$item.cBankAccountName}>" />
                                                    </td>
                                                </tr>
                                                <{/if}>
                                            <{/foreach}>
                                            
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    買方經紀人
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(1)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentname1" maxlength="10" class="input-text-big" value="<{$data_buyer.sAgentName1}>"　/>
                                                </td>
                                                <th>經紀人手機(1)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile1" maxlength="10" class="input-text-per" value="<{$data_buyer.sAgentMobile1}>"　/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(2)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentname2" maxlength="10" class="input-text-big" value="<{$data_buyer.sAgentName2}>"　/>
                                                </td>
                                                <th>經紀人手機(2)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile2" maxlength="10" class="input-text-per" value="<{$data_buyer.sAgentMobile2}>"　/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(3)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentname3" maxlength="10" class="input-text-big" value="<{$data_buyer.sAgentName3}>"　/>
                                                </td>
                                                <th>經紀人手機(3)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile3" maxlength="10" class="input-text-per" value="<{$data_buyer.sAgentMobile3}>"　/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>經紀人姓名(4)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentname4" maxlength="10" class="input-text-big" value="<{$data_buyer.sAgentName4}>"　/>
                                                </td>
                                                <th>經紀人手機(4)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile4" maxlength="10" class="input-text-per" value="<{$data_buyer.sAgentMobile4}>"　/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    代收款項 <!-- (買方) -->
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>地政士費︰</th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_scrivenermoney_buyer" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cScrivenerMoneyBuyer}>"/>元
                                                <th><span class="th_title_sml">應付仲介費總額︰</span></th> 
                                                <td>
                                                    NT$<input type="text" name="expenditure_realestatemoney_buyer" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cRealestateMoneyBuyer}>" onblur="check_money()"/>元
                                                </td>
                                                <th><span class="th_title_sml">先行收受仲介費︰</span></th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_advancemoney_buyer"  maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cAdvanceMoneyBuyer}>"/>元
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="th_title_sml">應付仲介費餘額︰</span></th>
                                                <td>
                                                    NT$<input type="text" name="expenditure_dealmoney_buyer" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_expenditure.cDealMoneyBuyer}>" disabled='disabled'/>元
                                                </td>
                                                <th>折讓原因︰</th>
                                                <td colspan="3">
                                                    <input type="text" name="expenditure_reason_buyer" maxlength="255" class="input-text-per" value="<{$data_expenditure.cReasonBuyer}>"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    <br/>  
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>建檔︰</th>
                                                <td>
                                                    <{if $data_bankcode.bFrom==2}>
                                                        <input type="text" name="" maxlength="10" class="input-text-mid" value="地政士" disabled="disabled"/>
                                                    <{else}>
                                                        <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$case_undertaker.pName}>" disabled="disabled"/>
                                                    <{/if}>
                                                    
                                                </td>
                                                <th>最後修改者︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                                </td>
                                                <th>最後修改時間︰</th>
                                                <td>
                                                    <input type="text" maxlength="10" class="input-text-per" value="<{$data_case.cLastTime}>" disabled="disabled"/>
                                                </td>
                                            </tr>
                                        </table>      
                                    </div>
                                    <{if $smarty.session.pBusinessView == '1'}>
                                        <{if $smarty.session.member_pDep == 7}>
                                             <{assign var='sales_dis' value='disabled=disabled'}> 
                                            
                                        <{/if}>
                                        <div id="tabs-sales">
                                            <table border="0" width="100%">
                                                <tr>
                                                    <th style="width:100px;">業務資訊：</th>
                                                    <td style="line-height: 30px;">
                                                    
                                                    <{if $scrivener_sales !=''}>

                                                        <{$scrivener_office}>：<b><{$scrivener_sales}></b>
                                                    <{/if}><br>
                                                    
                                                    <{if $branchnum_sales !=''}>    
                                                        <{$branch_type1}>：
                                                        <{if $data_realstate.cBranchNum != 505}>
                                                        <b><{$branchnum_data_sales}></b>
                                                        <{/if}>    
                                                    <{/if}><br>

                                                    <{if $branchnum_sales1 !=''}>   
                                                        <{$branch_type2}>：
                                                        <{if $data_realstate.cBranchNum1 != 505}>
                                                        <b><{$branchnum_data_sales1}></b>
                                                        <{/if}>
                                                    <{/if}><br>
                                                    <{if $branchnum_sales2 !=''}>   
                                                        <{$branch_type3}>：
                                                        <{if $data_realstate.cBranchNum2 != 505}>
                                                        <b><{$branchnum_data_sales2}></b>
                                                        <{/if}>
                                                    <{/if}>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width:100px;">業績歸屬：</th>
                                                    <td style="line-height: 30px;">
                                                    <{if $data_case.cSignCategory==1}>
                                                        <{if $branchnum_sales !=''}>
                                                            <{if $data_case.cFeedbackTarget == 1}>
                                                                <{$branch_type1}>
                                                            <{else}>
                                                                <{$scrivener_office}>
                                                            <{/if}>
                                                           <!--  <{if $data_realstate.cBranchNum1==505}>
                                                            (地政士)
                                                            <{/if}>    -->                                      
                                                            ：
                                                            <{html_options name="option_sales1" options=$sales_option}>
                                                            <input type="button" value="Add" onclick="add(<{$data_realstate.cBranchNum}>,1,<{$data_case.cFeedbackTarget}>)" <{$sales_dis}>>
                                                            &nbsp;&nbsp;<span id="salesList1"><{$sales1}> </span>
                                                            <br>
                                                        <{/if}>
                                                        <{if $branchnum_sales1 !=''}>
                                                            <{if $data_case.cFeedbackTarget1 == 1}>
                                                                <{$branch_type2}>
                                                            <{else}>
                                                                <{$scrivener_office}>
                                                            <{/if}>
                                                            <!-- <{$branch_type2}> -->
                                                          <!--   <{if $data_realstate.cBranchNum1==505}>
                                                            (地政士)
                                                            <{/if}> -->
                                                            ：
                                                            <{html_options name="option_sales2" options=$sales_option}>
                                                            <input type="button" value="Add" onclick="add(<{$data_realstate.cBranchNum1}>,2,<{$data_case.cFeedbackTarget1}>)" <{$sales_dis}>>
                                                            &nbsp;&nbsp;<span id="salesList2"><{$sales2}> </span>
                                                            <br>
                                                        <{/if}>
                                                        <{if $branchnum_sales2 !=''}>
                                                            <{if $data_case.cFeedbackTarget2 == 1}>
                                                                <{$branch_type3}>
                                                            <{else}>
                                                                <{$scrivener_office}>
                                                            <{/if}>
                                                          <!--   <{$branch_type3}>
                                                            <{if $data_realstate.cBranchNum2==505}>
                                                            (地政士)
                                                            <{/if}> -->
                                                            ：
                                                            <{html_options name="option_sales3" options=$sales_option}>
                                                            <input type="button" value="Add" onclick="add(<{$data_realstate.cBranchNum2}>,3,<{$data_case.cFeedbackTarget2}>)" <{$sales_dis}>>
                                                            &nbsp;&nbsp;<span id="salesList3"><{$sales3}> </span>
                                                            <br>
                                                        <{/if}> 
                                                    <{/if}>    
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    <{/if}>

                                <{/if}>
                                <div id="tabs-backmoney">
                                    <table width="100%" border="0">
                                        
                                        <tr>
                                            <td colspan="6" class="tb-title">回饋對象</td>
                                        </tr>
                                        <tr>
                                            <th width="10%">仲介店名︰</th>
                                            <td colspan="4"><label id='bt'><{$branch_type1}></label></td>
                                            <td width="20%" >保證費金額:<font color="red"><{$data_income.cCertifiedMoney}></font></td>   
                                        </tr>
                                         <tr>
                                            <th width="10%">案件回饋︰</th>
                                            <td width="22%">
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback" <{$feedback.1}> value="0">&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio()" onblur="checkfeed()" style="width:80px;text-align:right;" name="cCaseFeedBackMoney"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney}>" >&nbsp;元
                                            </td>
                                            <td width="10%">
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback" <{$feedback.2}> value="1">&nbsp;不回饋
                                                <input type="hidden" name="cCaseFeedBackModifier" value="<{$data_case.cCaseFeedBackModifier}>">
                                                <input type="hidden" name="cCaseFeedBackModifyTime" value="">
                                            </td>
                                            <th width="10%">回饋對象︰</th>
                                            <td width="15%">
                                                <input type="radio" id="FBT1" onclick="sync_radio()" name="cFeedbackTarget"<{$fbcheckedR.1}> value="1"<{$fbDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2" onclick="sync_radio()" name="cFeedbackTarget"<{$fbcheckedS.2}> value="2"<{$fbDisabled}>>&nbsp;地政士
                                            </td>
                                            <td rowspan="3"  align="center" valign="center">
                                                <{if $data_case.cCaseFeedBackModifier !=''}>
                                                    <div style="color:red;font-size:20px;">已手動更改</div>
                                                    <!-- <font color="red"></font> -->
                                                <{/if}>
                                            </td>
                                           
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th width="20%">仲介店名︰</th>
                                            <td colspan="5"><label id='bt1'><{$branch_type2}></label></td>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>案件回饋︰</th>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback1" <{$feedback.11}> value="0">&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio()" onblur="checkfeed()" style="width:80px;text-align:right;" name="cCaseFeedBackMoney1"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney1}>">&nbsp;元
                                            </td>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback1" <{$feedback.12}> value="1">&nbsp;不回饋
                                            </td>
                                            <th>回饋對象︰</th>
                                            <td>
                                                <input type="radio" id="FBT1"  name="cFeedbackTarget1" onclick="sync_radio()" <{$fbcheckedR.11}> value="1"<{$fbDisabled}> >&nbsp;仲介
                                                <input type="radio" id="FBT2"  name="cFeedbackTarget1" onclick="sync_radio()" <{$fbcheckedS.12}> value="2"<{$fbDisabled}> >&nbsp;地政士
                                            </td>
                                            
                                            
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th width="20%">仲介店名︰</th>
                                            <td colspan="5"><label id='bt2'><{$branch_type3}></label></td>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>案件回饋︰</th>
                                            <td>
                                                <input type="radio" onclick="sync_radio()"  <{$_disabled}> name="cCaseFeedback2" <{$feedback.21}> value="0">&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio()" onblur="checkfeed()" style="width:80px;text-align:right;" name="cCaseFeedBackMoney2"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney2}>">&nbsp;元
                                            </td>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback2" <{$feedback.22}> value="1">&nbsp;不回饋
                                            </td>
                                            <th>回饋對象︰</th>
                                            <td>
                                                <input type="radio" id="FBT1"  name="cFeedbackTarget2" onclick="sync_radio()" <{$fbcheckedR.21}> value="1"<{$fbDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2"  name="cFeedbackTarget2" onclick="sync_radio()" <{$fbcheckedS.22}> value="2"<{$fbDisabled}>>&nbsp;地政士
                                            </td>
                                            
                                            
                                        </tr>
                                     
                                        <tr id="sp_show_mpney" style="display:<{$sSpRecall}>;"> 
                                            <th>地政士事務所</th>
                                            <td colspan="2"><{$scrivener_office}></td>
                                            <th>特殊回饋︰</td>
                                            <td colspan="3"><input type="text" onchange="sync_radio()" onblur="checkfeed()" onkeyup="setMark()" style="width:80px;text-align:right;" name="cSpCaseFeedBackMoney" maxlength="8" value="<{$data_case.cSpCaseFeedBackMoney}>" <{$_disabled}> >&nbsp;元
                                            <input type="hidden" name="cSpCaseFeedBackMoneyMark" value="<{$data_case.cSpCaseFeedBackMoneyMark}>">
                                            <input type="hidden" name="cScrivenerSpRecall2" value='<{$data_case.cScrivenerSpRecall2}>'>
                                            </td>
                                        </tr>
                                        
                                    </table>
                                    <table width="100%" border="0" class="feedm">
                                        <tr>
                                            <td colspan="6" class="tb-title">其他回饋對象
                                                <div style="float:right;padding-right:10px;">
                                                    <a href="#" onclick="addOtherFeed()">新增回饋對象</a>
                                                    <input type="hidden" name="addOFeed" value="0">
                                                </div>
                                            </td>
                                        </tr>
                                        <{foreach from=$otherFeed key=key item=item}>
                                        <tr id="DOtherFeed<{$item.fId}>">
                                            <th width="10%">回饋對象：<input type="hidden" name="otherFeedId[]" value="<{$item.fId}>"><input type="hidden" name="otherFeedCheck[]" id="otherFeedCheck<{$item.fId}>"></th>
                                            <td width="15%"><{html_radios name="otherFeedType<{$item.fId}>" options=$menu_ftype selected=$item.fType  onClick="ChangeFeedStore('',<{$item.fId}>)" }></td>
                                            <th width="10%">店名：</th>
                                            <td width="35%">
                                            <select name="otherFeedstoreId<{$item.fId}>" onChange="otherFeedCg(<{$item.fId}>)" style="width:300px;" class="newfeedcheckStore<{$item.fType}>" alt="<{$item.fId}>">
                                                <{foreach from=$item.store  key=k item=i}>
                                                    <{if $item.fStoreId == $k}>
                                                        <{assign var='ck' value='selected=selected'}> 
                                                    <{else}>
                                                        <{assign var='ck' value=''}> 
                                                    <{/if}>
                                                    <option value="<{$k}>" <{$ck}>><{$i}></option>
                                                <{/foreach}>
                                            </select>
                                                
                                            </td>
                                            <th width="10%">回饋金：</th>
                                            <td width="20%"><input type="text" style="width:80px;text-align:right;" name="otherFeedMoney[]"  value="<{$item.fMoney}>" onKeyUp="otherFeedCg(<{$item.fId}>)" id="otherFeedMoney<{$item.fId}>">元
                                            <input type="button" value="刪除" onclick="delfeedmoney('',<{$item.fId}>,'')">
                                          
                                            </td>
                                        </tr>
                                        <{/foreach}>
                                        <tr id="OtherFeedcopy0" class="dis otherf"> <!-- -->
                                            <th width="10%">回饋對象：<input type="hidden" name="newotherFeedCheck[]"></th>
                                            <td><{html_radios name="newotherFeedType0" options=$menu_ftype selected="1" onClick="ChangeFeedStore('new','0')"}></td>
                                            <th width="10%">店名：</th>
                                            <td>
                                            <select name="newotherFeedstoreId0" onChange="otherFeedCg(0)" style="width:300px;" class="newfeedcheckStore1" alt="0">
                                                <{foreach from=$otherFeedStore  key=k item=i}>
                                                    
                                                    <option value="<{$k}>" <{$ck}>><{$i}></option>
                                                <{/foreach}>
                                            </select>

                                               
                                                
                                            </td>
                                            <th width="10%">回饋金：</th>
                                            <td>
                                                <input type="text" style="width:80px;text-align:right;" name="newotherFeedMoney[]" id="newotherFeedMoney0" >元
                                                <input type="button" value="刪除" id="OtherFeedDel0" onclick="delfeedmoney('new','OtherFeedcopy0','')">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            
                                            <th width="10%">最後修改人：</th>
                                            <td width="15%"><{$CaseFeedBackModifier}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <th width="10%">日期：</th>
                                            <td width="30%"><{$cCaseFeedBackModifyTime}>&nbsp;</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </table>      
                                </div>
                               

                            </form>
                    </div>

                    <center>
                            <br/>
                            
                                <{if $is_edit == 1}>
                                    <{if $limit_show == '0'}>

                                    <{/if}>

                                        <{if $data_case.cSignCategory != 2 && $smarty.session.member_modifycase==1}>
                                            <button id="save" style="width:150px;display:">儲存</button>      
                                        <{else}>
 
                                        <{/if}>
                                        <button id="ctrlform" style="width:150px;">匯出控管表</button>

                                         <{if $data_case.cSignCategory != 2}>
                                            <button id="checklist" style="width:150px;display:;">編修點交表</button>
                                             
                                        <{else}>


                                        <{/if}>
                                        <button id="servicefee" style="width:200px;">匯出服務費申請單</button>
                                        <{if $data_bankcode.bFrom == 2 }>
                                                
                                            <{if $data_case.cSignCategory == 2}>
                                                <button id="ecs">ECS</button>
                                                <button id="unecs" disabled=disabled>切換回地政士</button>
                                            <{else}>
                                                <button id="ecs" disabled=disabled>ECS</button>
                                                <button id="unecs">切換回地政士</button>
                                            <{/if}>
                                        <{/if}>
                                        

                                        <!--<button id="finishform">匯出點交表</button>-->
                                        <!--<button id="upload">上傳點交單</button>-->
                                        
                                         
                                <{else}>
                                    <{if $data_case.cSignCategory != 2}>
                                        <button id="add" style="width:150px;display:;">儲存</button>
                                    <{/if}>
                                <{/if}>
                                <button id="copy" onclick="copyCase()">複製案件</button>
                            
                    </center>
                    <input type="hidden" name="uniqid" id="uniqid" value="<{$uniqid}>" />
                    <form name="form_back" id="form_back" method="POST"  action="listbuyowner.php">
                    </form>
                    <form name="form_upload" id="form_upload" method="POST" target="_blank" action="<{$WEB_STAGE}>/upload_c.php">
                        <input type="hidden" name="cid" id="cid" value="<{$data_case.cCertifiedId}>" />
                    </form>
                                        
                    <div id="dialog"></div>

                    <div id="footer">
                        <p>2012 第一建築經理股份有限公司 版權所有</p>
                    </div>
                                
                </div>
            </div>
        </div>
    </body>
</html>



