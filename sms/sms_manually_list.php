<?php
include_once '../openadodb.php' ;
include_once '../session_check.php' ;


$_GET = escapeStr($_GET) ;
$cat = $_GET['cat'];
$line = $_GET['line'];

if ($cat == 's' || $cat == 's1') {
	$exceptsId = array(632, 575,552,620,411,224) ;

	if ($cat == 's1') {
		$str = ' AND sCategory = 1';
	}

	$sql = "SELECT sMobileNum,sId,CONCAT('SC',LPAD(sId,4,'0')) as Code,sOffice FROM tScrivener WHERE sStatus = 1 AND sId NOT IN (".@implode(',',$exceptsId).") ".$str." ORDER BY sId ASC";
	
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$data[] = $rs->fields;

		$rs->MoveNext();
	}

	$c = 0;
	for ($i=0; $i < count($data); $i++) { 
		
		$sql = "SELECT sMobile,sName FROM tScrivenerSms WHERE sScrivener ='".$data[$i]['sId']."' AND sDel = 0 GROUP BY sMobile";

		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			// continue;
			$check = true;
			if ($cat == 's' && $line == 1) {
				$check = checkLineMobile($data[$i]['sMobileNum'],$rs->fields['sMobile']);
			}
			
			if ($check) {
				if ($rs->fields['sMobile'] != '') {
					$mobile[$c]['name'] = $rs->fields['sName'];
					$mobile[$c]['mobile'] = $rs->fields['sMobile'];
					$mobile[$c]['code'] = $data[$i]['Code'];
					$mobile[$c]['office'] = $data[$i]['sOffice'];
					$c++;
				}
			}
			
				
			$rs->MoveNext();
		}
	}

	
}elseif ($cat == 'b') {
	$sql = "SELECT
				*,
				CONCAT((SELECT bCode FROM tBrand AS b WHERE b.bId=bBrand),LPAD(bId,5,'0')) as Code,
				(SELECT bName FROM tBrand AS b WHERE b.bId=bBrand) AS brand
			FROM
				tBranch WHERE bStatus = 1";
			
	$rs = $conn->Execute($sql);

	while (!$rs->EOF) {
		$data[] = $rs->fields;

		$rs->MoveNext();
	}
	$c = 0;
	for ($i=0; $i < count($data); $i++) { 
		$sql = "SELECT bMobile,bName FROM tBranchSms WHERE bBranch ='".$data[$i]['bId']."' AND bDel = 0 AND bCheck_id = 0 GROUP BY bMobile";
		$rs = $conn->Execute($sql);

		while (!$rs->EOF) {
			if ($rs->fields['bMobile'] != '') {

				$mobile[$c]['name'] = $rs->fields['bName'];
				$mobile[$c]['mobile'] = $rs->fields['bMobile'];
				$mobile[$c]['code'] = $data[$i]['Code'];
				$mobile[$c]['brand'] = $data[$i]['brand'];
				$mobile[$c]['store'] = $data[$i]['bStore'];
			}
				$c++;
			$rs->MoveNext();
		}
	}
}

if ($line == 1) {
	$checked = "checked=checked";
}

$tmp = filter_array($mobile);
unset($mobile);

$mobile = $tmp;

function filter_array($a) {
		
		$count=count($a);

		
		for ($i = 0 ; $i < $count ; $i ++) {

			if ($a[$i]['mobile']!='') { 
				$b[$a[$i]['mobile']] ++ ;
			}

			if ($b[$a[$i]['mobile']] > 1) {

				
					unset($a[$i]) ;
				
				
			}

		}
	
		$b = array_merge($a) ;

		return $b ;
	}
// echo @implode(',', $mobile);

function checkLineMobile($mobile,$mobile2){
	global $conn;
	$sql = "SELECT lId FROM tLineAccount WHERE lIdentity = 'S' AND lStatus = 'Y' AND lId NOT IN(4,7,8,12,13,817) AND lLineId  != '' AND (lCaseMobile = '".$mobile."' OR lCaseMobile = '".$mobile2."' OR lCaseMobile2 = '".$mobile."' OR lCaseMobile2 = '".$mobile2."')";

	$rs = $conn->Execute($sql);

	if ($rs->fields['lId']) { 
		return false; //有資料
	}else{
		return true;
	}

	
}

?>

