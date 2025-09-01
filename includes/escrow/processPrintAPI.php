<?php
include_once dirname(dirname(dirname(__FILE__))).'/openpdodb.php' ;

$pdo = $pdolink;
$CertifiedId = $_GET['cId'];

$buyerowner = $_GET['iden'];

// $buyerowner = 1; //1buy 2owner
// $CertifiedId = '006018166'; 
// $CertifiedId = '070010857';
// $CertifiedId = '080295292';

if (!$CertifiedId && !$buyerowner) {
    echo json_encode(array('code'=>201,'msg'=>'錯誤'));

    exit;
}

$sql = 'SELECT cEscrowBankAccount,cSignDate FROM tContractCase WHERE cCertifiedId = ?';

$rs = $pdo->prepare($sql) ;
$rs->bindValue(1,$CertifiedId,PDO::PARAM_STR) ;
$rs->execute() ;
$tmp = $rs->fetch();
$tVR_Code = $tmp['cEscrowBankAccount'];
$SignDate = (substr($tmp['cSignDate'], 0,4)-1911).substr($tmp['cSignDate'], 5,2).substr($tmp['cSignDate'], 8,2);
unset($tmp);
// echo $tVR_Code ."<bR>";
// echo $SignDate."<br>";
// echo "<pre>";
// print_r($dataCase);



// die;

##
  
 //取得銷帳檔入帳紀錄資料
$query = '
SELECT 
    exp.id,
    exp.eTradeDate,
    exp.eLender,
    exp.eTradeCode,
    exp.eDebit,
    exp.eLender,
    exp.eStatusIncome,
    exp.eSummary,
    (SELECT sName FROM tCategoryIncome WHERE sId = exp.eStatusRemark) eStatusRemarkName,
    exp.eRemarkContent,
    exp.eBuyerMoney,
    exp.eExtraMoney,
    exp.eExplain
FROM 
  tExpense AS exp
WHERE 
  exp.eDepAccount = ? 
  AND eTradeDate >= ?
  AND exp.eStatusIncome = "2" 
  AND (exp.eTradeStatus = "0" OR exp.eTradeStatus ="8")
 ;
' ;

$rs = $pdo->prepare($query) ;
$rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
$rs->bindValue(2,$SignDate,PDO::PARAM_STR) ;
$rs->execute() ;

$_income = array() ;
while ($tmp = $rs->fetch()) {

  $_income[] = $tmp;
                        
}

