<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php' ;


$sCpCity = '台中市';
$today = date('Y-m-d');

$sql = "SELECT * FROM tBankBanner WHERE bOk2 = 1 AND bDel = 0 AND (bArea LIKE '%".$sCpCity."%' OR bArea ='') ORDER BY bSort ASC";
// echo $sql;
$rs = $conn->Execute($sql);

while (!$rs->EOF) {

	if ($rs->fields['bLink2']) {
		$tmp_url = '"'.$rs->fields['bLink2'].'"';
	}else{
		
		$tmp_url = '"'.$rs->fields['bUrl2'].'"';
	}
	
	$tmp_url = urldecode($tmp_url);

	if (($today >= $rs->fields['bStart'] && $today <= $rs->fields['bEnd']) && ($rs->fields['bEnd'] !='0000-00-00')) {
		$tmp[] = $tmp_url;
	}

	unset($tmp_url);
	$rs->MoveNext();
}

$banner = @implode(',', $tmp);

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

	<link rel="stylesheet" href="/css/colorbox.css" />
	<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery.colorbox.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){

		
		var count = 0;

		banner(count);
		// for (var i = 0; i < array.length; i++) {
		// 	if (array[(i+1)] == '') {
		// 		ck = 1;
		// 	}
		// 	ad1(array[i],array[(i+1)],ck);

		// };
		
		// ad1(array[0],array[(0+1)],ck);


	
		// ad2();
	});
	function banner(count){
		// console.log('banner:'+count);

		
		var array = [<?=$banner?>];
		var max = array.length;
		var url = array[count];

		
		ad1(url,max,count);

	}
	
	function ad1(url,max,count){
		console.log(count+'_'+max);
		if (count < max) {
			$.colorbox({
				iframe:true,
				width:"90%",
				height:"95%",
				href:url,
				onClosed:function bClose(){
					
						
						banner((count+1));

						
					
					
				}
					
				
			}) ;
		}
		
			
	}

	

	</script>
</head>
<body>
	
</body>
</html>