<!DOCTYPE>
<html>
<head>
	<meta charset="UTF-8">
	<title>名單匯入</title>
	
	<script src="/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript">
        $(document).ready(function() {
        	// var cat = "<?=$cat?>";
        	// if (cat == 's') {
        	// 	$(".scrivener").show();
        	// 	$(".branch").hide();
        	// }else{
        	// 	$(".scrivener").hide();
        	// 	$(".branch").show();
        	// }
        	
        });
        function setSms(){
        	var m = new Array();
        	var cc = 0;
        	$('[name="mobile[]"]').each(function() {
				if ($(this).prop('checked')) {
					 ;
					m[cc] = $(this).val();
					cc++;
				}		
			}) ;
			// console.log(m);
			parent.$("[name='mobile']").text(m.join(',')) ;
			alert("匯入完成");
			
			parent.$.fn.colorbox.close();//關閉視窗
        }

        function checkAll(){
        	
        	if ($('[name="all"]').prop('checked')) {
        		$('[name="mobile[]"]').prop('checked', 'checked');
        	}else{
        		$('[name="mobile[]"]').prop('checked', '');
        	}
        }

        function checkLine(){
        	alert("名單即將更新，請等頁面重整後在按下確認匯入");
        	if ($('[name="line"]').prop('checked')) {
        		location.href = 'sms_manually_list.php?cat=<?=$cat?>&line=1';
        	}else{
        		location.href = 'sms_manually_list.php?cat=<?=$cat?>';
        	}
        }
	</script>
	<style>
		th{
			color: rgb(255, 255, 255);
		    font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
		    font-size: 1em;
		    font-weight: bold;
		    background-color: rgb(156, 40, 33);
		    padding: 6px;
   			 border: 1px solid #CCCCCC;
		}
		td{
			color: rgb(51, 51, 51);
			font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
			font-size: 100%;
			padding: 6px;
			border: 1px solid #CCCCCC;
			text-align: left;
		}
		.bt{
			color: #FFF;
		    font-family: Verdana;
		    font-size: 12px;
		    font-weight: bold;
		    line-height: 14px;
		    background-color: rgb(156, 40, 33);
		    text-align: center;
		    display: inline-block;
		    padding: 8px 12px;
		    border: 1px solid #FFF;
		}
		.bt:hover{
			color: rgb(156, 40, 33);
		    font-size: 12px;
		    background-color: #FFFFFF;
		    border: 1px solid rgb(156, 40, 33);
		}
	</style>
</head>
<body>
<center>
	<?php if ($cat == 's') { ?>
		<input type="checkbox" value="1" name="line" onclick="checkLine()" <?=$checked?>>過濾LINE名單
	<?php } ?>

	<input type="button" value="確認匯入" onclick="setSms()" class="bt" name="ex">
	<?php if ($cat == 's' || $cat == 's1') { ?>
		<table class="scrivener">
		<tr>
			<th><input type="checkbox" name="all" checked="checked" onclick="checkAll()"></th>
			<th>地政士編號</th>
			<th>事務所名稱</th>
			<th>姓名</th>
			<th>手機號碼</th>
		</tr>
		<?php
		foreach ($mobile as $k => $v) {
			echo "<tr>";
			echo '<td><input type="checkbox" name="mobile[]" id="" value="'.$v['mobile'].'" checked="checked"></td>';
			echo '<td>'.$v['code'].'</td>';
			echo '<td>'.$v['office'].'</td>';
			echo '<td>'.$v['name'].'</td>';
			echo '<td>'.$v['mobile'].'</td>';
			echo "</tr>";
		}
		
		?>
	</table>
	<?php }else{ ?>
		<table class="branch">
		<tr>
			<th><input type="checkbox" name="all" checked="checked" onclick="checkAll()"></th>
			<th>店編號</th>
			<th>品牌</th>
			<th>店名稱</th>
			<th>姓名</th>
			<th>手機號碼</th>
		</tr>
		<?php
		foreach ($mobile as $k => $v) {
			echo "<tr>";
			echo '<td><input type="checkbox" name="mobile[]" id="" value="'.$v['mobile'].'" checked="checked"></td>';
			echo '<td>'.$v['code'].'</td>';
			echo '<td>'.$v['brand'].'</td>';
			echo '<td>'.$v['store'].'</td>';
			echo '<td>'.$v['name'].'</td>';
			echo '<td>'.$v['mobile'].'</td>';
			echo "</tr>";
		}
		
		?>
	</table>
	<?php	}  ?>
	
	
	<input type="button" value="確認匯入" onclick="setSms()" class="bt">
</center>
</body>
</html>