unset($tmp);
##
//取得一銀保證號碼之所有交換票據資料
if (preg_match("/^60001/",$tVR_Code)) {   //若為一銀的案件，則加入票據資料
    $sql = '
      SELECT 
        * 
      FROM 
        tExpense_cheque 
      WHERE 
        eDepAccount = "00'.$tVR_Code.'" 
        AND eTradeStatus = "0" 
      ORDER BY 
        eTradeDate 
      ASC;
    ' ;
    // echo $sql;
    $rs = $pdo->prepare($sql) ;
    // $rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
    $rs->execute() ;
    $x = 0 ;
    while ($tmp = $rs->fetch()) {
        $_cheque[$x] = $tmp ;
        $_cheque[$x]['match'] = 'x' ;
        $x++;
        unset($tmp);
	}
}else if (preg_match("/^9998[56]0/",$tVR_Code)) {  //若為永豐的案件，則加入票據資料
    //取得次交票紀錄(Time to Pay tickets)
    $Time2Pay = array() ;
      
    $sql = '
        SELECT 
          DISTINCT eCheckNo
        FROM 
          tExpense_cheque 
        WHERE 
          eDepAccount = ? 
          AND eTradeStatus = "0" 
          AND eCheckDate = "0000000"
        ORDER BY 
          eDepAccount
        ASC; 
    ' ;
    $rs = $pdo->prepare($sql) ;
    $rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
    $rs->execute() ;
    while ($tmp = $rs->fetch()) {
        $tmp2[] =$tmp;

      unset($tmp);
    }
    $y = 0 ;
    for ($i=0; $i <count($tmp2) ; $i++) { 
        $sql = '
	        SELECT 
	           * 
	        FROM 
	           tExpense_cheque 
	        WHERE 
	           eDepAccount = ? 
	           AND eTradeStatus = "0"
	           AND eCheckDate = "0000000"            
	           AND eCheckNo = ?
	        ORDER BY 
	           eTradeDate
	        DESC 
	        LIMIT 1
        ' ;
        $rs = $pdo->prepare($sql) ;
        $rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
        $rs->bindValue(2,$tmp2[$i]['eCheckNo'],PDO::PARAM_STR) ;
        $rs->execute() ;
        while ($tmp = $rs->fetch()) {
          	if ($buyerowner == 2 && $tmp['eDepAccount'] == '0099986007014557' && $tmp['id'] == '18151') {
            	# code...
          	}else{
            	$Time2Pay[$y] = $tmp ;
            	$Time2Pay[$y]['match'] = 'x' ;
            	$Time2Pay[$y++]['Time2Pay'] = '1' ;//保留、顯示
            	unset($tmp);
          	}
                            
        }

    }
                        
    ##
    //取得託收票紀錄(Bills for Collection)
    $B4C = array() ;
      
 	$sql = '
   	SELECT 
     	* 
   	FROM 
     	tExpense_cheque 
   	WHERE 
     	eDepAccount = ? 
     	AND eTradeStatus = "0" 
     	AND eCheckDate <> "0000000"
   	ORDER BY 
     	eDepAccount,eCheckDate 
   	ASC; 
 	' ;
    $rs = $pdo->prepare($sql) ;
    $rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
                          
    $rs->execute() ;
    $x = 0 ;
    while ($tmp = $rs->fetch()) {
      	$B4C[$x] = $tmp;
      	$B4C[$x]['match'] = 'x';
      	$B4C[$x]['Time2Pay'] = '1' ;    //保留、顯示
      	$x++;
      	unset($tmp);
    }
                       
                        ##
      
   	//比對當相同紀錄出現時，剔除託收票紀錄
   	for ($x = 0 ; $x < count($Time2Pay) ; $x ++) {
     	for ($y = 0 ; $y < count($B4C) ; $y ++) {
	       	if ($Time2Pay[$x]['eDepAccount'] == $B4C[$y]['eDepAccount']
	            && $Time2Pay[$x]['eCheckNo'] == $B4C[$y]['eCheckNo']
	            && $Time2Pay[$x]['eDebit'] == $B4C[$y]['eDebit']
	            && $Time2Pay[$x]['eLender'] == $B4C[$y]['eLender']) {
	              
	            $B4C[$y]['Time2Pay'] = '2' ;  //剔除、不顯示
	       	}
     	}
   	}
      
    $B4C = array_merge($B4C,$Time2Pay) ;
    unset($Time2Pay) ;
      
    $y = 0 ;
    for ($x = 0 ; $x < count($B4C) ; $x ++) {
      if ($B4C[$x]['Time2Pay'] == '1') {    //僅取出保留的票據資料
        $_cheque[$y++] = $B4C[$x] ;
      }
    }
    unset($B4C) ;
                        ##
}else if (preg_match("/^96988/",$tVR_Code)) {    //若為台新的案件，則加入票據資料
    $sql = 'SELECT 
    			id,
    			eDepAccount,
    			eDebit,
    			eLender,
    			eTradeDate,
    			eCheckNo
    		FROM
    			tExpense_cheque
    		WHERE
    			eDepAccount = ? AND eTradeStatus = "0" ORDER BY eTradeDate ASC; ' ;
    $rs = $pdo->prepare($sql) ;
    $rs->bindValue(1,'00'.$tVR_Code,PDO::PARAM_STR) ;
    $rs->execute() ;
    $x=0;
    while ($tmp = $rs->fetch()) {
      $_cheque[$x] = $tmp;
      $_cheque[$x]['match'] = 'x' ;
      $x++;
      unset($tmp);
    }
                       
}
unset($tmp);

