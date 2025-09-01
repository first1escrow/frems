<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=9"/>
		<link rel="stylesheet" href="colorbox.css" />
		<{include file='meta.inc.tpl'}>
		<script src="js/jquery.colorbox.js"></script>
		<script src="/js/IDCheck.js"></script>
        <script src="/js/lib/comboboxNormal.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {    
                <{if !$smarty.session.member_id|in_array:[1,6,12,48]}>    
                disableContractBank();
                <{/if}>
                switchContractBank();
                setComboboxNormal('bank select','class');
                showScrivenerBranch("<{$data.sScrivenerBranch}>");
                feedDataCatSwitch();
                toggleScrivenerSystemOther(); // 初始化代書系統選項顯示狀態

                var rg = "<{$data.sRg}>";
                var status = "<{$data.sStatus}>";
                var men = "<{$smarty.session.member_id}>";
                var dep = "<{$smarty.session.member_pDep}>";
                var data_feedData_count = parseInt($("[name='data_feedback_count']").val());

                $(".tks").hide();
                if ("<{$ticketShow}>" == 'OK') {
                    $(".tks").show();
                }

                if ("<{$_disabled}>" != '') {
                    $("[name='feedDateCat']").attr('disabled', 'disabled');
                    $("[name='feedbackmoney[]']").attr('disabled', 'disabled');
                }

                if ("<{$signData.sContractStatus}>" == 1) {
                    $("#signSalesblock").show();
                } else {
                    $("#signSalesblock").hide();
                }
                
                $("#status_t").hide();  
                <{if $smarty.session.member_rg == 1}>
                if (rg == 0) {
                    $(".rg_show").hide();
                } else if(rg == 1){
                    setInterval("getRgBalance()",60000) ;
                }

                $("[name='sRg']").on('click',function() {
                    if ($(this).val()==1) {
                        $(".rg_show").show();
                        $("[name='sRgfirst']").val(1);
                    } else {
                        $(".rg_show").hide();
                        $("[name='sRgfirst']").val('');
                    }
                });
               <{/if}>
                
                if (status == 2) {
                    let array = "input,select,textarea";
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                    });
                     
                    $("#save").hide();
                } else if(status == 3) {
                    let array = "input,select,textarea";
                    $("#content").find(array).each(function() {
                        $(this).attr('disabled', true);
                       
                    });

                    $("#status_t").show();
                    $("#save").hide();
                }

                $('[name="sStatus"]').removeAttr('disabled');
                { $disable_account  = false;}
                if (['4', '7', '5', '6', '12'].includes(dep)) { //經辦 業務只可以看
                     var array = "input,select,textarea";
                   { $disable_account  = true}
                   $('.bank select').combobox('disable');
                     $(".distable").find(array).each(function() {
                        if (men != 48 && men != 1) {
                            $(this).attr('disabled', true);
                        }
                    }); 

                    if (dep == 7) {
                        $('[name="sStatus"]').attr('disabled', true);
                    }
                }

                if (data_feedData_count == 0) {
                    $(".distable2").show();
                } else {
                     $(".distable2").hide();
                }

                getFBseasons() ;

				$(".iframe").colorbox({iframe:true, width:"900px", height:"500px"});
                $(".iframe2").colorbox({iframe:true, width:"40%", height:"60%"});
				
				/* 檢核輸入統一編號是否合法 */
				if (checkUID($('[name="sSerialnum"]').val())) {
					$('#suId').html('<img src="/images/ok.png">') ;
				} else {
					$('#suId').html('<img src="/images/ng.png">') ;
				}
				
				$('[name="sSerialnum"]').keyup(function() {
					if (checkUID($('[name="sSerialnum"]').val())) {
						$('#suId').html('<img src="/images/ok.png">') ;
					} else {
						$('#suId').html('<img src="/images/ng.png">') ;
					}
				}) ;
				////

				/* 檢核輸入身分證字號是否合法 */
				if (checkUID($('[name="sIdentifyId"]').val())) {
					$('#ssId').html('<img src="/images/ok.png">') ;
				} else {
					$('#ssId').html('<img src="/images/ng.png">') ;
				}
				
				$('[name="sIdentifyId"]').keyup(function() {
					if (checkUID($('[name="sIdentifyId"]').val())) {
						$('#ssId').html('<img src="/images/ok.png">') ;
					} else {
						$('#ssId').html('<img src="/images/ng.png">') ;
					}
				}) ;

                $("#check_changeFeedBackData").on('keydown', function() {
                    $("[name='change_feedbackData']").val(1);
                });

                $("#check_changeFeedBackData").on('change', function() {
                    $("[name='change_feedbackData']").val(1);
                });
				////
				
                $('#sms').live('click', function () {
                    $("#dialog-confirm11").dialog({
                        resizable: false,
                        height:200,
                        modal: true,
                        buttons: {
                            "編輯": function() {
                                $('#form_sms').submit();
                            },
                            "取消": function() {
                                $( this ).dialog("close");
                            }
                        }
                    });
                });

                $('[name=sAccountNum1]').live('change', function () {
					GetBankBranchList($('[name=sAccountNum1]'),
                                        $('[name=sAccountNum2]'),
                                        null);
                });

                $('[name=sAccountNum11]').live('change', function () {
                    GetBankBranchList($('[name=sAccountNum11]'),
                                        $('[name=sAccountNum21]'),
                                        null);
                });

                $('[name=sAccountNum12]').live('change', function () {
                    GetBankBranchList($('[name=sAccountNum12]'),
                                        $('[name=sAccountNum22]'),
                                        null);
                });
                
                $('#add').on('click', function () {
                    if ($("[name='zip2']").val() == '') {
                        alert('請輸入郵寄地址');
                        return false;
                    }

                    $('#add').hide();//禁止使用者多按

                    save('add');
                });
                
                $('#save').on('click', function () {
                    var status = $('[name="sStatus"]').val();
                    var id = $('#scrivenerId [name="id"]').val();
                    var oCpZip = $("#checkCpZip").val();
                    var oCpAddr = $("#checkCpAddr").val();
                    var nCpZip = $("[name='zip2']").val();
                    var nCpAddr = $("[name='addr2']").val();
                    var reg = /\d{2,3}-\d{1,2}-\d{1,2}$/ ;
                    var txt = $("[name='sBirthday']").val().split('-');
                    var email = $("[name='sEmail']").val();

                    if (email != '') {
                        let filter_mail = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
                        if (!filter_mail.test(email)) {
                            alert('請輸入正確的電子信箱');
                            $("[name='sEmail']").focus();
                            return false;
                        }
                    }

                    if (!reg.test($("[name='sBirthday']").val())) {
                        alert('生日格式有問題，請確認後在存檔');
                        $('#save').show();
                        return false;
                    } else if (txt[1] > 12) {
                        alert('生日格式有問題，請確認後在存檔');
                        $('#save').show();
                        return false;
                    } else if(txt[2] > 31) {
                        alert('生日格式有問題，請確認後在存檔');
                        $('#save').show();
                        return false;
                    }   

                    $('#save').hide();//禁止使用者多按

                   //是隨案&有修改回饋金資料
                   if($("[name='feedDateCat']").val() == 2 && $("[name='change_feedbackData']").val() == 1) {
                       $.ajax({
                           url: '/includes/maintain/checkbanktransrelay.php',
                           type: 'POST',
                           dataType: 'html',
                           data: {'sId': id},
                       })
                       .done(function(txt) {
                           if (txt != 1) {
                               alert('此代書有當日結案案件 如果是雙帳戶請確認'+ txt);
                               return;
                           }
                       });
                   }

                    if ([2, 3].includes(status)) {
                        //確認是否有進行中案件
                        $.ajax({
                            url: '/includes/maintain/checkcontractstaus.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'bId': id,'type':'s'},
                        })
                        .done(function(txt) {
                            if (txt == 1) {
                                alert('有進行中案件，禁止更改狀態');
                                return;
                            } else {
                                save('save');
                            }
                        });
                    } else {
                        save('save');
                    }
                });

                $("#appoint").on('click', function() {
                    let no = "<{$data.sId}>" ;
                    $("#form_edit").attr('action', 'appoint.php');
                    $("#form_edit").attr('target', '_blank');
                    $("[name='id']").val(no);
                    $("#form_edit").submit();
                });

                $('[name="sStatus"]').on('change',function() {
                    let val = $('[name="sStatus"]').val();
                    if (val == 3) {
                        $("#status_t").show();
                    } else if (val == 1){
                        $("#save").show();
                        $("#status_t").hide();
                    } else{ 
                        $("#status_t").hide();
                    }
                });

                $(".iframe").colorbox({
                    iframe:true,
                    width:"900px",
                    height:"500px",
                    onClosed:function() {
                        $('#reloadPage').submit();
                    }
                });
                
                $( "#tabs" ).tabs({
                    selected: 0
                });
                
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

                $("[name='sBrand[]']").live('click', function() {
                    var ck = 0;
                    var cbxVehicle = new Array();

                    $('input:checkbox:checked[name="sBrand[]"]').each(function(i) { 
                        if ([1, 49].includes(this.value)) { //勾選台屋跟優美
                            ck = 1;
                        }
                    });

                    $('input:checkbox[name="sBrand[]"]').each(function(i) {  //如勾選台屋跟優美 非仲介要勾
                        if (this.value == 2 && ck == 1) {
                            $(this).attr('checked', 'checked');
                        }
                    });
                });

                $("[name='sContractStatus[]']").live('click', function() {
                    if ($("[name='sContractStatus[]']").attr('checked')) {
                        $("[name='sContractStatusTime']").val("<{$today}>");
                        $("#signSalesblock").show();
                    } else {
                       $("[name='sContractStatusTime']").val('000-00-00');
                       $("#signSalesblock").hide();
                    }
                });

                $('[name="sStatus"]').on('change',function() {
                    let val = $('[name="sStatus"]').val();
                    let status_now = "<{$data.sStatus}>";
                    
                    $("#status_t").hide();
                    if (val == 3) {
                        $("#status_t").show();
                    }

                    $("#save").hide();
                    if (val != status_now) {
                        $("#save").show();
                    }
                });

                $('[name=sAccountNum5]').live('change', function () {
                    GetBankBranchList($('[name=sAccountNum5]'),
                                        $('[name=sAccountNum6]'),
                                        null);
                });

                $('[name="FBYear"]').change(function() {
                    getFBseasons() ;
                }) ;

                $(".checkBlackListIn").change(function() {
                   $('[name="checkBlackListChange"]').val(1);
                });

                $('[name="sBrand[]"]').click(function () {
                    switchContractBank();
                });

                $('#appoint').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#save').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#add').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#buyer_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#owner_edit').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );

                $('#sms').button( {
                    icons:{
                        primary: "ui-icon-info"
                    }
                } );
            });
            
            function checkBlackList(){
                $.ajax({
                    url: '../includes/maintain/checkScrivenerBlackList.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id:$('#scrivenerId [name="id"]').val(),
                        name: $("[name='sName']").val(),
                        office:$("[name='sOffice']").val(),
                        identifyId:$("[name='sIdentifyId']").val(),
                        zip:$("[name='zip2']").val(),
                        address:$("[name='addr2']").val()
                    },
                }).done(function(obj) {
                    if (obj.code == 201) {
                        alert(obj.msg);
                    }
                });
            }

            function brandForScr(){
                let e = "<{$is_edit}>";
                let sId = "<{$data.sId}>";
                if (e != 1) {
                    alert("請新增完後再編輯");
                    return false;
                };

                let url = "brandForScr.php?sId=<{$data.sId}>";
                $.colorbox({
                    iframe: true, 
                    width: "60%", 
                    height: "90%", 
                    href: url,
                    onClosed: function () {
                        $.ajax({
                            url: '../includes/escrow/getBrandForScr.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {"scrivner":sId,"cat":'brdScr'},
                        })
                        .done(function(txt) {
                            $("#showBrandScr").empty();
                            $("#showBrandScr").html(txt);
                        });
                    }
                }) ;
            }

            function checkID(no,val,type)
            {
                if (checkUID(val)) {
                    $('#'+type+'fId'+no).html('<img src="/images/ok.png">') ;
                } else {
                    $('#'+type+'fId'+no).html('<img src="/images/ng.png">') ;
                }
            }

            // 用於追蹤雙擊檢測
            var scrivenerSystemClickTimer = null;
            var scrivenerSystemLastClicked = null;
            
            function toggleScrivenerSystemOther() {
                var selectedValue = $('input[name="sScrivenerSystem"]:checked').val();
                if (selectedValue === '3') {
                    $('#scrivenerSystemOther').show();
                } else {
                    $('#scrivenerSystemOther').hide();
                    $('input[name="sScrivenerSystemOther"]').val('');
                }
            }

            function handleScrivenerSystemClick(element) {
                // 清除之前的定時器
                if (scrivenerSystemClickTimer) {
                    clearTimeout(scrivenerSystemClickTimer);
                }
                
                // 如果是同一個元素的第二次點擊（雙擊）
                if (scrivenerSystemLastClicked === element && $(element).is(':checked')) {
                    // 取消選取
                    $(element).prop('checked', false);
                    scrivenerSystemLastClicked = null;
                    toggleScrivenerSystemOther();
                    return;
                }
                
                // 第一次點擊，設定定時器
                scrivenerSystemClickTimer = setTimeout(function() {
                    scrivenerSystemLastClicked = null;
                }, 400); // 400ms 內的第二次點擊視為雙擊
                
                scrivenerSystemLastClicked = element;
                
                // 選取當前項目
                $('input[name="sScrivenerSystem"]').prop('checked', false);
                $(element).prop('checked', true);
                
                // 觸發相關功能
                toggleScrivenerSystemOther();
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

            function getArea2(ct, ar, zp) {
                let url = '../escrow/listArea.php' ;
                ct = $('#' + ct + ' :selected').val() ;
                
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
                let zips = $('#' + ar + ' :selected').val() ;

                $('#' + zp + '').val(zips);
                $('#' + zp + 'F').val(zips.substr(0,3));
            }

            function Bankchange(name1,name2){
                GetBankBranchList($('#'+name1),$('#'+name2),null);                                    
            }

            function delFeedBack(no) {
                $.ajax({
                    url: '../includes/maintain/feedBackDataDel.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'no': no},
                })
                .done(function(txt) {
                    if (txt) {
                        $('#reloadPage').submit() ;
                    }
                });
            }

            function save(fun){
                let password1 = $('[name=sPassword]').val();
                let password2 = $('[name=password2]').val();
                if (password1 != password2) {
                    alert('確認密碼必需一致!');
                    $('#' + fun).show();
                    return;
                }

                if (!checkPayByCaseCategory()) {
                    alert('請指定預定切換隨案出款的時間');
                    $('#' + fun).show();
                    $('[name="sFeedDateCatSwitchDate"]').focus();
                    return;
                }

                var input = $('input');
                var textarea = $('textarea');
                var select = $('select');
                var arr_input = new Array();
                var reg = /.*\[]$/ ;

                $.each(select, function(key,item) {
                    if ($(item).attr("multiple") == 'multiple') {
                        $(item).children().each(function() {
                            if ($(this).is(':selected')) {
                                if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(this).val();
                            }
                        });
                    }

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
                        } else {
                            arr_input[$(item).attr("name")] = $(item).attr("value");
                        }
                    }
                });

                if ($('[name="checkBlackListChange"]').val() == 1) {
                    checkBlackList();
                }

                var obj_input = $.extend({}, arr_input);

                let link = '/includes/maintain/scrivenersave.php';
                if (fun == 'add') {
                    link = '/includes/maintain/scriveneradd.php';
                }

                var request = $.ajax({
                    url: link,
                    type: "POST",
                    data: obj_input,
                    dataType: "html"
                });

                request.done( function( msg ) {
                    alert(msg);

                    let sales_from = '<{$from_sales}>';
                    if (sales_from=='sales') { ////判斷是否為業務責任區審核來的
                        $("#form_back").attr('action','/sales/salesAgents.php');
                    }

                    let dep = "<{$smarty.session.member_pDep}>";
                    if ([9, 10].includes(dep)) {
                        $("#reloadPage").submit();
                    } else {
                        $('#form_back').submit();
                    }
                })
                .fail(function( jqXHR, textStatus ) {
                    alert(jqXHR.responseText);
                    $('#' + fun).show();
                });
            }

			function sms() {
                $("#dialog-confirm11").dialog({
                    resizable: false,
                    height:200,
                    modal: true,
                    buttons: {
                        "編輯": function() {
                            $('#form_sms').submit();
                        },
                        "取消": function() {
                            $( this ).dialog("close");
                        }
                    }
                });
            }

            function getFBseasons() {
                let yr = $('[name="FBYear"]').val() ;
                let no = "<{$data.sId}>" ;
                let url = 'branchfeedback.php' ;
                
                $.post(url, {"y": yr,"sn": no,"type": 'scrivener'}, function(txt) {
                    let str = txt.split(',') ;
                    $('[name="fbs1"]').val(str[0]) ;
                    $('[name="fbs2"]').val(str[1]) ;
                    $('[name="fbs3"]').val(str[2]) ;
                    $('[name="fbs4"]').val(str[3]) ;
                }) ;
            }
			
			function salesConfirm(no, act) {
				let url = 'salesScrivenerBack.php' ;
				
				$.post(url, {'sid': no, 'stid': '<{$data.sId}>', 'act': act}, function(txt) {
					let arr = jQuery.parseJSON(txt) ;
					
					$('#salesList').html(arr[0] + '&nbsp;') ;
					$('[name="sSales"]').val(0) ;
					$('#salesConfirm').html(arr[1] + '&nbsp;') ;
				}) ;
			}
			
			function unlocker() {
				let URLs = "unlocker.php" ;
				$.ajax({
					url : URLs,
					data: {id: "<{$data.sId}>", tp: "s"},
					type: "POST",
					dataType: "text",
					success: function(txt) {
						if (txt == 'T') {
							$('#lockerPNG').remove() ;
							$('#scrivenerId').append('<img id="lockerPNG" src="../images/unlock.png">') ;
							alert('帳號已解鎖完成!!') ;
						} else {
							alert('帳號解鎖錯誤!!請通知資訊單位處理!!') ;
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(xhr.status) ;
						alert(thrownError) ;
					}
				}) ;
			}

            function RgBonus(){
                if (confirm("確定要加值?")) {
                    $.ajax({
                        url: '../includes/maintain/setRgBonus.php',
                        type: 'POST',
                        dataType: 'html',
                        data: {id: "SC<{$data.sId|str_pad:4:'0000':0}>",cat:"S",money:$("[name='sRgBonus']").val()},
                    })
                    .done(function(respone) {
                        alert(respone);
                        $("[name='sRgBonus']").val(0);
                        getRgBalance();
                    });
                }
            }

            function getRgBalance(){
                $.ajax({
                    url: '../includes/maintain/getRgBalance.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "SC<{$data.sId|str_pad:4:'0000':0}>"},
                })
                .done(function(re) {
                    $("[name='sRgBalance']").val(re);
                });
                
            }
           
            function checkFeed(){
                if ($("[name='feedCat']:checked").val() == 1) {
                    $('.feedCat2').attr('disabled','disabled');
                    $('.feedCat1').removeAttr('disabled');
                } else if ($("[name='feedCat']:checked").val() == 2) {
                    $('.feedCat2').removeAttr('disabled');
                    $('.feedCat1').attr('disabled','disabled');
                }
            }

            function scrivenerFeedSp(){
                let url = "scrivenerFeedSp.php?sId=<{$data.sId}>";
                
                $.colorbox({
                    iframe: true, 
                    width: "60%", 
                    height: "90%", 
                    href: url,
                    onClosed: function() {}
                }) ;
            }

            function addBank(){
                let count = parseInt($("[name='bank_count']").val());

                $.ajax({
                    url: '../includes/maintain/getBankBlock.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {'val': count},
                })
                .done(function(msg) {
                    $(msg).insertBefore("#copyBank");
                    setComboboxNormal('NewBankMain' + count,'id');
                    setComboboxNormal('NewBankBranch' + count,'id');
                });
            
                $("[name='bank_count']").val(count + 1);
            }

            function copyFeedData(id){
                $("[name='change_feedbackData']").val(1);
                
                let url = 'copyFeedData.php?id='+id;
                $.colorbox({
                    iframe: true, 
                    width: "50%", 
                    height: "50%", 
                    href: url,
                    onClosed: function() {
                        let count = parseInt($("[name='data_feedback_count']").val());
                        let no = count + 1;

                        $("[name='newAccountNum[]']").each(function(index, val) {
                            $("#" + $(this).attr('id')).combobox("destroy");
                            setComboboxNormal($(this).attr('id'),"id");
                        });

                        $("[name='newAccountNumB[]']").each(function(index, val) {
                            $("#" + $(this).attr('id')).combobox("destroy");
                            setComboboxNormal($(this).attr('id'),"id");
                        });
                    }
                }) ;
            }

            function copyFeedData2(id){
                $("[name='change_feedbackData']").val(1);

                let cno = parseInt($("[name='data_feedback_count']").val());
                if (cno > 0) {
                    $("#cl",window.parent.document).click();
                    cno ++;
                }

                $('.newrow:last [name="newTtitle[]"]').val($('[name="sName"]').val());
                $('.newrow:last [name="newMobileNum[]"]').val($('[name="sMobileNum"]').val());

                // 
                if ($('[name="sSerialnum"]').val() != '') {
                    $('.newrow:last [name="newIdentity[]"]').val(3);
                    $('.newrow:last [name="newIdentityNumber[]"]').val($('[name="sSerialnum"]').val());
                } else {
                    $('.newrow:last [name="newIdentity[]"]').val(2);
                    $('.newrow:last [name="newIdentityNumber[]"]').val($('[name="sIdentifyId"]').val());
                }

                $('.newrow:last [name="newzipC[]"]').val($("[name='zip2']").val());
                $('.newrow:last [name="newzipCF"]').val($("[name='zip2']").val());
                $('.newrow:last [name="newcountryC"]').val($('[name="country2"]').val());

                $('.newrow:last [name="newzipR[]"]').val($("[name='zip2']").val());
                $('.newrow:last [name="newzipRF"]').val($("[name='zip2']").val());
                $('.newrow:last [name="newcountryR"]').val($('[name="country2"]').val());

                let url = '../escrow/listArea.php' ;
                $('.newrow:last #newareaCR option').remove() ;
                $('.newrow:last #newareaR2 option').remove() ;
            
                $.post(url,{"city":$('[name="country2"]').val()},function(txt) {
                    let str = '' ;
                    str = str + txt  ;
                    $('.newrow:last [name="newareaC"]').append(str) ;
                    $('.newrow:last [name="newareaC"]').val($('[name="area2"]').val());

                    $('.newrow:last [name="newareaR"]').append(str) ;
                    $('.newrow:last [name="newareaR"]').val($('[name="area2"]').val());
                }) ;

                $('.newrow:last [name="newaddrC[]"]').val($("[name='addr2']").val());
                $('.newrow:last [name="newaddrR[]"]').val($("[name='addr2']").val());

                $.ajax({
                    url: 'getScrivenerAccount.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: "<{$data.sId}>"},
                }).done(function(msg) {
                    let obj = JSON.parse(msg);

                    $('.newrow:last [name="newAccountNum[]"]').combobox("destroy");
                    $('.newrow:last [name="newAccountNum[]"]').val(obj.bank);
                    $('.newrow:last [name="newAccountNumB[]"]').combobox("destroy");
                    
                    GetBankBranchListCb('.newrow:last [name="newAccountNum[]"]','.newrow:last [name="newAccountNumB[]"]',obj.bankBranch);

                    $('.newrow:last [name="newAccountNum[]"]').combobox();
                    $('.newrow:last [name="newAccount[]"]').val(obj.Account3);
                    $('.newrow:last [name="newAccountName[]"]').val(obj.Account4);
                    $('.newrow:last [name="newAccountNum[]"]').combobox();
                });
            }

            function GetBankBranchListCb(bank, branch, sc) {
                $(branch).prop('disabled', true) ;
                
                let request = $.ajax({  
                    url: "/includes/maintain/bankbranchsearch.php",
                    type: "POST",
                    data: {
                        bankcode: $(bank).val()
                    },
                    dataType: "json"
                });

                request.done(function(data) {
                    $(branch).children().remove().end();
                    $(branch).append('<option value="">------</option>')
                    $.each(data, function (key, item) {
                        if (key == sc) {
                            $(branch).append('<option value="' + key +'" selected>' + item + '</option>');
                        } else {
                            $(branch).append('<option value="'+key+'">' + item + '</option>');
                        }
                    });

                    $(branch).combobox();
                });
                
                $(branch).prop('disabled', false) ;
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
                }else{
                    var html = "<span id=\"sign"+sales+"\" class=\"signSales\"><span id=\"signName\">"+$("[name='signSales']").find('option:selected').text()+"<\/span><a href=\"javascript:void(0)\" onclick=\"delSignSales("+sales+")\">X<\/a><input type=\"hidden\" name=\"signSalseID[]\" value=\""+sales+"\"><\/span>";
                    $(html).insertAfter("#addSalse");
                }

               $("[name='signSales']").val(0);
            }

            function delSignSales(sales){
               $("#sign"+sales).remove();
            }

            function showScrivenerBranch(branch) {
                if ((branch != '') && (branch != null) && (branch != undefined)) {
                    let url = '/includes/maintain/getScrivenerBranch.php';
                
                    $.post(url, {"branch": branch} , function(response) {
                        let branches = new Array;
                        let el = '';
                        
                        $.each(response, function(index, value) {
                            el += '<span id="scrivenerBranch' + value.sId + '" style="border: 1px solid #ccc;padding: 2px;"><span style="cursor: pointer;padding-right: 1px;" onclick="removeScrivenerBranch(' + value.sId + ')">Ｘ</span>' + value.sOffice + '</span>';
                            branches.push(value.sId);
                        });

                        $('[name=""sScrivenerBranch]').val(branches.join(','));
                        $('#scrivenerBranch').empty().html(el);
                    }, 'json');
                } else {
                     $('#scrivenerBranch').empty();
                }
            }
            
            function addScrivenerBranch() {
                let addScr = $('[name="scrivenerBranch"]').val();

                if ((addScr != 0) && (addScr != undefined) && (addScr != null) && (addScr != '')) {
                    let _branches = $('[name="sScrivenerBranch"]').val().split(',');
                    _branches.push(addScr);

                    let unique = _branches.filter(onlyUnique);
                    let _branch = unique.join(',');
                    
                    $('[name="sScrivenerBranch"]').val(_branch);
                    showScrivenerBranch(_branch);
                }
            }
            
            function removeScrivenerBranch(id) {
                if ((id != 0) && (id != undefined) && (id != null) && (id != '')) {
                    let _branches = $('[name="sScrivenerBranch"]').val().split(',');

                    let unique = _branches.filter(onlyUnique);
                    if (unique.length > 0) {
                        unique.forEach((item, index) => {
                            if (item == id) {
                                unique.splice(index, 1);
                            }
                        });
                        let _branch = unique.join(',');

                        $('[name="sScrivenerBranch"]').val(_branch);
                        showScrivenerBranch(_branch);
                    }
                } else {
                    showScrivenerBranch(null);
                }
            }

            function onlyUnique(value, index, self) {
                return self.indexOf(value) === index;
            }

            function switchContractBank() {
                let brands = getCooperateBrand();

                $('.contractBank').each(function() {
                    if ((brands.length == 1) && (brands[0] == 2)) {
                        if (($(this).val() == 1) && ($(this).prop('checked') === false)) {
                            $(this).prop('checked', false);
                            $(this).closest('label').hide();
                        } else {
                            $(this).closest('label').show();
                        }
                    } else {
                        if (($(this).val() == 7) && ($(this).prop('checked') === false)) {
                            $(this).prop('checked', false);
                            $(this).closest('label').hide();
                        } else {
                            $(this).closest('label').show();
                        }
                    }
                });
            }

            function getCooperateBrand() {
                let cBrands = new Array;

                $('[name="sBrand[]"]').each(function() {
                    if ($(this).prop('checked') === true) {
                        cBrands.push($(this).val());
                    }
                });

                return cBrands;
            }

            //關閉非已選取的永豐銀行
            function disableContractBank() {
                $('.contractBank').each(function() {
                    if ((($(this).val() == '4') || ($(this).val() == '6')) && ($(this).prop('checked') === false)) {
                        $(this).closest('label').empty();
                    }
                });
            }

            //預備回饋方式若為隨案時，顯示回饋日期
            function feedDataCatSwitch() {
                let feedback_category = $("[name='sFeedDateCatSwitch'] option:selected").val();
                $('#feedDataSwitch').hide();
                if (feedback_category == '2') {
                    $('#feedDataSwitch').show();
                } else {
                    $('[name="sFeedDateCatSwitchDate"]').val('');
                }
            }
            
            //檢查隨案回饋方式是否有選擇回饋日期
            function checkPayByCaseCategory() {
                let feedback_category = $("[name='sFeedDateCatSwitch'] option:selected").val();
                let feedback_data_switch_date = $('[name="sFeedDateCatSwitchDate"]').val();
                if ((feedback_category == '2') && (!feedback_data_switch_date)) {
                    return false;
                }

                return true;
            }

            function bankDelete(id) {
                if (confirm("確定要刪除?")) {
                    $.ajax({
                        url: '/includes/maintain/scrivenerBankDelete.php',
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
				font-size:12pt;
            }
            
            .input-text-mid{
                width:80px;
				font-size:12pt;
            }
            
            .input-text-sml{
                width:36px;
				font-size:12pt;
            }
            
            .text-center {
                text-align: center;
				font-size:12pt;
            }
            .text-right {
                text-align: right;
				font-size:12pt;
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

        .ui-autocomplete-input{ 
            width: 120px;
        }
        .signSales{
                background-color: white;
                margin-right: 1px;
                border: 1px solid #999;
                padding: 2px;
            }
        </style>
    </head>
    <body id="dt_example">
    <div id="show"></div>
        <div id="dialog-confirm11" title="編輯" style=" display:none;">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>選擇簡訊對象時，請先儲存以保存目已編輯資訊?</p>
        </div>
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
                        <div id="menu-lv2"></div>
                        <br/> 
                            <div id="tab" class="tab_content">
                               <div id="tabs">

            <ul>
                <li><a href="#tabs-contract">基本資料維護</a></li>
                <li><a href="#tabs-feedback">回饋金資訊</a></li>
                <{if $smarty.session.member_act_report == '1'}>
                <li><a href="#tabs-act">活動設定</a></li>
                <{/if}>
            </ul>
            <div id="tabs-contract">
                <form id="reloadPage" method="POST">
                    <input type="hidden" name="id" value="<{$data.sId}>">
                </form>
                <form name='form_scrivener'>
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
                        <th>地政士編號︰</th>
                        <td id="scrivenerId">
                            <input type="hidden" name="checkBlackListChange" >
                           <input type="hidden" name="id" value="<{$data.sId}>">
                           <{if empty($data.sId) }>
                           <input type="text" maxlength="10" class="input-text-big" value="" disabled='disabled' />
                           <{else}>
                           <input type="text" maxlength="10" class="input-text-big" value="SC<{$data.sId|str_pad:4:'0000':0}>" disabled='disabled' />
						   <{$locker}>
                           <{/if}>
                           <br>
                           <{if $is_edit == 1}>
                               <{if $data.sBlackListId != 0 && $data.sBlackListId != ''}>
                                    <font color="red">
                                        <input type="checkbox" name="blacklist" value="1" checked="checked" >黑名單
                                    </font>
                                <{else}>
                                    <input type="checkbox" name="blacklist" value="1">黑名單
                               <{/if}>
                           <{/if}>
                        </td>
                        <th>密碼輸入︰</th>
                        <td>
                            <input type="text" name="sPassword" maxlength="12" class="input-text-big" value="<{$data.sPassword}>"  /><br/>
                            密碼長度6~12碼，密碼必同時包含大、小寫英文字母阿拉伯數字0-9英文小寫視為不同密碼
                        </td>
                        <th>再次確認密碼︰</th>
                        <td>
                            <input type="password" name="password2" maxlength="12s" class="input-text-big" value="<{$data.sPassword}>"  />
                        </td>
                    </tr>
                    <tr>
                        <th>執照有效期限︰</th>
                        <td>
                            <input type="text" name="sLicenseExpired" maxlength="20" class="datepickerROC input-text-per" value="<{$data.sLicenseExpired}>" readonly />
                        </td>
                        <th>事務所名稱︰</th>
                        <td>
                             <input type="text" name="sOffice" maxlength="30" class="input-text-per checkBlackListIn" value="<{$data.sOffice}>"  />
                        </td>
                        <th>統一編號︰</th>
                        <td>
                             <input type="text" name="sSerialnum" maxlength="8" style="width:120px;" class="input-text-per" value="<{$data.sSerialnum}>"  />
							 <span id="suId"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>地政士︰</th>
                        <td>
                            <input type="text" name="sName" maxlength="20" class="input-text-per checkBlackListIn" value="<{$data.sName}>"  />
                        </td>
                        <th>身份證號碼︰</th>
                        <td>
                            <input type="text" name="sIdentifyId" maxlength="10" style="width:120px;" class="input-text-per checkBlackListIn" value="<{$data.sIdentifyId}>"  />
							<span id="ssId"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>聯絡電話(1)︰</th>
                        <td>
                            <input type="text" name="sTelArea" maxlength="3" class="input-text-sml" value="<{$data.sTelArea}>" /> -
                            <input type="text" name="sTelMain" maxlength="10" class="input-text-mid"  value="<{$data.sTelMain}>" />
                        </td>
                        <th>聯絡電話(2)︰</th>
                        <td>
                            <input type="text" name="sTelArea2" maxlength="3" class="input-text-sml" value="<{$data.sTelArea2}>" /> -
                            <input type="text" name="sTelMain2" maxlength="10" class="input-text-mid" value="<{$data.sTelMain2}>" />
                        </td>
                        <th>傳真號碼︰</th>
                        <td>
                            <input type="text" name="sFaxArea" maxlength="3" class="input-text-sml" value="<{$data.sFaxArea}>" /> -
                            <input type="text" name="sFaxMain" maxlength="10" class="input-text-mid" value="<{$data.sFaxMain}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>行動電話︰</th>
                        <td><input type="text" name="sMobileNum" maxlength="14" class="input-text-per" value="<{$data.sMobileNum}>" /> </td>
                        <th>狀態︰</th>
                        <td colspan="2">
                        <{html_options name=sStatus options=$menu_status class="input-text-per" selected=$data.sStatus style="width:80px;"}>
                         <span id="status_t">，時間
                            <input type="text" name="sStatusDateStart" value="<{$data.sStatusDateStart}>" class="datepickerROC" readonly="" style="width:70px;" >至<input type="text" name="sStatusDateEnd" value="<{$data.sStatusDateEnd}>" class="datepickerROC" readonly="" style="width:70px;" >
                                
                        </span>
                        </td>
                        
                        <td>
                            <{if $sms_target != 'distable' }>
                                <input type="button" onclick="sms()" value="發送簡訊對象" class="bt4" style="display:;width:100px;height:40px;">
                            <{/if}>

                            </div>
                       </td>
                    </tr>
                    <tr>
                        <th>地政士類型︰</th>
                        <td><{html_options name="sCategory" style="width:80px;" options=$sOptions selected=$data.sCategory}></td>
                        <th>生日︰</th>
                        <td colspan="3">
                            <input type="text"  name="sBirthday" value="<{$data.sBirthday}>" style="width:100px;" placeholder="格式:000-00-00">
                        </td>
                       
                    </tr>
                    <tr>
                        <th>分店：<input type="hidden" name="sScrivenerBranch"></th>
                        <td colspan="5">
                        <{html_options name="scrivenerBranch" style="width:200px;" options=$menu_scriveners}>
                        <input type="button" onclick="addScrivenerBranch()" style="padding: 5px;" value="加入">
                        <span id="scrivenerBranch"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>代書系統︰</th>
                        <td colspan="5">
                            <label><input type="radio" name="sScrivenerSystem" value="1" <{if $data.sScrivenerSystem == '1'}>checked<{/if}> onclick="handleScrivenerSystemClick(this)"> 顧代書系統</label>
                            <label><input type="radio" name="sScrivenerSystem" value="2" <{if $data.sScrivenerSystem == '2'}>checked<{/if}> onclick="handleScrivenerSystemClick(this)"> 王代書系統</label>
                            <label><input type="radio" name="sScrivenerSystem" value="3" <{if $data.sScrivenerSystem == '3'}>checked<{/if}> onclick="handleScrivenerSystemClick(this)"> 其他</label>
                            <span id="scrivenerSystemOther" style="<{if $data.sScrivenerSystem != '3'}>display:none;<{/if}>">
                                <input type="text" name="sScrivenerSystemOther" value="<{$data.sScrivenerSystemOther}>" placeholder="請輸入其他說明" style="width:200px;">
                            </span>
                        </td>
                    </tr>

					 <tr>
                        <th>負責業務︰</th>
                        <td>
                           
							<span style="margin-left:10px;" id="salesList">
								<{$sSales}>
                                <{if $smarty.session.pBusinessOwnership == 1}>
                                <a href="../sales/salesScrivenerArea.php" target="_blank">(編輯)</a>
                                <{/if}>
								&nbsp;
							</span>
                        </td>
						<th>前負責業務︰</th>
                        <td colspan="3">
                            <{html_options name=sSales options=$menu_sales selected=$data.sSales}>，更改日期:
                            <input type="text" class="datepickerROC" name="sSalesDate" value="<{$data.sSalesDate}>" readonly style="width:100px;">
                        </td>
                    </tr>
                    <{if $smarty.session.pBusinessEdit == '1' && $smarty.session.pBusinessView == '1'}>
                    <tr>
                        <th>業務已簽約　<br>地政士︰</th>
                        <td colspan="5">
                           
                            <{html_checkboxes name='sContractStatus' options=$menu_cstatus selected=$signData.sContractStatus separator=' '}>
                            ，簽約日期：
                            <input type="text" class="datepickerROC" name="sContractStatusTime" value="<{$signData.sSignDate}>"  readonly style="width:100px;">
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
                    <{else}>
                        <tr>
                        <th>業務已簽約　<br>地政士︰</th>
                        <td colspan="5">
                            <{html_checkboxes name='sContractStatus' options=$menu_cstatus selected=$signData.sContractStatus separator=' ' disabled=disabled}>
                            ，簽約日期：
                            <input type="text" name="sContractStatusTime" value="<{$signData.sSignDate}>"  readonly style="width:100px;">
                            <span id="signSalesblock">簽約業務：

                                <{foreach from=$signSales key=key item=item}>
                                <span id="sign<{$key}>" class="signSales" >
                                    <span id="signName"><{$item}></span> 
                                    <input type="hidden" name="signSalseID[]" value="<{$key}>">
                                </span>
                                <{/foreach}>
                            </span>
                        </td>
                    </tr>
                    <{/if}>

                    <tr>
                        <th>績效分數業務︰</th>
                        <td colspan="5">
                            <span style="margin-left:10px;">
                                <{$performanceSales}>
                                <{if $smarty.session.pBusinessOwnership == 1}>
                                <a href="/sales/salesPerformanceArea.php" target="_blank">(編輯)</a>
                                <{/if}>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th><span style='color:#FF0000;'>*</span>郵寄地址︰</th>
                        <td colspan="5" class="distable">
                            <input type="hidden" name="zip2" id="zip2" value="<{$data.sCpZip1}>" class="checkBlackListIn"/>
                            <input type="text" maxlength="6" name="zip2F" id="zip2F" class="input-text-sml text-center " readonly="readonly" value="<{$data.sCpZip1|substr:0:3}>" />
                            <select class="input-text-big" name="country2" id="country2" onchange="getArea('country2','area2','zip2')">
                                <{$listCity2}>
                            </select>
							<span id="area2R">
                            <select class="input-text-big" name="area2" id="area2" onchange="getZip('area2','zip2')" >
                                <{$listArea2}>
                            </select>
							</span>
                            <input style="width:330px;" name="addr2" value="<{$data.sCpAddress}>" class="checkBlackListIn"/>
                            <input type="hidden" id="checkCpAddr" value="<{$data.sCpAddress}>">
                            <input type="hidden" id="checkCpZip" value="<{$data.sCpZip1}>">
							<{if $data.sCpAddress != ''}>
							<a href="http://www.first1.com.tw/includes/mapsTgos.php?zips=<{$data.sCpZip1}>&addr=<{$data.sCpAddress}>" style="font-size:10pt;" class="iframe">查看地圖</a>
							<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <th>電子郵件︰</th>
                        <td colspan="3"><input type="text" name="sEmail" maxlength="50" class="input-text-per" value="<{$data.sEmail}>" /></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>合作仲介品牌︰</th>
                        <td>
                            <{if $smarty.session.member_id|in_array:[1, 3, 6, 48]}>
                                <{html_checkboxes name=sBrand options=$menu_brand selected=$data.sBrand separator=' '}>
                            <{else}>
                                <{html_checkboxes name=sBrand options=$menu_brand selected=$data.sBrand separator=' ' disabled='disabled'}>
                            <{/if}>
                        </td>
                        <th>合約銀行︰</th>
                        <td colspan="4">
                            <{if $smarty.session.member_id|in_array: [ 6, 48]}>
                                <{html_checkboxes class="" name=sBank options=$menu_contractbank selected=$data.sBank separator='&nbsp;&nbsp;'}>
                            <{else}>
                                <{if $data.sBank|@count > 1}>
                                    <{html_checkboxes class="contractBank" name=sBank options=$menu_contractbank selected=$data.sBank separator='&nbsp;&nbsp;' disabled='disabled'}>
                                <{else}>
                                    <{html_radios class="contractBank" name=sBank options=$menu_contractbank selected=$data.sBank[0] separator='&nbsp;&nbsp;' disabled='disabled'}>
                                <{/if}>
                            <{/if}>
                        </td>
                    </tr>
                    <tr>
                        
                        <th>繳回文件︰</th>
                        <td colspan="5" >
                            <{html_checkboxes name='sBackDocument' options=$menu_sbackDoc selected=$data.sBackDocument separator=' '}>
                            備註: <input type="text" name="sBackDocumentNote" id="" value="<{$data.sBackDocumentNote}>" style="width:150px">
                        </td>
                    </tr>
                   
                    <tr class='tks'>
                        <th>委任日期︰</th>
                        <td>
                            <input type="text" name="sAppointDate" class="datepickerROC" maxlength="14" class="calender input-text-big" value="<{$data.sAppointDate}>"  />
                        </td>
                        <th>本票票號︰</th>
                        <td>
                            <input type="text" name="sTicketNumber" maxlength="14" class="input-text-per" value="<{$data.sTicketNumber}>" />
                        </td>
                        <th>本票金額︰</th>
                        <td>
                            NT$<input type="text" name="sTicketMoney" maxlength="14" class="input-text-big" value="<{$data.sTicketMoney}>" />
                        </td>
                    </tr>
                    <tr class='tks'>
                        <th>開票日期︰</th>
                        <td>
                            <input type="text" name="sOpenDate" class="datepickerROC" maxlength="14" class="calender input-text-big" value="<{$data.sOpenDate}>"  />
                        </td>
                        <th>確核交存日期︰</th>
                        <td>
                            <input type="text" name="sSaveDate" class="datepickerROC" maxlength="14" class="calender input-text-big" value="<{$data.sSaveDate}>"  />
                        </td>
                        <th>發票類別︰</th>
                        <td>
                            <{html_radios name=sInvoiceCase options=$menu_invoice selected=$data.sInvoiceCase}>
                        </td>
                    </tr>
                     <tr class='tks'>
                        <th>本票備註︰</th>
                        <td colspan="5">
                            <input type="text" name="sTicketRemark" maxlength="255" class="input-text-per" value="<{$data.sTicketRemark}>" />
                        </td>
                    </tr>
                    
                    <tr>
                        <th>承辦人︰</th>
                        <td>
                             <{html_options name=sUndertaker1 options=$menu_ppl class="input-text-per" selected=$data.sUndertaker1}>
                        </td>
                        <th>第二承辦人︰</th>
                        <td>
                             <{html_options name=sUndertaker2 options=$menu_ppl class="input-text-per" selected=$data.sUndertaker2}>
                        </td>
                        <th>發票人︰</th>
                        <td>
                            <input type="text" name="sDrawer" maxlength="14" class="input-text-per" value="<{$data.sDrawer}>" />
                        </td>
                    </tr>
                   
                    <tr>
                        <th>代書作業習慣及　<br>注重細節等描述︰</th>
                        <td colspan="5">
                            <textarea rows="3" name="sRemark1" class="input-text-per"><{$data.sRemark1}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>評定摘要︰</th>
                        <td colspan="5">
                            <textarea rows="3" name="sRemark2" class="input-text-per"><{$data.sRemark2}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>歷史窗口︰</th>
                        <td colspan="5">
                            <textarea rows="3" name="sRemark3" class="input-text-per"><{$data.sRemark3}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>保證號碼　<br>注意事項︰</th>
                        <td colspan="5">
                            <textarea rows="3" name="sRemark5" class="input-text-per"><{$data.sRemark5}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>其它備註︰</th>
                        <td colspan="5">
                            <textarea rows="3" name="sRemark4" class="input-text-per"><{$data.sRemark4}></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tb-title">
                            指定解匯帳戶

                            <div style="float:right;padding-right:10px;">
                                <{if $disable_account == false}>
                                <a href="#" onclick="addBank()">增加</a>
                                <input type="hidden" name="bank_count" value="<{$bankcount}>">
                                <{/if}>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>總行(1)︰</th>
                        <td class="bank">
                            <{html_options name=sAccountNum1 options=$menu_bank selected=$data.sAccountNum1 }>
                        </td>
                        <th>分行(1)︰</th>
                        <td  class="bank">
                            <select name="sAccountNum2" class="input-text-per">
							<{$menu_branch}>
                            </select>
                        </td>
                        <th>指定帳號(1)︰</th>
                        <td class="distable">
                            <input type="text" name="sAccount3" maxlength="14" class="input-text-per" value="<{$data.sAccount3}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(1)︰</th>
                        <td colspan="2" class="distable">
                            <input type="text" name="sAccount4" class="input-text-per" value="<{$data.sAccount4}>" />
                        </td>
                        <td></td>
                        <th>停用：</th>
                        <td class="distable"><{html_checkboxes name="sAccountUnused" options=$menu_accunused selected=$data.sAccountUnused }></td>
                    </tr>
                    <tr>
                        <th>總行(2)︰</th>
                        <td  class="bank">
                            <{html_options name=sAccountNum11 options=$menu_bank selected=$data.sAccountNum11 }>
                        </td>
                        <th>分行(2)︰</th>
                        <td  class="bank">
                            <select name="sAccountNum21" class="input-text-per">
							<{$menu_branch21}>
                            </select>
                        </td>
                        <th>指定帳號(2)︰</th>
                        <td class="distable">
                            <input type="text" name="sAccount31" maxlength="14" class="input-text-per" value="<{$data.sAccount31}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(2)︰</th>
                        <td colspan="2" class="distable">
                            <input type="text" name="sAccount41" class="input-text-per" value="<{$data.sAccount41}>" />
                        </td>
                        <td></td>
                        <th>停用：</th>
                        <td class="distable"><{html_checkboxes name="sAccountUnused1" options=$menu_accunused selected=$data.sAccountUnused1 }></td>
                    </tr>
                    <tr>
                        <th>總行(3)︰</th>
                        <td class="bank">
                            <{html_options name=sAccountNum12 options=$menu_bank selected=$data.sAccountNum12 }>
                        </td>
                        <th>分行(3)︰</th>
                        <td class="bank">
                            <select name="sAccountNum22" class="input-text-per">
							<{$menu_branch22}>
                            </select>
                        </td>
                        <th>指定帳號(3)︰</th>
                        <td class="distable">
                            <input type="text" name="sAccount32" maxlength="14" class="input-text-per" value="<{$data.sAccount32}>" />
                        </td>
                    </tr>
                    <tr>
                        <th>戶名(3)︰</th>
                        <td colspan="2" class="distable">
                            <input type="text" name="sAccount42" class="input-text-per" value="<{$data.sAccount42}>" />
                        </td>
                        <td></td>
                        <th>停用：</th>
                        <td class="distable"><{html_checkboxes name="sAccountUnused2" options=$menu_accunused selected=$data.sAccountUnused2 }></td>
                    </tr>
                    <{foreach from=$dataBank key=key item=item}>
                    <tr class="transBank<{$item.sId}>">
                        <th>總行(<{$item.no}>)︰<input type="hidden" name="sAccountId14[]" value="<{$item.sId}>"></th>
                        <td class="bank">
                            <{if $item.bUnUsed == 1}>
                                <{html_options name="sAccountNum14[]" id="sAccountNum14<{$item.sId}>" options=$menu_bank selected=$item.sBankMain onchange="Bankchange('sAccountNum14<{$item.sId}>','sAccountNum24<{$item.sId}>')" disabled="disabled"}>
                            <{else}>
                               <{html_options name="sAccountNum14[]" id="sAccountNum14<{$item.sId}>" options=$menu_bank selected=$item.sBankMain onchange="Bankchange('sAccountNum14<{$item.sId}>','sAccountNum24<{$item.sId}>')" }>      
                            <{/if}>
                        </td>
                        <th>分行(<{$item.no}>)︰</th>
                        <td class="bank">
                            <select name="sAccountNum24[]" id="sAccountNum24<{$item.sId}>" class="input-text-per" <{$item.disabled}>>
                            <{$item.bankbranch}>
                            </select>
                        </td>
                        <th>指定帳號(<{$item.no}>)︰</th>
                        <td class="distable">
                            <input type="text" name="sAccount34[]" id="sAccount34<{$item.sId}>" maxlength="14" class="input-text-per" value="<{$item.sBankAccountNo}>" <{$item.disabled}>/>
                        </td>
                    </tr>
                    <tr class="transBank<{$item.sId}>">
                        <th>戶名(<{$item.no}>)︰</th>
                        <td colspan="2" class="distable">
                            <input type="text" name="sAccount44[]" id="sAccount44<{$item.sId}>" class="input-text-per acc_disabled3" value="<{$item.sBankAccountName}>" <{$item.disabled}>/>
                        </td>
                        <td></td>
                         <th>停用(<{$item.no}>)</th>
                        <td class="distable">
                            <span>
                                <input type="checkbox" name="sAccountUnused4[]" id="sAccountUnused4<{$item.sId}>" value="<{$item.sId}>" <{$item.checked}>> 是
                            </span>
                            <span style="float:right;">
                                <{if $disable_account == false}>
                                <a href="Javascript:void(0);" style="font-size:0.8em" onclick="bankDelete('<{$item.sId}>')">刪除紀錄</a>
                                <{/if}>
                            </span>
                        </td>
                    </tr>
                    <{/foreach}>
                    <tr id="copyBank">
                        <td colspan="6"><hr></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tb-title">&nbsp;</td>
                    </tr>
                    <tr>
                        <th>建立時間：</th>
                        <td><{$data.sCreat_time}></td>
                        <th >最後修改人：</th>
                        <td><{$data.sEditor}></td>
                        <th >修改時間：</th>
                        <td><{$data.sModify_time}></td>
                    </tr>
                </table>
                </form>
            </div>

            <div id="tabs-feedback">
                <input type="hidden" name="change_feedbackData">

                <{if $smarty.session.member_pDep == '9' || $smarty.session.member_pDep =='10' || $smarty.session.member_id == '6' || $smarty.session.member_pDep == '5' || $smarty.session.member_pDep == '4' || $smarty.session.member_pDep == '7' || $smarty.session.member_pDep == '12' || $smarty.session.member_pCaseFeedBackModify != 0}>
                    <div id="check_changeFeedBackData">
                        <table border="0" width="100%">
                            <tr>
                                <td colspan="6" class="tb-title">
                                    回饋金對象資料
                                     <input type="button" value="匯入資料" onclick="copyFeedData(<{$data.sId}>)">
                                     <{if $is_edit == 1}>
                                     <input type="button" value="帶入維護頁籤資料" onclick="copyFeedData2(<{$data.sId}>)">
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
                                <th colspan="1">
                                    <{if $disable_account == false}>
                                        <a href="#" onclick="delFeedBack('<{$item.fId}>')">刪除</a></th>
                                    <{/if}>
                            </tr>
                            <tr>
                                <th  width="10%">回饋方式︰<input type="hidden" name="fId[]" value="<{$item.fId}>"></th>
                                <td  width="20%">
                                    <{if $item.fStop == 1}>
                                        <{html_options name="fFeedBack[]" options=$menu_categoryrecall selected=$item.fFeedBack disabled="disabled"}>
                                    <{else}>
                                        <{html_options name="fFeedBack[]" options=$menu_categoryrecall selected=$item.fFeedBack}>
                                    <{/if}>
                                    
                                </td>
                                <th>姓名/抬頭︰</th>
                                <td width="22%">
                                    <input type="text" name="fTitle[]"  class="input-text-big" value="<{$item.fTitle}>" <{$item.disabled}>/>
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
                                    <span id="fId<{$item.no}>" ></span>
                                </td>
                                <th>收件人稱謂︰</th>
                                <td><input type="text" name="fRtitle[]" value="<{$item.fRtitle}>" <{$item.disabled}>></td>
                            </tr>
                            <tr>
                                <th>回饋報表收件地址︰</th>
                                <td colspan="5">
                                    <input type="hidden" name="fZipC[]" id="zipC<{$item.no}>" value="<{$item.fZipC}>" />
                                    <input type="text" maxlength="6" name="zipC<{$item.no}>F" id="zipC<{$item.no}>F" class="input-text-sml text-center" readonly="readonly" value="<{$item.fZipC}>" />
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
                                <td class="bank">
                                    <{if $item.fStop == 1}>
                                         <{html_options name="fAccountNum[]" id="fAccountNum<{$item.no}>" options=$menu_bank selected=$item.fAccountNum onchange="Bankchange('fAccountNum<{$item.no}>','fAccountNumB<{$item.no}>')" style="width:250px;" disabled="disabled"}>
                                    <{else}>
                                         <{html_options name="fAccountNum[]" id="fAccountNum<{$item.no}>" options=$menu_bank selected=$item.fAccountNum onchange="Bankchange('fAccountNum<{$item.no}>','fAccountNumB<{$item.no}>')" style="width:250px;"}>
                                    <{/if}>
                                </td>
                                <th>分行︰</th>
                                <td class="bank">
                                    <select name="fAccountNumB[]" id="fAccountNumB<{$item.no}>" class="input-text-per" <{$item.disabled}>>
                                    <{$item.bank_branch}>
                                    </select>
                                </td>
                                <th>指定帳號︰</th>
                                <td>
                                    <input type="text" name="fAccount[]" maxlength="14" class="input-text-per" value="<{$item.fAccount}>" <{$item.disabled}>/>
                                </td>
                            </tr>
                            <tr>
                                <th>戶名︰</th>
                                <td>
                                    <input type="text" name="fAccountName[]" class="input-text-per" value="<{$item.fAccountName}>" <{$item.disabled}>/>
                                </td>
                                <th>發票種類︰</th>
                                <td>
                                    <{if $item.fStop == 1}>
                                         <{html_options name="fNote[]" options=$menu_note selected=$item.fNote disabled="disabled"}>
                                    <{else}>
                                         <{html_options name="fNote[]" options=$menu_note selected=$item.fNote }>
                                    <{/if}>
                                    </td>
                                <th>所得類別︰</th>
                                <td>
                                    <{if $item.fStop == 1}>
                                        <{html_options name="fIncomeCategory[]" options=$menu_incomecategory selected=$item.fIncomeCategory disabled="disabled"}>
                                    <{else}>
                                        <{html_options name="fIncomeCategory[]" options=$menu_incomecategory selected=$item.fIncomeCategory }>
                                    <{/if}>
                                </td>
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
                                        <input type="hidden" name="feedback_count" value="0">
                                        <input type="hidden" name="data_feedback_count" value="<{$data_feedData_count}>">
                                    </div>
                                </th>
                            </tr>
                        </table>

                        <table border="0" width="100%" class="newrow distable distable2">
                            <tr>
                                <th  width="10%">回饋方式︰</th>
                                <td  width="20%">
                                    <{html_options name="newFeedBack[]" options=$menu_categoryrecall}>
                                </td>
                                <th>姓名/抬頭︰</th>
                                <td width="22%">
                                    <input type="text" name="newTtitle[]"  class="input-text-big"  />
                                </td>
                                <th>店長行動電話︰</th>
                                <td><input type="text" name="newMobileNum[]" maxlength="10" class="input-text-big" /></td>
                            </tr>
                            <tr>
                                <th>身份別︰</th>
                                <td>
                                    <{html_options name="newIdentity[]" options=$menu_categoryidentify}>
                                </td>
                                <th>證件號碼︰</th>
                                <td>
                                    <input type="text" name="newIdentityNumber[]" id="newIdentityNumber"  class="input-text-big" onkeyup="checkID('',this.value,'new')" />
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
                                    <select name="newAccountNumB[]" id="newAccountNumB" class="input-text-per"></select>
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
                                <th>所得類別︰</th>
                                <td><{html_options name="newIncomeCategory[]" options=$menu_incomecategory}></td>
                            </tr>
                           <tr>
                                <td colspan="6"><hr></td>
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
                                <a href="formfeedback.php?storeId=<{$data.sId}>&cat=1" class="iframe" style="font-size:9pt;">編修簡訊對象</a></div>
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
                                <a href="formscrivenerfeedback.php?sId=<{$data.sId}>" class="iframe" style="font-size:9pt;">編修簡訊對象</a></div>
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
                            <input type="text" maxlength="14" class="input-text-per" value="<{$item.sName}>" disabled='disabled'>
                        </td>
                        <th>行動電話︰</th>
                        <td>
                            <input type="text" maxlength="10" class="input-text-per" value="<{$item.sMobile}>" disabled='disabled'>
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
                        <td colspan="6" class="tb-title">&nbsp;</td>
                    </tr>
                    <tr>
                        <th>最後修改人：</th>
                        <td colspan="2"><{$data.sEditor_Accounting}></td>
                        <th>修改時間：</th>
                        <td colspan="2"><{$data.sModify_time_Accounting}></td>
                    </tr>
                    <tr>
                        <td colspan="6"><hr></td>
                    </tr>
                    <{if $smarty.session.member_pFeedBackModify!='0'}>
                    <tr>
                        <td colspan="6" class="tb-title">
                           回饋金資訊
                        </td>
                    </tr>

                    <tr>
                        <th>回饋比率︰</th>
                        <td>百分之&nbsp;<input type="text" name="sRecall" style="width:60px;" value="<{$data.sRecall}>" <{$_disabled}>></td>
                        <th>結算方式</th>
                        <td colspan="3">
                            <{if $is_edit == 1}>
                            <span style="padding-right:10px;">
                                <{$feed_date_cat_name}>
                                <input type="hidden" name="feedDateCat" value="<{$data.sFeedDateCat}>">
                                <{if $data.sFeedDateCat == 2}>
                                    <{$data.sFeedDateCatSwitchDate}>
                                <{/if}>
                            </span>
                            <span>
                                <span>
                                    （預計切換結算方式：<span><{html_options name="sFeedDateCatSwitch" options=$menu_feedDateCat selected=$data.sFeedDateCatSwitch onchange="feedDataCatSwitch()"}></span>
                                    <span id="feedDataSwitch">時間：<input type="month" name="sFeedDateCatSwitchDate" min="<{$min_date}>" value="<{$data.sFeedDateCatSwitchDate}>"></span>）
                                </span>
                            </span>
                            <{else}>
                                <span>
                                <{html_radios name='feedDateCat' options=$menu_feedDateCat selected=$data.sFeedDateCat}>
                                </span>
                            <{/if}>
                        </td>
                    </tr>
                    
                    <tr>
                        <th>品牌回饋地政士︰</th>
                        <td><input type="button" onclick="brandForScr()" value="編輯" class="bt4" style="display:;width:100px;height:40px;">
                            <span id="showBrandScr">
                            <{foreach from=$FeedSp key=key item=item}> 
                                <{$item.BrandName}>:<{$item.sReacllBrand}>%(品牌)、<{$item.sRecall}>%(地政士)；
                            <{/foreach}>
                            </span>
                        </td>
                        <th>未收足回饋</th>
                        <td colspan="3"><{html_checkboxes name='feedbackmoney' options=$menu_cstatus selected=$data.sFeedbackMoney separator=' '}></td>
                    </tr>
                    <tr>
                        <th>特殊回饋比率︰</th>
                        <td colspan="5">
                            <{if $data.sSpRecall2 != ''}>
                                <{assign var='ck1' value=''}> 
                                <{assign var='ck2' value='checked=checked'}> 

                                <{assign var='dis1' value='disabled=disabled'}> 
                                <{assign var='dis2' value=''}>
                            <{else}>
                                <{assign var='ck1' value='checked=checked'}> 
                                <{assign var='ck2' value=''}> 
                                
                                <{assign var='dis1' value=''}> 
                                <{assign var='dis2' value='disabled=disabled'}>
                            <{/if}>
                            <input type="radio" name="feedCat" value="1" onclick="checkFeed()" <{$ck1}> <{$_disabled}>>百分之
                            <input type="text" name='sSpRecall' style="width:60px;" value="<{$data.sSpRecall}>" class="feedCat1" <{$dis1}>  <{$_disabled}>>
                        </td>
                    </tr>
                     <tr>
                        <th>回饋金備註︰</th>
                        <td colspan="5"><textarea name="sRenote" class="input-text-per" <{$_disabled}> ><{$data.sRenote}> </textarea></td>
                    </tr>

                    <{/if}>
                    <tr>
                        <th>案件統計表>回饋金報表(藍底標記)</th>
                        <td>
                            <{html_radios name=feedbackMark options=$menu_mark selected=$data.sFeedbackMark}>
                                
                        </td>
                    </tr>
                    
                </table>
            </div>

            <{if $smarty.session.member_act_report == '1'}>
            <div id="tabs-act">
            
                <table border="0" width="100%">
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
                            <{foreach from=$va.Gifts key=kb item=vb}>
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
            
            </div>
            <{/if}>

        </div>

        <center>
            <br/>
            
            <{if $is_edit == 1}>
            <button id="save">儲存</button>
            <{else}>
            <button id="add">儲存</button>
            <{/if}>

            <button id="appoint">委任書</button>
        </center>

    <form name="form_back" id="form_back" method="POST"  action="listscrivener.php">
    </form>
    <form name="form_sms" id="form_sms" method="POST"  action="formscrivenersms.php">
        <input type="hidden" name="scid" value="<{$data.sId}>" />
    </form>
                            </div>
                        </div>
                    </div></div>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>










