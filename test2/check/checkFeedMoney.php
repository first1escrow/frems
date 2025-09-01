<?php
ini_set("display_errors", "On"); 
error_reporting(E_ALL & ~E_NOTICE);
include_once '../../openadodb.php';
include_once '../../session_check.php' ;
$_POST = escapeStr($_POST) ;
if ($_POST) {

    //(cc.cCaseStatus=2 OR( cc.cEndDate <='2016-07-31 23:59:59' AND cc.cEndDate >='2016-07-01 00:00:00')) AND ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0 
    
    if ($_POST['cCertifiedId']) {
       $str = "AND cc.cCertifiedId ='".$_POST['cCertifiedId']."'";
    }

    if ($_POST['status']) {
        $str = " AND cc.cCaseStatus = '".$_POST['status']."'";
    }

    if ($_POST['month']) {
        $start = $_POST['year']."-".str_pad($_POST['month'],2,'0',STR_PAD_LEFT)."-01 00:00:00";
        $end = $_POST['year']."-".str_pad($_POST['month'],2,'0',STR_PAD_LEFT)."-31 23:59:59";
        $str = "AND cc.cEndDate >= '".$start."' AND cc.cEndDate <= '".$end."'";
    }

   $sql ="SELECT 
            cc.cCertifiedId AS cCertifiedId,
            ci.cTotalMoney AS cTotalMoney,
            ci.cCertifiedMoney as cerifiedmoney,
            cc.cSignDate AS cSignDate,
            cc.cEndDate AS cEndDate,
            ci.cFirstMoney as cFirstMoney,
            cr.cBranchNum AS branch,
            cr.cBranchNum1 AS branch1,
            cr.cBranchNum2 AS branch2,
            cr.cBrand AS brand,
            cr.cBrand1 AS brand1,
            cr.cBrand2 AS brand2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS bRecall1,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS bRecall2,
            (SELECT bRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS bRecall3,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum)  AS scrRecall1,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum1)  AS scrRecall2,
            (SELECT bScrRecall FROM tBranch WHERE bId=cr.cBranchNum2)  AS scrRecall3,
            (SELECT sRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sRecall,
            (SELECT sSpRecall FROM tScrivener WHERE sId=cs.cScrivener) AS sSpRecall,
            cc.cBranchRecall AS cBranchRecall,
            cc.cBranchRecall1 AS cBranchRecall1,
            cc.cBranchRecall2 AS cBranchRecall2,
            cc.cBranchScrRecall AS cBranchScrRecall,
            cc.cBranchScrRecall1 AS cBranchScrRecall1,
            cc.cBranchScrRecall AS cBranchScrRecall2,                        
            cc.cCaseFeedBackMoney AS cCaseFeedBackMoney,
            cc.cCaseFeedBackMoney1 AS cCaseFeedBackMoney1,
            cc.cCaseFeedBackMoney2 AS cCaseFeedBackMoney2,
            cc.cSpCaseFeedBackMoney AS cSpCaseFeedBackMoney,
            cc.cScrivenerSpRecall AS cScrivenerSpRecall,
            cc.cCaseFeedback AS cCaseFeedback,
            cc.cCaseFeedback1 AS cCaseFeedback1,
            cc.cCaseFeedback2 AS cCaseFeedback2,
            cc.cFeedbackTarget AS cFeedbackTarget,
            cc.cFeedbackTarget1 AS cFeedbackTarget1,
            cc.cFeedbackTarget2 AS cFeedbackTarget2          
        FROM 
            tContractCase AS cc
        JOIN tContractRealestate AS cr ON cr.cCertifyId=cc.cCertifiedId
        JOIN tContractIncome AS ci ON ci.cCertifiedId=cc.cCertifiedId
        JOIN tContractScrivener AS cs  ON cs.cCertifiedId = cc.cCertifiedId
        WHERE 
            ci.cTotalMoney !=0 AND cc.cCaseFeedBackModifier ='' AND ci.cCertifiedMoney !=0 ".$str."
            ORDER BY cc.cSignDate ASC";
    // echo $sql;
       
    $rs = $conn->Execute($sql);

    while (!$rs->EOF) {
        # code...
        $list[] = $rs->fields;
        $rs->MoveNext();
    }



    for ($i=0; $i < count($list); $i++) {  //cBranchRecall cBranchScrRecall
        $data[$i]['cSignDate'] = $list[$i]['cSignDate'];
        $data[$i]['cEndDate'] = $list[$i]['cEndDate'];
        $data[$i]['cTotalMoney'] = $list[$i]['cTotalMoney'];
        $data[$i]['cCertifiedMoney'] = $list[$i]['cerifiedmoney'];
        $data[$i]['org_cCaseFeedBackMoney'] = $list[$i]['cCaseFeedBackMoney'];
        $data[$i]['org_cCaseFeedBackMoney1'] = $list[$i]['cCaseFeedBackMoney1'];
        $data[$i]['org_cCaseFeedBackMoney2'] = $list[$i]['cCaseFeedBackMoney2'];
        $data[$i]['org_cSpCaseFeedBackMoney'] = $list[$i]['cSpCaseFeedBackMoney'];
        $data[$i]['org_cBranchRecall'] = $list[$i]['cBranchRecall'];
        $data[$i]['org_cBranchRecall1'] = $list[$i]['cBranchRecall1'];
        $data[$i]['org_cBranchRecall2'] = $list[$i]['cBranchRecall2'];
        $data[$i]['org_cBranchScrRecall'] = $list[$i]['cBranchScrRecall'];
        $data[$i]['org_cBranchScrRecall1'] = $list[$i]['cBranchScrRecall1'];
        $data[$i]['org_cBranchScrRecall2'] = $list[$i]['cBranchScrRecall2'];
        $data[$i]['org_cScrivenerSpRecall'] = $list[$i]['cScrivenerSpRecall'];

        if ($list[$i]['cSpCaseFeedBackMoney'] == '') {
            $data[$i]['org_cSpCaseFeedBackMoney'] = 0;
        }

        if ($list[$i]['cCaseFeedback'] == 1) {
            $data[$i]['org_msg'] = '不回饋'; 
        }
        

        $tmpCerifiedMoney = ($list[$i]['cTotalMoney']-$list[$i]['cFirstMoney']) * 0.0006;
        $bcount = 0;

         $data[$i]['tmpCerifiedMoney'] = $tmpCerifiedMoney;


         if (($list[$i]['cerifiedmoney'] + 10) < $tmpCerifiedMoney) {  //不回饋
            
            $uSql[] = 'cCaseFeedback = 1,cCaseFeedback1 = 1,cCaseFeedback2 = 1';
            $uSql[] = 'cCaseFeedBackMoney =0,cCaseFeedBackMoney1 =0,cCaseFeedBackMoney2 =0,cSpCaseFeedBackMoney=0';
            $data[$i]['msg'] = '不回饋';         
         }else{
            //確認店家數及地政回饋比率
            if ($list[$i]['branch'] > 0) {

                if ($list[$i]['cFeedbackTarget'] == 2) {//scrivener
                    $brecall[0] = $list[$i]['sRecall'];
                }else{
                    $brecall[0] = $list[$i]['bRecall1'];
                }

                if ($list[$i]['scrRecall1'] != '' || $list[$i]['scrRecall1'] != '0') {
                    

                    $scrRecall[0] = $list[$i]['scrRecall1'];//仲介回饋地政士
                    $scrRePart = $list[$i]['scrRecall1'];//仲介回饋地政士
                    
                }
                                     
                $bcount++;
            }

            if ($list[$i]['branch1'] > 0) {
                
                if ($list[$i]['cFeedbackTarget1'] == 2) {//scrivener
                    $brecall[1] = $list[$i]['sRecall'];

                }else{
                    $brecall[1] = $list[$i]['bRecall2'];
                }

                //比對比率是否一樣 ()

                if ($brecall[0] == $brecall[1]) {
                    $type = 1;
                }

               

                if ($list[$i]['scrRecall2'] != '' || $list[$i]['scrRecall2'] != '0') {
                    $scrRecall[1] = $list[$i]['scrRecall2'];//仲介回饋地政士
                    $scrRePart = $list[$i]['scrRecall2'];//仲介回饋地政士
                }
                                     
                $bcount++;
            }

            if ($list[$i]['branch2'] > 0) {
                if ($list[$i]['cFeedbackTarget2'] == 2) {//scrivener
                    $brecall[2] = $list[$i]['sRecall'];
                }else{
                    $brecall[2] = $list[$i]['bRecall3'];
                }

                if ($list[$i]['scrRecall3'] != '' || $list[$i]['scrRecall3'] != '0') {
                    $scrRecall[2] = $list[$i]['scrRecall3'];//仲介回饋地政士
                    $scrRePart = $list[$i]['scrRecall3'];//仲介回饋地政士
                }
                                     
                $bcount++;
            }



            // sort($scrRecall);//目前仲介回饋給地政士都是同比率

            if ($bcount == 1) { //只有一間店
                 $_feedbackMoney = round(($brecall[0]/100)*$list[$i]['cerifiedmoney']);

                 $uSql[] = "cCaseFeedBackMoney = '".$_feedbackMoney."',cCaseFeedBackMoney1=0,cCaseFeedBackMoney2=0";
                $data[$i]['cCaseFeedBackMoney'] = $_feedbackMoney;
                $data[$i]['cCaseFeedBackMoney1'] = 0;
                $data[$i]['cCaseFeedBackMoney2'] = 0;
                if ($scrRePart != 0) {
                                
                    $scrFeedMoney = round(($scrRePart/100)*$list[$i]['cerifiedmoney']) ;
            
                    $uSql[] = "cSpCaseFeedBackMoney = '".$scrFeedMoney."'";
                    $data[$i]['cSpCaseFeedBackMoney'] = $scrFeedMoney;

                }else{
                    $data[$i]['cSpCaseFeedBackMoney'] = 0;
                }
             
            }else if($bcount > 1){
                

                if ($type == 1) { //一樣比率的案件，照舊的算法算 ( (保證費*回饋對象%數)/店家數)

                    $_feedbackMoney = round(($brecall[0]/100)*$list[$i]['cerifiedmoney']);
                    $_fdMod = $_feedbackMoney % $bcount ;
                    $_feedbackMoney =floor($_feedbackMoney / $bcount) ;
                    
                    $uSql[] = "cCaseFeedBackMoney = '".($_feedbackMoney+$_fdMod)."',cCaseFeedBackMoney1='".$_feedbackMoney."'";
                    $data[$i]['cCaseFeedBackMoney'] = ($_feedbackMoney+$_fdMod);
                    $data[$i]['cCaseFeedBackMoney1'] = $_feedbackMoney;
                    if ($bcount == 3) {
                        $uSql[] = "cCaseFeedBackMoney2='".$_feedbackMoney."'";
                        $data[$i]['cCaseFeedBackMoney2'] = $_feedbackMoney;
                    }else{
                        $data[$i]['cCaseFeedBackMoney2'] = 0;
                    }
                               
                }else{
                              
                    $_feedbackMoney = round((($brecall[0]/100)*$list[$i]['cerifiedmoney'])/$bcount);
                    $uSql[] = "cCaseFeedBackMoney='".$_feedbackMoney."'";
                    $data[$i]['cCaseFeedBackMoney'] = $_feedbackMoney;
 
                    $_feedbackMoney = round((($brecall[1]/100)*$list[$i]['cerifiedmoney'])/$bcount);

                    $uSql[] = "cCaseFeedBackMoney1='".$_feedbackMoney."'";
                    $data[$i]['cCaseFeedBackMoney1'] = $_feedbackMoney;

                    if ($bcount == 3) {
                        $_feedbackMoney = round((($brecall[2]/100)*$list[$i]['cerifiedmoney'])/$bcount);
                        $uSql[] = "cCaseFeedBackMoney2='".$_feedbackMoney."'";
                        $data[$i]['cCaseFeedBackMoney2'] = $_feedbackMoney;
                    }else{
                        $data[$i]['cCaseFeedBackMoney2'] = 0;
                    }
                    
                }  
                              
                //仲介地政士回饋
                // if ($scrRePart != '') {
                //     $scrFeedMoney = round(($scrRePart/100)*$list[$i]['cerifiedmoney']) ;
                //      $uSql[] = "cSpCaseFeedBackMoney='".$scrFeedMoney."'";
                //      $data[$i]['cSpCaseFeedBackMoney'] = $scrFeedMoney;
                // }

                

                 //仲介地政士回饋
                if ($scrRePart != '') {
                    

                    if ($list[$i]['cFeedbackTarget'] == 1 && $scrRecall[0] !='') {
                        $scrFeedMoney = round(($scrRecall[0]/100)*$list[$i]['cerifiedmoney']) ;
                       
                    }

                    if ($list[$i]['cFeedbackTarget1'] == 1 && $scrRecall[1] !='') {
                        $scrFeedMoney = round(($scrRecall[1]/100)*$list[$i]['cerifiedmoney']) ;
                        
                    }

                    if ($list[$i]['cFeedbackTarget1'] == 1 && $scrRecall[2] !='') {
                        $scrFeedMoney = round(($scrRecall[2]/100)*$list[$i]['cerifiedmoney']) ;
                        
                    }


                    

                    if ($scrFeedMoney != 0 && $scrFeedMoney != '') {
                         $data[$i]['cSpCaseFeedBackMoney'] = $scrFeedMoney;
                    }

                    // $scrFeedMoney = round(($scrRePart/100)*$list[$i]['cerifiedmoney']) ;
                    //  $uSql[] = "cSpCaseFeedBackMoney='".$scrFeedMoney."'";
                }
            }

             ##特殊回饋金計算
            if ($scrRePart == '' || $scrRePart == 0) {    
            // echo $list[$i]['brand']."<br>";
                $check = 0;
                if ($list[$i]['brand']!=1&&$list[$i]['brand']!=49&&$list[$i]['brand']!=2) {//不是優美跟台屋的//不為非仲介成交

                    $check=1;
                            
                }elseif ($list[$i]['brand1']!=1&&$list[$i]['brand1']!=49&&$list[$i]['brand1']!=0&&$list[$i]['brand1']!=2) {
                    $check=1;
                }elseif ($list[$i]['brand2']!=1&&$list[$i]['brand2']!=49&&$list[$i]['brand2']!=0&&$list[$i]['brand2']!=2) {
                    $check=1;
                }
                // echo $v['brand']." ".$v['sSpRecall']." ".$check." ";
                if($list[$i]['sSpRecall']!=0&&$check==1)
                {
                        
                        $val = $list[$i]['sSpRecall'] / 100; //百分之X
                        
                        $spFb = round($list[$i]['cerifiedmoney'] * $val);//總回饋金

                        $uSql[] = "cSpCaseFeedBackMoney='".$spFb."'";
                        $data[$i]['cSpCaseFeedBackMoney'] = $spFb;
                       
                }
            }
            ##

         }

         if ($data[$i]['cSpCaseFeedBackMoney'] == ''  ) {
             $data[$i]['cSpCaseFeedBackMoney'] = 0;
         }

        $data[$i]['cBranchRecall'] = $brecall[0];
        $data[$i]['cBranchRecall1'] = $brecall[1];
        $data[$i]['cBranchRecall2'] = $brecall[2];

        $data[$i]['cBranchScrRecall'] = $scrRecall[0];
        $data[$i]['cBranchScrRecall1'] = $scrRecall[1];
        $data[$i]['cBranchScrRecall2'] = $scrRecall[2];

       

        $data[$i]['cScrivenerRecall'] = $list[$i]['cScrivenerRecall'];
        $data[$i]['cScrivenerSpRecall'] = $list[$i]['sSpRecall'];
        
        $data[$i]['cCertifiedId'] = $list[$i]['cCertifiedId'];

        $uSql[] = "cBranchRecall = '".$brecall[0]."',cBranchRecall1 = '".$brecall[1]."',cBranchRecall2 = '".$brecall[2]."'";
        $uSql[] = "cBranchScrRecall = '".$scrRecall[0]."',cBranchScrRecall1 = '".$scrRecall[1]."',cBranchScrRecall2 = '".$scrRecall[2]."'";
        $uSql[] = "cScrivenerRecall='".$list[$i]['cScrivenerRecall']."',cScrivenerSpRecall = '".$list[$i]['sSpRecall']."'";
        $str = implode(',', $uSql);
        $sql = "UPDATE tContractCase SET ".$str." WHERE cCertifiedId ='".$list[$i]['cCertifiedId']."';";

        // echo $sql."<br>";

        // $conn->Execute($sql);
        unset($brecall);unset($scrRecall);unset($uSql);unset($type);unset($scrFeedMoney);

        // write_log($id.":".$sql."\r\n",'checkFeedPart');
         //cBranchRecall cBranchScrRecall cScrivenerRecall cScrivenerSpRecall 回饋比率寫入
        
        $cCertifiedId[] = $list[$i]['cCertifiedId'];
    }
}



