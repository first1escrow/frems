<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <{include file='meta.inc.tpl'}>
        <link rel="stylesheet" href="/css/colorbox.css" />
        <link rel="stylesheet" type="text/css" href="/css/cmc_loading.css" />
        <link rel="stylesheet" type="text/css" href="/css/transferArea.css?v=20230816">
        <script src="/js/jquery.colorbox.js"></script>
        <script src="/js/IDCheck.js"></script>
        <script src="/js/lib/lib.js?v=6"></script>
        <script src="/js/lib/comboboxNormal.js"></script>
        <script src="/js/transferArea.js?v=20230818"></script>
        <script src="/js/escrow/invoiceDonation.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
				getMarguee(<{$smarty.session.member_id}>) ;
				setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)

                <{if $checkCertifiedFee == 0}>
                $(".checkCertifiedFee").show();
                <{/if}>
				
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

                //回饋金鎖定
                if ("<{$data_case.cFeedBackClose}>"  == 1) {
                    $(".feedbackClose").each(function() {
                        $(this).attr('disabled', true);
                    });

                    var array = "input,select,textarea";
                     $("#tabs-realty").find(array).each(function() {
                        $(this).attr('disabled', true);
                    });
                }
                //代書回饋金鎖定
                if ("<{$data_case.cFeedBackScrivenerClose}>" == 1) {
                    $(".scrivenerClose").each(function() {
                        $(this).attr('disabled', true);
                    });
                }

                //賣方備註如果已經送出給銀行就會鎖住
                $("#build").hide();

                if ("<{$data_owner.cOtherName}>"=='') {
                    $('#owner_ciden').hide();//統一編號用法定代理人(賣)
                }
               
                if ("<{$data_buyer.cOtherName}>"=='') {
                    $('#buy_ciden').hide();//統一編號用法定代理人(買)
                }

                $(".iframe").colorbox({iframe:true, width:"1200px", height:"90%",onClosed:function(){
                    getinv();
                }}) ;
                
                $('#dialog').dialog({
                    modal: true,
                    autoOpen: false,
                    buttons: {
                        "OK": function() {
                            $('#dialog').dialog('close') ;
                        }
                    }
                });
                
                checkID('o') ;              
                checkID('b') ;
                checkIdDouble();
                
                /* 檢核是否輸入賣方身分證字號 */
                $('[name="owner_identifyid"]').keyup(async function() {
                    checkID('o') ;
                    checkIdDouble('o');
                });
                
                /* 檢核是否輸入買方身分證字號 */
                $('[name="buy_identifyid"]').keyup(async function() {
                    checkID('b') ;
                    checkIdDouble('b');
                });
                
                CatchBank();
                
                $('[name=owner_bankkey]').on('change', function () {
                    GetBankBranchList($('[name=owner_bankkey]'),
                                        $('[name=owner_bankbranch]'),
                                        null);
                });

                $('[name=buyer_bankkey]').on('change', function () {
                    GetBankBranchList($('[name=buyer_bankkey]'),
                                        $('[name=buyer_bankbranch]'),
                                        null);
                });

                $(".bank select").each(function(index, el) {
                    setComboboxNormal($(this).attr("id"),'id');
                });

                //
                setCombobox("scrivener_id");
                //

                ///////////////////////////////////////////////////////////////////////////////
                $('[name=scrivener_id]').on('change', CatchScrivener);
                $('[name=property_measuremain]').on('change', SubArea);
                $('[name=property_measureext]').on('change', SubArea);
                $('[name=property_measurecommon]').on('change', SubArea);
                $('[name=invoice_splitowner]').on('click', SplitInvoice);
                $('[name=invoice_splitbuyer]').on('click', SplitInvoice);
                $('[name=invoice_splitrealestate]').on('click', SplitInvoice);
                $('[name=invoice_splitscrivener]').on('click', SplitInvoice);
                $('[name=invoice_splitother]').on('click',SplitInvoice);
                $("[name='case_cancellingClause']").on('click',function() {
                    if ($(this).val() == 0) {
                        $("[name='case_cancellingClauseNote']").val('');
                    }
                });
               
                $('#finishform').on('click', function () {
                    var id = $('[name=certifiedid]').val();
                    window.open ('/bank/report/excel_save.php?id='+id, 'newwindow', 'height=200, width=400, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no');
                });

                $('#ctrlform').on('click', function () {
                    var id = $('[name=certifiedid]').val();
                    window.open ('/bank/report/control_report.php?id='+id, 'newwindow', 'height=200, width=400, top=0, left=0, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=n o, status=no');
                });

                $('#save').on('click', function () {
                    if (!checkInvoiceClose()) {
                        alert('此案件發票已在開立階段，頁面資料已過期請重新整理');
                        return false;
                    }
                    if (!checkScrivenerClose()) {
                        alert('此案件地政士回饋金已鎖定，頁面資料已過期請重新整理');
                        return false;
                    }
                    var caseStatus = $('[name="case_status"]').val();
                    if (caseStatus ==3 || caseStatus == 4 || caseStatus == 9 || caseStatus == 10) {
                        if ($('input:checkbox:checked[name="sellerTarget[]"]').length == 0) {
                            alert("請選擇賣方出款選項");
                            $("[name='sellerTarget[]']").focus();
                            return false;
                        }
                    }

                    /* 儲存前先檢查買方是否為非本國人並警示 */
                    if (checkSaveF('b')) {
                        alert('買方外國人身分未選擇是否已住滿 "183天" !!') ;
                    }
                    
                    /* 儲存前先檢查買方是否為非本國人並警示 */
                    if (checkSaveF('o')) {
                        alert('賣方外國人身分未選擇是否已住滿 "183天" !!') ;
                    }
                   
                    if ($("[name='branch_staus']").val() == 2) {
                        alert('第一間店已關店，請重新開啟或更改店家');
                        $("#tabs-realty").click();
                        return false;
                    } else if ($("[name='branch_staus1']").val() == 2) {
                        alert('第二間店已關店，請重新開啟或更改店家');
                        $("#tabs-realty").click();
                        return false;
                    } else if ($("[name='branch_staus2']").val() == 2) {
                        alert('第三間店已關店，請重新開啟或更改店家');
                        $("#tabs-realty").click();
                        return false;
                    }

                    //檢查配件店家不能有非仲介成交
                    if ($('[name="realestate_branchnum1"]').val() > 0) {
                        if ($('[name="realestate_brand"]').val() == 2 || $('[name="realestate_brand1"]').val() == 2 || $('[name="realestate_brand2"]').val() == 2 || $('[name="realestate_brand3"]').val() == 2 || $('[name="realestate_branchnum"]').val() == 505 || $('[name="realestate_branchnum1"]').val() == 505|| $('[name="realestate_branchnum2"]').val() == 505 || $('[name="realestate_branchnum3"]').val() == 505) {
                            alert("配件禁止店家為「非仲介成交」");
                            return false;
                        }
                    }
                    
                    if ($('[name="cCaseFeedBackModifier"]').val() != '' && (<{$smarty.session.member_pCaseFeedBackModify}> == 2 )) {
                        if (($('[name="cFeedbackTarget"]:checked').val() == 2 || $('[name="cFeedbackTarget1"]:checked').val() ==2 || $('[name="cFeedbackTarget2"]:checked').val() ==2) && $("[name='cSpCaseFeedBackMoney']").val() > 0) {
                            alert("回饋對象為代書且有地政士特殊回饋!!");
                        }
                    }
                        
                    var st = $('[name="case_status"]').val();
                    if (st ==3 || st == 4 || st == 9 || st == 10) {
                        if ("<{$CertifyDate}>" != '' && $("[name='case_signdate']").val() == '') {
                            alert("請輸入簽約日");
                            return false;
                        }

                        if ($('[name="realestate_branchnum"]').val() == 0 || $('[name="realestate_branchnum"]').val() == '') {
                            alert("請選擇仲介店");
                            return false;
                        }
                    }

                    if (st == 3 || st == 10) {
                        if (st == 10) {
                            var money = "<{$data_case.cCaseMoney}>";

                            if (money == 0) {
                                alert('已結案有保留款，專戶金額不可以為0');
                                return false;
                            }
                        }

                        //20220727 案件狀態為結案時，需確認建物門牌欄位是否有填寫
                        if (st == 3) {
                            if (caseCloseCheck(st) !== true) {
                                alert('建物門牌資料不完整!!');
                                return false;
                            }
                        }

                        if ($('[name="cCaseFeedBackModifier"]').val() == '' && "<{$data_case.cFeedBackScrivenerClose}>" != 1) {
                            feedback_money();
                        }
                        
                        var invoice =  invoice_dealing2();
                        var interest = interest_dealing();
                        
                        if (invoice=='0') {
                            alert('請檢查發票是否有分配完全');
                            return false;
                        }
                        
                        if (interest =='0') {
                            alert('請檢查利息是否有分配正確');
                            return false;
                        }
                        
                        var buy_identifyid = $("[name='buy_identifyid']").val();
                        var owner_identifyid = $("[name='owner_identifyid']").val();

                        var pat = /[a-zA-Z]{2}/ ; //要調整
                        var pat2 = /[a-zA-z]{1}[8|9]{1}[0-9]{8}/;//2021新證號
                
                        if (pat.test(buy_identifyid) || pat2.test(buy_identifyid)){
                            if ($("[name='buyer_passport']").val() =='') {
                                alert("請填寫買方護照號碼");
                                $("#li-buyer").click();
                                $("[name='buyer_passport']").focus();
                                return false;
                            }

                            if ($("[name='buyer_country']").val() == '' || $("[name='buyer_country']").val() == '0') {
                                alert("請填寫買方國籍代碼");
                                $("#li-buyer").click();
                                $("[name='buyer_country']").focus();
                                return false;
                            }
                        }

                        if (pat.test(owner_identifyid) || pat2.test(owner_identifyid)) {
                            if ($("[name='owner_passport']").val() =='') {
                                alert("請填寫賣方護照號碼");
                                $("#li-owner").click();
                                $("[name='owner_passport']").focus();
                                return false;
                            }

                            if ($("[name='owner_country']").val() == '' || $("[name='owner_country']").val() == '0') {
                                alert("請填寫賣方國籍代碼");
                                $("#li-owner").click();
                                $("[name='owner_country']").focus();
                                return false;
                            }
                        }

                        if (checkAffixBracnch() == false) {
                            return false;
                        };

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
                                alert('其他買方資料不齊全，請檢查');
                                $("#li-buyer").click();
                                return false;
                            } else if (check ==2){

                                alert('其他賣方資料不齊全，請檢查');
                                $("#li-owner").click();
                                return false;
                            } else {
                                if (CheckField()) {
                                    CatchData('save');
                                }
                            }
                        });
                    } else {
                        if (st == 4) {
                            var invoice =  invoice_dealing2();
                            var interest = interest_dealing() ;
                            
                            if (invoice=='0') {
                                alert('請檢查發票是否有分配完全');
                                return false;
                            }
                            
                            if (interest =='0') {
                                alert('請檢查利息是否有分配正確');
                                return false;
                            }

                            if (checkAffixBracnch() == false) {
                                return false;
                            }
                        }

                        //20220715
                        if (terminateContract()) {
                            alert('解約案件，請確認買賣雙方的戶籍與通訊地址以及 ID 是否完整!?');
                            return false;
                        }

                        CatchData('save');
                    }
                });
                
                $('#add').on('click', function () {
                    //檢查配件店家不能有非仲介成交
                    if ($('[name="realestate_branchnum1"]').val() > 0) {
                        if ($('[name="realestate_brand"]').val() == 2 || $('[name="realestate_brand1"]').val() == 2 || $('[name="realestate_brand2"]').val() == 2 || $('[name="realestate_brand3"]').val() == 2) {
                            alert("配件禁止店家為「非仲介成交」");
                            return false;
                        }
                    }

                    //20220727 案件狀態為結案時，需確認建物門牌欄位是否有填寫
                    var st = $('[name="case_status"]').val();
                    if (st == 3) {
                        if (caseCloseCheck(st) !== true) {
                            alert('建物門牌資料不完整!!');
                            return false;
                        }
                    }
                    
                    //20220715
                    if (terminateContract()) {
                        alert('解約案件，請確認買賣雙方的戶籍與通訊地址以及 ID 是否完整!?');
                        return false;
                    }

                    CatchData('add');
                });

                var ck = $("#ecs").attr('disabled');

                if (ck != 'disabled') {
                    $("#ecs").on('click', function() {
                        var cid =$('[name="certifiedid"]').val();

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
                } else {
                    $("#unecs").on('click', function() {
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

                $("#servicefee").on('click', function(event) {
                    let cid =$('[name="certifiedid"]').val();
                    let cat = "<{$scr_sCategory}>";
                    
                    let type = "<{$data_bankcode.bApplication}>";
                    <{if 69|in_array:[$data_realstate.cBrand, $data_realstate.cBrand1, $data_realstate.cBrand2, $data_realstate.cBrand3]}>
                    $('form[name=form_fee] input[name=brand]').val(69);
                    <{/if}>

                    $('form[name=form_fee] input[name=cid]').val(cid);
                    $('form[name=form_fee] input[name=cat]').val(cat);
                    $('form[name=form_fee] input[name=type]').val(type);

                    $('form[name=form_fee]').attr('action', 'formservicefee.php');
                    $('form[name=form_fee]').submit();
                });

                $( "#tabs" ).tabs({
                    selected: <{$_tabs}>
                });

                 $('#sync_owneraddr').on('change', function () {
                     if ($('#sync_owneraddr').attr('checked') == 'checked') {
                         $('[name=owner_basecountry]').val($('[name=owner_registcountry]').val());
                         $('[name=owner_basearea]').html($('[name=owner_registarea]').html());
                         $('[name=owner_basearea]').val($('[name=owner_registarea]').val());
                         $('[name=owner_baseaddr]').val($('[name=owner_registaddr]').val());
                         $('[name=owner_basezip]').val($('[name=owner_registzip]').val());
                         $('[name=owner_basezipF]').val($('[name=owner_registzipF]').val());
                     }
                });

                $('#sync_buyeraddr').on('change', function () {
                     if ($('#sync_buyeraddr').attr('checked') == 'checked') {
                         $('[name=buyer_basecountry]').val($('[name=buyer_registcountry]').val());
                         $('[name=buyer_basearea]').html($('[name=buyer_registarea]').html());
                         $('[name=buyer_basearea]').val($('[name=buyer_registarea]').val());
                         $('[name=buyer_baseaddr]').val($('[name=buyer_registaddr]').val());
                         $('[name=buyer_basezip]').val($('[name=buyer_registzip]').val());
                         $('[name=buyer_basezipF]').val($('[name=buyer_registzipF]').val());
                     }
                });
                
                $('[name=income_signmoney]').on('blur', function () {
                     var tmp = $('[name=income_signmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_signmoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });

                $('[name=income_affixmoney]').on('blur', function () {
                     var tmp = $('[name=income_affixmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_affixmoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });

                $('[name=income_dutymoney]').on('blur', function () {
                     var tmp = $('[name=income_dutymoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_dutymoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });

                $('[name=income_estimatedmoney]').on('blur', function () {
                     var tmp = $('[name=income_estimatedmoney]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_estimatedmoney]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });

                $('[name="income_certifiedMoneyPower1"]').on('blur', function() {
                     var tmp = $('[name=income_certifiedMoneyPower1]').val();
                     if (!(/^[0-9]+\.?[0-9]{0,5}$/).test(tmp) ) {
                         $('[name=income_certifiedMoneyPower1]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });

                $('[name="income_certifiedMoneyPower2"]').on('blur', function() {
                     var tmp = $('[name=income_certifiedMoneyPower2]').val();
                     tmp = tmp.replace(/\,/g, '');
                     if ( !(/^[0-9]+$/).test(tmp) ) {
                         $('[name=income_certifiedMoneyPower2]').val('0');
                     }
                     CatchIncome();
                     SplitInvoice();
                });
                
                <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                setCombobox2("realestate_brand",'');/*第一組仲介品牌*/
                setCombobox2("realestate_brand",1);/*第二組仲介品牌*/
                setCombobox2("realestate_brand",2);/*第三組仲介品牌*/
                setCombobox2("realestate_brand",3);/*第四組仲介品牌*/

                setCombobox2("realestate_branchcategory",'');/*第一組仲介類型*/
                setCombobox2("realestate_branchcategory",1);/*第二組仲介類型*/
                setCombobox2("realestate_branchcategory",2);/*第三組仲介類型*/
                setCombobox2("realestate_branchcategory",3);/*第四組仲介類型*/

                setCombobox2("realestate_branch",'');/*第二組仲介店*/
                setCombobox2("realestate_branch",1);/*第二組仲介店*/
                setCombobox2("realestate_branch",2);/*第三組仲介店*/
                setCombobox2("realestate_branch",3);/*第四組仲介店*/
                <{/if}>

                $('.currency-money1').on('blur',function(event) {
                    setCurrencymoney();
                });
                
                $('[name=expenditure_realestatemoney]').on('blur', function () {
                    CountDelMoney();
                });
                $('[name=expenditure_advancemoney]').on('blur', function () {
                    CountDelMoney();
                });
                
                $('[name=expenditure_realestatemoney_buyer]').on('blur', function () {
                    CountDelMoney();
                });
                $('[name=expenditure_advancemoney_buyer]').on('blur', function () {
                    CountDelMoney();
                });
                
                $('#upload').on('click', function () {
                    $('#form_upload').submit();
                });
                
                $('#checklist').on('click', function () {
                    var url = '/checklist/form_list_db.php?cCertifiedId=<{$data_case.cCertifiedId}>' ;
                    $.colorbox({iframe:true, width:"1000px", height:"100%", href:url}) ;
                }) ;
                
                $('[name="income_certifiedmoney"]').change(function() {
                    feedback_money() ;
                }) ;

                //如果出租情形選擇無會把欄位清掉
                $('[name=property_rentstatus]').on('click', function () {
                    var val = $('[name=property_rentstatus]:checked').val();
                    if (val==2) {
                        $("[name=property_rentdate]").val('');
                        $("[name=property_rent]").val('');
                        $("[name=property_finish]").removeAttr('checked');
                    }
                });

                //建物編輯
                $('#new_build').on('click', function () {
                    var v = parseInt($("[name='new_buildcount']").val());
                    $.ajax({
                        url: '../includes/escrow/getBuildTpl.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {item: v,limit:<{$limit_show}>},
                    })
                    .done(function(html) {
                        $(html).insertAfter('.newP:last');
                    });
                    
                    $("[name='new_buildcount']").val((v+1));
                 });
                
                //賣方國際代碼
                $('[name=ocountry]').on('change', function () {
                    var v = $('[name=ocountry]').val();
                    $("[name='owner_country']").val(v);
                });

                //買方國際代碼
                $('[name=bcountry]').on('change', function () {
                    var v = $('[name=bcountry]').val();
                    $("[name='buyer_country']").val(v);
                });

                $('#trans_build').on('click', function () {
                   var url = "/bank/new/out1.php?vr=<{$data_case.cEscrowBankAccount}>";
                   window.open(url,'export');
                }) ;

                $('[name="buy_categoryidentify"]').on('click', function () {
                    var v = $('[name="buy_categoryidentify"]:checked').val();
                    if (v == 2) {
                        $('#buy_ciden').show();
                    }
                }) ;

                $('[name="owner_categoryidentify"]').on('click', function () {
                    var v = $('[name="owner_categoryidentify"]:checked').val();
                    if (v==2) {
                        $('#owner_ciden').show();
                    }
                }) ;

                $("[name='income_firstmoney']").on('click', function() {
                    if (confirm("填入金額後會影響保證費及回饋金，確定要輸入?")) {
                        $("[name='income_firstmoney']").on('blur', function() {
                            certifiedmoneyCount();
                        });
                    } else {
                        $("#unfo").focus();
                    }
                });
                
                //檢查勾選狀態
                $(".buyercklist").on('click', function() {
                    checkChecklist('buyer');
                });

                $(".ownercklist").on('click', function() {
                    checkChecklist('owner');
                });

                checkChecklist('buyer');
                checkChecklist('owner');
                CalculationRatio();

                //賣方金額確認-主要
                $("body").on('click', '.show_owner1_check', function(e) {
                    var cid =$('[name="certifiedid"]').val();
                    var identifyId = e.currentTarget.getAttribute("data-identifyid");
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }

                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'owner', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': identifyId, 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //賣方金額確認-其他
                $("body").on('click', '.show_owner_check', function(e) {
                    var cid = $('[name="certifiedid"]').val();
                    var identifyId = e.currentTarget.getAttribute("data-identifyid");
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }

                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'ownerOther', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': identifyId, 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //買方金額確認-主要
                $("body").on('click', '.show_buyer1_check', function(e) {
                    var cid =$('[name="certifiedid"]').val();
                    var identifyId = e.currentTarget.getAttribute("data-identifyid");
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }

                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'buyer', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': identifyId, 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //買方金額確認-其他
                $("body").on('click', '.show_buyer_check', function(e) {
                    var cid = $('[name="certifiedid"]').val();
                    var identifyId = e.currentTarget.getAttribute("data-identifyid");
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }

                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'buyerOther', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': identifyId, 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //仲介
                $("body").on('click', '.show_branch_check', function(e) {
                    var cid = $('[name="certifiedid"]').val();
                    var no = e.currentTarget.getAttribute("data-no");
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }
                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'realestate', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': '', 'no': no, 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //地政士
                $("body").on('click', '.show_scrivener1', function(e) {
                    var cid = $('[name="certifiedid"]').val();
                    var money = e.currentTarget.getAttribute("data-money");

                    if(e.target.checked) {
                        var cMoneyCheck = 1;
                    } else {
                        var cMoneyCheck = 0;
                    }
                    $.ajax({
                        url: '../includes/escrow/contractInvoiceMoneySave.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {'type': 'scrivener', 'cid': cid, 'cMoneyCheck': cMoneyCheck, 'identifyId': '', 'money': money},
                    })
                    .done(function(data) {

                    });
                });

                //
                $('.detailShow').click(function() {
                    let _target = $(this).prop('name');

                    if ($(this).prop('checked')) {
                        if (_target == 'buyer_show') {
                            _target = '#buyerDetail';
                        } else {
                            _target = '#ownerDetail';
                        }

                        $(_target).empty().html('<a href="Javascript:void(0);" style="font-size: 18px;" onclick="buyerOwnerWebDetail()">詳細</a>');
                    } else {
                        if (_target == 'buyer_show') {
                            _target = '#buyerDetail';
                        } else {
                            _target = '#ownerDetail';
                        }
                        
                        $(_target).empty().css('font-size', '18px').html('詳細');
                    }
                });

                setCombobox2("land_category",'');

                <{if $data_case.cCaseHandler == 1}>
                setTimeout(function() {
                    alert('本案件為法務關注案件！');
                }, 800);
                <{/if}>

                //設定按鍵icon
                $('#finishform').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#ctrlform').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#servicefee').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#save').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#add').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#copy').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#ecs').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#unecs').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#legalbtn').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#land_edit').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#sms').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#build_edit').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#upload').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#checklist').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });

                $('#trans_build').button( {
                    icons:{
                        primary: "ui-icon-transfer-e-w"
                    }
                });

                $('#land_price').button( {
                    icons:{
                        primary: "ui-icon-transfer-e-w"
                    }
                });
            });
            
            function checkAddr(name){
                var zip = '';
                var addr = '';
                var count = 0;
              
                if (name == 'new') {                    
                    addr = $("#property_addr"+name).val();
                    zip = $("#property_zip"+name).val();
                } else {
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
                        }
                    }

                    return;
               });
            }
            
            function getCountryCode(cat){
                var val ='';

                if (cat == 'b') {
                    val = $("[name='buyer_country']").val();
                } else if (cat == 'o'){
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
                });
                
                if (check == 'error') {
                    return false;
                } else {
                    return true;
                }
            }
            function checkScrivenerClose(){
                var close = "<{$data_case.cFeedBackScrivenerClose}>";
                var check = 1;

                $.ajax({
                    async: false, //同步處理
                    url: '/includes/escrow/check_other.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cid': "<{$data_case.cCertifiedId}>",'type':'scrivenerClose','feedBackScrivenerClose':close},
                })
                .done(function(txt) {
                    check = txt ;
                });

                if (check == 'error') {
                    return false;
                } else {
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
            }

            //
            function phone_edit(v) {
                var c = $('[name=certifiedid]').val() ;
                var url = 'formphonedit.php?t='+ v +'&cid=' + c+"&cSignCategory=<{$data_case.cSignCategory}>";
                
                $.colorbox({iframe:true, width:"750px", height:"700px", href:url}) ;
            }

            function land_price_edit(no) {
                var sign = "<{$data_case.cSignCategory}>";

                if ($('[name=is_edit]').val() == '1') {
                    if (sign==1) {
                        var catchRes = CatchData2('edit');
                        if(catchRes == false) {
                            return;
                        }
                    }

                    setTimeout(function() {
                        $('#form_land').attr('action', 'formlandprice.php?item='+no+"&cSignCategory=<{$data_case.cSignCategory}>");
                        $('#form_land [name=id]').val($('[name=certifiedid]').val());
                        
                        $('#form_land').submit();
                    }, 1000);
                } else {
                    $("#dialog-confirm11").dialog({
                        resizable: false,
                        height:200,
                        modal: true,
                        buttons: {
                            "編輯土地資料": function() {
                                if ($('[name=scrivener_bankaccount]').val() == null) {
                                    alert('請選擇保證證號!!');
                                    return;
                                } else {
                                    var catchRes = CatchData2('add');
                                    if(catchRes == false) {
                                        return;
                                    }
                                    var bkacc = $('[name=scrivener_bankaccount]').val();
                                    $('#form_land').attr('action', 'formlandprice.php?item='+no+"&cSignCategory=<{$data_case.cSignCategory}>");
                                    $('#form_land [name=id]').val(bkacc.substring(14, 5));
                                    $('#form_land').submit();
                                }
                            },
                            "取消": function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
            }
            
            function land_edit() {
                var sign = "<{$data_case.cSignCategory}>";

                if ($('[name=is_edit]').val() == '1') {
                    if (sign == 1) {
                        var catchRes = CatchData2('edit');
                        if(catchRes == false) {
                            return;
                        }
                    }
                    
                    setTimeout(function() {
                        $('#form_land').attr('action', 'formland2edit.php');
                        $('#form_land [name=id]').val($('[name=certifiedid]').val());
                        $('#form_land').submit();
                    }, 1000);
                } else {
                    $("#dialog-confirm11").dialog({
                        resizable: false,
                        height:200,
                        modal: true,
                        buttons: {
                            "編輯土地資料": function() {
                                    if ($('[name=scrivener_bankaccount]').val() == null) {
                                        alert('請選擇保證證號!!');
                                        return;
                                    } else {
                                        var catchRes = CatchData2('add');
                                        if(catchRes == false) {
                                            return;
                                        }
                                        var bkacc = $('[name=scrivener_bankaccount]').val();
                                        $('#form_land').attr('action', 'formland2edit.php');
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
                    if (sign == 1) {
                        var  catchRes = CatchData2('edit');
                        if(catchRes == false) {
                            return;
                        }
                    }

                    $('#form_build  [name=bitem]').val(item);

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
                                    var catchRes = CatchData2('add');
                                    if(catchRes == false) {
                                        return;
                                    }
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
                    if (sign == 1) {
                        var catchRes = CatchData2('edit');
                        if(catchRes == false) {
                            return;
                        }
                    }

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
                                    var catchRes = CatchData2('add');
                                    if(catchRes == false) {
                                        return;
                                    }

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
                
                setCurrencymoney();
                certifiedmoneyCount() ;
            }
            
            /* 計算保證費金額 */
            function certifiedmoneyCount() {
                var first = $("[name='income_firstmoney']").val();
                var total = $('[name=income_totalmoney]').val();

                first = parseInt(first.replace(/\,/g, ''));
                if (!(/^[0-9]+$/).test(first)) {
                    first = 0;
                }

                total = parseInt(total.replace(/\,/g, ''));
                var cer = (total-first) * 0.0006 ;
                cer = Math.round(cer) ;

                if (cer <= 600) {
                    cer = 600; 
                }

                $('[name=income_certifiedmoney]').val(cer) ;
                feedback_money() ;      //計算回饋金
            }

            function checkTranSellerNote(){
                <{if $checkOwnerNote == 0}>
                if ($('input:checkbox:checked[name="sellerTarget[]"]').length == 0) {
                    alert("請選擇賣方出款選項");
                    $("[name='sellerTarget[]']").focus();
                    return false;
                }

                if ($('input:checkbox[name="sellerTarget[]"]').filter('[value="5"]').prop('checked') == true && $("[name='sellerNote']").val() == '') {
                    alert("賣方出款選項 其他未填寫");
                    $("[name='sellerNote']").focus();
                    return false;
                }
                <{/if}>
                if ($('input:checkbox[name="sellerTarget[]"]').filter('[value="1"]').prop('checked') == true && $("[name='relation1']").val() == '') {
                    alert("受領人的關係未填寫");
                    $("[name='relation1']").focus();
                    return false;
                }
                if ($('input:checkbox[name="sellerTarget[]"]').filter('[value="3"]').prop('checked') == true && $("[name='relation3']").val() == '') {
                    alert("受領人的關係未填寫");
                    $("[name='relation3']").focus();
                    return false;
                }
                if ($('input:checkbox[name="sellerTarget[]"]').filter('[value="4"]').prop('checked') == true && $("[name='relation4']").val() == '') {
                    alert("受領人的關係未填寫");
                    $("[name='relation4']").focus();
                    return false;
                }
                var check = 1 ;
                var note = new Array();
                $('input:checkbox:checked[name="sellerTarget[]"]').each(function(i) { note[i] = this.value; });

                $.ajax({
                    url: '../bank/new/sellerNote.php',
                    type: 'POST',
                    dataType: 'html',
                    async:false,
                    data: {
                        sellerTarget: note,
                        cId:"<{$data_case.cCertifiedId}>",
                        sellerNote:$("[name='sellerNote']").val(),
                        relation1:$("[name='relation1']").val(),
                        relation3:$("[name='relation3']").val(),
                        relation4:$("[name='relation4']").val()
                    },
                })
                .done(function(msg) {
                    if (msg == 'error') {
                        alert("賣方備註修改失敗");
                    }
                });
            }
            
            function CatchData(type) {
                $('.cmc_overlay').show();
                if(CheckField() == false) {
                    return false;
                }

                if (checkTranSellerNote() == false) {
                    $('.cmc_overlay').hide();
                    return false;
                }
                
                var status = $('[name="case_status"]').val() ;
                var url_submit = '';
                var input = $('input');
                var textarea = $('textarea');
                var select = $('select');
                var arr_input = new Array();
                var arr_input2 = new Array();
                var valscid = $('[name=scrivener_id] option:selected').val();
                var valba = $('[name=scrivener_id] option:selected').val();
                var st = $('[name="case_status"]').val() ;
                var total = $('[name="income_totalmoney"]').val() ;
                    total = parseInt(total.replace(/\,/g, ''));

                if(total < 10000000 && "<{$data_owner_total}>" > 3) {
                    alert("賣方五人以上(含五人),總價低於1000萬(不含1000萬)。賣方履保費以每人1000元計收");
                }

                if (typeof(valscid) == 'undefined' || valscid == '0' || typeof(valba) == 'undefined') {
                    alert('請選擇地政士和帳號');
                    $('.cmc_overlay').hide();
                    return;
                }

                if ($("[name='realestate_brand']").val() == 1 || $("[name='realestate_brand1']").val() == 1 || $("[name='realestate_brand2']").val() ==1){
                    if ($('[name="case_signdate"]').val() =='' && (st == 3 || st == 2 || st == 4 || st==9 || st == 10)) {
                        alert("請輸入簽約日");
                        $('.cmc_overlay').hide();
                        return false;
                    }
                } else {
                    if ($('[name="case_signdate"]').val() =='' && (st == 3 || st == 4 || st==9 || st == 10)) {
                        alert("請輸入簽約日");
                        $('.cmc_overlay').hide();
                        return false;
                    }
                }

                if (type == 'add') {
                    url_submit = '/includes/escrow/contractadd.php';
                } else {
                    url_submit = '/includes/escrow/contractsave.php';
                    var feedbackScrivenerCheck = "<{$feedbackScrivenerCheck}>" ;

                    //檢查是否是業務專用的地政士
                    if (feedbackScrivenerCheck == 0) {
                       $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                       $('input:radio[name="cCaseFeedBackMoney"]').val(0) ;
                    }
                }
                
                var reg = /.*\[]$/ ; //reg.test($(item).attr("name"))
                var reg2 = /buy/ ;
                var reg3 = /owner/ ;
                var reg4 = /expenditure_/;

                $.each(select, function(key,item) {
                    if (reg.test($(item).attr("name"))) {
                        
                        if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                            arr_input[$(item).attr("name")] = new Array();

                            if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                                arr_input2[$(item).attr("name")] = new Array();
                            }
                        }
                            
                        arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                        if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")][arr_input2[$(item).attr("name")].length] = $(item).val();
                        }
                    } else {
                        arr_input[$(item).attr("name")] = $(item).attr("value");

                        if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")] = $(item).attr("value");
                        }
                    }
                });

                $.each(textarea, function(key,item) {
                    arr_input[$(item).attr("name")] = $(item).attr("value");

                    if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                        arr_input2[$(item).attr("name")] = $(item).attr("value");
                    }
                });

                $.each(input, function(key,item) {
                    if ($(item).attr("name") == 'scrivener_print' || $(item).attr("name") == 'realestate_print' || $(item).attr("name") == 'realestate_print1' || $(item).attr("name") == 'realestate_print2' || $(item).attr("name") == 'buyer_print' || $(item).attr("name") == 'owner_print') {
                       if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = 'Y';
                        } else {
                            arr_input[$(item).attr("name")] = 'N';
                        }

                        if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")] = arr_input[$(item).attr("name")];
                        }
                    } else if(reg.test($(item).attr("name"))) {
                        if ($(item).is(':checkbox')) {
                            if ($(item).attr("name") == 'newbuyer_cklist2[]' || $(item).attr("name") == 'newowner_cklist2[]' || $(item).attr("name") == 'buyer_cklist2[]' || $(item).attr("name") == 'owner_cklist2[]') {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }

                                if ($(item).is(':checked')) {
                                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = 1;
                                } else {
                                    arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = 0;
                                }
                            } else if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();

                                    if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                                        arr_input2[$(item).attr("name")] = new Array();
                                    }
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                                if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                                    arr_input2[$(item).attr("name")][arr_input2[$(item).attr("name")].length] = $(item).val();
                                }
                            }
                        } else {
                             if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();

                                if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                                    arr_input2[$(item).attr("name")] = new Array();
                                }
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();

                            if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                                arr_input2[$(item).attr("name")][arr_input2[$(item).attr("name")].length] = $(item).val();
                            }
                        }
                    } else if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = '1';
                        } else {
                            arr_input[$(item).attr("name")] = '0';
                        }

                        if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")] = arr_input[$(item).attr("name")];

                        }
                    } else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }

                        if (<{$data_property_count}> > 20 && (reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")] = $(item).attr("value");
                        }
                    } else {
                        arr_input[$(item).attr("name")] = $(item).attr("value");

                        if (<{$data_property_count}> > 20 && (reg4.test($(item).attr("name")) ||reg2.test($(item).attr("name")) || reg3.test($(item).attr("name")))) {
                            arr_input2[$(item).attr("name")] = $(item).attr("value");
                        }
                    }
                });

                if (<{$data_property_count}> > 20 ) {
                    arr_input2["id"] = "<{$data_case.cCertifiedId}>";
                    arr_input2["certifiedid"] = "<{$data_case.cCertifiedId}>";
                }
                
                var obj_input = $.extend({}, arr_input);
                var obj_input2 = $.extend({}, arr_input2);

                var request = $.ajax({
                    url: url_submit,
                    type: "POST",
                    data: obj_input,
                    dataType: "html"
                });
               
                request.done( function( msg ) {
                    //alert(msg);

                    if (type == 'add') {
                        var id = $('[name="scrivener_bankaccount"]').val() ;
                        id = id.substr(5,9) ;

                        checkScrivenerCaseCount();
                    } else {
                        var id = $('[name="certifiedid"]').val() ;
                    }
                    
                    var modify_check = "<{$smarty.session.member_modifycase}>";
                    if (<{$data_property_count}> > 20 ) {
                        var request2 = $.ajax({
                            url: "/includes/escrow/contractsave_bo.php",
                            type: "POST",
                            data: obj_input2,
                            async: false, //同步處理
                            dataType: "html"
                        });

                        request2.done( function( msg ){
                           // console.log(msg);
                        });
                    }

                    if (modify_check == 1) {
                        $('form[name=form_edit]').attr('action', '/escrow/formbuyowneredit.php');
                        $('form[name=form_edit] input[name=id]').val(id);
                        $('form[name=form_edit]').submit();
                    } else {
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

                if(CheckField() == false) {
                    return false;
                }

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
                    } else {
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
                        } else {
                            arr_input[$(item).attr("name")] = 'N';
                        }
                    } else if (reg.test($(item).attr("name"))) {
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }
                        } else {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    } else if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = '1';
                        } else {
                            arr_input[$(item).attr("name")] = '0';
                        }
                    } else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }
                    } else {
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
                    if (type == 'add') {
                        checkScrivenerCaseCount();
                    }
                });
            }

            function checkScrivenerCaseCount() {
                <{if $smarty.session.member_id == 12}>
                var valscid = $('[name=scrivener_id] option:selected').val();

                $.ajax({
                    url: '/includes/escrow/check_other.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {sId:valscid,type:'ScrCaseCount'},
                })
                .done(function(msg) {
                    alert(msg);
                });
                <{/if}>
            }
            
            function SplitInvoice() {
                var status = $('[name="case_status"]').val() ;
                $('[name=invoice_invoiceowner]').val(0);
                $('[name=invoice_invoicebuyer]').val(0);
                $('[name=invoice_invoicerealestate]').val(0);
                $('[name=invoice_invoicescrivener]').val(0);
                $('[name=invoice_invoiceother]').val(0);

                var total = $('[name="income_certifiedmoney"]').val() ;
                total = total.replace(/,/g, '');
        
                var money = 0;
                var p = 0;
                if ( (/^[0-9]+$/).test(total) ) {
                    money += total ;

                    if ($('[name=invoice_splitowner]').is(':checked')) {
                        p ++;
                    }  

                    if ($('[name=invoice_splitbuyer]').is(':checked')) {
                        p ++;
                    }

                    if ($('[name=invoice_splitrealestate]').is(':checked')) {
                        p ++;
                    }  
                    
                    if ($('[name=invoice_splitscrivener]').is(':checked')) {
                        p ++;
                    }

                    if ($('[name=invoice_splitother]').is(':checked')) {
                        p ++;
                    }

                    if (p > 0) {
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
                
                setCurrencymoney();
            
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
                
                if ((/^[0-9]+$/).test(rsm) && (/^[0-9]+$/).test(adm)) {
                        rsm = parseInt(rsm);
                        adm = parseInt(adm);
                        if (rsm > adm) {
                            dm = rsm - adm;
                        }
                }
                $('[name=expenditure_dealmoney]').val(dm)
                
                var rsm = $('[name=expenditure_realestatemoney_buyer]').val();
                var adm = $('[name=expenditure_advancemoney_buyer]').val();
                var dm = 0;
                
                rsm = rsm.replace(/,/g, '');
                adm = adm.replace(/,/g, '');
                
                if ((/^[0-9]+$/).test(rsm) && (/^[0-9]+$/).test(adm)) {
                    rsm = parseInt(rsm);
                    adm = parseInt(adm);
                    if (rsm > adm) {
                        dm = rsm - adm;
                    }
                }
                $('[name=expenditure_dealmoney_buyer]').val(dm)
                
                setCurrencymoney();
            }
            
            function CheckColor(name,color){    
                $('[name='+name+']').focus();
                $('[name='+name+']').css('background-color', color);
            }
            
            function CheckField() {
                const regex = /[\uD800-\uDBFF][\uDC00-\uDFFF]/; //難字判斷
                var msg = new Array();
                var index = 0;
                if ($('[name=case_status]').val() == '3' || $('[name=case_status]').val()=='4' || $('[name=case_status]').val()=='9' || $('[name=case_status]').val()=='10') {
                    if ($('[name=buy_name]').val() == '' || regex.test($('[name=buy_name]').val())) {
                        CheckColor('buy_name','#E4BEB1');
                        
                        msg[index] = '買方姓名';
                        index ++;
                    } else {
                        CheckColor('buy_name','#FFFFFF');
                    }

                    if ($('[name=buy_identifyid]').val() == '' || !checkUID($('[name=buy_identifyid]').val())) {
                        CheckColor('buy_identifyid','#E4BEB1');

                        msg[index] = '買方帳號';
                        index ++;
                    } else {
                        CheckColor('buy_identifyid','#FFFFFF');
                    }

                    if ($('[name=buyer_registzip]').val() == '' || $('[name=buyer_registaddr]').val() == '' || regex.test($('[name=buyer_registaddr]').val())) {
                        CheckColor('buyer_registaddr','#E4BEB1');
                        CheckColor('buyer_registcountry','#E4BEB1');
                        CheckColor('buyer_registarea','#E4BEB1');
                        
                        msg[index] = '買方戶籍地址';
                        index ++;
                    } else {
                        CheckColor('buyer_registaddr','#FFFFFF');
                        CheckColor('buyer_registcountry','#FFFFFF');
                        CheckColor('buyer_registarea','#FFFFFF');
                    }

                    if ($('[name=buyer_basezip]').val() == '' || $('[name=buyer_baseaddr]').val() == '' || regex.test($('[name=buyer_baseaddr]').val())) {
                        CheckColor('buyer_basecountry','#E4BEB1');
                        CheckColor('buyer_basearea','#E4BEB1');
                        CheckColor('buyer_baseaddr','#E4BEB1');
                        
                        msg[index] = '買方通訊地址';
                        index ++;
                    } else {
                        CheckColor('buyer_basecountry','#FFFFFF');
                        CheckColor('buyer_basearea','#FFFFFF');
                        CheckColor('buyer_baseaddr','#FFFFFF');
                    }

                    if ($('[name=owner_name]').val() == '' || regex.test($('[name=owner_name]').val())) {
                        CheckColor('owner_name','#E4BEB1');

                        msg[index] = '賣方姓名';
                        index ++;
                    } else {
                        CheckColor('owner_name','#FFFFFF');
                    }

                    if ($('[name=owner_identifyid]').val() == '' || !checkUID($('[name=owner_identifyid]').val())) {
                        CheckColor('owner_identifyid','#E4BEB1');

                        msg[index] = '賣方帳號';
                        index ++;
                    } else {
                        CheckColor('owner_identifyid','#FFFFFF');
                    }  

                    if ($('[name=owner_registzip]').val() == '' || $('[name=owner_registaddr]').val() == '' || regex.test($('[name=owner_registaddr]').val())) {
                        CheckColor('owner_registcountry','#E4BEB1');
                        CheckColor('owner_registarea','#E4BEB1');
                        CheckColor('owner_registaddr','#E4BEB1');

                        msg[index] = '賣方戶籍地址';
                        index ++;
                    } else {
                        CheckColor('owner_registcountry','#FFFFFF');
                        CheckColor('owner_registarea','#FFFFFF');
                        CheckColor('owner_registaddr','#FFFFFF');
                    }

                    if ($('[name=owner_basezip]').val() == '' || $('[name=owner_baseaddr]').val() == '' || regex.test($('[name=owner_baseaddr]').val())) {
                        CheckColor('owner_basecountry','#E4BEB1');
                        CheckColor('owner_basearea','#E4BEB1');
                        CheckColor('owner_baseaddr','#E4BEB1');

                        msg[index] = '賣方通訊地址';
                        index ++;
                    } else {
                        CheckColor('owner_basecountry','#FFFFFF');
                        CheckColor('owner_basearea','#FFFFFF');
                        CheckColor('owner_baseaddr','#FFFFFF');
                    }
                        
                    var show = '儲存請先確定以下資料是否有填齊全，或有難字︰\n';
                    show +=  msg.join('、');
                    show += '\n';
                    if (msg.length == 0) {
                        return true;
                    } else {
                        alert(show);
                        return false;
                    }
                }
                return true;
            }

            function CheckDate() {
                var show = '';
                var msg = new Array();
                var index = 0;
                
                if (!DateInspection($('[name=case_signdate]').val(),'b')) {
                    msg[index] = '簽約日期';
                    index ++;
                }

                if (!DateInspection($('[name=case_cEndDate]').val(),'b')) {
                    msg[index] = '實際點交日期';
                    index ++;
                }

                if (!DateInspection($('[name=land_movedate]').val(),'a')) {
                    msg[index] = '前次移轉現值或原規定地價';
                    index ++;
                }

                if (!DateInspection($('[name=owner_birthdayday]').val(),'b')) {
                    msg[index] = '賣方出生日期';
                    index ++;
                }

                if (!DateInspection($('[name=buy_birthdayday]').val(),'b')) {
                    msg[index] = '買方出生日期';
                    index ++;
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
                var status_old = <{$data_case.cCaseStatus}>;
                var dd = '<{$data_case.cEndDate}>' ;
                var dep = "<{$smarty.session.member_pDep}>";
                
                if (dd == '0000-00-00 00:00:00') {
                    dd = '' ;
                }
                
                var today = new Date() ;
                var now_day = (today.getFullYear() - 1911) + '-' + (today.getMonth()+1) + '-' + today.getDate() ;
                /* 法務狀態檢查 */
                if(status_old == 11 && dep != 6) {
                    alert('案件狀態:法務簽結 限定法務人員使用');
                    $('[name="case_status"]').val(11);
                    return false;
                }
                if(status == 11 && dep != 6) {
                    alert('案件狀態:法務簽結 限定法務人員使用');
                    $('[name="case_status"]').val(status_old);
                    return false;
                }

                if (status==3 || status == 10 || status == 4 || status == 9) {
                    if(CheckField() == false) {
                        return false;
                    }
                    /* 發票金額與利息分配檢核 */
                    if ((invoice_dealing() == 1)&&(interest_dealing() == 1)) {
                        //有反映保留款改結案會變動到結案時間(原本下面的程式應該不會變動才對，暫時做防呆)
                        if (dd != '') { $('[name=case_cEndDate]').val(dd) ; }
                        else { $('[name=case_cEndDate]').val(now_day) ; }

                        $('#save').show() ;
                    } else {
                        $('#save').hide() ;
                        if (dd != '') {
                            $('[name=case_cEndDate]').val(dd) ;
                        } else {
                            $('[name=case_cEndDate]').val(now_day) ;
                        }
                        
                        alert('請確認發票對象或利息金額是否正確分配!?') ;
                    }

                    $('[name="check_End"]').val(1); //確認是否改為結案
                    
                    //進度表
                    for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                        $('#ps'+i).addClass('step_class') ;
                    }
                    //需要檢查買賣方地址
                    newURL = 'buyerownerlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>&cCaseStatus=1';
                    $('#moreowner').attr("href", newURL);
                    newURL = 'buyerownerlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cCaseStatus=1';
                    $('#morebuyer').attr("href", newURL);
                    $('#trans_build').css({'display':''}) ;
                } else if ((status == 5) || (status == 7) || (status == 8)) {
                    if (dd != '') {
                        $('[name=case_cEndDate]').val(dd) ;
                    } else {
                        $('[name=case_cEndDate]').val(now_day) ;
                    }

                    $('#save').css({'display':''}) ;

                    for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                    }
                    //需要檢查買賣方地址
                    newURL = 'buyerownerlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>&cCaseStatus=0';
                    $('#moreowner').attr("href", newURL);
                    newURL = 'buyerownerlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cCaseStatus=1';
                    $('#morebuyer').attr("href", newURL);
                    $('#trans_build').css({'display':''}) ;
                } else if (status == 11) {
                    if(dep != 6) {
                        $('#save').css({'display':'none'}) ;
                        $('#trans_build').css({'display':'none'}) ;
                        alert('案件狀態:法務簽結 限定法務人員使用')
                    }

                } else {
                    if (dd != '') {
                        $('[name=case_cEndDate]').val(dd) ;
                    } else {
                        $('[name=case_cEndDate]').val('') ;
                    }

                    $('#save').css({'display':''}) ;
                    $('#trans_build').css({'display':''}) ;
                    
                    for (var i = 1 ; i < 7 ; i ++) {
                        $('#ps'+i).removeClass('step_class') ;
                        $('#ps1').addClass('step_class') ;
                    }
                    //需要檢查買賣方地址
                    newURL = 'buyerownerlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>&cCaseStatus=0';
                    $('#moreowner').attr("href", newURL);
                    newURL = 'buyerownerlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cCaseStatus=1';
                    $('#morebuyer').attr("href", newURL);
                }
            }

            function chk_other () {
                if ($("[name='buy_identifyid']").val()=='') {
                    alert("買方身分證未填寫");
                    return 2;
                } else if ($("[name='buyer_registzipF']").val()==''||$("[name='buyer_registaddr']").val()=='') {
                    alert("買方戶籍地址未填寫"); 
                    return 2;
                } else if ($("[name='buyer_basezipF']").val()==''||$("[name='buyer_baseaddr']").val()=='') {
                    alert("買方通訊地址未填寫");
                    return 2;
                } else if ($("[name='owner_identifyid']").val()=='') {
                    alert("賣方身分證未填寫");
                    return 2;
                } else if ($("[name='owner_registzipF']").val()==''||$("[name='owner_registzipF']").val()=='') {
                    alert("賣方戶籍地址未填寫");
                    return 2;
                } else if ($("[name='owner_basezipF']").val()==''||$("[name='owner_baseaddr']").val()=='') {
                    alert("賣方通訊地址未填寫");
                    return 2;
                } else {
                    return 1;
                }
            }

            function checkAffixBracnch(){
                //判斷幸福家是否有選賣方仲介店
                var brand = $("[name='realestate_brand']").val();
                var brand1 = $("[name='realestate_brand1']").val();
                var brand2 = $("[name='realestate_brand2']").val();

                if (brand == 69 || brand1 == 69 || brand2 == 69) {
                    if ($("[name='cAffixBranch']:checked").val() == undefined) {
                        alert('幸福家請選用印仲介店');
                        return false;
                    }
                }

                //判斷宏鎰集團是否有選賣方服務費收款店
                var funcAffixBranch= "<{$funcAffixBranch}>";

                if(funcAffixBranch == 'group18'){
                    if ($("[name='cAffixBranch[]']:checked").val() == undefined) {
                        alert('宏鎰集團請選賣方服務費收款店');
                        return false;
                    }
                }

                return true;
            }
            
            /* 檢核發票金額是否分配完成 */
            function invoice_dealing() {
                var tot = $('[name="income_certifiedmoney"]').val() ;
                tot = tot.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(tot)) {
                    tot = 0;
                } 
                tot = parseInt(tot) ;             //履保費總額
                var vOwner = parseInt($('[name="invoice_invoiceowner"]').val()) ;           //賣方發票金額
                var vBuyer = parseInt($('[name="invoice_invoicebuyer"]').val()) ;           //買方發票金額
                var vRealty = parseInt($('[name="invoice_invoicerealestate"]').val()) ;     //仲介發票金額
                var vScr = parseInt($('[name="invoice_invoicescrivener"]').val()) ;         //代書發票金額
                var vOther = parseInt($('[name="invoice_invoiceother"]').val()) ;           //其他發票金額

                if ($('[name="invoice_invoiceother"]').val()==undefined) {
                    vOther=0;
                }
                
                var all_inv = vOwner + vBuyer + vRealty + vScr + vOther ;
              
                if (tot == all_inv) {
                    return 1 ;
                } else {
                    return 0 ;
                }
            }

            function getinv(){
                var id = $('[name="certifiedid"]').val();
                
                $.ajax({
                    url: 'inv_table.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'id': id},
                })
                .done(function(txt) {
                    $("#invoice_target").empty();
                    $("#invoice_target").html(txt);
                    setCurrencymoney();
                });
            }
            //

            /* 檢核發票金額是否分配完成 (細項分配)*/
            function invoice_dealing2() {
                var tot = $('[name="income_certifiedmoney"]').val() ;
                tot = tot.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(tot)) {
                    tot = 0;
                } 
                tot = parseInt(tot) ;             //履保費總額
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

                if (tot != all_inv) {
                    return '0' ;
                } else {
                    return '1' ;
                }
            }
            
            /* 檢核利息是否分配完成 */
            function interest_dealing() {
                var intA = $('[name="int_total"]').val() ;
                var intB = $('[name="int_money"]').val() ;

                if (intA == intB) {
                    return 1 ;
                } else {
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
                } else {
                    return false ;
                }
            }

            function addBranchList(i) {
                $('#addBranch'+i).html('') ;
                var item = '.show_' + i + '_realty' ;
                $(item).show() ;
            }
            
            function clearFeedbackMoney(){
                $('[name="cCaseFeedBackMoney"]').val(0) ;
                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                $('[name="cCaseFeedBackMoney3"]').val(0) ;
                $('[name="cSpCaseFeedBackMoney"]').val(0) ;
                
                $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ;

                $('input:radio[name="cFeedbackTarget"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cFeedbackTarget1"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cFeedbackTarget2"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cFeedbackTarget3"]').filter('[value="1"]').attr('checked',true) ;

                if ($('[name="income_firstmoney"]').val()=='') {
                    $('[name="income_firstmoney"]').val(0);
                }
            }

            function getSalesScrivenerName() {
                // let name = $('[name="scrivener_id"]:selected').text();
                let name_arr = <{$menu_scrivener|@json_encode nofilter}>;
                let index = "<{$data_scrivener.cScrivener}>";
                let name = name_arr[index] ?? '';

                return name.match(/業務專用/g) ? true : false;
            }

            function feedback_overlay(kind){
                if(kind == '1'){
                    console.log('feedback_overlay_start');
                    $('.cmc_overlay').show();
                    // $('#save').button({
                    //     label: '儲存(已鎖定)',
                    //         icons: {
                    //         primary: "ui-icon-document"
                    //     }
                    // }).prop("disabled", true);
                } else if(kind == '2'){
                    $('.cmc_overlay').hide();
                    console.log('feedback_overlay_end');
                }
            }

            function feedback_money() {
                feedback_overlay('1');//偵測回饋金運算是否跑完
                clearFeedbackMoney();

                var _val = parseFloat(33.33/100) ;//預設回饋比率為萬分之2 //2013-10-09 依據政耀要求只要是配件案件，一律回饋萬分之2 //2015/01/22改為百分之33 //20150204配件以最小的比率為主 //2015/04/28 預設回饋比率改為百分之33.33
                var cer_real = $('[name="income_certifiedmoney"]').val() ;
                cer_real = cer_real.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(cer_real)) {
                    cer_real = 0;
                } 
                cer_real = parseInt(cer_real,10) ;             //履保費總額

                var _total = parseInt($('[name="income_totalmoney"]').val().replace(/\,/g, '')) ;              //總價金
                var first  =parseInt($('[name="income_firstmoney"]').val().replace(/\,/g, ''));                //降保
                var cer_title =( _total-first) * 0.0006 ;       //總價金萬分之六的應收保證費
                var sSpRecall=  parseFloat("<{$scrivener_sSpRecall}>")/100;//地政士特殊回饋(仲介回饋地政士或品牌回饋代書(優先算)則不用算)
                var branchbook = $("[name='data_feedData']").val();//合作契約書
                var branchbook1 = $("[name='data_feedData1']").val();//合作契約書
                var branchbook2 = $("[name='data_feedData2']").val();//合作契約書
                var branchbook3 = $("[name='data_feedData3']").val();//合作契約書
                var brand = $("[name='realestate_brand']").val();
                var brand1 = $("[name='realestate_brand1']").val();
                var brand2 = $("[name='realestate_brand2']").val();
                var brand3 = $("[name='realestate_brand3']").val();
                var tg = $('[name="cFeedbackTarget"]:checked').val() ;
                var tg1 = $('[name="cFeedbackTarget1"]:checked').val() ;
                var tg2 = $('[name="cFeedbackTarget2"]:checked').val() ;
                var tg3 = $('[name="cFeedbackTarget3"]:checked').val() ;
                var realty = $('[name="realestate_branchnum"]').val() ;
                var realty1 = $('[name="realestate_branchnum1"]').val() ;
                var realty2 = $('[name="realestate_branchnum2"]').val() ;
                var realty3 = $('[name="realestate_branchnum3"]').val() ;
                var brandScr = $("[name='scrivener_BrandScrRecall']").val();
                var brandScr1 = $("[name='scrivener_BrandScrRecall1']").val();
                var brandScr2 = $("[name='scrivener_BrandScrRecall2']").val();
                var brandScr3 = $("[name='scrivener_BrandScrRecall3']").val();
                var scrivenerF = $("[name='sFeedbackMoney']").val(); // 未收足回饋(地)
                var branchF = $("[name='data_bFeedbackMoney']").val(); // 未收足回饋(仲1)
                var branchF1 = $("[name='data_bFeedbackMoney1']").val(); // 未收足回饋(仲2)
                var branchF2 = $("[name='data_bFeedbackMoney2']").val(); // 未收足回饋(仲3)
                var branchF3 = $("[name='data_bFeedbackMoney3']").val(); // 未收足回饋(仲3)
                var brecall =new Array();//回饋比率
                var scrrecall =new Array();//仲介店回饋地政士
                var scrpartsp =new Array();//品牌回饋代書(優先算)
                var bcount  = 0; //店家數量
                var _feedbackMoney = 0;//回饋金
                var scrpart = 0;//特殊回饋(仲介店回饋地政士OR品牌回饋代書)
                var feed = 0; // 1:未收足
                var funcAffixBranch= "<{$funcAffixBranch}>";//20250417 判斷服務費收款店checkbox ex:宏鎰集團
                //var funcAffixBranch= '';
                 //比率
                if (realty > 0) {
                    if (tg == 2) {//回饋對象為代書
                        brecall[0] = parseFloat($('[name="sRecall"]').val())/100;
                    }else{
                        brecall[0] = parseFloat($('[name=realestate_bRecall]').val())/100;  
                    }

                    //仲介回饋地政士
                    if ($('[name="realestate_bScrRecall"]').val() != '') {
                        scrrecall.push(parseFloat($('[name="realestate_bScrRecall"]').val())/100);
                    }
                                        
                    //品牌回饋代書(優先算)
                    if ( brandScr != '0' && brandScr != '') {
                        brecall[0] = parseFloat($('[name=scrivener_BrandRecall]').val())/100; //仲介部分    
                        scrpartsp.push(parseFloat($('[name="scrivener_BrandScrRecall"]').val())/100) ;
                    }
                            
                    //怕有空值，一律以預設%數為主
                    //廷力房屋回饋比例為0
                    if (brecall[0] == null || brecall[0] == '') {
                        if($('[name="realestate_branchnum"]').val() != '1934'){
                            brecall[0] = _val;
                        }
                    }
                    bcount++;            
                    
                }

                if(realty1 > 0){
                    if (tg1 == 2) {//回饋對象為代書
                        brecall[1] = parseFloat($('[name="sRecall"]').val())/100;                        
                    }else{
                        brecall[1] = parseFloat($('[name=realestate_bRecall1]').val())/100;                       
                    }
                                        
                    //仲介回饋地政士
                    if ($('[name="realestate_bScrRecall1"]').val() != 0) {
                        scrrecall.push(parseFloat($('[name="realestate_bScrRecall1"]').val())/100);
                    }

                    //品牌回饋代書(優先算)
                    if (brandScr1 != '0' && brandScr1 != '') {
                        brecall[1] = parseFloat($('[name=scrivener_BrandRecall1]').val())/100; //仲介部分
                        scrpartsp.push(parseFloat($('[name="scrivener_BrandScrRecall1"]').val())/100) ;
                    }

                    //怕有空值，一律以預設%數為主
                    //廷力房屋回饋比例為0
                    if (brecall[1] == null || brecall[1] == '') {
                        if($('[name="realestate_branchnum1"]').val() != '1934'){
                            brecall[1] = _val;
                        }
                    }

                    bcount++;
                }

                if(realty2 > 0){
                    if (tg2 == 2) {//回饋對象為代書
                        brecall[2] = parseFloat($('[name="sRecall"]').val())/100;                    
                    } else {
                        brecall[2] = parseFloat($('[name=realestate_bRecall2]').val())/100;                               
                    }

                    //仲介回饋地政士
                    if ($('[name="realestate_bScrRecall2"]').val() != '') {
                        scrrecall.push(parseFloat($('[name="realestate_bScrRecall2"]').val())/100);
                    }

                    //品牌回饋代書(優先算)
                    if (brandScr2 != '0' && brandScr2 != '') {
                        brecall[2] = parseFloat($('[name=scrivener_BrandRecall2]').val())/100;     
                        scrpartsp.push(parseFloat($('[name="scrivener_BrandScrRecall12"]').val())/100) ;
                    }

                    //怕有空值，一律以預設%數為主
                    //廷力房屋回饋比例為0
                    if (brecall[2] == null || brecall[2] == '') {
                        if($('[name="realestate_branchnum2"]').val() != '1934'){
                            brecall[2] = _val;
                        }
                    }

                    bcount++;
                }

                if(realty3 > 0){
                    if (tg3 == 2) {//回饋對象為代書
                        brecall[2] = parseFloat($('[name="sRecall"]').val())/100;                    
                    } else {
                        brecall[2] = parseFloat($('[name=realestate_bRecall3]').val())/100;                               
                    }

                    //仲介回饋地政士
                    if ($('[name="realestate_bScrRecall3"]').val() != '') {
                        scrrecall.push(parseFloat($('[name="realestate_bScrRecall3"]').val())/100);
                    }

                        //品牌回饋代書(優先算)
                    if (brandScr3 != '0' && brandScr2 != '') {
                        brecall[2] = parseFloat($('[name=scrivener_BrandRecall3]').val())/100;     
                        scrpartsp.push(parseFloat($('[name="scrivener_BrandScrRecall13"]').val())/100) ;
                    }

                    //怕有空值，一律以預設%數為主
                    //廷力房屋回饋比例為0
                    if (brecall[3] == null || brecall[3] == '') {
                        if($('[name="realestate_branchnum3"]').val() != '1934'){
                            brecall[3] = _val;
                        }
                    }

                    bcount++;
                }

                if (scrrecall.length > 0) {
                    scrrecall.sort(); //取一個就好
                    scrpart = scrrecall[(scrrecall.length-1)];
                }

                if (scrpartsp.length > 0) {
                    scrpartsp.sort(); //取一個就好
                    scrpart = scrpartsp[(scrpartsp.length-1)];
                }

                if ((cer_real + 10) < cer_title) { //實收(+10元誤差)小於應收 ※不回饋 (未收足)
                    $('.checkCertifiedFee').show();

                    //未收足回饋0元
                    $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;
                    $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ;

                    if (bcount == 1) {
                        //第一間無合作契約書給代書
                        if ((branchbook == '' || branchbook == 0 || branchbook == undefined) && $('[name="realestate_branchnum"]').val() > 0 && brand != 1 && brand != 69) {
                            $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                            
                            if (scrivenerF == 1) { //地政士未收足也要回饋
                                $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney"]').val(Math.round(parseFloat($('[name="sRecall"]').val())/100*cer_real)) ;
                                if (getSalesScrivenerName()) {
                                    $('[name="cCaseFeedBackMoney"]').val(0) ;
                                }
                            } 
                        } else {
                            if (branchF == 1) {
                                $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney"]').val(Math.round( brecall[0]*cer_real)) ;
                            }
                        }

                        if (brand == 2) {
                            $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                        }
                    } else {
                        branchbookCount = Number(branchbook) + Number(branchbook1) + Number(branchbook2) + Number(branchbook3); //有幾家仲介有合契
                        if (branchF == 1) {
                            //有合契
                            if ((branchbook == '1') || ( brand == 1 || brand == 69)) {
                                $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney"]').val(Math.round((brecall[0]*cer_real)/bcount)) ;
                            } else {
                                $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney"]').val(0) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        } else {
                            //有合契
                            if ((branchbook == '1') || ( brand == 1 || brand == 69)) {
                                $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ; //回饋
                            } else {
                                $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ; //不回饋
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        }

                        if (branchF1 == 1) {
                            //有合契
                            if ((branchbook1 == '1') || ( brand1 == 1 || brand1 == 69)) {
                                $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney1"]').val(Math.round((brecall[1]*cer_real)/bcount)) ;
                            } else {
                                $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        } else {
                            //有合契
                            if ((branchbook1 == '1') || ( brand1 == 1 || brand1 == 69)) {
                                $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;
                            } else {
                                $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        }

                        if (branchF2 == 1) {
                            //有合契
                            if ((branchbook2 == '1') || ( brand2 == 1 || brand2 == 69)) {
                                $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney2"]').val(Math.round((brecall[2]*cer_real)/bcount)) ;
                            } else {
                                $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                                 $('[name="cCaseFeedBackMoney2"]').val(0) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget2"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        } else {
                            //有合契
                            if ((branchbook2 == '1') || ( brand2 == 1 || brand2 == 69)) {
                                $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;
                            } else {
                                $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget2"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        }

                        if (branchF3 == 1) {
                            //有合契
                            if ((branchbook3 == '1') || ( brand3 == 1 || brand3 == 69)) {
                                $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney3"]').val(Math.round((brecall[3]*cer_real)/bcount)) ;
                            } else {
                                $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;
                                $('[name="cCaseFeedBackMoney3"]').val(0) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget3"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        } else {
                            //有合契
                            if ((branchbook3 == '1') || ( brand3 == 1 || brand3 == 69)) {
                                $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ;
                            } else {
                                $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;
                                if(branchbookCount == 0) { //仲介都沒合契
                                    $('input:radio[name="cFeedbackTarget3"]').filter('[value="2"]').attr('checked',true) ; //回饋對象: 地政士
                                    $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ; //回饋
                                }
                            }
                        }
                    }
                } else {
                    ///////////////////////////幸福家配件要各自算(20170112)//////////
                    //賣方    幸福家
                    //買方    不管品牌
                    //回饋金  給賣方

                    //賣方  他牌
                    //買方  幸福家
                    // 1.按照一般算法算(含保證書[基本資料維護有回饋資料]) 
                    // 2.給幸福家(不含保證書[基本資料維護有回饋資料])
                    //改用契約書用印店做判斷 //20180222
                    //2019 配件只有幸幸配才要用用印店  ，幸他(含台屋) 用買賣方
                    ////////////////////////////////////////////////////////////////////
                    var ownerbrand = '';//要回饋的店家
                    var ownercol = '';//回饋金欄位
                    var ownerRecall ='';//回饋比率
                    var ownercheck = 0;//是否有合作契約書
                    var ownerfeed = '';
                    var buyerfeed = '';
                    var o = 0;//
                    var buyerbrand = '';
                    var buyercol = '';
                    var buyerRecall = '';
                    var buyercheck = 0;
                    if(funcAffixBranch == 'group18') {
                        var countAffixBranch = $('input:checkbox:checked[name="cAffixBranch[]"]').length; //賣方服務費收款店有幾家
                    }



                    var col = '';
                    var col1 = '';
                    var col2 = '';
                    var col3 ='';
                    var tmp_c = 0;

                    var brand69 = 0;
                    if (brand >0 && brand == 69) { brand69++; }
                    if (brand1 > 0 && brand1 == 69) { brand69++;}
                    if (brand2 > 0 && brand2 == 69) { brand69++;}
                    if (brand3 > 0 && brand3 == 69) { brand69++;}

                    if ((bcount > 1 && brand69 == bcount) || funcAffixBranch == 'group18'){
                        //配件只有幸福家或是宏鎰集團
                        if ($('#cAffixBranch').is(':checked')) {
                            ownerbrand = brand;
                            ownercol = 'cCaseFeedBackMoney';
                            ownerRecall = brecall[0];
                            ownercheck = branchbook;
                            
                            o++;
                            if(funcAffixBranch == 'group18') {
                                _feedbackMoney = Math.round(ownerRecall*cer_real/countAffixBranch);
                                $("[name='"+ownercol+"']").val(_feedbackMoney);
                            }
                        } else {
                            buyerbrand = brand;
                            buyercol = 'cCaseFeedBackMoney';
                            buyerRecall = brecall[0];
                            buyercheck = branchbook;
                        }        
                      
                        if (brand1 > 0 && realty1 > 0) {
                            if ($('#cAffixBranch1').is(':checked')) {
                                ownerbrand = brand1;
                                ownercol = 'cCaseFeedBackMoney1';
                                ownerRecall = brecall[1];
                                ownercheck = branchbook1;
                               
                                o++;
                                if(funcAffixBranch == 'group18') {
                                    _feedbackMoney = Math.round(ownerRecall*cer_real/countAffixBranch);
                                    $("[name='"+ownercol+"']").val(_feedbackMoney);
                                }
                            } else {
                                buyerbrand = brand1;
                                buyercol = 'cCaseFeedBackMoney1';
                                buyerRecall = brecall[1];
                                buyercheck = branchbook1;
                            }
                        }

                        if (brand2 > 0 && realty2 > 0) {
                            if ($('#cAffixBranch2').is(':checked')) {
                                ownerbrand = brand2;
                                ownercol = 'cCaseFeedBackMoney2';
                                ownerRecall = brecall[2];
                                ownercheck = branchbook2;
                               
                                o++;
                                if(funcAffixBranch == 'group18') {
                                    _feedbackMoney = Math.round(ownerRecall*cer_real/countAffixBranch);
                                    $("[name='"+ownercol+"']").val(_feedbackMoney);
                                }
                            } else {
                                buyerbrand = brand2;
                                buyercol = 'cCaseFeedBackMoney2';
                                buyerRecall = brecall[2];
                                buyercheck = branchbook2;
                            }
                        }

                        if (brand3 > 0 && realty3 > 0) {
                            if ($('#cAffixBranch3').is(':checked')) {
                                ownerbrand = brand3;
                                ownercol = 'cCaseFeedBackMoney3';
                                ownerRecall = brecall[3];
                                ownercheck = branchbook3;
                               
                                o++;
                                if(funcAffixBranch == 'group18') {
                                    _feedbackMoney = Math.round(ownerRecall*cer_real/countAffixBranch);
                                    $("[name='"+ownercol+"']").val(_feedbackMoney);
                                }
                            }else{
                                buyerbrand = brand3;
                                buyercol = 'cCaseFeedBackMoney3';
                                buyerRecall = brecall[3];
                                buyercheck = branchbook3;
                            }
                        }
                                    
                        //忘記選契約書用印店，用買賣方判斷
                        if (o == 0) {
                            if ($("[name='cServiceTarget']:checked").val() == 2) {
                                ownerbrand = brand;
                                ownercol = 'cCaseFeedBackMoney';
                                ownerRecall = brecall[0];
                                ownercheck = branchbook;
                                
                                o++;
                            } else {
                                buyerbrand = brand;
                                buyercol = 'cCaseFeedBackMoney';
                                buyerRecall = brecall[0];
                                buyercheck = branchbook;
                               
                            }

                            if (brand1 > 0 && realty1 > 0) {
                                if ($("[name='cServiceTarget1']:checked").val() == 2) {
                                    ownerbrand = brand1;
                                    ownercol = 'cCaseFeedBackMoney1'; 
                                    ownerRecall = brecall[1];
                                    ownercheck = branchbook1;
                                   
                                    o++;
                                } else {
                                    buyerbrand = brand1;
                                    buyercol = 'cCaseFeedBackMoney1';
                                    buyerRecall = brecall[1];
                                    buyercheck = branchbook1;
                                }
                            }

                            if (brand2 > 0 && realty2 > 0) {
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

                            if (brand3 > 0 && realty3 > 0) {
                                if ($("[name='cServiceTarget3']:checked").val() == 2) {
                                    ownerbrand = brand3;
                                    ownercol = 'cCaseFeedBackMoney3';  
                                    ownerRecall = brecall[3]; 
                                    ownercheck = branchbook3;  
                                   
                                    o++; 
                                } else {
                                    buyerbrand = brand3;
                                    buyercol = 'cCaseFeedBackMoney3';
                                    buyerRecall = brecall[3];
                                    buyercheck = branchbook3;   
                                }
                            }

                            if (o == 0) {//沒有選定賣方則從買賣方選一個
                                if ($("[name='cServiceTarget']:checked").val() == 1) {
                                    ownerbrand = brand;
                                    ownercol = 'cCaseFeedBackMoney';
                                    ownerRecall = brecall[0];
                                    ownercheck = branchbook;
                                } else if ($("[name='cServiceTarget1']:checked").val() == 1 && brand1 > 0) {
                                    ownerbrand = brand1;
                                    ownercol = 'cCaseFeedBackMoney1';
                                    ownerRecall = brecall[1];
                                    ownercheck = branchbook1;
                                } else if ($("[name='cServiceTarget2']:checked").val() == 1 && brand2 > 0) {
                                    ownerbrand = brand2;
                                    ownercol = 'cCaseFeedBackMoney2';
                                    ownerRecall = brecall[2];
                                    ownercheck = branchbook2;
                                } else if ($("[name='cServiceTarget3']:checked").val() == 1 && brand3 > 0) {
                                    ownerbrand = brand3;
                                    ownercol = 'cCaseFeedBackMoney3';
                                    ownerRecall = brecall[3];
                                    ownercheck = branchbook3;
                                }
                            }
                        }
                      
                        if (ownerbrand == 69) {
                            if (ownerfeed == '') {
                                _feedbackMoney = Math.round(ownerRecall*cer_real);
                                $("[name='"+ownercol+"']").val(_feedbackMoney);
                                $("[name='"+buyercol+"']").val(0);
                            } else {
                                $("[name='"+ownercol+"']").val(0);
                                $("[name='"+buyercol+"']").val(0);
                                $('input:radio[name="'+ownerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                $('input:radio[name="'+buyerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                            }
                        } else if (ownerbrand != 69 && funcAffixBranch != 'group18') {
                            if (ownercheck > 0) {
                                _feedbackMoney = Math.round((brecall[0]*cer_real)/bcount);
                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                 _feedbackMoney = Math.round((brecall[1]*cer_real)/bcount);
                                $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;

                                if (bcount == 3) {
                                    _feedbackMoney = Math.round((brecall[2]*cer_real)/bcount);
                                    $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                }

                            } else {
                                //沒合作契約書回饋給幸福家(買)
                                if (buyerfeed == '') {
                                    _feedbackMoney = Math.round(ownerRecall*cer_real);
                                    $("[name='"+buyercol+"']").val(_feedbackMoney);
                                    $("[name='"+ownercol+"']").val(0);
                                } else {
                                    $("[name='"+ownercol+"']").val(0);
                                    $("[name='"+buyercol+"']").val(0);
                                    $('input:radio[name="'+ownerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                    $('input:radio[name="'+buyerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                }
                            }
                        }

                        if (branchbook == 0) {
                            $("[name='cCaseFeedBackMoney']").val(0);
                            $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook1 == 0) {
                            $("[name='cCaseFeedBackMoney1']").val(0);
                            $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook2 == 0) {
                            $("[name='cCaseFeedBackMoney2']").val(0);
                            $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook3 == 0) {
                            $("[name='cCaseFeedBackMoney3']").val(0);
                            $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;
                        }
                    } else if (bcount > 1 && (brand == 69 || brand1 == 69 || brand2 ==69)) {
                        //幸福他排配(含台屋)
                        if (tg == 2) {
                            ownerbrand = brand;
                            ownercol = 'cCaseFeedBackMoney';
                            ownerRecall = brecall[0];
                            ownercheck = branchbook;
                            
                            o++;
                        } else {
                            buyerbrand = brand;
                            buyercol = 'cCaseFeedBackMoney';
                            buyerRecall = brecall[0];
                            buyercheck = branchbook;
                        }        
                      
                        if (brand1 > 0 && realty1 > 0) {
                            if (tg1 == 2) {
                                ownerbrand = brand1;
                                ownercol = 'cCaseFeedBackMoney1';
                                ownerRecall = brecall[1];
                                ownercheck = branchbook1;
                               
                                o++;
                            } else {
                                buyerbrand = brand1;
                                buyercol = 'cCaseFeedBackMoney1';
                                buyerRecall = brecall[1];
                                buyercheck = branchbook1;
                            }
                        }

                        if (brand2 > 0 && realty2 > 0) {
                            if (tg2 == 2) {
                                ownerbrand = brand2;
                                ownercol = 'cCaseFeedBackMoney2';
                                ownerRecall = brecall[2];
                                ownercheck = branchbook2;
                                
                                o++;
                            } else {
                                buyerbrand = brand2;
                                buyercol = 'cCaseFeedBackMoney2';
                                buyerRecall = brecall[2];
                                buyercheck = branchbook2;
                            }
                        }

                        if (brand3 > 0 && realty3 > 0) {
                            if (tg3 == 2) {
                                ownerbrand = brand3;
                                ownercol = 'cCaseFeedBackMoney3';
                                ownerRecall = brecall[3];
                                ownercheck = branchbook3;
                                
                                o++;
                            } else {
                                buyerbrand = brand3;
                                buyercol = 'cCaseFeedBackMoney3';
                                buyerRecall = brecall[3];
                                buyercheck = branchbook3;
                            }
                        }

                        if (o == 0) {
                            if ($("[name='cServiceTarget']:checked").val() == 2) {
                                ownerbrand = brand;
                                ownercol = 'cCaseFeedBackMoney';
                                ownerRecall = brecall[0];
                                ownercheck = branchbook;
                               
                                o++;
                            } else {
                                buyerbrand = brand;
                                buyercol = 'cCaseFeedBackMoney';
                                buyerRecall = brecall[0];
                                buyercheck = branchbook;
                            }

                            if (brand1 > 0 && realty1 > 0) {
                                if ($("[name='cServiceTarget1']:checked").val() == 2) {
                                    ownerbrand = brand1;
                                    ownercol = 'cCaseFeedBackMoney1'; 
                                    ownerRecall = brecall[1];
                                    ownercheck = branchbook1;
                                    
                                    o++;
                                } else {
                                    buyerbrand = brand1;
                                    buyercol = 'cCaseFeedBackMoney1';
                                    buyerRecall = brecall[1];
                                    buyercheck = branchbook1;
                                }
                            }

                            if (brand2 > 0 && realty2 > 0) {
                                if ($("[name='cServiceTarget2']:checked").val() == 2) {
                                    ownerbrand = brand2;
                                    ownercol = 'cCaseFeedBackMoney2';  
                                    ownerRecall = brecall[2]; 
                                    ownercheck = branchbook2;  
                                   
                                    o++; 
                                } else {
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
                                    ownercheck = branchbook;
                                } else if ($("[name='cServiceTarget1']:checked").val() == 1 && brand1 > 0) {
                                    ownerbrand = brand1;
                                    ownercol = 'cCaseFeedBackMoney1';
                                    ownerRecall = brecall[1];
                                    ownercheck = branchbook1;
                                } else if ($("[name='cServiceTarget2']:checked").val() == 1 && brand2 > 0) {
                                    ownerbrand = brand2;
                                    ownercol = 'cCaseFeedBackMoney2';
                                    ownerRecall = brecall[2];
                                    ownercheck = branchbook2;
                                }
                            }
                        }
                      
                        if (ownerbrand == 69) {
                            if (ownerfeed == '') {
                                _feedbackMoney = Math.round(ownerRecall*cer_real);
                                $("[name='"+ownercol+"']").val(_feedbackMoney);
                                $("[name='"+buyercol+"']").val(0);
                            } else {
                                $("[name='"+ownercol+"']").val(0);
                                $("[name='"+buyercol+"']").val(0);
                                $('input:radio[name="'+ownerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                $('input:radio[name="'+buyerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                            }
                        } else if (ownerbrand != 69) {
                            if (ownercheck > 0) { //他牌是契約用印店且有合作契約書 各店:保證費*回饋趴/回饋數
                                _feedbackMoney = Math.round((brecall[0]*cer_real)/bcount);
                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                _feedbackMoney = Math.round((brecall[1]*cer_real)/bcount);
                                $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney) ;
                                if (bcount == 3) {
                                    _feedbackMoney = Math.round((brecall[2]*cer_real)/bcount);
                                    $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney) ;
                                }
                            } else {
                                //沒合作契約書回饋給幸福家(買)
                                if (buyerfeed == '') {
                                    _feedbackMoney = Math.round(buyerRecall*cer_real);
                                    $("[name='"+buyercol+"']").val(_feedbackMoney);
                                    $("[name='"+ownercol+"']").val(0);
                                } else {
                                    $("[name='"+ownercol+"']").val(0);
                                    $("[name='"+buyercol+"']").val(0);
                                    $('input:radio[name="'+ownerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                    $('input:radio[name="'+buyerfeed+'"]').filter('[value="1"]').attr('checked',true) ;
                                }
                            }
                        }

                        if (branchbook == 0) {
                            $("[name='cCaseFeedBackMoney']").val(0);
                            $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook1 == 0) {
                            $("[name='cCaseFeedBackMoney1']").val(0);
                            $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook2 == 0) {
                            $("[name='cCaseFeedBackMoney2']").val(0);
                            $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                        }

                        if (branchbook3 == 0) {
                            $("[name='cCaseFeedBackMoney3']").val(0);
                            $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;
                        }
                    } else {
                        if (bcount == 1) { //只有一間店
                            _feedbackMoney = Math.round(brecall[0]*cer_real);

                            $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                            $('[name="cCaseFeedBackMoney1"]').val(0) ;
                            $('[name="cCaseFeedBackMoney2"]').val(0) ;
                            //回饋地政士

                            if (brand == 2) {
                                $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                                _feedbackMoney = Math.round((parseFloat($('[name="sRecall"]').val())/100)*cer_real);

                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                            }

                            //無合作契約書給代書
                            if ((branchbook == '' || branchbook == 0 || branchbook == undefined) && $('[name="realestate_branchnum"]').val() > 0 && brand != 1 && brand != 69) {
                                $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;
                                _feedbackMoney = Math.round((parseFloat($('[name="sRecall"]').val())/100)*cer_real);

                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                            }

                            //如有回饋給地政士另有地政士特殊回饋
                            if (($('[name="cFeedbackTarget"]:checked').val() == 2 || $('[name="cFeedbackTarget1"]:checked').val() == 2 || $('[name="cFeedbackTarget"]:checked').val() == 2) && (brand != 69 && brand != 1 && brand != 49 && brand != 2)  && sSpRecall > 0) {
                                if (sSpRecall > brecall[0]) {
                                    _feedbackMoney = Math.round(sSpRecall*cer_real);
                                } else {
                                    _feedbackMoney = Math.round((parseFloat($('[name="sRecall"]').val())/100)*cer_real);
                                }
                               
                                $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                            }

                            if (getSalesScrivenerName()) { //如果代書為業務本身時，將回饋金歸 0
                                $('[name="cCaseFeedBackMoney"]').val(0) ;
                            }
                        } else if (bcount > 1) {
                            var _feedbackMoney = 0;
                            var _feedbackMoney1 = 0;
                            var _feedbackMoney2 = 0;
                            var _feedbackMoney3 = 0;

                            //計算回饋
                            if (realty > 0) {
                                col = 'cCaseFeedBackMoney';
                                var _feedbackMoney = Math.round((brecall[0]*cer_real)/bcount);
                                $('[name="'+col+'"]').val(_feedbackMoney) ;
                            }

                            if (realty1 > 0) {
                                col1 = 'cCaseFeedBackMoney1';
                                var _feedbackMoney1 = Math.round((brecall[1]*cer_real)/bcount);
                                $('[name="'+col1+'"]').val(_feedbackMoney1) ;
                            }

                            if (realty2) {
                                col2 = 'cCaseFeedBackMoney2';
                                var _feedbackMoney2 = Math.round((brecall[2]*cer_real)/bcount);
                                $('[name="'+col2+'"]').val(_feedbackMoney2) ;
                            }

                            if (realty3) {
                                col3 = 'cCaseFeedBackMoney3';
                                var _feedbackMoney3 = Math.round((brecall[2]*cer_real)/bcount);
                                $('[name="'+col3+'"]').val(_feedbackMoney3) ;
                            }
                            
                            //是否為台屋優美或有合作契約書
                            if ((brand == 1 || brand == 49 || branchbook > 0)) {  
                                tmp_c++;
                            } else {
                                //無合契
                                $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;//不回饋
                                $('[name="cCaseFeedBackMoney"]').val(0) ;
                            }

                            if ((brand1 == 1 || brand1 ==49 || branchbook1 > 0) && realty1 > 0) {
                                tmp_c++;
                            } else {
                                //無合契
                                $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;//不回饋
                                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                            }

                            if ((brand2 == 1 || brand2 ==49 || branchbook2 > 0) && realty2 > 0) {
                                tmp_c++;
                            } else {
                                //無合契
                                $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;//不回饋
                                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                            }

                            if ((brand3 == 1 || brand3 ==49 || branchbook3 > 0) && realty3 > 0) {
                                tmp_c++;
                            } else {
                                //無合契
                                $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;//不回饋
                                $('[name="cCaseFeedBackMoney3"]').val(0) ;
                            }

                            //配件都沒有合作契約書，回饋給代書
                            if (tmp_c == 0) {
                                if (realty > 0) {
                                    $('input:radio[name="cCaseFeedback"]').filter('[value="0"]').attr('checked',true) ;//回饋
                                    $('input:radio[name="cFeedbackTarget"]').filter('[value="2"]').attr('checked',true) ;//回饋代書
                                    _feedbackMoney = Math.round((parseFloat($('[name="sRecall"]').val())*cer_real/100)/bcount);//改用代書回饋比例
                                    $('[name="cCaseFeedBackMoney"]').val(_feedbackMoney) ;
                                }

                                if (realty1 > 0) {
                                    $('input:radio[name="cCaseFeedback1"]').filter('[value="0"]').attr('checked',true) ;//回饋
                                    $('input:radio[name="cFeedbackTarget1"]').filter('[value="2"]').attr('checked',true) ;//回饋代書
                                    _feedbackMoney1 = Math.round((parseFloat($('[name="sRecall"]').val())*cer_real/100)/bcount);
                                    $('[name="cCaseFeedBackMoney1"]').val(_feedbackMoney1) ;
                                }

                                if (realty2 > 0) {
                                    $('input:radio[name="cCaseFeedback2"]').filter('[value="0"]').attr('checked',true) ;//回饋
                                    $('input:radio[name="cFeedbackTarget2"]').filter('[value="2"]').attr('checked',true) ;//回饋代書
                                    _feedbackMoney2 = Math.round((parseFloat($('[name="sRecall"]').val())*cer_real/100)/bcount);
                                    $('[name="cCaseFeedBackMoney2"]').val(_feedbackMoney2) ;
                                }

                                if (realty3 > 0) {
                                    $('input:radio[name="cCaseFeedback3"]').filter('[value="0"]').attr('checked',true) ;//回饋
                                    $('input:radio[name="cFeedbackTarget3"]').filter('[value="2"]').attr('checked',true) ;//回饋代書
                                    _feedbackMoney3 = Math.round((parseFloat($('[name="sRecall"]').val())*cer_real/100)/bcount);
                                    $('[name="cCaseFeedBackMoney3"]').val(_feedbackMoney3) ;
                                }
                            }
                        }
                    }

                    if ((scrpart != 0 && scrpart != '' && scrpart != undefined) && ($('[name="cFeedbackTarget"]:checked').val() != 2 && $('[name="cFeedbackTarget1"]:checked').val() != 2 && $('[name="cFeedbackTarget2"]:checked').val() != 2 && $('[name="cFeedbackTarget3"]:checked').val() != 2)) { 
                        scrFeedMoney = Math.round(scrpart*cer_real) ;
                       
                        $('#sp_show_mpney').show();
                        $('[name="cSpCaseFeedBackMoney"]').val(scrFeedMoney) ;

                    } else {
                        $('[name="cSpCaseFeedBackMoney"]').val(0) ;
                    }
                    ////////////////////////////////////////////////////////

                    //如果有回饋給地政士 特殊回饋不回饋                
                    if ((scrpart == undefined || scrpart == 0 || scrpart == '')  && ($('[name="cFeedbackTarget"]:checked').val() != 2 && $('[name="cFeedbackTarget1"]:checked').val() != 2 && $('[name="cFeedbackTarget2"]:checked').val() != 2 && $('[name="cFeedbackTarget2"]:checked').val() != 2)) { 
                        //如果仲介品牌有回饋給地政士 特殊回饋不回饋
                        if (feed == 1) {
                            if (scrivenerF == 1) {
                                SpRecall();
                            }
                        } else {
                            SpRecall();
                        }
                    }

                    $('[name="cCaseFeedBackModifier"]').val('') ;
                    $('[name="cCaseFeedBackModifyTime"]').val('') ;
                }

                //20240509 SC2207 陳志瓶代書所有案件均不回饋仲介,只要回饋代書30%(代書特殊回饋設成30%)
                //20241106 012001440回饋金發生沒收入卻有成本的狀況 討論後 佩琪決定之後都人工去看陳志瓶代書的案件
                if ($("[name='scrivener_id']").val() == '2207') {
                    special_feedback(2207);
                    feedback_overlay('2');
                    return;
                }

                feedback_overlay('2');
            }
          
            /**
             * 20240509 SC2207 陳志瓶代書所有案件均不回饋仲介,只要回饋代書30%(代書特殊回饋設成30%)
             * 20241106 012001440回饋金發生沒收入卻有成本的狀況 討論後 佩琪決定之後都人工去看陳志瓶代書的案件
             */
            function special_feedback(_sId) {
                $('input:radio[name="cCaseFeedback"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback1"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback2"]').filter('[value="1"]').attr('checked',true) ;
                $('input:radio[name="cCaseFeedback3"]').filter('[value="1"]').attr('checked',true) ;

                $('[name="cCaseFeedBackMoney"]').val(0) ;
                $('[name="cCaseFeedBackMoney1"]').val(0) ;
                $('[name="cCaseFeedBackMoney2"]').val(0) ;
                $('[name="cCaseFeedBackMoney3"]').val(0) ;

                let _income_certifiedmoney = $('[name="income_certifiedmoney"]').val() ;
                _income_certifiedmoney = _income_certifiedmoney.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(_income_certifiedmoney)) {
                    _income_certifiedmoney = 0;
                } 
                _income_certifiedmoney = parseInt(_income_certifiedmoney,10) ;   

                let _scrivener_special_rate = $('[name=scrivener_sSpRecall]').val() / 100 ?? 0;
                $('[name="cSpCaseFeedBackMoney"]').val(Math.round(_scrivener_special_rate * _income_certifiedmoney));

                let _scriverner_name = $('[name=scrivener_office]').val();
                $('#sp_show_scrivener_name').empty().html(_scriverner_name);
                $('#sp_show_mpney').show();
            }

            function OtherRecall3(type){
                var realty1 = $('[name="realestate_branchnum"]').val() ;
                var realty2 = $('[name="realestate_branchnum1"]').val() ;
                var realty3 = $('[name="realestate_branchnum2"]').val() ;
                var tg1 = $('[name="cFeedbackTarget"]:checked').val() ;
                var tg2 = $('[name="cFeedbackTarget1"]:checked').val() ;
                var tg3 = $('[name="cFeedbackTarget2"]:checked').val() ;

                var certifiedmoney = $('[name="income_certifiedmoney"]').val() ;
                certifiedmoney = certifiedmoney.replace(/\,/g, '');

                if (!(/^[0-9]+$/).test(certifiedmoney)) {
                    certifiedmoney = 0;
                } 
                certifiedmoney = parseInt(certifiedmoney) ;             //履保費總額
                
                $.ajax({
                    url: '../includes/escrow/setOtherFeed_v2.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cId':"<{$data_case.cCertifiedId}>","type":type,'branch':realty1,'branch1':realty2,'branch2':realty3,"certifiedmoney":certifiedmoney,"CaseFeedBackModifier":$('[name="cCaseFeedBackModifier"]').val(),'tg':tg1,'tg1':tg2,'tg2':tg3},
                })
                .done(function(txt) {
                    // console.log(txt);
                });
            }

            function OtherRecall2(c,cat,type){
                var realty1 = $('[name="realestate_branchnum"]').val() ;
                var realty2 = $('[name="realestate_branchnum1"]').val() ;
                var realty3 = $('[name="realestate_branchnum2"]').val() ;

                var certifiedmoney = $('[name="income_certifiedmoney"]').val() ;
                certifiedmoney = certifiedmoney.replace(/\,/g, '');

                if (!(/^[0-9]+$/).test(certifiedmoney)) {
                    certifiedmoney = 0;
                } 
                certifiedmoney = parseInt(certifiedmoney) ;             //履保費總額
                
                $.ajax({
                    url: '../includes/escrow/setOtherFeed.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cId':"<{$data_case.cCertifiedId}>",'c':c,'type':type,'cat':cat,'branch':realty1,'branch1':realty2,'branch2':realty3},
                })
                .done(function(txt) {
                    var arr = txt.split(',');
                    
                    if (arr[0] == 'del') {
                        if (c == '1') {
                            for (var i = 1; i < arr.length; i++) {
                                $("#DOtherFeed"+arr[i]).remove();
                            }
                        } else {
                            var check = 0;
                            $("[name='newotherFeedMoney[]']").each(function() {
                                if ($(this).val() > 0) {
                                    check++;
                                }
                            });
                            
                            if (check == 0) {
                                $("#OtherFeedcopy0").attr('class','dis otherf');//dis otherf 
                                $("#OtherFeedcopy0 [name='newotherFeedCheck[]']").val('');
                            }
                        }    
                    } else if (arr[0] == 'del2') {

                    } else {
                        for (var i = 0; i < arr.length; i++) {
                            var no = parseInt($("[name='addOFeed']").val());
                            var arr2 = arr[i].split('_');
                            var ck = 0;
                            var ckId = '';

                            if (arr2[0] != '') {
                                $(".newfeedcheckStore1").each(function() {
                                    if ($(this).val() == arr2[0] ) {
                                        ck =1;
                                        ckId = $(this).attr('alt');
                                    }
                                });

                                if (ck == 0) { //未重複就新增
                                    addOtherFeed('auto');
                                    $('#OtherFeedcopy'+no+' input:radio[name="newotherFeedType'+no+'"]').filter('[value="2"]').attr('checked',true) ;
                                    
                                    ChangeFeedStore('new',no,arr2[0]);
                                    $("#OtherFeedcopy"+no+" [name='newotherFeedstoreId"+no+"']").val(arr2[0]);
                                    $("#OtherFeedcopy"+no+" #newotherFeedMoney"+no).val(Math.round(arr2[1]*certifiedmoney));
                                } else {
                                    //alt newotherFeedMoney35 
                                    if ("<{$is_edit}>" == 0 || ckId == 0) { //save
                                        $("#OtherFeedcopy0").attr('class','otherf');//dis otherf 
                                        $("#newotherFeedMoney0").val(Math.round(arr2[1]*certifiedmoney));    
                                    } else {
                                        otherFeedCg(ckId);
                                        $("#otherFeedMoney"+ckId).val(Math.round(arr2[1]*certifiedmoney));
                                    }
                                }
                            }
                        };
                    }
                });
            }

            function OtherRecall(c) {
                var brand = $("[name='realestate_brand']").val();
                var brand1 = $("[name='realestate_brand1']").val();
                var brand2 = $("[name='realestate_brand2']").val();

                var certifiedmoney = $('[name="income_certifiedmoney"]').val() ;
                certifiedmoney = certifiedmoney.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(certifiedmoney)) {
                    certifiedmoney = 0;
                } 
                certifiedmoney = parseInt(certifiedmoney) ;  //履保費總額

                $.ajax({
                     url: '../includes/escrow/setOtherFeed.php',
                     type: 'POST',
                     dataType: 'html',
                     data: {'brand': brand,'brand1':brand1,'brand2':brand2,'cId':"<{$data_case.cCertifiedId}>",'c':c,'type':'69'},
                 }).done(function(txt) {
                    var arr = txt.split(',');
                    if (arr[0] == 'del') {
                        if (c=='1') {
                            for (var i = 1; i < arr.length; i++) {
                                delfeedmoney('',arr[i],'cat');
                                $("#DOtherFeed"+arr[i]).remove();
                            }
                        } else {
                            $("#OtherFeedcopy0").attr('class','dis otherf');//dis otherf 
                            $("#OtherFeedcopy0 [name='newotherFeedCheck[]']").val('');
                        }
                    } else {
                        for (var i = 0; i < arr.length; i++) {
                            var no = parseInt($("[name='addOFeed']").val());
                            var arr2 = arr[i].split('_');
                            var ck = 0;
                            var ckId = '';

                            if (arr2[0] != '') {
                                $(".newfeedcheckStore1").each(function() {
                                    if ($(this).val() == arr2[0] ) {
                                        ck =1;
                                        ckId = $(this).attr('alt');
                                    }
                                });

                                if (ck == 0) { //未重複就新增
                                    addOtherFeed('auto');

                                    $("#OtherFeedcopy"+no+" [name='newotherFeedstoreId"+no+"']").val(arr2[0]);
                                    $("#OtherFeedcopy"+no+" #newotherFeedMoney"+no).val(Math.round(arr2[1]*certifiedmoney));
                                } else {
                                    //alt newotherFeedMoney35 
                                    if ("<{$is_edit}>" == 0 || ckId == 0) { //save
                                        $("#OtherFeedcopy0").attr('class','otherf');//dis otherf 
                                        $("#newotherFeedMoney0").val(Math.round(arr2[1]*certifiedmoney));    
                                    } else {
                                        otherFeedCg(ckId);
                                        $("#otherFeedMoney"+ckId).val(Math.round(arr2[1]*certifiedmoney));
                                    }
                                }
                            }
                        };
                    }
                });
            }

            function SpRecall(){ //地政士特殊回饋金
                var _total = parseInt($('[name="income_totalmoney"]').val().replace(/\,/g, '')) ;  
                var sSpRecall= "<{$scrivener_sSpRecall}>";
                var check = 0;
                var branchCount = 0;

                var spMoney;

                var cMoney = $('[name="income_certifiedmoney"]').val() ;
                cMoney = cMoney.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(cMoney)) {
                    cMoney = 0;
                } 
                cMoney = parseInt(cMoney) ;  //履保費總額

                //有台屋、非仲一律不回饋
                if ($('[name="realestate_branchnum"]').val() > 0) {
                    branchCount++;
                    if ($("[name='realestate_brand']").val() != 1 && $("[name='realestate_brand']").val() != 49 && $("[name='realestate_brand']").val() != 2) {
                        check ++;
                    }
                }

                if ($('[name="realestate_branchnum1"]').val() > 0) {
                    branchCount++;
                    if ($("[name='realestate_brand1']").val() != 1 && $("[name='realestate_brand1']").val() != 49 && $("[name='realestate_brand1']").val() != 2) {
                        check ++;
                    }
                }

                if ($('[name="realestate_branchnum2"]').val() > 0) {
                    branchCount++;
                    if ($("[name='realestate_brand2']").val() != 1 && $("[name='realestate_brand2']").val() != 49 && $("[name='realestate_brand2']").val() != 2) {
                        check ++;
                    }
                }

                if ($('[name="realestate_branchnum3"]').val() > 0) {
                    branchCount++;
                    if ($("[name='realestate_brand3']").val() != 1 && $("[name='realestate_brand3']").val() != 49 && $("[name='realestate_brand3']").val() != 2) {
                        check ++;
                    }
                }

                if (sSpRecall=='') {
                    sSpRecall= $("[name='sSpRecall']").val();
                }

                if ((branchCount == check) && sSpRecall != 0) {
                    sSpRecall=sSpRecall/ 100; //20150121
                    spMoney= Math.round(cMoney * sSpRecall); //20150122

                    $('#sp_show_mpney').show();
                    if (_total!=''){
                        $('[name="cSpCaseFeedBackMoney"]').val(spMoney) ;
                    }
                } else {
                    $('[name="cSpCaseFeedBackMoney"]').val('0') ;
                    $('#sp_show_mpney').hide();
                }
            }

            function checkfeed(){
                var cerMoney = $('[name="income_certifiedmoney"]').val() ;
                cerMoney = cerMoney.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(cerMoney)) {
                    cerMoney = 0;
                }

                cerMoney = parseInt(cerMoney) ;             //履保費總額
                var feed = $("[name='cCaseFeedBackMoney']").val();
                var feed1 = $("[name='cCaseFeedBackMoney1']").val();
                var feed2 = $("[name='cCaseFeedBackMoney2']").val();
                var feedsp = $("[name='cSpCaseFeedBackMoney']").val();

                //配件加起來算，因配件目前是平分
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
            ////
            
            /* 同步回饋金 radio */
            function sync_radio() {
                $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>') ;
                $('[name="cCaseFeedBackModifyTime"]').val('time') ;
            }
            ////
            function checkFeedBackMoney(dataName) {
                if($('[name='+dataName+']').val() > 0) {
                    alert('不回饋 金額須為0');
                    event.returnValue = false
                    return false;
                }
            }
            
            /* 建立多組買賣方 */
            function more(ch) {
                var url = 'buyerownerlist.php?iden=' + ch + '&cCertifyId=' + $('[name="case_bankaccount"]').val() ;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url}) ;
            }
            ////
            
            /* 利息分配編輯 */
            function int_arrange() {
                var url = 'int_dealing.php?cCertifiedId=' + $('[name="case_bankaccount"]').val() ;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url, onClosed:function() {
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
                        setCurrencymoney();
                    });
                }}) ;
            }
            ////
            
            /* 發票對象編輯 */
            function invoice_check() {
                var latestCertifiedMoney =$('[name="income_certifiedmoney"]').val();
                latestCertifiedMoney = latestCertifiedMoney.replace(/\,/g, '');
                if (!(/^[0-9]+$/).test(latestCertifiedMoney)) {
                    latestCertifiedMoney = 0;
                }
                var url = 'inv_dealing.php?cCertifiedId=' + $('[name="case_bankaccount"]').val() +'&cSignCategory=<{$data_case.cSignCategory}>' + '&latestCertifiedMoney=' + latestCertifiedMoney;
                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
                    var id = $('[name="certifiedid"]').val();
                    $.ajax({
                        url: 'inv_table.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {'id': id},
                    })
                    .done(function(txt) {
                        $("#invoice_target").empty();
                        $("#invoice_target").html(txt);
                        setCurrencymoney();
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
                var pat2 = /[a-zA-z]{1}[8|9]{1}[0-9]{8}/;//2021新證號
                var pat3 = /[9]{1}[0-9]{6}/
                if (pat.test(str) || pat2.test(str)) {
                    $('#foreign'+tg).show() ;
                } else if (str.length == 7 && pat3.test(str)){ //未滿183天的大陸人民
                    $('#foreign'+tg).show() ;
                } else {
                    $('#foreign'+tg).hide() ;
                }
                ////
                
                /* 檢核身分證字號或統一編號合法性 */
                if (str) {
                    if (checkUID(str)) {
                        _id.html('<img src="/images/ok.png">') ;
                    } else {
                        _id.html('<img src="/images/ng.png">') ;
                    }
                }
                ////
            }
            ////

            /* 檢核身份證字號是否重複 */
            function checkIdDouble(tg = 'a') {
                var arr = new Array;
                
                let sn = $('[name="buy_identifyid"]').val();
                if ((sn != '') && (sn != undefined) && (sn != null)) {
                    arr.push(sn);
                }
                
                sn = $('[name="owner_identifyid"]').val();
                if ((sn != '') && (sn != undefined) && (sn != null)) {
                    arr.push(sn);
                }

                let url = '/includes/escrow/checkIdDouble.php';
                $.post(url, {'cId': '<{$data_case.cCertifiedId}>', 'other': 'Y'}, function(response) {
                    arr = [...arr, ...response];
                    
                    let _duplicated = hasDuplicates(arr);
                    if (_duplicated != '') {
                        console.log(arr);
                        alert('身分證號/統編已存在!!(' + _duplicated + ')');
                        $('#save').hide();
                        if (tg == 'b') {
                            $('[name="buy_identifyid"]').focus().select();
                        } else if (tg == 'o') {
                            $('[name="owner_identifyid"]').focus().select();
                        }
                    } else {
                        $('#save').show();
                        if (tg == 'b') {
                            getCustomer('buy', $('[name="buy_identifyid"]').val());
                        } else if (tg == 'o') {
                            getCustomer('owner', $('[name="owner_identifyid"]').val());
                        }
                    }
                }, 'json');
            }

            function hasDuplicates(arr) {
                var counts = [];

                for (var i = 0; i <= arr.length; i++) {
                    if (counts[arr[i]] === undefined) {
                        counts[arr[i]] = 1;
                    } else {
                        return arr[i];
                    }
                }
                return '';
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
                var pat2 = /[a-zA-z]{1}[8|9]{1}[0-6]{8}/;//2021新證號
                
                if ((pat.test(str)|| pat2.test(str)) && (rsd == undefined)) {
                    return true ;
                } else {
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
                } else {
                    $.post(url,{'bid':bid,'sales':sales,'num':num,'act':'add','cid':CertifiedId,'target':target},function(txt) {
                        $('#salesList'+num).html(txt+'&nbsp;') ;
                        $('[name="option_sales'+num+'"]').val(0) ;
                    }) ;
                }
            }

            function del(num,sales,bid) {
                var url = 'bsales_list.php' ;
                var CertifiedId='<{$data_case.cCertifiedId}>';
    
                $.post(url,{'num':num,'cid':CertifiedId,'sales':sales,'bid':bid,'act':'del'},function(txt) {
                    $('#salesList'+num).html(txt+'&nbsp;') ;
                    $('[name="option_sales"]'+num).val(0) ;
                }) ;
            }

           
            //刪除建物資料
            function del_build(item) { 
                var url = 'delete_build.php' ;
                var CertifiedId='<{$data_case.cCertifiedId}>';

                $("[name='ditem']").val(item);
                $("[name='form_build_del']").submit();
            }

            function processing(step) {
                var _original = "<{$data_case.status}>" ;
                $('[name="cCaseProcessing"]').val(step) ;

                var today = new Date() ;
                var now_day = (today.getFullYear() - 1911) + '-' + (today.getMonth()+1) + '-' + today.getDate() ;

                if (step == 6) {
                     $('[name="case_cEndDate"]').val(now_day);
                } else {
                     $('[name="case_status"]').val(2);
                     $('[name="case_cEndDate"]').val('');
                }

                for (var i = 1 ; i < 7 ; i ++) {
                    if (i <= step) {
                        $('#ps'+i).addClass('step_class') ;
                    } else {
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
                } else {
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
                
                buy_tmp = buy_tmp.replace(',','');
                owner_tmp = owner_tmp.replace(',','');
                sum = parseInt(buy_tmp)+parseInt(owner_tmp);
                
                if (total<sum) {
                    alert('服務費大於總價金的6%');
                }
            }

            function Bankchange(type,no){
                GetBankBranchList($('#'+type+'_bankkey'+no+''),
                                    $('#'+type+'_bankbranch'+no+''),
                                    null);
            }
             
            function addOtherFeed(cat){
                var no = parseInt($("[name='addOFeed']").val());
                
                if (no == 0) { //
                    $("#OtherFeedcopy0").attr('class', 'otherf');
                    $("[name='newotherFeedCheck[]']").val(1);
                } else {
                    $("[name='newotherFeedCheck[]']").val(1);
                    $("#OtherFeedcopy0").clone().insertAfter(".otherf:last").attr({'id': 'OtherFeedcopy'+no,'class': 'otherf'});
                    var one = $("[name='newotherFeedType0']:checked").val();

                    $("#OtherFeedcopy"+no+" [name='newotherFeedType0']").attr({
                        'name': 'newotherFeedType'+no,
                        'onClick': "ChangeFeedStore('new','"+no+"','')"
                    });

                    if (one == 1) {
                        $("[name='newotherFeedType0']").filter('[value="1"]').attr('checked',true);
                    } else {
                        $("[name='newotherFeedType0']").filter('[value="2"]').attr('checked',true);
                    }

                    $("#OtherFeedcopy"+no+" #OtherFeedDel0").attr({'id':'OtherFeedDel'+no,'onClick':'delfeedmoney("new","OtherFeedcopy'+no+'","")'});
                    $("#OtherFeedcopy"+no+" [name='newotherFeedstoreId0']").attr({'name': 'newotherFeedstoreId'+no});
                    $("#OtherFeedcopy"+no+" #newotherFeedMoney0").val('');
                    $("#OtherFeedcopy"+no+" #newotherFeedMoney0").attr('id', 'newotherFeedMoney'+no);
                }

                $("[name='addOFeed']").val((no+1));
                if (cat == '') {
                    sync_radio();
                }
            }

            function ChangeFeedStore(cat,i,val){
                if (cat == '') {
                    otherFeedCg(i);
                }
               
                var type = $("[name='"+cat+"otherFeedType"+i+"']:checked").val();
                $.ajax({
                    url: '../includes/escrow/feedBackMoneyAjax.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'type': type,'act':'st','val':val},
                })
                .done(function(txt) {
                    $("[name='"+cat+"otherFeedstoreId"+i+"'] option").remove();
                    $("[name='"+cat+"otherFeedstoreId"+i+"']").html(txt);
                });
            }
            
            function otherFeedCg(i){
                $("#otherFeedCheck"+i).attr('value', '1');
                $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>') ;
                $('[name="cCaseFeedBackModifyTime"]').val('time') ;
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
                            } else {
                                $("#"+tg).attr('class', 'dis');
                                $("[name='addOFeed']").val(0);
                            }
                        } else {
                             $.ajax({
                                url: '../includes/escrow/feedBackMoneyAjax.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {'type': type,'act':'del','id':tg},
                            })
                            .done(function(txt) {
                                if (cat == '') {
                                    alert('刪除成功');
                                    $('[name="cCaseFeedBackModifier"]').val('<{$smarty.session.member_id}>') ;
                                    $('[name="cCaseFeedBackModifyTime"]').val('time') ;

                                    CatchData('save');
                                }
                            });
                        }
                    } else {
                        return false;
                    }
                } else {

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

                if(note == '') {
                    alert('請輸入服務內容');
                    return false;
                }

                $.ajax({
                    url: '../includes/escrow/service_msg.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'cid':cid,'date':date,'hour':hour,'min':min,'man':man,'note':note,'type':'add'},
                }).done(function(txt) {
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
                    $(".ser_msg").html('');
                    $(".ser_msg").html(txt);
                });
            }

            function ser_show(){
                var val = $('[name="ser"]').val();
                if (val == 1) {
                    $(".ser_msg2").hide("slow");
                    $('[name="ser"]').val(0);
                } else {
                    $(".ser_msg2").show("slow");
                    $('[name="ser"]').val(1);
                }
            }

            function AddNoteMsg(){
                $.ajax({
                    url: '../includes/escrow/note_msg.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {type: 'add',note:$('[name="invoice_remark"]').val(),cId:$("#reload [name='id']").val()},
                })
                .done(function(msg) {
                   $(".note_msg").html(msg);
                });
            }

            function DelNoteMsg(id){
                $.ajax({
                    url: '../includes/escrow/note_msg.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {cId:$("#reload [name='id']").val(),'id':id,'type':'del'},
                }).done(function(txt) {
                    $(".note_msg").html(txt);
                });
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

                $("[name='owner_money5']").val((val1+val2));
            }
			
			function newImg(b, c) {
				var url = "showcIdStamp.php?bId=" + b + "&cId=" + c ;
				window.open(url, "", config="scrollbars=yes,resizable=yes") ;
			}

            function download(cat, id) {
                let cId = "<{$data_case.cCertifiedId}>";
                $('#print [name="cat"]').val(cat);
                $("#print").submit();
            }

            function checkCalTax(){
                $("[name='changeLand']").val(1);
            }

            function eContract2(id){
                $.ajax({
                    url: 'getEContract.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {code: id},
                })
                .done(function(msg) {
                    var obj = jQuery.parseJSON(msg);

                    if (obj.code == 200) {
                        alert('已完成');
                    } else {
                        alert('轉換失敗，請至「保號->代書」恢復成案件再試');
                    }

                    $('form[name=form_edit]').attr('action', '/escrow/formbuyowneredit.php');
                    $('form[name=form_edit] input[name=id]').val($('[name="checkcId"]').val());
                    $('form[name=form_edit]').submit();
                });
            }

            var timeout;
            var delay = 1000; //間隔 1 秒再取輸入值
            function checkCertifiedId() {
                if(timeout) {
                    clearTimeout(timeout);
                }

                timeout = setTimeout(function() {
                    idConvScr();
                }, delay);
            }

            function idConvScr() {
                var url = 'id_conv_scr.php' ;
                var id = $('[name="checkcId"]').val() ;
                var check='<{$smarty.session.member_bankcheck}>';

                $.get(url+'?cid='+id,function(txt) {
                    var obj = jQuery.parseJSON(txt);

                    if (obj.status == 'ng') {
                        $('#showcheckId').html(obj.statusMsg) ;
                    } else {
                        $('#showcheckId').html(obj.statusMsg+'&nbsp;<span style="color:#000080;font-weight:bold;">'+obj.scrivener+'</span>&nbsp;<span style="color:#FF0000;font-weight:bold;">'+obj.bank+'</span><span id="btnEC"></span>') ;
                         
                        if (obj.bFrom == 2) {
                            $("#btnEC").html("<input type=\"button\" onClick =\"eContract2('"+obj.account+"')\" value=\"電子合約書轉回建經\">");
                        }

                        if (obj.status == 'ok') {
                            $("[name='case_bank']").val(obj.bankCode);
                            $("[name='checkScr']").val(obj.scrivener);
                            $("[name='scrivener_id']").val(obj.sId);
                            $('[name="checkcId2"]').val(obj.account) ;
                            $("[name='certifiedid_view']").val(id);

                            CatchScrivener(); 
                        }
                    }
                }) ;
            }

            function casmsg(cat){
                var url = "contractNote.php?cCertifyId=<{$data_case.cCertifiedId}>&cat="+cat;

                $.colorbox({iframe:true, width:"1200px", height:"90%", href:url,onClosed:function(){
                    $.ajax({
                        url: 'contractNoteTable.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {"cat": cat,"cId":"<{$data_case.cCertifiedId}>"},
                    })
                    .done(function(msg) {
                        $("#casmsg"+cat).html(msg);
                    });
                }}) ;
            }

            function copyCase(){
                if ("<{$is_edit}>" == 0) {
                    var cid = $('[name="checkAcc"]', window.parent.document).val();
                } else {
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
                } else if (iden == 'owner' && "<{$data_owner.cIdentifyId}>" != '') {
                    check++;
                }

                $.ajax({
                    url: '../includes/escrow/getCustomer.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: id,cId:"<{$data_case.cCertifiedId}>",iden:iden},
                })
                .done(function(msg) {
                    var obj = JSON.parse(msg); //buy_name
                    
                    if (obj.msg == 'ok') {
                        if (check > 0 && checkUID(id)) {
                            if (!confirm("已有資料存在，是否要取代?")) {
                                return false;
                            }
                        }

                        $('[name="'+iden+'_name"]').val(obj.name);
                        $('[name="'+iden+'_birthdayday"]').val(obj.birthday);

                        if (iden =='buy') {
                            iden = 'buyer';
                        };

                        //自動勾選不帶入點交單buyerChecklist
                        $("#"+iden+"Checklist").click();
                        $("#"+iden+"Checklist").prop('checked', true);
                        $('[name="'+iden+'_registcountry"]').val(obj.city);//buyer_registcountry
                        $('[name="'+iden+'_registarea"] option').remove() ; //buyer_registarea

                        $.post('listArea.php',{"city":obj.city},function(txt) {
                            $('[name="'+iden+'_registarea"]').append(txt) ;
                            $('[name="'+iden+'_registarea"]').val(obj.zip);
                        }) ;

                        $('[name="'+iden+'_registaddr"]').val(obj.addr);//buyer_registaddr
                        $('[name="'+iden+'_registzip"]').val(obj.zip);//buyer_registzip
                        $('[name="'+iden+'_registzipF"]').val(obj.zip);
                    }
                });
            }

            function openFeedbackModifyConfirm() {
                if (feedbackApplyStatus()) {
                    openFeedbackModify('');
                }
            }

            function openFeedbackModify(id) {
                let str = '';
                if (id) {
                    str = '&rId='+id;
                }

                let url = 'formbuyownereditSalesFeed.php?id=<{$data_case.cCertifiedId}>&certifyDate=<{$CertifyDate}>&cat=add'+str ;
                $.colorbox({
                    iframe: true, 
                    width: "80%", 
                    height: "100%", 
                    href: url,
                    onClosed: function() {
                        $.ajax({
                            url: 'salesFeedbackMoney_table.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {cId: '<{$data_case.cCertifiedId}>'},
                        })
                        .done(function(html) {
                            $("#tbl_feedback").html(html);
                        })
                    }
                });
            }

            function feedbackApplyStatus(id='') {
                let url = '/includes/escrow/feedbackApplyStatus.php';

                $.post(url, {cId: '<{$data_case.cCertifiedId}>'})
                    .done(function (response) {
                        if (response == 'OK') {
                            openFeedbackModify(id);
                        } else {
                            alert('案件已發布！無法申請變更 請連繫經辦');
                            return false;
                        }
                });
            }


            function delSalesFeedConfirm(id) {
                if (feedbackApplyStatus(id)) {
                    delSalesFeed(id);
                }
            }
            
            function delSalesFeed(id){
                $.ajax({
                    url: 'salesFeedbackMoney_del.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id:id},
                })
                .done(function(msg) {
                    if (msg == 'ok') {
                        alert('刪除成功');
                        $.ajax({
                            url: 'salesFeedbackMoney_table.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {cId: '<{$data_case.cCertifiedId}>'},
                        })
                        .done(function(html) {
                            $("#tbl_feedback").html(html);
                        });
                    } else {
                        alert('刪除失敗');
                    }
                });
            }

            function clickChecklist(cat){
                $("."+cat+"cklist").each(function() {
                    if ($('#'+cat+'Checklist').prop('checked') == true) {
                        $(this).prop('checked', 'checked');
                    }else{
                        $(this).prop('checked', '');
                    }
                });
            }

            function checkChecklist(cat){
                var bankCount = parseInt($("."+cat+"cklist").length);
                var bankcheckCount = 0;

                $("."+cat+"cklist").each(function() {
                    if ($(this).prop('checked') == true) {
                        bankcheckCount++;
                    }
                });

                if (bankCount == bankcheckCount) {
                    $("[name='"+cat+"Checklist']").prop('checked', 'checked');
                } else {
                    $("[name='"+cat+"Checklist']").prop('checked', '');
                }
            }

            function OtherBank(cId){
                var url = 'formbuyownerBank.php?id='+cId;
                $.colorbox({iframe:true, width:"60%", height:"60%", href:url}) ;
            }

            function addSalesList(cat){
                var clonedRow = $("."+cat+"sales:last").clone(true);
                var no = parseInt($("."+cat+"sales").length)+1;
                
                clonedRow.find('[type*="text"]').val('');
                clonedRow.find('.'+cat+'salesNo').text(no);
                clonedRow.insertAfter("."+cat+"sales:last");
            }

            function CalculationRatio(){
                var total = 0;
                var ratio = 0;
                if ($('[name="income_certifiedmoney"]').val() != '' ) {
                    var certifiedmoney = parseInt($('[name="income_certifiedmoney"]').val().replace(/\,/g, ''));
                    $(".feedbackmoneysum").each( function() {
                        var check = 1;
                        if ($(this).val() != '') {
                            if ($(this).attr("name") == 'cCaseFeedBackMoney' && $('[name="cCaseFeedback"]:checked').val() == 1) {
                                check = 0;
                            }

                            if($(this).attr("name") == 'cCaseFeedBackMoney1' && $('[name="cCaseFeedback1"]:checked').val() == 1){
                                check = 0;
                            }

                            if($(this).attr("name") == 'cCaseFeedBackMoney2' && $('[name="cCaseFeedback2"]:checked').val() == 1){
                                check = 0;
                            } 

                            if (check == 1) {
                                total += parseInt($(this).val());
                            }
                        }
                    });

                    if (certifiedmoney > 0) {
                        var ratio = ((total/certifiedmoney)*100).toFixed(2); //取二位 
                    }
                }

                $("#showRatio").html(ratio+'%');
            }

 			function transferLegal(handler) {
                $.ajax({
                    url: '/includes/escrow/caseHandlerUpdate.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "<{$data_case.cCertifiedId}>", to: handler, vr_code: "<{$data_case.cEscrowBankAccount}>"},
                })
                .done(function(msg) {
                    if (msg == 'OK') {
                        let txt = '';
                        if (handler == 1) {
                            $('#legalbtn').attr('onclick', 'transferLegal(2)');

                            $('#legalbtn').addClass('legal-warning');
                            $('#legalbtn').button({
                                label: '返還經辦'
                            });

                            txt = '移交法務'
                        }

                        if (handler == 2) {
                            $('#legalbtn').attr('onclick', 'transferLegal(1)');

                            $('#legalbtn').removeClass('legal-warning');
                            $('#legalbtn').button({
                                label: '移交法務'
                            });

                            txt = '返還經辦';
                        }

                        alert('案件已' + txt);
                    } else {
                        alert('轉移失敗');
                    }
                });
            }

            function sendNotice(){
				var is_edit = "<{$is_edit}>";

                if (is_edit != 1) {
                    alert("請儲存案件後再發送通知");
                    return false;
                }

                $.ajax({
                    url: '../includes/escrow/sendLineMessage.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {cat: 1,cId:"<{$data_case.cCertifiedId}>"},
                })
                .done(function(data) {
                   alert(data);
                   $("#line").attr('disabled', 'disabled');
                });
            }

            //如果解約, 買賣雙方的戶籍&通訊地址&ID, 也要KEY完整才能存
            function terminateContract() {
                var _case_status         = $('[name="case_status"]').val();         //解約案件

                var _buyer_name          = $('[name="buy_name"]').val();            //買方姓名
                var _buyer_id            = $('[name="buy_identifyid"]').val();      //買方證號
                var _buyer_register_zip  = $('[name="buyer_registzip"]').val();     //買方戶籍郵遞區號
                var _buyer_register_addr = $('[name="buyer_registaddr"]').val();    //買方戶籍地址
                var _buyer_base_zip      = $('[name="buyer_basezip"]').val();       //買方通訊郵遞區號
                var _buyer_base_addr     = $('[name="buyer_baseaddr"]').val();      //買方通訊地址
                
                var _owner_name          = $('[name="owner_name"]').val();          //賣方姓名
                var _owner_id            = $('[name="owner_identifyid"]').val();    //賣方證號
                var _owner_register_zip  = $('[name="owner_registzip"]').val();     //賣方戶籍郵遞區號
                var _owner_register_addr = $('[name="owner_registaddr"]').val();    //賣方戶籍地址
                var _owner_base_zip      = $('[name="owner_basezip"]').val();       //賣方通訊郵遞區號
                var _owner_base_addr     = $('[name="owner_baseaddr"]').val();      //賣方通訊地址

                if (_case_status == 4) {
                    if (checkEmbty(_buyer_name)
                        || checkEmbty(_buyer_id)
                        || checkEmbty(_buyer_register_zip)
                        || checkEmbty(_buyer_register_addr)
                        || checkEmbty(_buyer_base_zip)
                        || checkEmbty(_buyer_base_addr)
                        || checkEmbty(_owner_name)
                        || checkEmbty(_owner_id)
                        || checkEmbty(_owner_register_zip)
                        || checkEmbty(_owner_register_addr)
                        || checkEmbty(_owner_base_zip)
                        || checkEmbty(_owner_base_addr)
                        || checkOthersData()
                        ) {
                        return true
                    }
                }
                return false;
            }
            function checkOthersData() {
                var res = '';
                 $.ajax({
                    async: false,
                    url: '../includes/escrow/validateAddr.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {cId:"<{$data_case.cCertifiedId}>"},
                    success: function(data) {
                        if(data.status == 400) {
                            res = true;
                        } else {
                            res = false;
                        }
                    }
                });
                return res;
            }

            //檢查是否有值
            function checkEmbty(_name) {
                if ((_name == '') || (_name == undefined) || (_name == null)) {
                    return true;
                } else {
                    return false;
                }
            }

            //檢查案件欄位是否正確填寫
            function caseCloseCheck(_status) {
                //結案時檢查
                var _check = true;
                if (_status == 3) {
                    $('.js-property-zip').each(function() {
                        console.log($(this).val())
                        if (($(this).val() == '') || ($(this).val() == undefined) || ($(this).val() == null)) {
                            _check = false;
                        }
                    });

                    if (_check) {
                        $('.js-property-addr').each(function() {
                            console.log($(this).val())
                            if (($(this).val() == '') || ($(this).val() == undefined) || ($(this).val() == null)) {
                                _check = false;
                            }
                        });
                    }
                }

                return _check;
            }
            
            //
            function buyerOwnerWebDetail() {
                let url = 'buyerOwnerWebDetail.php?cId=<{$data_case.cCertifiedId}>';
                $.colorbox({iframe:true, width:"800px", height:"100%", href:url}) ;
            }

            function cloneBuildingLand(no) {
                $('.building_land_' + no).first().clone().insertAfter('.building_land_' + no + ':last');
                $('.building_land_' + no).last().find(':input').val('');
            }

            function cloneNewBuildingLand(no) {
                $('.new_building_land_' + no).first().clone().insertAfter('.new_building_land_' + no + ':last');
                $('.new_building_land_' + no).last().find(':input').val('');
            }

            function deleteBuildingLand(item) {
                $(item).closest('div').remove();
            }

            function AddLegalNotify(cId) {
                let _item = $('[name="lItem"] :selected').val();
                let _date = $('[name="lDate"]').val();
                let _remark = $('[name="lRemark"]').val();

                if (_item == 0) {
                    alert('請指定催告事項');
                    $('[name="lItem"]').focus().select();
                    return;
                }

                if (!_date) {
                    alert('請選取催告到期日');
                    $('[name="lDate"]').focus().select();
                    return;
                }

                let url = '/includes/escrow/legalNotify.php';
                $.post(url, {'cId': cId, 'item': _item, 'date': _date, 'remark': _remark}, function(response) {
                    if (response.status == 200) {
                        alert('更新完成');
                    } else {
                        console.log(response);
                        alert('更新失敗');
                    }
                }, 'json')
                .fail(function() {
                    alert('系統失敗！無法完成操作');
                });
            }

            function land_before_transfer_edit() {
                let cId = $('[name="certifiedid"]').val();
                let url = 'landBeforeTransferEdit.php?cId=' + cId;
                $.colorbox({iframe:true, width:"800px", height:"100%", href:url}) ;
            }

            function downloadKuCSV(cId) {
                let el = '<input type="hidden" name="cId" value="' + cId + '">';
                $('#ku').empty().append(el).submit();
            }
        </script>
        <style type="text/css">
            .transfer-class {
                float: left;
                width: 150px;
                /* border: 1px solid; */
                padding: 5px;
            }

            .transfer-power {
                /* width: 250px; */
                /* border: 1px solid; */
                padding: 5px;
            }

            .dis{
                display:none; 
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

            .note_msg td{
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

            #trans_build,#land_price  {
                 background: #FFAC55;
            }

            #trans_build,#land_price:hover{
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
                width:100px;
            }

            .ui-autocomplete {
                width:100px;
                max-height: 300px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
                /* add padding to account for vertical scrollbar */
                padding-right: 20px;
            }

            .ui-autocomplete-input {
                width:100px;
            }

            .selectCombobox{
                width: 150px;
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

            .checkCertifiedFee{
                /*background-color: #FFF;*/
               border:2px solid #850000;
            }

            .upload_area{
                border: 1px solid #999;
                padding: 5px;
                height: 100px;
                text-align: center;
                background-color: #FFF;
                cursor: pointer
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

            /* 進度條的css */
            .uploadProgress {
                position:relative; 
                width:100%; 
                border: 1px solid #ddd; 
                padding: 1px; 
                border-radius: 3px; 
            }

            .uploadProgressOCR {
                position:relative; 
                width:100%; 
                border: 1px solid #ddd; 
                padding: 1px; 
                border-radius: 3px; 
            }

            .uploadBar { 
                background-color: #9e2925; 
                width:0%; 
                height:20px; 
                border-radius: 3px; 
            }

            .uploadBarOCR { 
                background-color: #9e2925; 
                width:0%; 
                height:20px; 
                border-radius: 3px; 
            }

            .uploadPercent { 
                position:absolute; 
                display:inline-block; 
                left:48%; 
                color: #FFF;
                top:3px; 
            }

            .uploadPercentOCR { 
                position:absolute; 
                display:inline-block; 
                left:48%; 
                color: #FFF;
                top:3px; 
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

            .legal-warning {
                color: #0000FF;
            }
        </style>
    </head>
    <body id="dt_example">
        <div class="cmc_overlay" style="display:none;">
            <div class="cmc_overlay__inner">
                <div class="cmc_overlay__content"><span class="cmc_spinner"></span></div>
            </div>
        </div>

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
                                    <{if $smarty.session.member_pDep == 4 ||  $smarty.session.member_pDep == 7  || $smarty.session.member_pDep == 1 || $smarty.session.pFeedBackAudit == 1|| $smarty.session.member_id == 36 }>
                                    <li><a href="#tabs-backmoney2">回饋金修改申請</a></li>
                                    <{/if}>

                                    <{* <{if p$smarty.session.member_pDe|in_array: [1, 5, 6]}> *}>
                                    <{if $smarty.session.member_id == 6 && $data_case.cCaseStatus == 2}>
                                        <{if $is_edit == '1'}>  
                                        <li><a href="#tabs-ocr">謄本解析</a></li>
                                        <{/if}>
                                    <{/if}>

                                    <{if $smarty.session.member_id == 6 && $data_case.cCaseStatus == 2}>
                                        <{if $is_edit == '1'}>  
                                        <li><a href="#tabs-sign">線上簽署</a></li>
                                        <{/if}>
                                    <{/if}>
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
                                       <{if $data_case.cRelatedCase != ''}>
                                       <tr>
                                           <th>連件︰</th>
                                           <td colspan="3" style="word-break:break-all"><{$data_case.cRelatedCase}></td>
                                       </tr>
                                       <{/if}>
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
                                            <td>
                                                <{foreach from=$data_buyer5 key=key item=item}>
                                                <{$item.cName}>,
                                                <{/foreach}>
                                            </td>
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
                                        <tr>
                                            <th>仲介品牌︰</th>
                                            <td><{$brand_type4}></td>
                                            <th>仲介店名︰</th>
                                            <td><{$branch_type4}></td>
                                        </tr>
                                        <{foreach from=$data_property key=key item=item}>
                                        <tr>
                                            <th>標的物座落︰</th>
                                            <td colspan="3"><{$item.cAddr_country}><{$item.cAddr}></td>
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
                                                <td colspan="7" class="tb-title">
                                                    帳務收支明細 <a href="bankTransConfirmCall.php?action=contract&cid=<{$data_case.cCertifiedId}>&bid=" class="iframe" style="font-size:9pt;">(照會)</a>
                                                </td>                                         
                                            </tr>
                                            <tr>
                                                <th style="width:110px;">日期</th>
                                                <th style="width:150px;">帳款摘要</th>
                                                <th style="width:110px;">收入</th>
                                                <th style="width:90px;">支出</th>
                                                <th style="width:90px;">餘額</th>
                                                <th style="width:170px;">備註</th>
                                            </tr>
                                            <{$tbl}>
                                            <tr>
                                                <th style="width:110px;" colspan="7">&nbsp;</th>
                                            </tr>
                                            <tr style="background-color:#FFFFFF;">
                                                <td>&nbsp;</td>
                                                <td style="text-align:right;">合計</td>
                                                <td style="text-align:right;"><{$incomeTotal}></td>
                                                <td style="text-align:right;"><{$outgoingTotal}></td>
                                                <td style="text-align:right;"><{$total}>&nbsp; </td>
                                                <td colspan="2">(收入-支出) 
                                                    <{if $minus_money > 0}>
                                                    <font color="red">(NT$<{$minus_money}>不可動用)</font>
                                                    <{/if}>
                                                    <{if $taishinSPMoney > 0}>
                                                    <font color="blue">(NT$<{$taishinSPMoney}>未返還)</font>
                                                    <{/if}>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div style="background-color:#FFFFFF;text-align:center;padding-bottom:10px;padding-top:10px;">
                                        <{if $is_edit == 1}>
                                            <{if $data_case.cSignCategory != 2 && $data_case.cCaseStatus != 11}>
                                            <input type="button" id="trans_build" value="出款建檔" class="trans_build" >
                                            <{/if}>
                                            <{if $smarty.session.member_pDep == 6 && $data_case.cCaseStatus == 11}>
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
                                            <th colspan="6">&nbsp;</th>
                                        </tr>
                                        <tr>
                                            <th>銀行別︰</th>
                                            <td>
                                                <{html_options name=case_bank options=$menu_categorybank_twhg selected=$data_case.cBank disabled="disabled"}>
                                            </td>
                                            <th>保證號碼︰</th>
                                            <td>
                                                <input type="hidden" name="certifiedid"  maxlength="10" value="<{$data_case.cCertifiedId}>" disabled="disabled"/>
                                                <input type="text" name="certifiedid_view"  maxlength="10" class="input-text-big" value="<{$data_case.cCertifiedId}>" disabled="disabled"/>
                                            </td>
                                            <th>群義契約編號︰</th>
                                            <td>
                                                <input type="text" name="data_bankcode_No72" id="data_bankcode_No72" class="input-text-big" value="<{$data_bankcode.bNo72}>">
                                            </td>   
                                        </tr>
                                        <tr>
                                            <th>案件狀態︰</th>
                                            <td>
                                                <{if $disabled_caseStatus == '' || $data_case.cCaseStatus == 10 || $smarty.session.member_id|in_array:[1, 6, 12, 22, 117]}>
                                                <{html_options name="case_status" onchange="chk_status()" options=$menu_statuscontract selected=$data_case.cCaseStatus}>
                                                <{else}>
                                                <{html_options name="case_status" onchange="chk_status()" options=$menu_statuscontract selected=$data_case.cCaseStatus disabled="disabled"}>
                                                <{/if}>
                                            </td>
                                            <th>實際點交日︰</th>
                                            <td>
                                                <{if $data_case.cEndDate == '0000-00-00 00:00:00' || $data_case.cCaseStatus==2}>
                                                <input type="text" name="case_cEndDate" maxlength="10" class="input-text-big" value="" onclick="showdate(form_case.case_cEndDate)" readonly />
                                                <{else}>
                                                    <{if $disabled_caseStatus == ''}>
                                                    <input type="text" name="case_cEndDate" maxlength="10" class="input-text-big" value="<{$data_case.cEndDate}>" onclick="showdate(form_case.case_cEndDate)" readonly/>
                                                    <{else}>
                                                    <input type="text" name="case_cEndDate" maxlength="10" class="input-text-big" value="<{$data_case.cEndDate}>" onclick="showdate(form_case.case_cEndDate)" disabled/>
                                                    <{/if}>
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
                                            <th class="">結案日期︰</th>
                                            <td>
                                                <input type="text"   maxlength="10" class="input-text-big" value="<{$data_case.cFeedbackDate}>" disabled />
                                            </td>
                                            <th>
                                                履保費出款日︰
                                            </th>
                                            <td>
                                                <input type="text"   maxlength="10" class="input-text-big" value="<{$CertifyDate}>" disabled />
                                            </td>                                
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
                                            <th>連件︰<br><span style=" font-size:10px;color:red">(請用,分隔)</span></th>
                                            <td colspan="3">
                                                <textarea name="relatedCase" cols="60" rows="2"><{$data_case.cRelatedCase}></textarea>
                                            </td>
                                            <th>報表上傳︰</th>
                                            <td colspan="1">
                                                <{html_options name="case_reportupload" options=$menu_reportupload selected=$data_case.cCaseReport}>
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
                                                NT$<input name="income_signmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose scrivenerClose" value="<{$data_income.cSignMoney}>" />元
                                            </td>
                                            <th>用印款︰</th>
                                            <td>
                                                NT$<input name="income_affixmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose scrivenerClose" value="<{$data_income.cAffixMoney}>" />元
                                            </td>
                                            <th>完稅款︰</th> 
                                            <td>
                                                NT$<input name="income_dutymoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose scrivenerClose" value="<{$data_income.cDutyMoney}>" />元
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>尾款︰</th>
                                            <td>
                                                NT$<input name="income_estimatedmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose scrivenerClose" value="<{$data_income.cEstimatedMoney}>" />元
                                            </td>
                                            <th>未入專戶︰</th>
                                            <td>
                                                NT$<input name="income_nointomoney" type="text" maxlength="16" size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cNotIntoMoney}>"/>元
                                            </td>
                                            <th class="th_title_sml"><div id="unfo" tabindex="-1"></div>降保(履保費要少收才需填入金額)</th>
                                            <td>NT$<input name="income_firstmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right feedbackClose scrivenerClose" value="<{$data_income.cFirstMoney}>" />元</td>
                                            
                                        </tr>
                                         <tr>
                                            <th>買賣總價金︰</th>
                                            <td colspan="5" >
                                                NT$<input name="income_totalmoney" type="text" maxlength="16" style="text-align:right;" size="12" value="<{$data_income.cTotalMoney}>" disabled='disabled' class=" text-right feedbackClose currency-money1"/>元 
                                                (含營業稅：<input type="text" name="income_businessTax"  size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cBusinessTax}>">元;
                                                土地：<input name="income_land" type="text" maxlength="16" size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cLand}>" />元;
                                                建物：<input name="income_building" type="text" maxlength="16" size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cBuilding}>" />元;
                                                車位價款<input name="income_parking" type="text" maxlength="16" size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cParking}>" />元，如未分開計價者免填;

)<br>
                                                (含訂金: <input type="text" name="income_depositMoney"  size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cDepositMoney}>">元整 交由特約地政士存匯入履保專戶)
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>保證費金額︰</th> 
                                            <td>
                                                NT$<input name="income_certifiedmoney" type="text" maxlength="16" class="text-right currency-money1 feedbackClose scrivenerClose" size="12"  value="<{$data_income.cCertifiedMoney}>"  style="text-align:right;"<{$certifiedchg}>  />元
                                            </td>
                                            <th>承諾書金額：</th>
                                             <{if $smarty.session.member_pDep == 6 || $smarty.session.member_pDep == 1}>
                                            <td>NT$<input type="text" name="income_commitmentmoney" class="text-right currency-money1" value="<{$data_income.cCommitmentMoney}>" size="12" style="text-align:right;">元</td>
                                            <{else}>
                                            <td>NT$<input type="text" name="income_commitmentmoney" class="text-right currency-money1" value="<{$data_income.cCommitmentMoney}>" size="12" style="text-align:right;" disabled="disabled">元</td>
                                            <{/if}>
                                             <td><!-- 保證費比率 --></td>
                                            <td><input name="income_certifiedMoneyPower1" type="hidden" maxlength="5" class="text-right feedbackClose" size="4"  value="<{$data_income.cCertifiedMoneyPower1}>" style="text-align:right;"<{$certifiedchg}> />
                                            <input name="income_certifiedMoneyPower2" type="hidden" maxlength="5" class="text-right feedbackClose" size="8"  value="<{$data_income.cCertifiedMoneyPower2}>" style="text-align:right;"<{$certifiedchg}> /></td>
                                        </tr>
                                        <tr>
                                            <th>解約條款：</th>                                           
                                            <td colspan="5">
                                                <{html_radios name=case_cancellingClause options=$inputSelect2 selected=$data_case.cCancellingClause}>  
                                            </td>
                                        </tr>  
                                        <tr>
                                            <th class="checkCertifiedFee" style="display: none">未收足審核︰</th>
                                            <td class="checkCertifiedFee" style="display: none" colspan="3">
                                                業務:<{$data_income.cInspetorName}><br>業務主管: <{$data_income.cInspetorName2}>
                                            </td>
                                            <td colspan="2" style="display: none"  class="checkCertifiedFee">
                                                <{if $data_income.cInspetor2 == 0 && $data_income.cReasonCategory == 0}>
                                                <input type="button" value="發送通知" onclick="sendNotice()" id="line">
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="checkCertifiedFee" style="display: none">未收足原因︰</th>
                                            <td colspan="5" class="checkCertifiedFee" style="display: none">
                                                <{if $data_income.cReasonCategory == 0}>
                                                <{assign var='ck' value='checked=checked'}> 
                                                <{else}>
                                                <{assign var='ck1' value='checked=checked'}> 
                                                <{/if}>

                                                <input type="radio" name="income_reason_cat" value="0" <{$ck}>>
                                                <{if $data_income.cInspetor2 > 0}>
                                                <input type="text" name="income_reason" value="<{$income_reason}>" style="width: 85%" readonly disabled>
                                                <input type="radio" name="income_reason_cat" value="1" disabled <{$ck1}>>預售屋
                                                <{else}>
                                                <input type="text" name="income_reason" value="<{$income_reason}>" style="width: 85%">  
                                                <input type="radio" name="income_reason_cat" value="1" <{$ck1}>>預售屋 
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>一般增值稅︰</th>
                                            <td>
                                                NT$<input name="income_addedtaxmoney" type="text" maxlength="16" size="12" class="currency-money1 text-right scrivenerClose" value="<{$data_income.cAddedTaxMoney}>"/>元
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                賣方出款選項
                                            </td>
                                        </tr>
                                        <tr class="ownerNote">
                                            <th>&nbsp;</th>
                                            <td colspan="5">
                                            <{foreach $menuSellerNote as $key => $item}>
                                                <label><input type="checkbox" name="sellerTarget[]" value="<{$key}>" <{if $key|in_array: $dataSellerNote.another }>checked="checked"<{/if}>> <{$item}></label>
                                                <{if $key|in_array: [1,3,4] }> 受領人的關係：<input type='text' name="relation<{$key}>"
                                                    <{if $key == 1 }> value="<{$dataSellerNote.relation1}>" <{/if}>
                                                    <{if $key == 3 }> value="<{$dataSellerNote.relation3}>" <{/if}>
                                                    <{if $key == 4 }> value="<{$dataSellerNote.relation4}>" <{/if}>
                                                > <{/if}><br>
                                            <{/foreach}>
                                                <input type="text" name="sellerNote" id="" style="width: 90%;" value="<{$dataSellerNote.anotherNote}>" />
                                            </td>
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
                                                                <td ><{$data_owner.cName}> <{$data_owner.cIdentifyId}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_owner1" class="currency-money1 text-right"><{$data_owner.cInvoiceMoney}></span>元
                                                                    <input type="checkbox" class="show_owner1_check" data-identifyid="<{$data_owner.cIdentifyId}>" data-money="inv" <{if $data_owner.cInvoiceMoneyCheck == 1 }>checked <{/if}> >
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
                                                                            <{if $item.cInvoicePrint == 'Y'}>
                                                                            <font color="red">[列印]</font>
                                                                            <{/if}>
                                                                        ;
                                                                        <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{assign var='i' value='2'}>
                                                            <{foreach from=$data_owner_other key=key item=item}>
                                                            <{assign var='id' value=$item.cId}>
                                                            <tr>
                                                                <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_owner<{$i}>" class="currency-money1 text-right">NT$<{$item.cInvoiceMoney}></span>元
                                                                    <input type="checkbox"  class="show_owner_check" data-identifyid="<{$item.cIdentifyId}>" data-money="inv" <{if $item.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
                                                                    <input type="hidden" class="inv_show_owner" name="inv_show_owner<{$i}>" value="<{$item.cInvoiceMoney}>">
                                                                </td>
                                                                <td>
                                                                    <span id="owner_invdonate<{$i++}>">
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
                                                                            <{if $item.cInvoicePrint == 'Y'}>
                                                                            <font color="red">[列印]</font>
                                                                            <{/if}>
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
                                                                <td ><{$data_buyer.cName}> <{$data_buyer.cIdentifyId}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_buyer1" class="currency-money1 text-right"><{$data_buyer.cInvoiceMoney}></span>元
                                                                    <input type="checkbox" class="show_buyer1_check" data-identifyid="<{$data_buyer.cIdentifyId}>"  data-money="inv" <{if $data_buyer.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
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
                                                                        <{if $item.cInvoicePrint == 'Y'}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
                                                                        ;
                                                                        <{/if}>
                                                                    <{/foreach}>
                                                                </td>
                                                            </tr>
                                                            <{assign var='i' value='2'}>
                                                            <{foreach from=$data_buyer_other key=key item=item}>
                                                            <{assign var='id' value=$item.cId}>
                                                            <tr>
                                                                <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                                <td>
                                                                    NT$<span id="inv_show_buyer<{$i}>"  class="currency-money1 text-right">$<{$item.cInvoiceMoney}></span>元
                                                                    <input type="checkbox"  class="show_buyer_check" data-identifyid="<{$item.cIdentifyId}>" data-money="inv" <{if $item.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
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
                                                                        <{if $item.cInvoicePrint == 'Y'}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
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
                                                                <input type="checkbox"  class="show_branch_check" data-no="" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
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
                                                                        <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
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
                                                                <input type="checkbox"  class="show_branch_check" data-no="1" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck1 == 1 }>checked <{/if}>>
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
                                                                        <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
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
                                                                <input type="checkbox"  class="show_branch_check" data-no="2" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck2 == 1 }>checked <{/if}>>
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
                                                                        <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
                                                                    </span>
                                                                    ;
                                                                    <{/if}>
                                                                <{/foreach}>                                                         
                                                            </td>
                                                        </tr>
                                                        <{/if}>  
                                                        <{if $branch_type4 !=''}>
                                                        <tr class="th_title_sml">
                                                            <td ><{$branch_type4}></td>
                                                            <td>
                                                                NT$<span id="inv_show_branch<{$i}>" class="currency-money1 text-right"><{$data_realstate.cInvoiceMoney3}></span>元
                                                                <input type="checkbox"  class="show_branch_check" data-no="3" data-money="inv" <{if $data_realstate.cInvoiceMoneyCheck3 == 1 }>checked <{/if}>>
                                                               <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$i}>" value="<{$data_realstate.cInvoiceMoney3}>">
                                                            </td>
                                                            <td>
                                                               <span id="real_invdonate<{$i++}>">
                                                                    <{if $data_realstate.cInvoiceDonate3==1}>
                                                                    <font color="red">[捐贈]</font>
                                                                    <{/if}>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <{if $data_realstate.cInvoicePrint3=="Y"}>
                                                                <font color="red">[列印]</font>
                                                                <{/if}>
                                                            </td>
                                                            <td>
                                                                <{foreach from=$data_invoice_another key=key item=item}>
                                                                    <{if $item.cDBName =='tContractRealestate3'}>
                                                                    <{$item.cName}>
                                                                    NT$<span id="inv_show_branch<{$j}>" class="currency-money1 text-right"><{$item.cInvoiceMoney}></span>元
                                                                    <input type="hidden" class="inv_show_branch" name="inv_show_branch<{$j}>" value="<{$item.cInvoiceMoney}>">
                                                                    <span id="real_invdonate<{$j++}>">
                                                                        <font color="red"><{$item.cInvoiceDonate}></font>
                                                                        <{if $item.cInvoicePrint=="Y"}>
                                                                        <font color="red">[列印]</font>
                                                                        <{/if}>
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
                                                                <input type="checkbox"  class="show_scrivener1" data-money="inv" <{if $data_scrivener.cInvoiceMoneyCheck == 1 }>checked <{/if}>>
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
                                                                            <{if $item.cInvoicePrint=="Y"}>
                                                                                <font color="red">[列印]</font>
                                                                            <{/if}>
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
                                                        <td><{$data_owner.cName}> <{$data_owner.cIdentifyId}></td>
                                                        <td>NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_owner.cInterestMoney}></span>元
                                                            <input type="checkbox"  class="show_owner1_check" data-identifyid="<{$data_owner.cIdentifyId}>" data-money="int" <{if $data_owner.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                        </td>
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
                                                        <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                        <td>
                                                            NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                            <input type="checkbox"  class="show_owner_check" data-identifyid="<{$item.cIdentifyId}>" data-money="int" <{if $item.cInterestMoneyCheck == 1 }>checked <{/if}>>
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
                                                        <td><{$data_buyer.cName}> <{$data_buyer.cIdentifyId}></td>
                                                        <td>NT$<span id="int_show_owner0" class="currency-money1 text-right"><{$data_buyer.cInterestMoney}></span>元
                                                            <input type="checkbox"  class="show_buyer1_check" data-identifyid="<{$data_buyer.cIdentifyId}>" data-money="int" data-money="int" <{if $data_buyer.cInterestMoneyCheck == 1 }>checked <{/if}>>
                                                        </td>
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
                                                        <td><{$item.cName}> <{$item.cIdentifyId}></td>
                                                        <td>
                                                            NT$<span id="int_show_owner<{$i++}>" class="currency-money1 text-right">NT$<{$item.cInterestMoney}></span>元
                                                            <input type="checkbox"  class="show_buyer_check" data-identifyid="<{$item.cIdentifyId}>" data-money="int" <{if $item.cInterestMoneyCheck == 1 }>checked <{/if}>>
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
                                                            <input type="checkbox"  class="show_branch_check" data-no="" data-money="int" <{if $data_realstate.cInterestMoneyCheck == 1 }>checked <{/if}>>
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
                                                            <input type="checkbox"  class="show_branch_check" data-no="1" data-money="int" <{if $data_realstate.cInterestMoneyCheck1 == 1 }>checked <{/if}>>
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
                                                            <input type="checkbox"  class="show_branch_check" data-no="2" data-money="int" <{if $data_realstate.cInterestMoneyCheck2 == 1 }>checked <{/if}>>
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
                                                    <{if $branch_type4 !=''}>
                                                    <tr class="th_title_sml">
                                                        <td><{$branch_type4}></td>
                                                        <td>
                                                            NT$<span id="int_show_branch2" class="currency-money1 text-right"><{$data_realstate.cInterestMoney3}></span>元
                                                            <input type="checkbox"  class="show_branch_check" data-no="3" data-money="int" <{if $data_realstate.cInterestMoneyCheck3 == 1 }>checked <{/if}>>
                                                        </td>
                                                        <td>
                                                           <{foreach from=$data_int_another key=key item=item}>
                                                                <{if $item.cDBName =='tContractRealestate3'}>
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
                                                            <input type="checkbox"  class="show_scrivener1" data-money="int" <{if $data_scrivener.cInterestMoneyCheck == 1 }>checked <{/if}>>
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
                                         <{if $data_case.cProperty==1 || $data_case.cProperty==2}>
                                        <tr>
                                            <th style="height:40px">賣方就本買賣標的</th>
                                            <td colspan="5">
                                                
                                                <{if $data_case.cProperty==1}>
                                                <input type="radio" name="case_property" value="1" checked>保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，或雖有前開原因經買方定七日期限仍無法排除，買賣雙方同意無條件解約。<br>
                                                <input type="radio" name="case_property" value="2">已告知買方為受農地套繪管制，買方仍同意依約履行。
                                                <{else if  $data_case.cProperty==2}>
                                                <input type="radio" name="case_property" value="1">保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，或雖有前開原因經買方定七日期限仍無法排除，買賣雙方同意無條件解約。<br>
                                                <input type="radio" name="case_property" value="2"  checked>已告知買方為受農地套繪管制，買方仍同意依約履行。
                                                <{else}>
                                               <!--  <input type="radio" name="case_property" value="1">保證確實未受農地套繪管制或做為他棟建築物法定空地或通行權使用，否則買方得無條件解約。<br>
                                                <input type="radio" name="case_property" value="2" >已告知買方為受農地套繪管制，買方仍同意依約履行。 -->
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <th>土地類別</th>
                                            <td colspan="5">
                                                
                                                1.農地 <br>
                                                 <{html_checkboxes name=landCategoryLand options=$menu_landCategoryLand selected=$data_LandCategory.cLand separator="<br>"}>
                                              
                                               <br>
                                            2.建地 <br>
                                            <{html_checkboxes name=landCategoryBuild options=$menu_landCategoryBuild selected=$data_LandCategory.cBuild separator="<br>"}>
                                              <br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td colspan="5">
                                                本買賣標的應於點交前完成土地鑑界作業，鑑界規費由賣方負擔（如需整地，施作及費用
                                                <{html_radios name=LandFee options=$menu_LandFee selected=$data_LandCategory.cLandFee}>
                                              
                                                。土地鑑界結果如與本契約約定或權利登記內容不符者，買賣雙方同意以土地總價款換算單位土地面積之單價計算找補；作共用壁用地減少面積情形而未告知者，亦同。
                                            </td>
                                        </tr>
                                      
                                        <tr>
                                            <td colspan="6" class="tb-title">
                                                其他
                                                <div style="float:right;padding-right:10px;"> <a href="contract_special.php?cCertifyId=<{$data_case.cCertifiedId}>&sign=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修特約事項</a> </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="tb-title">備註</td>
                                           <!--  <td colspan="5">
                                                <textarea rows="5" name="invoice_remark" class="input-text-per"><{$data_invoice.cRemark}></textarea>
                                            </td> -->
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="note_msg">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0"> 
                                                    <tr >
                                                        <!-- <td width="5%" align="center" class="tb-title2">序號</td> -->
                                                        <td width="20%" align="left" class="tb-title2">日期/時間</td>
                                                        <td width="10%" align="left" class="tb-title2">承辦</td>
                                                        <td width="60%"align="left" class="tb-title2">內容</td>
                                                        <td width="5%"align="center" class="tb-title2">刪除</td>
                                                    </tr>
                                                    <{foreach from=$contractNote[4] key=key item=item}>
                                                   <tr>
                                                       <!-- <td width="5%" align="center"><{$item.no}></td> -->
                                                       <td width="20%"><{$item.cCreatTime}></td>
                                                       <td width="10%"><{$item.cCreator}></td>
                                                       <td width="60%"><{$item.cNote}></td>
                                                       <td width="5%" align="center">
                                                            <a href="javascript:void(0)" onclick="DelNoteMsg(<{$item.cId}>)">刪除</a>
                                                        </td>
                                                   </tr>
                                                    <{/foreach}>
                                     
                                                </table>
                                            </td>
                                            
                                        </tr>
                                        <tr><td colspan="6"><hr></td></tr> 
                                        <tr>
                                            <th >備註內容</th>
                                            <td colspan="5">
                                                <div style="float: left;display:inline;height: 40px;">
                                                    <textarea name="invoice_remark" id="" cols="90" rows="2"></textarea>
                                                </div>
                                                <div style="float: left;display:inline;height: 40px; line-height:40px;padding-left: 10px">
                                                    <input type="button" value="新增" onclick="AddNoteMsg()">
                                                </div>    
                                                
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>催告通知</th>
                                            <td colspan="5" style="border-top: 1px solid;">
                                                <div style="padding: 5px;">
                                                    <span style="">事　　項：</span>
                                                    <span style="padding-right: 20px;">
                                                        <{if $legal_record_edit == "disabled"}>
                                                        <{html_options name="lItem" options=$menu_legal_items selected=$selected_legal_items disabled="disabled"}>
                                                        <{else}>
                                                        <{html_options name="lItem" options=$menu_legal_items selected=$selected_legal_items}>
                                                        <{/if}>
                                                    </span>
                                                    <span style="">催告到期日：</span>
                                                    <span style="padding-right: 20px;">
                                                        <input type="date" name="lDate" value="<{$legal_record.lDate}>" <{$legal_record_edit}>>
                                                    </span>
                                                </div>
                                                <div style="padding-left: 5px;">
                                                    <span style="vertical-align: top;">催告備註：</span>
                                                    <span style="">
                                                        <textarea name="lRemark" cols="80" rows="5" <{$legal_record_edit}>><{$legal_record.lRemark}></textarea>
                                                    </span>
                                                    
                                                    <input type="button" style="vertical-align: top;" value="更新" onclick="AddLegalNotify('<{$data_case.cCertifiedId}>')" <{$legal_record_edit}>>
                                                </div>
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
                                            <td colspan="3">
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
                                            <th>指定前次對象：</th>
                                            <td>
                                                <input type="button" onclick="land_before_transfer_edit()" value="編輯" class="bt4" style="display:;width:100px;height:40px;">
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
                                            
                                            <th>面積︰</th>
                                            <td>
                                                <input type="text" name="land_measure" maxlength="10" size="12" class="text-right" value="<{$data_land.cMeasure}>" onKeyup="checkCalTax()"/>M<sup>2</sup>
                                                <input type="hidden" name="changeLand">
                                            </td>
                                            <th>使用分區︰</th>
                                            <td>
                                                <{html_options name=land_category options=$menu_categoryarea selected=$data_land.cCategory}>
                                            </td>
                                            <th><!-- 地目︰ --></th>
                                            <td>
                                                <!--  <{html_options name=land_land4 options=$menu_categoryland selected=$data_land.cLand4 }> -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>公告土地現值︰</th>
                                            <td>
                                                <input type="text" name="land_money" maxlength="16" size="13" class="currency-money1 text-right" value="<{$data_land.cMoney}>" onKeyup="checkCalTax()"/>元/M<sup>2</sup>
                                            </td>
                                            <th>權利範圍︰</th>
                                            <td colspan="2">
                                                <input type="text" name="lpower1" size="10" class="text-right" value="<{$data_land.cPower1}>" onKeyup="checkCalTax()"/> / 
                                                <input type="text" name="lpower2" size="10" class="text-right" value="<{$data_land.cPower2}>" onKeyup="checkCalTax()"/>
                                            </td>
                                            <td> 
                                                <{if $limit_show == '0'}>
                                                <input type="button" onclick="land_edit()" value="編輯土地資料" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                            </td>
                                        </tr>
                                        
                                        <{foreach from=$landPrice key=key item=item}>
                                        <tr>
                                            <th class="th_title_sml">前次移轉現值或原規定地價</th>
                                            <td >
                                                <input type="hidden" name="land_id[]" value="<{$item.cId}>">
                                                <input type="text" name="land_movedate[]" placeholder="000-00" size="4" maxlength="7" class="calender date-field text-right" value="<{$item.cMoveDate}>" onKeyup="checkCalTax()" onclick="showdate_m(this)"/>
                                               
                                                <input type="text" name="land_landprice[]" maxlength="13" size="10" class="calender currency-money2 text-right" value="<{$item.cLandPrice}>" onKeyup="checkCalTax()" />元/M<sup>2</sup>

                                            </td>
                                        
                                            <th class="th_title_sml">前次移轉現值或原規定地價權利範圍</th>
                                            <td colspan="2"><input type="text" class="text-right" name="land_power1[]" value="<{$item.cPower1}>" size="10" onKeyup="checkCalTax()"/> / <input type="text" class="text-right" name="land_power2[]" value="<{$item.cPower2}>" size="10" onKeyup="checkCalTax()"/></td>
                                            
                                           <td>
                                                <{if $is_edit == 1}>
                                                   <{if $key == 1}>
                                                    <input type="button" onclick="land_price_edit(0)" value="編輯多組前次移轉" id="land_price">
                                                   <{/if}>
                                               <{/if}>
                                           </td>
                                        </tr>
                                        <{/foreach}>
                                       
                                        <tr>
                                            <th colspan="2">買賣標的如為農地，賣方同意約定按</th>
                                            <td colspan="4">
                                            <{if $data_land.cFarmLand==1 || $data_land.cFarmLand==0}>
                                                <input type="radio" name="land_farmland" value="1" checked>一般稅率申報
                                                <input type="radio" name="land_farmland" value="2" >申請不課徵土地增值稅，但因可歸責於賣方之事由而無法辦理時 (如無法提供農用證明)，則逕按一般稅率完納 , 賣方絶無異 。
                                            <{else}>
                                                <input type="radio" name="land_farmland" value="1">一般稅率申報
                                                <input type="radio" name="land_farmland" value="2" checked>申請不課徵土地增值稅，但因可歸責於賣方之事由而無法辦理時 (如無法提供農用證明)，則逕按一般稅率完納 , 賣方絶無異 。
                                            <{/if}>
                                                
                                            </td>

                                        </tr>
                                       
                                    </table>   
                                </div>
                                <div id="tabs-build">
                                    <div style="float:right;padding-right:10px;">
                                        <a href="#" style="font-size:9pt;" id="new_build">新增物件</a>
                                        <input type="hidden" name="buildcount" value="<{$data_property_count}>">
                                        <input type="hidden" name="new_buildcount" value="0">
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
                                                    <input type="hidden" name="property_zip<{$item.cItem}>" id="property_zip<{$item.cItem}>" value="<{$item.cZip}>" class="js-property-zip"/>
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
                                                    <input style="width:330px;" class="pAddr js-property-addr" name="property_addr<{$item.cItem}>" value="<{$item.cAddr}>" onkeyup="checkAddr(<{$item.cItem}>)"/>

                                                    <{if $item.cItem == 0}>
                                                    <span id="showSameAddr<{$item.cItem}>"></span>
                                                    <input type="button" name="import<{$item.cItem}>" value="匯入資料" style="display:none">
                                                    <{/if}>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th valign="top">建物座落地號︰</th>

                                                <td colspan="5" id="building_land_<{$item.cItem}>">
                                                    <div style="text-align:right;">
                                                        <a href="Javascript:void(0);" style="font-size:10pt;padding:right:10px;" onclick="cloneBuildingLand(<{$item.cItem}>)">新增建物座落地號</a>
                                                    </div>
                                                    <{if empty($item.buildingLand)}>
                                                    <div class="building_land_<{$item.cItem}>" style="padding-bottom:5px;" id="<{$_k}>">
                                                        <span style="padding-right: 10px;">
                                                            <input type="text" style="width:130px;padding-right:20px;" name="buildingLandSession_<{$item.cItem}>[]" id="" value="">&nbsp;段
                                                        </span>
                                                        <span style="padding-right: 20px;">
                                                            <input type="text" style="width:130px;padding-right:20px;" name="buildingLandSessionExt_<{$item.cItem}>[]" id="" value="">&nbsp;小段
                                                        </span>
                                                        <span>建物座落地號︰</span>
                                                        <span><input type="text" style="width:130px;" name="buildingLandNo_<{$item.cItem}>[]" id="" value=""></span>
                                                        <span>

                                                        </span>
                                                    </div>
                                                    <{else}>
                                                    <{foreach from=$item.buildingLand key=_k item=_v}>
                                                    <div class="building_land_<{$item.cItem}>" style="padding-bottom:5px;" id="<{$_k}>">
                                                        <span style="padding-right: 10px;">
                                                            <input type="text" style="width:130px;padding-right:20px;" name="buildingLandSession_<{$item.cItem}>[]" id="" value="<{$_v.cBuildingSession}>">&nbsp;段
                                                        </span>
                                                        <span style="padding-right: 20px;">
                                                            <input type="text" style="width:130px;padding-right:20px;" name="buildingLandSessionExt_<{$item.cItem}>[]" id="" value="<{$_v.cBuildingSessionExt}>">&nbsp;小段
                                                        </span>
                                                        <span>建物座落地號︰</span>
                                                        <span><input type="text" style="width:130px;" name="buildingLandNo_<{$item.cItem}>[]" id="" value="<{$_v.cBuildingLandNo}>"></span>
                                                        <span>
                                                            <a href="Javascript:void(0);" style="font-size:10pt;" onclick="deleteBuildingLand(this)">刪除</a>
                                                        </span>
                                                    </div>
                                                    <{/foreach}>
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
                                                <th>實際移轉面積</th>
                                                <td colspan="3">
                                                    <input type="text" name="property_actualArea<{$item.cItem}>" value="<{$item.cActualArea}>" size="8"  readonly >M<sup>2</sup>
                                                </td>
                                            </tr>
                                            <tr>
                                                
                                               <th><span class="th_title_sml">隨同主建物轉移<br>共同使用部分︰</span></th>
                                                <td colspan="5">面積：<input type="text" name="property_publicmeasuretotal<{$item.cItem}>" size="8" value="<{$item.cPublicMeasureTotal}>">&nbsp;持分<input type="text" name="property_publicmeasuremain<{$item.cItem}>" size="8" value="<{$item.cPublicMeasureMain}>"></td>

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
                                                <th>預計交屋日︰</th>
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
                                                顧代書系統專區
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>前台下載︰</th>
                                            <td>
                                                <{html_radios name="ku_download" options=$ku_download_menu selected=$ku_download separator="　"}>
                                            </td>
                                            <th>匯入檔︰</th>
                                            <td colspan="3">
                                                <button type="button" onclick="downloadKuCSV('<{$data_case.cCertifiedId}>')" class="bt4" style="padding:5px;">下載</button>
                                            </td>
                                        </tr>
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
                                                    <select name="scrivener_bankaccount" id="scrivener_bankaccount">
                                                        <option value="0"> -- </option>
                                                    </select>
                                                <{/if}>
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseS"><{$scrivener_sales}></span></td>
                                            <td></td>
                                            <td >
                                                
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_edit()" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
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
                                                <input type="hidden" name="sFeedbackMoney" value="<{$scr_sFeedbackMoney}>">
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
                                                <input type="hidden" name="scrivener_BrandScrRecall3" value="<{$data_case.cBrandScrRecall3}>">

                                                <input type="hidden" name="scrivener_BrandRecall" value="<{$data_case.cBrandRecall}>">
                                                <input type="hidden" name="scrivener_BrandRecall1" value="<{$data_case.cBrandRecall1}>">
                                                <input type="hidden" name="scrivener_BrandRecall2" value="<{$data_case.cBrandRecall2}>">
                                                <input type="hidden" name="scrivener_BrandRecall3" value="<{$data_case.cBrandRecall3}>">
                                                
                                            </td>
                                        </tr>

                                        <{else}>
                                        <tr>
                                            <td><input type="hidden" class="input-text-pre" style="width:100px;" name="sRecall" value="<{$data_case.cScrivenerRecall}>"  disabled="disabled">
                                            <input type="hidden" name="sSpRecall" value="">
                                            <input type="hidden" name="sFeedbackMoney" value="">
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
                                        <{if $feedbackAccount != false}>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    地政士回饋金帳戶
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>身分別︰</th>
                                                <td><{$feedbackAccount.identity}></td>
                                                <th>證件號碼︰</th>
                                                <td><{$feedbackAccount.idNumber}></td>
                                                <th>戶名︰</th>
                                                <td><{$feedbackAccount.accountName}></td>
                                            </tr>
                                            <tr>
                                                <th>總行︰</th>
                                                <td><{$feedbackAccount.bankMain}></td>
                                                <th>分行︰</th>
                                                <td><{$feedbackAccount.bankBranch}></td>
                                                <th>帳號︰</th>
                                                <td><{$feedbackAccount.account}></td>

                                            </tr>
                                        <{/if}>
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
                                            <td colspan="5" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                                    仲介資料 <{html_radios name="cServiceTarget" options=$STargetOption selected=$cServiceTarget seperator=' ' onClick = "feedback_money()"}>
                                                    <input type="hidden" name="checkCase3">
                                                <{else}>
                                                     仲介資料 <{html_radios name="cServiceTarget" options=$STargetOption selected=$cServiceTarget seperator=' '  disabled=disabled}>
                                                    <input type="hidden" name="checkCase3">
                                                <{/if}>
                                            </td>
                                            <td class="tb-title th_title_sml">
                                                <{if $showAffixBranch.cBrand == '' }>
                                                <div style="display:<{$showAffixBranch.cBrand}>">
                                                    <{if $data_realstate.cAffixBranch == 1}>
                                                        <{assign var='ck' value='checked=checked'}> 
                                                    <{else}>
                                                        <{assign var='ck' value=''}> 
                                                    <{/if}>
                                                    <input type="radio" name="cAffixBranch" id="cAffixBranch" class="feedbackClose scrivenerClose" value="b" onclick="feedback_money()" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                                <{if $showAffixBranch.group18Brand == '' }>
                                                <div >
                                                    <{if $data_realstate.cAffixBranch == 1}>
                                                        <{assign var='ck' value='checked=checked'}>
                                                    <{else}>
                                                        <{assign var='ck' value=''}>
                                                    <{/if}>
                                                    <input type="checkbox" name="cAffixBranch[]" id="cAffixBranch" class="feedbackClose scrivenerClose" value="b" onclick="feedback_money()" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                                <{if $data_realstate.cBranchNum == 505}>
                                                    <input type="button" value="新增出款銀行" id="bank505" style="padding:5px;" onclick="OtherBank('<{$data_case.cCertifiedId}>')">
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr>
                                             <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                                <th><span class="sign-red">*</span>仲介品牌︰</th>
                                                <td>
                                                    <{html_options name="realestate_brand" id="realestate_brand" options=$menu_brand selected=$data_realstate.cBrand }>
                                                </td>
                                                <th><span class="sign-red">*</span>仲介商類型︰</th>
                                                <td> <{html_options name="realestate_branchcategory" id="realestate_branchcategory" options=$menu_categoryrealestate selected=$data_realstate.bCategory }></td>
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
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum}>','1')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
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
                                                <input type="hidden" name="data_feedData" value="<{$data_feedDataCount1}>">
                                                <input type="hidden" name="data_bFeedbackMoney" value="<{$data_bFeedbackMoney1}>">
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
                                                <textarea name="Feedback_CashierOrderMemo" rows="5" class="input-text-per" disabled="disabled"><{$rel1['note']}></textarea>
                                                <!-- <textarea name="Feedback_CashierOrderMemo" rows="5" class="input-text-per" disabled="disabled"><{$rel1.bCashierOrderMemo}></textarea> -->
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
                                                            <td align="center" class="th_title_sml tb-title2">戶籍<br>地址</td>
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
                                        <{if $data_realstate.cBranchNum1 == 0 && $data_realstate.cBrand != 2 && $data_case.cFeedBackScrivenerClose != 1}>
                                        <tr><td colspan="6" style="text-align:right;">
                                            <span id="addBranch2"><a href="#" style="font-size:9pt;" onclick="addBranchList('2')">(第二組仲介)</a></span></td>
                                        </tr>
                                        <{/if}>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <td colspan="5" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 && $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                                仲介資料 <{html_radios name="cServiceTarget1" options=$STargetOption selected=$cServiceTarget1 seperator=' ' onClick = "feedback_money()"}>
                                                <{else}>
                                                仲介資料 <{html_radios name="cServiceTarget1" options=$STargetOption selected=$cServiceTarget1 seperator=' ' disabled=disabled}>
                                                <{/if}>
                                            </td>
                                            <td class="tb-title th_title_sml">
                                                <{if $showAffixBranch.cBrand1 == '' }>
                                                 <div style="display:<{$showAffixBranch.cBrand1}>">
                                                    <{if $data_realstate.cAffixBranch1 == 1}>
                                                        <{assign var='ck' value='checked=checked'}> 
                                                    <{else}>
                                                        <{assign var='ck' value=''}> 
                                                    <{/if}>

                                                     <input type="radio" name="cAffixBranch" id="cAffixBranch1" class="feedbackClose scrivenerClose" onclick="feedback_money()" value="b1" <{$ck}>>賣方服務費收款店
                                                 </div>
                                                <{/if}>
                                                <{if $showAffixBranch.group18Brand1 == '' }>
                                                <div >
                                                    <{if $data_realstate.cAffixBranch1 == 1}>
                                                        <{assign var='ck' value='checked=checked'}>
                                                    <{else}>
                                                        <{assign var='ck' value=''}>
                                                    <{/if}>
                                                    <input type="checkbox" name="cAffixBranch[]" id="cAffixBranch1" class="feedbackClose scrivenerClose" value="b1" onclick="feedback_money()" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
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
                                            
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum1}>','2')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
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
                                                <input type="hidden" name="data_feedData1" value="<{$data_feedDataCount2}>">
                                                 <input type="hidden" name="data_bFeedbackMoney1" value="<{$data_bFeedbackMoney2}>">
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
                                                <!-- <textarea name="Feedback_CashierOrderMemo1" rows="5" class="input-text-per" disabled="disabled"><{$rel2.bCashierOrderMemo}></textarea> -->
                                                 <textarea name="Feedback_CashierOrderMemo1" rows="5" class="input-text-per" disabled="disabled"><{$rel2.note}></textarea>
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
                                                                <td align="center" class="th_title_sml tb-title2">戶籍<br>地址</td>
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
                                        <{if $data_realstate.cBranchNum2 == 0 && $data_realstate.cBranchNum1 != 0 && $data_case.cFeedBackScrivenerClose != 1}>
                                        <tr class="show_2_realty"><td colspan="6" style="text-align:right;"><span id="addBranch3"><a href="#" style="font-size:9pt;" onclick="addBranchList('3')">(第三組仲介)</a></span></td></tr>
                                        <{/if}>
                                        <tr class="show_3_realty"  style="display:<{$third_branch}>;">
                                            <td colspan="5" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                                仲介資料 <{html_radios name="cServiceTarget2" options=$STargetOption selected=$cServiceTarget2 seperator=' ' onClick = "feedback_money()"}>
                                                <{else}>
                                                仲介資料 <{html_radios name="cServiceTarget2" options=$STargetOption selected=$cServiceTarget2 seperator=' ' disabled=disabled}>
                                                <{/if}>
                                            </td>
                                            <td class="tb-title th_title_sml">
                                                <{if $showAffixBranch.cBrand2 == '' }>
                                                <div style="display:<{$showAffixBranch.cBrand2}>">
                                                    <{if $data_realstate.cAffixBranch2 == 1}>
                                                        <{assign var='ck' value='checked=checked'}> 
                                                    <{else}>
                                                        <{assign var='ck' value=''}> 
                                                    <{/if}>
                                                    <input type="radio" name="cAffixBranch" id="cAffixBranch2" class="feedbackClose scrivenerClose" onclick="feedback_money()" value="b2" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                                <{if $showAffixBranch.group18Brand2 == '' }>
                                                <div >
                                                    <{if $data_realstate.cAffixBranch2 == 1}>
                                                        <{assign var='ck' value='checked=checked'}>
                                                    <{else}>
                                                        <{assign var='ck' value=''}>
                                                    <{/if}>
                                                    <input type="checkbox" name="cAffixBranch[]" id="cAffixBranch2" class="feedbackClose scrivenerClose" value="b2" onclick="feedback_money()" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                            </td>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                        <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
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
                                            
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum2}>','3')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
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
                                                <input type="hidden" name="data_bFeedbackMoney2" value="<{$data_bFeedbackMoney3}>">
                                            </td>
                                        </tr>
                                        </table>
                                        <div id="branchFeedData2">
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
                                                        <td align="center" class="th_title_sml tb-title2">戶籍<br>地址</td>
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
                                        <table border="0" width="100%" class="show_4_realty" >
                                        <{if $data_realstate.cBranchNum3 == 0 && $data_realstate.cBranchNum2 != 0}>
                                        <tr class="show_3_realty"><td colspan="6" style="text-align:right;"><span id="addBranch4"><a href="#" style="font-size:9pt;" onclick="addBranchList('4')">(第四組仲介)</a></span></td></tr>
                                        <{/if}>
                                        <tr class="show_4_realty"  style="display:<{$fourth_branch}>;">
                                            <td colspan="5" class="tb-title">
                                                <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                                仲介資料 <{html_radios name="cServiceTarget3" options=$STargetOption selected=$cServiceTarget3 seperator=' ' onClick = "feedback_money()"}>
                                                <{else}>
                                                仲介資料 <{html_radios name="cServiceTarget3" options=$STargetOption selected=$cServiceTarget3 seperator=' ' disabled=disabled}>
                                                <{/if}>
                                            </td>
                                            <th class="tb-title th_title_sml">
                                                <{if $showAffixBranch.cBrand3 == '' }>
                                                <div style="display:<{$showAffixBranch}>">
                                                    <{if $data_realstate.cAffixBranch3 == 1}>
                                                        <{assign var='ck' value='checked=checked'}> 
                                                    <{else}>
                                                        <{assign var='ck' value=''}> 
                                                    <{/if}>
                                                    <input type="radio" name="cAffixBranch" id="cAffixBranch3" class="feedbackClose scrivenerClose" onclick="feedback_money()" value="b3" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                                <{if $showAffixBranch.group18Brand3 == '' }>
                                                <div >
                                                    <{if $data_realstate.cAffixBranch3 == 1}>
                                                        <{assign var='ck' value='checked=checked'}>
                                                    <{else}>
                                                        <{assign var='ck' value=''}>
                                                    <{/if}>
                                                    <input type="checkbox" name="cAffixBranch[]" id="cAffixBranch3" class="feedbackClose scrivenerClose" value="b3" onclick="feedback_money()" <{$ck}>>賣方服務費收款店
                                                </div>
                                                <{/if}>
                                            </th>
                                        </tr>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                        <{if $data_case.cCaseStatus != 3 &&  $data_case.cCaseStatus != 4 && $data_case.cFeedBackScrivenerClose != 1}>
                                            <th><span class="sign-red">*</span>仲介品牌︰</th>
                                            <td>
                                                <{html_options name="realestate_brand3" options=$menu_brand selected=$data_realstate.cBrand3 }>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介商類型︰</th>
                                            <td> <{html_options name="realestate_branchcategory3" options=$menu_categoryrealestate selected=$data_realstate.bCategory3 }></td>
                                            <th><span class="sign-red">*</span>仲介店名︰</th>
                                            <td>
                                                <input type="hidden" name="realestate_branchnum3" value="<{$data_realstate.cBranchNum3}>" />
                                               
                                                 <input type="hidden" name="branch_status3" value="<{$realestate_status3}>">
                                                <span id="realestate_branch3R">
                                                <select class="realty_branch" name="realestate_branch3">
                                                    <{$branch_options3}>
                                                </select>
                                                </span>
                                            </td>
                                        <{else}>
                                            <th><span class="sign-red">*</span>仲介品牌︰</th>
                                            <td>
                                                 <input type="hidden" name="realestate_brand3" value="<{$data_realstate.cBrand3}>" >
                                                    <input type="text" value="<{$brand_type3}>" disabled=disabled>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介商類型︰</th>
                                            <td> 
                                                <input type="hidden" name="realestate_branchcategory3" value="<{$data_realstate.bCategory3}>">
                                                <input type="text" value="<{$branch_cat4}>" disabled=disabled>
                                            </td>
                                            <th><span class="sign-red">*</span>仲介店名︰</th>
                                            <td>
                                                 <input type="hidden" name="realestate_branchnum3" value="<{$data_realstate.cBranchNum3}>" />
                                                
                                                    <input type="text" value="<{$branch_type4}>" disabled=disabled>
                                                   
                                            </td>
                                        <{/if}>
                                        </tr>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>仲介公司名稱︰</th>
                                            <td>
                                                <input type="text" name="realestate_name3" maxlength="10" class="input-text-pre" value="<{$data_realstate.cName3}>" disabled='disabled' />
                                            </td>
                                            <th>統一編號︰</th>
                                            <td>
                                                <input type="text" name="realestate_serialnumber3" maxlength="10" class="input-text-big" value="<{$rel4.bSerialnum}>" disabled="disabled" />
                                            </td>
                                            <th>電話︰</th> 
                                            <td>
                                                <input type="text" name="realestate_telarea3" maxlength="3" class="input-text-sml" value="<{$data_realstate.cTelArea3}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_telmain3" maxlength="10" class="input-text-mid" value="<{$data_realstate.cTelMain3}>" disabled='disabled' />
                                            </td>
                                        </tr>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>傳真︰</th>
                                            <td>
                                                <input type="text" name="realestate_faxarea3" maxlength="3" class="input-text-sml" value="<{$data_realstate.cFaxArea3}>" disabled='disabled' /> -
                                                <input type="text" name="realestate_faxmain3" maxlength="10" class="input-text-mid" value="<{$data_realstate.cFaxMain3}>" disabled='disabled' />
                                            </td>
                                            <th>負責業務︰</th>
                                            <td><span id="showSalseB3"><{$branchnum_data_sales3}></span></td>
                                            <td>
                                            
                                                <{if $is_edit == 1}>
                                                <input type="button" onclick="sms_realty_edit('<{$data_realstate.cBranchNum3}>','4')" value="選擇簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                                                <{/if}>
                                           
                                            </td>
                                            
                                            <{if $imgStampEdit3 == 1}>
                                                
                                                <td rowspan="2" valign="top" style="text-align:center;">
                                                    <div id="showImg3" style="margin-top:5px;width:246px;height:145px;border: 1px solid #CCC;padding:2px;">
                                                        <{$imgStamp3}>
                                                    </div>
                                                </td>
                                                
                                            <{else}>
                            
                                                <td rowspan="2" valign="top" style="text-align:center;">
                                                    &nbsp;
                                                </td>
                                                
                                            <{/if}>
                                            
                                        </tr>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>地址︰</th>
                                            <td colspan="4">
                                                <input type="hidden" name="realestate_zip3" id="realestate_zip3" value="<{$data_realstate.cZip3}>" disabled='disabled'  />
                                                <input type="text" maxlength="6" name="realestate_zip3F" id="realestate_zip3F" class="input-text-sml text-center" readonly="readonly" value="<{$data_realstate.cZip3|substr:0:3}>" disabled='disabled'  />
                                                <span id="realestate_country2R">
                                                <select class="input-text-big" name="realestate_country3" id="realestate_country3" onchange="getArea('realestate_country3','realestate_area3','realestate_zip3')" disabled='disabled' >
                                                    <{$realestate_country3}>
                                                </select>
                                                </span>
                                                <span id="realestate_area3R">
                                                <select class="input-text-big" name="realestate_area3" id="realestate_area3" onchange="getZip('realestate_area3','realestate_zip3')" disabled='disabled' >
                                                    <{$realestate_area3}>
                                                </select>
                                                </span>
                                                <input style="width:330px;" name="realestate_addr3" value="<{$data_realstate.cAddress3}>" disabled='disabled'  />
                                            </td>
                                        </tr>
                                       
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>服務費先行撥付同意書︰</th>
                                            <td>
                                                <span id="promissory4"><{$promissory4}></span>
                                                <input type="hidden" name="realestate_bRecall3" value="<{$data_realstate.bRecall3}>" />
                                                <input type="hidden" name="realestate_bScrRecall3" value="<{$data_realstate.bScrRecall3}>" />
                                            </td>
                                            <{if $smarty.session.member_pFeedBackModify!='0'}>
                                            
                                            <th>回饋比率</th>
                                            <td><span id="rea_bRecall2"><{$data_case.cBranchRecall3}></span>%</td>
                                            <th>代書回饋比率<br></th>
                                            <td><span id="rea_bScrRecall2"><{$data_case.cBranchScrRecall3}></span>%</td>
                                            <{else}>
                                            
                                            <th>&nbsp;</th>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <th>&nbsp;</th>

                                            <{/if}>
                                        </tr>
                                         <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>本票備註︰</th>
                                            <td colspan="5">
                                                <input type="text" name="Feedback_CashierOrderRemark3" maxlength="255" class="input-text-per" value="<{$rel4.bCashierOrderRemark}>" disabled="disabled">
                                            </td>
                                        </tr>
                                        <{if $smarty.session.member_pFeedBackModify!='0'}>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>回饋金備註︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_Renote3" rows="5" class="input-text-per" disabled="disabled"><{$rel3.bRenote}></textarea>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>備註說明︰</th>
                                            <td colspan="5">
                                                <textarea name="Feedback_CashierOrderMemo3" rows="5" class="input-text-per" disabled="disabled"><{$rel4.bCashierOrderMemo}></textarea>
                                                <input type="hidden" name="data_feedData3" value="<{$data_feedDataCount4}>">
                                            </td>
                                        </tr>
                                        </table>
                                        <div id="branchFeedData3">
                                        <table border="0" width="100%" class="show_4_realty" style="display:<{$fourth_branch}>;">
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
                                                        <td align="center" class="th_title_sml tb-title2">戶籍<br>地址</td>
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
                                                <th width="14%">承辦人︰</th>
                                                <td width="20%">
                                                    <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$undertaker}>" disabled="disabled"/>
                                                </td>
                                                <th width="14%">最後修改者︰</th>
                                                <td width="20%">
                                                    <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                                </td>
                                                <th width="14%">最後修改時間︰</th>
                                                <td width="18%">
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
                                                <td>
                                                    <input type="button" value="前台賣方(new)" class="btnD" style="font-size:10pt;" onclick="download('n2','<{$data_owner.cIdentifyId}>')">
                                                </td>
                                                <td align="right">
                                                    <input type="button" value="列印賣方案件資料" class="btnD" onclick="download(2,'<{$data_owner.cIdentifyId}>')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方資料
                                                     <{if $smarty.session.member_pCaseAccounting == 1 || $smarty.session.member_pDep == 5}>
                                                        <{if $data_owner.cShow == 1}>
                                                             (<input type="checkbox" name="owner_show" class="detailShow" id="" value="1" checked="">可看<span id="ownerDetail" style="font-size: 18px;"><a href="Javascript:void(0);" style="font-size: 18px;" onclick="buyerOwnerWebDetail()">詳細</a></span>收支)
                                                        <{else}>
                                                            <input type="checkbox" name="owner_show" class="detailShow" id="" value="1">可看<span id="ownerDetail" style="font-size: 18px;">詳細</span>收支
                                                        <{/if}>
                                                   
                                                    <{/if}>
                                                    <div style="float:right;padding-right:10px;">
                                                    <!-- |&nbsp;<a href="buybehalflist.php?iden=o&cCertifyId=<{$data_case.cEscrowBankAccount}>" class="iframe" style="font-size:9pt;">編修登記名義人</a> &nbsp; -->|&nbsp;
                                                    <a href="buycontractlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修代理人</a>&nbsp;|&nbsp;
                                                        <a href="buyerownerlist.php?iden=o&cCertifyId=<{$data_case.cCertifiedId}>&cSingCategory=<{$data_case.cSignCategory}>&cCaseStatus=<{$checkOwnerAddr}>" class="iframe" id="moreowner" style="font-size:9pt;">編修多組賣方</a>
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
                                                                    <{html_radios name="owner_resident_limit" options=$inputSelect selected=$owner_resident_seledted separator='　' class="invoice"}>
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
                                                <th>戶籍地址︰</th>
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
                                                <th>E-MAIL︰</th>
                                                <td><input type="text" name="owner_mail" id="" value="<{$data_owner.cEmail}>"></td>
                                            </tr>
                                            <tr>
                                                <th>移轉範圍︰</th>
                                                <td>
                                                    <div><input type="button" style="margin-left: 10px;padding: 5px;" onclick="transferArea('<{$data_case.cCertifiedId}>', '4', '<{$data_owner.cId}>')" value="設定"></div>
                                                </td>
                                                <{if $smarty.session.member_id == 6}>
                                                <th>捐贈設定︰</th>
                                                <td colspan="3">
                                                    <div><input type="button" style="margin-left: 10px;padding: 5px;" onclick="invoiceDonation('owner_identifyid', 'owner')" value="設定"></div>
                                                </td>
                                                <{/if}>
                                            </tr>
                                            <tr>
                                                <th>前順位金額︰</th>
                                                <td>
                                                    NT$<input type="text" name="owner_money1" maxlength="15" size="12" class="currency-money1 text-right" value="<{$data_owner.cMoney1}>" />元
                                                </td>
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
                                                    賣方解匯資料
                                                     <span style="font-size:12px">
                                                        <{html_checkboxes name="ownerChecklist" options =$menu_checklist selected=$data_owner['ownerChecklist'] onclick="clickChecklist('owner')" id="ownerChecklist"}>
                                                    </span>
                                                    <div style="float:right;padding-right:10px;">
                                                      <a href="#owner_bankkey" onclick="addBankList('owner')">新增</a>  
                                                      <input type="hidden" name="owner_bank_count" value="<{$owner_bank_count+1}>">    
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯總行(1)︰</th>
                                                <td class="bank">
                                                    <{html_options name=owner_bankkey id="owner_bankkey" options=$menu_bank selected=$data_owner.cBankKey2 class="invoice" style="width: 20%"}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行(1)︰</th>
                                                <td  class="bank">
                                                    <select name="owner_bankbranch" id="owner_bankbranch" class="input-text-per invoice" style="width: 20%">
                                                    <{$owner_menu_branch}>
                                                    </select>
                                                </td>
                                                <td colspan="2" valign="center" align="center">
                                                    <{if $data_owner.cChecklistBank == 1}>
                                                        <input type="checkbox" name="owner_cklist" class="ownercklist" checked="checked">不帶入點交單和出款
                                                    <{else}>
                                                        <input type="checkbox" name="owner_cklist" class="ownercklist">不帶入點交單和出款
                                                    <{/if}>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯帳號(1)︰</th>
                                                <td><input type="text" name="owner_bankaccnumber" class="input-text-big invoice ownerAcc" value="<{$data_owner.cBankAccNumber}>" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶(1)︰</th>
                                                <td>
                                                    <input type="text" name="owner_bankaccname" class="input-text-big invoice" value="<{$data_owner.cBankAccName}>" />
                                                </td>
                                                <th>金額: </th>
                                                <td><input type="text" name="owner_bankMoney"  style="width:100px;" value="<{$data_owner.cBankMoney}>"></td>

                                            </tr>
                                            <!--動態 -->
                                            
                                            <tr class="ownercopy2 delownercopy">
                                                <td colspan="6"><hr><input type="hidden" name="owner_bankid2[]" value="<{$owner_bank[0].cId}>"></td>
                                            </tr>

                                            <tr class="ownercopy2">
                                                <th><span class="sign-red">*</span>指定解匯總行<span name="ownertext">(2)</span>︰</th>
                                                <td class="bank">
                                                    <{html_options name="owner_bankkey2[]" options=$menu_bank class="invoice" id="owner_bankkey2" onchange="Bankchange('owner',2)" selected=$owner_bank[0].cBankMain}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行<span name="ownertext">(2)</span>︰</th>
                                                <td class="bank">
                                                    <select name="owner_bankbranch2[]" class="input-text-per invoice" id="owner_bankbranch2">
                                                    <{$owner_bank[0].menu_branch}>
                                                    </select>
                                                </td>
                                                <td colspan="2" valign="center" align="center">
                                                    <{if $owner_bank[0].cChecklistBank == 1}>
                                                    <input type="checkbox" name="owner_cklist2[]" value="1" class="ownercklist" checked="checked">
                                                    <{else}>
                                                    <input type="checkbox" name="owner_cklist2[]" value="1" class="ownercklist">
                                                    <{/if}>
                                                    不帶入點交單和出款
                                                </td>
                                            </tr>
                                            
                                            <tr class="ownercopy2">
                                                <th><span class="sign-red">*</span>指定解匯帳號<span name="ownertext">(2)</span>︰</th>
                                                <td><input type="text" name="owner_bankaccnumber2[]" class="input-text-big invoice ownerAcc" value="<{$owner_bank[0].cBankAccountNo}>" id="owner_bankaccnumber2" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶<span name="ownertext">(2)</span>︰</th>
                                                <td>
                                                    <input type="text" name="owner_bankaccname2[]" class="input-text-big invoice" value="<{$owner_bank[0].cBankAccountName}>" id="owner_bankaccname2"/>
                                                </td>
                                                 <th>金額: </th>
                                                <td><input type="text" name="owner_bankMoney2[]" id="owner_bankMoney2" style="width:100px;" value="<{$owner_bank[0].cBankMoney}>"></td>
                                            </tr>
                                            <{foreach from=$owner_bank key=key item=item}>
                                                <{if $key > 0 }>
                                                <tr class="ownercopy<{$item.num}>">
                                                    <td colspan="6"><hr><input type="hidden" name="owner_bankid2[]" value="<{$item.cId}>"></td>
                                                </tr>

                                                
                                                <tr class="ownercopy<{$item.num}> delownercopy">
                                                    <th><span class="sign-red">*</span>指定解匯總行<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td class="bank">
                                                        <{html_options name="owner_bankkey2[]" options=$menu_bank class="invoice" id="owner_bankkey<{$item.num}>" onchange="Bankchange('owner',<{$item.num}>)" selected=$item.cBankMain}>
                                                    </td>
                                                    <th><span class="sign-red">*</span>指定解匯分行<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td class="bank">
                                                        <select name="owner_bankbranch2[]" class="input-text-per invoice" id="owner_bankbranch<{$item.num}>">
                                                        <{$item.menu_branch}>
                                                        </select>
                                                    </td>
                                                    <td colspan="2" valign="center" align="center">
                                                    <{if $item.cChecklistBank == 1}>
                                                        <input type="checkbox" name="owner_cklist2[]" value="1" class="ownercklist" checked="checked">
                                                    <{else}>
                                                        <input type="checkbox" name="owner_cklist2[]" value="1" class="ownercklist">
                                                    <{/if}>
                                                    不帶入點交單和出款
                                                    </td>
                                                </tr>
                                                
                                                <tr class="ownercopy<{$item.num}> delownercopy">
                                                    <th><span class="sign-red">*</span>指定解匯帳號<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td><input type="text" name="owner_bankaccnumber2[]"  class="input-text-big invoice ownerAcc" value="<{$item.cBankAccountNo}>" maxlength="14"/></td>
                                                    <th><span class="sign-red">*</span>指定解匯帳戶<span name="ownertext">(<{$item.num}>)</span>︰</th>
                                                    <td>
                                                        <input type="text" name="owner_bankaccname2[]" class="input-text-big invoice" value="<{$item.cBankAccountName}>" />
                                                    </td>
                                                    <th>金額: </th>
                                                    <td><input type="text" name="owner_bankMoney2[]" id="" style="width:100px;" value="<{$item.cBankMoney}>"></td>
                                                </tr>
                                                <{/if}>
                                            <{/foreach}>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    賣方經紀人
                                                    <div style="float:right;padding-right:10px;">
                                                        <a href="#buyerS" onclick="addSalesList('owner')">新增</a>  
                                                    </div>
                                                </td>
                                            </tr>
                                            <{foreach from=$ownerSalesPhone key=key item=item}>
                                            <tr class="ownersales">
                                                <th>經紀人姓名(<span class="ownersalesNo"><{$key+1}></span>)︰<input type="hidden" name="owner_agenId[]" value="<{$item.cId}>"></th>
                                                <td>
                                                    <input type="text" name="owner_agentname[]" maxlength="10" class="input-text-big" value="<{$item.cName}>"　/>
                                                </td>
                                                <th>經紀人手機(<span class="ownersalesNo"><{$key+1}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile[]" maxlength="10" class="input-text-per" value="<{$item.cMobileNum}>"/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <{/foreach}>
                                             <tr class="ownersales">
                                                <th>經紀人姓名(<span class="ownersalesNo"><{$ownerSalesPhone_count}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentname[]" maxlength="10" class="input-text-big" value=""　/>
                                                </td>
                                                <th>經紀人手機(<span class="ownersalesNo"><{$ownerSalesPhone_count}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="owner_agentmobile[]" maxlength="10" class="input-text-per" value=""/>
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
                                                 
                                            </tr>
                                            <tr>
                                                <th>冰箱</th>
                                                <td><input type="text" name="furniture_refrigerator" value="<{$furniture.cRefrigerator}>" size="3">台</td>
                                                <th>流理台</th>
                                                <td><input type="text" name="furniture_sink" value="<{$furniture.cSink}>" size="3">台</td>
                                                <th>天然瓦斯</th>
                                                <td><input type="text" name="furniture_gas" value="<{$furniture.cGas}>" size="3">台</td>
                                            </tr>

                                            <tr>
                                                <th>其他︰</th>
                                                <td><input type="text" name="furniture_other" value="<{$furniture.cOther}>" size="10"></td> 
                                            </tr>
                                
                                            </table>
                                            </div>
                                             <div id="tabs-buyer">
                                            <table border="0" width="100%">
                                            <tr>
                                                <th width="14%">承辦人︰</th>
                                                <td width="20%">
                                                    <input type="text" name="" maxlength="10" class="input-text-mid" value="<{$undertaker}>" disabled="disabled"/>
                                                </td>
                                                <th width="14%">最後修改者︰</th>
                                                <td width="20%">
                                                    <input type="text" maxlength="10" class="input-text-mid" value="<{$case_lasteditor.pName}>" disabled="disabled"/>
                                                </td>
                                                <th width="14%">最後修改時間︰</th>
                                                <td width="18%">
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
                                                <td>
                                                    <input type="button" value="前台買方(new)" class="btnD" style="font-size:10pt;" onclick="download('n1','<{$data_buyer.cIdentifyId}>')">
                                                </td>
                                                <td align="right"><input type="button" value="列印買方案件資料" class="btnD" onclick="download(1,'<{$data_buyer.cIdentifyId}>')">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    買方資料
                                                     <{if $smarty.session.member_pCaseAccounting == 1 || $smarty.session.member_pDep == 5}>
                                                        <{if $data_buyer.cShow == 1}>
                                                             (<input type="checkbox" name="buyer_show" class="detailShow" id="" value="1" checked="">可看<span id="buyerDetail" style="font-size: 18px;"><a href="Javascript:void(0);" style="font-size: 18px;" onclick="buyerOwnerWebDetail()">詳細</a></span>收支)
                                                        <{else}>
                                                            <input type="checkbox" name="buyer_show" class="detailShow" id="" value="1">可看<span id="buyerDetail" style="font-size: 18px;">詳細</span>收支
                                                        <{/if}>
                                                   
                                                    <{/if}>
                                                    <div style="float:right;padding-right:10px;">
                                                    |&nbsp;<a href="buybehalflist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&SignCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修登記名義人</a> &nbsp;|&nbsp;
                                                        <a href="buycontractlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&SignCategory=<{$data_case.cSignCategory}>" class="iframe" style="font-size:9pt;">編修代理人</a>&nbsp;|&nbsp;
                                                        <a href="buyerownerlist.php?iden=b&cCertifyId=<{$data_case.cCertifiedId}>&cCaseStatus=<{$checkOwnerAddr}>" class="iframe" id="morebuyer" style="font-size:9pt;">編修多組買方</a>
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
                                                                    <{html_radios name="buyer_resident_limit" options=$inputSelect selected=$buyer_resident_seledted separator='　' class="invoice"}>
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
                                                <td><input type="text" name="buyer_authorized" value="<{$data_buyer.cAuthorized}>" style="width: 70%"></td>
                                               
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
                                                <th>戶籍地址︰</th>
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
                                                <th>E-MAIL︰</th>
                                                <td><input type="text" name="buyer_mail" id="" value="<{$data_buyer.cEmail}>"></td>
                                            </tr>
                                            <tr>
                                                <th>移轉範圍︰</th>
                                                <td>
                                                    <div><input type="button" style="margin-left: 10px;padding: 5px;" onclick="transferArea('<{$data_case.cCertifiedId}>', '3', '<{$data_buyer.cId}>')" value="設定"></div>
                                                    <div id="transfer-area-owner"></div>
                                                </td>
                                                <{if $smarty.session.member_id == 6}>
                                                <th>捐贈設定︰</th>
                                                <td colspan="3">
                                                    <div><input type="button" style="margin-left: 10px;padding: 5px;" onclick="invoiceDonation('buy_identifyid', 'buyer')" value="設定"></div>
                                                </td>
                                                <{/if}>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="tb-title">
                                                    買方解匯資料 
                                                    <span style="font-size:12px">
                                                        <{html_checkboxes name="buyerChecklist" options =$menu_checklist selected=$data_buyer['buyerChecklist'] onclick="clickChecklist('buyer')" id="buyerChecklist"}>

                                                    </span>
                                                    <div style="float:right;padding-right:10px;">
                                                      <a href="#" onclick="addBankList('buyer')">新增</a>  
                                                      <input type="hidden" name="buyer_bank_count" value="<{$buyer_bank_count+1}>">    
                                                    </div>
                                                </td>
                                            </tr>
                                           
                                            <tr>
                                                <th><span class="sign-red">*</span>指定解匯總行(1)︰</th>
                                                <td class="bank">
                                                    <{html_options name=buyer_bankkey id="buyer_bankkey" options=$menu_bank selected=$data_buyer.cBankKey2 class="invoice" style="width:300px;"}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行(1)︰</th>
                                                <td  class="bank">
                                                    <select name="buyer_bankbranch" id="buyer_bankbranch" class="input-text-per invoice" style="width:300px;">
                                                    <{$buyer_menu_branch}>
                                                    </select>
                                                </td>
                                                <td colspan="2" valign="center" align="center">
                                                    <{if $data_buyer.cChecklistBank == 1}>
                                                        <input type="checkbox" name="buyer_cklist" class="buyercklist" value="1" checked="checked"> 
                                                    <{else}>
                                                        <input type="checkbox" name="buyer_cklist" class="buyercklist" value="1"> 
                                                    <{/if}>
                                                        不帶入點交單和出款
                                                </td>
                                            </tr>
                                            <tr >
                                                <th><span class="sign-red">*</span>指定解匯帳號(1)︰</th>
                                                <td><input type="text" name="buyer_bankaccnumber" class="input-text-big invoice buyerAcc" value="<{$data_buyer.cBankAccNumber}>" maxlength="14"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶(1)︰</th>
                                                <td >
                                                    <input type="text" name="buyer_bankaccname" class="input-text-big invoice" value="<{$data_buyer.cBankAccName}>" />
                                                </td>
                                                <th>金額: </th>
                                                <td><input type="text" name="buyer_bankMoney" id="" style="width:100px;" value="<{$data_buyer.cBankMoney}>"></td>
                                            </tr>
                                            <!--動態 -->
                                            
                                            <tr class="buyercopy2 ">
                                                <td colspan="6"><hr><input type="hidden" name="buyer_bankid2[]" value="<{$buyer_bank[0].cId}>"></td>
                                            </tr>

                                            <tr class="buyercopy2 ">
                                                <th><span class="sign-red">*</span>指定解匯總行<span name="buyertext">(2)</span>︰</th>
                                                <td class="bank">
                                                    <{html_options name="buyer_bankkey2[]" options=$menu_bank class="invoice" id="buyer_bankkey2" onchange="Bankchange('buyer',2)" style="width:300px;" selected=$buyer_bank[0].cBankMain}>
                                                </td>
                                                <th><span class="sign-red">*</span>指定解匯分行<span name="buyertext">(2)</span>︰</th>
                                                <td class="bank">
                                                    <select name="buyer_bankbranch2[]" class="input-text-per invoice" id="buyer_bankbranch2" style="width:300px;">
                                                    <{$buyer_bank[0].menu_branch}>
                                                    </select>
                                                </td>
                                                <td colspan="2" valign="center" align="center">
                                                    <{if $buyer_bank[0].cChecklistBank == 1}>
                                                        <input type="checkbox" name="buyer_cklist2[]" id="buyer_cklist2" value="1" class="buyercklist" checked="checked">
                                                    <{else}>
                                                        <input type="checkbox" name="buyer_cklist2[]" id="buyer_cklist2" value="1" class="buyercklist">
                                                    <{/if}>
                                                    不帶入點交單和出款
                                                </td>
                                            </tr>
                                            
                                            <tr class="buyercopy2 ">
                                                <th><span class="sign-red">*</span>指定解匯帳號<span name="buyertext">(2)</span>︰</th>
                                                <td><input type="text" name="buyer_bankaccnumber2[]" class="input-text-big invoice buyerAcc" value="<{$buyer_bank[0].cBankAccountNo}>" maxlength="14" id="buyer_bankaccnumber2"/></td>
                                                <th><span class="sign-red">*</span>指定解匯帳戶<span name="buyertext">(2)</span>︰</th>
                                                <td>
                                                    <input type="text" name="buyer_bankaccname2[]" class="input-text-big invoice" value="<{$buyer_bank[0].cBankAccountName}>" id="buyer_bankaccname2" />
                                                </td>
                                                <th>金額: </th>
                                                <td><input type="text" name="buyer_bankMoney2[]" id="buyer_bankMoney2" style="width:100px;" value="<{$buyer_bank[0].cBankMoney}>"></td>
                                            </tr>
                                            <{foreach from=$buyer_bank key=key item=item}>
                                                <{if $key > 0 }>
                                                <tr class="buyercopy<{$item.num}>">
                                                    <td colspan="6"><hr><input type="hidden" name="buyer_bankid2[]" value="<{$item.cId}>"></td>
                                                </tr>

                                                
                                                <tr class="buyercopy<{$item.num}> delbuyercopy">
                                                    <th><span class="sign-red">*</span>指定解匯總行<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td class="bank">
                                                        <{html_options name="buyer_bankkey2[]" options=$menu_bank class="invoice" id="buyer_bankkey<{$item.num}>" onchange="Bankchange('buyer',<{$item.num}>)" selected=$item.cBankMain}>
                                                    </td>
                                                    <th><span class="sign-red">*</span>指定解匯分行<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td  class="bank">
                                                        <select name="buyer_bankbranch2[]" class="input-text-per invoice" id="buyer_bankbranch<{$item.num}>">
                                                        <{$item.menu_branch}>
                                                        </select>
                                                    </td>
                                                    <td colspan="2" valign="center" align="center">
                                                        <{if $item.cChecklistBank == 1}>
                                                            <input type="checkbox" name="buyer_cklist2[]" class="buyercklist" value="1" checked="checked">
                                                        <{else}>
                                                            <input type="checkbox" name="buyer_cklist2[]" class="buyercklist" value="1">
                                                        <{/if}>
                                                        不帶入點交單和出款</td>
                                                </tr>
                                                
                                                <tr class="buyercopy<{$item.num}> delbuyercopy">
                                                    <th><span class="sign-red">*</span>指定解匯帳號<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td><input type="text" name="buyer_bankaccnumber2[]" class="input-text-big invoice buyerAcc" value="<{$item.cBankAccountNo}>" maxlength="14"/></td>
                                                    <th><span class="sign-red">*</span>指定解匯帳戶<span name="buyertext">(<{$item.num}>)</span>︰</th>
                                                    <td>
                                                        <input type="text" name="buyer_bankaccname2[]" class="input-text-big invoice" value="<{$item.cBankAccountName}>" />
                                                    </td>
                                                    <th>金額: </th>
                                                        <td><input type="text" name="buyer_bankMoney2[]" id="" style="width:100px;" value="<{$item.cBankMoney}>"></td>
                                                    </tr>
                                                <{/if}>
                                            <{/foreach}>
                                            
                                            <tr>
                                                <td colspan="6" class="tb-title" id="buyerS">
                                                    買方經紀人
                                                    <div style="float:right;padding-right:10px;">
                                                      <a href="#buyerS" onclick="addSalesList('buyer')">新增</a>  
                                                      </div>
                                                </td>
                                            </tr>
                                            <{foreach from=$buyerSalesPhone key=key item=item}>
                                            <tr class="buyersales">
                                                <th>經紀人姓名 (<span class="buyersalesNo"><{$key+1}></span>)︰
                                                    <input type="hidden" name="buyer_agenId[]" value="<{$item.cId}>">
                                                </th>
                                                <td>
                                                    <input type="text" name="buyer_agentname[]" maxlength="10" class="input-text-big" value="<{$item.cName}>"　/>
                                                </td>
                                                <th>經紀人手機 (<span class="buyersalesNo"><{$key+1}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile[]" maxlength="10" class="input-text-per" value="<{$item.cMobileNum}>"　/>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <{/foreach}>
                                            <tr class="buyersales">
                                                <th>經紀人姓名 (<span class="buyersalesNo"><{$buyerSalesPhone_count}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentname[]" maxlength="10" class="input-text-big" value=""　/>
                                                </td>
                                                <th>經紀人手機 (<span class="buyersalesNo"><{$buyerSalesPhone_count}></span>)︰</th>
                                                <td>
                                                    <input type="text" name="buyer_agentmobile[]" maxlength="10" class="input-text-per" value=""　/>
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
                                        <{if $smarty.session.member_pDep == 7 || $data_case.cFeedBackClose == 1}>
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
                                    <table width="100%" border="0" class="gridtable">
                                        
                                        <tr>
                                            <td colspan="6" class="tb-title">回饋對象</td>
                                        </tr>
                                        <tr>
                                            <th width="10%">仲介店名︰</th>
                                            <td colspan="4"><label id='bt'><{$branch_type1}></label></td>
                                            <td width="20%">
                                               
                                                保證費金額:<font color="red"><{$data_income.cCertifiedMoney}></font>
                                                <span class="checkCertifiedFee" style="display: none;border:0px;"><font color="red">(未收足)</font></span>
                                                <br>
                                                案件回饋比例:<font color="red" id="showRatio"></font>

                                            </td>   
                                        </tr>
                                         <tr>
                                            <th width="10%">案件回饋︰</th>
                                            <td width="22%">
                                                <input type="radio" onclick="sync_radio()" <{$_disabled}> name="cCaseFeedback" <{$feedback.1}> value="0" <{if $data_case.cFeedbackTarget == 2 }> <{$scrivenerDisabled}> <{/if}> >&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio();checkfeed();" onblur="" style="width:80px;text-align:right;" name="cCaseFeedBackMoney" maxlength="8" value="<{$data_case.cCaseFeedBackMoney}>" class="feedbackClose feedbackmoneysum"<{$_disabled}> <{if $data_case.cFeedbackTarget == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;元
                                            </td>
                                            <td width="10%">
                                                <input type="radio" onclick="sync_radio(); checkFeedBackMoney('cCaseFeedBackMoney');" class="feedbackClose" <{$_disabled}> name="cCaseFeedback" <{$feedback.2}> value="1" <{if $data_case.cFeedbackTarget == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;不回饋
                                                <input type="hidden" name="cCaseFeedBackModifier" value="<{$data_case.cCaseFeedBackModifier}>">
                                                <input type="hidden" name="cCaseFeedBackModifyTime" value="">
                                            </td>
                                            <th width="10%">回饋對象︰</th>
                                            <td width="15%">
                                                <input type="radio" id="FBT1" class="feedbackClose" onclick="sync_radio()" name="cFeedbackTarget"<{$fbcheckedR.1}> value="1"<{$fbDisabled}> <{$scrivenerDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2" class="feedbackClose" onclick="sync_radio()" name="cFeedbackTarget"<{$fbcheckedS.2}> value="2"<{$fbDisabled}> <{$scrivenerDisabled}>>&nbsp;地政士
                                            </td>
                                            <td rowspan="3"  align="center" valign="center">
                                                <{if $data_case.cCaseFeedBackModifier !=''}>
                                                    <div style="color:red;font-size:20px;">已手動更改</div>
                                                    <!-- <font color="red"></font> -->
                                                <{/if}>
                                            </td>
                                           
                                        </tr>
                                        <{foreach from=$individual key=key item=item}>
                                            <{if $data_realstate.cBranchNum == $key}>
                                                <{foreach from=$item key=bId item=feedback}>
                                                    <tr>
                                                        <th>個案回饋︰</th>
                                                        <td colspan="2"><{$feedback.name}></td>
                                                        <th>回饋金額︰</th>
                                                        <td colspan="3"><{$feedback.fMoney}></td>
                                                    </tr>
                                                <{/foreach}>
                                            <{/if}>
                                         <{/foreach}>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th width="20%">仲介店名︰</th>
                                            <td colspan="5"><label id='bt1'><{$branch_type2}></label></td>
                                        </tr>
                                        <tr class="show_2_realty" style="display:<{$second_branch}>;">
                                            <th>案件回饋︰</th>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" class="feedbackClose" <{$_disabled}> name="cCaseFeedback1" <{$feedback.11}> value="0" <{if $data_case.cFeedbackTarget1 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio();checkfeed();" class="feedbackClose feedbackmoneysum" onblur="" style="width:80px;text-align:right;" name="cCaseFeedBackMoney1"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney1}>" <{if $data_case.cFeedbackTarget1 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;元
                                            </td>
                                            <td>
                                                <input type="radio" onclick="sync_radio();checkFeedBackMoney('cCaseFeedBackMoney1');" class="feedbackClose" <{$_disabled}> name="cCaseFeedback1" <{$feedback.12}> value="1" <{if $data_case.cFeedbackTarget1 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;不回饋
                                            </td>
                                            <th>回饋對象︰</th>
                                            <td>
                                                <input type="radio" id="FBT1"  name="cFeedbackTarget1" class="feedbackClose" onclick="sync_radio()" <{$fbcheckedR.11}> value="1"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2"  name="cFeedbackTarget1" class="feedbackClose" onclick="sync_radio()" <{$fbcheckedS.12}> value="2"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;地政士
                                            </td>
                                            
                                            
                                        </tr>
                                        <{foreach from=$individual key=key item=item}>
                                            <{if $data_realstate.cBranchNum1 == $key}>
                                                <{foreach from=$item key=bId item=feedback}>
                                                    <tr>
                                                        <th>個案回饋︰</th>
                                                        <td colspan="2"><{$feedback.name}></td>
                                                        <th>回饋金額︰</th>
                                                        <td colspan="3"><{$feedback.fMoney}></td>
                                                    </tr>
                                                <{/foreach}>
                                            <{/if}>
                                         <{/foreach}>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th width="20%">仲介店名︰</th>
                                            <td colspan="5"><label id='bt2'><{$branch_type3}></label></td>
                                        </tr>
                                        <tr class="show_3_realty" style="display:<{$third_branch}>;">
                                            <th>案件回饋︰</th>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" class="feedbackClose" <{$_disabled}> name="cCaseFeedback2" <{$feedback.21}> value="0" <{if $data_case.cFeedbackTarget2 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio();checkfeed();" class="feedbackClose feedbackmoneysum" onblur="" style="width:80px;text-align:right;"  name="cCaseFeedBackMoney2"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney2}>" <{if $data_case.cFeedbackTarget2 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;元
                                            </td>
                                            <td>
                                                <input type="radio" onclick="sync_radio();checkFeedBackMoney('cCaseFeedBackMoney2');" class="feedbackClose" <{$_disabled}> name="cCaseFeedback2" <{$feedback.22}> value="1" <{if $data_case.cFeedbackTarget2 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;不回饋
                                            </td>
                                            <th>回饋對象︰</th>
                                            <td>
                                                <input type="radio" id="FBT1" class="feedbackClose" name="cFeedbackTarget2" onclick="sync_radio()" <{$fbcheckedR.21}> value="1"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2" class="feedbackClose" name="cFeedbackTarget2" onclick="sync_radio()" <{$fbcheckedS.22}> value="2"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;地政士
                                            </td>
                                            
                                            
                                        </tr>
                                        <{foreach from=$individual key=key item=item}>
                                            <{if $data_realstate.cBranchNum2 == $key}>
                                                <{foreach from=$item key=bId item=feedback}>
                                                    <tr>
                                                        <th>個案回饋︰</th>
                                                        <td colspan="2"><{$feedback.name}></td>
                                                        <th>回饋金額︰</th>
                                                        <td colspan="3"><{$feedback.fMoney}></td>
                                                    </tr>
                                                <{/foreach}>
                                            <{/if}>
                                         <{/foreach}>
                                        <{if $data_case.cSignDate >= 110-07-21}>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th width="20%">仲介店名︰</th>
                                            <td colspan="5"><label id='bt2'><{$branch_type4}></label></td>
                                        </tr>
                                        <tr class="show_4_realty" style="display:<{$fourth_branch}>;">
                                            <th>案件回饋︰</th>
                                            <td>
                                                <input type="radio" onclick="sync_radio()" class="feedbackClose" <{$_disabled}> name="cCaseFeedback3" <{$feedback.31}> value="0" <{if $data_case.cFeedbackTarget3 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;回饋
                                                金額：<input type="text" onchange="sync_radio();checkfeed();" class="feedbackClose feedbackmoneysum" onblur="" style="width:80px;text-align:right;" name="cCaseFeedBackMoney3"<{$_disabled}> maxlength="8" value="<{$data_case.cCaseFeedBackMoney3}>" <{if $data_case.cFeedbackTarget3 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;元
                                            </td>
                                            <td>
                                                <input type="radio" onclick="sync_radio();checkFeedBackMoney('cCaseFeedBackMoney3');" class="feedbackClose" <{$_disabled}> name="cCaseFeedback3" <{$feedback.32}> value="1" <{if $data_case.cFeedbackTarget3 == 2 }> <{$scrivenerDisabled}> <{/if}>>&nbsp;不回饋
                                            </td>
                                            <th>回饋對象︰</th>
                                            <td>
                                                <input type="radio" id="FBT1" class="feedbackClose" name="cFeedbackTarget3" onclick="sync_radio()" <{$fbcheckedR.31}> value="1"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;仲介
                                                <input type="radio" id="FBT2" class="feedbackClose" name="cFeedbackTarget3" onclick="sync_radio()" <{$fbcheckedS.32}> value="2"<{$fbDisabled}>  <{$scrivenerDisabled}>>&nbsp;地政士
                                            </td>
                                            
                                            
                                        </tr>
                                        <{foreach from=$individual key=key item=item}>
                                            <{if $data_realstate.cBranchNum3 == $key}>
                                                <{foreach from=$item key=bId item=feedback}>
                                                <tr>
                                                    <th>個案回饋︰</th>
                                                    <td colspan="2"><{$feedback.name}></td>
                                                    <th>回饋金額︰</th>
                                                    <td colspan="3"><{$feedback.fMoney}></td>
                                                </tr>
                                                <{/foreach}>
                                            <{/if}>
                                         <{/foreach}>
                                        <{/if}>
                                     
                                        <tr id="sp_show_mpney" style="display:<{$sSpRecall}>;"> 
                                            <th>地政士事務所</th>
                                            <td colspan="2" id="sp_show_scrivener_name"><{$scrivener_office}></td>
                                            <th>特殊回饋︰</td>
                                            <td colspan="3"><input type="text" onchange="sync_radio()" class="feedbackClose feedbackmoneysum" onblur="checkfeed()" style="width:80px;text-align:right;" name="cSpCaseFeedBackMoney"<{$_disabled}>  <{$scrivenerDisabled}> maxlength="8" value="<{$data_case.cSpCaseFeedBackMoney}>">&nbsp;元</td>
                                        </tr>
                                        
                                    </table>
                                    <table width="100%" border="0" class="feedm">
                                        <tr>
                                            <td colspan="6" class="tb-title">其他回饋對象
                                                <div style="float:right;padding-right:10px;">
                                                    <{if $data_case.cFeedBackClose != 1}> 
                                                        <a href="#" class="add-feedback" onclick="addOtherFeed('')">新增回饋對象</a>
                                                    <{/if}>
                                                    <input type="hidden" name="addOFeed" value="0">
                                                </div>
                                            </td>
                                        </tr>
                                        <{foreach from=$otherFeed key=key item=item}>
                                        <tr id="DOtherFeed<{$item.fId}>">
                                            <th width="10%">回饋對象：<input type="hidden" name="otherFeedId[]" value="<{$item.fId}>"><input type="hidden" name="otherFeedCheck[]" id="otherFeedCheck<{$item.fId}>"></th>
                                            <td width="15%"><{html_radios name="otherFeedType<{$item.fId}>" options=$menu_ftype selected=$item.fType  onClick="ChangeFeedStore('',<{$item.fId}>,'')" class="feedbackClose scrivenerClose"}></td>
                                            <th width="10%">店名：</th>
                                            <td width="35%">
                                            <select name="otherFeedstoreId<{$item.fId}>" onChange="otherFeedCg(<{$item.fId}>)" style="width:300px;" class="feedbackClose newfeedcheckStore1 <{if $item.fType == 1}>scrivenerClose<{/if}>" alt="<{$item.fId}>">
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
                                            <td width="20%"><input type="text" style="width:80px;text-align:right;" name="otherFeedMoney[]"  value="<{$item.fMoney}>" class="feedbackClose feedbackmoneysum <{if $item.fType == 1}>scrivenerClose<{/if}>" onKeyUp="otherFeedCg(<{$item.fId}>)" id="otherFeedMoney<{$item.fId}>">元
                                            <{if $data_case.cFeedBackClose != 1}> 
                                            <input type="button" value="刪除" onclick="delfeedmoney('',<{$item.fId}>,'')" <{if $item.fType == 1}><{$scrivenerDisabled}><{/if}>>
                                            <{/if}>
                                            </td>
                                        </tr>
                                        <{/foreach}>
                                        <tr id="OtherFeedcopy0" class="dis otherf"> <!-- -->
                                            <th width="10%">回饋對象：<input type="hidden" name="newotherFeedCheck[]"></th>
                                            <td><{html_radios name="newotherFeedType0" options=$menu_ftype selected="1" onClick="ChangeFeedStore('new','0','')"}></td>
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
                                                <input type="text" style="width:80px;text-align:right;" name="newotherFeedMoney[]" id="newotherFeedMoney0" class="feedbackmoneysum" >元
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

                                <{if ($smarty.session.member_pDep == 4 ||  $smarty.session.member_pDep == 7)||  ($smarty.session.pFeedBackAudit == 1 || $smarty.session.member_pDep == 1|| $smarty.session.member_id == 36)}>
                                <div id="tabs-backmoney2">
                                    <input type="button" value="點我申請" class="btnD feedbackClose" onclick="openFeedbackModifyConfirm()" <{if ($smarty.session.member_id == 36)}>disabled <{/if}> >
                                    <div id="tbl_feedback">
                                    <{foreach from=$SalesReview key=key item=item}>
                                        <table cellpadding="0" cellspacing="0" width="100%" border="1">
                                        <tr>
                                            <th colspan="6" >
                                            <{if $item.Status == 0}>
                                                <input type="button" value="修改" onclick="feedbackApplyStatus(<{$item.fId}>)">&nbsp;
                                                &nbsp;
                                                &nbsp;
                                                &nbsp;
                                                &nbsp;
                                                <input type="button" value="刪除" onclick="delSalesFeedConfirm(<{$item.fId}>)">
                                            <{/if}>
                                            &nbsp;
                                            </th>
                                        </tr>
                                        <tr>
                                            <th width="15%">案號︰</th>
                                            <td width="20%"><{$data_case.cCertifiedId}></td>
                                            <th width="10%">保證費金額︰</th>
                                            <td width="20%">
                                                    <{if $item.fCertifiedMoney == 0}><{$data_income.cCertifiedMoney|number_format:0}>
                                                    <{else}><{$item.fCertifiedMoney|number_format:0}>
                                                    <{/if}>
                                            </td>
                                            <th width="10%">狀態︰</th>
                                            <td width="20%"><{$item.fStatus}></td>
                                        </tr>
                                      
                                        <tr>
                                            <td colspan="6" class="tb-title">回饋對象</td>
                                        </tr>
                                       
                                        <tr>
                                            <th width="15%">仲介店名︰</th>
                                            <td colspan="5">
                                            <{$item.BranchName}>
                                        </tr>
                                        <tr>
                                            <th width="15%">案件回饋︰</th>
                                            <td>
                                                <span style="background-color:#CCC;">
                                                <{if $item.fCaseFeedback == 0}>
                                                    回饋，金額：<{$item.fCaseFeedBackMoney|number_format:0}>元
                                                <{else}>
                                                    不回饋
                                                <{/if}> 
                                                </span>
                                            </td>
                                            
                                            <th>回饋對象︰</th>
                                            <td colspan="3">
                                                <{if $item.fFeedbackTarget  == 1}>
                                                    仲介
                                                <{else}>
                                                    地政士
                                                <{/if}> 

                                            </td>
                                            
                                        </tr>
                                        <{foreach from=$item.individualName key=k item=name}>
                                            <tr>
                                                <th width="15%">個案回饋︰</th>
                                                <td><span style="background-color:#CCC;">金額：<{$item.individualMoney.$k}>元</span></td>
                                                <th>回饋名稱︰</th>
                                                <td colspan="3"><{$name}></td>
                                            </tr>
                                        <{/foreach}>
                                         <{if $item.BranchName2 != ''}>
                                        <tr >
                                            <th>仲介店名︰</th>
                                            <td colspan="5"><{$item.BranchName2}></td>
                                        </tr>
                                        <tr >
                                            <th>案件回饋︰</th>
                                            <td>
                                                <span style="background-color:#CCC;">
                                                <{if $item.fCaseFeedback2 == 0}>
                                                    回饋，金額：<{$item.fCaseFeedBackMoney2|number_format:0}>元
                                                <{else}>
                                                    不回饋
                                                <{/if}> 
                                                </span>
                                            </td>
                                           
                                            <th>回饋對象︰</th>
                                            <td colspan="3">
                                                <{if $item.fFeedbackTarget2 == 1}>
                                                    仲介
                                                <{else}>
                                                    地政士
                                                <{/if}> 
                                                
                                            </td>

                                        </tr>
                                        <{foreach from=$item.individualName2 key=k item=name}>
                                            <tr>
                                                <th width="15%">個案回饋︰</th>
                                                <td><span style="background-color:#CCC;">金額：<{$item.individualMoney2.$k}>元</span></td>
                                                <th>回饋名稱︰</th>
                                                <td colspan="3"><{$name}></td>
                                            </tr>
                                        <{/foreach}>

                                         <{/if}>
                                         <{if $item.BranchName3 != ''}>
                                        <tr >
                                            <th>仲介店名︰</th>
                                            <td colspan="5"><{$item.BranchName3}></td>
                                        </tr>
                                        <tr >
                                            <th>案件回饋︰</th>
                                            <td>
                                                <span style="background-color:#CCC;">
                                                <{if $item.fCaseFeedback3 == 0}>
                                                    回饋，金額：<{$item.fCaseFeedBackMoney3|number_format:0}>元
                                                <{else}>
                                                    不回饋
                                                <{/if}> 
                                                </span>
                                            </td>
                                           
                                            <th>回饋對象︰</th>
                                            <td colspan="3">
                                                <{if $item.fFeedbackTarget3 == 1}>
                                                    仲介
                                                <{else}>
                                                    地政士
                                                <{/if}>  
                                            </td>

                                        </tr>
                                        <{foreach from=$item.individualName3 key=k item=name}>
                                            <tr>
                                                <th width="15%">個案回饋︰</th>
                                                <td><span style="background-color:#CCC;">金額：<{$item.individualMoney3.$k}>元</span></td>
                                                <th>回饋名稱︰</th>
                                                <td colspan="3"><{$name}></td>
                                            </tr>
                                        <{/foreach}>
                                        <{/if}>
                                        <{if $item.BranchName6 != ''}>
                                        <tr >
                                            <th>仲介店名︰</th>
                                            <td colspan="5"><{$item.BranchName6}></td>
                                        </tr>
                                        <tr >
                                            <th>案件回饋︰</th>
                                            <td>
                                                <span style="background-color:#CCC;">
                                                <{if $item.fCaseFeedback6 == 0}>
                                                    回饋，金額：<{$item.fCaseFeedBackMoney6|number_format:0}>元
                                                <{else}>
                                                    不回饋
                                                <{/if}> 
                                                </span>
                                            </td>
                                           
                                            <th>回饋對象︰</th>
                                            <td colspan="3">
                                                <{if $item.fFeedbackTarget6 == 1}>
                                                    仲介
                                                <{else}>
                                                    地政士
                                                <{/if}>  
                                            </td>

                                        </tr>
                                        <{foreach from=$item.individualName6 key=k item=name}>
                                            <tr>
                                                <th width="15%">個案回饋︰</th>
                                                <td><span style="background-color:#CCC;">金額：<{$item.individualMoney6.$k}>元</span></td>
                                                <th>回饋名稱︰</th>
                                                <td colspan="3"><{$name}></td>
                                            </tr>
                                        <{/foreach}>
                                        <{/if}>
                                        <{if $item.ScrivenerSPFeedMoney > 0}>
                                         <tr> 
                                            <th>地政士事務所</th>
                                            <td colspan="2"><{$scrivener_office}></td>
                                            <th>特殊回饋︰</td>
                                            <td colspan="3">
                                                <span style="background-color:#CCC;"><{$item.ScrivenerSPFeedMoney|number_format:0}>元</span>
                                            </td>
                                        </tr>
                                        <{/if}>
                                        <{if $item.scrivenerAccount!= '' }>
                                         <tr>
                                            <th>回饋金帳戶</th>
                                            <td colspan="6"><{$item.scrivenerAccount}></td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <td colspan="6" class="tb-title">其他回饋對象</td>                              
                                        </tr>
                                        <{foreach from=$item.data key=key item=item2}>
                                        <tr>
                                            <th>回饋對象：</th>
                                            <td>
                                                <{if $item2.fFeedbackTarget == 1}>
                                                    地政士
                                                <{else}>
                                                    仲介
                                                <{/if}>
                                            </td>
                                            <th>店名：</th>
                                            <td><{$item2.Name}></td>
                                            <th>回饋金：</th>
                                            <td>
                                                <span style="background-color:#CCC;"><{$item2.fCaseFeedBackMoney|number_format:0}>元</span>
                                            </td>
                                        </tr>
                                        <{if $item2.fCaseFeedBackNote}>
                                        <tr>
                                            <th>原因：</th>
                                            <td colspan="5"><{$item2.fCaseFeedBackNote}></td>
                                        </tr>
                                        <{/if}>
                                        <{/foreach}>

                                        <tr>
                                            <td colspan="6" class="tb-title">備註：</td>                              
                                        </tr>
                                        <{if $item.fNote != ''}>
                                       
                                        <tr>
                                            <td colspan="6" style="padding-left: 10px;"><{$item.fNote}></td>
                                        </tr>
                                        <{/if}>
                                        <{foreach from=$delNote key=key item=item2}>
                                            <{if $item2.fNote != ''}>
                                            <tr>
                                                
                                                <th>店名：</th>
                                                <td><{$item2.Code}><{$item.Name}></td>
                                                <th>回饋金：</th>
                                                <td><{$item2.fCaseFeedBackMoney}></td>
                                                <th>刪除原因:</th>
                                                <td ><{$item2.fNote}></td>
                                            </tr>
                                           
                                            
                                            <{/if}>       
                                        <{/foreach}>
                                        <{if $item.otherAccount!= '' }>
                                         <tr>
                                            <th>其他回饋金帳戶</th>
                                            <td colspan="6"><{$item.otherAccount}></td>
                                        </tr>
                                        <{/if}>
                                        <tr>
                                            <th>申請</th>
                                            <td colspan="2"><{$item.fCreator}>(<{$item.fApplyTime}>)</td>
                                            
                                            <th>審核</th>
                                            <td colspan="2">
                                                <{if $item.fAuditor != ''}>
                                                    <{$item.fAuditor}>(<{$item.fAuditorTime}>)
                                                <{/if}>
                                                    
                                            </td>
                                           
                                        </tr>
                                        
                                    </table>
                                    <br>
                                    
                                    <{/foreach}>
                                    </div>
                                </div>
                                <{/if}>
<{if $smarty.session.member_id == 6 && $data_case.cCaseStatus == 2}>
                                <{include file='includes/escrow/tabsOcr.inc.tpl'}>
                                <{include file='includes/escrow/tabsSign.inc.tpl'}>
<{/if}>
                        </form>
                    </div>

                    <center>
                        <br/>
                        
                        <{if $is_edit == 1}>
                            <{if $data_case.cSignCategory != 2 && $smarty.session.member_modifycase==1 && $data_case.cCaseStatus != 11 }>
                            <button id="save" style="width:150px;display:">儲存</button>
                            <{/if}>
                            <{if $smarty.session.member_pDep == 6 && $data_case.cCaseStatus == 11}>
                            <button id="save" style="width:150px;display:">儲存</button>
                            <{/if}>

                            <button id="ctrlform" style="width:150px;">匯出控管表</button>

                            <{if $data_case.cSignCategory != 2}>
                            <button id="checklist" style="width:150px;display:;">編修點交表</button>
                            <{/if}>

                            <button id="servicefee" style="width:200px;">匯出服務費申請單</button>
                            <{if $data_bankcode.bFrom == 2 }>
                                <{if $data_case.cSignCategory == 2}>
                                <button id="ecs">ECS</button>
                                <button id="unecs" disabled=disabled>切換回地政士</button>
                                <{else}>
                                <button id="unecs">切換回地政士</button>
                                <{/if}>
                            <{/if}>

                            <{if $smarty.session.pLegalCase == 1}>
                                <{if $legal.lSataus == '' || $legal.lSataus == 2}>
                                <{if $data_case.cCaseHandler == 1}>
                                <button id="legalbtn" class="legal-warning" onclick="transferLegal(2)">返還經辦</button>
                                <{else}>
                                <button id="legalbtn" onclick="transferLegal(1)">移交法務</button>
                                <{/if}>
                                <{/if}>
                            <{/if}>
                        <{else}>
                            <{if $data_case.cSignCategory != 2 && $data_case.cCaseStatus != 11 }>
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

                    <div style="height:20px;"></div>
                    <form method="POST" id="ku" action="ku.php"></form>
                </div>
                <div id="footer">
                    <p>2012 第一建築經理股份有限公司 版權所有</p>
                </div>
            </div>
        </div>
<script>
//回饋金隨案付款
if(<{$data_case.cFeedBackScrivenerClose}> == 1) {
    if(<{$data_realstate.cBranchNum}> == 505) {
            $(".gridtable input").attr("disabled", true);
            $(".add-feedback").hide();
    } else {
        $('[name^="cFeedbackTarget"]').attr("disabled", true);

        $("[name='newotherFeedType0'][value='2']").prop("checked", true).trigger("click");
        $('[name="newotherFeedType0"]').attr("disabled", true);
        $('[name^="otherFeedType"]').attr("disabled", true);
    }
}

function open_confirm_call(cid, bid){
        var cb_url = 'bankTransConfirmCall.php?action=contract&cid=' + cid + '&bid=' + bid;

        $.colorbox({
            iframe:true,
            width:"1200px", height:"90%",
            href:cb_url
        });
    }
</script>
    </body>
</html>
