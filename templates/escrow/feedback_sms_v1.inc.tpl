<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script src="/js/lib/comboboxNormal.js"></script>
        <script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
        <script type="text/javascript">
            // Enable pusher logging - don't include this in production
            Pusher.logToConsole = true;
            var pusher = new Pusher("<{$pusher['key']}>", {
                cluster: "<{$pusher['cluster']}>",
                forceTLS: false,
            });
            
            var channel = pusher.subscribe("first1-feedback-sms");
            channel.bind("sms-shorturl", (data) => {
                if (data == 'FINISH') {
                    $('#sms_generate').show();
                    $('#url-cnt').empty();
                } else {
                    $('#url-cnt').empty().html(data);
                }
            });

            $(document).ready(function() {
                smstxt();
				getMarguee(<{$smarty.session.member_id}>) ;
				setInterval(function() { getMarguee2(<{$smarty.session.member_id}>); }, 180000)
				
                setComboboxNormal('choice_branch','name');

                if ("<{$sms_generate}>" == 'none') {
                    $('#sms_generate').hide();
                } else {
                    $('#sms_generate').show();
                }

                $("[name='add']").live('click', function() {
                    var bId = $("[name='choice_branch']").val();
                    var bname = $("[name='choice_branch'] option:selected").text();
                    var check = 0;
                    var cat = $("[name='cat']:checked").val();
                    var msg = $("[name='msg']").val();

                   $('[name="branch"]').find('option').each(function() {
                        if ($(this).val() == bId) { //判斷是否有增加過
                            check = 1;
                        }
                    });
                    
                    if (check == 0) {
                        $("[name='branch']").append("<option value='"+bId+"'>"+bname+"</option>"); 

                        $.ajax({
                            url: 'feedback_sms_send_v1.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {'branch':bId,'send':'2',cat:cat,'msg': msg},
                        }).done(function(txt) {
                            <{if $smarty.session.member_id == 6}>
                            console.log(txt);
                            <{/if}>

                            $(txt).insertAfter("#show");
                        })

                    } else if (check == 1) {
                        alert('已增加過');
                    }
                });

                
                $("[name='del']").live('click', function() {
                    var bId = $("[name='branch']").val();
                    var bname = $("[name='branch'] option:selected").text();

                    $("[name='b"+bId+"']").remove();
                    $("[name='branch'] option:selected").remove();

                });
                
                $("#reset").live('click', function() {
                    location.replace(location.href);
                });

                $("[name='send']").live('click', function() {
                    $('#action').hide();

                    var msg = $("[name='msg']").val();
                    var branch ;
                    var tmp = new Array();
                    var cat = $("[name='cat']:checked").val();
                    
                   $('[name="branch"]').find('option').each(function(i) {
                        tmp[i] = $(this).val();
                    }); 

                    branch = tmp.join(',');
                    if (tmp.length <= 0) {
                        alert('請選取"寄送店家"');
                        return false;
                    }

                    if (cat == 1) {
                        $.ajax({
                            url: 'feedback_sms_generation.php',
                            type: 'POST',
                            dataType: 'html',
                            data: {
                                'branch': branch,
                                'cat'   : cat,
                                'msg'   : msg,
                                'uuid'  : _uuid()
                            },
                        })
                        .done(function(txt) {
                            <{if $smarty.session.member_id == 6}>
                            console.log(txt);
                            <{/if}>

                            alert('短網址產生中，請稍候...');
                            $("#reset").trigger('click');
                        });
                    } else {
                        if (confirm("確定要發送簡訊通知？") == true) {
                            $.ajax({
                                url: 'feedback_sms_send_v1.php',
                                type: 'POST',
                                dataType: 'html',
                                data: {'msg': msg,'branch':branch,'send':'1',"cat":cat},
                            })
                            .done(function(txt) {
                                <{if $smarty.session.member_id == 6}>
                                console.log(txt);
                                <{/if}>
                                if (txt == true) {
                                    alert('簡訊發送成功!!') ;
                                }else{
                                    alert('簡訊發送成功!!') ;
                                }
                            })
                        }
                    }

                    $('#action').show();
                });
    		  
                /*-----------------------------------------*/
                $("[name='add']").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
                $("[name='del']").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );

                $("[name='send'], #reset").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                } );
            } );
			
			function show(op) {
				$( "#dialog" ).dialog(op) ;
			}

            function smsList() {
                var cat = $("[name='cat']:checked").val();

                $.colorbox({
                    iframe  : true, 
                    width   : "1000px", 
                    height  : "100%", 
                    href    : "feedback_smsList_v1.php?cat=" + cat,
                    onClosed: function() {
                        if ((cat == 1) && ($('#sms_notify').val() == 1)) {
                            $('#sms_notify').val(2);
                            alert('請點擊下方 "產製短網址" 按鈕，進行短網址轉換');
                        }
                    }
                }) ;
            }

            function smstxt() {
                var cat = $("[name='cat']:checked").val();

                if (cat == '1') {
                    $('#action').val('產製短網址');
                    $('#sms_generate').show();
                    $('[name="msg"]').val("第一建經通知：<{$season}><first1>店家名稱</first1>回饋金已結算,請點下列網址至第一建經官網確認,並依辦法請款,謝謝。");
                } else {
                    $('#action').val('寄送簡訊');
                    $('#sms_generate').hide();
                    $('[name="msg"]').val("第一建經通知：回饋金已於<{$today}>匯入台端指定帳戶，敬請確認查收，若有疑問請洽<{$company.tel}>分機888蕭小姐、分機885陳小姐");
                }
            }

            function _uuid() {
                var d = Date.now();
                if (typeof performance !== 'undefined' && typeof performance.now === 'function') {
                    d += performance.now();
                }

                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                    var r = (d + Math.random() * 16) % 16 | 0;
                    d = Math.floor(d / 16);
                    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
            }

            function sms_send() {
                // alert('發送通知簡訊');
                $.colorbox({
                    iframe  : true, 
                    width   : "1000px", 
                    height  : "100%", 
                    href    : "feedback_sms_feedback_send.php",
                    onClosed: function() {
                        location.replace(location.href);
                    }
                }) ;
            }

        </script>
		<style>
		#container table {
            border: solid #CCC 1px;
        }

        #container th {
            background-color:#E4BEB1;
             /*border: solid #CCC 1px;*/
            padding: 10px;
        }
       
        #container td {
            padding: 10px;
            border: solid #CCC 1px;
        }

        .ui-autocomplete-input {
            width:250px;
            height: 20px;
        }

        .btn {
            color: #000;
            font-family: Verdana;
            font-size: 12px;
            font-weight: bold;
            line-height: 12px;
            background-color: #CCCCCC;
            text-align:center;
            display:inline-block;
            padding: 8px 12px;
            border: 1px solid #DDDDDD;
            /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
        }

        .btn:hover {
            color: #000;
            font-size:12px;
            background-color: #999999;
            border: 1px solid #CCCCCC;
        }

        .btn.focus_end {
            color: #000;
            font-family: Verdana;
            font-size: 12px;
            font-weight: bold;
            line-height: 12px;
            background-color: #CCCCCC;
            text-align:center;
            display:inline-block;
            padding: 8px 12px;
            border: 1px solid #FFFF96;
            /*border-radius:0.5em 0.5em 0.5em 0.5em;*/
        }
        
        .sms-notify {
            font-size: 9pt;
        }
        
        .sms-notify:hover {
            font-size: 12pt;
            font-weight: bold;
        }

        </style>
    </head>
    <body id="dt_example">
        <input type="hidden" id="sms_notify" value=2>

		<div id="wrapper">
            <div id="header">
                <table width="1000" border="0" cellpadding="2" cellspacing="2">
                    <tr>
                        <td width="233" height="72">&nbsp;</td>
                        <td width="753"><table width="100%" border="0" align="right" cellpadding="3" cellspacing="3">
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
                                    <td width="81%" align="right"><!-- <a href="#" onClick="window.open('../bank/create.php', '_blank', config='height=450,width=650,resizable=yes');"><img src="/images/icon_a1.png" alt="" width="94" height="22" /></a> --></td>
                                    <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3></td>
                                </tr>
                            </table></td>
                    </tr>
                </table> 
            </div>
            <{include file='menu1.inc.tpl'}>
			<ul id="menu">
			<div id="dialog"></div>
			</ul>
                <table width="1000" border="0" cellpadding="4" cellspacing="0">
                    <tr>
                        <td bgcolor="#DBDBDB">
                            <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                <tr>
                                    <td height="17" bgcolor="#FFFFFF">
                                        <div id="menu-lv2">
                                                        
                                        </div>
                                        <br/> 
                                        <h1>回饋金簡訊(測試版)</h1>
                                        <div id="container">
                                            <center>
                                            
                                                <form name="form_search">
                                                <div style="text-align:left;" id="url-cnt"></div>
                                                <table border="0" cellspacing="0" cellpadding="0" width="100%" >
                                                    <tr>
                                                        <th colspan="3">寄送類別</th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">
                                                            <input type="radio" name="cat" id="" value="1" checked onclick="smstxt()"><span style="padding-left: 5px;">通知簡訊<span id="sms_generate" style="display:none;"><a href="Javascript:void(0);" class="sms-notify" onclick="sms_send()">（*查看待發送通知）</a></span></span>
                                                            <input type="radio" name="cat" id="" value="0" onclick="smstxt()"><span style="padding-left: 5px;">收款簡訊</span>
                                                            
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="3">仲介店選擇</th>
                                                    </tr>
                                                    <tr>
                                                        <td align="center">店家選擇</td>
                                                        <td rowspan="2" align="center">
                                                            <input type="button" value="匯入名單->" onclick="smsList()" class="btn">
                                                            <br><br><br>
                                                            <input type="button" value="增加->" name="add"><br><br><br>
                                                            <input type="button" value="刪除" name="del">
                                                        </td>
                                                        <td align="center">寄送店家</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="45%" align="center">
                                                            <{html_options name=choice_branch options=$menu}>

                                                        </td>
                                                        
                                                        <td width="45%" align="center">
                                                             <select multiple="multiple" size="10" name="branch" style="width:300px;height:250px;"></select>  
                                                        </td>
                                                    </tr>
                                                   
                                                    <tr>
                                                        <th colspan="3">簡訊文字</th>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="3" align="center"> <textarea name="msg" cols="100" rows="5">第一建經通知：回饋金已於<{$today}>匯入台端指定帳戶，敬請確認查收，若有疑問請洽<{$company.tel}>分機888蕭小姐、分機885陳小姐</textarea></td>
                                                    </tr>
                                                    
                                                    
                                                </table>
                                                <table border="0" cellspacing="0" cellpadding="0" width="100%"style="table-layout:fixed" >
                                                    <tr id="show">
                                                        <th colspan="4">簡訊對象</th>
                                                    </tr>
                                                </table>
                                                <div style="padding:10px;">
                                                    <input type="button" id="action" name="send" value="寄送簡訊">
                                                    <input type="button" id="reset" value="重新整理">
                                                </div>
                                                
                                                </form>
                                                <!-- <div id="show"></div> -->
                                            </center>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            <div id="footer">
                <p>2012 第一建築經理股份有限公司 版權所有</p>
            </div>
    </body>
</html>