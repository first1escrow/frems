<?php
include_once '../configs/config.class.php';
include_once '../openadodb.php';
include_once 'class/contract.class.php';
include_once '../session_check.php' ;
include_once 'class/getAddress.php' ;

$_POST = escapeStr($_POST) ;
$contract = new Contract();
$advance = new Advance();
$id = empty($_POST["id"]) 
        ? $_GET["id"]
        : $_POST["id"];

$zip = $_GET['zip'];
$addr = $_GET['addr'];
$limit = $_GET['limit'];
$edit = $_GET['edit'];
// if (!empty($_POST['cat'])) {
	
// }

##合約書##
$data_case = $contract->GetContract($id);
$data_case['cSignDate'] = $advance->ConvertDateToRoc($data_case['cSignDate'], base::DATE_FORMAT_NUM_DATE);

$sql = "SELECT sName FROM tStatusCase WHERE sId = '".$data_case['cCaseStatus']."'";

$rs = $conn->Execute($sql);

$status = $rs->fields['sName'];
// $data_income = $contract->GetIncome($id);
##買方##
$data_buyer = $contract->GetBuyer($id);
$data_buyer['count'] = count($contract->GetOthers($id,1))+1;
// $data_buyer['cName'] = 'XXX 等10人';
if (preg_match("/等.*人/",$data_buyer['cName'])) {
	$data_buyer['count'] = 1; //他們會自己KEY等???人，所以就不顯示
}

if ($data_buyer['count'] > 1) {
	$data_buyer['cName'] .= "等".$data_buyer['count']."人";
}
##賣方##
$data_owner = $contract->GetOwner($id);
$data_owner['count'] = count($contract->GetOthers($id,2))+1;
if (preg_match("/等.*人/",$data_owner['cName'])) {
	$data_owner['count'] = 1; //他們會自己KEY等???人，所以就不顯示
}

if ($data_owner['count'] > 1) {
	$data_owner['cName'] .= "等".$data_owner['count']."人";
}
###仲介店###
$data_realstate = $contract->GetRealstate($id);

if ($data_realstate['cBranchNum'] > 0) {
	$sql='SELECT
			(SELECT bName FROM tBrand WHERE bId="'.$data_realstate['cBrand'].'") as brandName,
			b.bName,
			b.bStore,
			b.bCategory
		FROM					
			tBranch AS b
		WHERE
			bId='.$data_realstate['cBranchNum'].'
		ORDER BY
			bId
		ASC'; 
	$rs = $conn->Execute($sql) ;
	$branch = $rs->fields['brandName']."-".$rs->fields['bStore'];
}

if ($data_realstate['cBranchNum1'] > 0) {
	$sql='SELECT
			(SELECT bName FROM tBrand WHERE bId="'.$data_realstate['cBrand1'].'") as brandName,
			b.bName,
			b.bStore,
			b.bCategory
		FROM
					
			tBranch AS b
		WHERE
			bId='.$data_realstate['cBranchNum1'].'
		ORDER BY
			bId
		ASC'; 
	$rs = $conn->Execute($sql) ;
	$branch1 = $rs->fields['brandName']."-".$rs->fields['bStore'];
}

if ($data_realstate['cBranchNum2'] > 0) {
	$sql='SELECT
			(SELECT bName FROM tBrand WHERE bId="'.$data_realstate['cBrand2'].'") as brandName,
			b.bName,
			b.bStore,
			b.bCategory
		FROM
					
			tBranch AS b
		WHERE
			bId='.$data_realstate['cBranchNum2'].'
		ORDER BY
			bId
		ASC'; 
	$rs = $conn->Execute($sql) ;
	$branch2 = $rs->fields['brandName']."-".$rs->fields['bStore'];
}
#####價金

$data_income = $contract->GetIncome($id);

##建物

$sql="SELECT * FROM tContractProperty WHERE cCertifiedId='".$id."'";

$rs = $conn->Execute($sql);
$i=0;