##

//取出未兌現支票據資料
$j = 0 ;
$cheque = array() ;
for ($x = 0 ; $x < count($_cheque) ; $x ++) { 

    for ($j = 0 ; $j < count($_income) ; $j ++) {

        if (preg_match("/^60001/",$tVR_Code)) {
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount'])  //保證號碼相同
                &&($_income[$j]['eTradeCode'] == '1793')            //交易代碼為1950
                &&($_cheque[$x]['eDebit'] == $_income[$j]['eDebit'])      //支出金額相符
                &&($_cheque[$x]['eLender'] == $_income[$j]['eLender'])      //收入金額相符
                
                &&($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate'])   //票據日期須小於銷帳日期
                &&($_income[$j]['match'] == 'x')) {               //銷帳紀錄須未被配對
                              
                $_income[$j]['match'] = '1' ;     //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match'] = '1' ;     //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
                break ;
            }

            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount'])  //保證號碼相同
                &&($_income[$j]['eTradeCode'] == '1950')            //交易代碼為1950
                &&($_cheque[$x]['eDebit'] == $_income[$j]['eDebit'])      //支出金額相符
                &&($_cheque[$x]['eLender'] == $_income[$j]['eLender'])      //收入金額相符
                &&($_income[$j]['eTradeStatus'] == '0')             //交易狀態為票據交易
                &&($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate'])   //票據日期須小於銷帳日期
                &&($_income[$j]['match'] == 'x')) {               //銷帳紀錄須未被配對(重要)
              
                $_income[$j]['match'] = '1' ;     //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match'] = '1' ;     //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
                break ;
            }
        }else if (preg_match("/^9998[56]0/",$tVR_Code)){
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount'])   //保證號碼相同
                &&($_cheque[$x]['eDebit'] == $_income[$j]['eDebit'])       //支出金額相符
                &&($_cheque[$x]['eLender'] == $_income[$j]['eLender'])     //收入金額相符
                &&($_income[$j]['eSummary'] == '票據轉入')           //交易摘要為票據轉入
                &&($_cheque[$x]['eCheckNo'] == $_income[$j]['eCheckNo'])     //支票號碼相同
                &&($_income[$j]['match'] == 'x')) {                //銷帳紀錄須未被配對
              
                $_income[$j]['match'] = '1' ;      //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match'] = '1' ;     //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
                break ;
            }
              //這筆是票據入帳，調帳沒有條全部金額 所以強制用寫死的方式 入帳狀態用2 非3
            if ($tVR_Code == '99986006034599') {
                $_income[$j]['match'] = '1' ;      //在銷帳紀錄中找到支票紀錄
                $_cheque[$x]['match'] = '1' ;     //在支票紀錄中找到銷帳紀錄
                $_income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
                break ;    
            }

        }else if (preg_match("/^96988/",$tVR_Code)){
            if (($_cheque[$x]['eDepAccount'] == $_income[$j]['eDepAccount'])   //保證號碼相同
              &&($_income[$j]['eTradeCode'] == 'PDC')              //交易代碼為 PDC 票據交易
              &&($_cheque[$x]['eDebit'] == $_income[$j]['eDebit'])       //支出金額相符
              &&($_cheque[$x]['eLender'] == $_income[$j]['eLender'])     //收入金額相符
              &&($_income[$j]['eTradeStatus'] == '0')              //交易狀態為票據交易
              &&($_cheque[$x]['eTradeDate'] < $_income[$j]['eTradeDate'])    //票據日期須小於銷帳日期
              &&($_income[$j]['match'] == 'x')) {                //銷帳紀錄須未被配對(重要)
                                  
              $_income[$j]['match'] = '1' ;      //在銷帳紀錄中找到支票紀錄
              $_cheque[$x]['match'] = '1' ;     //在支票紀錄中找到銷帳紀錄
              $_income[$j]['remark'] = ' 本款項由'.tDate_check($_cheque[$x]['eTradeDate'],'md','b','/',0,0).'支票兌現' ;
              break ;
            }
        }

    }

    $j = 0;
    ////20171023 佩琦 006055907 有一張票據待兌現70萬 請不要顯示在賣方官網 幫拉掉
    if ($_cheque[$x]['id'] != '16500' || ($buyerowner == 1 && $_cheque[$x]['id'] == '16500')) { 
        //如果是託收票  以到期日加一日為兌現日
        if ($_cheque[$x]['eTipDate'] !='' || ($_cheque[$x]['eCheckDate'] != '0000000' && $_cheque[$x]['eCheckDate'] != '')) {  
            $_expire_date = tDate_check($_cheque[$x]['eCheckDate'],'ymd','b','-',1,1) ;  //
        }else{
            $_expire_date = tDate_check($_cheque[$x]['eTradeDate'],'ymd','b','-',3,1) ;   //票據(預計)兌現時間
        }
        if ($_expire_date <= date("Y-m-d")) {                     //若今日超過兌現時間，則不顯示
          $_cheque[$x]['match'] = '1' ;
        }
                                
        if ($_cheque[$x]['match']=='x') {
          $cheque[$j] = $_cheque[$x] ;
          $cheque[$j]['cheque'] = '1' ;
                                  
          $j ++ ;
        }
    }
                        
}
unset($_cheque) ;
##

