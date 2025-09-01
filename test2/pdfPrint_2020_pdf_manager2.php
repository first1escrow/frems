<?php
                                                // 建立 FPDF
$pdf = new PDF1() ;                                                         // 建立 FPDF
$cell_width = 200;
$cell_height = 8;
$cell_height2 = 10;
$cell_height3 = 5;
$pdf->Open() ;                                                              // 開啟建立新的 PDF 檔案
$pdf->SetAuthor('first') ;                                          // 設定作者
$pdf->SetAutoPageBreak(1,2) ;                                               // 設定自動分頁並指定距下方邊界1mm
$pdf->SetMargins(5,5,5) ;                                                   // 設定顯示邊界 (左、上、右)
$pdf->AddUniCNShwFont('uni');                                               // 設定為 UTF-8 顯示輸出
$pdf->AddPage() ;     
$pdf->SetFont('uni','',12) ;

##



//判斷店家名稱長度
$h = 8 ;
$hStore = checkHeight($data['code'].$data['sStoreName'],20) ;
$hSales = checkHeight(@implode(',', $sales),4) ;
$hSalesMobile = checkHeight(@implode(',', $salesMobile),11) ;


if ($hStore > $hSales ) {
    // echo 'A';
    $mul = $hStore;
    $hStore = $h;

    if ($hSales <= 1) $hSales = $h * $mul ;
    else $hSales = $h ;

    if ($hSalesMobile <= 1) $hSalesMobile = $h * $mul ;
    else $hSalesMobile = $h ;

}elseif ($hSales > $hStore) {
    // echo 'B';
    $mul = $hSales;
    $hSales = $h;

    if ($hStore <= 1) $hStore = $h * $mul ;
    else $hStore = $h ;

    if ($hSalesMobile <= 1) $hSalesMobile = $h * $mul ;
    else $hSalesMobile = $h ;

}else{
    $mul = 1;
    $hStore  = $hSales = $hSalesMobile = $h;

   
}

$h *= $mul ;

// echo $hSales."_".$hStore."_".$hSalesMobile."_".$h;

// die;
$pdf->setFillColor(255,153,153); 
$pdf->Cell($cell_width,$cell_height,'回饋金報表',1,1,'C',1) ;

$Y = $pdf->GetY() ;
$pdf->Cell(16,$h,'名稱：',1,0,'C',0) ;
// $pdf->Cell(60,$h,$data['code'].$data['sStoreName'],1,0,'C',0) ;
$pdf->MultiCell(70,$hStore,$data['code'].$data['sStoreName'],1,'L') ;


$X = 5+16+70 ;
$pdf->SetXY($X,$Y) ;

$pdf->Cell(25,$h,'專屬顧問：',1,0,'C',0) ;
// $pdf->Cell(21,$hSales,@implode(',', $sales),1,0,'C',0) ;
$pdf->MultiCell(21,$hSales,@implode(',', $sales),1,'L') ;

$X = $X+25+21 ;
$pdf->SetXY($X,$Y) ;
$pdf->Cell(31,$h,'專屬顧問電話：',1,0,'C',0) ;
// $pdf->Cell(23,$hSalesMobile,@implode(',', $salesMobile),1,1,'C',0) ;
$pdf->MultiCell(37,$hSalesMobile,@implode(',', $salesMobile),1,'L') ;

$pdf->Cell($cell_width,$cell_height,'結算時間：'.$endTime,1,1,'L',0) ;

unset($h);
###
$pdf->SetFont('uni','',10) ;
$pdf->Cell(10,$cell_height2,'序號',1,0,'L',0) ;
$pdf->Cell(18,$cell_height2,'結案日期',1,0,'L',0) ;
$pdf->Cell(15,$cell_height2,'店編號',1,0,'L',0) ;
$pdf->Cell(28,$cell_height2,'店名',1,0,'L',0) ;

$pdf->Cell(18,$cell_height2,'保證號碼',1,0,'L',0) ;
$pdf->Cell(19,$cell_height2,'買方',1,0,'L',0) ;
$pdf->Cell(19,$cell_height2,'賣方',1,0,'L',0) ;
$pdf->Cell(24,$cell_height2,'買賣總價金',1,0,'L',0) ;
$pdf->Cell(15,$cell_height2,'保證費',1,0,'L',0) ;
$pdf->Cell(18,$cell_height2,'回饋金額',1,0,'L',0) ;
$pdf->Cell(16,$cell_height2,'案件類型',1,1,'L',0);

