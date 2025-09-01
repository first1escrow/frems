<?php

//include('../web_addr.php') ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;
include_once '../tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], ' ', '查看出款未審核列表') ;

//if ($_SESSION["member_id"] != '1' and $_SESSION["member_id"] != '5' ) { 
if ($_SESSION["member_bankcheck"] != '1') {
	echo '
	<script>
	alert("您無此功能使用權限!!") ;
	</script>
	' ;
	//header('Location: /bank/new/out.php') ;
	exit ;
}

//合約銀行資料
$sql = 'SELECT * FROM tContractBank WHERE (cShow="1" OR cId =5) ORDER BY cOrder ASC;' ;
$rs = $conn->Execute($sql) ;
while (!$rs->EOF) {
	$conBank[] = $rs->fields ;
	$rs->MoveNext() ;
}
unset($rs) ;
##
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出帳建檔列表</title>
<link rel="stylesheet" href="../css/colorbox.css" />
<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery.colorbox.js"></script>

<script>

$(document).ready(function(){

				//Examples of how to assign the ColorBox event to elements
				// $(".group1").colorbox({rel:'group1'});
				// $(".group2").colorbox({rel:'group2', transition:"fade"});
				// $(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
				// $(".group4").colorbox({rel:'group4', slideshow:true});
				// $(".ajax").colorbox();
				// $(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344});
				$(".iframe").colorbox({
					iframe:true, width:"1100", height:"500",					
					onClosed:function(){ reload_page(); }
				});
				// $(".inline").colorbox({inline:true, width:"50%"});
				// $(".callbacks").colorbox({
				// 	onOpen:function(){ alert('onOpen: colorbox is about to open'); },
				// 	onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
				// 	onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
				// 	onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
				// 	onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
				// });
				
				// //Example of preserving a JavaScript event for inline calls.
				// $("#click").click(function(){ 
				// $('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
				// 	return false;
				// });
				// $("#export_file").click(function() {
				// 	$.colorbox({	
				// 		iframe:true, width:"1100", height:"500",						
				// 		href: "/bank/_export_all.php?x=<?php echo $b;?>&y=<?php echo $export;?>",
				// 		onClosed:function(){ reload_page(); }
				// 	});
				// })
});
function open_w(url){	
	window.open (url , 'newwindow', 'height=500, width=1100, top=250, left=250, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
function reload_page(){
	location.reload();
}
function showChild(id){
	var cls = $(".c"+id).attr('class');
	var arr = new Array();
	arr = cls.split(' ');
	// 
	if (arr[1] == 'cClose') { //child cClose c99986006113464
		
		$(".c"+id).attr('class', cls.replace("cClose","cOpen"));
		$("[name='s"+id+"']").val("縮合");
		
	}else{
		$(".c"+id).attr('class', cls.replace("cOpen","cClose"));
		$("[name='s"+id+"']").val("展開");
	}
	// arr[1]
	//str.replace(/Microsoft/, "W3School")

}
function checkALL(id){
	// check[]
	var ck = $("[name='a"+id+"']").prop('checked');
	
	$(".c"+id+" [name='check[]']").each( function() {

		if (ck == true) {
			$(this).prop('checked', true);
		}else{
			$(this).prop('checked', false);
		 
		}
	});
	
}
function auth(){
	var input = $('input');
	var arr_input = new Array();
	var reg = /.*\[]$/ ;

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
	var obj_input = $.extend({}, arr_input);
	var request = $.ajax({  
                            url: "check_out3.php",
                            type: "POST",
                            data: obj_input,
                            dataType: "html"
                        });
    request.done( function( msg ) {
    	alert(msg);
    	reload_page();
       // console.log(msg);
       // location.href="list.php";
                            
    });
}
</script>
<style>
	.owner{
		background-color:#FFE8E8;
		width:1016px;
		
	}
	.main{
		width:1016px;
	}
	.exp{
		width:1016px;
		/*border:1px solid #999;*/
	}

	.child{
		padding-left: 50px;
		width:1016px;
		font-size:10px;
		
	}
	.child table{
		padding:5px;
		margin-bottom: 5px;
		background-color:#E8FFE8;
		/*border: 1px solid #999;*/
	}
	
	.cvalue{
		/*background-color:#FFF;*/
		padding-left: 1px;
	}
	.cClose{
		display: none;
	}
	.cOpen{
		display: '';
	}
	.xxx-button {
		color:#FFFFFF;
		font-size:12px;
		font-weight:normal;
		
		text-align: center;
		white-space:nowrap;
		height:20px;
		
		background-color: #a63c38;
	    border: 1px solid #a63c38;
	    border-radius: 0.35em;
	    font-weight: bold;
	    padding: 0 20px;
	    margin: 5px auto 5px auto;
	}
	.xxx-button:hover {
		background-color:#333333;
		border:1px solid #333333;
	}

</style>
</head>

<body>
<div style="width:1024px; margin-bottom:5px; height:22px; background-color: #CCC">
<div style="float:left;margin-left: 10px;"> <a href="instructions/IBookList.php">指示書</a> </div>
<div style="float:left;margin-left: 10px;"> <a href="/bank/list2.php">待修改資料</a> </div>
<?php
if ($_SESSION["member_id"] == '6' || $_SESSION["member_pDep"] == 5) { //個別權限顯示
?>
<div style="float:left;margin-left: 10px;"> <a href="/bank/BankTransProcess.php">出款進度</a> </div>
<?php } ?>
<?php
if ($_SESSION["member_bankcheck"] == '1') { //個別權限顯示
?>
<div style="float:left; margin-left: 10px;"> <font color=red><strong>未審核列表</strong></font></div>
<div style="float:left; margin-left: 10px;"> <a href="/bank/list_ok.php">已審核列表</a></div>
<?php } ?>
</div>
<div>
<div>待出帳(審核)列表</div>
<?php
for ($i = 0 ; $i < count($conBank) ; $i ++) {
		
	$sql = 'select * from tBankTrans where tOK<>"1" AND tVR_Code LIKE "'.$conBank[$i]['cBankVR'].'%" ORDER BY tOwner ASC';
	$rs = $conn->Execute($sql);	

	while (!$rs->EOF) {


		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['Total'] += $rs->fields["tMoney"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['count']++;
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tDate'] = $rs->fields["tDate"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['tVR_Code'] = $rs->fields["tVR_Code"];
		$data[$conBank[$i]['cBankVR']][$rs->fields['tOwner']][$rs->fields["tVR_Code"]]['data'][] = $rs->fields;

		$rs->MoveNext();
	}
}

for ($i = 0 ; $i < count($conBank) ; $i ++) {
	$bank = $conBank[$i]['cBankFullName'] ;
	if ($conBank[$i]['cBankMain']=='807') {
		$bank .= '('.$conBank[$i]['cBranchName'].')' ;
	}
	echo "<div style=\"font-weight:bold;color:#002060;\">～～～&nbsp;".$bank."案件&nbsp;～～～</div>";

	if (is_array($data[$conBank[$i]['cBankVR']])) {
		foreach ($data[$conBank[$i]['cBankVR']] as $key => $value) {
			echo "<div class=\"owner\">".$key."</div>";
			foreach ($value as $k => $v) {

				echo "<div class=\"exp\">";
					echo "<div class=\"main\">";
					echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" class=\"font12\"><tr>";
						echo "<td width=\"258\">";	
							echo "<input type=\"checkbox\" name=\"a".$v["tVR_Code"]."\"  onclick=\"checkALL('".$v["tVR_Code"]."')\"> 
									專屬帳號 <font color=\"blue\"><strong>".$v["tVR_Code"]."</strong></font>
									"; //<a class=\"iframe\" href=\"/bank/check_out.php?vr_code=".$rs->fields["tVR_Code"]."\"><strong>".$rs->fields["tVR_Code"]."</strong></a>
							if (preg_match("/96988000000008/",$v["tVR_Code"])) {
								echo '(利息)' ;		//台新利息
							}
							else if (preg_match("/99985000000000/",$v["tVR_Code"])) {
								echo '(利息)' ;		//永豐西門利息
							}
							else if (preg_match("/99986000000000/",$v["tVR_Code"])) {
								echo '(利息)' ;		//永豐城中利息
							}
							
						echo "</td>";
						echo "<td width=\"251\">待出帳總金額 ".$v["Total"]." 元</td>";
						echo "<td width=\"163\">筆數 ".$v["count"]."</td>";
						echo "<td>建檔時間 ".$v["tDate"]."</td>";
						echo "<td width=\"34\"><input type=\"button\" name=\"s".$v["tVR_Code"]."\" value=\"展開\" onclick=\"showChild('".$v["tVR_Code"]."')\"></td>";
					echo "</tr></table>";
					echo "</div>";
					echo "<div class=\"child cClose c".$v["tVR_Code"]."\"><table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"966\">";
					foreach ($v['data'] as $kk => $vv) {
						// print_r($vv);
						$sql = "select * from tBank where bBank3='".substr($vv['tBankCode'], 0,3)."' and bBank4='' limit 1";
					  	$rs1 = $conn->Execute($sql);	
					  	$bank = $rs1->fields["bBank4_name"];
					  	//
					  	$sql = "select * from tBank where bBank3='".substr($vv['tBankCode'],0, 3)."' and bBank4='".substr($vv['tBankCode'],3)."' limit 1";
						 // echo $sql."<br>";
					 	$rs2 = $conn->Execute($sql);	
					  	$bankBranch = $rs2->fields["bBank4_name"];

					  	switch ($vv["tCode"]){
							  case "01":
					        $_title = "聯行轉帳";
					        if ($rs->fields["tCode2"] == '虛轉虛') {
					          $_title = "虛轉虛";
					        }
							  	
							  break;
							  case "02":
							  	$_title = "跨行代清償";
							  break;
							  case "03":
							  	$_title = "聯行代清償";
							  break;
							  case "04":
							  	$_title = "大額繳稅";
							  break;
							  case "05":
							  	$_title = "臨櫃開票";
						        if ($rs->fields['tCode2'] == '臨櫃領現') {
						          $_title = '臨櫃領現';
						        }
							  break;
							  case "06":
							  	$_title = "利息";
							  break;
						}
						echo "<tr>";
							echo "<td><input type=\"checkbox\" name=\"check[]\" value=\"".$vv["tId"]."\"></td>";
							echo "<td><span class=\"ctitle\">類別</span> <span class=\"cvalue\">".$vv["tKind"]."</span><br><span class=\"ctitle\">交易類別</span><span class=\"cvalue\">&nbsp;".$_title."</span></td>";
							echo "<td><span class=\"ctitle\">解匯行</span> <span class=\"cvalue\">".$bank."</span><br><span class=\"ctitle\">分行別</span><span class=\"cvalue\">&nbsp;".$bankBranch."</span></td>";
							echo "<td><span class=\"ctitle\">戶名</span> <span class=\"cvalue\">".$vv["tAccountName"]."</span><br><span class=\"ctitle\">證號</span><span class=\"cvalue\">".$vv["tAccountId"]."</span></td>";
							echo "<td><span class=\"ctitle\">帳號</span><span class=\"cvalue\">".$vv["tAccount"]."</span></td>";
							echo "<td><span class=\"ctitle\">金額</span><span class=\"cvalue\">NT$ ".$vv["tMoney"]."元(不含匯費)</span></td>";
							echo "<td>";
								if ($vv['pdf'] != '') {
									echo "<input type=\"button\" value=\"指示書\" onclick=\"book(".$vv['bId'].",'".$vv['pdf']."')\" class=\"xxx-button\"/>";
								}
							echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>&nbsp;</td>";
						echo "<td cosplan=\"2\"><span class=\"ctitle\">附言(備註)</span><span class=\"cvalue\">&nbsp;".$vv["tTxt"]."</sapn>";
						if ($vv["tObjKind"] == '解除契約') {
			      			echo "<td ><span class=\"cvalue\">解約/終止履保</sapn>";
			      		}else{
			      			echo "<td ><span class=\"cvalue\">".$vv["tObjKind"]."</sapn>";
			      		}
      					echo "<td>&nbsp;</td>
						      <td>&nbsp;</td>
						      <td >&nbsp;</td>";
						
						echo "</tr>";
							echo "<tr><td colspan=\"7\"><hr /></td></tr>";
					}
					echo "</table></div>";
				echo "</div>";	
				echo "<div style=\"width:1016px;\"><hr></div>";
			}
			// echo "<pre>";
			// 	print_r($value);

			
					
		}
	}
	
	
}
// echo "<pre>";
// 	print_r($data);
?>

</div>
<div>
	<input type="button" value="審核通過" onclick="auth()" />
</div>
</body>
</html>