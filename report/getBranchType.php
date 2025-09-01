<?php
include_once dirname(dirname(__FILE__)).'/openadodb.php' ;


function branch_type($conn,$arr)
{
	 // type - (O:其他 T台灣 U:優美 F:永春 非仲介:3 直營:2 加盟:1 其他未指定 :N) 
	
		$cat1 = checkCat($conn,$arr['branch'],$arr['brand']) ; //已案件的brand為主
		$cat2 = checkCat($conn,$arr['branch1'],$arr['brand1']) ;
		$cat3 = checkCat($conn,$arr['branch2'],$arr['brand2']) ;


		if (($cat1 == '加盟其他品牌') || ($cat2 == '加盟其他品牌') || ($cat3 == '加盟其他品牌')) {
			//$cat = '加盟其他品牌' ;
			$type = 'O';
			// $data['type'] = 'O'; //店類別

			// //判斷該類別所屬的店
			// if ($cat1 == '加盟其他品牌') { 
			// 	$data['branch'] = $arr['branch'];
			// 	$data['brand'] = $arr['brand'];
			// }elseif ($cat2 == '加盟其他品牌') {
			// 	$data['branch'] = $arr['branch1'];
			// 	$data['brand'] = $arr['brand1'];
			// }else{
			// 	$data['branch'] = $arr['branch2'];
			// 	$data['brand'] = $arr['brand2'];
			// }
			##
			
		}
		else if (($cat1 == '加盟台灣房屋') || ($cat2 == '加盟台灣房屋') || ($cat3 == '加盟台灣房屋')) {
			
			if (($cat1 != '加盟其他品牌') && ($cat2 != '加盟其他品牌') && ($cat3 != '加盟其他品牌')) {
				$type = 'T';
				/*$data['type'] = 'T'; //店類別

				//判斷該類別所屬的店
				if ($cat1 == '加盟台灣房屋') { 
					$data['branch'] = $arr['branch'];
					$data['brand'] = $arr['brand'];

				}elseif ($cat2 == '加盟台灣房屋') {
					$data['branch'] = $arr['branch1'];
					$data['brand'] = $arr['brand1'];
				}else{
					$data['branch'] = $arr['branch2'];
					$data['brand'] = $arr['brand2'];
				}
				##*/
			}
		}
		else if (($cat1 == '加盟優美地產') || ($cat2 == '加盟優美地產') || ($cat3 == '加盟優美地產')) {
			//$cat = '加盟優美地產' ;
			if (($cat1 != '加盟其他品牌') && ($cat2 != '加盟其他品牌') && ($cat3 != '加盟其他品牌')
				&& ($cat1 != '加盟台灣房屋') && ($cat2 != '加盟台灣房屋') && ($cat3 != '加盟台灣房屋')) {
					$type = 'U';
					// $data['type'] = 'U'; //店類別

					// //判斷該類別所屬的店
					// if ($cat1 == '加盟台灣房屋') { 
					// 	$data['branch'] = $arr['branch'];
					// 	$data['brand'] = $arr['brand'];

					// }elseif ($cat2 == '加盟台灣房屋') {
					// 	$data['branch'] = $arr['branch1'];
					// 	$data['brand'] = $arr['brand1'];
					// }else{
					// 	$data['branch'] = $arr['branch2'];
					// 	$data['brand'] = $arr['brand2'];
					// }
					// ##
			}
		}
		else if (($cat1 == '加盟永春不動產') || ($cat2 == '加盟永春不動產') || ($cat3 == '加盟永春不動產')) {
			//$cat = '加盟永春不動產' ;
			if (($cat1 != '加盟其他品牌') && ($cat2 != '加盟其他品牌') && ($cat3 != '加盟其他品牌')
				&& ($cat1 != '加盟台灣房屋') && ($cat2 != '加盟台灣房屋') && ($cat3 != '加盟台灣房屋')
				&& ($cat1 != '加盟優美地產') && ($cat2 != '加盟優美地產') && ($cat3 != '加盟優美地產')) {
					$type = 'F' ;
			}
		}
		else if ((preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3))) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			$type = '1';
			// $data['type'] = '1'; //店類別

			// //判斷該類別所屬的店
			// if ($cat1 == '加盟') { 
			// 	$data['branch'] = $arr['branch'];
			// 	$data['brand'] = $arr['brand'];
			// }elseif ($cat2 == '加盟') {
			// 	$data['branch'] = $arr['branch1'];
			// 	$data['brand'] = $arr['brand1'];
			// }else{
			// 	$data['branch'] = $arr['branch2'];
			// 	$data['brand'] = $arr['brand2'];
			// }
					##
		}
		else if (($cat1 == '直營') || ($cat2 == '直營') || ($cat3 == '直營')) {
			//$cat = '直營' ;
			//$list[$j++] = $arr ;
			if (!(preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3))) {
				$type = '2';

				// $data['type'] = '2'; //店類別

				// //判斷該類別所屬的店
				// if ($cat1 == '加盟') { 
				// 	$data['branch'] = $arr['branch'];
				// 	$data['brand'] = $arr['brand'];
				// }elseif ($cat2 == '加盟') {
				// 	$data['branch'] = $arr['branch1'];
				// 	$data['brand'] = $arr['brand1'];
				// }else{
				// 	$data['branch'] = $arr['branch2'];
				// 	$data['brand'] = $arr['brand2'];
				// }
			}
		}
		else if (($cat1 == '非仲介成交') || ($cat2 == '非仲介成交') || ($cat3 == '非仲介成交')) {
			//$cat = '非仲介成交' ;
				$type = '3';
				// $data['type'] = '3'; //店類別
				// 	//判斷該類別所屬的店
				// if ($cat1 == '非仲介成交') { 
				// 	$data['branch'] = $arr['branch'];
				// 	$data['brand'] = $arr['brand'];
				// }elseif ($cat2 == '非仲介成交') {
				// 	$data['branch'] = $arr['branch1'];
				// 	$data['brand'] = $arr['brand1'];
				// }else{
				// 	$data['branch'] = $arr['branch2'];
				// 	$data['brand'] = $arr['brand2'];
				// }
			
		}
		else  {
			if (!(preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3) || preg_match("/直營/",$cat1) ||
				 preg_match("/直營/",$cat2) || preg_match("/直營/",$cat3) || preg_match("/非仲介成交/",$cat1) || preg_match("/非仲介成交/",$cat2)
				|| preg_match("/非仲介成交/",$cat3))) 
			{
				$type = 'N';
				// $data['type'] = 'N'; //店類別
				// 	//判斷該類別所屬的店
				// if ($cat1 == '非仲介成交') { 
				// 	$data['branch'] = $arr['branch'];
				// 	$data['brand'] = $arr['brand'];
				// }elseif ($cat2 == '非仲介成交') {
				// 	$data['branch'] = $arr['branch1'];
				// 	$data['brand'] = $arr['brand1'];
				// }else{
				// 	$data['branch'] = $arr['branch2'];
				// 	$data['brand'] = $arr['brand2'];
				// }
			}
		}
	
	return $type;
}