while (!$rs->EOF) {

	//修正地址縣市區域重複
	$rs->fields['cAddr'] = filterCityAreaName($conn,$rs->fields['cZip'],$rs->fields['cAddr']) ;

	if (preg_match("/0000\-00\-00/",$rs->fields['land_movedate'])) { $rs->fields['land_movedate'] = '' ; }
	else{$rs->fields['land_movedate'] = $advance->ConvertDateToRoc($rs->fields['land_movedate'], base::DATE_FORMAT_NUM_DATE);}

	if (preg_match("/0000\-00\-00/",$v['cRentDate'])) { $rs->fields['cRentDate'] = '' ; }
	else{$rs->fields['cRentDate'] = $advance->ConvertDateToRoc($rs->fields['cRentDate'], base::DATE_FORMAT_NUM_DATE);}
	

	if (preg_match("/0000\-00\-00/",$rs->fields['cClosingDay'])) { $rs->fields['cClosingDay'] = '' ; }
	else { $rs->fields['cClosingDay'] = $advance->ConvertDateToRoc($rs->fields['cClosingDay'], base::DATE_FORMAT_NUM_DATE); }

	if (preg_match("/0000\-00\-00/",$rs->fields['cBuildDate'])) { $rs->fields['cBuildDate'] = '' ; }
	else { $rs->fields['cBuildDate'] = $advance->ConvertDateToRoc($rs->fields['cBuildDate'], base::DATE_FORMAT_NUM_DATE); }

	##建物類別
	$rs->fields['cObjUse']=explode(',', $rs->fields['cObjUse']);

	//輸入的地址排第一個
	// $tmp[] = $rs->fields;
	if ($addr == $rs->fields['cAddr']) { 
		$tmp[] = $rs->fields;
	}else{
		$tmp2[] = $rs->fields;
	}


	$i++;
	$rs->MoveNext();
}



$data_property = (is_array($tmp2))? array_merge($tmp,$tmp2) : $tmp;

$property = json_encode($data_property);

