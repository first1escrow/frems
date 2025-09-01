<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;

$eid = $_POST['eid'] ;
$cid = $_POST['cid'] ;

if (($eid) && ($cid)) {
	$sql = '
		SELECT 
			A.*,
			B.ePayTitle as info,
			B.eLender as money,
			(SELECT cName FROM tCategoryExpense WHERE A.eItem=cId) as cItem
		FROM 
			tExpenseDetail AS A
		JOIN
			tExpense AS B ON B.id=A.eExpenseId
		WHERE 
			A.eExpenseId="'.$eid.'" 
			AND A.eCertifiedId="'.$cid.'"
	; ' ;
	
	$rs = $conn->Execute($sql) ;
	$i = 0 ;
	while (!$rs->EOF) {
		$list[$i++] = $rs->fields ;
		$rs->MoveNext() ;
	}
	
	if (count($list) > 0) {
	
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>款項明細</title>
<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>-->
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript">

</script>
<style>
#tbl {
	 border-collpase:collpase;
	 font-size: 9pt;
	 text-align: center;
}
.cells {
	border: 1px solid #CCC;
	padding: 5px;
}
</style>
</head>
<body>
<?php 
if ($list[0]['info']) {
	echo '摘要：'.$list[0]['info'].'　' ;
}
echo 'NT.$'.number_format(substr($list[0]['money'],0,-2)) ;
?>
<table id="tbl">
	<tr>
		<td class="cells">對象</td><td class="cells">用途類別</td><td class="cells">金額</td>
	</tr>
<?php
for ($i = 0 ; $i < count($list) ; $i ++) {
	echo "\t<tr>\n" ;
	
	//身分
	if ($list[$i]['eTarget'] == '2') {
		$list[$i]['eTarget'] = '賣方' ;
	}
	else {
		$list[$i]['eTarget'] = '買方' ;
	}
	echo "\t\t<td class='cells'>".$list[$i]['eTarget']."</td>\n" ;
	##
	
	//類別
	echo "\t\t<td class='cells'>".$list[$i]['cItem']."</td>\n" ;
	##
	
	echo "\t\t<td class='cells'>".number_format($list[$i]['eMoney'])."</td>\n" ;	
	
	echo "\t</tr>\n" ;
}
?>
</table>
</body>
</html>



<?php
	}
}
?>