$no = 1;
$total = $certifiedMoney = $feedbackMoney = 0;
if (count($dataCase) > 0) {
    foreach ($dataCase as $k => $v) {


            $sql = "SELECT
                        (SELECT bStore FROM tBranch WHERE bId = cBranchNum) AS bStore,
                        (SELECT bStore FROM tBranch WHERE bId = cBranchNum1) AS bStore1,
                        (SELECT bStore FROM tBranch WHERE bId = cBranchNum2) AS bStore2,
                        (SELECT bStore FROM tBranch WHERE bId = cBranchNum3) AS bStore3,
                        CONCAT((Select bCode From `tBrand` c Where c.bId = cBrand ),LPAD(cBranchNum,5,'0')) as bCode,
                        CONCAT((Select bCode From `tBrand` c Where c.bId = cBrand1 ),LPAD(cBranchNum1,5,'0')) as bCode1,
                        CONCAT((Select bCode From `tBrand` c Where c.bId = cBrand2 ),LPAD(cBranchNum2,5,'0')) as bCode2,
                        CONCAT((Select bCode From `tBrand` c Where c.bId = cBrand3 ),LPAD(cBranchNum3,5,'0')) as bCode3,
                        cBranchNum,
                        cBranchNum1,
                        cBranchNum2,
                        cBranchNum3
                    FROM
                        tContractRealestate
                    WHERE cCertifyId = '".$v['sCertifiedId']."'";

                   
            $rs = $conn->Execute($sql);
            $code = array();
            $store = array();
            if ($rs->fields['cBranchNum'] > 0 && substr($data['code'], 0,2) == substr($rs->fields['bCode'], 0,2)) {
                array_push($code, $rs->fields['bCode']);
                array_push($store, n2w($rs->fields['bStore']));

            }

            if ($rs->fields['cBranchNum1'] > 0 && substr($data['code'], 0,2) == substr($rs->fields['bCode1'], 0,2)) {
               array_push($code, $rs->fields['bCode1']);
               array_push($store, $rs->fields['bStore1']);
            }

            if ($rs->fields['cBranchNum2'] > 0 && substr($data['code'], 0,2) == substr($rs->fields['bCode2'], 0,2)) {
               array_push($code, $rs->fields['bCode2']);
               array_push($store, $rs->fields['bStore2']);
            }

            if ($rs->fields['cBranchNum3'] > 0 && substr($data['code'], 0,2) == substr($rs->fields['bCode3'], 0,2)) {
               array_push($code, $rs->fields['bCode3']);
               array_push($store, $rs->fields['bStore3']);
            }



        $v['sEndDate'] = (substr($v['sEndDate'], 0,4)-1911).substr($v['sEndDate'], 4,6);//

        $h = 5;
        $heightArray = array();
        $codeTxt = @implode('', $code) ;
        $storeTxt = @implode('', $store) ;

       
        $bName =  n2w(trim($v['sBuyer'])) ;
        $oName = n2w(trim($v['sOwner'])) ;
        $storeTxt = n2w($storeTxt);
        
        $hBuyer = checkHeight($bName,4) ;
        $hOwner = checkHeight($oName,4) ;

           
        $hCode = checkHeight($codeTxt,7) ;
        $hStore = checkHeight($storeTxt,7) ;
        array_push($heightArray, $hBuyer);
        array_push($heightArray, $hOwner);
        array_push($heightArray, $hCode);
        array_push($heightArray, $hStore);


        // if (count($store) > 1) {
            // echo $v['sCertifiedId'].":".$hBuyer."_".$hOwner."_".$hCode."_".$hStore."<br>";
        // }

            if ($hCode  > $hStore && $hCode  > $hBuyer && $hCode  > $hOwner) {
                $mul = $hCode ;

                $hStore = $h;
                $hBuyer = ($hBuyer <= 1)? ($h * $mul ):$h;
                $hOwner = ($hOwner <= 1)? ($h * $mul ):$h;

                $hCode = $h ; 
            }elseif ($hStore > $hCode && $hStore >$hBuyer && $hStore>$hOwner) {
                $mul = $hStore ;

                $hCode = ($hCode <= 1)? ($h * $mul ):$h;
                $hBuyer = ($hBuyer <= 1)? ($h * $mul ):$h;
                $hOwner = ($hOwner <= 1)? ($h * $mul ):$h;

                $hStore = $h ; 
            }elseif ($hBuyer > $hCode && $hBuyer >$hStore && $hBuyer>$hOwner) {
                $mul = $hBuyer ;

                $hCode = ($hCode <= 1)? ($h * $mul ):$h;

                if ($hStore <= 1) {
                    $hStore = ($h * $mul );
                }elseif ($hStore <  $mul) {
                    $hStore = ($h * $mul*0.5 );
                }else{
                    $hStore = $h;
                }
                // $hStore = ($hStore <= 1)? :$h;
                $hOwner = ($hOwner <= 1)? ($h * $mul ):$h;

                $hBuyer = $h ; 
            }elseif ($hOwner > $hCode && $hOwner >$hStore && $hOwner>$hBuyer) {
                $mul = $hOwner ;

                $hCode = ($hCode <= 1)? ($h * $mul ):$h;
                $hStore = ($hStore <= 1)? ($h * $mul ):$h;
                $hBuyer = ($hBuyer <= 1)? ($h * $mul ):$h;

                $hOwner = $h ; 
            }elseif($hBuyer == $hCode && $hBuyer == $hOwner &&  $hBuyer == $hStore){
                 $mul = 1 ;
               
                $hCode = $h;
                $hStore = $h;
                $hBuyer = $h;
                $hOwner = $h;

            }else {
                rsort($heightArray);
                // echo $v['sCertifiedId'].":".$hBuyer."_".$hOwner."_".$hCode."_".$hStore."<br>";
                $mul = $heightArray[0] ;
                

                $hCode =  ($hCode <= 1)? ($h * $mul ):$h;
                $hStore = ($hStore <= 1)? ($h * $mul ):$h;
                $hBuyer = ($hBuyer <= 1)? ($h * $mul ):$h;
                $hOwner = ($hOwner <= 1)? ($h * $mul ):$h;

                // echo $hBuyer."_".$hOwner."_".$hCode."_".$hStore."<br>";
                // die;
                // $mul = $hOwner ;
                // if ($hBuyer <= 1){
                //     $hBuyer = $h * $mul ;
                // }elseif (($hOwner-$hBuyer) == 1) {

                //    $hBuyer = 5+round(0.56*$hBuyer,2); //1.7 round((6.7/$hOwner),1)

                //    if ($v['sCertifiedId'] == '081017878') {
                //        $hBuyer = 5+round(0.4*$hBuyer,2);
                //    }
                // }else{ 
                //     $hBuyer = $h ;
                // }
                // $hOwner = $h ;
            }

            $h *= $mul ;

        
        // $X = 5 + 10 + 18 + 15 + 28 + 18 + 19 ;
            $X = 5 + 10+ 18 + 15 ;

       
        $pdf->Cell(10,$h,$no,1,0,'C') ;
        
       
        $pdf->Cell(18,$h,$v['sEndDate'],1,0,'L') ;

        $Y = $pdf->GetY() ;
        $pdf->MultiCell(15,$hCode,$codeTxt,1,'L') ;
        $pdf->SetXY($X,$Y) ;
        $pdf->MultiCell(28,$hStore,$storeTxt,1,'L') ;
        
        $X += 28 ;
        $pdf->SetXY($X,$Y) ;
        $pdf->Cell(18,$h,$v['sCertifiedId'],1,0,'C') ;

        $X += 18 ;
        $pdf->SetXY($X,$Y) ;
        $pdf->MultiCell(19,$hBuyer,$v['sBuyer'],1,'L') ;
        
         $X += 19 ;
        $pdf->SetXY($X,$Y) ;
        $pdf->MultiCell(19,$hOwner,$v['sOwner'],1,'L') ;
        // $pdf->Cell(19,$h,$v['sBuyer'],1,0,'C') ;
        // $pdf->Cell(19,$h,$v['sOwner'],1,0,'C') ;
        
         
        
        
        $X += 19 ;
        $pdf->SetXY($X,$Y) ;
        
        $pdf->Cell(24,$h,number_format($v['sTotalMoney']),1,0,'R') ;
        $pdf->Cell(15,$h,number_format($v['sCertifiedMoney']),1,0,'R') ;
        $pdf->Cell(18,$h,number_format($v['sFeedBackMoney']),1,0,'R') ;
        $pdf->Cell(16,$h,$v['sFeedDateCat'],1,1,'L',0);

        $total += $v['sTotalMoney'];
        $certifiedMoney += $v['sCertifiedMoney'];
        $feedbackMoney += $v['sFeedBackMoney'];

        $no++;

        
    }
}else{
    $pdf->Cell($cell_width,$cell_height,'無資料',1,1,'L',0) ;
}

