<?php
include_once '../web_addr.php' ;
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$_POST = escapeStr($_POST) ;

if ($_POST['cId']) {

	// if ($_POST['cat'] == 0) {
	// 	$sql = "UPDATE tContractCase SET cFeedBackClose = 2 WHERE cCertifiedId = '".$_POST['cId']."'";

		
	// }else{
	// 	$sql = "UPDATE tContractCase SET cFeedBackClose = 1 WHERE cCertifiedId = '".$_POST['cId']."'";

	// }
	
	// if ($conn->Execute($sql)) {
	// 		$msg = "<script>alert('更改成功')</script>";
	// }

}
$cid = $_POST['cid'];//保號


if ($cid) {
	$sql_search .= " cCertifiedId = '".$cid."'";

	$sql = "SELECT cCertifiedId AS CertifiedId, cFeedBackClose, cFeedBackScrivenerClose FROM tContractCase WHERE ".$sql_search;
	$rs = $conn->Execute($sql);

	$i = 0;
	while (!$rs->EOF) {
        $list[$i]=$rs->fields;
        ##列顏色
        if ($i % 2 == 0) { $list[$i]['color'] = "#FFFFFF" ; }
        else { $list[$i]['color'] = "#F8ECE9" ; }
        ##按鈕
        if ($list[$i]['cFeedBackClose'] == 1 or $list[$i]['cFeedBackScrivenerClose'] == 1) {
            $list[$i]['cInvoiceText'] = '關閉';
        } else {
            $list[$i]['cInvoiceText'] = '開啟';
        }
//        if ($list[$i]['cFeedBackScrivenerClose'] == 1) {
//            $list[$i]['ScrivenerText'] = '關閉';
//        }else{
//            $list[$i]['ScrivenerText'] = '開啟';
//        }
		$i++;
		$rs->MoveNext();
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>解鎖回饋金</title>
	<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript">

	$(document).ready(function() {
	});
	function contract(cid) {
		$('[name="id"]').val(cid) ;
		//alert($("#id").val()) ;
		$('[name="form_edit"]').submit() ;
	}

	function search(){
		// alert('----');
		$("[name='search_form']").attr('action', 'unLockCase.php');

		$("[name='search_form']").submit();
	}
	function contract_invstatus(cid, st, type){
		// $("[name='cid']").val(cid);
		$.ajax({
			url: 'FeedBackCaseStaus.php',
			type: 'post',
			dataType: 'html',
			data: {'cid': cid, 'status':st, 'type':type},
		}).done(function(txt) {
				// console.log(txt);
			if (txt == 'ok') {
				// $("form1[name='cid']").val(cid);
				$("[name='form1']").attr('action', 'unLockCase.php');

				$("[name='form1']").submit();


			}
		});
		// alert($("[name='cid']").val());
	}
	</script>
	<style>
		.xxx-input {
		    color: #666666;
		    font-size: 14px;
		    font-weight: normal;
		    background-color: #FFFFFF;
		    text-align: left;
		    height: 24px;
		    padding: 0 5px;
		    border: 1px solid #CCCCCC;
		    border-radius: 0.35em;
		}
		.ra1 input[type="radio"] {/*隱藏原生*/
			/*display:none;*/
			position: absolute;
			left: -9999px;
		}
		.ra1 input[type="radio"] + label span {
			display:inline-block;
			width:20px;
			height:20px;
			margin:0px 4px 0 0;
			vertical-align:-4px;
			background:url("../images/check_radio_sheet2.png") -40px top no-repeat;
			cursor:pointer;
			background-size:80px 20px;
			transition: none;
			-webkit-transition:none;
		}
		.ra1 input[type="radio"]:checked + label span {
			background:url("../images/check_radio_sheet2.png") -60px top no-repeat;
			background-size:80px 20px;
			transition: none;
			-webkit-transition:none;
		}
		.ra1 label {
			color: #333333;
			font-size: 16px;
			font-weight: normal;
			cursor:pointer;
			white-space: nowrap;
			/*float:left;*/
			margin: 10px 20px 10px 0px;
			/*-webkit-appearance: push-button;
			-moz-appearance: button;*/
		}
		

		/* class clear */
		.xxx-clear:before, 
		.xxx-clear:after, 
		.xxx-clear::before, 
		.xxx-clear::after {
			content: "";
			display: table;
		}
		.xxx-clear:after, 
		.xxx-clear::after {
			clear: both;
		}
		.xxx-clear {
			zoom: 1;
		}

		.block{
			width: 500px;
			
			border: 1px solid ;
		}

	</style>
</head>
<body>
<h3>回饋案件解鎖/鎖住</h3>
<form name="form_edit" method="POST" action="/escrow/formbuyowneredit.php" target="_blank">
	<input type="hidden" name="id" value='' />
</form>
<form name="search_form" method="POST">
		
	<table cellspacing="0" cellpadding="0" style="width:100%;">
												
		<tr>
			<th style="background-color:#E4BeB1;text-align:center;height:40px;">保證號碼</th>
			<td style="background-color:#F8ECE9;text-align:left;height:40px;padding-left:10px;">
				<input type="text" name="cid" value="" class="xxx-input">&nbsp;&nbsp;
				<input type="button" value="查詢" onclick="search()" name="btn">
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
	</table>
</form>
<form name="form1" method="POST">
	<input type="hidden" name="cid" value="<?=$cid?>">
	<table cellspacing="0" cellpadding="0" style="width:100%;" id="loc">
		<tr style="background-color:#E4BeB1;text-align:center;height:40px;">
			<th width="15%">保證號碼</th>
            <th width="30%">目前狀態</th>
            <th width="40%">功能</th>
		</tr>
		<?php
		if (is_array($list)) {
			foreach ($list as $k => $v) { ?>
				<tr style="text-align:center;background-color:<?=$v['color']?>;height:40px;">
					<td><a href="#" onclick="contract('<?=$v['CertifiedId']?>')"><?=$v['CertifiedId']?></a><input type="hidden" name="CertifiedId" value=""></td>
					<td><?=$v['cInvoiceText']?></td>
					<td>
					    <?php
					    	if ($v['cFeedBackClose'] == 1 or $v['cFeedBackScrivenerClose'] == 1) { ?>
					    		<input type="button" onclick="contract_invstatus('<?=$v['CertifiedId']?>', 2, 'A')" value="開啟" >
						
					    <?php }else{ ?>
					    		<input type="button" onclick="contract_invstatus('<?=$v['CertifiedId']?>',1, 'A')" value="關閉" >
					    <?php } ?>
					</td>
				</tr>
			<?php } 
		} ?>

											
		<!-- <{foreach from=$data key=key item=item}> -->
		
		<!-- <{/foreach}> -->
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
												

	</table>
</form>
<!-- 	<form action="" method="POST">
	<center>
	<div class="block">
		<div style="line-heigh:50px;">
			<span class="ra1"><input type="radio" name="cat" value="0" id="cat1" checked><label for="cat1"><span></span>解鎖</label></span>
			<span class="ra1"><input type="radio" name="cat" value="1" id="cat2" ><label for="cat2"><span></span>鎖住</label></span>
		</div>
		<div class="xxx-clear"></div>
		<div style="line-heigh:20px;">保證號碼：<input type="text" name="cId" maxlength="9" class="xxx-input"></div>
		<div class="xxx-clear"></div>	
		
		<div style="line-heigh:20px;"><input type="submit" value="送出"></div>
	</div>
	</center>
	</form>
	<div class="xxx-clear"><?=$msg?></div> -->
</body>
</html>