<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>

<{include file='meta.inc.tpl'}> 
	
<script type="text/javascript">
$(document).ready(function() {

    $(window).focus(function() {
        var id = $("[name='runtest']").val();
        // console.log('focus'+$("[name='runtest']").val());
        StopMsg(id);
        
        // setInterval("getScrList()", 1000) ;

        // var t = $("[name='target']").val();
        // $("#a"+t).attr('class', 'end');
        // var check = 0;
        RunMsg(1000) ;
        // console.log('focus'+$("[name='runtest']").val());
    });

    $(window).load(function() {
        // console.log('load');
         setInterval("getScrList()", 1000) ;

        var t = $("[name='target']").val();
        $("#a"+t).attr('class', 'end');
        var check = 0;
        RunMsg(1000) ;
        // console.log('load'+$("[name='runtest']").val());
    });
   

    $(window).blur(function() {
       var id = $("[name='runtest']").val();
       // console.log(id);
       StopMsg(id);
      var id =  setInterval("RunUnReadMsg()", 1000) ;
       $("[name='runtest']").val(id);
       // console.log('blurGO'+id);
        // RunMsg(1000);
    });

    
  
    $("#msg").scroll(function() {
        
        if ($(this).scrollTop() == 0){
            StopMsg($("[name='runtest']").val());
            RunMsg(1000,$(".now:first").attr('id'));
            // getChat($("[name='target']").val(),$(".now:first").attr('id'),'2');
        }else if($(this).scrollTop() < 1598){ 
            //滑動時把正在執行的setInterval清掉，並設定30000後繼續執行
            
            StopMsg($("[name='runtest']").val());
            check = $("[name='runtest2']").val();
            if (check != 0) {
                 clearTimeout(check);   
            }

            $("[name='runtest2']").val(setTimeout('RunMsg(1000,"",1)',30000));
            // check =  ;
           
        }
 
        // check++;
    });
});

function RunUnReadMsg(){
    $.ajax({
        url: 'checkUnRead2.php',
        type: 'POST',
        dataType: 'html',
        data: {"target": $("[name='target']").val()},
    })
    .done(function(txt) {

        if (txt > 0) {
           
            // alert('您有未讀訊息');
            // confirm('您有未讀訊息');
            // console.log('您有未讀訊息');
        }
    });
    
}

function RunMsg(sec,day,step){//val

    if (day) {
        var id = setInterval("getChat('"+$("[name='target']").val()+"','"+day+"','1')", sec) ;
    }else{
        var id = setInterval("getChat('"+$("[name='target']").val()+"','','1')", sec) ;
    }
   
    $("[name='runtest']").val(id);

    return id;
}

function StopMsg(id){
    clearInterval(id);
    $("[name='runtest']").val('');

}

function setTarget(target){ //點擊
    var id = $("[name='runtest']").val();

    $("[name='target']").val(target);

    $("#Tname").text($("#c"+target).text());

    $(".child li").each(function() {
        $(this).removeClass();
    });

    $("#a"+target).attr('class', 'end');

    //滑動之後，換看另一人聊天訊息，要清掉TimeOut的設定
    clearTimeout($("[name='runtest2']").val()); 
    $("[name='runtest2']").val('');
    StopMsg(id);
    RunMsg(1000);
   
}

function getScrList(){

    var acc = $("[name='target']").val();
    $.ajax({
        url: 'memberList.php',
        type: 'POST',
        dataType: 'html',
        data: {"acc": acc},
    })
    .done(function(txt) {
        var obj = jQuery.parseJSON(txt);

        $.each(obj, function(index, val) {
             if (val > 0 && acc != index) {
                
                $("#b"+index).attr('class', 'unRead');
                $("#b"+index).text(val);
            }else{
                $("#b"+index).removeAttr('class');
                $("#b"+index).text('');
            }
        });
          
    });
    
}

function getChat(acc,day,step) {
    
    var urls = 'checkMsg.php' ;
    var i = 0;
 // console.log('checkMsg');

    $.ajax({
        url: urls,
        type: 'POST',
        dataType: 'text',
        data: "acc="+acc+"&ide=2&day="+day,
        success: function(txt) {
             
            var ch = txt.substr(0,1) ;
            txt = txt.substr(1) ;
            //alert(ch) ;
            if (ch == '2') {
                //有新訊息
                i = 1 ;
            }
              
            if (day == '') {

                $('#msg').empty().html(txt) ;
                $('#msg').scrollTop($('#msg').prop("scrollHeight"));
            }else{

                $(txt).insertBefore(".now:first");
            }
               
               
        },
         
    }) ;
    
   
   
}