unset($tmp);
unset($tmp2);
##
?>
<!DOCTYPE html>
<html>
<head>	
	<meta charset="UTF-8">
	<title>匯入案件資料</title>
	<script src="/js/jquery-1.10.2.min.js"></script>
	<script src="/js/IDCheck.js"></script>
	<script type="text/javascript">
        $(document).ready(function() {
         	// $("#copyBtn").attr('disabled','disabled');

        });


        function go(){
            	// var cId = $('[name="certifiedid_view"]', window.parent.document).val();
            	var checkid = $('[name="certifiedid_view"]', window.parent.document).val();
            	if ("<?=$edit?>" == 0) {
            		var newId = $('[name="checkAcc"]', window.parent.document).val();
            	}else{
            		var newId = $('[name="scrivener_bankaccount2"]', window.parent.document).val();
            	}
            	
            	var reg = /.*\[]$/ ;
            	var input = $('input');
            	var arr_input = new Array();

            	

            	if (checkid == '') {
            		alert("請先至合約書輸入保證號碼，再使用此功能");
            		return false;
            	};
            	$('[name="scrivener_bankaccount"]').val(newId);
            	// $("[name='oCertifiedId']").val("");
            	// $data_case
            	$.each(input, function(key,item) {

                    if(reg.test($(item).attr("name"))){
                        if ($(item).is(':checkbox')) {
                            if ($(item).is(':checked')) {
                                 if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                    arr_input[$(item).attr("name")] = new Array();
                                }
                                
                                arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                            }

                           
                        }else{
                             if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
                                arr_input[$(item).attr("name")] = new Array();
                            }
                            
                            arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
                        }
                    }else if ($(item).is(':checkbox')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = '1';
                        }
                        else {
                            arr_input[$(item).attr("name")] = '0';
                        }
                    }else if ($(item).is(':radio')) {
                        if ($(item).is(':checked')) {
                            arr_input[$(item).attr("name")] = $(item).val();
                        }
                    }else {
                        arr_input[$(item).attr("name")] = $(item).attr("value");
                    }
                    
                });

				 var obj_input = $.extend({}, arr_input);

            	$.ajax({
            		url: 'copyCaseUpdate.php',
            		type: 'POST',
            		dataType: 'html',
            		data: obj_input,
            	})
            	.done(function(msg) {
 					// console.log(msg);
            		if (msg) {

            			$('form[name=reload] input[name=id]', window.parent.document).val(msg.trim());
            			$('form[name=reload]', window.parent.document).attr('action', 'formbuyowneredit.php');
            			$('form[name=reload]', window.parent.document).submit();
            			
            			// parent.$.fn.colorbox.close();//關閉視窗
            		}
            	});
            	
            	
        }

   //      function go2(){
   //      	var checkid = $('[name="certifiedid_view"]', window.parent.document).val();
   //          var newId = $('[name="scrivener_bankaccount2"]', window.parent.document).val();
   //          var reg = /.*\[]$/ ;
   //          var input = $('input');
   //          var arr_input = new Array();

            	
   //          $('[name="scrivener_bankaccount"]').val(newId);
   //          	// $("[name='oCertifiedId']").val("");
   //          	// $data_case
   //          $.each(input, function(key,item) {

   //              if(reg.test($(item).attr("name"))){
   //                  if ($(item).is(':checkbox')) {
   //                      if ($(item).is(':checked')) {
   //                           if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
   //                              arr_input[$(item).attr("name")] = new Array();
   //                          }
                                
   //                          arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
   //                      }

                           
   //                  }else{
   //                      if (typeof(arr_input[$(item).attr("name")]) == 'undefined') {
   //                          arr_input[$(item).attr("name")] = new Array();
   //                      }
                            
   //                      arr_input[$(item).attr("name")][arr_input[$(item).attr("name")].length] = $(item).val();
   //                  }
   //              }else if ($(item).is(':checkbox')) {
   //                      if ($(item).is(':checked')) {
   //                          arr_input[$(item).attr("name")] = '1';
   //                      }
   //                      else {
   //                          arr_input[$(item).attr("name")] = '0';
   //                      }
   //              }else if ($(item).is(':radio')) {
   //                      if ($(item).is(':checked')) {
   //                          arr_input[$(item).attr("name")] = $(item).val();
   //                      }
   //              }else {
   //                  arr_input[$(item).attr("name")] = $(item).attr("value");
   //              }
                   
   //          });

			// var obj_input = $.extend({}, arr_input);
        	
   //      	$.ajax({
   //      		url: 'copyCaseUpdate.php',
   //      		type: 'POST',
   //          	dataType: 'html',
   //          	data: obj_input,
   //      	})
   //      	.done(function(msg) {
   //      		// console.log(msg);
   //      		if (msg) {

   //          			$('form[name=reload] input[name=id]', window.parent.document).val(msg.trim());
   //          			$('form[name=reload]', window.parent.document).attr('action', 'formbuyowneredit.php');
   //          			$('form[name=reload]', window.parent.document).submit();
            			
   //          			// parent.$.fn.colorbox.close();//關閉視窗
   //          	}
   //      	});
        	
   //      }

   //      function go_fail(){ //作廢不用了
        	

        	
			// $('input:checkbox:checked[name="cat[]"]').each(function(i) { 
				
			// 	if (this.value == 2) { //合約書

			// 		//case_signdate
			// 		$('[name="case_signdate"]', window.parent.document).val('<?=$data_case['cSignDate']?>');
			// 		$('[name="income_signmoney"]', window.parent.document).val("<?=$data_income['cSignMoney']?>");
			// 		$('[name="income_affixmoney"]', window.parent.document).val("<?=$data_income['cAffixMoney']?>");
			// 		$('[name="income_dutymoney"]', window.parent.document).val("<?=$data_income['cDutyMoney']?>");
			// 		$('[name="income_estimatedmoney"]', window.parent.document).val("<?=$data_income['cEstimatedMoney']?>");
			// 		$('[name="income_firstmoney"]', window.parent.document).val("<?=$data_income['cFirstMoney']?>");

			// 		$('[name="income_certifiedmoney"]', window.parent.document).val("<?=$data_income['cCertifiedMoney']?>");
			// 		$('[name="income_totalmoney"]', window.parent.document).val("<?=$data_income['cTotalMoney']?>");
			// 		$('[name="income_parking"]', window.parent.document).val("<?=$data_income['cParking']?>");
					
			// 	}else if(this.value == 4){ //建物

			// 		var v = parseInt($("[name='buildcount']", window.parent.document).val());
			// 		// $('[name="case_signdate"]', window.parent.document).val('<?=$data_case['cSignDate']?>');
			// 		var data = JSON.parse('<?=$property?>'); 
			// 		$('.newP', window.parent.document).each(function(index, el) {
						
			// 			if (index > 0) {
			// 				$(this).remove();
			// 			};
			// 		});
				
			// 		for (var i = 0; i < data.length; i++) {
			// 				//// data[i].cAddr
			// 		   if (i==0) {
			// 				//// $("#property_Item0", window.parent.document).val(data.);//property_Item0
			// 				//
							
			// 				$.each(data[i].cObjUse, function(index, val) {
							
			// 					$('input:checkbox[name="property_objuse0[]"]', window.parent.document).filter('[value="'+val+'"]').prop('checked',true) ;
								
			// 				});
			// 		   		$('[name="property_cOther0"]', window.parent.document).val(data[i].cOther);
			// 		   		$('[name="property_buildno0"]', window.parent.document).val(data[i].cBuildNo);
			// 		   		$('[name="property_builddate0"]', window.parent.document).val(data[i].cBuildDate);
			// 		   		$('[name="property_budmaterial0"]', window.parent.document).val(data[i].cBudMaterial);
			// 		   		$('[name="property_levelnow0"]', window.parent.document).val(data[i].cLevelNow);
			// 		   		$('[name="property_levelhighter0"]', window.parent.document).val(data[i].cLevelHighter);
			// 					// 

			// 		   		$('[name="property_housetown0"]', window.parent.document).filter('[value="'+data[i].cTownHouse+'"]').prop('checked',true) ;//data[i].cTownHouse
			// 				$('[name="property_power10"]', window.parent.document).val(data[i].cPower1);		
			// 				$('[name="property_power20"]', window.parent.document).val(data[i].cPower2);
			// 				$('[name="property_measuretotal0"]', window.parent.document).val(data[i].cMeasureTotal);

			// 				$('[name="property_publicmeasuretotal0"]', window.parent.document).val(data[i].cPublicMeasureTotal);

			// 				$('[name="property_publicmeasuremain0"]', window.parent.document).val(data[i].cPublicMeasureMain);

			// 				$('[name="property_objkind0"]', window.parent.document).val(data[i].cObjKind);

			// 				$('[name="property_room0"]', window.parent.document).val(data[i].cRoom);
			// 				$('[name="property_parlor0"]', window.parent.document).val(data[i].cParlor);
			// 				$('[name="property_toilet0"]', window.parent.document).val(data[i].cToilet);
			// 				$('[name="property_buildage0"]', window.parent.document).val(data[i].cBuildAge);
			// 				$('[name="property_closingday0"]', window.parent.document).val(data[i].cClosingDay);
						
			// 		   }else{
			// 		   		var tmp = JSON.stringify(data[i]);
			// 		   		// console.log(tmp);
			// 		   		$.ajax({
		 //                        url: '../includes/escrow/getBuildTpl.php',
		 //                        type: 'POST',
		 //                        dataType: 'html',
		 //                        data: {item: v,limit:<?=$limit?>,data:tmp},
		 //                    })
		 //                    .done(function(html) {
		                    	
		 //                    	$('.newP:last', window.parent.document).after(html);
		 //                        // $().insertAfter();
		 //                    });
		                    
		 //                    $("[name='buildcount']", window.parent.document).val((v+1));
			// 			}

			// 		}

			// 	}
				
			// });
   //      }
    </script>
    <style>
    	th{
			color: rgb(255, 255, 255);
		    font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
		    font-size: 1em;
		    font-weight: bold;
		    background-color: rgb(156, 40, 33);
		    padding: 6px;
   			 border: 1px solid #CCCCCC;
		}
		td{
			color: rgb(51, 51, 51);
			font-family: 微軟正黑體, "Microsoft JhengHei", 新細明體, PMingLiU, 細明體, MingLiU, 標楷體, DFKai-sb, serif;
			font-size: 100%;
			padding: 6px;
			border: 1px solid #CCCCCC;
			/*text-align: left;*/
		}
    </style>
