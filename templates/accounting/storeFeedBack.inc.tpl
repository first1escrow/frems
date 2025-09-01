<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta2.inc.tpl'}>
        <script src="/js/jquery.number.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                 // $('#dialog').dialog('close');
                searchData();

			
            } );
			
			function dia(op) {
				$( "#dialog" ).dialog(op) ;
			}
            function checkALL(){
                var all = $('[name="all"]').prop('checked');

                // console.log(all);
                if (all == true) {
                    $('[name="allForm[]"]').prop('checked', true);
                }else{
                    $('[name="allForm[]"]').prop('checked', false);
                }
                showcount();
                // allForm
            }
            function searchData(){
                // console.log('AAA');
                var tranStatus = $('[name="banktranStatus"]').val();
                var tmp = new Array();
                $('.bStore').each(function(i) { tmp[i] = this.id; });
                var br = tmp.join(',');

                var tmp = new Array();
                $('.sStore').each(function(i) { tmp[i] = this.id; });
                var scr = tmp.join(',');

                if ($('[name="banktranStatus"]').val() == 1) {
                    $('[name="sDate"]').val('');
                    $('[name="sDate2"]').val('');
                }

                // console.log(br);
                // console.log(scr);

                $.ajax({
                    url: 'storeFeedBack_result.php',
                    type: 'POST',
                    dataType: 'html',
                    data: { banktranStatus:tranStatus ,
                            sDate:$('[name="sDate"]').val(),
                            sDate2:$('[name="sDate2"]').val(),
                            branch:br,
                            scrivener:scr,
                            exp:$('[name="exp"]').val()
                        },
                })
                .done(function(html) {
                    $("#result").html(html);
                    showcount();

                    // $("[name='branch']").val('');
                    // $("[name='scrivener']").val('');
                });
                
            }
            function excel(){
                $("#myform").submit();
                setTimeout(searchData,2000);
               
                // $("#myform").submit(function() {
                   
                // });
                 // searchData();
            }
            function showcount(){
                var checkedCount = 0;
                var feedbackMoneyTotal = 0;
                $('[name="allForm[]"]').each(function() {
                    if ($(this).prop('checked') == true) {
                        // console.log($('[name="sFeedBackMoneyTotal_'+$(this).val()+'"]').val());
                        feedbackMoneyTotal += parseInt($('[name="sFeedBackMoneyTotal_'+$(this).val()+'"]').val())
                        checkedCount++;

                    }
                });

                $("#count").html(checkedCount);


                $("#feedBackMoney").html($.number(feedbackMoneyTotal));
            }

            function add(cat){

                if (cat == 'b') {
                    var val = $('[name="branch"]').val();
                    
                    var text = $('#branch option[value="'+val+'"]').text(); 
                    
                    
                    $("#showBrach").append('<div id="'+val+'" class="addStore bStore"><a href="#" onClick="del('+val+')" >(刪除)</a>'+text+'</div>');

                     
                    
                }else if(cat == 's'){
                    var val = $('[name="scrivener"]').val();
                    var text = $('#scrivener option[value="'+val+'"]').text(); 
                    
                    $("#showSctivener").append('<div id="'+val+'" class="addStore sStore"><a href="#" onClick="del('+val+')">(刪除)</a>'+text+'</div>');
                    $('input:checkbox[name="bCategory"]').filter('[value="3"]').attr('checked',false) ;
                    
                }
            }
            function del(id){
                $("#"+id).remove();
            }

            function setStatus(id){
                $.ajax({
                    url: 'storeFeedBack_status.php',
                    type: 'POST',
                    dataType: 'html',
                    data: {id: id},
                })
                .done(function(code) {
                    searchData();
                    if (code == 1) {
                        alert("已恢復未匯出");
                    }
                    // console.log(msg);
                    // alert(msg);
                    
                });
                
            }
        </script>
		<style>
		#dialog {
			background-image:url("../images/animated-overlay.gif") ;
			background-repeat: repeat-x;
			margin: 0px auto;
		}
         .tb_main{
            border: 1px solid #999;

        }
        .tb{
            border: 1px solid #FFFFFF;
            width: 100%;
            /*background-color: #FCEEEE;*/

        }
        .tb td{
            border: 1px solid #999;
            padding: 5px;
        }
        .tb th{
            border: 1px solid #999;
            background: #FCEEEE;
            padding: 5px;
        }

        .div-inline{ 
            display:inline;
           /* width: 90%;
            float: center;
            padding-bottom: 50px;
            */


            /*padding-right: 20px;*/
        } 
        .div-inline th{
          text-align: left;
        }
        .div-inline td{
            padding-left: 20px;
        }
        #show {
            padding: 50px;
           
        }
        .div-inline2{ 
            display:inline;
            width: 100%;
            float: center;
            padding-bottom: 50px;
           
            /*padding-right: 20px;*/
        } 
        .xxx-button {
            color:#FFFFFF;
            font-size:12px;
            font-weight:normal;
            
            text-align: center;
            white-space:nowrap;
            
            background-color: #a63c38;
            border: 1px solid #a63c38;
            border-radius: 0.35em;
            font-weight: bold;
            padding: 0 20px;
            margin: 0px auto 0px auto;

            width:auto;
            height:20px;
            font-size:16px;
        }
        .xxx-button:hover {
            background-color:#333333;
            border:1px solid #333333;
        }
		</style>
    </head>
    <body id="dt_example">

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
                                        <h3>&nbsp;</h3>
                                        <div id="container">
                                            <center>
                                                <h1>店家回饋出款檔</h1>
                                                <br>
                                                <!-- <div style="paddin:20px;text-align:right;"><a href="excel/example/feedbackExample.xlsx">範例檔格式下載</a></div> -->
                                                <br>
                                                <form name="myform" id="myform" method="POST"  action="storeFeedBack_excel.php" target="_blank">
                                                <table  align="center" class="tb_main" cellpadding="10" cellspacing="10">  
                                                    <tr>
                                                        <td align="center">狀態
                                                            <select name="banktranStatus" id="" >
                                                                <option value="1" selected>未匯出</option>
                                                                <option value="2">已匯出</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            匯出批次
                                                           <select name="exp" id="exp" class="easyui-combobox">
                                                                <{foreach from=$menu_exp key=key item=item}>
                                                                <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}>
                                                           </select>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                             匯出表單時間
                                                            <input type="text" name="sDate" class="datepickerROC" style="width:100px;">
                                                            ~
                                                            <input type="text" name="sDate2" class="datepickerROC" style="width:100px;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            地政士
                                                            <select name="scrivener" id="scrivener" class="easyui-combobox" style="width:300px;" >

                                                                <{foreach from=$menu_scrivener key=key item=item}>
                                                                <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}>
                                                            </select>
                                                            <div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('s')" class="xxx-button"></div>

                                                            <div id="showSctivener" style="padding-left:20px;">
                                                                
                                                            </div>
                                                        </td>
                                                        
                                                        
                                                        
                                                    </tr> 
                                                    <tr>
                                                        <td colspan="2">
                                                            仲&nbsp;&nbsp;&nbsp;介
                                                            <select id="branch" name="branch" class="easyui-combobox" style="width:300px;">
                                                                <{foreach from=$menu_branch key=key item=item}>
                                                                    <option value="<{$key}>"><{$item}></option>
                                                                <{/foreach}>
                                                            </select> 
                                                            <div style="display:inline;margin:50px;line-height:30px;"><input type="button" value="增加" onclick="add('b')" class="xxx-button"></div>
                                                            <div id="showBrach" style="padding-left:20px">
                                                                
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                         <td colspan="2" align="center">
                                                            <input type="button" value="查詢" onclick="searchData()">
                                                        </td>
                                                    </tr> 
                                                    </tr>

                                                </table>
                                                <br>
                                                <div id="result">
                                                    
                                                </div>
                                                
                                                <div><input type="button" value="匯出台新匯款檔" onclick="excel()" class="xxx-button"></div>
                                                <div>店家勾選數量: <span id="count"></span></div>
                                                <div>店家勾選總金額: <span id="feedBackMoney"></span></div>
                                                </form>
                                                <br>

                                                <div id="show">
                                                    <{$show}>
                                                </div>
                                                
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