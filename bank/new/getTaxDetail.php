<?php
include_once '../../session_check.php' ;
include_once '../../openadodb.php' ;

$cid = $_REQUEST['cid'] ;
$eid = $_REQUEST['eid'] ;
$t = $_REQUEST['t'] ;

if ($eid) {
	$eid = preg_replace("/_$/","",$eid) ;
	$eid_arr = explode("_",$eid) ;
}

if ($cid) {
	$sql = "SELECT tId,tMoney,tObjKind,tBankLoansDate FROM tBankTrans WHERE tObjKind2 = '01' AND tObjKind2Item ='' AND tMemo = '".$cid."'";
	$rs = $conn->Execute($sql);
	
	while (!$rs->EOF) {
		$list[] = $rs->fields ;
		$rs->MoveNext() ;
	}
	
?>
<html>
<head>
<script type="text/javascript">
$(document).ready(function() {
	$('#all').on('click', function () {
	                    
	    if($("#all").prop("checked")){

	        $('input[name="eid[]"]').each( function(i) {
	         $(this).prop("checked", true);
	    	});

	    }else
	    {
	        $('input[name="eid[]"]').each( function(i) {
	            $(this).prop("checked", false);
	    	});
	                        
	    }
	});
}) ;

function gogo() {
	var totalM = 0 ;
	var ee = new Array();
	var cc = 0;
	var item = new Array();
	$('[name="eid[]"]').each(function() {
		if ($(this).prop('checked')) {
			var m = parseInt($(this).attr('title')) ;
			totalM = totalM + m ;
			// ee = ee + $(this).val() + '_' ;
			ee[cc] = $(this).val();
			cc++;
		}		
	}) ;
	
	parent.$('.taxM').each(function () {
		var d = $(this).prop('title') ;
		if (d == "<?=$t?>") {
			$(this).val(totalM) ;
		}
	}) ;
	parent.$('[name="taxReturnPayId"]').val(ee.join('_')) ;
	parent.$('.ajax1').attr({'href':'getTaxDetail.php?cid=<?=$cid?>&t=<?=$t?>&eid='+ee}) ;

	
	// $('input:checkbox:checked[name="eid[]"]').each(function(i) { 
		
	// 	var txt = $("#item"+this.value).text()
		
	// 	if (txt =='土地增值稅') {txt='增值稅'}

	// 	item[i] = txt;

	// 	// alert(item);
	// });

	// parent.$(".t_txt").val(item.join('＋'));
	
	cancel() ;
}

function cancel() {
	parent.$('.ajax').colorbox.close() ;
}
</script>
<style>
td {
	text-align: center;
	border: 1px solid #CCC;
	padding: 5px;
}
</style>
</head>
<body>
<form method="POST" name="myform">
	<h3>返還公司代墊</h3>
<?php
if (count($list) > 0) {
	echo '
	<table width="400px" border="0" cellspacing="0px" cellpadding="0px">
		<tr style="font-weight:bold;color:#000080;">
			<td><input type="checkbox"  id="all"></td><td>出款日期</td><td>款項明細</td><td>金額</td>
		</tr>
	' ;
	//tId,tMoney,tObjKind
	foreach ($list as $k => $v) {
		$checked = '' ;
		for ($i = 0 ; $i < count($eid_arr) ; $i ++) {
			if ($eid_arr[$i] == $v['tId']) {
				$checked = ' checked="checked"' ;
			}
		}
		echo '
		<tr>
			<td><input type="checkbox"'.$checked.' name="eid[]" value="'.$v['tId'].'" title="'.$v['tMoney'].'"></td>
		' ;
		
			echo '<td>'.$v['tBankLoansDate'].'</td>' ;
		
		echo '<td><span id="item'.$v['tId'].'">'.$v['tObjKind'].'</span>&nbsp;</td>' ;
		echo '<td>'.number_format($v['tMoney']).'</td>' ;
		echo '
		</tr>
		' ;
	}
	
	echo '</table>' ;
}
?>
<div style="width:400px;text-align:center;margin-top:10px;">
	<input type="button" value="選擇" onclick="gogo()">&nbsp;
	<input type="button" value="取消" onclick="cancel()">
	<input type="hidden" name="update" value="">
</div>
</form>
</body>
</html>
<?php
}
?>