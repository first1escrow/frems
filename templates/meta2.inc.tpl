<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">

<title>第一建經後台管理</title>

<link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_page.css" />
<!-- <link rel="stylesheet" type="text/css" href="/libs/datatables/media/css/demo_table.css" /> -->

<link rel="stylesheet" href="/js/colorbox-master/colorbox.css" />
<link rel="stylesheet" type="text/css" href="/js/jquery-easyui/themes/material/easyui.css">
<link rel="stylesheet" type="text/css" href="/js/jquery-easyui/themes/icon.css">  
<link rel="stylesheet" href="/css/marquee.css" />

<script type="text/javascript" src="/js/jquery-easyui/jquery.min.js"></script>

<script type="text/javascript" src="/js/jquery-easyui/jquery.easyui.min.js"></script>
<script type="text/javascript" src="/js/jquery-easyui/locale/easyui-lang-zh_TW.js"></script>

<script type="text/javascript" src="/libs/jquery.formatCurrency-1.4.0.min.js"></script>
<script type="text/javascript" src="/js/colorbox-master/jquery.colorbox.js"></script>
<script type="text/javascript" src="/js/dateFunction.js"></script>
<script type="text/javascript" src="/js/marguee.js"></script>
    
<!-- <script type="text/javascript" language="javascript" src="/libs/datatables/media/js/jquery.dataTables.js"></script> -->
    
<script type="text/javascript" src="/js/p7exp.js"></script>
<script src="/js/hoverIntent.js"></script>

<script src="/js/jquery.superfish-1.5.0.js"></script>
<link rel="stylesheet" href="/css/superfish.css" media="screen">
<link rel="stylesheet" href="/css/superfish-navbar.css" media="screen">
<link rel="stylesheet" href="/css/superfish-vertical.css" media="screen">


<link rel="stylesheet" type="text/css" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css" rel="Stylesheet" />
<link rel="stylesheet" type="text/css" href="/css/layout-1.css"  />
<link rel="stylesheet" href="/css/datepickerROC.css" /> 
<script type="text/javascript" src="/js/datepicker/jquery-ui-datepicker.js"></script>
<link rel="stylesheet" href="/js/datepicker/jquery-ui-datepicker.css" /> 
<script type="text/javascript" src="/js/datepickerRoc.js"></script>


<!--20200811-->
<link rel="stylesheet" type="text/css" href="../js2/DataTables-1.10.21/css/jquery.dataTables.min.css"/>
<script type="text/javascript" src="../js2/DataTables-1.10.21/js/jquery.dataTables.min.js?v=1"></script>