// die;
//合計

$pdf->Cell(10,6,'',1,0,'L') ;
$pdf->Cell(18,6,'',1,0,'L') ;
$pdf->Cell(15,6,'',1,0,'L') ;
$pdf->Cell(28,6,'',1,0,'L') ;
$pdf->Cell(18,6,'',1,0,'L') ;
$pdf->Cell(19,6,'',1,0,'L') ;


$pdf->Cell(19,6,'合計',1,0,'L') ;
// $pdf->Cell(110,6,'合計',1,0,'R') ;
$pdf->Cell(24,6,number_format($total),1,0,'R') ;
$pdf->Cell(15,6,number_format($certifiedMoney),1,0,'R') ;
$pdf->Cell(18,6,number_format($feedbackMoney),1,0,'R') ;
$pdf->Cell(16,6,'',1,1,'L',0);
$pdf->SetFont('uni','',12) ;
//
$no = 1;
if (count($dataAccount)) {
    foreach ($dataAccount as $k => $v) {
        $title = (count($dataAccount) > 1)?'台端約定回饋金收款帳戶('.$no.')':'台端約定回饋金收款帳戶';
        $pdf->Cell($cell_width,$cell_height,$title,1,1,'L',1) ;
        $v['BankMain'] = str_replace('（農金資訊所屬會員）', '', $v['BankMain']);
        $v['BankMain'] = str_replace('（農金資中心所屬會員）', '', $v['BankMain']);
        $pdf->Cell(47.5,6,'銀行',1,0,'L') ;
        $pdf->Cell(47.5,6,$v['BankMain'],1,0,'L') ;
        $pdf->Cell(47.5,6,'分行',1,0,'L') ;
        $pdf->Cell(47.5,6,$v['BankBranch'],1,1,'L') ;

        $pdf->Cell(47.5,6,'指定帳號',1,0,'L') ;
        $pdf->Cell(142.5,6,$v['sBankAccountNo'],1,1,'L') ;

        $pdf->Cell(47.5,6,'戶名',1,0,'L') ;
        $pdf->Cell(142.5,6,$v['sBankAccountName'],1,1,'L') ;

        if (($data['sMethod'] == 3 || $data['sMethod'] == 2) && count($dataAccount) > 1) {
            $pdf->Cell(47.5,6,'收款金額',1,0,'L') ;
            $pdf->Cell(142.5,6,$v['sBankMoney'],1,1,'L') ;

        }
       
        $no++;
    }
}
if ($data['sMethod'] != 0) {
    $pdf->SetTextColor(255,0,0);
    $pdf->Cell($cell_width,$cell_height,'*以上資料確認無誤後，請按確認鍵。  (如需變更資料請洽專屬顧問)',0,1,'L',0) ;
}
$pdf->Ln();
##辦法
$pdf->SetTextColor(0,0,0);
// if ($method != 2 && count($dataAccount) == 1) {
//         $pdf->Cell($cell_width,$cell_height3,'請款辦法：',0,1,'L',0) ;
// }

