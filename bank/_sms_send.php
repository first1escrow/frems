<?php
//include('../web_addr.php') ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$sn = $_REQUEST["sn"];
$ts = $_REQUEST["ts"];
if ($_POST['cat']) {
	
	//檢查是否已經勾選，怕有同時操作問題
	$sql = '
		SELECT
			tStep1,
			tStep1Name,
			(SELECT pName FROM tPeopleInfo WHERE pId = tStep1Name) AS Name,
			tStep2,
			tStep2Name,
			(SELECT pName FROM tPeopleInfo WHERE pId = tStep2Name) AS Name2
		FROM
			tBankTrans
		WHERE
			tExport_nu="'.$sn.'"
			AND tSend != 1 
		GROUP BY
			tVR_Code,tObjKind
	' ;

	$rs = $conn->Execute($sql);

	if ($_POST['cat'] == 1 || $_POST['cat'] == 3) {
		if ($rs->fields['tStep1Name'] > 0 && $_SESSION['member_id'] != $rs->fields['tStep1Name']) {
			//_sms_send.php?sn=59115b1f5b234&ts=2017-05-09 14:01:03&tm=6503161
			echo "<script>alert('已有人審核第一階段，頁面將重新整理'); location.href='_sms_send.php?sn=".$sn."&ts=".$_REQUEST['ts']."&tm=".$_REQUEST['tm']."';</script>";
			die;
		}
	}

	if ($_POST['cat'] == 2 || $_POST['cat'] == 3 ) {
		if ($rs->fields['tStep2Name'] > 0 && $_SESSION['member_id'] != $rs->fields['tStep2Name']) {
			echo "<script>alert('已有人審核第二階段，頁面將重新整理');location.href='_sms_send.php?sn=".$sn."&ts=".$_REQUEST['ts']."&tm=".$_REQUEST['tm']."';</script>";
			die;
		}
	}



	// for ($i=0; $i < count($_POST['tId']); $i++) { 
	// 	// echo 'sms_send_'.$_POST['tId']."<br>";
	// 	if ($_POST['cat'] == 1 || $_POST['cat'] == 3) {
	// 		if ($_POST['sms_send_'.$_POST['tId'][$i]]) {
	// 			// echo $_POST['sms_send_'.$_POST['tId'][$i]]."_";
	// 			// $sql = "UPDATE tBankTrans SET tStep1 = 1 WHERE tId ='".$_POST['tId'][$i]."'";
	// 			$sql = "UPDATE tBankTrans SET tStep1 = 1,tStep2 = 1 WHERE tId ='".$_POST['tId'][$i]."'";
	// 			$conn->Execute($sql);
	// 			// echo $sql."<br>";
	// 		}
	// 		//審核人都要寫上
	// 		// $sql = "UPDATE tBankTrans SET tStep1Name = '".$_SESSION['member_id']."',tStep1Time = '".date('Y-m-d H:i:s')."' WHERE tId ='".$_POST['tId'][$i]."'";
	// 		$sql = "UPDATE tBankTrans SET tStep1Name = '".$_SESSION['member_id']."',tStep1Time = '".date('Y-m-d H:i:s')."',tStep2Name = '".$_SESSION['member_id']."',tStep2Time = '".date('Y-m-d H:i:s')."' WHERE tId ='".$_POST['tId'][$i]."'";
	// 		$conn->Execute($sql);
	// 	}

	// }

	
	
}

$sql = '
	SELECT
		tId,
		tVR_Code,
		tObjKind,
		tMoney,
		tMemo,
		tTxt,
		COUNT(tObjKind) as count_objkind,
		SUM(tMoney) as total_M,
		tKind,
		tBuyer,
		tSeller,
		tStep1,
		tStep1Name,
		(SELECT pName FROM tPeopleInfo WHERE pId = tStep1Name) AS Name,
		tStep2,
		tStep2Name,
		(SELECT pName FROM tPeopleInfo WHERE pId = tStep2Name) AS Name2

	FROM
		tBankTrans
	WHERE
		tExport_nu="'.$sn.'"
		AND tSend != 1 
	GROUP BY
		tVR_Code,tObjKind
