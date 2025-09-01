<?php
require_once dirname(dirname(dirname(__FILE__))).'/openadodb.php' ;
require_once dirname(dirname(__FILE__)).'/lib.php' ;

$v = $_POST ;
//print_r($v) ; exit ;

$arr = array() ;
$arr = getDistinct($v['ct']) ;

if ($v['noAll']) $qD = '' ;
else $qD = '<option value="">全區'."</option>\n" ;

foreach($arr as $k => $v) {
	$qD .= '<option value="'.$k.'">'.$v."</option>\n" ;
}
unset($arr) ;

echo $qD ;
?>