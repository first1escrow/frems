<?php
include_once dirname(dirname(__FILE__)).'/configs/config.class.php';
include_once dirname(dirname(__FILE__)).'/class/SmartyMain.class.php';
include_once dirname(dirname(__FILE__)).'/web_addr.php' ;
include_once dirname(dirname(__FILE__)).'/openadodb.php' ;
include_once dirname(dirname(__FILE__)).'/session_check.php' ;

$_POST = escapeStr($_POST) ;
$_GET = escapeStr($_GET) ;

$cat = ($_POST['cat'])?$_POST['cat']:$_GET['cat'];
$id = ($_POST['id'])?$_POST['id']:$_GET['id'];
// echo $cat;
// echo $id;
##
//契約書合作品牌
$sql = "SELECT bId,bName FROM tBrand WHERE bContract = 1";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
	$menuBrand[$rs->fields['bId']] = $rs->fields['bName'];
	$rs->MoveNext();
}
##

if ($_POST) {
	for ($i=0; $i < count($_POST['contract']); $i++) { 
		if ($_POST['contract'][$i] != '') {
			$formData[$i]['Indent'] = $_POST['Indent'][$i];//是否重新編號
			$formData[$i]['listItem'] = $_POST['listItem'][$i];
			$formData[$i]['contract'] = $_POST['contract'][$i];
		}
	}

	

	$content = base64_encode(json_encode($formData));

	unset($formData);
	

	if ($cat == 'add') {
		$sql = "INSERT INTO
					tEContract
				SET
					eContract = '".$_POST['category']."',
					eName = '".$_POST['name']."',
					eApplication = '".$_POST['category2']."',
					eContent = '".$content."',
					eSendIden = '".@implode(',', $_POST['sendIden'])."',
					eCreator = '".$_SESSION['member_id']."',
					eCreatTime = '".date('Y-m-d H:i:s')."',
					eEditor = '".$_SESSION['member_id']."'";

		$conn->Execute($sql);
		$cat = 'edit';
		$id = $conn->Insert_ID(); 
		echo $cat."<br>";
		echo $id;
	}elseif ($cat == 'edit') {
		$sql = "UPDATE
					tEContract
				SET
					eContract = '".$_POST['category']."',
					eName = '".$_POST['name']."',
					eApplication = '".$_POST['category2']."',
					eContent = '".$content."',
					eSendIden = '".@implode(',', $_POST['sendIden'])."',
					eCreatTime = '".date('Y-m-d H:i:s')."',
					eEditor = '".$_SESSION['member_id']."'
				WHERE 
				 eId = '".$id."'";
		
		$conn->Execute($sql);
	}
}


if ($cat == 'edit') {
	$sql = "SELECT * FROM tEContract WHERE eId = '".$id."'";
	// echo $sql;

	$rs = $conn->Execute($sql);
	$data = $rs->fields; // 
	$data['data'] = json_decode(base64_decode($rs->fields['eContent']),true);
	//編號
	// $no1 = $no2 = $no3 = 1;
	// for ($i=0; $i < count($data['data']); $i++) { 
	// 	if ($data['data'][$i]['listItem'] == 1) {

	// 		if (condition) {
	// 			# code...
	// 		}

	// 		$data['data'][$i]['no'] = '第'.NumToCh2($no1).'條'; 
	// 		$no1++;
	// 	}elseif ($data['data'][$i]['listItem'] == 2) {
	// 		$data['data'][$i]['no'] =  NumToCh2($no2); 
	// 		$no2++;
	// 	}elseif ($data['data'][$i]['listItem'] == 3) {
	// 		$data['data'][$i]['no'] = '('.$no3.')'; 
	// 		$no3++;
	// 	}
	// }

	
}
$dataCount = (count($data['data']) > 0)?count($data['data']):1;
$data['eContract'] = ($data['eContract'])?$data['eContract']:1;
// echo $sql."<bR>";
// echo $cat."<bR>";
// echo $id."<br>";
// echo "<pre>";
// echo print_r($data);
####
function NumToCh2($no){  //條文數字中文，目前只算到十位數 (待調整)

	$len =  strlen($no);
	// $str = new Array("零","一","二","三","四","五","六","七","八","九");
	$str = array("零","一","二","三","四","五","六","七","八","九");
	$newStr = '';

  
   	if ($len == 1) {
   	  	$newStr = $str[$no];
   	}else if($len == 2){

   	  	for ($i = 0; $i < $len; $i++) {
   	  		$cutStr = substr($no, $i,($i+1));// no.toString().substr($i,($i+1));

   	  		if ($cutStr == 1) { //十X

   	  			$newStr += '十';
   	  		}else{
   	  			$newStr += $str[$cutStr];
   	  			if ($i==0) {
   	  				$newStr += '十';
   	  			}
   	  		}
   	  	}
   	}

   	return $newStr;
   	  

}
$menuIndent = array(0=>'繼續編號',1=>'重新編號');
####
$smarty->assign('cat',$cat);
$smarty->assign('id',$id);
$smarty->assign('dataCount',$dataCount);
$smarty->assign('menuIndent',$menuIndent);
$smarty->assign("menulistItem",array(0 => '段落', 1=>'條文',2=>'編號(一)',3=>'編號(1)'));
$smarty->assign('menuContract',array(1=>'申請書',2=>'合約書'));
$smarty->assign('menuBrand',$menuBrand);
$smarty->assign('menuCategory',array(1=>'土地',2=>'建物'));//,3=>'預售屋'
$smarty->assign('menuApplication',array(1=>'加盟',2=>'直營(限台屋)',3=>'非仲介成交'));
$smarty->assign('menusendIden',array(1=>'是'));
$smarty->assign('data',$data);
$smarty->display('contractEdit.inc.tpl', '', 'contract');
?>
