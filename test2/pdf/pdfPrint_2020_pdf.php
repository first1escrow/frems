<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
##
//取得公司資訊
$company = json_decode(file_get_contents(dirname(dirname(dirname(__FILE__))).'/includes/company.json'),true) ;

$id = ($_POST['id'])?$_POST['id']:$_GET['id'];


$sql = "SELECT
            sType,
            sStoreCode,
            sStoreId,
            sStoreName,
            sMethod,
            sEndTime,
            sEndTime2,
            sStatus,
            sFeedbackAllCase,
            sFeedbackMark,
            sCreatTime
        FROM
            tStoreFeedBackMoneyFrom WHERE sId = '".$id."'";
$rs = $conn->Execute($sql);

$data = $rs->fields;

//編號
if ($data['sType'] == 1) {
   $data['code'] = $data['sStoreCode'].str_pad($data['sStoreId'],'4','0',STR_PAD_LEFT);
}elseif ($data['sType'] == 2) {
    $data['code'] = $data['sStoreCode'].str_pad($data['sStoreId'],'5','0',STR_PAD_LEFT);
}

$data['sStoreName'] = str_replace('(待停用)', '', $data['sStoreName']);

//結算時間

$data['sEndTime'] = str_replace('-', '/', (substr($data['sEndTime'], 0,4)-1911).substr($data['sEndTime'],4)) ;
$data['sEndTime2'] = str_replace('-', '/', (substr($data['sEndTime2'], 0,4)-1911).substr($data['sEndTime2'],4)) ;
$endTime = $data['sEndTime']."-".$data['sEndTime2'];

##回饋案件資料
$dataCase = array();
$sql = "SELECT * FROM tStoreFeedBackMoneyFrom_Case WHERE sFromId = '".$id."'";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $dataCase[] = $rs->fields;
    $rs->MoveNext();
}

##店回饋銀行資料
$dataAccount = array();
$sql = "SELECT
            (SELECT bBank4_name FROM tBank WHERE bBank3 = sBankMain AND bBank4 ='') AS BankMain,
            (SELECT bBank4_name FROM tBank WHERE bBank3 = sBankMain AND bBank4 = sBankBranch) AS BankBranch,
            sBankAccountNo,
            sBankAccountName,
            sBankMoney
        FROM
            tStoreFeedBackMoneyFrom_Account
        WHERE
            sFromId = '".$id."'";
$rs = $conn->Execute($sql);
while (!$rs->EOF) {
    $dataAccount[] = $rs->fields;
    $rs->MoveNext();
}
##
//專屬顧問
$sales = array();
$salesMobile = array();
if ($data['sType'] == 1) {
    $sql = "SELECT
                sSales AS sales,
                (SELECT pName FROM tPeopleInfo WHERE pId = sSales) AS salesName,
                (SELECT pMobile FROM tPeopleInfo WHERE pId = sSales) AS mobile
            FROM
                tScrivenerSales
            WHERE
                sScrivener = '".$data['sStoreId']."' ORDER BY sSales ASC";
}else{
    $sql = "SELECT 
                bSales AS sales,
                (SELECT pName FROM tPeopleInfo WHERE pId = bSales) AS salesName,
                (SELECT pMobile FROM tPeopleInfo WHERE pId = bSales) AS mobile,
                (SELECT bCategory FROM tBranch WHERE bId = bBranch) AS category
            FROM
                tBranchSales
            WHERE
                bBranch = '".$data['sStoreId']."' ORDER BY bSales ASC";
}

$rs = $conn->Execute($sql);
while (!$rs->EOF) {
 
    array_push($sales, $rs->fields['salesName']);
    array_push($salesMobile, $rs->fields['mobile']);
    $rs->MoveNext();
}
// asort($sales);
##
//半形<=>全形
function n2w($strs, $types = '0'){  // narrow to wide , or wide to narrow
	$nt = array(
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
	);
	$wt = array(
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
	);
 
	if ($types == '0') {		//半形轉全形
		// narrow to wide
		$strtmp = str_replace($nt, $wt, $strs);
	}
	else {						//全形轉半形
		// wide to narrow
		$strtmp = str_replace($wt, $nt, $strs);
	}
	return $strtmp;
}
##

//計算行高
function checkHeight($str='',$len=7) {
	$mul = 1 ;
	if ($str) $mul = ceil(mb_strlen($str,'utf-8') /$len) ;
	
	return $mul ;
}
##

//設定線條為實、虛線
class PDF1 extends PDF_Unicode
{
    function SetDash($black=false, $white=false)
    {
        if($black and $white)
            $s=sprintf('[%.3f %.3f] 0 d', $black*$this->k, $white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
}


if ($data['sFeedbackAllCase'] > 0) { //總店表
    require_once 'pdfPrint_2020_pdf_manager.php';
}elseif($data['sFeedbackMark'] > 0){
    require_once 'pdfPrint_2020_pdf_manager2.php';
}else{
    require_once 'pdfPrint_2020_pdf_branch.php';
}
exit;


?>