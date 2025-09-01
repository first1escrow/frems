<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
		<{include file='meta.inc.tpl'}>
        <script type="text/javascript">
            $(document).ready(function() {
                
                $("[name='send']").on('click',  function() {

                   $("[name='save']").val('ok');
                    $("[name='form_search']").submit();


                });

                /*-----------------------------------------*/

                $("[name='send']").button( {
                    icons:{
                        primary: "ui-icon-document"
                    }
                });
            });
		function clickAll(al,cl){
            var check = $("[name='"+al+"']").prop("checked");
            if (check) {
                $("."+cl).attr('checked', 'checked');
            }else{
                $("."+cl).removeAttr('checked');
            }
            // setZip($("[name='all']").val(),'all');
        }
        function checkClick(){
            var ck = 0;
            $(".cb").each(function() {
                if ($(this).prop('checked') == false) {
                    $("[name='all']").removeAttr('checked');
                    ck = 1;
                }
            });

            if (ck == 0) {
                $("[name='all']").attr('checked', 'checked');
            }
        }
        function send(){

            // var cc = ;
             $("[name='txt']").val($('#showMsg').html());

            var txt = $("[name='txt']").val(); //encodeURI(uri)
            var str = new Array();
            var i = 0;
            $(".cb2").each(function() {
                if ($(this).prop('checked')) {
                    str[i] = $(this).val();
                    i++;
                }
                
            });
            // console.log(str);

            $.ajax({
                url: 'LineSendMsg.php',
                type: 'POST',
                dataType: 'html',
                data: {lId: str,txt:txt},
            }).done(function(msg) {
                console.log(msg);
                alert("訊息已送出");
            });
            
        }
        function setSticker(code){
            var sTxt = $('#showMsg').html();
            // var clone = 
            $("#img_"+code).clone().removeAttr('onclick').appendTo('#showMsg');

        }	

        function openupload(){
            $.colorbox({iframe:true, width:"500px", height:"250px", href:"LineEmojiUpload.php"}) ;
        }
			
        </script>
        
		<style>
		
        input[type="checkbox"]{
            display: inline-block;
            width: 20px;
            height: 20px;
            margin: -3px 4px 6px 6px;
            vertical-align: middle;

        }
        .cb-title{
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif; 
            padding: 6px;
        }
        .cTitle{
            padding: 10px;
            background-color: #E4BEB1;
            font-size: 18px;
        }
        .cContent{
            padding:10px;
            background-color: #F8ECE9;
        }
        .line-title{
            font-size: 18px;
            border-bottom: 1px solid black;
            padding: 10px;
        }

        .btn{
            color: #FFF;
            font-family: Verdana;
            font-size: 16px;
            font-weight: bold;
            line-height: 24px;
            background-color: #880303;
            text-align:center;
            display:inline-block;
            padding: 10px 12px;
            border: 1px solid #880303;
            margin-top: 5px;
            margin-right: 5px;
        }

        .btn:hover {
            color: #880303;
            font-size:16px;
            background-color: #FFF;
            border: 1px solid #880303;
        }
        .tb th{
            color: rgb(255, 255, 255);
            font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
            font-size: 1em;
            font-weight: bold;
            background-color: rgb(156, 40, 33);
            border: 1px solid #CCCCCC;
            padding: 6px;
        }
        .tb td{
            padding: 6px;
            border: 1px solid #CCCCCC;
            /*text-align: center;*/
        }
        #show{
            overflow-y:scroll;
            overflow-x:hidden;
            height: 300px;
            margin-top: 10px;
            border:1px solid #999;
        }
        #msg{
             margin-top: 10px;
             /*text-align: center;*/
            /*border:1px solid #999;*/
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
                                        <h1>LINE訊息發送</h1>
                                        <div id="container">
                                            <{if $smarty.session.member_id == 6}>
                                            <div>
                                                <a href="javascript:void(0);" onclick="openupload()">上傳LINE表情貼</a>
                                            </div>
                                            <{/if}>
                                        <form action="" method="POST" name="from">
                                            <div class="cTitle">地區</div>
                                            <div class="cContent"><input type="checkbox" name="all"  onclick="clickAll('all','cb')" <{$checkedALL}>><span class="cb-title">全區</span><br><{$menuZip}></div>
                                            <div class="cTitle">身分別</div>
                                            <div class="cContent"> <{html_checkboxes name="iden" options=$menuIden selected=$iden}></div>
                                            <div style="padding: 10px;text-align: center"><input type="submit" value="查詢" class="btn"></div>
                                        </form>

                                            <hr>
                                            <div class="line-title">名單</div>
                                            <div id="show">
                                                <table cellpadding="0" cellspacing="0" border="0" class="tb" width="100%">
                                                    <tr>
                                                        <th><input type="checkbox" name="AllLine" onclick="clickAll('AllLine','cb2')"></th>
                                                        <th align="left">暱稱</th>
                                                        <th align="left">店編</th>
                                                        <th align="left">店名稱</th>
                                                        <th align="left">手機</th>
                                                    </tr>
                                                    <{foreach from=$list key=key item=item}>
                                                    <tr>
                                                        <td align="center"><input type="checkbox" name="lId[]" class="cb2" value="<{$item.lId}>"></td>
                                                        <td><{$item.lNickName}></td>
                                                        <td><{$item.lTargetCode}></td>
                                                        <td><{$item.storeName}></td>
                                                        <td><{$item.lCaseMobile}></td>
                                                    </tr>
                                                    <{/foreach}>
                                                </table>
                                            </div>
                                            <div class="line-title">訊息內容</div>
                                            <div id="msg">
                                                <div id="showMsg" style="border:1px solid #CCC;height: 200px;text-align: left;" contenteditable="true"> </div>
                                                <input type="hidden" name="txt">
                                            </div>
                                            <div>
                                                    <{foreach from=$moji key=key item=item}>
                                                    <!-- <span id="img_<{$item.lCode}>" class="sticker"> -->
                                                        <img src="LineStickerImg.php?id=<{$item.lCode}>" alt="<{$item.lCode}>" title="<{$item.lTxt}>" id="img_<{$item.lCode}>" class="sticker" height="20px" width="20px" onclick="setSticker('<{$item.lCode}>')">
                                                    <!-- </span> -->
                                                    <{/foreach}>
                                            </div>
                                            <div style="text-align:center;"><input type="button" value="送出" class="btn" onclick="send()"></div>
                                            <!-- <input type="checkbox" name="" id=""> -->

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