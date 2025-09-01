<?php
require_once dirname(__DIR__) . '/web_addr.php' ;
require_once dirname(__DIR__) . '/openadodb.php' ;
require_once dirname(__DIR__) . '/session_check.php' ;
require_once dirname(__DIR__) . '/tracelog.php' ;

$tlog = new TraceLog() ;
$tlog->selectWrite($_SESSION['member_id'], json_encode($_REQUEST), '查看出款未審核案件明細') ;

$ch = $_GET["ch"];	 // ch=1 不能修改
$ok = $_GET["ok"];
$arrangeDate = $_GET['dt'] ;
$cat = $_GET['cat'];
$bank = $_GET['bk'];

$vr_code = $_REQUEST["vr_code"];
$save = $_POST["save"];
if ($save == 'ok') {
	$bid = $_REQUEST["bid"];
	$check = $_REQUEST["check"];
	$_total = count($check);
	for ($i=0;$i<$_total;$i++) {
		$_value = $check[$i];
		$_id = $bid[$i];
		$_time = date("Y-m-d H:i:s");
		$sql = "update tBankTrans set tOk='$_value' , tOk_date='$_time' where tId='$_id'";
		// echo $sql.";<br>";
		$conn->Execute($sql);
	}
}
function tDate_check($_date,$_dateForm='ymd',$_dateType='r',$_delimiter='',$_minus=0,$_sat=0) {     
  $_aDate[0] = substr($_date,0,4) ;
  $_aDate[1] = substr($_date,4,2) ;
  $_aDate[2] = substr($_date,6) ;

 

  //$_cheque_date = implode('-',$_tDate) ;
  
  //是否遇六日要延後(六延兩天、日延一天)
  if ($_sat == '1') {
    $_ss = 0 ;
    $_ss = date("w",mktime(0,0,0,$_aDate[1],($_aDate[2]+$_minus),$_aDate[0])) ;
    if ($_ss == '0') {      //如果是星期日的話，則延後一天
      // if ($_minus < 0) {
      //  $_minus =  $_minus + $_minus + $_minus ;
      // }
      // else {
      //  $_minus = $_minus + $_minus ;
      // }  
      $weekend = 1;     
    }
    else if ($_ss == '6') {   //如果是星期六的話，則延後兩天
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="/libs/jquery/css/custom-theme/jquery-ui-1.8.18.custom.css">
<script src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
<script src="/libs/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<title>出帳檔審核匯出作業</title>
<style type="text/css">
.font12 {	font-size:12px;
}
</style>
<script>
$(document).ready(function() {
	$("#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }) ;
}) ;

function export_file(x){
  var _number = Math.random();
  var url = "_export_file.php?i=" + _number + "&id=" + x;
  //alert(url);
  $.ajax({
    url: url,
    error: function (xhr) {
      //alert(xhr);
      alert("error!!");
    },
    success: function (response) {
      
    }
  });
}
function open_w2(x){	
	var dd = $('#datepicker').val() ;
	url = '<?=$web_addr?>/bank/_export_file2.php?id=' + x + '&dt=' + dd ;
	window.open (url , 'newwindow2', 'height=200, width=300, top=150, left=150, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
function open_w(x){	
	var dd = $('#datepicker').val() ;
	url = '<?=$web_addr?>/bank/_export_file.php?id=' + x + '&dt=' + dd+'&cat=<?=$cat?>' ;
	window.open (url , 'newwindow2', 'height=200, width=300, top=150, left=150, toolbar=no, menubar=no, scrollbars=no, resizable=no,location=no, status=no')	
}
function pass(){
	if (confirm('是否全部審核通過?')) {
		$('[name="check[]"]').each(function() {
			$(this).val(1);
		});
	}
	
}

function book(id,url)
{
  $('[name="book_pdf"]').attr('action','instructions/'+url);

  $('[name="id"]').val(id);

  $('[name="book_pdf"]').submit();
}
</script>
</head>

<body>
<form action="" name="book_pdf" method="POST" target="_blank">
  <input type="hidden" name="id" />
</form>
<form id="form1" name="form1" method="post" action="">
  <table width="1016" border="0" cellpadding="1" cellspacing="1" class="font12" id="ttt">
  <?php
  	if ($ch == '' && $save != 'ok') {
  ?>
  <tr><td colspan="2"><input type="button" value="全部審核通過" onclick="pass()" /></td></tr>
  <?php } ?>
    <tr>
      <td colspan="2">專屬帳號 <strong><?php echo $vr_code;?></strong>
        <label for="vr_code2">
          <input name="vr_code" type="hidden" id="vr_code" value="<?php echo $vr_code;?>" />
          <input name="save" type="hidden" id="save" value="ok" />
        </label></td>
      <td width="172">&nbsp;</td>
      <td width="167">&nbsp;</td>
      <td width="182">&nbsp;</td>
      <td width="132">&nbsp;</td>
      <td width="12">&nbsp;</td>
    </tr>
    <?php
	if ($ok == "1") { $_cond = " and tOk='1' and tExport=2";} 

  if ($cat == 'all') {
    // $_cond .= ' AND tCode2 NOT IN("虛轉虛","大額繳稅","大額繳稅","臨櫃開票","臨櫃領現") AND tKind != "利息"';
    $_cond .= ' AND
      IF( tBank_kind = "台新",tCode2 NOT IN("一銀內轉","大額繳稅","臨櫃開票","臨櫃領現","聯行代清償") AND (tObjKind2 != "01" AND tObjKind2 != "02" AND tObjKind2 != "04" AND tObjKind2 != "05"),tCode2 NOT IN("一銀內轉","大額繳稅","臨櫃開票","臨櫃領現") AND tKind != "利息" ) ';
  }else{
    // $_cond .= ' AND tId = "'.$cat.'"';

    $sql = 'SELECT * FROM tContractBank WHERE cShow="1" AND cId="'.$bank.'" ORDER BY cId ASC;' ;
    $rs = $conn->Execute($sql) ;
    $conBank = $rs->fields ;
    $_bank = $conBank['cBankName'] ;
    unset($rs) ;

    $sql = "select tCode2,tObjKind2 from tBankTrans where tVR_Code LIKE '".$conBank['cBankVR']."%' AND tOk=1 AND tBank_kind='".$_bank."' AND tExport=2 AND tId = '".$cat."'";
    // echo $sql;
    $rs = $conn->Execute($sql); 

    if ($rs->fields['tObjKind2'] == '01' || $rs->fields['tObjKind2'] == '02' || $rs->fields['tObjKind2'] == '04' || $rs->fields['tObjKind2'] == '05') {
      $_cond .= ' AND tObjKind2 = "'.$rs->fields['tObjKind2'].'" ';
    }else{
      $_cond .= ' AND tCode2 = "'.$rs->fields['tCode2'].'" ';
    }
    
  }

  $sql = "select * from tBankTrans where tVR_Code='$vr_code' and tPayOk='2' $_cond";
  // echo $sql;
  $rs = $conn->Execute($sql);	
  $_error =0;
  while( !$rs->EOF ) {
	  $_target = $rs->fields["tBank_kind"];
	  //echo $_target;
	  //------
	  if ($rs->fields["tOk"] != '1' ) { $_error++;}
	  //------
	  switch ($rs->fields["tCode"]){
		  case "01":
        $_title = "聯行轉帳";
        if ($rs->fields["tCode2"] == '一銀內轉') {
          $_title = "一銀內轉";
        }
		  	
		  break;
		  case "02":
		  	$_title = "跨行代清償";
		  break;
		  case "03":
		  	$_title = "聯行代清償";
		  break;
		  case "04":
		  	$_title = "大額繳稅";
		  break;
		  case "05":
		  	$_title = "臨櫃開票";
        if ($rs->fields['tCode2'] == '臨櫃領現') {
          $_title = '臨櫃領現';
        }
		  break;
		  case "06":
		  	$_title = "利息";
		  break;
	  }
	  $bank3 = substr($rs->fields["tBankCode"],0,3);
	  $bank4 = substr($rs->fields["tBankCode"],3,4);
	  
	  $sql = "select * from tBank where bBank3='$bank3' and bBank4='' limit 1";
	  $rs1 = $conn->Execute($sql);	
	  $_bank_title = $rs1->fields["bBank4_name"];
	  //
	  $sql = "select * from tBank where bBank3='$bank3' and bBank4='$bank4'  limit 1";
	 // echo $sql;
	  $rs2 = $conn->Execute($sql);	
	  $_bank_cotitle = $rs2->fields["bBank4_name"];
	  
  ?>
    <tr id="tr_pos">
      <td width="143"><label for="target[]"></label>
        類別  <?php echo $rs->fields["tKind"];?> <br />
        交易類別 <?php echo $_title;?></td>
        
      <td width="186">解匯行 <?php echo str_replace("　", "", $_bank_title);?> <br />
        分行別 <?php echo str_replace("　", "", $_bank_cotitle);?></td>
      <td>戶名 <?php echo $rs->fields["tAccountName"];?> <br />
        證號 <?php echo $rs->fields["tAccountId"];?></td>
      <td>帳號 <?php echo $rs->fields["tAccount"];?></td>
      <td>金額 NT$ <?php echo $rs->fields["tMoney"];?>元(不含匯費)</td>
      <td><label for="check[]"></label>
        <select name="check[]" id="check[]" <?php if ($ch==1) { echo 'disabled="disabled"';} ?>>
          <option value="">請審核</option>
          <option value="1" <?php if ($rs->fields["tOk"] == '1' ) { echo 'selected="selected"';}?>  >審核通過</option>
          <option value="2" <?php if ($rs->fields["tOk"] == '2' ) { echo 'selected="selected"';}?> >審核不通過</option>
        </select>
        <input name="bid[]" type="hidden" id="bid[]" value="<?php echo $rs->fields["tId"];?>" /></td>
      <td >
        <?php
          if ($_title != '聯行轉帳' && $_title != '跨行代清償' && $_title != '聯行代清償' && $_title !='利息')  {
              $sql = 'SELECT bId,bCategory,bBank FROM tBankTrankBook WHERE bBankTranId = "'.$rs->fields['tId'].'"';
              // echo $sql;
              $rsb = $conn->Execute($sql);

              $data = $rsb->fields;

              if ($data['bBank'] == 4 || $data['bBank'] == 6) {
  
                    if ($data['bCategory'] == 2) {
                       $pdf = 'sinopac02_pdf.php';
                    }elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
                      $pdf = 'sinopac03_pdf.php';
                     
                    }else if ($data['bCategory'] == 6) {//6補通訊7退票領回8代收票據領回
                      $$pdf = 'sinopac05_pdf.php';
                     
                    }elseif ($data['bCategory'] == 7 || $data['bCategory'] == 8) {
                      $pdf = 'sinopac04_pdf.php';
                      
                    }
                    
              }elseif ($data['bBank'] == 1) {
                    if ($data['bCategory'] == 6) {
                      $pdf = 'firstInform3.php';
                    }elseif ($data['bCategory'] == 3 || $data['bCategory'] == 4 || $data['bCategory'] == 5) {
                      $pdf = 'firstInform1.php';
                    }
              }


              ?>
                <input type="button" value="指示書" onclick="book(<?php echo $data['bId'];?>,'<?php echo $pdf;?>')" />
              
            
        <?php  }
        ?>
        
      </td>
    </tr>
    <tr>
      <td>附言(備註)</td>
      <td colspan="2"><?php echo $rs->fields["tTxt"];?></td>
      <td>(<?php 
      		if ($rs->fields["tObjKind"] == '解除契約') {
      			echo '解約/終止履保';
      		}else{
      			echo $rs->fields["tObjKind"];
      		}
      			
      			?>)</td>
      			
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td >&nbsp;</td>
    </tr>
    <tr>
      <td height="19" colspan="7"><hr /></td>
    </tr>
    <?php
	 $rs->MoveNext();
  } 
  //echo $_target;
  if ($_target == '遠東') {
	  $_js = "open_w2('".$vr_code."');";
  } else {
	  $_js = "open_w('".$vr_code."');";
  }
  ?>
  </table>
<?php
	if ($ch != 1) {
		echo '
			<input type="submit" name="button" id="button" value="確認" />
		' ;
	}
	
	if ($_error == 0) {
		if ($ch != 1) {

      //五點後的打包 日期可以預設成次一個工作日(BY佩琦20170912)
      $tmpD = date("Y-m-d H:i:s");
      $checkTime = date("Y-m-d")." 17:00:00";
      // echo $tmpD."_".$checkTime;
      if ($tmpD >= $checkTime) {
        $eDay = date('Y-m-d',strtotime("+1 day"));
        $eDay = tDate_check(date("Ymd"),'ymd','b','-',1,1) ;
      }else{
        $eDay = date('Y-m-d');
      }

			echo '
				<input type="button" name="button2" id="button2" value="專屬帳號匯出檔" onclick="'.$_js.'" />
				&nbsp;銀行放款日：
				<input type="text" id="datepicker" name="dt" style="width:100px;" value="'.$eDay.'" readonly />
        ※(過了當天17點自動為次一個工作日)
			' ;
	}
}
?>
</form>
<p>
** 每筆資料需全部審核通過後才能匯出媒體檔<br />
</p>
</body>
</html>