function menuAct(name){
    // console.log();
    var val = $("#u"+name).attr('src');

    var matcher = new RegExp("bottom");
    if (matcher.test(val)) {

        $("#u"+name).attr('src',val.replace(/bottom/, "left"));
    }else{
         $("#u"+name).attr('src',val.replace(/left/, "bottom"));
    }

    var str = $("#"+name).prop('class');
    var arr = new Array();
    arr = str.split(' ');
    if (arr[(arr.length-1)] == 'menushow') {
        arr[(arr.length-1)] = 'menuhide';
    }else{
        arr[(arr.length-1)] = 'menushow';
    }

    str = arr.join(' ');
    
    // console.log(str);
    $("#"+name).attr('class', str);
    
}
function checkPic(){
    var f = $('[name="appfile"]').val()
    if (f != '') {
        var re = /\.(jpg|png)$/i;  //允許的圖片副檔名 
        if (!re.test(f)) { 
            alert("只允許上傳 JPG 或 PNG 影像檔") ;
            event.returnValue = false ;
        }
    }
}
</script>
<style>
.text {
        padding:10px;
        border:1px solid #CCC;
        width: 70%;
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
.btn.focus_end{
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

.left{
    width:25%;
    border:1px solid #000;
    height:404px;
    overflow:auto;
    float:left;
    background-color: rgb(158, 41, 37);
}
.rightTop{
    width:70%;
    border:1px solid #000;
    height:50px;
    overflow:auto;
    /*background-color: #F5F5F5;*/
    
}.rightTop span{
   vertical-align: middle;
    line-height:40px;
    font-size: 20px;
    padding-left: 30px;

}
.rightMiddle{
    width:70%;
    border:1px solid #000;
    overflow:auto;
    /*float:left;*/
    
    height:270px;
}
.rightBottom{
    width:70%;
    border:1px solid #000;
    overflow:auto;
    background-color: #F5F5F5;
    /*float:left;*/
    /*padding-left: 10px;*/
    height:80px;
}

#scrivener{
    width:100%;
    height: 30px;
}
.main ul{
   width:100%;
   /*height:100%*/
   
}

.main li span{
   line-height: 30px;
   font-size: 17px;
   padding-left: 5px;
   color:white;
   font-weight:bold;
}
.main li span:hover{
    cursor:pointer;
    color:#CCCCCC;
}

.child li{
   font-size: 15px;
   line-height: 30px;
   padding-left: 10px;
   color:inherit;
   font-weight:inherit;
   color:#FFF;
   font-weight:bold;
}
.child li:hover{
   line-height: 30px;
   padding-left: 10px;
   background-color: #999999;
   cursor:pointer;
   font-weight:bold;
}
.child .end{
    background-color: #F5F5F5;
    color: red;
}
.menushow{
    display: inline;
}
.menuhide{
    display: none;
}
.unRead{
    border-radius:0.5em 0.5em 0.5em 0.5em;
    background-color: #FFF;
    color:rgb(158, 41, 37);
    margin-left: 10px;
    padding-left: 5px;
    padding-right: 5px;
    /*width:30px;*/
    /*height: 30px;*/
    display: inline;
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
	                <td width="81%" align="right"></td>
	                <td width="14%" align="center"><h2> 登入者 <{$smarty.session.member_name}></h2></td><td width="5%" height="30" colspan="2"><h3><a href="/includes/member/logout.php">登出</a></h3>
	                </td>
	            </tr>
	            </table>
	        </td>
	    </tr>
	</table> 
</div>
<{include file='menu1.inc.tpl'}>
<table width="1000" border="0" cellpadding="4" cellspacing="0">
	<tr>
		<td bgcolor="#DBDBDB">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td height="17" bgcolor="#FFFFFF">
						<div id="menu-lv2"></div><br/>
                        <h3>&nbsp;</h3>
						<div id="container">
							<div id="dialog"></div>
							<div>
 								<h1>即時通訊管理</h1><br>
 									
									<div style="width:100%">
                                        <div class="left">
                                        <!-- <select name="scrivener" id="scrivener">
                                            <{foreach from=$list key=key item=item}>
                                                <option value="<{$key}>"><{$item.office}></option>
                                            <{/foreach}>
                                        </select> -->
                                            <ul class="main">

                                                <{foreach from=$list key=key item=item}>
                                                <li>
                                                    <span onclick="menuAct('<{$item.no}>')" ><{$item.office}>
                                                        <img src="../../images/ic_play_arrow_white_24dp_1x_bottom.png" id="u<{$item.no}>" style="vertical-align:middle;">
                                                    </span>
                                                    <ul class="child menushow" id="<{$item.no}>">
                                                        <{foreach from=$item.data key=k item=data}>
                                                        <li onclick="setTarget(<{$data.aId}>)" id="a<{$data.aId}>">
                                                            <div style="display: inline;" id="c<{$data.aId}>"><{$data.aName}></div>
                                                            <div class="" id="b<{$data.aId}>"></div>
                                                        </li>
                                                        <{/foreach}>
                                                    </ul>
                                                        
                                                </li>

                                                <{/foreach}>
                                                
                                                    
                                            </ul>
                                       
                                        </div>
                                        <div class="rightTop">
                                            <span id="Tname"><{$name}></span>
                                        </div>
                                        <div class="rightMiddle" id="msg">

                                        </div>
                                        <div class="rightBottom">
                                            <div style="padding-left:10px;">
                                                    <form method="POST" enctype="multipart/form-data" action="processAPP.php" onsubmit="checkPic()">
                                                    <p>請選擇上傳檔案：<input type="file" name="appfile"></p>
                                                    <!-- <input type="button" value="檔案上傳" onclick="showUpload()"> -->
                                                    <p><!-- 請輸入訊息： --><input type="text" name="content" class="text">
                                                    <input type="submit" value="Enter" class="btn">
                                                    <input type="hidden" name="target" value="<{$targetAcc}>">
                                                    <input type="hidden" name="flow" value= "2">
                                                    <input type="hidden" name="runtest">
                                                    <input type="hidden" name="runtest2">
                                                    </p>

                                                </form>
                                            </div>
                                            
                                        </div>
										
									</div>
                                    <!--  -->
							</div>

						</div>
						<div id="footer" style="height:50px;">
							<p>2012 第一建築經理股份有限公司 版權所有</p>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>


</body>
</html>