<?php
include_once '../../session_check.php' ;
include_once '../../openadodb.php' ;

$cid = $_REQUEST['cid'] ;
$eid = $_REQUEST['eid'] ;
$t = $_REQUEST['t'] ;
$cat = (int)$_REQUEST['cat'];

if ($eid) {
	$eid = preg_replace("/_$/","",$eid) ;
	$eid_arr = explode("_",$eid) ;
}

if ($cid) {
	$sql = "SELECT (SELECT cBankName FROM tContractBank WHERE cBankCode=cBank) AS bank FROM tContractCase WHERE cCertifiedId = '".$cid."'";
	$rs = $conn->Execute($sql);

	$bank = $rs->fields['bank'];
	
		// $bank ='';
		##
	if ($bank == '台新' && $cat == '1') { //申請代墊的只顯示賣  OR eTarget = 2
		$sql = '
			SELECT 
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as item
			FROM 
				tExpenseDetail AS a
			WHERE 
				eCertifiedId="'.$cid.'" 
				AND eOK="" AND (eObjKind2 = "01")
			ORDER BY 
				eId 
			ASC;
		' ;
	}elseif($bank == '台新'){
		##台新暫時不抓值皆出代書的帳戶，開放不代墊
		//AND eTarget != 2
		$sql = '
			SELECT 
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as item
			FROM 
				tExpenseDetail AS a
			WHERE 
				eCertifiedId="'.$cid.'"  AND eObjKind2 != "01"
				AND eOK="" 
			ORDER BY 
				eId 
			ASC;
		' ;
	}else{
		$sql = '
			SELECT 
				*,
				(SELECT cName FROM tCategoryExpense WHERE cId=a.eItem) as item
			FROM 
				tExpenseDetail AS a
			WHERE 
				eCertifiedId="'.$cid.'" 
				AND eOK=""
			ORDER BY 
				eId 
			ASC;
		' ;
	}

	
	
	$rs = $conn->Execute($sql) ;
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
	parent.$('[name="taxPayId"]').val(ee.join('_')) ;
	parent.$('.ajax1').attr({'href':'getExpenseDetail.php?cid=<?=$cid?>&t=<?=$t?>&eid='+ee}) ;

	
	$('input:checkbox:checked[name="eid[]"]').each(function(i) { 
		
		var txt = $("#item"+this.value).text()
		
		if (txt =='土地增值稅') {txt='增值稅'}

		item[i] = txt;

		// alert(item);
	});

	var txt = parent.$(".t_txt").val();
	

	parent.$(".t_txt").val(item.join('＋')+txt);
	
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
<?php
if (count($list) > 0) {
	echo '
	<table width="400px" border="0" cellspacing="0px" cellpadding="0px">
		<tr style="font-weight:bold;color:#000080;">
			<td><input type="checkbox" id="all"></td><td>對象</td><td>款項明細</td><td>金額</td>
		</tr>
	' ;
	
	foreach ($list as $k => $v) {
		$checked = '' ;
		for ($i = 0 ; $i < count($eid_arr) ; $i ++) {
			if ($eid_arr[$i] == $v['eId']) {
				$checked = ' checked="checked"' ;
			}
		}
		echo '
		<tr>
			<td><input type="checkbox"'.$checked.' name="eid[]" value="'.$v['eId'].'" title="'.$v['eMoney'].'"></td>
		' ;
		
		if ($v['eTarget'] == '2') {
			echo '<td>賣方</td>' ;
		}
		else if ($v['eTarget'] == '3') {
			echo '<td>買方</td>' ;
		}
		else {
			echo '<td>&nbsp;</td>' ;
		}
		
		echo '<td><span id="item'.$v['eId'].'">'.$v['item'].'</span>&nbsp;</td>' ;
		echo '<td>'.number_format($v['eMoney']).'</td>' ;
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