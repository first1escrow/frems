<?php
include_once '../../openadodb.php' ;
include_once '../../session_check.php' ;
require_once('../../tcpdf/tcpdf.php');
include_once 'bookFunction.php';

$_POST = escapeStr($_POST) ;
$bId = $_POST['id'] ;


$sql = "SELECT 
			*,
			(SELECT cBankName FROM tContractBank WHERE cId=bBank) AS cBankName,
			(SELECT cBranchName FROM tContractBank WHERE cId=bBank) AS cBranchName,
			(SELECT cName FROM tCategoryBook WHERE cId=bCategory ) AS CategoryName
		FROM
			tBankTrankBook
		WHERE
			bId = '".$bId."'";

$rs = $conn->Execute($sql);

$data = $rs->fields;
$data['CertifiedId_9'] = substr($data['bCertifiedId'],5);
$data['bReBank'] = str_replace('分行', '', $data['bReBank']);

//解匯行
$transBank = array() ;
if (preg_match("/^\d{7}$/",$data['bObank'])) {
	//總行
	$sql = 'SELECT * FROM tBank WHERE bBank3="'.substr($data['bObank'],0,3).'" AND bBank4="";' ;
	$rs = $conn->Execute($sql) ;
	$transBank['main'] = $rs->fields['bBank4_name'] ;
	##
	
	//分行
	$sql = 'SELECT * FROM tBank WHERE bBank3="'.substr($data['bObank'],0,3).'" AND bBank4="'.substr($data['bObank'],3).'";' ;
	$rs = $conn->Execute($sql) ;
	$transBank['branch'] = $rs->fields['bBank4_name'] ;
	##
}
##

##########################################################
//細項
$sql = "SELECT * FROM tBankTrankBookDetail WHERE bTrankBookId ='".$bId."' AND bDel = 0";

$rs = $conn->Execute($sql);

while (!$rs->EOF) {

	if ($rs->fields['bCat'] == '1') { //1:錯誤帳戶 //補通訊用
		$data_Error[] = $rs->fields;
	}elseif ($rs->fields['bCat'] == '2') { //2:正確帳戶 //補通訊用
		$data_Correct[] = $rs->fields;
	}

	$rs->MoveNext();
}