' ;
// echo $sql."<br><br>\n" ;
$rs = $conn->Execute($sql) ;
$cat = 0; //0:無權限 1有權限更改(step1) 2有權限更改(step2)  3:最大權限() 
while (!$rs->EOF) {
	
	$checkStep = $rs->fields['tStep1Name'];
	
	$checkStep2 = $rs->fields['tStep2Name'];
	


	
	// if ($_SESSION['member_id'] == 6) { //
	// 	$cat = 3;
	// }else
	// echo $checkStep."<bR>";
	

	if ($rs->fields['tObjKind'] == '仲介服務費') {
		$sql = '
			SELECT
				tId,
				tVR_Code,
				tObjKind,
				tMoney,
				tMemo,
				tTxt,
				tObjKind as count_objkind,
				tMoney as total_M,
				tKind,
				tBuyer,
				tSeller,
				tStep1,
				tStep1Name,
				(SELECT pName FROM tPeopleInfo WHERE pId = tStep1Name) AS Name,
				tStep2,
				tStep2Name,
				(SELECT pName FROM tPeopleInfo WHERE pId = tStep2Name) AS Name2
			FROM
				tBankTrans
			WHERE
				tExport_nu="'.$sn.'"
				AND tVR_Code="'.$rs->fields['tVR_Code'].'"
				AND tObjKind="仲介服務費"
				AND tSend != 1 
		' ;
		//echo 'RSA='.$sql."<br>\n" ;
		$rsA = $conn->Execute($sql) ;
		while (!$rsA->EOF) {
			$list[] = $rsA->fields ;
			if ($rsA->fields['tStep1'] == 1) {
				$stepchecked[] = $rsA->fields['tId'];
				
			}

			if ($rsA->fields['tStep2'] == 1) {
				$stepchecked1[] = $rsA->fields['tId'];
			}

			$rsA->MoveNext() ;
		}
	}
	else {
		if ($rs->fields['tStep1'] == 1) {
			$stepchecked[] = $rs->fields['tId'];
			
		}

		if ($rs->fields['tStep2'] == 1) {
			$stepchecked1[] = $rs->fields['tId'];
		}
		$list[] = $rs->fields ;
	}
	
	
	$rs->MoveNext() ;
}

// $cat = 1;

if (($checkStep > 0 && $checkStep != $_SESSION['member_id'] && $checkStep2 == 0) || $checkStep2 > 0 && $checkStep2 == $_SESSION['member_id']) { //(第一步已審核且第二審人不等於第一審合人且還未審核第二步驟) 或 已審核第二步驟 且是該審核者
	$cat = 2;
}elseif (($checkStep > 0 && $checkStep == $_SESSION['member_id']) || $checkStep == 0) {
	$cat = 1;
}

// echo $cat ;

