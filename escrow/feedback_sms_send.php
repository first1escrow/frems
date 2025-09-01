<?php
require_once dirname(__DIR__).'/configs/config.class.php';
require_once dirname(__DIR__).'/openadodb.php' ;
require_once dirname(__DIR__).'/session_check.php' ;
require_once dirname(__DIR__).'/sms/sms_function.php';
require_once dirname(__DIR__).'/first1DB.php';

/**
 * 取得地政士事務所名稱
 * param integer target_id: 地政士編號
 */
Function getScrivenerName($target_id)
{
    $conn = new first1DB;

    $sql = '
		SELECT
			s.sOffice AS store
		FROM
			tFeedBackStoreSms AS fs
		LEFT JOIN
			tScrivener AS s ON s.sId=fs.fStoreId
		WHERE
			fs.fType = 1 AND fs.fStoreId = :sId AND fs.fDelete = 0
	;';

    return $conn->one($sql, ['sId' => $target_id]);
}
##

/**
 * 取得仲介品牌與店名稱
 * param string target_type: 品牌代碼
 */
Function getBranchName($target_id)
{
    $conn = new first1DB;

	$sql = '
		SELECT 
			(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand,
			b.bStore as store
		FROM
			tFeedBackStoreSms AS fs
		LEFT JOIN
			tBranch AS b ON b.bId = fs.fStoreId
		WHERE
			fs.fType = 2 AND fs.fStoreId = :bId AND fs.fDelete = 0
	;';

    return $conn->one($sql, ['bId' => $target_id]);
}
##

/**
 * 濾除(待停用)字樣
 * param string $text: 欲過濾字串
 * param string $filter: 需過濾的字詞
 */
Function filter_words($text, $filter)
{
    return preg_replace("/$filter/iu", '', $text);
}
##

//確認輸入
$msg  = trim($_POST['msg']);
$bId  = trim($_POST['branch']);
$send = trim($_POST['send']);
$cat  = trim($_POST['cat']);
##

//2022-07-04
if ($cat == 1) {
    $target_type = substr($bId, 0, 2);
    $target_id   = (int)substr($bId, 2);

    $match = [];
    preg_match("/^(.*)\<first1\>.*\<\/first1\>(.*)$/iu", $msg, $match);

	if (!empty($match[1]) && !empty($match[2])) {
    	if ($target_type == 'SC') {
            $data  = getScrivenerName($target_id);
            $store = filter_words($data['store'], '\(待停用\)');
		} else {
            $data  = getBranchName($target_id);
            $store = filter_words($data['brand'], '\(待停用\)').filter_words($data['store'], '\(待停用\)');
    	}

		$msg = $match[1].$store.$match[2]."\r\n";

		$data = $store = null;
		unset($data, $store);
	}

    $target_type= $target_id = $match = null;
    unset($target_type, $target_id, $match);
}
##

//1:寄送、2:顯示寄送者
$sms = new SMS_Gateway();

if ($send==1) {
	if ($cat == 1) {
		$mag = $sms->send('', '', $bId, '回饋金2', '', 'y', 0, $msg);	//通知簡訊
	} else {
		$mag = $sms->send('', '', $bId, '回饋金', '', 'y', 0, $msg);

	}
	
	if (count($mag) <= 0) {
		echo true ;
	}
} else {
	if ($cat == 1) {
		$tmp = $sms->send('' , '', $bId, '回饋金2', '', 'n', 0, $msg);	//通知簡訊
	} else {
		$tmp = $sms->send('' , '', $bId, '回饋金', '', 'n', 0, $msg);
	}	

	for ($i = 0 ; $i < count($tmp) ; $i ++) { 
		$tbl .='<tr name="b'.$bId.'">';
		$tbl .='<td>'.$tmp[$i]["brand"].$tmp[$i]["bStore"].'</td>';
		$tbl .='<td>'.$tmp[$i]['title'].'</td>';
		$tbl .='<td>'.$tmp[$i]["mName"].'</td>';
		$tbl .='<td>'.$tmp[$i]["mMobile"].'</td>';
		$tbl .='</tr>';

		if ($cat == 1) {
			$tbl .='<tr name="b'.$bId.'">';
			$tbl .='<td colspan="4" style="word-wrap:break-word; ">'.$tmp[$i]["smsTxt"].'</td>';
			$tbl .='</tr>';
		}
	}

	echo $tbl;
}

exit;
?>
