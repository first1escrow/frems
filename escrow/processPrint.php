<?php

    // 初始化變數，避免未定義警告
    $cCertifiedId = '';
    $buyerowner   = '';
    $check        = isset($_POST['check']) ? $_POST['check'] : '';

    //從後台來的
    if ($cCertifiedId == '') {
        $cCertifiedId = $_POST['cCertifiedId'];
        $checkFrom    = 1;

    }

    if ($buyerowner == '') { //1買2賣
        $buyerowner = $_POST['cat'];
    }

    // $check 已在上方初始化

    if ($cCertifiedId == '' && $buyerowner == '') {
        header("location:http://www.first1.com.tw/");
        exit;
    }
    $hideProcessing = false; //true隱藏亞洲健康城進度圖
    ###
    //檢查票據是否兌現(日期檢查)
    //$_date=>原始日期, $_dateType=>回覆日期格式('ymd','y','m','d','ym','md'), $_dateForm=>民國('r')、西元('b'), $_divide=>分隔符號, $_minus=>加減日數, $_sat=>是否掠過六日
    function tDate_check($_date, $_dateForm = 'ymd', $_dateType = 'r', $_delimiter = '', $_minus = 0, $_sat = 0)
    {
        $_aDate[0] = (substr($_date, 0, 3) + 1911);
        $_aDate[1] = substr($_date, 3, 2);
        $_aDate[2] = substr($_date, 5);

        //$_cheque_date = implode('-',$_tDate) ;

        //是否遇六日要延後(六延兩天、日延一天)
        if ($_sat == '1') {
            $_ss = 0;
            $_ss = date("w", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0]));
            if ($_ss == '0') { //如果是星期日的話，則延後一天
                                   // if ($_minus < 0) {
                                   //  $_minus =  $_minus + $_minus + $_minus ;
                                   // }
                                   // else {
                                   //  $_minus = $_minus + $_minus ;
                                   // }
                $weekend = 1;
            } else if ($_ss == '6') { //如果是星期六的話，則延後兩天
                                          // if ($_minus < 0) {
                                          //  $_minus = $_minus + $_minus ;
                                          // }
                                          // else {
                                          //  $_minus =  $_minus + $_minus + $_minus ;
                                          // }
                $weekend = 2;
            }
        }
                                                                                                  ##
        $_minus = $_minus + $weekend;                                                             //傳進來的日期必須加上遇到加日延後的日期
        $_t     = date("Y-m-d", mktime(0, 0, 0, $_aDate[1], ($_aDate[2] + $_minus), $_aDate[0])); //設定日期為 t+1 天
        unset($_aDate);

        $_aDate = explode('-', $_t);

        if ($_dateType == 'r') { //若要回覆日期格式為"民國"
            $_aDate[0] = $_aDate[0] - 1911;
        } else { //若要回覆日期格式為"西元"

        }

        //決定回覆日期格式
        switch ($_dateForm) {
            case 'y': //年
                return $_aDate[0];
                break;
            case 'm': //月
                return $_aDate[1];
                break;
            case 'd': //日
                return $_aDate[2];
                break;
            case 'ym': //年月
                return $_aDate[0] . $_delimiter . $_aDate[1];
                break;
            case 'md': //月日
                return $_aDate[1] . $_delimiter . $_aDate[2];
                break;
            case 'ymd': //年月日
                return $_aDate[0] . $_delimiter . $_aDate[1] . $_delimiter . $_aDate[2];
                break;
            default:
                break;
        }
        ##
    }
    ##
    //取得案件合約資料
    $sql = '
  SELECT
    cId,
    cCertifiedId,
    cEscrowBankAccount,
    (SELECT pName FROM `tPeopleInfo` b Where a.cUndertakerId = b.pId) cUndertakerName,
    cUndertakerId,
    cSignDate,
    (SELECT sName From tStatusCase c Where a.cCaseStatus = c.sId) cCaseStatus,
    cCaseProcessing,
    cCaseStatus AS cCaseStatusId,
    cShow
  FROM
    tContractCase a
  WHERE
    cCertifiedId = ?