$readonly = ((($cat == 1 && $checkStep == 0) || $cat ==3  && $checkStep == 0) && $checkStep2 == 0) ? '':'disabled=disabled';
$readonly2 = (($cat == 2 && $checkStep2 == 0) || ($cat ==3 && $checkStep2 == 0)) ? '':'disabled=disabled';



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>發送簡訊</title>
<script src="/js/jquery-1.7.2.min.js"></script>
<script>
function sendSMS(){
	var v= new Array();
	var allChkbox=$('input[name^="sms_send_"]');
	jQuery.each(allChkbox, function(i, singlecheckbox) {
		if (singlecheckbox.checked) {
			_name = singlecheckbox.id
			v.push(singlecheckbox.value);
			
		}

		// alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
	});
	
	if (confirm('簡訊發送確認?')) {
        //
		if (v.length > 0) {
		
		//送出前轉換成JSON格式
  		var jsonText = JSON.stringify({ datas: v});
		//console.log(jsonText);
		//url = '<?=$web_addr?>/bank/_module_sms_json.php?json=' + jsonText;
		url = '_module_sms_json.php?json=' + jsonText;
		
		$.ajax({
		   url: url,
		   error: function(xhr) {
			  alert ("系統忙碌中！");
		   },
		   success: function(response) {
				console.log(response);
			 	//alert("已全部發送!"); 
				// alert(response) ;
				var t=$('span[id^="_sms_"]');
				// console.log(response);
				t.html('');
		   }
		});
	  }
		//    
    } else {
		
	}
	
}
function checkAll(){
	if ( $('input[name="checkbox"]').attr("checked")) {
	
		var allChkbox=$('input[name^="sms_send_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			
			if (!singlecheckbox.checked) {
				_name = singlecheckbox.id
				$('input[name="'+_name+'"]').attr("checked",true)
				
			} 
			
			//alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});
		
	} else {
		
		var allChkbox=$('input[name^="sms_send_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			
			if (singlecheckbox.checked) {
				_name = singlecheckbox.id
				$('input[name="'+_name+'"]').attr("checked",false)
				
			} 
			
			//alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});

	}
}

function checkAll2(){
	if ( $('input[name="checkbox2"]').attr("checked")) {
	
		var allChkbox=$('input[name^="step2_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			
			if (!singlecheckbox.checked) {
				_name = singlecheckbox.id
				$('input[name="'+_name+'"]').attr("checked",true)
				
			} 
			
			//alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});
		
	} else {
		
		var allChkbox=$('input[name^="step2_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			
			if (singlecheckbox.checked) {
				_name = singlecheckbox.id
				$('input[name="'+_name+'"]').attr("checked",false)
				
			} 
			
			//alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});

	}
}
function show_tip(x,y){

	var _obj = "#txt_" + x;
	var _obj2 = "#sms_txt_" + x;
	var _obj3 = "#_sms_" + x;
	//
	//url = '<?=$web_addr?>/sms/test2.php?tid='+x + '&yn=' + y ;
	url = '/sms/test2.php?tid='+x + '&yn=' + y ;
	$.ajax({
		   url: url,
		   error: function(xhr) {
			  alert ("系統忙碌中！");
		   },
		   success: function(response) {
				$(_obj2).html(response);
				$(_obj3).html('');
			 	//alert(response); 
		   }
		});
	//
	$(_obj).show();
	//$("#apDiv1").css("top", (event.pageY - xOffset) + "px").css("left", (event.pageX + yOffset) + "px");
	//alert(mX + "/" + mY);
	
}

function checkSmsTarget(){
	

	// alert(v2);
	if (<?=$cat?> == 2 || <?=$cat?> ==3) {
		var v= new Array();
		var v2 = new Array();
		

		var allChkbox=$('input[name^="sms_send_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			if (singlecheckbox.checked) {
				_name = singlecheckbox.id
				v.push(singlecheckbox.value);
				
			}

			// alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});

		var allChkbox=$('input[name^="step2_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			if (singlecheckbox.checked) {
				_name = singlecheckbox.id
				v2.push(singlecheckbox.value);
				
			}

			// alert(singlecheckbox.value+" is "+ singlecheckbox.checked);
		});
		var check = 1; //0沒有符合 1都符合
		for (var j = 0; j < v.length; j++) {
			var cc = 0;
			for (var i = 0; i < v2.length; i++) {
				
				// alert(v[j]+"_"+v2[i]);
				if (v[j] == v2[i]) {
					cc = 1;
				}
			}

			if (cc == 0) {
				check = cc;
			}
			
		}

		if (v.length != v2.length) {
			check = 0;
		}
		
		if (check == 0) {
			alert("勾選對象與第一審核者勾選項目不相符");
			return false;
		}else{
			$('[name="form"]').submit();
		}
		
	}else if(<?=$cat?> == 1){
		var v= new Array();
		
		var allChkbox=$('input[name^="sms_send_"]');
		jQuery.each(allChkbox, function(i, singlecheckbox) {
			if (singlecheckbox.checked) {
				
				v.push(singlecheckbox.value);
				
			}

		});

		if (v.length > 0) {
			$('[name="form"]').submit();
		}else{
			alert('請勾選項目');
		}
	}else{

		$('[name="form"]').submit();
	}

	
	
}
</script>
<style>
	.btn {
    color: #000;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #E4BEB1;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
    border-radius:0.5em 0.5em 0.5em 0.5em;
}
.btn:hover {
    color: #00008F;
    font-size:16px;
    background-color: #FFFFFF;
    border: 1px solid #DDDDDD;
}
.btn.focus_end{
    color: #000;
    font-family: Verdana;
    font-size: 16px;
    font-weight: bold;
    line-height: 20px;
    background-color: #E4BEB1;
    text-align:center;
    display:inline-block;
    padding: 8px 12px;
    border: 1px solid #DDDDDD;
    border-radius:0.5em 0.5em 0.5em 0.5em;
}
</style>
</head>

<body>
<div style="width:835px;text-align:right;padding-bottom:10px">
	審核人一：<?=$list[0]['Name']?>
	<!-- 審核人二：<?=$list[0]['Name2']?> -->
</div>
<form action="" method="POST" name="form">
<table width="830" border="1">
  <tr>
    <td width="122">保證號碼</td>
    <td width="144">項目</td>
    <td width="287">附言</td>
    <td width="108">金額</td>
    <td width="40">&nbsp;</td>
    <td width="80">
		<?php
			$checked = (count($list)==count($stepchecked)) ? 'checked=checked':'';
			// echo count($list)."_".count($stepchecked);
		?>
    	<input type="checkbox" name="checkbox" id="checkbox" <?=$checked?> <?=$readonly?> onchange="checkAll();"  />審核一
    </td>
   
    <td width="80">
    	
    </td>
   
  </tr>
<?php //while( !$rs->EOF ) { 
for ($i = 0 ; $i < count($list) ; $i ++) {
	//$sql1 = 'SELECT tPID,tKind FROM tSMS_Log WHERE tKind="'.$list[$i]['tObjKind'].'" AND tTransId="'.$list[$i]['tId'].'";' ;
	$sql1 = 'SELECT tPID,tKind FROM tSMS_Log_View WHERE tKind="'.$list[$i]['tObjKind'].'" AND tTransId="'.$list[$i]['tId'].'"  AND sSend_Time >= "'.$ts.'";' ;
	// echo $sql1."<br>";
	$rs1 = $conn->Execute($sql1) ;
	$_check = $rs1->RecordCount() ;

	$sql2 = "SELECT sCertifiedId FROM tSMS_Send_Log WHERE sKind = '".$list[$i]['tObjKind']."' AND sTransId ='".$list[$i]['tId']."' AND sSend_Time >= '".$ts."' ";
	$rs2 = $conn->Execute($sql2);
	$_check2 = $rs2->RecordCount() ;

?>
  <tr>
    <td><?php echo substr($list[$i]["tVR_Code"],5);?>&nbsp;</td>
    <td><?php echo $list[$i]["tObjKind"];?>&nbsp;</td>
    <td><?php echo $list[$i]["tTxt"];?>&nbsp;</td>
    <td><?php echo $list[$i]["total_M"];?>&nbsp;</td>
    <td><span id="_sms_<?php echo $list[$i]["tId"];?>"><?php if ( trim($list[$i]["tObjKind"]) != '其他') {?>
   <?php 
   if ($_check2 > 0 && $_check == 0 && $list[$i]["tId"] != 260665) { //260665因沒有項目先做排除的動作
   	echo "<a href=\"Javascript: show_tip('".$list[$i]["tId"]."','y');\">發送中</a>";
   }else if ($_check == 0 && $list[$i]["tId"] != 260665) { 
   			if ($list[$i]['tStep1'] > 0 && $list[$i]['tStep2'] > 0 ) { //個別看是否送
   	?>
    <a href="Javascript: show_tip('<?php echo $list[$i]["tId"];?>','y');"><img src="images/sms.jpg" width="30px" height="30px" alt="發送簡訊" title="發送簡訊" border="0" /></a>
   <?php } } ?>
	<?php } ?></span></td>
    <td>
    	<?php 
    		$checked = (@in_array($list[$i]['tId'], $stepchecked)) ? 'checked=checked':'';
    		
    		if ( trim($list[$i]["tObjKind"]) != '其他') { ?>
    		<input type="checkbox" name="sms_send_<?php echo $list[$i]["tId"];?>" id="sms_send_<?php echo $list[$i]["tId"];?>" value="<?php echo $list[$i]["tId"];?>" <?=$checked?> <?=$readonly?>/> <input type="hidden" name="tId[]" value="<?php echo $list[$i]["tId"];?>">
    	<?php } ?>
    </td>
  	
  	<td>
  		
  	</td>
  	
  </tr>
 
  <tr bgcolor="#FFFF00" id="txt_<?php echo $list[$i]["tId"];?>" style="display:none">
    <td colspan="6">簡訊內容: <span id="sms_txt_<?php echo $list[$i]["tId"];?>"></span></td>
  </tr>
  <?php } ?>
   
</table>
<div style="width:835px;text-align:center;padding-top:20px">
	<?php
		if ($_SESSION["member_bankcheck"] == '1') {
	 ?>
	<!-- <input type="button" value="審核" name="go" onclick="checkSmsTarget()" class="btn" /> -->
	<input type="button" value="發送簡訊" onclick="sendSMS()" class="btn" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<?php

	if ($checkStep > 0 ) { ?>
	
	<?php } } ?>
	
	<input type="hidden" name="cat"  value="<?=$cat?>" />
	

</div>

</form>



		<!-- <table width="835" border="0">
		    <tr>
		      <td align="right"><a href="Javascript: sendSMS();">發送簡訊</a></td>
		    </tr>
		</table> -->

</body>
</html>