// echo "<pre>";
//  print_r($cheque) ; 
//  echo "</pre>";
//  exit ;
//合併顯示

$income_arr = array_merge($_income,$cheque) ;

unset($_income) ; unset($cheque) ;
##
 //20160921佩琪要求應部分調帳關係，金額沒辦法顯示，這一筆入帳日期2016-09-13 其他 NT$65600(僅顯示給買方)
if ($cCertifiedId == '004047873' && $buyerowner == 1) {
  $cc = count($income_arr);
  $income_arr[$cc]['eTradeDate'] = '1050913';
  $income_arr[$cc]['eLender'] = '6560000';
  $income_arr[$cc]['eRemarkContent'] = '其他';
  $income_arr[$cc]['match'] = 'x';
  unset($cc);
}
                        
//排序
for ($j = 0 ; $j < count($income_arr) ; $j ++) {
  for ($x = 0 ; $x < (count($income_arr) - 1) ; $x ++) {
    if ($income_arr[$x]['eTradeDate'] > $income_arr[($x+1)]['eTradeDate']) {
        $tmp = $income_arr[$x] ;
        $income_arr[$x] = $income_arr[($x+1)] ;
        $income_arr[($x+1)] = $tmp ;
        unset($tmp) ;
    }
  }
}
// echo "<pre>";
//  print_r($income_arr) ;
// echo "</pre>";
##
$dataCount = 0;
for ($j = 0 ; $j < count($income_arr) ; $j ++) {
    if ($income_arr[$j]['eStatusIncome'] == '3') {      //調帳時之顯示
        
        if (($income_arr[$j]['eChangeMoney'] > 0)&&($income_arr[$j]['eChangeMoney'] != $income_arr[$j]['eLender'])) {   //若調帳後仍有餘額時顯示
            $data[$i]['Color'] = ($income_arr[$j]['cheque']=='1')?'#000080':'#000000' ;
            
            $data[$i]['Date'] = tDate_check($income_arr[$j]['eTradeDate'],'ymd','b','-',0,0);
            if ($income_arr[$j]['cheque'] == '1') {
              $data[$i]['Title'] = '支票';
              $data[$i]['Money'] = '('.number_format($income_arr[$j]['eLender']).')';

              if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
                $data[$i]['RemarkContent'] = '未兌現、預計'.tDate_check($income_arr[$j]['eCheckDate'],'md','b','/',0,0)."兌現" ;
              }else {
                $data[$i]['RemarkContent'] = '未兌現、預計二日後兌現</td>' ;
              }
            }else{
              if ($buyerowner == 2 ) {
                $sql = "SELECT eTitle,eMoney FROM tExpenseDetailSmsOther WHERE eExpenseId = '".$income_arr[$j]['id']."' AND eDel = 0" ;
                $rs = $pdo->prepare($sql) ;
                $rs->execute() ;
                $tmpMoney = 0;
                $checkItem = 0;
                while ($tmpDetail = $rs->fetch()) {
                  $tmpMoney += $tmpDetail['eMoney'];
                  $checkItem = 1; //有買方應付款項細項
                  unset($tmpDetail);
                }
                
                $tmp =  explode('+', $income_arr[$j]['eRemarkContent']);
                $text = '';
                for ($i=0; $i < count($tmp); $i++) { 
                  if ($tmp[$i] != '' && (!preg_match("/買方/", $tmp[$i]) && !preg_match("/契稅/",$tmp[$i]) && !preg_match("/印花稅/",$tmp[$i]))) {
                    $text .= '+'.$tmp[$i];
                  }
                                         
                }
                $income_arr[$j]['eRemarkContent'] = $text;//implode('+', $tmp)
                unset($tmp);
              }

              $income_arr[$j]['eExplain'] = str_replace('買方服務費', '', $income_arr[$j]['eExplain']);

            }
            
            $data[$i]['title'] = $income_arr[$j]['eStatusRemarkName'].$income_arr[$j]['eRemarkContent']; 
            if ($buyerowner == '2') {                   
              $data[$i]['Money'] = 'NT$'.number_format($income_arr[$j]['eChangeMoney'] - $income_arr[$j]['eBuyerMoney'] - $income_arr[$j]['eExtraMoney'] - $tmpMoney + 1 - 1);
            }else{
              $data[$i]['Money'] = number_format($income_arr[$j]['eChangeMoney']);
            }    

            $data[$i]['RemarkContent'] = $income_arr[$j]['eExplain'];
              
        }
    }else {//正常入帳時之顯示
        $income_arr[$j]['eLender'] = substr($income_arr[$j]['eLender'],0,-2) ;
                          
        if (($income_arr[$j]['eLender'] - $income_arr[$j]['eBuyerMoney'] + 1 - 1 !=0)|| $buyerowner == 1) { //如果賣方的金額為0就不顯示
            //顯示摘要、收入與備註
            if ($income_arr[$j]['cheque'] == '1') {
                $data[$dataCount]['Color'] = ($income_arr[$j]['cheque']=='1')?'#000080':'#000000' ;
                $data[$dataCount]['Date'] = tDate_check($income_arr[$j]['eTradeDate'],'ymd','b','-',0,0) ;
                $data[$dataCount]['Title'] = '支票';
                $data[$dataCount]['Money'] = '(NT$'.number_format($income_arr[$j]['eLender']).')';


                if (($income_arr[$j]['eCheckDate'] != '') && ($income_arr[$j]['eCheckDate'] != '0000000')) {
                  $data[$dataCount]['RemarkContent'] = '未兌現、預計'.tDate_check($income_arr[$j]['eCheckDate'],'md','b','/',0,0)."兌現" ;
                }else {
                  $data[$dataCount]['RemarkContent'] = '未兌現、預計二日後兌現' ;
                }
                $dataCount++;
            }else {
                // echo '**'.$buyerowner;
                if ($buyerowner == 2 ) {
                  $sql = "SELECT eTitle,eMoney FROM tExpenseDetailSmsOther WHERE eExpenseId = '".$income_arr[$j]['id']."' AND eDel = 0" ;
                  $rs = $pdo->prepare($sql) ;                        
                  $rs->execute() ;
                  $tmpMoney = 0;
                  $checkItem = 0;
                  while ($tmpDetail = $rs->fetch()) {
                    $tmpMoney += $tmpDetail['eMoney'];
                    $checkItem = 1; //有買方應付款項細項
                    unset($tmpDetail);
                  }
                  $income_arr[$j]['eRemarkContent'] = $income_arr[$j]['eRemarkContent'];
                  $tmp =  explode('+', $income_arr[$j]['eRemarkContent']);
                  $text = '';
                  for ($i=0; $i < count($tmp); $i++) { 
                    if ( $tmp[$i] != '' && (!preg_match("/買方/", $tmp[$i]) && !preg_match("/契稅/",$tmp[$i]) && !preg_match("/印花稅/",$tmp[$i]))) {
                      $text .= '+'.$tmp[$i];
                    }   
                  }
                                           
                  $income_arr[$j]['eRemarkContent'] = $text;//implode('+', $tmp)
                  unset($tmp);
                }

                $income_arr[$j]['eExplain'] = str_replace('買方服務費', '', $income_arr[$j]['eExplain']);

                if ($buyerowner == 2) {     //賣方
                  $tmpM = $income_arr[$j]['eLender'] - $income_arr[$j]['eBuyerMoney'] - $income_arr[$j]['eExtraMoney'] - $tmpMoney + 1 - 1;
                }else {                //買方
                  $tmpM = $income_arr[$j]['eLender'] ;
                }

                // echo $tmpM."<br>";
                if ($tmpM > 0) {
                  $data[$dataCount]['Color'] = ($income_arr[$j]['cheque']=='1')?'#000080':'#000000' ;
                  $data[$dataCount]['Date'] = tDate_check($income_arr[$j]['eTradeDate'],'ymd','b','-',0,0);
                  $data[$dataCount]['Title'] = $income_arr[$j]['eStatusRemarkName'].$income_arr[$j]['eRemarkContent'];
                  $data[$dataCount]['Money'] = 'NT$'.number_format($tmpM);   
                  $data[$dataCount]['RemarkContent'] = $income_arr[$j]['eExplain'];
                  $dataCount++;
                }
                unset($tmpM);          
            }
        }  
    }
}
// echo "<pre>";
//  print_r($data) ;
// echo "</pre>";

