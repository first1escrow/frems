<?php
include_once '../../openadodb.php' ;
include_once '../../web_addr.php' ;
require_once '../Classes/phpmailer/class.phpmailer.php' ;
require_once '../Classes/PHPExcel.php' ;
require_once '../Classes/PHPExcel/Writer/Excel2007.php' ;


//欲關帳之保證號碼清單
Function list_certify($_conn,$_s,$_e,$equ = '=') {
	//設定搜尋條件：結案時間、餘額為零	
	$sql = '
		SELECT
			cEndDate,
			cCertifiedId,
			cEscrowBankAccount as tVR_code,
			cCaseMoney
		FROM
			tContractCase
		WHERE
			cEndDate <> "0000-00-00 00:00:00"
			AND cEndDate <= "'.$_e.'"
			AND cCaseMoney '.$equ.' "0"
			AND cCaseStatus IN ("3","4","8")
		ORDER BY
			cCertifiedId
		ASC
	' ;
	//echo $sql."\n" ; exit ;
	
	$rs = $_conn->Execute($sql) ;
	##
	
	//取出所有保證號碼清單
	while (!$rs->EOF) {
		$data[] = $rs->fields ;
		$rs->MoveNext() ;
	}
	##
	
	return $data ;
}
##

//發送郵件、簡訊通知
Function mail_result($close_case,$title,$attachs) {
	//設定郵件資訊並發送
	$mail = new PHPMailer() ;
	$mail->IsSMTP() ;							//使用SMTP發信
	$mail->SMTPDebug	= 0;   
	$mail->SMTPAuth		= true ;  
	$mail->SMTPSecure	= "" ;
	//$mail->Host			= '192.168.1.106' ;		//SMTP server 
	$mail->Host			= '192.168.1.73' ;		//SMTP server 
	$mail->Port			= 25 ;  
	$mail->Username		= "www_sender";  
	$mail->Password		= "!www_sender!";  
	$mail->CharSet		= "utf-8";				//設定郵件編碼
	$mail->IsHTML(true);						//設定郵件內容為HTML

	$mail->SetFrom('www_sender@twhg.com.tw','第一建築經理股份有限公司後台系統') ;		//設定寄件者信箱
	$mail->AddReplyTo('jason.chen@twhg.com.tw','第一建築經理股份有限公司後台系統') ;	//設定回信信箱

	//$mail->AddAddress('joannafoxconn@gmail.com','台灣房屋 Joanna') ;
	$mail->AddAddress('jason.chen@twhg.com.tw','台灣房屋 陳銘慶') ;
	//$mail->AddBcc('jason.chen@twhg.com.tw','台灣房屋 陳銘慶') ;
	//$mail->AddBcc('cmc569@gmail.com') ;
 
	$mail->IsHTML(true) ;
	$mail->Subject = '第一建經合約銀行關帳帳號列表('.$title.')' ;
	$mail->Body = '(本信為系統自動發送，請勿直接回覆)<br>'."\n" ;
	$mail->Body .= '==================================<br>'."\n" ;
	
	for ($i = 0 ; $i < count($close_case) ; $i ++) {
		$mail->Body .= $close_case[$i]['cCertifiedId'].' , '.$close_case[$i]['cEndDate'].' , '.$close_case[$i]['cCaseMoney']."<br>\n" ;
	}

	$mail->Body .= '==================================<br>'."\n" ;
	$mail->Body .= '合計共 '.number_format(count($close_case)).' 筆<br>'."\n" ;

	echo "<br>\n" ;

	$mail->AddAttachment($attachs) ;		//增加附件

	//include('sms_send.php') ;

	if ($mail->Send()) {
		echo date("Y-m-d H:i:s").' 第一銀行關帳帳號郵件已送出!!'."\n" ;
		//send_sms('0922785490','一銀關帳郵件發送回報','一銀關帳郵件發送成功 [ '.date("Y-m-d H:i:s").' ]') ;
	}
	else {
		echo date("Y-m-d H:i:s").' 第一銀行關帳帳號郵件發送失敗!!'."\n" ;
		//send_sms('0922785490','一銀關帳郵件發送回報','一銀關帳郵件發送失敗 [ '.date("Y-m-d H:i:s").' ]') ;
	}
}
##

$_addr = substr($web_addr,7) ;

//時間範圍
$_m = date("m",mktime(0,0,0,(date("m")-4),1,date("Y"))) ;
$_y = date("Y",mktime(0,0,0,(date("m")-4),1,date("Y"))) ;

$_start = $_y.'-'.$_m.'-01 00:00:00' ;		//開始時間
$_end = $_y.'-'.$_m.'-31 23:59:59' ;		//結束時間
##
	// $_day = date("d");

