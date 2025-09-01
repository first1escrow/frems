<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<meta http-equiv="X-UA-Compatible" content="IE=9"/>
		
        <{include file='meta.inc.tpl'}>
		<script src="/js/IDCheck.js"></script>
        <script src="/js/lib/comboboxNormal.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                setComboboxNormal('bank select','class');
                setComboboxNormal('bank-acc select','class');

                $("#status_t").hide();		
                var status = "<{$data.bStatus}>";
                var accdis = "<{$data.bAccountUnused}>";
                var accdis1 = "<{$data.bAccountUnused1}>";
                var accdis2 = "<{$data.bAccountUnused2}>";
                var accdis3 = "<{$data.bAccountUnused3}>";
                var accdis_disabled = "<{$accdis_disabled}>";
                var men = "<{$smarty.session.member_id}>";
                var dep = "<{$smarty.session.member_pDep}>";
                var data_feedData_count = parseInt($("[name='data_feedback_count']").val());
                var rg = "<{$data.bRg}>";

                if ("<{$ticketShow}>" == 'OK') {
                    $(".tks").show();
                }else{
                    $(".tks").hide();      
                }

                if ("<{$_disabled}>" != '') {
                	$("[name='feedDateCat']").attr('disabled', 'disabled');
                	$("[name='feedbackmoney[]']").attr('disabled', 'disabled');
                }

                if ("<{$signData.bContractStatus}>" == 1) {
                    $("#signSalesblock").show();
                }else{
                    $("#signSalesblock").hide();
                }

                <{if $smarty.session.member_rg == 1}>
                if (rg == 0) {
                    $(".rg_show").hide();
                }else if(rg == 1){
                    setInterval("getRgBalance()",60000) ;
                }

                $("[name='bRg']").on('click',function() {
                    if ($(this).val()==1) {
                        $(".rg_show").show();   
                        $("[name='bRgfirst']").val(1);
                    }else{
                        $(".rg_show").hide();
                        $("[name='bRgfirst']").val('');
                    }
                });
                <{/if}>
                $(".iframe2").colorbox({iframe:true, width:"40%", height:"60%"});
                if (status ==2 ) {
                    var array = "input,select,textarea";
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                    });

                    $(".save").hide();
                }else if(status ==3){
                    var array = "input,select,textarea";
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                    });

                    $("#status_t").show();
                    $(".save").hide();
                }

                $('[name="bStatus"]').removeAttr('disabled');
                { $disable_account  = false;}
                if ( ['4', '7', '5', '6', '12'].includes(dep)) { //經辦 業務只可以看
                     var array = "input,select,textarea";
                   { $disable_account  = true}
                    $('.bank select').combobox('disable');
                     $(".distable").find(array).each(function() {
                        if (men != 48 && men != 1) {
                            $(this).attr('disabled', true);
                        }
                    }); 

                    if (dep == 7) {
                        $('[name="bStatus"]').attr('disabled', true);
                        $('[name="bBrand"]').attr('disabled', true);
                        $('[name="bStore"]').attr('disabled', true);
                        $('[name="bName"]').attr('disabled', true);
                    }
                }
                   
                if (data_feedData_count == 0) {
                    $(".distable2").show();
                }else{
                     $(".distable2").hide();
                }

                if (accdis==1) {
                    $(".acc_disabled").each(function() {
                         $(this).attr('disabled', true);
                    });
                }

                 if (accdis1==1) {
                    $(".acc_disabled1").each(function() {
                         $(this).attr('disabled', true);
                    });
                }

                 if (accdis2==1) {
                    $(".acc_disabled2").each(function() {
                         $(this).attr('disabled', true);
                    });
                }

                 if (accdis3==1) {
                    $(".acc_disabled3").each(function() {
                         $(this).attr('disabled', true);
                    });
                }

                if (accdis_disabled == 1) {
                    $('.bank-acc select').combobox('disable');
                    $(".accdis_disabled").each(function() {
                         $(this).attr('disabled', true);
                    });
                }

				$('#dialog').dialog({
					modal: true,
					autoOpen: false,
					buttons: {
						OK: function() {
							$(this).dialog("close") ;
						}
					}
				}) ;

                $( "#dialog2" ).dialog({
                    autoOpen: false,
                    modal: true,
                    minHeight:50,
                    show: {
                        effect: "blind",
                        duration: 1000
                    },
                    hide: {
                        effect: "explode",
                        duration: 1000
                    }
                });
				
				/* 檢核輸入統一編號是否合法 */
				if (checkUID($('[name="bSerialnum"]').val())) {
					$('#rId').html('<img src="/images/ok.png">') ;
				}
				else {
					$('#rId').html('<img src="/images/ng.png">') ;
				}
				
				$('[name="bSerialnum"]').keyup(function() {
					if (checkUID($('[name="bSerialnum"]').val())) {
						$('#rId').html('<img src="/images/ok.png">') ;
					}
					else {
						$('#rId').html('<img src="/images/ng.png">') ;
					}
				}) ;

                $("[name='fIdentityNumber[]']").each(function(key) { 
                    var i = key+1;
                    checkID(i,this.value,'')
                });
				
				getFBseasons() ;
				
				$(".ajax").colorbox({width:"400",height:"100"});
				$(".iframe").colorbox({
					iframe:true,
					width:"60%",
					height:"500px",
					onClosed:function() { $('#reloadPage').submit() ; }
				});

				var checkClick = 1;

                $('.add').on('click', function () {
                    //檢查太平洋店家是否有選定群組
                    if (checkPOGroupSelected() === false) {
                        alert('太平洋相關店家，請選定群組');
                        $('[name="bGroup"]').select().focus();
                        return ;
                    }
                    ////

                    var store =$("[name='bStore']").val();
                    var company = $("[name='bName']").val();
                    var brand = $("[name='bBrand']").val();
                        $.ajax({
                        url: '/includes/maintain/checkcontractstaus.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {'type':'bsamea','store':store,'company':company,'brand':brand},
                    })
                    .done(function(txt) {
                        if (txt != '') {
                            alert('有相同的店名和法人禁止新增\r\n同'+txt);
                        }else{
                            // if (checkClick <= 1) {
                            if (checkClick <= 3) {
                                checkClick++;
                                PassMessage('add');
                            }
                        }
                    });
                });

                $("[name='bContractStatus[]']").on('click', function() {
                    if ($("[name='bContractStatus[]']").attr('checked')) {
                        $("[name='bContractStatusTime']").val("<{$today}>");
                        $("#signSalesblock").show();
                    }else{
                       $("[name='bContractStatusTime']").val('000-00-00');
                       $("#signSalesblock").hide();
                    }
                });
                
                $('.save').on('click', function () {
                    var status = $('[name="bStatus"]').val();
                    var b = $('[name="id"]').val();

                    //檢查太平洋店家是否有選定群組
                    if (checkPOGroupSelected() === false) {
                        alert('太平洋相關店家，請選定群組');
                        $('[name="bGroup"]').select().focus();
                        return ;
                    }
                    ////

                    if (status == 2 || status == 3) {
                         //確認是否有進行中案件
                       $.ajax({
                            url: '/includes/maintain/checkcontractstaus.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'bId': b,'type':'b'},
                        })
                        .done(function(txt) {
                            if (txt == 1) {
                                alert('有進行中案件，禁止更改狀態');
                            }else{
                                PassMessage('save');
                            }
                        });
                    }else{
                        var store =$("[name='bStore']").val();
                        var company = $("[name='bName']").val();
                        var brand = $("[name='bBrand']").val();
                         $.ajax({
                            url: '/includes/maintain/checkcontractstaus.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'bId': b,'type':'bsames','store':store,'company':company,'brand':brand},
                        })
                        .done(function(txt) {
                           if (txt != '') {
                                alert('有相同的店名和法人禁止新增\r\n同'+txt);
                            }else{
                                PassMessage('save');
                            }
                        });
                    }
                });
                
                $( "#tabs" ).tabs({
                    selected: 0
                });