<!--[if lte IE 7]>
<style>
#menuwrapper, #p7menubar ul a {height: 1%;}
a:active {width: auto;}
</style>
<![endif]-->
<{include file='pusher.inc.tpl'}>
<script type="text/javascript">
		$(document).ready(function() {
                $('#menuBar').superfish({
                    animation: {height:'show'},
                    delay: 1200
                }) ;
				//setInterval("check_Hifax()",180000) ;
                $('.currency-money1').formatCurrency({roundToDecimalPlace:0, symbol:''});
                $('.currency-money2').formatCurrency({roundToDecimalPlace:2, symbol:''});
                
                    <{if isset($smarty.session.member_bankcheck) && $smarty.session.member_bankcheck == '1' && $smarty.session.member_pDep != 9 && $smarty.session.member_pDep != 10 && $smarty.session.member_id != 22  && $smarty.session.member_id != 12}>
                        SmsRemind();
                        setInterval("SmsRemind()", 60000) ; //出款簡訊提醒
                    <{/if}>
                    <{if $smarty.session.member_id|in_array: [13, 36]}>
                         BirthdayRemind();
                        setInterval("BirthdayRemind()", 60000) ; //
                    <{/if}>
              
        });
        
        $(function() {
		
		/* 設定利息出款 colorbox */
		$('.int_packing').each(function() {
			var val = $(this).val() ;
			$(this).colorbox({iframe:true, width:"900px", height:"90%", href:val}) ;
		}) ;
		////
		
       
	});
      function myformatter(date){
            var y = date.getFullYear()-1911;
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }   
        
        function SmsRemind(){
            $.ajax({
                url: '/includes/sms_remind.php',
                type: 'POST',
                dataType: 'html',
            })
            .done(function(txt) {
                // 
                if (txt != '') {
                    alert(txt);   
                }
  
            });
            
        }
        function BirthdayRemind(){
            $.ajax({
                url: '/scrivener/presentNotify.php',
                type: 'POST',
                dataType: 'html',
            })
            .done(function(txt) {
                // 
                if (txt != '') {
                    alert(txt);   
                }
  
            });
        }
        
        function MultiInput(id) {
            var obj_name = 'input[name='+id+']'
            var taskArray = new Array();
            $(obj_name).each(function() {
                taskArray.push($(this).val());
            });
            return taskArray;
        }
        function ConvertDateToRoc() {
            var a = /^((?:19|20)\d\d)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/;
            var b = /^((?:19|20)\d\d)-(0[1-9]|1[012])$/;
            var c = /^([01]?\d\d)-(0[1-9]|1[012])$/;
            var d = /^([01]?\d\d)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/;
            
            if (format.test($(this).val())) {
                var year = op_year.exec($(this).val());
                year = year-1911;
                var date = op_date.exec($(this).val());
            }
       }

        function DateInspection(date, type) {
            var a = /^([01]?\d\d)-(0[1-9]|1[012])$/;
            var b = /^([01]?\d\d)-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/;
            var reg = null;
            if (type == 'a') {
                reg = a;
            } else {
                reg = b;
            }
            
            if (reg.test(date) || (date.length == 0) ) {
                return true;
            } else {
                return false;
            }
        }
        
        function ShowCalender(obj) {
            //var form_name = this.form.attributes["name"].value + '.' + this.attributes["name"].value;
            //show_calendar(form_name);
			//showdate(eval(this.form.attributes["name"]value+'.'+this.attributes["name"].value)) ;
			showdate(obj) ;
        }
        
        
            function GetBankBranchList(bank, branch, sc) {
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
                });
				
				$(branch).prop('disabled',false) ;
            }
			
			function getArea(ct,ar,zp) {
				var url = 'listArea.php' ;
				var ct = $('[name="' + ct + '"] :selected').val() ;
				
				$('[name="' + zp + '"]').val('') ;
				$('[name="' + zp + 'F"]').val('') ;
				$('#' + ar + 'R').empty() ;
				
				$.post(url,{"city":ct},function(txt) {
					var str = '<select class="input-text-big" name="' + ar + '" id="' + ar + '" onchange=getZip("' + ar + '","' + zp + '")>' ;
					str = str + txt + '</select>' ;
					$('#' + ar + 'R').html(str) ;
				}) ;
			}
			
			function getZip(ar,zp) {
				var zips = $('[name="' + ar + '"] :selected').val() ;
				
				$('[name="' + zp + '"]').val(zips) ;
				$('[name="' + zp + 'F"]').val(zips.substr(0,3)) ;
			}
			
			function check_Hifax() {
				var url = '/hifax/checkMail.php' ;
				$.post(url,function(txt) {
					
					if (txt != '') {
						alert('收到新的傳真資料共 ' + txt + ' 筆，請至https://www.hibox.hinet.net查詢') ;
					}
				}) ;
			}
</script>
<style type="text/css">
    body {
        background-color: #F8ECE9;
        background-image: url();
    }
    .abgne_tab6 {MARGIN: 10px 0px;
                 WIDTH: 980px;
                 CLEAR: left
    }
    #tab h4 {
        font-family: "Times New Roman", Times, serif;
        font-size: 15px;
        text-decoration: none;
        background-image: url(/images/bg-222.gif);
        text-align: center;
        background-position: 155 34px;
        line-height: 26px;
    }
    
    #menu_lv2 {
        margin:10px;
        
    }
    
    #menu_lv2 span {
        font-size: 16px;
    }
    
    #menu_lv2 span:hover {
        background-color:#76b4ff;
/*        text-decoration:underline;*/
    }
    
    .lv2-selected{
        background-color:#6495ed;
    }
    
   #menu-lv2 {
        margin-left:10px;
        font-size: 16px;
   }
   .sf-menu li {
        background: #E1E1E1;
        font-weight: bold;
        width: 150px;
    }
    .sf-menu li li {
        background: #E4BEB1;
        color: #FFFFFF;
    }
	.sf-menu li li li {
        background: #E4BEBF;
        color: #FFFFFF;
    }
	.sf-menu li li .sf-sub-indicator {
		text-indent: 0px;
		overflow: visible;
	}
</style>