//確認是否為月初(建經檢查)
if (date("d")== '10') {
	//餘額為零之案件
	$zero = list_certify($conn,$_start,$_end) ;
	//foreach ($list as $k => $v) {
	//	echo $v['cCertifiedId']."\n" ;
	//}
	##
	
	//餘額不為零之案件
	$non_zero = list_certify($conn,$_start,$_end,'<>') ;
	##
	
	//寫入excel檔
	$objPHPExcel = new PHPExcel();
	//Set properties 設置文件屬性
	$objPHPExcel->getProperties()->setCreator("第一建經");
	$objPHPExcel->getProperties()->setLastModifiedBy("第一建經");
	$objPHPExcel->getProperties()->setTitle("第一建經");
	$objPHPExcel->getProperties()->setSubject("結案案件餘額明細");
	$objPHPExcel->getProperties()->setDescription("第一建經結案案件餘額明細");

	//指定第一頁工作頁
	$objPHPExcel->setActiveSheetIndex(0);

	//調整欄位寬度
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25.4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25.4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25.4);

	$cell_no = 1 ;
	//主標題
	$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'保證號碼') ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,'結案日期') ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'帳戶餘額') ;

	//餘額為零之案件
	$cell_no ++ ;	//愈填寫查詢結果起始的儲存格位置

	for ($i = 0 ; $i < count($zero) ; $i ++) {					//寫入查詢結果
		//寫入資料
		$_date = substr($zero[$i]['cEndDate'],0,10) ;			//調整結案日期格式

		$objPHPExcel->getActiveSheet()->getCell('A'.($cell_no + $i))->setValueExplicit($zero[$i]['tVR_code'], PHPExcel_Cell_DataType::TYPE_STRING) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+$cell_no),$_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+$cell_no),$zero[$i]['cCaseMoney']);
	
		//設定案件金額千分位符號
		//$objPHPExcel->getActiveSheet()->getStyle('J'.($i+$cell_no).':K'.($i+$cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
		//設定保證號碼置中
		//$objPHPExcel->getActiveSheet()->getStyle('B'.($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		//設定狀態置中
		//$objPHPExcel->getActiveSheet()->getStyle('H'.($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		//設定儲存格內容為文字模式
		//$objPHPExcel->getActiveSheet()->getCell('A'.$cell_no)->setValueExplicit($rs->fields['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING) ;
	}
	
	//Rename sheet 重命名工作表標籤
	$objPHPExcel->getActiveSheet()->setTitle('已結案餘額為零之案件');
	##
	
	
	//指定第二頁工作頁
	$objPHPExcel->createSheet() ;
	$objPHPExcel->setActiveSheetIndex(1) ;

	//調整欄位寬度
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25.4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25.4);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25.4);

	$cell_no = 1 ;
	//主標題
	$objPHPExcel->getActiveSheet()->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$cell_no,'保證號碼') ;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$cell_no,'結案日期') ;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$cell_no,'帳戶餘額') ;

	//餘額不為零之案件
	$cell_no ++ ;	//愈填寫查詢結果起始的儲存格位置

	for ($i = 0 ; $i < count($non_zero) ; $i ++) {					//寫入查詢結果
		//寫入資料
		$_date = substr($non_zero[$i]['cEndDate'],0,10) ;			//調整結案日期格式

		$objPHPExcel->getActiveSheet()->getCell('A'.($cell_no + $i))->setValueExplicit($non_zero[$i]['tVR_code'], PHPExcel_Cell_DataType::TYPE_STRING) ;
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+$cell_no),$_date);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+$cell_no),$non_zero[$i]['cCaseMoney']);
	
		//設定案件金額千分位符號
		//$objPHPExcel->getActiveSheet()->getStyle('J'.($i+$cell_no).':K'.($i+$cell_no))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	
		//設定保證號碼置中
		//$objPHPExcel->getActiveSheet()->getStyle('B'.($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		//設定狀態置中
		//$objPHPExcel->getActiveSheet()->getStyle('H'.($i+$cell_no))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		//設定儲存格內容為文字模式
		//$objPHPExcel->getActiveSheet()->getCell('A'.$cell_no)->setValueExplicit($rs->fields['cCertifiedId'], PHPExcel_Cell_DataType::TYPE_STRING) ;
	}
	
	//Rename sheet 重命名工作表標籤
	$objPHPExcel->getActiveSheet()->setTitle('已結案餘額不為零之案件');
	##
	
	//Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	//Save Excel 2007 file 保存
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	
	//檔案名稱
	$file_name = $_y.'年度'.$_m.'月份結案案件明細.xlsx' ;
	
	// $file_path = "/home/httpd/html/".$_addr."/bank/report/excel/" ;
	$file_path = "excel/" ;

	$_file = $file_path.$file_name ;
	$objWriter->save($_file);
	
	//郵件、簡訊通知
	mail_result($zero,'第一建經',$_file) ;
	##
	
	exit ;
}
##

//確認是否為月底(一銀)
if (date("d") == '25') {
	//餘額為零之案件
	$list = list_certify($conn,$_start,$_end) ;
	##
	
	// $_path = '/home/httpd/html/'.$_addr.'/bank/report/case_close/'.date("Ymd").'.txt' ;
	$_path ='case_close/'.date("Ymd").'.txt';
	$fh = fopen($_path,'w') ;		//準備寫入一銀上傳檔

	$i = 0 ;
	for ($i = 0 ; $i < count($list) ; $i ++) {
		//echo ($i+1).'.'.$list[$i]['cCertifiedId'].','.$list[$i]['cEndDate'].','.$list[$i]['cCaseMoney']."<br>\n" ;
	
		//更新案件是否結案
		$sql = 'UPDATE tExpense SET eClose="1" WHERE eDepAccount="00'.$list[$i]['tVR_code'].'" ;' ;
		$conn->Execute($sql) ;
		##
		
		$cId = $list[$i]['tVR_code'] ;
		if (preg_match("/^60001/",$cId)) {
			fwrite($fh,$list[$i]['cCertifiedId']."\n") ;
		}
	}
	
	fclose($fh) ;			//結束寫檔動作

	//郵件、簡訊通知
	mail_result($list,'一銀'.$error,$_path) ;
	##
	
	exit ;
}
##

?>