function branch_type2($conn,$arr)
{
	 // type - (O:其他 T台灣 U:優美 非仲介:3 直營:2 加盟:1 其他未指定 :N) 
	
		$cat1 = checkCat($conn,$arr['branch'],$arr['brand']) ; //已案件的brand為主
		$cat2 = checkCat($conn,$arr['branch1'],$arr['brand1']) ;
		$cat3 = checkCat($conn,$arr['branch2'],$arr['brand2']) ;


		if (($cat1 == '加盟其他品牌') || ($cat2 == '加盟其他品牌') || ($cat3 == '加盟其他品牌')) {
			//$cat = '加盟其他品牌' ;
			$type['type'] = '11';

			if ($cat1 == '加盟其他品牌') {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}elseif ($cat2 == '加盟其他品牌') {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif ($cat3 == '加盟其他品牌') {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
			
			
		}
		else if (($cat1 == '加盟台灣房屋') || ($cat2 == '加盟台灣房屋') || ($cat3 == '加盟台灣房屋')) {
			
			if ($cat1 == '加盟台灣房屋') {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];

			}elseif ($cat2 == '加盟台灣房屋') {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];

			}elseif ($cat3 == '加盟台灣房屋') {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];

			}
				$type['type']  = '12';
				
			
		}
		else if (($cat1 == '加盟優美地產') || ($cat2 == '加盟優美地產') || ($cat3 == '加盟優美地產')) {
			if ($cat1 == '加盟優美地產') {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}elseif ($cat2 == '加盟優美地產') {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif ($cat3 == '加盟優美地產') {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
				$type['type']  = '13';
		}
		else if (($cat1 == '加盟永春不動產') || ($cat2 == '加盟永春不動產') || ($cat3 == '加盟永春不動產')) {
			if ($cat1 == '加盟永春不動產') {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}elseif ($cat2 == '加盟永春不動產') {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif ($cat3 == '加盟永春不動產') {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
				$type['type']  = '14';
		}
		else if ((preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3))) {
			//$cat = '所有加盟(其他品牌、台灣房屋、優美地產)' ;
			if (preg_match("/^加盟/",$cat1)) {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}elseif (preg_match("/^加盟/",$cat2)) {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif (preg_match("/^加盟/",$cat3)) {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
				$type['type']  = '1';
			
					##
		}
		else if (($cat1 == '直營') || ($cat2 == '直營') || ($cat3 == '直營')) {
			//$cat = '直營' ;
			//$list[$j++] = $arr ;
			if (preg_match("/^直營/",$cat1)) {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}elseif (preg_match("/^直營/",$cat2)) {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif (preg_match("/^直營/",$cat3)) {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
				$type['type']  = '2';
		}
		else if (($cat1 == '非仲介成交') || ($cat2 == '非仲介成交') || ($cat3 == '非仲介成交')) {
			if ($cat1 == '非仲介成交') {
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
			}
			elseif ($cat2 == '非仲介成交') {
				$type['bid'] = $arr['branch1'];
				$type['brand'] = $arr['brand1'];
				$type['manager'] = $arr['bManager1'];
				$type['group'] = $arr['bGroup1'];
			}elseif ($cat3 == '非仲介成交') {
				$type['bid'] = $arr['branch2'];
				$type['brand'] = $arr['brand2'];
				$type['manager'] = $arr['bManager2'];
				$type['group'] = $arr['bGroup2'];
			}
				$type['type']  = '3';
				
			
		}
		else  {
			if (!(preg_match("/^加盟/",$cat1) || preg_match("/^加盟/",$cat2) || preg_match("/^加盟/",$cat3) || preg_match("/直營/",$cat1) ||
				 preg_match("/直營/",$cat2) || preg_match("/直營/",$cat3) || preg_match("/非仲介成交/",$cat1) || preg_match("/非仲介成交/",$cat2)
				|| preg_match("/非仲介成交/",$cat3))) 
			{
				$type['bid'] = $arr['branch'];
				$type['brand'] = $arr['brand'];
				$type['manager'] = $arr['bManager'];
				$type['group'] = $arr['bGroup'];
				$type['type']  = '4';
				
			}
		}
	
	return $type;
}

//檢核仲介類型
function checkCat($conn,$no,$brand) {
	$val = '' ;
	
	if ($no) {
		$sql = 'SELECT
					(SELECT bId FROM tBrand AS br WHERE br.bId = '.$brand.') AS bBrand,
					bCategory
					
				FROM
					tBranch
				WHERE
					bId="'.$no.'" AND bId<>"0";' ;
		
		$tmp = $conn->Execute($sql);
		
		if ($tmp->fields['bCategory'] == '1') {
			$val = '加盟' ;
			if ($tmp->fields['bBrand'] == '1') {
				$val .= '台灣房屋' ;
			}
			else if ($tmp->fields['bBrand'] == '2') {
				$val .= '非仲介成交' ;
			}
			else if ($tmp->fields['bBrand'] == '49') {
				$val .= '優美地產' ;
			}
			else if ($tmp->fields['bBrand'] == '56') {
				$val .= '永春不動產' ;
			}
			else {
				$val .= '其他品牌' ;
			}
		}
		else if ($tmp->fields['bCategory'] == '2') {
			$val = '直營' ;
		}
		else if ($tmp->fields['bCategory'] == '3') {
			$val = '非仲介成交' ;
		}
	}
	// echo $val."-----";
	return $val ;
}
?>