';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $data_case = $rs->fetch();

    ##

    //取得買方資料
    $sql = 'SELECT * FROM tContractBuyer WHERE cCertifiedId=? ;';
    $rs  = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $data_buyer = $rs->fetch();
    if (preg_match("/亞洲健康城/", $data_buyer['cName'])) {
        $hideProcessing = true;
    }
    $showdetail = 0;
    if ($buyerowner == 1 && $data_buyer['cShow'] == 1) {
        $showdetail = 1;
    }

    //取得賣方資料
    $sql = 'SELECT * FROM tContractOwner WHERE cCertifiedId=? ;';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $data_owner = $rs->fetch();
    if (preg_match("/亞洲健康城/", $data_owner['cName'])) {
        $hideProcessing = true;
    }

    if ($buyerowner == 2 && $data_owner['cShow'] == 1) {
        $showdetail = 1;
    }

    if ($buyerowner == 1) { //1買2賣
        $customer  = $data_buyer['cName'];
        $loginTime = $data_buyer['cLoginTime'];
    } else if ($buyerowner == 2) {
        $customer  = $data_owner['cName'];
        $loginTime = $data_buyer['cLoginTime'];
    }

    if ($check == 6 || $check == 7) {
        $customer = $_SESSION['customer'];
    }

    $ly = substr($loginTime, 0, 4);
    if (! preg_match("/^0000/", $ly)) {
        $ly -= 1911;
    }
    $lm = substr($loginTime, 5, 2);
    $ld = substr($loginTime, 8, 2);

    //取的案件財產資料
    $sql = 'SELECT * FROM tContractProperty WHERE cCertifiedId=? ;';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $data_property = $rs->fetchALL();

    for ($i = 0; $i < count($data_property); $i++) {

        // 修飾地址資料
        $sql = 'SELECT zCity,zArea FROM tZipArea WHERE zZip=?;';

        $rs = $pdo->prepare($sql);
        $rs->bindValue(1, $data_property[$i]['cZip'], PDO::PARAM_STR);
        $rs->execute();
        $tmp = $rs->fetch();
        // $rs = $conn->Execute($sql);
        // $tmp = $rs->fields;

        $patt                       = $tmp['zCity'];
        $data_property[$i]['cAddr'] = preg_replace("/$patt/", "", $data_property[$i]['cAddr']);
        $patt                       = $tmp['zArea'];
        $data_property[$i]['cAddr'] = preg_replace("/$patt/", "", $data_property[$i]['cAddr']);
        $data_property[$i]['cAddr'] = $tmp['zCity'] . $tmp['zArea'] . $data_property[$i]['cAddr'];
        unset($tmp);
    }

    //取得案件仲介資料
    $sql = '
  SELECT
     *,
    (SELECT bStore FROM tBranch WHERE bId=a.cBranchNum) as cBranch,
    (SELECT bStore FROM tBranch WHERE bId=a.cBranchNum1) as cBranch1,
    (SELECT bStore FROM tBranch WHERE bId=a.cBranchNum2) as cBranch2,
    (SELECT bStore FROM tBranch WHERE bId=a.cBranchNum3) as cBranch3,
    (SELECT bName FROM tBrand WHERE bId=a.cBrand1) as brandName1,
    (SELECT bName FROM tBrand WHERE bId=a.cBrand2) as brandName2,
    (SELECT bName FROM tBrand WHERE bId=a.cBrand3) as brandName3
  FROM
    tContractRealestate AS a
  WHERE
    cCertifyId=?
  ;';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $data_realstate = $rs->fetch();

    ##

    //取得金流匯入款部分
    $sql = '
  SELECT
    id,
    eAccount,
    eTradeDate,
    eTradeNum,
    CONVERT(LEFT( eLender, 13 ), SIGNED) eLender,
    eDepAccount,
    SUBSTRING(eDepAccount, -9) CertifiedId,
    ePayTitle,
    (Select sName From tStatusIncome b Where a.eStatusIncome = b.sId) StatusIncome,
    eStatusRemark,
    (Select sName From tCategoryIncome e Where e.sId = a.eStatusRemark) eStatusRemarkName,
    eRemarkContent,
    eBuyerMoney,
    eExtraMoney,
    (SELECT pName FROM tPeopleInfo c Where c.pId = a.eLastEditer) eLastEditer,
    eLastTime,
    eExplain
  FROM
    tExpense a
  WHERE
    SUBSTRING(eDepAccount, -9) = ?
    AND NOT eStatusIncome = "3"
    AND NOT eStatusIncome = "1"
    AND NOT eTradeStatus = "9"
    AND NOT eStatusIncome = "0"
  ORDER BY eTradeDate ASC;';

    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();

    while ($tmp = $rs->fetch()) {
        $list_income[] = $tmp;
        unset($tmp);
    }

    #

    $sql = '
SELECT
  bName as cName
FROM
  tBrand
