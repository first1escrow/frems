<?php
include_once '../../openadodb.php' ;
$_POST = escapeStr($_POST) ;

$cat = $_POST['cat'];
$count = $_POST['cou'] +1;
?>
<tr><td class="padding-top:5px">&nbsp;</td></tr>
<tr class="loc<?=$cat?>">
	<td>戶名(<?=$count?>)：<input type="text" name="N<?=$cat?>accountName[]" value="" class="step1"/></td>
</tr>
<tr class="loc<?=$cat?>" >
	<td>帳號(<?=$count?>)：<input type="text" name="N<?=$cat?>account[]" value="" class="step1"/></td>
</tr>
<tr class="s4 <{$data.show4}> loc<?=$cat?>">
	<td>金額(<?=$count?>)：<input type="text" name="N<?=$cat?>money[]" value="" class="step1 "/></td>
</tr>