$pageEnd = $pdf->GetY();
// $pdf->Cell($cell_width,$cell_height3,$pageEnd,0,1,'L',0) ;
if ($pageEnd > 250) {
    $pdf->AddPage();
}

if ($data['sMethod'] == 1) {
    $pdf->Cell($cell_width,$cell_height3,'請款辦法：',0,1,'L',0) ;
    $pdf->Cell(43,$cell_height3,'請開立三聯式發票並將',0,0,'L',0);

    $pdf->SetFont('uni','B',12) ;
    $pdf->Cell(47,$cell_height3,'發票扣抵聯及收執聯正本',0,0,'L',0);

    $pdf->SetFont('uni','',12) ;
    $pdf->Cell(30,$cell_height3,'及回饋金報表寄回。',0,1,'L',0);

    $Y = $pdf->GetY() ;
    $pdf->Cell($cell_width,$cell_height3,'發票抬頭：第一建築經理股份有限公司',0,1,'L',0);
    $pdf->Cell($cell_width,$cell_height3,'統一編號：５３５４９９２０',0,1,'L',0);
    $pdf->Cell($cell_width,$cell_height3,'地　　址：'.$company['Addr2'],0,1,'L',0);
    $pdf->Cell($cell_width,$cell_height3,'品　　名：佣金收入',0,1,'L',0);
    

    $Y2 = $pdf->GetY() ;
    ##
    //銷售額
    $X = 10+130;
    $pdf->SetXY($X,$Y) ;
    // $pdf->Cell(50,5,"銷售額(未稅)",0,'R') ;
    $pdf->Cell(30,5,"銷售額(未稅)",1,0,'R') ;
    $money1 = round($feedbackMoney/1.05);
    $money2 = $feedbackMoney-$money1;

    $pdf->Cell(30,5,'$'.number_format($money1),1,1,'R') ;
    $Y = $pdf->GetY() ;
    $pdf->SetXY($X,$Y) ;
    $pdf->Cell(30,5,"營業稅",1,0,'R') ;
    $pdf->Cell(30,5,'$'.number_format($money2),1,1,'R') ;

    $Y = $pdf->GetY() ;
    $pdf->SetXY($X,$Y) ;
    $pdf->Cell(30,5,"總計",1,0,'R') ;
    $pdf->Cell(30,5,'$'.number_format($feedbackMoney),1,1,'R') ;

    $pdf->SetY($Y2) ;

}elseif ($data['sMethod'] == 2) {
     if (count($dataAccount) > 1) {
            $Y = $pdf->GetY() ;
            
            
            for ($i=0; $i < count($dataAccount); $i++) { 

                if ($i > 0) {
                    $X = $pdf->GetX();
                    $pdf->setY($Y);
                    $pdf->setX(($X+10));

                }
                
                $pdf->Cell(30,30,'用印',1,0,'C',0);
                $X = $pdf->GetX();//恢復原X
                $pdf->setY($Y+12);
                $pdf->setX(($X+5));
                $pdf->Cell(18,18,'用印',1,0,'C',0);


            }

            $pdf->Ln();
            $Y = $pdf->GetY() ;
            $pdf->setY(($Y+10));
            $pdf->Cell($cell_width,$cell_height3,'請款辦法：',0,1,'L',0) ;
            $pdf->Cell($cell_width,$cell_height3,'請將回饋金報表下載用印後傳真至02-2711-2881，並來電確認。',0,1,'L',0);
            $pdf->Cell($cell_width,$cell_height3,'若給付金額達新台幣二萬元以上，依法代扣所得稅款10%。',0,1,'L',0);

        }else{
            $Y = $pdf->GetY() ;
            $X = $pdf->GetX();//恢復原X
            $pdf->Cell($cell_width,$cell_height3,'請款辦法：',0,1,'L',0) ;
            $pdf->Cell($cell_width,$cell_height3,'請將回饋金報表下載用印後傳真至02-2711-2881，並來電確認。',0,1,'L',0);
            $pdf->Cell($cell_width,$cell_height3,'若給付金額達新台幣二萬元以上，依法代扣所得稅款10%。',0,1,'L',0);
            $Y2 = $pdf->GetY() ;
            $pdf->SetTextColor(195,195,195);
            $pdf->setY($Y);
            $pdf->setX(($X+120));
            $pdf->Cell(30,30,'用印',1,0,'C',0);
            $pdf->setY(($Y+12));
            $pdf->setX(($X+160));

            $pdf->Cell(18,18,'用印',1,0,'C',0);

            $pdf->SetTextColor(0,0,0);
            $pdf->setY($Y2);
        }

}else if($data['sMethod'] == 3){
    $pdf->Cell($cell_width,$cell_height3,'請款辦法：',0,1,'L',0) ;
    $pdf->Cell($cell_width,$cell_height3,'按下確認完成後，第一建經將依指示匯入台端約定帳戶。',0,1,'L',0);
    $pdf->Cell($cell_width,$cell_height3,'若給付金額達新台幣二萬元以上，依法代扣所得稅款10%。',0,1,'L',0);
     $pdf->Cell($cell_width,$cell_height3,'若給付金額達新台幣二萬元以上，依全民健康保險法給付日為109/12/31前扣取補充保險費1.91%，給',0,1,'L',0);
    $pdf->Cell($cell_width,$cell_height3,'付日為110/1/1起扣取補充保險費2.11%。',0,1,'L',0);;
}
$pdf->Cell($cell_width,$cell_height3,'洽詢電話：'.$company['tel'].'分機101、分機885、分機888',0,1,'L',0);


// foreach ($data as $k => $v) {

   


    
        // $pdf->Cell(190,20,'結算明細暨付款通知書',0,1,'C',0) ;
    // include dirname(__FILE__).'/pdfHead.php' ;
    // include dirname(__FILE__).'/pdfBody.php' ;
    // include dirname(__FILE__).'/pdfFooter.php' ;

       
        // $pdf->Output() ;



       
   
  
    
    
    // echo $file.$fileName;
    // exit("http://first.nhg.tw/accounting/pdf/".$fileName);
    // 
    // 
    // $cnt ++ ;
// }

$fileName = date('YmdHis').".pdf";
$file = dirname(__FILE__);
$link = $file.$fileName;        
$pdf->Output() ;

// $link = preg_replace("/\/var\/www\/html\//", "http://", $link) ; ///var/www/html/
// $link2 ="/pdf/".$fileName;
?>