WHERE
  bId=?'
    ;
    $rs = $pdo->prepare($sql);
    $rs->bindValue(1, $data_realstate['cBrand'], PDO::PARAM_STR);
    $rs->execute();
    $data_realstate_re = $rs->fetch();

    // $rs = $conn->Execute($sql);
    // $data_realstate_re = $rs->fields;

    // //確認登入者身分
    // $query = '
    //   SELECT
    //     *
    //   FROM
    //     tContractBuyer
    //   WHERE
    //     cCertifiedId=?
    //     AND cIdentifyId=? ;
    // ' ;
    // // $rs =  $conn->Execute($query);
    // // $tmp = $rs->fields;
    // $rs = $pdo->prepare($query) ;
    // $rs->bindValue(1,$cCertifiedId,PDO::PARAM_STR) ;
    // $rs->bindValue(2,$acc,PDO::PARAM_STR) ;
    // $rs->execute() ;
    // $tmp = $rs->fetch();

    // $buyerowner = '' ;

    // if ($buyerowner == 1) {//買方登入
    //   # code...
    // }elseif ($buyerowner == 2) {
    //   # code...
    // }else{
    //   header("location:http://www.first1.com.tw/");
    //   exit;
    // }

    $sql = "
SELECT
  scr.sUndertaker1 cUndertakerId,
  scr.sOffice,
  scr.sId,
  peo.pName undertaker,
  peo.pExt ext,
  peo.pGender pGender
FROM
  tContractScrivener AS csc
JOIN
  tScrivener AS scr ON scr.sId=csc.cScrivener
JOIN
  tPeopleInfo AS peo ON peo.pId=scr.sUndertaker1