?>
<style type="text/css">
    .org{
       background: #FF7878;
    }
    .now{/*blue NOW*/
        background: #6EFFFF;
    }
    .info{
       background: #FFCC6E;

        
    }
    .diff{
        
        background: #FFFFFF;
        color:#FF0000;
    }
   /* td{
        border:1px #FFF solid;
    }
    th{
        border:1px #FFF solid;
    }*/
    table{
        border: 2px #000 solid;
    }

</style>

<form action="" method="POST">
    保證號碼:<input type="text" name="cCertifiedId" id="" style="80px">
    案件狀態: <select name="status" size="1" style="width:160px;">
                <option value="">全部</option>
                <option value="2">進行中</option>
        <option value="3">已結案</option>
        <option value="4">解約/終止履保</option>
        <option value="6">異常</option>
        <option value="8">作廢</option>
        <option value="9">發函終止</option>

            </select>
    結案日期:
    <select name="year" id="">
        <option value="">請選擇</option>
        <?php
            $y = date('Y');

            for ($i=2012; $i <= $y; $i++) { 
                echo "<option value='".$i."'>".$i."</option>";
            }
             
        ?>
       
    </select>年
    
    <select name="month" id="">
        <option value="">請選擇</option>
        <?php
            for ($i=1; $i < 13 ; $i++) { 
                echo "<option value='".$i."'>".$i."</option>";
            }
        ?>
    </select>  月  
    <input type="submit" value="查詢">