</head>
<body>
<center><font color="red">※請先至合約書輸入保證號碼，再使用此功能<br></font></center>
	
	<form action="formbuyowneredit.php" method="POST" target="_blank">
		<table cellspacing="0" cellpadding="0" border="0" width="70%" align="center">
		<tr>
			<td colspan="6">建物對應案件</td>
		</tr>
		<tr>
			<th width="20%">保證號碼:</th>
			<td width="30%">	
				<?=$id?>
				<input type="hidden" name="id" value="<?=$id?>">
				
			</td>
			<th width="20%">案件狀態:</th>
			<td width="30%">
				<?=$status?>
			</td>
		</tr>
		<tr>
			<th>買方姓名:</th>
			<td><?=$data_buyer['cName']?></td>
			<th>賣方姓名:</th>
			<td><?=$data_owner['cName']?></td>
		</tr>
		<tr>
			<th>仲介店:</th>
			<td colspan="3"><?=$branch?></td>
		</tr>
		<?php
		if ($branch1) {
		?>
		<tr>
			<th>仲介店:</th>
			<td colspan="3"><?=$branch1?></td>
		</tr>
		<?php
		}
		if ($branch2) {
		?>
		<tr>
			<th>仲介店:</th>
			<td colspan="3"><?=$branch2?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="4" align="center"><input type="submit" value="觀看案件"></td>
		</tr>
		</table>
	</form>
	<br>
	<form action="" method="POST" name="form">
		

		<table cellspacing="0" cellpadding="0" border="0" width="70%" align="center">
			
			<!-- <tr>
				<th width="20%">保證號碼：</th>
				<td>
					<input type="text" name="certifyId" maxlength="9" onkeyup="checkCertifiedId()">
					<input type="hidden" name="scrivener_bankaccount">
					<br>
					<span id="showcheckId"></span>
				</td>
			</tr> -->
			<tr>
				<th width="20%">匯入頁籤：</th>
				<td>
					<input type="hidden" name="scrivener_bankaccount" >
					<input type="hidden" name="oCertifiedId" value="<?=$id?>">
					<input type="hidden" name="zip" value="<?=$zip?>">
					<input type="hidden" name="addr" value="<?=$addr?>">
					<input type="hidden" name="edit" value="<?=$edit?>">

					<!-- <input type="checkbox" name="cat[]" id="" value="1">案件明細表(只複製客服內容) -->
					<input type="checkbox" name="cat[]" id="" value="2">合約書
					<input type="checkbox" name="cat[]" id="" value="3">土地
					<input type="checkbox" name="cat[]" id="" value="4" checked>建物
					<!-- <input type="checkbox" name="cat[]" id="" value="5">地政士(保證號碼會自動對應地政士) -->
					<input type="checkbox" name="cat[]" id="" value="6">仲介
					<input type="checkbox" name="cat[]" id="" value="7">買方
					<input type="checkbox" name="cat[]" id="" value="8">賣方
					
					<?php
					if ($edit == 1) { ?>
					<input type="button" value="匯入" id="copyBtn" onclick="go()">
					<?php }else{ ?>
					<input type="button" value="匯入" id="copyBtn" onclick="go()">
					<?php }
					?>
					
				</td>
				
			</tr>
			
		</table>
		</form>
		
	<!--  -->
</body>
</html>