WHERE
  cCertifiedId=:cid"
    ;

    $rs = $pdo->prepare($sql);
    $rs->bindValue("cid", $cCertifiedId, PDO::PARAM_STR);
    $rs->execute();
    $list = $rs->fetch();

    if ($list['sId'] == 1182) {
        $hideProcessing = true;
    }

    if ($list['pGender'] == 'M') {
        $list['undertaker'] = mb_substr($list['undertaker'], 0, 1, "UTF-8") . '先生';
    } else {
        $list['undertaker'] = mb_substr($list['undertaker'], 0, 1, "UTF-8") . '小姐';
    }

    //公司資訊
    $company = json_decode(file_get_contents(dirname(dirname(__FILE__)) . '/includes/company.json'), true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>買賣方案件查詢</title>
<link type="text/css" rel="stylesheet" href="../css/print.css" />
</head>
<script type="text/javascript">
<!--// 自動列印: 彈出印表機視窗-->
window.print();
function printPage() {

}
</script>
<body>
<div class="container">
<div class="first-logo"><a href="http://www.first1.com.tw/"><img src="../images/print/first-logo.png" alt="第一建經" border="0" /></a></div>
<section id="page-part33">
<div class="price-container">
    <div class="price-content">
        <h3 class="title2">履約價金保證系統  〉 <span>買賣方案件查詢</span> <span class="datetime"><!-- 登入時間 :<?php echo $ly?>/<?php echo $lm?>/<?php echo $ld?> --></span></h3>
            <div class="price-user">
                <div class="price-user-w">親愛的<span><?php echo $customer?></span>客戶 &nbsp;&nbsp; 您好 : 歡迎蒞臨第一建經 ESCROW 線上作業系統 !<br/>
                        如有任何問題，請洽您的服務人員 <b><?php echo $list['undertaker']?></b> &nbsp; 聯絡電話 : <?php echo $company['tel']?> *(<?php echo $list['ext']?>) &nbsp;&nbsp; 傳真號碼 : <?php echo $company['fax']?>
                </div>
            </div><!--price-user-->

            <div class="price-user-1 clearfix">
                <?php
                    if (! $hideProcessing) {
                    ?>
                <div class="price-user-2">
                    <ul class="clearfix">
                        <li>
                            <div class="user-2active">
                                <div class="seven-other">步驟<br/>
                                <span>進度圖</span></div>
                            </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo (int) $data_case['cCaseProcessing'] >= 1 ? "user-2active" : "user-2"; ?>" >
                                <div class="seven-1">簽約</div>
                            </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo (int) $data_case['cCaseProcessing'] >= 2 ? "user-2active" : "user-2"; ?>">
                            <div class="seven-1">用印</div>
                        </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo (int) $data_case['cCaseProcessing'] >= 3 ? "user-2active" : "user-2"; ?>">
                                <div class="seven-1">完稅</div>
                            </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo (int) $data_case['cCaseProcessing'] >= 4 ? "user-2active" : "user-2"; ?>">
                                <div class="seven-1">過戶</div>
                            </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo (int) $data_case['cCaseProcessing'] >= 5 ? "user-2active" : "user-2"; ?>">
                                <div class="seven-1">代償</div>
                            </div>
                        </li>
                        <li>
                            <img src="../images/print/arrow1.png"/>
                        </li>
                        <li>
                            <div class="<?php echo((int) $data_case['cCaseProcessing'] >= 6 || $data_case['cCaseStatusId'] == 3 || $data_case['cCaseStatusId'] == 4 || $data_case['cCaseStatusId'] == 9) ? "user-2active" : "user-2"; ?>">
                            <div class="seven-other">點交<br/>
                            <span>(結案)</span></div>
                         </div>
                        </li>
                    </ul>
                </div>
                <?php
                    }
                ?>
            <div class="b-form01-333 clearfix">
                <div class="div-tables">
                    <table class="rwd-tables">
                    <tbody>
                        <tr class="tr_th">
                            <th width="18%" class="th_1">承辦人</th>
                            <td width="15%" class="td_2"><?php echo $list['undertaker']?></td>
                            <th colspan="2" class="th_1">案件狀態</th>
                            <td width="15%" class="td_2"><?php echo $data_case['cCaseStatus']?></td>
                            <th class="th_1">簽約日期</th>
                            <td width="15%" class="td_3"><?php echo substr($data_case['cSignDate'], 0, 10)?></td>
                        </tr>
                        <tr class="tr_th">
                            <th class="th_1">保證號碼</th>
                            <td colspan="3" class="td_2"><?php echo $data_case['cCertifiedId']?></td>
                            <th class="th_1" nowrap>專屬帳號</th>
                            <td colspan="2" class="td_2"><?php echo $data_case['cEscrowBankAccount']?></td>
                        </tr>
                        <tr class="tr_th">
                            <th class="th_1">賣方姓名</th>
                            <td colspan="3" class="td_2">
                            <?php
                                //遮蔽個資姓名
                                if (strlen($data_owner['cIdentifyId']) == 10) {
                                    for ($i = 0; $i < mb_strlen($data_owner['cName'], 'UTF-8'); $i++) {
                                        $arrName[$i] = mb_substr($data_owner['cName'], $i, 1, 'UTF-8');
                                        if (($i > 0) && ($i < (mb_strlen($data_owner['cName'], 'UTF-8') - 1))) {
                                            $arrName[$i] = 'Ｏ';
                                        }
                                    }
                                    $data_owner['cName'] = implode('', $arrName);
                                    unset($arrName);
                                }
                                ##

                                //統計加入是否多人
                                $_sql = 'SELECT * FROM tContractOthers WHERE cCertifiedId=? AND cIdentity="2"';
                                $rs   = $pdo->prepare($_sql);
                                $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
                                $rs->execute();
                                $_oMax = $rs->rowCount();
                                if ($_oMax > 0) {
                                    $data_owner['cName'] .= '等' . ($_oMax + 1) . '人';
                                }
                                ##

                                echo $data_owner['cName'];
                            ?>
                            </td>
                                <th class="th_1">賣方ID</th>
                            <td colspan="2" class="td_2">
                                <?php
                                    if (strlen($data_owner['cIdentifyId']) == 10) {
                                        $data_owner['cIdentifyId'] = substr($data_owner['cIdentifyId'], 0, 5) . '****' . substr($data_owner['cIdentifyId'], -1);
                                    }
                                    echo $data_owner['cIdentifyId'];
                                ?>
                            </td>
                      </tr>
                      <tr class="tr_th">
                        <th class="th_1">買方姓名</th>
                        <td colspan="3" class="td_2">
                             <?php
                                 if (strlen($data_buyer['cIdentifyId']) == 10) {
                                     for ($i = 0; $i < mb_strlen($data_buyer['cName'], 'UTF-8'); $i++) {
                                         $arrName[$i] = mb_substr($data_buyer['cName'], $i, 1, 'UTF-8');
                                         if (($i > 0) && ($i < (mb_strlen($data_buyer['cName'], 'UTF-8') - 1))) {
                                             $arrName[$i] = 'Ｏ';
                                         }
                                     }
                                     $data_buyer['cName'] = implode('', $arrName);
                                     unset($arrName);
                                 }

                                 //統計加入是否多人
                                 $_sql = '
                            SELECT
                              *
                            FROM
                              tContractOthers
                            WHERE
                              cCertifiedId=?
                              AND cIdentity="1"
                          ';

                                 $rs = $pdo->prepare($_sql);
                                 $rs->bindValue(1, $cCertifiedId, PDO::PARAM_STR);
                                 $rs->execute();
                                 $_bMax = $rs->rowCount();

                                 // $rs= $conn->Execute($_sql);
                                 // $_bMax = $rs->RecordCount();

                                 if ($_bMax > 0) {
                                     $data_buyer['cName'] .= '等' . ($_bMax + 1) . '人';
                                 }
                                 ##

                                 echo $data_buyer['cName'];
                             ?>
                        </td>
                        <th class="th_1">買方ID</th>
                        <td colspan="2" class="td_2">
                           <?php
                               if (strlen($data_buyer['cIdentifyId']) == 10) {
                                   $data_buyer['cIdentifyId'] = substr($data_buyer['cIdentifyId'], 0, 5) . '****' . substr($data_buyer['cIdentifyId'], -1);
                               }
                               echo $data_buyer['cIdentifyId'];
                           ?>
                        </td>
                      </tr>
                      <?php if (! $hideProcessing): ?>
                        <tr class="tr_th">
                          <th class="th_1">仲介品牌</th>
                          <td colspan="3" class="td_2"><?php echo $data_realstate_re['cName']; ?></td>
                          <th class="th_1" nowrap>仲介店名</th>
                          <td colspan="2" class="td_2"><?php echo str_replace('(待停用)', '', $data_realstate['cBranch']); ?></td>
                        </tr>
                          <?php if ($data_realstate['cBranch1']): ?>
                              <tr class="tr_th">
                                  <th class="th_1">仲介品牌</th>
                                  <td colspan="3" class="td_2"><?php echo $data_realstate['brandName1']; ?></td>
                                  <th class="th_1">仲介店名</th>
                                  <td colspan="2" class="td_2">
                                      <?php echo str_replace('(待停用)', '', $data_realstate['cBranch1']); ?></td>
                              </tr>
                          <?php endif?>
<?php if ($data_realstate['cBranch2']): ?>
                              <tr class="tr_th">
                                  <th class="th_1">仲介品牌</th>
                                  <td colspan="3" class="td_2"><?php echo $data_realstate['brandName2']; ?></td>
                                  <th class="th_1">仲介店名</th>
                                  <td colspan="2" class="td_2">
                                      <?php echo str_replace('(待停用)', '', $data_realstate['cBranch2']); ?></td>
                              </tr>
                          <?php endif?>
<?php if ($data_realstate['cBranch3']): ?>
                              <tr class="tr_th">
                                  <th class="th_1">仲介品牌</th>
                                  <td colspan="3" class="td_2"><?php echo $data_realstate['brandName3']; ?></td>
                                  <th class="th_1">仲介店名</th>
                                  <td colspan="2" class="td_2">
                                      <?php echo str_replace('(待停用)', '', $data_realstate['cBranch3']); ?></td>
                              </tr>
                          <?php endif?>
                        <tr class="tr_td">
                          <th class="th_1" nowrap>地政士名稱</th>
                          <td colspan="6" class="td_2"><?php echo $list['sOffice']; ?></td>
                        </tr>
                      <?php endif?>
<?php
    for ($i = 0; $i < count($data_property); $i++) {

    ?>
                          <tr class="tr_td">
                            <th class="th_1">標的物座落</th>
                            <td colspan="6" class="td_2"><?php echo $data_property[$i]['cZip']; ?><?php echo $data_property[$i]['cAddr']; ?></td>
                          </tr>
                        <?php }
                        ?>
                      </tbody>
                    </table>
              </div>
              <?php if (! $hideProcessing): ?>
              <section class="e-table">
                   <?php if ($showdetail != 1):
                           include_once 'processing_normal.php'; //測試機

                           // include_once '/home/httpd/html/SSL/newWeb/login/processing_normal.php';
                       else:
                           include_once 'processing_sp.php'; //測試機
                                                             // include_once '/home/httpd/html/SSL/newWeb/login/processing_sp.php';
                   endif?>
              </section>
               <?php endif?>
            </div>
                <!--b-form01-33-->
                  <div class="other-ps clearfix">
                  <div class="other-ps-w">
                  <h3>注意事項 : </h3>
                  <p>1. 上列資料若有疑問，請儘速與第一建經服務人員聯繫，服務專線 : <?php echo $company['tel']?>，我們將竭誠為您服務。<br/>
                     2. 除簽約款外，請買方自行存匯入各期價款，切勿將款項假手他人代存。</p>
                   </div>
               </div><!--other-ps-->

           </div><!--price-user-1-->
       </div><!--price-content-->
    </div><!--price-container-->
</section>
</div>
</body>
</html>
