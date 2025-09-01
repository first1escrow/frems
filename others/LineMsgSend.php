<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../openadodb.php' ;
// $code = substr($_GET['code'],2);
// // // 
// $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
// // 
// $emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');

// echo $emoticon;
$LineId = $_GET['lineId'];
$txt = urldecode($_GET['txt']);
// echo $txt."_";
$sql = "SELECT * FROM tLineMoji";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {
	$code = substr($rs->fields['lCode'], 2);
	
	// 
	$bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
	// 
	$emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');
	// $emoticon = 'A';
	
	

	$txt = preg_replace("/\(".$rs->fields['lCode']."\)/", $emoticon , $txt);

	

	$rs->MoveNext();
}


// 去除
// $code = '0x100090';
// $code = substr($code, 2);
// // 
// $bin = hex2bin(str_repeat('0', 8 - strlen($code)) . $code);
// // 
// $emoticon =  mb_convert_encoding($bin, 'UTF-8', 'UTF-32BE');

// //
// $msg = '早安'.$emoticon; 

$url = "https://firstbotnew.azurewebsites.net/bot/api/linePush.php?lineId=".$LineId."&txt=".urlencode($txt);

file_get_contents($url);

function hex2bin($code){
	$code = strtolower($code);

        $txt = "";
       
        $i = 0;
        do {
            $txt .= chr(hexdec($code{$i}.$code{($i + 1)}));
            $i += 2;
        } while ($i < strlen($code));

    return $txt;
}
?>