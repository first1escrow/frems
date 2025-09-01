<?php
include_once('../../openadodb.php') ;
include_once('../../web_addr.php') ;
include_once '../../session_check.php' ;

$send = $_REQUEST["send"];
$_now = $_REQUEST["now"];

if ($send == 'ok'){
	$toMail = $_POST["toMail"];
	$Name = "台灣房屋"; //senders name
	$email = "michael.liu@twhg.com.tw"; //senders e-mail adress
	$recipient = $toMail ; //recipient
	$mail_body = "信託履保業務對帳紀錄報表"; //mail body
	$subject = "信託履保業務對帳紀錄報表"; //subject
	$header = "From: ". $Name . " <" . $email . ">\r\n"; //optional headerfields
	
	mail($recipient, $subject, $mail_body, $header); //mail command :)
}

//合約銀行基本資料
$sql = 'SELECT cBankFullName FROM tContractBank WHERE cShow="1" GROUP BY cBankFullName ORDER BY cId ASC;' ;
$rs = $conn->Execute($sql) ;
while(!$rs->EOF) {
	$conBank[] = $rs->fields['cBankFullName'] ;
	$rs->MoveNext() ;
}
unset($rs) ;
$bank_list = implode('、',$conBank) ;
##

if ($_now == "") { 
	//$_now = date("Y-m-d"); // 當天日期
	$_con = "";
} else {
	$_con = "and C.cApplyDate<='$_now'";
}

$sql = "SELECT  C.cApplyDate,A.cCertifiedId,A.cName as owner ,A.cBaseAddr as o_address , A.cIdentifyId as o_id , A.cMobileNum as o_mobile ,B.cName as buyer,B.cBaseAddr as b_address,B.cIdentifyId as b_id , B.cMobileNum as b_mobile, cCaseMoney, C.cEscrowBankAccount as vr_code FROM tContractOwner as A ,tContractBuyer as B , tContractCase as C where A.cCertifiedId = B.cCertifiedId and A.cCertifiedId=C.cCertifiedId $_con and cCaseMoney > 0";
//echo $sql;
$rs=$conn->Execute($sql);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="layout-2.css" rel="stylesheet" type="text/css" />
<style type="text/css">
body,td,th {
	font-family: "微軟正黑體";
}
#tt {
	border-top-style:solid; border-color:#000000; border-width: 1px;
}
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<body>
<table width="800" border="0" align="center" cellpadding="1" cellspacing="1">
  <tr>
    <td align="center" id="header h2"><h3><strong>第一建築經理股份有限公司</strong></h3></td>
  </tr>
  <tr>
    <td align="center" id="header h1"><strong>信託履保業務對帳紀錄報表</strong></td>
  </tr>
  <tr>
    <td align="right"><input type="button" name="button" id="button" value="產生對帳單" onclick="window.open ('/bank/report/save_excel.php', 'newwindow', 'height=300, width=400, top=100, left=100, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')" /></td>
  </tr>
  <tr>
    <td align="right"><fieldset>                
        <table width="100%" border="0" cellpadding="1" cellspacing="1">
          <tr>
            <td width="10%" align="left">銀行別：</td>
            <td width="60%" align="left"><?=$bank_list?></td>
            <td width="11%" align="left"><?php if ($_now <>"") { ?>查詢日期:<?php } ?></td>
            <td width="19%" align="left"><?php echo $_now;?></td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" id ="tt">
          <tr>
            <td bgcolor="#000000"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" id="table2">
              <tr>
                <td align="center" bgcolor="#F4E1DD"><strong>序 號</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>保證號碼</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>買方姓名</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>身分證號</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>手機號</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>賣方姓名</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>身分證號</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>手機號</strong></td>
                <td align="center" bgcolor="#F4E1DD"><strong>餘 額</strong></td>
              </tr>
              <?php 
			  $j=1;
			  while( !$rs->EOF ) {
				  //$_vr_code = "60001".$rs->fields["cCertifiedId"];
				  $_vr_code = $rs->fields['vr_code'] ;
			  ?>
              <tr >
                <td width="41" align="center" bgcolor="#FFFFFF" ><?php echo $j;?></td>
                <td width="57" align="center" bgcolor="#FFFFFF"><?php echo $_vr_code;?></td>
                <td width="69" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["owner"];?></td>
                <td width="111" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["o_id"];?></td>
                 <td width="118" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["o_mobile"];?></td>
                <td width="69" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["buyer"];?></td>
                <td width="111" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["b_id"];?></td>
                 <td width="108" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["b_mobile"];?></td>
                <td width="82" align="center" bgcolor="#FFFFFF"><?php echo $rs->fields["cCaseMoney"];?></td>
              </tr>
              
               <?php
			   	 $_total = $_total + $rs->fields["cCaseMoney"];
				 $rs->MoveNext();
				 $j++;
				} 
				?>
                <!-- 利息收入 -->
                <?php
				//利息收入-所得稅支出 start
				$sqlx = "select * from tExpense where eTradeCode in ('1912','1920')";
				$rsx = $conn->Execute($sqlx);
				while( !$rsx->EOF ) {
					$_eLender = (int)substr($rsx->fields["eLender"],0,-2);
					$_eDebit = (int)substr($rsx->fields["eDebit"],0,-2);
					$_t_money = $_t_money + $_eLender - $_eDebit;
					$rsx->MoveNext(); 
				}
				$_total = $_total +$_t_money;
				?>
                <tr >
                <td colspan="8" align="center" bgcolor="#FFFFFF" ><div style="text-align:right;">利息收入</div></td>
                <td width="82" align="center" bgcolor="#FFFFFF"><?php echo $_t_money;?></td>
              	</tr>
                <tr>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF">&nbsp;</td>
                <td align="center" bgcolor="#FFFFFF"><?php echo $_total;?></td>
              </tr>
            </table></td>
          </tr>
        </table>
    </fieldset></td>
  </tr>
</table>
</body>
</html>