</form>

 <table cellpadding="2" cellspacing="2" >
    <tr>
        <th class="info">保證號碼</th>
        <th class="info">簽約日期</th>
        <th class="info">結案日期</th>
        <th class="info">總價金</th>
        <th class="info">保證費</th>
        <th class="info">實收保證費</th>
        <th class="org">原回饋比率1</th>
        <th class="org">原回饋給地政比率1</th>
        <th class="org">原回饋比率2</th>
        <th class="org">原回饋給地政比率2</th>
        <th class="org">原回饋比率3</th>
        <th class="org">原回饋給地政比率3</th>
        <th class="org">原地政士特殊回饋</th>
        <th class="org">原回饋金1</th> 
        <th class="org">原回饋金2</th>
        <th class="org">原回饋金3</th>
        <th class="org">原回饋給地政(SP)</th>
        <th class="org">原不回饋</th>

        <th class="now">回饋比率1</th>
        <th class="now">回饋給地政比率1</th>
        <th class="now">回饋比率2</th>
        <th class="now">回饋給地政比率2</th>
        <th class="now">回饋比率3</th>
        <th class="now">回饋給地政比率3</th>
        <th class="now">地政士特殊回饋</th>
        <th class="now">回饋金1</th>        
        <th class="now">回饋金2</th>
        <th class="now">回饋金3</th>
        <th class="now">回饋給地政(SP)</th>
        <th class="now">不回饋</th>
    </tr>
    <?php
        for ($i=0; $i < count($data); $i++) { 

            $cc ='';$cc2 = '';

            echo "<tr>";
            echo "<td class='info'>".$data[$i]['cCertifiedId']."</td>";
            echo "<td class='info'>".$data[$i]['cSignDate']."</td>";
            echo "<td class='info'>".$data[$i]['cEndDate']."</td>";
            echo "<td class='info'>".$data[$i]['cTotalMoney']."</td>";

            echo "<td class='info'>".$data[$i]['tmpCerifiedMoney']."</td>";
            echo "<td class='info'>".$data[$i]['cCertifiedMoney']."</td>";

            echo "<td class='org'>".$data[$i]['org_cBranchRecall']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cBranchScrRecall']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cBranchRecall1']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cBranchScrRecall1']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cBranchRecall2']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cBranchScrRecall2']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cScrivenerSpRecall']."</td>";
           
            echo "<td class='org'>".$data[$i]['org_cCaseFeedBackMoney']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cCaseFeedBackMoney1']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cCaseFeedBackMoney2']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_cSpCaseFeedBackMoney']."&nbsp;</td>";
            echo "<td class='org'>".$data[$i]['org_msg']."&nbsp;</td>";

            
            if (($data[$i]['org_cCaseFeedBackMoney'] != $data[$i]['cCaseFeedBackMoney']) ||
                ($data[$i]['org_cCaseFeedBackMoney1'] != $data[$i]['cCaseFeedBackMoney1']) ||
                ($data[$i]['org_cCaseFeedBackMoney2'] != $data[$i]['cCaseFeedBackMoney2']) ||
                ($data[$i]['org_cSpCaseFeedBackMoney'] != $data[$i]['cSpCaseFeedBackMoney'])
                ) {
                $cc = "diff";
            }

            if ($data[$i]['org_msg'] == '不回饋' && $data[$i]['msg'] == '不回饋') {
                $cc ='';
            }

           
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchRecall']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchScrRecall']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchRecall1']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchScrRecall1']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchRecall2']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cBranchScrRecall2']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cScrivenerSpRecall']."&nbsp;</td>";

            echo "<td class='now ".$cc."'>".$data[$i]['cCaseFeedBackMoney']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cCaseFeedBackMoney1']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cCaseFeedBackMoney2']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['cSpCaseFeedBackMoney']."&nbsp;</td>";
            echo "<td class='now ".$cc."'>".$data[$i]['msg']."&nbsp;</td>";
            // cCaseFeedBackMoney cSpCaseFeedBackMoney
            echo "</tr>";
            unset($cc);
        }
    ?>
</table> 