//新增飛鷹地產仲介店時，回饋金預設值要自動設定
                // $('[name=bBrand]').on('change', function () {
                //     if($('[name=bBrand]').val() == 75) {
                //         $('[name=bRecall]').val('25');
                //         $('[name=bScrRecall]').val('18');
                //     }
                // });

                $('[name=bAccountNum1]').on('change', function () {
                    GetBankBranchList($('[name=bAccountNum1]'),
                                        $('[name=bAccountNum2]'),
                                        null);
                });

                $('[name=bAccountNum11]').on('change', function () {
                    GetBankBranchList($('[name=bAccountNum11]'),
                                        $('[name=bAccountNum21]'),
                                        null);
                });

                $('[name=bAccountNum12]').on('change', function () {
                    GetBankBranchList($('[name=bAccountNum12]'),
                                        $('[name=bAccountNum22]'),
                                        null);
                });

                 $('[name=bAccountNum13]').on('change', function () {
                    GetBankBranchList($('[name=bAccountNum13]'),
                                        $('[name=bAccountNum23]'),
                                        null);
                });
              
                $('[name="FBYear"]').change(function() {
					getFBseasons() ;
				}) ;
				
                $('#sync_owneraddr').on('change', function () {
                    if ($('#sync_owneraddr').attr('checked') == 'checked') {
                        $('[name=owner_basecountry]').val($('[name=owner_registcountry]').val());
                        $('[name=owner_basearea]').html($('[name=owner_registarea]').html());
                        $('[name=owner_basearea]').val($('[name=owner_registarea]').val());
                        $('[name=owner_baseaddr]').val($('[name=owner_registaddr]').val());
                        $('[name=owner_basezip]').val($('[name=owner_registzip]').val());
                    }
                });

                $('#sync_buyeraddr').on('change', function () {
                    if ($('#sync_buyeraddr').attr('checked') == 'checked') {
                        $('[name=buyer_basecountry]').val($('[name=buyer_registcountry]').val());
                        $('[name=buyer_basearea]').html($('[name=buyer_registarea]').html());
                        $('[name=buyer_basearea]').val($('[name=buyer_registarea]').val());
                        $('[name=buyer_baseaddr]').val($('[name=buyer_registaddr]').val());
                        $('[name=buyer_basezip]').val($('[name=buyer_registzip]').val());
                    }
                });

                $('#search').on('click', function () {
                    $( "#dialog2" ).dialog("open") ;

                    var url ='branch_ai_result.php';
                    var lander_Sdate = $("[name='lander_Sdate']").val();
                    var lander_Edate = $("[name='lander_Edate']").val();
                    var sales_bank = $("[name='sales_bank']").val();
                    var ge_id = $("[name='ge_id']").val();
                    var bid = $("[name='id']").val();
                    var brand = $("[name='bBrand']").val();

                    $.post(url, {'lander_Sdate': lander_Sdate,'lander_Edate':lander_Edate,'sales_bank':sales_bank,'ge_id':ge_id,'bid':bid,'brand':brand}, function(data) {
                        $("#show").html(data);
                        $( "#dialog2" ).dialog("close") ;
                    });
                });

                $('[name="bStatus"]').on('change',function() {
                    var val = $('[name="bStatus"]').val();
                    var status_now = "<{$data.bStatus}>";
                    
                    if (val == 3) {
                        $("#status_t").show();
                    }else {
                        $("#status_t").hide();
                    }

                    if (val != status_now) {
                        $(".save").show();
                    }else{
                        $(".save").hide();
                    }
                });
				
                $('#imgStamp').on('click',function() {
					var url = "stamp.php?bId=<{$data.bId}>" ;
					
					$.colorbox({
						iframe: true,
						width: "400px",
						height: "300px",
						href: url,
						onClosed: function() {
							//$('#reloadPage').submit() ;
						}
					}) ;
                });

                $("#check_changeFeedBackData").on('keydown', function() {
                    $("[name='change_feedbackData']").val(1);
                });

                $("#check_changeFeedBackData").on('change', function() {
                    $("[name='change_feedbackData']").val(1);
                });

                $('[name="bBrand"], [name="bCategory"]').on('change',function() {
                    var is_edit = <{$is_edit}>;
                    if(is_edit == 0) {
                        channelLevelSet();
                    }
                });

                hidechannelLevel();

                $('#imgStamp').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('.save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('.add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#buyer_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#owner_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#search').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#btnPrint').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                });

                $('#nSave').button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
            });

            function channelLevelSet() { //當通路為台屋加盟、時設為B級；若台屋加盟但店名包含總管理字樣時，將通路設為A級、且隱藏通路欄位顯示；其餘則依率顯示為A級通路
                $('#channel-weight').show();

                if($("[name='bBrand']").val() == 1 && $("[name='bCategory']").val() == 1 && !$('[name="bStore"]').val().match(/總管理/)) {
                    $("[name='bSalesLevel']").val('B');
                    return;
                }

                $("[name='bSalesLevel']").val('A');
                hidechannelLevel();
            }

            function hidechannelLevel() { //台屋加盟且店名包含總管理字樣時，隱藏通路欄位顯示
                if($("[name='bBrand']").val() == 1 && $("[name='bCategory']").val() == 1 && $('[name="bStore"]').val().match(/總管理/)) {
                    $('#channel-weight').hide();
                }
            }

			function NewSave(){
                if (confirm("確定是否要另存成新的店家?")) {
                    PassMessage('nAdd');
                }
            }

			function newImg() {
				$('#newImg').submit() ;
			}
			
            function checkID(no,val,type) {
                if (checkUID(val)) {
                    $('#'+type+'fId'+no).html('<img src="/images/ok.png">') ;
                } else {
                    $('#'+type+'fId'+no).html('<img src="/images/ng.png">') ;
                }
            }

           function addFeedBack(){
                var count = parseInt($("[name='data_feedback_count']").val());
                var no = count+1;

                $("#newAccountNum").combobox("destroy");
                $("#newAccountNumB").combobox("destroy");

                var clonedRow = $('.newrow:first').clone(true).show();
                clonedRow.find('#newzipC').attr('id', 'newzipC'+no);
                clonedRow.find('#newcountryC').attr({
                    id: 'newcountryC'+no,
                    onchange: 'getArea2("newcountryC'+no+'","newareaC'+no+'","newzipC'+no+'")'
                });
                clonedRow.find('#newareaC').attr({
                    id: 'newareaC'+no,
                    onchange: 'getZip2("newareaC'+no+'","newzipC'+no+'")'
                });

                clonedRow.find('#newcountryR').attr({
                    id: 'newcountryR'+no,
                    onchange: 'getArea2("newcountryR'+no+'","newareaR'+no+'","newzipR'+no+'")'
                });
                
                clonedRow.find('#newareaR').attr({
                    id: 'newareaR'+no,
                    onchange: 'getZip2("newareaR'+no+'","newzipR'+no+'")'
                });
                clonedRow.find('#newzipCF').attr('id', 'newzipC'+no+'F');    
                clonedRow.find('#newzipR').attr('id', 'newzipR'+no);
                clonedRow.find('#newzipRF').attr('id', 'newzipR'+no+'F'); 
                clonedRow.find('#newAccountNum').attr({
                    id: 'newAccountNum'+no,
                    onchange: 'Bankchange("newAccountNum'+no+'","newAccountNumB'+no+'")'
                });

                clonedRow.find('#newAccountNumB').attr('id', 'newAccountNumB'+no);
                clonedRow.find('#newfId').attr('id', 'newfId'+no);
                clonedRow.find('#newIdentityNumber').attr('onkeyup', "checkID("+no+",this.value,'new')");
                clonedRow.find('input').val('');
                clonedRow.find('select').val('');
                
                clonedRow.insertAfter('.newrow:last');

                setComboboxNormal("newAccountNum","id");
                setComboboxNormal("newAccountNumB","id");
                setComboboxNormal("newAccountNum"+no,"id");
                setComboboxNormal("newAccountNumB"+no,"id");
                
               $("[name='data_feedback_count']").val(no);
            }

            function getArea2(ct,ar,zp) {
                var url = '../escrow/listArea.php' ;
                var ct = $('#' + ct + ' :selected').val() ;
                
                $('#' + zp + '').val('') ;
                $('#' + zp + 'F').val('') ;
                $('#' + ar + ' option').remove() ;
                
                $.post(url,{"city":ct},function(txt) {
                    var str = '' ;
                    str = str + txt  ;
                    $('#' + ar ).append(str) ;
                }) ;
            }
            
            function getZip2(ar,zp) {
                var zips = $('#' + ar + ' :selected').val() ;

                $('#' + zp + '').val(zips);
                $('#' + zp + 'F').val(zips.substr(0,3));
            }

            function Bankchange(main,branch){ //type,no
                GetBankBranchList($('#'+main),$('#'+branch),null);                                    
            }

            function PassMessage(func) {
                var t_url = '';
                if (func == 'add') {
                    t_url = '/includes/maintain/branchadd.php';
                }else if(func == 'nAdd'){
                    t_url = '/includes/maintain/branchnewadd.php';
                }else {
                    t_url = '/includes/maintain/branchsave.php';
                }

                var password1 = $('[name=password1]').val();
                var password2 = $('[name=password2]').val();
                if (password1 != password2) {
                    alert('確認密碼必需一致!');
                    return;
                }

                var zip = $('[name="zip"]').val();
                if (zip=='') {
                    $('[name="area"]').focus();
                    alert("請填寫公司地址");
                    return false;
                }

                var email = $("[name='bEmail']").val();
                if (email != '') {
                    var filter_mail = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
                    if (!filter_mail.test(email)) {
                        alert('請輸入正確的電子信箱');
                        $("[name='bEmail']").focus();
                        return false;
                    }
                }

                var input = $('input');
                var textarea = $('textarea');
                var select = $('select');
                var arr_input = new Array();
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
                    if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    } else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }
                    } else {
                        if (reg.test($(item).attr("name"))) {
                            if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }else{
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }    
                    }
                });

                arr_input['tNID'] = MultiInput('tNID');
                arr_input['tName'] = MultiInput('tName');
                arr_input['tMobile'] = MultiInput('tMobile');

                $('.save').hide();//禁止使用者多按
                var obj_input = $.extend({}, arr_input);
                var request = $.ajax({  
                    url: t_url,
                    type: "POST",
                    data: obj_input,
                    dataType: "html"
                });
                request.done( function( msg ) {
                    var dep = "<{$smarty.session.member_pDep}>";
                    alert(msg); 
                    var sales_from = '<{$from_sales}>';

                    if (sales_from=='sales') { ////判斷是否為業務責任區審核來的
                        $("#form_back").attr('action','/sales/salesAgents.php');
                    }

                    if (dep==9 || dep==10) {
                        $("#reloadPage").submit();
                    }else{
                        $('#form_back').submit();
                    }
                });
            }

			function show_hide_branch() {
				var cl = $('[name="bStoreClass"]:checked').val() ;

				if (cl == 1) {
					$('#bClassBranch').css({'display':''}) ;
					$('#get_branch').css({'display':''}) ;
				} else {
					$('#bClassBranch').css({'display':'none'}) ;
					$('#get_branch').css({'display':'none'}) ;
				}
			}

			function getFBseasons() {
				var yr = $('[name="FBYear"]').val() ;
				var no = "<{$data.bId}>" ;
				var url = 'branchfeedback.php' ;
				
				$.post(url,{"y":yr,"sn":no,"type":'branch'},function(txt) {
					var str = txt.split(',') ;
					$('[name="fbs1"]').val(str[0]) ;
					$('[name="fbs2"]').val(str[1]) ;
					$('[name="fbs3"]').val(str[2]) ;
					$('[name="fbs4"]').val(str[3]) ;
				}) ;
			}
			
			function delFeedBack(no) {
				var url = '../includes/maintain/feedBackDataDel.php' ;
				$.post(url,{'no':no,'bId':"<{$data.bId}>"},function() {
                    $('#reloadPage').submit() ;
				}) ;
			}
			
            function first_pg01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var record_limit = $('[name="record_limit"]').val() ;
                var current_page = parseInt($('[name="current_page"]').val()) ;
                var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();

                if (current_page <= 1) { return false ; }
                else { current_page = 1 ; }
                
                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                        $( "#dialog2" ).dialog("close") ;
                    }) ;
            }

            function back_pg01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var record_limit = $('[name="record_limit"]').val() ;
                var current_page = parseInt($('[name="current_page"]').val()) - 1 ;
                var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();

                if (current_page <= 0) { return false ; }
                
                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                        $( "#dialog2" ).dialog("close") ;
                    }) ;
            }

            function next_pg01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var total_page = parseInt($('[name="total_page"]').val()) ;
                var current_page = parseInt($('[name="current_page"]').val()) + 1 ;
                var record_limit = $('[name="record_limit"]').val() ;
                var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();

                if (current_page > total_page) { return false ; }

                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                        $( "#dialog2" ).dialog("close") ;
                    }) ;
            }

            function last_pg01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var total_page = parseInt($('[name="total_page"]').val()) ;
                var current_page = parseInt($('[name="current_page"]').val()) ;
                var record_limit = $('[name="record_limit"]').val() ;
                var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();

                if (current_page >= total_page) { return false ; }
                else { current_page = total_page ; }
                
                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                        $( "#dialog2" ).dialog("close") ;
                    }) ;
            }

            function direct_pg01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var total_page = parseInt($('[name="total_page"]').val()) ;
                var current_page = parseInt($('[name="current_page"]').val()) ;
                var record_limit = $('[name="record_limit"]').val() ;
                var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();

                if (current_page >= total_page) { current_page = total_page ; }
                else if (current_page <= 0) { current_page = 1 ; }
                
                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                        $( "#dialog2" ).dialog("close") ;
                    }) ;
            }

            function show_limit01() {
                $( "#dialog2" ).dialog("open") ;
                var sales_bank = $('[name="sales_bank"]').val() ;
                var lander_Sdate = $('[name="lander_Sdate"]').val() ;
                var lander_Edate = $('[name="lander_Edate"]').val() ;
                var ge_id = $('[name="ge_id"]').val() ;
                var total_page = parseInt($('[name="total_page"]').val()) ;
                var current_page = parseInt($('[name="current_page"]').val()) ;
                var record_limit = $('[name="record_limit"]').val() ;
               var bid = $("[name='id']").val();
                var brand = $("[name='bBrand']").val();
                
                $.post('branch_ai_result.php',
                    {'sales_bank':sales_bank,'lander_Sdate':lander_Sdate,'lander_Edate':lander_Edate,'ge_id':ge_id,'current_page':current_page,'record_limit':record_limit,'bid':bid,'brand':brand},
                    function (txt) {
                        $('#show').html(txt) ;
                         $( "#dialog2" ).dialog("close") ;
                    }) ;
            }
			
            function printpage() {
                $("[name='print_b']").attr('action', 'branch_print.php');
                $("[name='print_b']").submit();
            }
			
			function unlocker() {
				var URLs = "unlocker.php" ;
				$.ajax({
					url : URLs,
					data: {id:"<{$data.bId}>", tp:"b"},
					type: "POST",
					dataType: "text",
					success: function(txt) {
						if (txt == 'T') {
							$('#lockerPNG').remove() ;
							$('#branchId').append('<img id="lockerPNG" src="../images/unlock.png">') ;
							alert('帳號已解鎖完成!!') ;
						}
						else {
							alert('帳號解鎖錯誤!!請通知資訊單位處理!!') ;
							//alert(txt) ;
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(xhr.status) ;
						alert(thrownError) ;
					}
				}) ;
			}

            function RgBonus(){
                $.ajax({
                    url: '../includes/maintain/setRgBonus.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "<{$data.bCode2}>",cat:"R",money:$("[name='bRgBonus']").val()},
                })
                .done(function(respone) {
                    alert(respone);
                    $("[name='bRgBonus']").val(0);
                    getRgBalance();
                });
            }

            function getRgBalance(){
                $.ajax({
                    url: '../includes/maintain/getRgBalance.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "<{$data.bCode2}>"},
                })
                .done(function(re) {
                    $("[name='bRgBalance']").val(re);
                });
            }

            function addBank(){
                var count = parseInt($("[name='bank_count']").val());
                if (<{$accdis_disabled}> == 1) { //經辦人員不能隨意修改指定解匯帳戶
                    return;
                }
                $.ajax({
                    url: '../includes/maintain/getBankBlock.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'val': count},
                })
                .done(function(msg) {
                    $(msg).insertBefore("#copyBank");
                    setComboboxNormal('NewBankMain'+count,'id');
                    setComboboxNormal('NewBankBranch'+count,'id');
                });
            
                $("[name='bank_count']").val((count+1));
           }

            function copyFeedData(id){
                $("[name='change_feedbackData']").val(1);
                var url = 'copyFeedData.php?id='+id;
                $.colorbox({iframe:true, width:"50%", height:"50%", href:url ,onClosed:function() {
                    var count = parseInt($("[name='data_feedback_count']").val());
                    var no = count+1;

                    $("[name='newAccountNum[]']").each( function(index, val) {
                        $("#"+$(this).attr('id')).combobox("destroy");
                        setComboboxNormal($(this).attr('id'),"id");
                    });

                    $("[name='newAccountNumB[]']").each( function(index, val) {
                        $("#"+$(this).attr('id')).combobox("destroy");
                        setComboboxNormal($(this).attr('id'),"id");
                    });
                }}) ;
            }

            function copyFeedData2(id){
               $("[name='change_feedbackData']").val(1);
                var cno = parseInt($("[name='data_feedback_count']").val());
                if (cno > 0) {
                    $("#cl",window.parent.document).click();
                    cno++;
                }

                $('.newrow:last [name="newTtitle[]"]').val($('[name="bName"]').val());
                $('.newrow:last [name="newMobileNum[]"]').val($('[name="bMobileNum"]').val());
                $('.newrow:last [name="newIdentity[]"]').val(3);
                $('.newrow:last [name="newIdentityNumber[]"]').val($('[name="bSerialnum"]').val());
                $('.newrow:last [name="newzipC[]"]').val($("[name='zip']").val());
                $('.newrow:last [name="newzipCF"]').val($("[name='zip']").val());
                $('.newrow:last [name="newcountryC"]').val($('[name="country"]').val());

                var url = '../escrow/listArea.php' ;
                $('.newrow:last #newareaCR option').remove() ;
                            
                $.post(url,{"city":$('[name="country"]').val()},function(txt) {
                    var str = '' ;
                    str = str + txt  ;
                    $('.newrow:last [name="newareaC"]').append(str) ;
                    $('.newrow:last [name="newareaC"]').val($('[name="area"]').val());
                }) ;

                $('.newrow:last [name="newaddrC[]"]').val($("[name='addr']").val());

                $.ajax({
                    url: 'getBranchAccount.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "<{$data.bId}>"},
                })
                .done(function(msg) {
                    var obj = JSON.parse(msg);
                    
                    $('.newrow:last [name="newAccountNum[]"]').combobox("destroy");
                    $('.newrow:last [name="newAccountNum[]"]').val(obj.bank);
                    $('.newrow:last [name="newAccountNumB[]"]').combobox("destroy");
                    
                    GetBankBranchListCb('.newrow:last [name="newAccountNum[]"]','.newrow:last [name="newAccountNumB[]"]',obj.bankBranch);

                    $('.newrow:last [name="newAccountNum[]"]').combobox();
                    $('.newrow:last [name="newAccount[]"]').val(obj.Account3);
                    $('.newrow:last [name="newAccountName[]"]').val(obj.Account4);
                });
            }

            function addSignSales(){
                var sales = $("[name='signSales']").val();
                var check = 0;
                if (sales == 0) {
                    alert("請選擇簽約業務");
                    return false;
                }

                $('[name="signSalseID[]"]').each(function(index, el) {
                    if ($(this).val() == sales) {
                        check = 1;
                    }
                });

                if (check == 1) {
                    alert("已重複建立簽約業務");
                    return false;
                }

                if ($(".signSales:last").length > 0) {
                    var clone = $(".signSales:last").clone(true);

                    clone.find('#signName').html($("[name='signSales']").find('option:selected').text());
                    clone.find('[name="signSalseID[]"]').val(sales);
                    clone.find('a').attr('onclick',"delSignSales("+sales+")");
                    clone.insertAfter(".signSales:last").attr("id",'sign'+sales);
                } else {
                    var html = "<span id=\"sign"+sales+"\" class=\"signSales\"><span id=\"signName\">"+$("[name='signSales']").find('option:selected').text()+"<\/span><a href=\"javascript:void(0)\" onclick=\"delSignSales("+sales+")\">X<\/a><input type=\"hidden\" name=\"signSalseID[]\" value=\""+sales+"\"><\/span>";
                    $(html).insertAfter("#addSalse");
                }

               $("[name='signSales']").val(0);
            }

            function delSignSales(sales){
               $("#sign"+sales).remove();
            }

            function GetBankBranchListCb(bank, branch, sc) {
                $(branch).prop('disabled',true) ;
                
                var request = $.ajax({  
                    url: "/includes/maintain/bankbranchsearch.php",
                    type: "POST",
                    data: {
                        bankcode: $(bank).val()
                    },
                    dataType: "json"
                });

                request.done(function( data ) {
                    $(branch).children().remove().end();
                    $(branch).append('<option value="">------</option>')
                    $.each(data, function (key, item) {
                        if (key == sc ) {
                            $(branch).append('<option value="'+key+'" selected>'+item+'</option>');
                        } else {
                            $(branch).append('<option value="'+key+'">'+item+'</option>');
                        }
                    });
                    $(branch).combobox();
                });
                
                $(branch).prop('disabled',false) ;
            }

            function setCashierOrderMemo(category,id){
                 var b = $('[name="id"]').val();
                 if (b == '') {
                    alert("請新增店家後再寫備住");
                    return false;
                 }

                if ($("[name='bCashierOrderMemo']").val() == '' && category == 'add') {
                    alert('請填寫備註欄位');
                    return false;
                }

                $.ajax({
                    url: 'setCashierOrderMemo.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {bId: $("[name='id']").val(),memo:$("[name='bCashierOrderMemo']").val(),category:category,id:id},
                })
                .done(function(html) {
                    $("#memo").html(html);
                    if (category =='add') {
                        $("[name='bCashierOrderMemo']").val('');
                    }
                });
            }

            function checkPOGroupSelected() {
                let _brand = $('[name="bBrand"] option:selected').val();

                if (_brand == '46') {
                    let _group = $('[name="bGroup"] option:selected').val();
                    let _groups = ["25", "26", "27"];
                    
                    if (!_groups.includes(_group)) {
                        return false;
                    }
                }
                
                return true;
            }

            function individual(action, store='') {
                if (action == 'DELETE') {
                    individual_delete(store);
                    return;
                }

                if (action == 'ADD') {
                    individual_add();
                    return;
                }
            }

            function individual_delete(store) {
                if (store == '') {
                    alert('店家不存在');
                    return;
                }

                if (confirm('確定是否要刪除此店家?')) {
                    $.ajax({
                        url: '/includes/maintain/individual.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {bId: '<{$data.bId}>', action: 'DELETE', store: store},
                    })
                    .done(function(html) {
                        $('#bIndividual').empty().html(html);
                        alert('刪除成功');
                    })
                    .fail(function(jqXHR, textStatus) {
                        alert(jqXHR.responseText);
                    });
                }
            }

            function individual_add() {
                let store = $('#bIndividual_select option:selected').val();

                if (store == '') {
                    alert('請選擇店家');
                    return;
                }

                $.ajax({
                    url: '/includes/maintain/individual.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {bId: '<{$data.bId}>', action: 'ADD', store: store},
                })
                .done(function(html) {
                    $('#bIndividual').empty().html(html);
                    $('#bIndividual_select option:selected').remove();
                    alert('新增成功');
                })
                .fail(function(jqXHR, textStatus) {
                    alert(jqXHR.responseText);
                });
            }

            function bankDelete(id) {
                if (<{$accdis_disabled}> == 1) { //經辦人員不能隨意修改指定解匯帳戶
                    alert('無法刪除指定解匯帳戶');
                    return;
                }

                if (confirm("確定要刪除?")) {
                    $.ajax({
                        url: '/includes/maintain/branchBankDelete.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {id: id},
                    })
                    .done(function(response) {
                        if (response == 'ok') {
                            $('.transBank' + id).remove();
                            $('[name="bank_count"]').val(parseInt($('[name="bank_count"]').val()) - 1);
                            alert('刪除成功');
                        } else {
                            let msg = '刪除失敗';
                            msg = response ? msg + '(' + response + ')' : msg;
                            alert(msg);
                        }
                    })
                    .fail(function(xhr, status, error) {
                        alert('刪除失敗');
                        console.log(xhr.responseText, xhr);
                    });
                }
            }

            function newBankDelete(id) {
                if (confirm("確定要刪除?")) {
                    $('.newBankDelete' + id).remove();
                    $('[name="bank_count"]').val(parseInt($('[name="bank_count"]').val()) - 1);
                }
            }
        </script>
        <style type="text/css">
            #nSave  {
                 background: #FFAC55;
            }

            #nSave:hover{
                background: #FF8F19 ;
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

			.small_font {
				font-size: 9pt;
				line-height:1;
			}

            #tb001 {
                border-style:solid ;
                border-color:#CCC ;
                border-width:1px ;
                text-align:left ;
            }

            #dialog2 {
                background-image:url("/images/animated-overlay.gif") ;
                background-repeat: repeat-x;
                margin: 0px auto;
            }

            .ui-autocomplete-input{ 
                width: 120px;
            }

            .signSales{
                background-color: white;
                margin-right: 1px;
                border: 1px solid #999;
                padding: 2px;
            }

           .ra1{
                /*font-size: 16px;*/
                width: 18px;
                height: 16px;
           }

           .tb td{
                border:1px solid #999;
                padding-top:3px; 
                padding-bottom:3px; 
            }
        </style>
    </head>
    <body id="dt_example">
        <form id="newImg" method="POST" target="_blank" action="showStamp.php">
            <input type="hidden" name="bId" value="<{$data.bId}>">
        </form>		
        <form name="print_b" method="POST" target="_blank">
            <input type="hidden" name="id" value="<{$data.bId}>">
        </form>
		<form id="reloadPage" method="POST">
			<input type="hidden" name="id" value="<{$data.bId}>">
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
                         <td width="753">
                            <table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
                                <tr>
                                    <td colspan="3" align="right"><h1><{include file='welcome.inc.tpl'}></h1></td>
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

                    </tr>
                </table>
            </div>

            <div id="content">
            <div id="show2">
                    </div>
                    <div class="abgne_tab">
                        <{include file='menu1.inc.tpl'}>
                        <div class="tab_container">
                            <div id="menu-lv2"></div>
                            <br/> 
                            <div id="tab" class="tab_content">
                            <div id="dialog2"></div>
                               <div id="tabs">
            <ul>
                <li><a href="#tabs-contract">仲介店頭維護</a></li>
                <li><a href="#tabs-feedback">回饋金資訊</a></li>
                <li><a href="#tabs-ai">帳務明細</a></li>
                <{if $smarty.session.member_act_report == '1'}>
                    <li><a href="#tabs-act">活動設定</a></li>
                <{/if}>
            </ul>
            <div id="tabs-contract">
                
                <form name="form_branch">
                <table border="0" width="100%" id="print">
                    <tr>
                        <td width="14%"></td>
                        <td width="19%"></td>
                        <td width="14%"></td>
                        <td width="19%"></td>
                        <td width="14%"></td>
                        <td width="19%"></td>
                    </tr>
                    <tr>
                        <th>仲介店編號︰</th>
                        <td id="branchId">
                           <input type="hidden" name="id" value="<{$data.bId}>">
                           <input type="text"  maxlength="10" class="input-text-big" value="<{$data.bCode2}>" disabled='disabled' />
						   <{$locker}>
                        </td>
                        <th>密碼輸入︰</th>
                        <td>
                            <input type="text" name="password1" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  />
                            <br/>
                            密碼長度6~12碼，密碼必同時包含大、小寫英文字母阿拉伯數字0-9英文小寫視為不同密碼
                        </td>
                        <th>再次確認密碼︰</th>
                        <td>
                            <input type="password" name="password2" maxlength="12" class="input-text-big" value="<{$data.bPassword}>"  />
                        </td>
                    </tr>
                    <tr>
                        <th>仲介品牌名稱︰</th>
                        <td>
                            <{html_options name=bBrand options=$menu_brand selected=$data.bBrand}>
                        </td>
                        <th>仲介店名︰</th>
                        <td>
                            <input type="text" name="bStore" maxlength="20" class="input-text-per" value="<{$data.bStore}>"  />
                        </td>
                        <th>仲介商類型︰</th>
                        <td>
                            <{html_options name=bCategory options=$menu_categoryrealestate selected=$data.bCategory}>
                        </td>
                    </tr>
                    <tr>
                        <th>仲介公司︰</th>
                        <td colspan="3">
                            <input type="text" name="bName" maxlength="30" class="input-text-per" value="<{$data.bName}>"  />
                        </td>
						<{if $imgStampNew == 1}>
						
						<td colspan="2" rowspan="5" valign="top" style="text-align:center;">
							&nbsp;
						</td>
						
						<{else}>
						
						<td colspan="2" rowspan="5" valign="top" style="text-align:center;">
							<div><button id="imgStamp" type="button">指定大小章圖檔</button></div>
							<div id="showImg" style="margin-top:5px;width:246px;height:160px;border: 1px solid #CCC;padding:2px;">
								<{$imgStamp}>
							</div>
						</td>
						
						<{/if}>
						<td></td>
                    </tr>
                    <tr> 
                        <th>狀態︰</th>
                        <td colspan="5">
                            <{html_options name=bStatus options=$menu_categorybranchstatus selected=$data.bStatus}>
                            <span id="status_t">，時間
                            <input type="text" name="bStatusDateStart" value="<{$data.bStatusDateStart}>" class="datepickerROC" readonly="" style="width:100px;" >至<input type="text" name="bStatusDateEnd" value="<{$data.bStatusDateEnd}>" class="datepickerROC" readonly="" style="width:100px;" >
                                
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>統一編號︰</th>
                        <td>
                            <input type="text" name="bSerialnum" maxlength="8" style="width:150px;" class="input-text-per" value="<{$data.bSerialnum}>"  />
							<span id="rId"></span>
                        </td>
                       
                        <td colspan="4"></td>
                    </tr>
                    <tr>
                        <th>群組︰</th>
                        <td><{html_options name=bGroup options=$menu_group selected=$data.bGroup}>(群組回饋設定該欄位)</td>
                        <th>群組2︰</th>
                        <td colspan="3"><{html_options name=bGroup2 options=$menu_group selected=$data.bGroup2}></td>
                    </tr>
					
                    <tr>
                        <th>負責業務︰</th>
                        <td>
							<span style="margin-left:10px;" id="salesList">
								<{$bSales}> <{if $smarty.session.pBusinessOwnership == 1}><a href="../sales/salesBranchArea.php" target="_blank">(編輯)</a> <{/if}>
							</span>
                        </td>
						
						<td colspan="4">
							
						</td>
                    </tr>
                    <tr>
                        <th>前負責業務︰</th>
                        <td colspan="5">
                            <{html_options name=bSales options=$menu_sales selected=$data.bSales}>，更改日期:
                            <input type="text" class="datepickerROC" name="bSalesDate" value="<{$data.bSalesDate}>" readonly style="width:100px;">
                        </td>
                    </tr>
                    <{if $smarty.session.pBusinessEdit == '1' && $smarty.session.pBusinessView == '1'}>
				    <tr>
                        <th>業務已簽約仲介店︰</th>
                        <td colspan="5">
                            <{html_checkboxes name='bContractStatus' options=$menu_cstatus selected=$signData.bContractStatus separator=' '}>，簽約日期：
                            <input type="text" name="bContractStatusTime" value="<{$signData.sSignDate}>" class="datepickerROC" readonly style="width:100px;">
                            <span id="signSalesblock">簽約業務(未選會帶預設業務)：
                                <{html_options name=signSales options=$menu_sales}> 
                                <input type="button" value="新增" onclick="addSignSales()" id="addSalse">
                                <{foreach from=$signSales key=key item=item}>
                                <span id="sign<{$key}>" class="signSales" >
                                    <span id="signName"><{$item}></span> 
                                    <a href="javascript:void(0)" onclick="delSignSales(<{$key}>)">X</a>
                                    <input type="hidden" name="signSalseID[]" value="<{$key}>">
                                </span>
                                <{/foreach}>
                            </span>
                        </td>
                    </tr>

                    <{if $data.bOldStoreID > 0}>
                    <tr>
                        <th>援前仲介店編號</th>
                        <td>
                            <input type="text" value="<{$data.bOldStoreCode}>" name='bOldStoreID' maxlength="7">
                        </td>
                       
                        <td colspan="4"></td>
                    </tr>
                    <{/if}>

                    <{else}>
                    <tr>
                         <th>業務已簽約仲介店︰</th>
                        <td colspan="5">
                            <{html_checkboxes name='bContractStatus' options=$menu_cstatus selected=$signData.bContractStatus separator=' ' disabled=disabled}>，簽約日期：
                            <input type="text" name="bContractStatusTime" value="<{$signData.sSignDate}>"  readonly style="width:100px;">
                            <span id="signSalesblock">簽約業務(未選會帶預設業務)：
                                
                                <{foreach from=$signSales key=key item=item}>
                                <span id="sign<{$key}>" class="signSales" >
                                    <span id="signName"><{$item}></span> 
                                    <input type="hidden" name="signSalseID[]" value="<{$key}>">
                                </span>
                                <{/foreach}>
                            </span>
                        </td>
                    </tr>

                    <{if $data.bOldStoreID > 0}>
                    <tr>
                        <th>援前仲介店編號</th>
                        <td>
                            <input type="text" value="<{$data.bOldStoreCode}>" name='bOldStoreID' maxlength="7" disabled="disabled">
                        </td>
                       
                        <td colspan="4"></td>
                    </tr>
                    <{/if}>
                    <{/if}>

                    <tr>
                        <th>績效分數業務︰</th>
                        <td colspan="5">
                            <span style="margin-left:10px;">
                                <{$performanceSales}>
                                <{if $smarty.session.pBusinessOwnership == 1}>
                                <a href="/sales/salesPerformanceArea.php#tabs-realty" target="_blank">(編輯)</a>
                                <{/if}>
                            </span>
                        </td>
					</tr>

                    <{if $smarty.session.member_id|in_array:[1, 3, 6, 48]}>
                        <{assign var="weighted_disable" value=""}>
                    <{else}>
                        <{assign var="weighted_disable" value="disabled"}>
                    <{/if}>
                    <tr id="channel-weight">
                        <th>通路等級︰</th>
						<td>
                            <{if $weighted_disable == 'disabled'}>
							    <{html_options name=bSalesLevel options=$channel_menu selected=$data.bSalesLevel disabled="disabled"}>
                            <{else}>
                                <{html_options name=bSalesLevel options=$channel_menu selected=$data.bSalesLevel}>
                            <{/if}>
                            級通路
                        </td>
                        <th>加權分數︰</th>
                        <td colspan="3">
							加分：<input type="text" style="width: 24px;margin: 5px;text-align: center;" name="bSalesWeightAdding" value="<{$data.bSalesWeightAdding}>" <{$weighted_disable}>>分<br>
							扣分：<input type="text" style="width: 24px;margin: 5px;text-align: center;" name="bSalesWeightMinus" value="<{$data.bSalesWeightMinus}>" <{$weighted_disable}>>分
                        </td>
					</tr>
					
                    <tr>
                        <th>總店/單店︰</th>
						<td>
							<label for="bStoreClass1">
								<input type="radio" id="bStoreClass1" onclick="show_hide_branch()" name="bStoreClass"<{if $data.bStoreClass == 1 }> checked<{/if}> value="1">總店　
							</label>
							<label for="bStoreClass2">
								<input type="radio" id="bStoreClass2" onclick="show_hide_branch()" name="bStoreClass"<{if $data.bStoreClass != 1 }> checked<{/if}> value="2">單店
							</label>							
							<br><br>
							<span style="font-size:9pt;">(如選擇總店，請務必設定分店)</span>
                        </td>
                        <th>分店︰</th>
                        <td colspan="3">
							<input type="text" id="bClassBranch" style="width:300px;display:<{if $data.bStoreClass!=1}>none<{/if}>;" name="bClassBranch" value="<{$data.bClassBranch}>" />
							<a href="get_branch.php" id="get_branch" style="display:<{if $data.bStoreClass!=1}>none<{/if}>;" class="small_font ajax">選擇</a>
							<br>
							<span style="font-size:9pt;">(可自行輸入店頭編號或搜尋選擇分店；中間區隔以";"為主)</span>
                        </td>
					</tr>
                    <tr>
                        <th>同店設定</th>
                        <td colspan="5">
                            <input type="text" name="sameStore" id="" value="<{$data.bSameStore}>" style="width:400px"><span style="font-size:9pt;">(同店不同法人，自行輸入店編號中間區隔以";"為主)</span>
                        </td>
                    </tr>   
                    <tr>
                        <th>前台顯示設定︰</th>
                        <td>
							<label for="bAccDetail">
								<input type="checkbox" id="bAccDetail" name="bAccDetail"<{if $data.bAccDetail == 1 || $data.bAccDetail == ''}> checked<{/if}> value="1">帳務明細查詢
							</label>
                        </td>
                        <th>前台顯示設定︰</th>
                        <td >
							<label for="bCaseDetail">
								<input type="checkbox" id="bCaseDetail" name="bCaseDetail"<{if $data.bCaseDetail == 1 || $data.bCaseDetail == ''}> checked<{/if}> value="1">案件明細查詢
							</label>
                        </td>
                        <{if $data.bStoreClass == 1 }>
                        <th>機器人顯示分店︰</th>
                        <td>
                            <input type="checkbox" name="bBot" <{if $data.bBot == 1 }>checked<{/if}> value="1">
                        </td>
                        <{else}>
                        <td colspan="2"><input type="hidden" name="bBot" value="<{$data.bBot}>"></td>
                        <{/if}>
                    </tr>
                    <tr>
                        <th>店東︰</th>
                        <td>
                            <input type="text" name="bManager" maxlength="10" class="input-text-per" value="<{$data.bManager}>"  />
                        </td>
                        <th>聯絡電話︰</th>
                        <td>
                            <input type="text" name="bTelArea" maxlength="4" class="input-text-sml" value="<{$data.bTelArea}>" style="width: 20%"/> -
                            <input type="text" name="bTelMain" maxlength="10" class="input-text-mid" value="<{$data.bTelMain}>" />
                        </td>
                        <th>傳真號碼︰</th>
                        <td>
                            <input type="text" name="bFaxArea" maxlength="4" class="input-text-sml" value="<{$data.bFaxArea}>" /> -
                            <input type="text" name="bFaxMain" maxlength="10" class="input-text-mid" value="<{$data.bFaxMain}>" />
                            <input type="checkbox" id="faxDefault" name="faxDefault[]" <{if $data.bFaxDefault == 1}> checked<{/if}> value="1">不帶入出款傳真欄位
                        </td>
                    </tr>
                    <tr>
                        <th>行動電話︰</th>
                        <td><input type="text" name="bMobileNum" maxlength="14" class="input-text-per" value="<{$data.bMobileNum}>" /> </td>
                        <th>電子郵件︰</th>
                        <td><input type="text" name="bEmail" maxlength="255" class="input-text-per" value="<{$data.bEmail}>" /></td>
                        <th>負責人︰</th>
                        <td><input type="text" name="bPrincipal"  value="<{$data.bPrincipal}>"></td>
                    </tr>
                    <tr>
                        <th><span style='color:#FF0000;'>*</span>公司地址︰</th>
                        <td colspan="4" class="<{$address_disabled}>">
                            <input type="hidden" name="zip" id="zip" value="<{$data.bZip}>" />
                            <input type="text" maxlength="6" name="zipF" id="zipF" class="input-text-sml text-center" readonly="readonly" value="<{$data.bZip|substr:0:3}>" />
                            <select class="input-text-big" name="country" id="country" onchange="getArea('country','area','zip')">
                                <{$listCity}>
                            </select>
							<span id="areaR">
                            <select class="input-text-big" name="area" id="area" onchange="getZip('area','zip')">
                                <{$listArea}>
                            </select>
							</span>
                            <input style="width:330px;" name="addr" value="<{$data.bAddress}>" />
                        </td>
						<td>
							<{if $data.bAddress != ''}>
							<!-- <a href="../others/maps.php?zips=<{$data.bZip}>&addr=<{$data.bAddress}>" style="font-size:10pt;" class="iframe">查看地圖</a>
                            &nbsp;&nbsp;|&nbsp;&nbsp; -->
                            <a href="http://www.first1.com.tw/includes/mapsTgos.php?zips=<{$data.bZip}>&addr=<{$data.bAddress}>" style="font-size:10pt;" class="iframe">查看地圖</a>
							<{/if}>
						</td>
                    </tr>
                    <tr>
                        <th>服務費先行撥付同意書︰</th>
                        <td >
                            <{html_checkboxes name='bServiceOrderHas' options=$menu_bServiceOrderHas selected=$data.bServiceOrderHas separator=' '}>
                        </td>
                        <th>合作契約書︰</th>
                        <td ><{html_checkboxes name='bCooperationHas' options=$menu_bServiceOrderHas selected=$data.bCooperationHas separator=' '}></td>
                       
                    </tr>
                    <tr>
                         <th>繳回文件</th>
                        <td colspan="6">
                            <{html_checkboxes name='bBackDocument' options=$menu_BackDocument selected=$data.bBackDocument separator=' '}>
                            <!-- 備註: <input type="text" name="bBackDocumentNote" id="bBackDocumentNote" value="<{$data.bBackDocumentNote}>"> -->
                        </td>
                    </tr>
                    <tr>
                        <th>繳回文件備註:</th>
                        <td colspan="6"><textarea name="bBackDocumentNote" class="input-text-per"><{$data.bBackDocumentNote}></textarea></td>
                    </tr>
                    <tr class='tks'>
                        <td colspan="7" class="tb-title" >
                            本票資料
                            
                        </td>
                    </tr>
                    <tr class='tks'>
                        <th>本票同意書︰</th>
                        <td>
                            <{html_checkboxes name='bCashierOrderHas' options=$menu_cashierorderhas selected=$data.bCashierOrderHas separator=' '}>
                            
                        </td>
                        <th>本票票號︰</th>
                        <td>
                            <input type="text" name="bCashierOrderNumber" maxlength="20" class="input-text-per" value="<{$data.bCashierOrderNumber}>" /> 
                        </td>
                        <th>本票金額︰</th>
                        <td>
                            <input type="text" name="bCashierOrderMoney" size="13" class="text-right" value="<{$data.bCashierOrderMoney}>" />
                        </td>
                    </tr>
                    <tr class='tks'>
                        <th>收票承辦人︰</th>
                        <td>
                            <{html_options name=bCashierOrderPpl options=$menu_ppl selected=$data.bCashierOrderPpl}>
                        </td>
                        <th>發票(法人)︰</th>
                        <td>
                            <input type="text" name="bInvoice1" maxlength="255" class="input-text-per" value="<{$data.bInvoice1}>" />
                        </td>
                        <th>發票(自然人)︰</th>
                        <td>
                            <input type="text" name="bInvoice2" maxlength="255" class="input-text-per" value="<{$data.bInvoice2}>" />
                        </td>
                    </tr>
                    <tr class='tks'>
                        <th>開票日期︰</th>
                        <td>
                            <input type="text" name="bCashierOrderDate" class="datepickerROC" maxlength="15" class="calender input-text-big" value="<{$data.bCashierOrderDate}>"  />
                        </td>
                        <th>確核交存日期︰</th>
                        <td>
                           <input type="text" name="bCashierOrderSave" class="datepickerROC" maxlength="15" class="calender input-text-big" value="<{$data.bCashierOrderSave}>"  />
                        </td>
                        <th>待歸還本票︰</th>
                        <td>  <input type="text" name="bReTicket" id="" value="<{$data.bReTicket}>">
                        </td>
                    </tr>
                    <tr class='tks'>
                        <th>本票備註︰</th>
                        <td colspan="5"><input type="text" name="bCashierOrderRemark" maxlength="255" class="input-text-per" value="<{$data.bCashierOrderRemark}>" /></td>
                    </tr>
                    <tr class='tks'>
                        <th colspan="6">&nbsp;</th>
                    </tr>
                   <tr>
                        <td colspan="6" class="tb-title">
                            備註說明
                        </td>
                    </tr>
                   
                    <tr>
                        <td colspan="6" id="memo" class="tb">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" > 
                                <tr>
                                    <!-- <td width="5%" align="center" class="tb-title">序號</td> -->
                                    <td width="12%" align="left" class="tb-title">建立時間</td>
                                    <td width="10%" align="left" class="tb-title">建立者</td>
                                    <td width="60%"align="left" class="tb-title">內容</td>
                                    <td width="8%"align="left" class="tb-title">狀態</td>
                                    <td width="10%"align="center" class="tb-title">功能</td>
                                </tr>
                                <{foreach from=$data_note key=key item=item}>
                                <tr>
                                    <!-- <td align="center"><{$item.no}></td> -->
                                    <td><{$item.bCreatTime}></td>
                                    <td align="center"><{$item.bCreator}></td>
                                    <td><{$item.bNote}></td>
                                    <td align="center"><a href="javascript:void(0)" onclick="setCashierOrderMemo('status',<{$item.bId}>)"><{$item.bStatusName}></a></td>
                                    <td align="center"><a href="javascript:void(0)" onclick="setCashierOrderMemo('del',<{$item.bId}>)">刪除</a></td>
                                </tr>
                                <{/foreach}>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th>備註:</th>
                        <td colspan="5">
                            <!-- <input type="text" name="bCashierOrderMemo" style="width:90%"> -->
                            <textarea name="bCashierOrderMemo" style="width:90%"></textarea>    
                            <div style="float:right;padding-right:10px;padding-top: 10px;">
                                <input type="button" value="新增" onclick="setCashierOrderMemo('add','')">
                            </div>
                            
                        </td>
                    </tr>
                   <!--  <tr>
                        <th>備註說明︰</th>
                        <td colspan="5">
                            <textarea name="bCashierOrderMemo" class="input-text-per"><{$data.bCashierOrderMemo}></textarea>
                        </td>
                    </tr> -->
                     <!-- <tr>
                        <th>可用系統︰</th>
                        <td>
                            <{html_checkboxes name='bSystem' options=$menu_categorybank_twhg selected=$data.bSystem separator=' <br /> '}>
                        </td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                       
                        <th>保證費率︰</th>
                        <td>
                            <input type="text" name="bCertified" maxlength="15" class="input-text-big" value="<{$data.bCertified}>" />
                        </td>
                    </tr> -->
                   
                    <tr>
                        <td colspan="6" class="tb-title">
                            指定解匯帳戶
                            <div style="float:right;padding-right:10px;">
                                <{if $accdis_disabled == 0}>
                                <a href="#" onclick="addBank()">增加</a>  
                                <input type="hidden" name="bank_count" value="<{$bankcount}>">
                                <{/if}>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>總行(1)︰</th>
                        <td class="bank-acc" width="30%">
                            <{html_options name=bAccountNum1 options=$menu_bank selected=$data.bAccountNum1 class="acc_disabled accdis_disabled"}>
                        </td>
                        <th>分行(1)︰</th>
                        <td  class="bank-acc" width="30%">
                            <select name="bAccountNum2" class="input-text-per acc_disabled accdis_disabled">
							<{$menu_branch}>
                            </select>
                        </td>
                        <th>指定帳號(1)︰</th>
                        <td>
                            <input type="text" name="bAccount3" maxlength="14" class="input-text-per acc_disabled accdis_disabled" value="<{$data.bAccount3}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(1)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount4" class="input-text-per acc_disabled accdis_disabled" value="<{$data.bAccount4}>" />
                        </td>
                        <td></td>
                        <th>停用(1)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused" options=$menu_accunused selected=$data.bAccountUnused class="accdis_disabled"}>
						</td>
                    </tr>
                    <tr>
                        <th>總行(2)︰</th>
                        <td class="bank-acc">
                            <{html_options name=bAccountNum11 options=$menu_bank selected=$data.bAccountNum11 class="acc_disabled1 accdis_disabled"}>
                        </td>
                        <th>分行(2)︰</th>
                        <td class="bank-acc">
                            <select name="bAccountNum21" class="input-text-per acc_disabled1 accdis_disabled">
							<{$menu_branch21}>
                            </select>
                        </td>
                        <th>指定帳號(2)︰</th>
                        <td>
                            <input type="text" name="bAccount31" maxlength="14" class="input-text-per acc_disabled1 accdis_disabled" value="<{$data.bAccount31}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(2)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount41" class="input-text-per acc_disabled1 accdis_disabled " value="<{$data.bAccount41}>" />
                        </td>
                        <td></td>
                        <th>停用(2)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused1" options=$menu_accunused selected=$data.bAccountUnused1 class="accdis_disabled"}>
						</td>
                    </tr>
                     <tr>
                        <th>總行(3)︰</th>
                        <td class="bank-acc">
                            <{html_options name=bAccountNum12 options=$menu_bank selected=$data.bAccountNum12 class="acc_disabled2 accdis_disabled"}>
                        </td>
                        <th>分行(3)︰</th>
                        <td class="bank-acc">
                            <select name="bAccountNum22" class="input-text-per acc_disabled2 accdis_disabled">
                            <{$menu_branch22}>
                            </select>
                        </td>
                        <th>指定帳號(3)︰</th>
                        <td>
                            <input type="text" name="bAccount32" maxlength="14" class="input-text-per acc_disabled2 accdis_disabled" value="<{$data.bAccount32}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(3)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount42" class="input-text-per acc_disabled2 accdis_disabled" value="<{$data.bAccount42}>" />
                        </td>
                        <td></td>
                         <th>停用(3)</th>
                        <td>
							<{html_checkboxes name="bAccountUnused2" options=$menu_accunused selected=$data.bAccountUnused2 class="accdis_disabled"}>
						</td>
                    </tr>

                    <tr>
                        <th>總行(4)︰</th>
                        <td class="bank-acc">
                            <{html_options name=bAccountNum13 options=$menu_bank selected=$data.bAccountNum13 class="acc_disabled3 accdis_disabled"}>
                        </td>
                        <th>分行(4)︰</th>
                        <td class="bank-acc">
                            <select name="bAccountNum23" class="input-text-per acc_disabled3 accdis_disabled">
                            <{$menu_branch23}>
                            </select>
                        </td>
                        <th>指定帳號(4)︰</th>
                        <td>
                            <input type="text" name="bAccount33" maxlength="14" class="input-text-per acc_disabled3 accdis_disabled" value="<{$data.bAccount33}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(4)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount43" class="input-text-per acc_disabled3 accdis_disabled" value="<{$data.bAccount43}>" />
                        </td>
                        <td></td>
                         <th>停用(4)</th>
                        <td>
                            <{html_checkboxes name="bAccountUnused3" options=$menu_accunused selected=$data.bAccountUnused3 class="accdis_disabled"}>
                        </td>
                    </tr>
                   
                    <{foreach from=$dataBank key=key item=item}>
                    <tr class="transBank<{$item.bId}>">
                        <th>總行(<{$item.no}>)︰<input type="hidden" name="bAccountId14[]" value="<{$item.bId}>"></th>
                        <td class="bank-acc">
                            <{if $item.bUnUsed == 1}>
                                <{html_options name="bAccountNum14[]" id="bAccountNum14<{$item.bId}>" options=$menu_bank selected=$item.bBankMain class="accdis_disabled" onchange="Bankchange('bAccountNum14<{$item.bId}>','bAccountNum24<{$item.bId}>')" disabled="disabled"}>
                            <{else}>
                               <{html_options name="bAccountNum14[]" id="bAccountNum14<{$item.bId}>" options=$menu_bank selected=$item.bBankMain class="accdis_disabled" onchange="Bankchange('bAccountNum14<{$item.bId}>','bAccountNum24<{$item.bId}>')" }>
                            <{/if}>
                        </td>
                        <th>分行(<{$item.no}>)︰</th>
                        <td class="bank-acc">
                            <select name="bAccountNum24[]" id="bAccountNum24<{$item.bId}>" class="input-text-per accdis_disabled" <{$item.disabled}>>
                            <{$item.bankbranch}>
                            </select>
                        </td>
                        <th>指定帳號(<{$item.no}>)︰</th>
                        <td>
                            <input type="text" name="bAccount34[]" id="bAccount34<{$item.bId}>" maxlength="14" class="input-text-per accdis_disabled" value="<{$item.bBankAccountNo}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr class="transBank<{$item.bId}>">
                        <th>戶名(<{$item.no}>)︰</th>
                        <td colspan="2">
                            <input type="text" name="bAccount44[]" id="bAccount44<{$item.bId}>" class="input-text-per accdis_disabled" value="<{$item.bBankAccountName}>" <{$item.disabled}>/>
                        </td>
                        <td></td>
                         <th>停用(<{$item.no}>)</th>
                        <td>
                            <span>
                                <input type="checkbox" name="bAccountUnused4[]" id="bAccountUnused4<{$item.bId}>" value="<{$item.bId}>" <{$item.checked}> class="accdis_disabled"> 是
                            </span>
                            <span style="float:right;">
                                <{if $accdis_disabled == 0}>
                                <a href="Javascript:void(0);" style="font-size:0.8em" onclick="bankDelete('<{$item.bId}>')">刪除紀錄</a>
                                <{/if}>
                            </span>
                        </td>
                    </tr>
                    <{/foreach}>
                    <tr id="copyBank">
                        <td colspan="6"><hr></td>
                    </tr>
                    

					<{if $is_edit == '1' }>
                    <tr>
                        <td colspan="6" class="tb-title">
                            簡訊發送對象<div style="float:right;padding-right:10px;">
                                <{if $sms_target == 'distable' }>

                                <{else}>
                                    <a href="formbranchsms.php?bId=<{$data.bId}>" class="iframe" style="font-size:9pt;">編修簡訊對象</a>
                                <{/if}>

							</div>
                        </td>
                    </tr>
                    <{foreach from=$data_sms key=key item=item}>
                    <tr>
                        <th>職稱︰</th>
                        <td>
                            <input type="text" class="input-text-mid" value="<{$item.tTitle}>" disabled='disabled'>
							<input type="checkbox" value="<{$item.bMobile}>"<{$item.defaultSms}> disabled='disabled'>
                        </td>
                        <th>姓名︰</th>
                        <td>
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.bName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.bMobile}>" disabled='disabled'>
                        </td>
                    </tr>
                    <{/foreach}>
					<{/if}>
                    <tr>
                        <td colspan="6" class="tb-title">店家簡訊固定文字</td>
                        
                    </tr>
                    <tr>
                        <td colspan="6">
                            <textarea name="smsText" class="input-text-per"><{$data.bSmsText}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>服務費簡訊樣式</th>
                        <td colspan="5"><{html_options name="smsStyle" options=$menu_smsStyle selected=$data.bSmsTextStyle}></td>
                    </tr>
                    <tr>
                        <th colspan="6">&nbsp;</th>
                    </tr>
                     <tr>

                        <th>建立時間：</th>
                        <td><{$data.bCreat_time}></td>
                        <th >最後修改人：</th>
                        <td><{$data.bEditor}></td>
                        <th >修改時間：</th>
                        <td><{$data.bModify_time}></td>
                          
                    </tr>
             </table>
                </form>
                <div  style="background-color:snow">
                    <center>
                        <br/>
                            <{if $is_edit == 1}>
                            <button class="save">儲存</button>
                            <{else}>
                            <button class="add">儲存</button>
                            <!-- <button class="add" onclick="addNew()">儲存</button> -->
                            <{/if}>
                            <input type="button" id="btnPrint" onclick="printpage()" value="列印" />
                            <{if $is_edit == 1 && $smarty.session.member_id == 6 || $smarty.session.member_id == 1 || $smarty.session.member_id == 39 || $smarty.session.member_id == 48}>
                            <input type="button" id="nSave" value="另存新檔" onclick="NewSave()">
                            <{/if}>
                    </center>
                </div>
                 
            </div>
            <div id="tabs-feedback">
                <input type="hidden" name="change_feedbackData">
                <{if $smarty.session.member_pDep == '9' || $smarty.session.member_pDep =='10' || $smarty.session.member_id == '6' || $smarty.session.member_pDep == '5' || $smarty.session.member_pDep == '4' || $smarty.session.member_pDep == '7' || $smarty.session.member_pDep == '12' || $smarty.session.member_pCaseFeedBackModify != 0}>
                <div id="check_changeFeedBackData">
                <table border="0" width="100%" class="feed">
                    <tr>
                        <td colspan="6" class="tb-title">
                            回饋金對象資料
                            <input type="button" value="匯入資料" onclick="copyFeedData(<{$data.bId}>)">
                            <{if $is_edit == 1}>
                            <input type="button" value="帶入維護頁籤資料" onclick="copyFeedData2(<{$data.bId}>)">
                            <{/if}>
                        </td>
                    </tr>
                </table>
                <{foreach from=$data_feedData key=key item=item}>
             
                <table border="0" width="100%" class="distable">
                    <tr>
                        <td colspan="5" class="tb-title">
                            <input type="checkbox" name="feedBackStop[]" id="" value="<{$item.fId}>" <{$item.stop}>>停用
                        </td>
                        <td colspan="1" class="tb-title" style="text-align: left;">
                            <{if $disable_account == false}>
                            <a href="#" onclick="delFeedBack('<{$item.fId}>')">刪除</a>
                            <{/if}>
                        </td>
                    </tr>
                    <tr>
                        <th width="10%">回饋方式︰<input type="hidden" name="fId[]" value="<{$item.fId}>"></th>
                        <td width="20%">
                            <{if $item.fStop == 1}>
                            <{html_options name="fFeedBack[]" options=$menu_categoryrecall selected=$item.fFeedBack disabled="disabled" }>
                            <{else}>
                            <{html_options name="fFeedBack[]" options=$menu_categoryrecall selected=$item.fFeedBack }>
                            <{/if}>
                            
                        </td>
                        <th>姓名/抬頭︰</th>
                        <td width="20%">
                            <input type="text" name="fTitle[]" class="input-text-big" value="<{$item.fTitle}>" <{$item.disabled}>/>
                        </td>
                        <th>店長行動電話︰</th>
                        <td><input type="text" name="fMobileNum[]" maxlength="10" class="input-text-big" value="<{$item.fMobileNum}>" <{$item.disabled}>/></td>
                    </tr>
                    <tr>
                        <th>身份別︰</th>
                        <td>
                            <{if $item.fStop == 1}>
                            <{html_options name="fIdentity[]" options=$menu_categoryidentify selected=$item.fIdentity disabled="disabled"}>
                            <{else}>
                            <{html_options name="fIdentity[]" options=$menu_categoryidentify selected=$item.fIdentity}>
                            <{/if}>
                            
                        </td>
                        <th>證件號碼︰</th>
                        <td>
                            <input type="text" name="fIdentityNumber[]" class="input-text-big" value="<{$item.fIdentityNumber}>" onkeyUp = "checkID(<{$item.no}>,this.value,'')" <{$item.disabled}>/>
                            <span id="fId<{$item.no}>"></span>
                        </td>
                        <th>收件人稱謂︰</th>
                        <td><input type="text" name="fRtitle[]" value="<{$item.fRtitle}>" <{$item.disabled}>></td>
                    </tr>
                    <tr>
                        <th>回饋報表收件地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="fZipC[]" id="zipC<{$item.no}>" value="<{$item.fZipC}>" />
                            <input type="text" maxlength="6" name="zipC<{$item.no}>F" id="zipC<{$item.no}>F" class="input-text-sml text-center" readonly="readonly" value="<{$item.fZipC}>" <{$item.disabled}>/>
                            <select class="input-text-big" name="countryC<{$item.no}>" id="countryC<{$item.no}>" onchange="getArea2('countryC<{$item.no}>','areaC<{$item.no}>','zipC<{$item.no}>')" <{$item.disabled}>>
                                <{$item.countryC}>
                            </select>

                            <span id="areaC<{$item.no}>R">
                            <select class="input-text-big" name="areaC<{$item.no}>" id="areaC<{$item.no}>" onchange="getZip2('areaC<{$item.no}>','zipC<{$item.no}>')" <{$item.disabled}>>
                                <{$item.areaC}>
                            </select>
                            </span>
                            <input style="width:500px;" name="fAddrC[]" value="<{$item.fAddrC}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr>
                        <th>戶籍地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="fZipR[]" id="zipR<{$item.no}>" value="<{$item.fZipR}>" />
                            <input type="text" maxlength="6" name="zipR<{$item.no}>F" id="zipR<{$item.no}>F" class="input-text-sml text-center" readonly="readonly" value="<{$item.fZipR}>" />
                            <select class="input-text-big" name="countryR<{$item.no}>" id="countryR<{$item.no}>" onchange="getArea2('countryR<{$item.no}>','areaR<{$item.no}>','zipR<{$item.no}>')" <{$item.disabled}>>
                               <{$item.countryR}>
                            </select>
                            <span id="areaR<{$item.no}>R">
                            <select class="input-text-big" name="areaR<{$item.no}>" id="areaR<{$item.no}>" onchange="getZip2('areaR<{$item.no}>','zipR<{$item.no}>')" <{$item.disabled}>>
                               <{$item.areaR}>
                            </select>
                            </span>
                            <input style="width:500px;" name="fAddrR[]" value="<{$item.fAddrR}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr>
                        <th>電子郵件︰</th>
                        <td colspan="3">
                            <input type="text" name="fEmail[]" maxlength="255" class="input-text-per" value="<{$item.fEmail}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr>
                        <th>總行︰</th>
                        <{if $item.fStop == 1}>
                        <td>
                            <{html_options name="fAccountNum[]" id="fAccountNum<{$item.no}>" options=$menu_bank selected=$item.fAccountNum onchange="Bankchange('fAccountNum<{$item.no}>','fAccountNumB<{$item.no}>')" style="width:250px;" disabled="disabled"}>
                        </td>
                        
                        <{else}>
                        <td class="bank">

                            <{html_options name="fAccountNum[]" id="fAccountNum<{$item.no}>" options=$menu_bank selected=$item.fAccountNum onchange="Bankchange('fAccountNum<{$item.no}>','fAccountNumB<{$item.no}>')" style="width:250px;"}>


                        </td>
                        <{/if}>
                        
                        <th>分行︰</th>
                         <{if $item.fStop == 1}>
                        <td>
                            <select name="fAccountNumB[]" id="fAccountNumB<{$item.no}>" class="input-text-per" <{$item.disabled}>>
                            <{$item.bank_branch}>
                            </select>
                        </td>
                        
                        <{else}>
                         <td class="bank">
                            <select name="fAccountNumB[]" id="fAccountNumB<{$item.no}>" class="input-text-per" >
                            <{$item.bank_branch}>
                            </select>
                        </td>
                        <{/if}>

                       
                        <th>指定帳號︰</th>
                        <td>
                            <input type="text" name="fAccount[]" maxlength="14" class="input-text-per" value="<{$item.fAccount}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr>
                        <th>戶名︰</th>
                        <td>
                            <input type="text" name="fAccountName[]"  class="input-text-per" value="<{$item.fAccountName}>" <{$item.disabled}>/>
                        </td>
                        <th>發票種類︰</th>
                        <!-- <td><input type="text" name="fNote[]" id="" value="<{$item.fNote}>"></td> -->
                        <td>
                            <{if $item.fStop == 1}>
                            <{html_options name="fNote[]" options=$menu_note selected=$item.fNote disabled="disabled"}>
                            <{else}>
                            <{html_options name="fNote[]" options=$menu_note selected=$item.fNote }>
                            <{/if}>

                            
                                
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                 
                </table>
                <{/foreach}>

                 <table border="0" width="100%" class="distable">
                    <tr>
                        <th colspan="6">

                             <div style="float:right;padding-right:10px;">
                                 <{if $disable_account == false}>
                                <a href="#" onclick="addFeedBack()" id="cl">增加對象</a>
                                 <{/if}>
                                 <!-- <input type="hidden" name="feedback_count" value="0">    -->
                                 <input type="hidden" name="data_feedback_count" value="<{$data_feedData_count}>">   
                            </div>

                        </th>
                    </tr>
                </table>
                <table border="0" width="100%" class="newrow distable distable2">
                    
                    <tr>
                        <th width="10%">回饋方式︰</th>
                        <td width="20%">
                            <{html_options name="newFeedBack[]" options=$menu_categoryrecall}>
                           
                        </td>
                        <th>姓名/抬頭︰</th>
                        <td width="20%">
                            <input type="text" name="newTtitle[]" class="input-text-big"  />
                        </td>
                        <th >店長行動電話︰</th>
                        <td><input type="text" name="newMobileNum[]" maxlength="10" class="input-text-big" /></td>
                    </tr>
                    <tr>
                        <th>身份別︰</th>
                        <td>
                            <{html_options name="newIdentity[]" options=$menu_categoryidentify}>
                        </td>
                        <th>證件號碼︰</th>
                        <td>
                            <input type="text" name="newIdentityNumber[]" class="newIdentityNumber"  class="input-text-big" onkeyup="checkID('',this.value,'new')" />
                            <span id="newfId"></span>
                        </td>
                        <th>收件人稱謂︰</th>
                        <td><input type="text" name="newRtitle[]" ></td>
                    </tr>
                    <tr>
                        <th>回饋報表收件地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="newzipC[]" id="newzipC" />
                            <input type="text" maxlength="6" name="newzipCF" id="newzipCF"  class="input-text-sml text-center" readonly="readonly"/>
                            <select class="input-text-big" name="newcountryC" id="newcountryC"  onchange="getArea2('newcountryC','newareaC','newzipC')">
                                <{$FeedCity}>
                            </select>
                            <span id="newareaCR">
                            <select class="input-text-big" name="newareaC" id="newareaC" onchange="getZip2('newareaC','newzipC')">
                                
                            </select>
                            </span>
                            <input style="width:500px;" name="newaddrC[]"  />
                        </td>
                    </tr>
                    <tr>
                        <th>戶籍地址︰</th>
                        <td colspan="5">
                            <input type="hidden" name="newzipR[]" id="newzipR" />
                            <input type="text" maxlength="6" name="newzipRF" id="newzipRF" class="input-text-sml text-center" readonly="readonly"/>
                            <select class="input-text-big" name="newcountryR" id="newcountryR" onchange="getArea2('newcountryR','newareaR','newzipR')">
                                <{$FeedCity}>
                            </select>
                            <span id="newareaRR">
                            <select class="input-text-big" name="newareaR" id="newareaR" onchange="getZip2('newareaR','newzipR')">
                                
                            </select>
                            </span>
                            <input style="width:500px;" name="newaddrR[]"/>
                        </td>
                    </tr>
                    <tr>
                        <th>電子郵件︰</th>
                        <td colspan="3">
                            <input type="text" name="newEmail[]" maxlength="255" class="input-text-per"/>
                        </td>
                    </tr>
                    <tr>
                        <th>總行︰</th>
                        <td class="bank">
                            <{html_options name="newAccountNum[]" id="newAccountNum" options=$menu_bank onchange="Bankchange('newAccountNum','newAccountNumB')" style="width:250px;"}>
                        </td>
                        <th>分行︰</th>
                        <td class="bank">
                            <select name="newAccountNumB[]" id="newAccountNumB" class="input-text-per">
                            
                            </select>
                        </td>
                        <th>指定帳號︰</th>
                        <td>
                            <input type="text" name="newAccount[]" maxlength="14" class="input-text-per" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名︰</th>
                        <td>
                            <input type="text" name="newAccountName[]"  class="input-text-per"/>
                        </td>
                        <th>發票種類︰</th>
                        <td><{html_options name="newNote[]" options=$menu_note}></td>
                        <td></td>
                        <td></td>
                    </tr>
                   <tr>
                        <td colspan="6" class="tb-title">&nbsp;</td>
                    </tr>
                </table>
                 </div>
                <{/if}>
               
                <table border="0" width="100%">
                     <tr>
                        <td colspan="6" class="tb-title">
                            回饋金通知簡訊對象<div style="float:right;padding-right:10px;">
                                <{if $sms_target == 'distable' }>

                                <{else}>
                                    <a href="formfeedback.php?storeId=<{$data.bId}>&cat=2" class="iframe" style="font-size:9pt;">編修簡訊對象</a>
                                <{/if}>

                        </td>
                    </tr>
                    <{foreach from=$data_feedsmsNotify key=key item=item}>
                    <tr>
                         <th>職稱︰</th>
                        <td>
                            <input type="text" class="input-text-mid" value="<{$item.fTitle}>" disabled='disabled'>
                               
                        </td>
                        <th>姓名︰</th>
                        <td>
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.fName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.fMobile}>" disabled='disabled'>
                        </td>
                    </tr>
                    <{/foreach}>        
                    <tr>
                        <td colspan="6" class="tb-title">
                            回饋金出款簡訊對象資料<div style="float:right;padding-right:10px;">
                                <{if $sms_target == 'distable' }>

                                <{else}>
                                    <a href="formbranchfeedback.php?bId=<{$data.bId}>" class="iframe" style="font-size:9pt;">編修簡訊對象</a></div>
                                <{/if}>

                        </td>
                    </tr>
                    <{foreach from=$data_feedsms key=key item=item}>
                    <tr>
                        <th>職稱︰</th>
                        <td>
                            <input type="text" class="input-text-mid" value="<{$item.tTitle}>" disabled='disabled'>
                               
                        </td>
                        <th>姓名︰</th>
                        <td>
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.bName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.bMobile}>" disabled='disabled'>
                        </td>
                    </tr>
                    <{/foreach}>                   
                    <tr>
                        <td colspan="6" class="tb-title">
                            季回饋金額
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <{html_options name="FBYear" style="width:100px;" options=$FBYear selected=$FBYearSelect}>
                            年度　
                            第一季
                            <input type="text" name="fbs1" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第二季
                            <input type="text" name="fbs2" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第三季
                            <input type="text" name="fbs3" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                            第四季
                            <input type="text" name="fbs4" class="input-text-per" style="width:120px;" value="" disabled="disabled">
                        </td>
                    </tr>
                     <tr>
                        <th colspan="6">&nbsp;</th>
                    </tr>
                    <tr>

                        <th width="20%">最後修改人(會計)：</th>
                        <td colspan="2" width="30%"><{$data.bEditor_Accounting}></td>
                        <th width="20%">修改時間(會計)：</th>
                        <td colspan="2" width="30%"><{$data.bModify_time_Accounting}></td>
                    </tr>
                    <tr>
                    	<td colspan="6">&nbsp;</td>
                    </tr>
                </table>


                <table border="0" width="100%">
                    <{if $smarty.session.member_pFeedBackModify!='0'}>
                    <tr>
                        <td colspan="6" class="tb-title">
                            回饋金資訊
                        </td>
                    </tr>
                    <tr>
                        <th>回饋日期︰</th>
                        <td>
                            <{html_radios name='feedDateCat' options=$menu_feedDateCat selected=$data.bFeedDateCat}>
                           
                        </td>
                         
                        <th>回饋比率︰</th>
                        <td>
                            <!-- 萬分之 -->
                            百分之
                            <input type="text" name="bRecall" maxlength="5" style="text-align:right;width:50px;" class="input-text-big" value="<{$data.bRecall}>" <{$_disabled}> />
                        </td>
                        <th>代書回饋比率︰</th>
                        <td>
                            百分之
                            <input type="text" name="bScrRecall" maxlength="5" style="text-align:right;width:50px;" class="input-text-big" value="<{$data.bScrRecall}>" <{$_disabled}> />
                        </td>
                    </tr>
                    
                    <tr>
                        <th>個案回饋對象︰</th>
                        <td colspan="3">
                        <{if $data.bBrand != 3 }>
                            <select id="bIndividual_select" style="width:200px;margin-right: 20px;">
                            <{html_options options=$menu_bIndividual}>
                            </select>
                            <button type="button" style="padding: 5px;" onclick="individual('ADD', '')">加入</button>
                            <div id="bIndividual" style="padding: 10px;">
                                <{foreach from=$bIndividuals key=key item=item}>
                                <span style="padding:5px;border: 1px solid;border-radius:5px;font-size:9pt;margin-right:10px;margin-top:5px;">
                                    <{$item}> 
                                    <a href="Javascript:individual('DELETE', <{$key}>)" style="margin-left:2px;font-size:9pt;">X</a>
                                </span>
                                <{/foreach}>
                            </div>
                        <{/if}> 
                        </td>
                        <th>個案回饋比率︰</th>
                        <td>
                            <{if $data.bBrand == 3 }>
                            百分之
                            <input type="text" name="bIndividualRate" maxlength="5" style="text-align:right;width:50px;" class="input-text-big" value="<{$data.bIndividualRate}>" <{$_disabled}> />
                            <{/if}>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>未收足回饋︰</th>
                        <td colspan="5">
                            <{html_checkboxes name='feedbackmoney' options=$menu_cstatus selected=$data.bFeedbackMoney separator=' '}>
                        </td>
                    </tr>
                    <tr>
                        <th>回饋金備註︰</th>
                        <td colspan="5"><textarea name="bRenote" class="input-text-per" <{$_disabled}> ><{$data.bRenote}></textarea></td>
                    </tr>
                    <{if $data.bStoreClass == 1 }>
                    <tr>
                        <th>回饋通知書-分店回饋案件︰</th>
                        <td><{html_radios name=bFeedbackAllCase options=$menu_feedbackType selected=$data.bFeedbackAllCase}></td>
                    </tr>
                    <{else}>
                        <tr>
                            <td colspan="2"><input type="hidden" name="bFeedbackAllCase" value="<{$data.bFeedbackAllCase}>"></td>
                        </tr>
                    <{/if}>
                    <tr>
                        <th>回饋通知書顯示仲介店︰</th>
                        <td><{html_radios name=feedbackMark2 options=$menu_rg selected=$data.bFeedbackMark2}></td>
                    </tr>
                    <tr>
                        <th>案件統計表>回饋金報表(綠底標記)︰</th>
                        <td><{html_radios name=feedbackMark options=$menu_mark selected=$data.bFeedbackMark}></td>
                    </tr>
                    <tr>
                        <th colspan="6">&nbsp;</th>
                    </tr>
                    <{if $AnotherFeedBack.bRecall != ''}>
                     <tr>
                        <th>回饋指定店家︰</th>
                        <td><{$AnotherFeedBack.code}><{$AnotherFeedBack.branch}></td>
                        <th>回饋比率︰</th>
                        <td><{$AnotherFeedBack.bRecall}>%</td>
                        <th>品牌簽約日︰</th>
                        <td><{$AnotherFeedBack.bSignDate}></td>
                    </tr>
                    <{/if}>
                   
                    <{/if}>   
                </table>

                 <div  style="background-color:snow">
                    <center>
                        <br/>
                        <{if $is_edit == 1}>
                        <button class="save">儲存</button>
                        <{else}>
                        <button class="add">儲存</button>
                        <{/if}>
                    </center>
                </div>
            </div>
            <div id="tabs-ai">
                <form name="calform">
                    <div id="sales_feed01" style="width:100%; margin: 0px auto;">
                        <table id="tb001" align="center" style="width:100%;" >
                        <tr>
                            <th width="10%">金流系統</th>
                            <td width="20%">
                                <select name="sales_bank" size="1">
                                    <option value="" selected="selected">全部</option>
                                    <{html_options  options=$menu_categorybank_twhg }>
                                </select>
                            </td>
                            <th width="10%">保證號碼</th>
                            <td width="20%">
                                <input type="text" name="ge_id" size="10" class="keyin2">
                            </td>
                            <td width="20%" rowspan="2" align="center" valign="center"><input type="button" value="查詢" id="search"> </td>
                        </tr>
                        <tr>
                            <th>出款日期(起)</th>
                            <td>
                                <input type="text" name="lander_Sdate" size="10" class="keyin2 datepickerROC">
                            </td>
                            <th>出款日期(迄)</th>
                            <td>
                                <input type="text" name="lander_Edate" size="10" class="keyin2 datepickerROC">
                            </td>
                        </tr>
                        </table>
                    </div>

                    <div id="show"></div>
                </form>
            </div>
            <{if $smarty.session.member_act_report == '1'}>
            <div id="tabs-act">
                
                    <table border="0" width="100%">
                        <tr>
                            <td class="tb-title" colspan="2">2020回饋店東活動</td>
                        </tr>
                        <tr>
                            <th>說明：</th>
                            <td>
                                <div>
                                     <b><font color="blue">辦法一：</font></b><br>
                                    單店每月送計2件(含)，每件<font color="red">400元</font>禮券 <br>
                                    單店每月送計5件(含)，每件<font color="red">600元</font>禮券<br>
                                </div>
                                <div>
                                     <b><font color="blue">辦法二：</font></b><br>
                                    單店每月送計2件(含)，每件<font color="red">600元</font>禮券 <br>
                                    單店每月送計5件(含)，每件<font color="red">700元</font>禮券 <br>
                                </div>
                                <div>
                                     <b><font color="blue">辦法三：</font></b><br>
                                    單店每月送計2件(含)，每件<font color="red">600元</font>禮券 <br>
                                    單店每月送計5件(含)，每件<font color="red">800元</font>禮券 <br>
                                </div>
                                <div>
                                      <b><font color="blue">辦法四：</font></b><br>
                                    自即日起累計至110.01.31止，送件達10件(含)以上，每件<font color="red">700</font>元禮券
                                </div>
                            </td>
                        </tr>
                            <tr>
                                <th>參加辦法：</th>
                                <td><{html_radios name='act2020' class="ra1" options=$menu_act selected=$data.bAct_2020 }></td>
                            </tr>
                            <tr>
                                <th>禮品：</th>
                                <td>
                                    <{html_radios name='gift2020' class="ra1" options=$menu_gift2020 selected=$data.bAct_2020_gift }>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">&nbsp;</th>
                            </tr>
                        <tr>
                            <td class="tb-title" colspan="2">2021回饋店東活動</td>
                        </tr>
                        <tr>
                            <th>說明：</th>
                            <td>
                                <div>110.10.01~12.31</div>
                                <div>
                                     <b><font color="blue">辦法一：</font></b><br>
                                    每件贈送<font color="red">500元</font> <br>
                                    活動期間內累計8件，每件贈送<font color="red">700元</font><br>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>參加辦法：</th>
                            <td><{html_radios name='act2021' class="ra1" options=$menu_act2021 selected=$data.bAct_2021 }></td>
                        </tr>
                        <tr>
                            <th>禮品：</th>
                            <td>
                                <{html_radios name='gift2021' class="ra1" options=$menu_gift2021 selected=$data.bAct_2021_gift }>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">&nbsp;</th>
                        </tr>

                        <{foreach from=$activities key=ka item=va}>

                        <input  type="hidden" name="activities[]" value="<{$va['aId']}>">
                        <tr>
                            <td class="tb-title" colspan="2"><{$va['aYear']}> <{$va['aTitle']}></td>
                        </tr>
                        <tr>
                            <th>說明：</th>
                            <td>
                                <div><{$va['aStartDate']}>~<{$va['aEndDate']}></div>
                                
                                <div>
                                    <{foreach from=$va.Rules key=kb item=vb}>
                                    <b><font color="blue"><{$vb['aTitle']}>：</font></b><br>
                                    <{$vb['aItem']}>
                                    <{/foreach}>
                                </div>
                                
                            </td>
                        </tr>
                        <tr>
                            <th>參加辦法：</th>
                            <td>
                                <label><input type="radio" name="activity_<{$va['aId']}>_rule" value="0" checked="checked" class="ra1">未參加</label>
                                <{foreach from=$va.Rules key=kb item=vb}>
                                    <label><input type="radio" name="activity_<{$va['aId']}>_rule" value="<{$vb['aId']}>" class="ra1" <{$vb['checked']}>><{$vb['aTitle']}></label>
                                <{/foreach}>
                            </td>
                        </tr>

                        <{if $va['aPriority'] == 'Y'}>
                        <tr>
                            <th>優先／特定：</th>
                            <td>
                        <label><input type="checkbox" name="activity_<{$va['aId']}>_priority" value="Y" class="ra1" <{if $va['priority'] == 'Y'}>checked<{/if}>>優先／特定</label>
                            </td>
                        </tr>
                        <{/if}>

                        <tr>
                            <th>禮品：</th>
                            <td>
                                <{foreach from=$va['Gifts'] key=kb item=vb}>
                                    <label><input type="radio" name="activity_<{$va['aId']}>_gift" value="<{$vb['aId']}>" class="ra1" <{$vb['checked']}>><{$vb['aName']}></label>
                                <{/foreach}>
                            </td>
                        </tr>

                        <{if $va['ext']}>
                        <tr>
                            <th><{$va['ext']['title']}>：</th>
                            <td>
                                <input type="hidden" name="activity_<{$va['aId']}>_ext" value="Y">
                                <{foreach from=$va['extRecords'] key=kc item=vc}>
                                    <{$vc}>
                                <{/foreach}>
                            </td>
                        </tr>
                        <{/if}>

                        <tr>
                            <th colspan="2">&nbsp;</th>
                        </tr>
                        
                        <{/foreach}>
                        
                    </table>
                    
                <div  style="background-color:snow">
                    <center>
                        <br/>
                        <{if $is_edit == 1}>
                        <button class="save">儲存</button>
                        <{/if}>
                    </center>
                </div>
                
            </div>
            <{/if}>
        </div>
    
    <form name="form_back" id="form_back" method="POST"  action="listbranch.php">
    </form>
                            </div>
                        </div>
                    </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
            
			<div id="dialog" title="注意!!"></div>
			<div id="dialog-confirm" title="確認!!"></div>
    </body>
</html>