echo json_encode($data);

exit;

//  exit ;

##
function tDate_check($_date,$_dateForm='ymd',$_dateType='r',$_delimiter='',$_minus=0,$_sat=0) {     
  $_aDate[0] = (substr($_date,0,3) + 1911) ;
  $_aDate[1] = substr($_date,3,2) ;
  $_aDate[2] = substr($_date,5) ;

  //$_cheque_date = implode('-',$_tDate) ;
  
  //是否遇六日要延後(六延兩天、日延一天)
  if ($_sat == '1') {
    $_ss = 0 ;
    $_ss = date("w",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;
    if ($_ss == '0') {      //如果是星期日的話，則延後一天
     
      $weekend = 1;     
    }
    else if ($_ss == '6') {   //如果是星期六的話，則延後兩天
     
      $weekend = 2;
    }
  }
  ##
  $_minus = $_minus+$weekend;//傳進來的日期必須加上遇到加日延後的日期
  $_t = date("Y-m-d",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;    //設定日期為 t+1 天
  unset($_aDate) ;

  $_aDate = explode('-',$_t) ;
  
  if ($_dateType=='r') {    //若要回覆日期格式為"民國"
    $_aDate[0] = $_aDate[0] - 1911 ;
  }
  else {            //若要回覆日期格式為"西元"
  
  }

  //決定回覆日期格式
  switch ($_dateForm) {
    case 'y':       //年
          return $_aDate[0] ;
          break ;
    case 'm':       //月
          return $_aDate[1] ;
          break ;
    case 'd':       //日
          return $_aDate[2] ;
          break ;
    case 'ym':        //年月
          return $_aDate[0].$_delimiter.$_aDate[1] ;
          break ;
    case 'md':        //月日
          return $_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    case 'ymd':       //年月日
          return $_aDate[0].$_delimiter.$_aDate[1].$_delimiter.$_aDate[2] ;
          break ;
    default:
          break ;
  }
  ##
}
?>