############################################
//人員傳真號碼
$sql = "SELECT pFaxNum FROM tPeopleInfo WHERE pId ='".$data['bCreatorId']."'";
$rs = $conn->Execute($sql);
$Fax = $rs->fields['pFaxNum'];
######################################################################

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, 'cm', 'A4', true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);	
	$pdf->SetMargins('2.4', '1.8', '2.4');
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);



	$pdf->AddPage();
	
	$pdf->SetFont('msungstdlight', 'B', 18);
	$Header='<span style="text-align:center;vertical-align:top">';	
	$Header.= '不動產買賣價金第一建經履約保證信託指示通知書';
	$Header.='</span><br>';
	$Header.='<span style="text-align:center;vertical-align:top">';
	$Header.= '(匯款更正專用)';
	$Header.='</span>';
	
	
	$pdf->writeHTML($Header, true, 0, true, true);
	$pdf->SetFont('msungstdlight', 'B', 12);

 
	$txt = '<table width="100%">
	          
	            <tr>
	               <td width="60%">致：永豐銀行信託部</td>
	               <td width="40%">自：第一建經</td>
	            </tr>
	            <tr>
	               <td><u>許晉嘉/富保琴/蕭育伶/廖心慧/林姿秀</u></td>
	               <td><u>蕭家津/吳佩琦</u></td>
	            </tr>
	            <tr>
	               <td>Fax：02-2506-0161</td>
	               <td>Fax：02-2751-8586/02-2752-8811</td>
	            </tr>
	            <tr>
	               <td>Tel：02-2183-5143/2183-5328
	               </td>
	               <td>Tel：02-2772-0111#888及101
	               </td>
	            </tr>
	            <tr>
	            	<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;02-2183-5169/2183-5334</td>
	            	<td>&nbsp;</td>
	            </tr>

	      </table>';
	    $pdf->writeHTMLCell(0, 0, '', '', $txt, 0, 1, 0, true, '', true);
	    $pdf->SetFont('msungstdlight', 'B', 14);
	    $pdf->Ln(1);
	   	$x = $pdf->getX()-1;
	    $y = $pdf->getY();
	    $y2 = $y;
	    $tmp = explode('-', dateformate($data['bDate']));

	    $txt = '指示日期：'.$tmp[0]."年".$tmp[1]."月".$tmp[2]."日";

	    $border = array(
		   'T' => array('width' => 0.1, 'color' => array(0,0,0)),
		   'B' => array('width' => 0.05, 'color' => array(0,0,0)),
		   'L' => array('width' => 0.1, 'color' => array(0,0,0)),
		);

	    $pdf->writeHTMLCell(11,0, $x, $y, $txt, $border, 1, 0, true, '', true);


	    $x = $pdf->getX()+9.5;
	     $border = array(
		   'T' => array('width' => 0.1, 'color' => array(0,0,0)),
		   'R' => array('width' => 0.1, 'color' => array(0,0,0)),
		   'B' => array('width' => 0.05, 'color' => array(0,0,0)),
		);
		$txt = $data['bBookId'] ;

	    $txt = '指示單編號：<u>&nbsp;&nbsp;'.$txt.'&nbsp;&nbsp;</u>' ;
	    $pdf->writeHTMLCell(7.5,0, $x, $y,$txt, $border, 1, 0, true, '', true);

	    $x = $pdf->getX()-1;
	    $y = $pdf->getY();
		
		$txt = '■保證號碼：'.substr($data['bCertifiedId'],0,5).'-'.substr($data['bCertifiedId'],5) ;
 
	    $border = array(
		   'B' => array('width' => 0.1, 'color' => array(0,0,0)),
		   'L' => array('width' => 0.1, 'color' => array(0,0,0)),
		);
	    $pdf->writeHTMLCell(11,0, $x, $y,$txt, $border, 1, 0, true, '', true);

	    $x = $pdf->getX()+9.5; //

	    $border = array(
		   'B' => array('width' => 0.1, 'color' => array(0,0,0)),
		   'R' => array('width' => 0.1, 'color' => array(0,0,0)),
		);
		
		$txt = '' ;
	   	if ($data['bBank'] == 4) $txt ='專戶帳號：104-018-1000199-9';
		else if ($data['bBank'] == 6) $txt ='專戶帳號：126-018-0001599-9';
		
	    $pdf->writeHTMLCell(7.5,0, $x, $y,$txt, $border, 1, 0, true, '', true);
	    ################
		
	    $x = $pdf->getX()-1;
	    $y = $pdf->getY();
	   
	   	$y3 = $y; //畫線用
		$y += 0.1 ;
		$pdf->SetFont('msungstdlight', 'B', 14);
		$txt = '指示內容：';
		$pdf->writeHTMLCell(11,0, $x, $y,$txt, 0, 1, 0, true, '', true);

		$x = $pdf->getX()+9.5; //
	    $pdf->writeHTMLCell(7.5,0, $x, $y,'', 0, 2, 0, true, '', true);

	    $pdf->SetFont('msungstdlight', '', 12);
		
	   	$txt = '一、解 款 行：'.$transBank['main'].'/'.$transBank['branch'] ; //
	    $pdf->writeHTMLCell(11,0, 1.5, $pdf->getY()+0.1,$txt, 0, 1, 0, true, '', true);
				
		$txt = '二、錯誤資料：' ;
		$pdf->writeHTMLCell(11,0, 1.5, $pdf->getY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '戶名(1)：'.$data['bEaccountName'] ;
		$pdf->writeHTMLCell(9.5,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '帳號(1)：'.$data['bEaccount'] ;
		$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '金額(1)：'.$data['bEmoney'] ;
		// if ($txt == 0) $txt = '' ;
		$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);

		for ($i=0; $i < count($data_Error); $i++) { 
			$txt = '戶名('.($i+2).')：'.$data_Error[$i]['bEaccountName'] ;
			$pdf->writeHTMLCell(9.5,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
			
			$txt = '帳號('.($i+2).')：'.$data_Error[$i]['bEaccount'] ;
			$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
			
			$txt = '金額('.($i+2).')：'.$data_Error[$i]['bEmoney'] ;
			// if ($txt == 0) $txt = '' ;
			$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		}
		
		$txt = '三、更正資料：' ;
		$pdf->writeHTMLCell(11,0, 1.5, $pdf->getY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '戶名(1)：'.$data['bCaccountName'] ;
		$pdf->writeHTMLCell(9.5,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '帳號(1)：'.$data['bCaccount'] ;
		$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = '金額(1)：'.$data['bCmoney'] ;
		// if ($txt == 0) $txt = '' ;
		$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);

		for ($i=0; $i < count($data_Correct); $i++) { 
			$txt = '戶名('.($i+2).')：'.$data_Correct[$i]['bEaccountName'] ;
			$pdf->writeHTMLCell(9.5,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
			
			$txt = '帳號('.($i+2).')：'.$data_Correct[$i]['bEaccount'] ;
			$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
			
			$txt = '金額('.($i+2).')：'.$data_Correct[$i]['bEmoney'] ;
			// if ($txt == 0) $txt = '' ;
			$pdf->writeHTMLCell(11,0, 2.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		}
		
		$pdf->SetY($pdf->GetY()+1) ;
		$txt = '四、其他：' ;
		$pdf->writeHTMLCell(11,0, 1.5, $pdf->GetY()+0.1,$txt, 0, 1, 0, true, '', true);
		
		$txt = $data['bOther'] ;
		$y = $pdf->GetY();
		if (empty($txt)) {
			// $pdf->Line(4,$y,11,$y) ;
			$pdf->Line(4, $y, 11, $y,array('width' => 0.01)) ;
		}
		else {
			$txt = '<u>'.$txt.'</u>' ;
			$pdf->writeHTMLCell(11,0, 1.5, $pdf->GetY(),$txt, 0, 1, 0, true, '', true);
		}
		
	   	//撐高用
	    $x = $pdf->getX();
	    $y = $pdf->getY()+2;
	    $pdf->MultiCell(11,0, '', 0, 'L', false, 1, 5, $y,true,0,false);

		$y = $pdf->getY();
		$y4 = $y;//畫線用
		$y = $y-6;
		$x = $pdf->getX()+9.5;
		$loc_y = 13;//印章固定位置
		$pdf->SetFont('msungstdlight', 'B', 14);
		$txt ='有權簽章人簽章：';
		$pdf->Text(12,$loc_y,$txt) ;

		$img_file ='images/stamp.png';
		$pdf->Image($img_file, ($x+0.2), ($loc_y+1),6,3.43);

		$pdf->SetFont('msungstdlight', '', 12);
		$pdf->Line(12, 18, 19, 18) ;

		##############################################
		$y = $pdf->getY();
		$h = $y4-$y3; 
		$border = array(
		  	'T' => array('width' => 0.1, 'color' => array(0,0,0)),	
		   	'L' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'B' => array('width' => 0.1, 'color' => array(0,0,0)),
		);
		
	   	$pdf->writeHTMLCell(11,$h, 1.4, $y3,'', $border, 2, 0, true, '', true);

	   	$border = array(
		  	'T' => array('width' => 0.05, 'color' => array(0,0,0)),	
		   
		);
		
	   	$pdf->writeHTMLCell(18,$h1,1.4, ($y3+0.1),'', $border, 2, 0, true, '', true);

	 	//中間單線
	   	$h = $y3-$y2; 
	   
	   	$border = array(
		  	'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		);
	   	$pdf->writeHTMLCell(0.1,$h, 11.8, $y2,'', $border, 2, 0, true, '', true);

	   	//中間雙線
	   	$h = $y4-$y3; 
	   
	   	$border = array(
		  	'R' => array('width' => 0.01, 'color' => array(0,0,0)),
		);
	   	$pdf->writeHTMLCell(0.1,$h, 11.8, $y3,'', $border, 2, 0, true, '', true);

	   	$pdf->writeHTMLCell(0.1,$h, 11.85, $y3,'', $border, 2, 0, true, '', true);

	   		$border = array(
		  	'T' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'R' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'B' => array('width' => 0.1, 'color' => array(0,0,0)),
		);
		
	   	$pdf->writeHTMLCell(8.4,$h, 11, $y3,'', $border, 2, 0, true, '', true);
	   ###############################
	   	$x = 1.5;
	   	$y = $pdf->getY()+1;
	   	 $border = array(
		  	'T' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'R' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'L' => array('width' => 0.1, 'color' => array(0,0,0)),
		   	'B' => array('width' => 0.1, 'color' => array(0,0,0)),
		);

	    
	   	$txt = '永豐銀行執行狀況<font size="10px">（以下由永豐銀行填寫）</font>：';
	   	$pdf->writeHTMLCell(18,3, $x, $y,$txt, $border, 2, 0, true, '', true);
	
	
	
	$pdf->Output() ;
	// $pdf->Output();
	
// ?>