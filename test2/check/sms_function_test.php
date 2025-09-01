<?php
require_once dirname(__DIR__).'/.env.php';

/*
	pid 保證號
	sid 地政士id
	bid 店id
	target 類別: income / trans(出帳) -> 賣方先動撥/仲介服務費/扣繳稅款/代清償/點交
	$tid 出帳id / 入帳id
*/

class SMS_Gateway extends PDO{
  private static $dDSN;
  private static $dUser;
  private static $dPassword;
  public $DB_link;
  public $execSQL;

  public function __construct() {
	global $env;

    $this->DB_link      = '';
    $this->execSQL      = '';
	$this->dbtype_sql   = $env['db']['197']['driver'];
    $this->host_sql     = $env['db']['197']['host'];
	$this->dbname_sql   = $env['db']['197']['database'];
	$this->username_sql = $env['db']['197']['username'];
	$this->password_sql = $env['db']['197']['password'];
	$this->log_path     = 'log/' ;

    $this->fet_SysId      = $env['sms']['fet']['fet_SysId'];
    $this->fet_SrcAddress = $env['sms']['fet']['fet_SrcAddress'];
    $this->acc_china      = $env['sms']['cht']['acc_china'];
    $this->pwd_china      = $env['sms']['cht']['pwd_china'];
    $this->uid            = $env['sms']['apol']['uid'];
    $this->upass          = $env['sms']['apol']['upass'];

	try {
      // utf-8
      //$this->DB_link = new PDO($this->dDSN,$this->dUser,$this->dPassword,array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'utf8\''));
      //$this->DB_link->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
	  
	  $this->DB_link = new PDO($this->dbtype_sql . ':host=' . $this->host_sql . ';dbname=' . $this->dbname_sql, $this->username_sql, $this->password_sql);
	  // 資料庫使用 UTF8 編碼
      $this->DB_link->query('SET NAMES UTF8');
    } catch (PDOException $e) {
      //echo "<p>DBconnectFalse : ".$this->dDSN."</p><p>DB Error: ".$e->getMessage()."</p>";
	  echo "DBconnectFalse: ".$e->getMessage();
      return "DBconnectFalse: ".$e->getMessage();
    }
  }
  
	//手動發送簡訊模組(那裡有用到?20151125)
	public function manual_sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) {
		$sys = $this->getSmsSystem() ;
		
		//$sms_id = $this->send_cht_sms($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;
		$sms_id = $this->sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid,$sys) ;
		
		$results = '' ;
		if (($sms_id != 's')&&($sms_id != 'p')) {		//發送成功或部份已發出
			$results = '簡訊發送失敗!!失敗原因：' ;
		}
		
		switch ($sys) {
			case '1' :
					$results = $this->return_code($sms_id) ;
					break ;
			case '2' :
					$results = $this->fet_sms_code($sms_id) ;
					break ;
			default :
					$results = $this->return_code($sms_id) ;
		}
		
		//return $results ;
		return $sms_id ;
	}

	function manual_sms_send2($name,$mobile,$target,$txt){
		$sys = $this->getSmsSystem() ;
				//開始發送簡訊
		if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile,$name,$txt."\r\n",$target,'',$tid,$sys) ; //回饋金特殊
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
			return $this->out_sms_code($_s,$_p,$_f,$_n) ;
		}
			
	}
	##

	public function send($pid,$sid,$bid,$target,$tid,$ok="n",$realty=0,$arr=null,$stxt=''){
		// $ok = 'n' 表示不發送,只回傳簡訊內容
		$_all = array() ;
		
		$sys = $this->getSmsSystem() ;
		$_contract_data = $this->getContractData($pid);
		$push_test = 0;
		//echo "<pre>";
		//print_r($_contract_data);exit;
		//所有的店東跟店長收簡訊要加物件地址
		switch($target) {
			case "income" :
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				// $this->testLog($pid,$target,$timeTxt);
				$this->execSQL = "SELECT a.eLender,a.eDebit,a.eBuyerMoney,a.eExtraMoney,a.eDepAccount,a.eTradeStatus,b.sName AS _title,a.eRemarkContent,a.eTradeDate,a.eChangeMoney FROM tExpense AS a INNER JOIN tCategoryIncome AS b ON a.eStatusRemark = b.sId WHERE a.eTradeStatus = 0 AND a.eDepAccount = '00".$pid."' and a.id = $tid";
				// echo $this->execSQL;
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				//echo $getMD->rowCount();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				
				//匯入款總金額
				$money = substr($fetchValue[0]["eLender"],0,-2) + 1 - 1 ;
				##
				// $changeMoney = $this->getChangeMoney($tid,$pid);
				// echo $money."_".$fetchValue[0]["eChangeMoney"];
				if ($money != $fetchValue[0]["eChangeMoney"] && $fetchValue[0]["eChangeMoney"] > 0) {
					$changeMoney = $fetchValue[0]["eChangeMoney"];//調帳後餘額
				}else{
					$changeMoney = 0;
				}
				
				
				//
				$_data = $_data1 = $_data4 = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							$_data[$i]['push'] = 1;//sms
							// $push[] = $_data[$i];
						}elseif ($check == 3) {
							$_data[$i]['push'] = 2;//sms
							// $push[] = $_data[$i];
						}
						
					}
				}
				


				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
				
				$_data = array_merge($_data,$_data1,$_data4) ;
				unset($_data1) ; unset($_data4) ;
				

				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
				}
				
				$_data = array_merge($_data,$_data1,$_data4) ;
				unset($_data1) ; unset($_data4) ;


				##

				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
				}
				
				$_data = array_merge($_data, $_data1, $_data4) ;
				 unset($_data1) ; unset($_data4) ;


				//增加第四組仲介簡訊發送
				$bid4 = $this->getFourBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
				}


				
				$_all = array_merge($_data, $_data1, $_data4) ;
				unset($_data) ; unset($_data1) ; unset($_data4) ;

				##	



				
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
			
				$bCount = 0 ;							//計算買方人數用
				//主買方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$_all[$_start+$_t]["iden"] = 'buy';
				$_all[$_start+$_t]["tTitle"] = '買方';
				//tTitle
				$bCount ++ ;
				$_t ++ ;
				##

				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["iden"] = 'buy';
					$_all[$_start+$_t]["tTitle"] = '買方';
					$_t ++ ;
				}

				unset($other_phone);
				##

				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$_all[$_start+$_t]["iden"] = 'buy';
					$_all[$_start+$_t]["tTitle"] = '買方';
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//買方代理人手機
					$_all[$_start+$_t]["iden"] = 'buy';
					$_all[$_start+$_t]["tTitle"] = '買方代理人';
					$_t ++ ;
				}

				unset($_other_owners);
				##
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);


				
				// if ($_contract_data[0]["b_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ; 			//買方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;		//買方經紀人(1)手機
				// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
				// 	$_t ++ ;
				// }
			
				// if ($_contract_data[0]["b_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;		//買方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;	//買方經紀人(2)手機
				// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;		//買方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;	//買方經紀人(3)手機
				// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;		//買方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;	//買方經紀人(4)手機
				// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
				// 	$_t++;
				// }
				##

				//賣方
													
				//入帳金額中若有仲介服務費且匯入金額大於仲介服務費時,(所有?)賣方單獨發送
				
				if (((($money - $fetchValue[0]["eBuyerMoney"]) > 0) && ($fetchValue[0]["eBuyerMoney"] > 0))||$fetchValue[0]['eExtraMoney'] >0) {
					$_owner_no = 0 ;	//索引
					$oCount = 0 ;							//計算賣方人數用
					//主賣方
					$_special[$_owner_no]["mName"] = $_contract_data[0]["o_name"] ; 			//賣方姓名
					$_special[$_owner_no]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
					$_special[$_owner_no]["iden"] = 'owner';
					$_special[$_owner_no]["tTitle"] = '賣方';
					$oCount ++ ;
					$_owner_no ++ ;
					##

					//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_special[$_owner_no]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_special[$_owner_no]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_special[$_owner_no]["iden"] = 'owner';
						$_special[$_owner_no]["tTitle"] = '賣方';
						$_owner_no ++ ;
					}

					unset($other_phone);
					##
					
					//其他賣方
					$_other_owners = $this->get_others($pid,'2') ;
					//print_r($_other_owners) ;
					for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
						$_special[$_owner_no]["mName"] = $_other_owners[$i]['cName'] ;			//其他賣方姓名
						$_special[$_owner_no]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;	//其他賣方手機
						$_special[$_owner_no]["iden"] = 'owner';
						$_special[$_owner_no]["tTitle"] = '賣方';
						$oCount ++ ;
						$_owner_no ++ ;
					}
					unset($_other_owners);
					##

					//7賣方代理人
						$_other_owners = $this->get_others($pid,'7') ;

						for ($i = 0 ; $i < count($_other_owners) ; $i ++) {

							$_special[$_owner_no]["mName"] = $_other_owners[$i]['cName'] ;			//賣方代理人姓名
							$_special[$_owner_no]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;	//賣方代理人手機
							$_special[$_owner_no]["iden"] = 'owner';
							$_special[$_owner_no]["tTitle"] = '賣方代理人';
							$_owner_no ++ ;
						}
						unset($_other_owners);
					##


					
				}
				else if ((($money - $fetchValue[0]["eBuyerMoney"]) == 0) && ($fetchValue[0]["eBuyerMoney"] > 0)) {
					//入帳金額中若有仲介服務費且匯入金額等於仲介服務費時,則賣方不發送
				}
				else {
					$oCount = 0 ;							//計算賣方人數用
					//主賣方
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 				//賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
					$_all[$_start+$_t]["iden"] = 'owner';
					$_all[$_start+$_t]["tTitle"] = '賣方';
					$oCount ++ ;
					$_t ++ ;
					##

					//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_all[$_start+$_t]["iden"] = 'owner';
						$_all[$_start+$_t]["tTitle"] = '賣方';
						$_t ++ ;
					}

					unset($other_phone);
					##
					
					//其他賣方
					$_other_owners = $this->get_others($pid,'2') ;
					//print_r($_other_owners) ;
					for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
						$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//其他賣方姓名
						$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//其他賣方手機
						$_all[$_start+$_t]["iden"] = 'owner';
						$_all[$_start+$_t]["tTitle"] = '賣方';
						$oCount ++ ;
						$_t ++ ;
					}
					unset($_other_owners);
					##

					//7賣方代理人
						$_other_owners = $this->get_others($pid,'7') ;
					// print_r($_other_owners) ;
					// die;
					for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
						$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//賣方代理人姓名
						$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//賣方代理人手機
						$_all[$_start+$_t]["iden"] = 'owner';
						$_all[$_start+$_t]["tTitle"] = '賣方代理人';
						$_t ++ ;
					}
					unset($_other_owners);
					##

				}
				##


				##
			
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);

				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_all[$_start+$_t]["tTitle"] ='賣方經紀人' ;
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_all[$_start+$_t]["tTitle"] ='賣方經紀人' ;
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_all[$_start+$_t]["tTitle"] ='賣方經紀人' ;
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(3)手機
				// 	$_all[$_start+$_t]["tTitle"] ='賣方經紀人' ;
				// 	$_t++;	
				// } 
				##

			
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				//$money = (int)substr($fetchValue[0]["eLender"],0,-2);

				
			
				$memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
				$M = substr($fetchValue[0]["eTradeDate"],3,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["eTradeDate"],5,2);
				$D = preg_replace("/^0/","",$D) ;
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##

				$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo.$money.'元' ;
				
				//調帳後的餘額簡訊	(20180613)
				
				if ($changeMoney > 0) {
					$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo.$changeMoney.'元' ;
					// echo 'XX';
				}
				//
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all,$boss) ;
				
				$_total = count($_all);
				// $arr = array(0,1,2,3,4);
				//要寄送的對象(勾選)
				if ($arr) {
					// unset($boss);
					// $_all = $arr;
					for ($i=0; $i < $_total; $i++) {  //全部
						

						for ($j=0; $j < count($arr); $j++) { //有勾選到的陣列
							
							if ($arr[$j]==$i) { //勾選到的
								//PUSH
								if ($push_test == 1) {
									if ($_all[$i]['boss'] == 1) {
										$boss[] = $_all[$i]; //店長店東
									}elseif($_all[$i]['push'] == 1){
										$push[] = $_all[$i];//推撥
									}elseif($_all[$i]['push'] == 2){
										$push[] = $_all[$i];//推撥
										$tmp[] =$_all[$i];//寫入陣列
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}else{
									if ($_all[$i]['boss'] == 1) {
										$boss[] = $_all[$i]; //店長店東
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}
								
							}
						}
					}
					$_all = $tmp;

					$_total = count($_all);
					
					unset($tmp);

					$sms_txt = $stxt;
					// $ok = 'QQ';
				}
				
				##
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
				// echo $sms_txt."\n";

				if ($ok == 'y'){
					for ($i = 0 ; $i < $_total ; $i ++) {						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-", "%", "_") ;
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;
							//if (substr($mobile_tel,0,2)=='09'){
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {

								
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、簡訊已發出' ;
									$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、單筆多封簡訊部分發出!!明細請至簡訊明細查詢' ;
									$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
								}
								else {											//發送失敗(f)
									$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
								}
							}
							else {												//門號錯誤(n)
								$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
							}
						}
					}

					if (count($boss) > 0) {
						$tmp = substr($pid,5,9);
						$addr = $this->getProperty($tmp);
						$memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
						// $sms_txt = str_replace(')', '）', $sms_txt);
						// $tmp2 = explode('）', $sms_txt);
						// $sms_txt = $tmp2[0].';'.$addr.'）'.$tmp2[1];
						$sms_txt = $sms_txt."(".$addr.")";
						
						$_boss = $this->sendBossSms($boss,$sms_txt,$target,$pid,$tid,$sys); //******
						unset($tmp);
						
					}

					// //////推撥/////
					// if ($push_test == 1) {
					// 	for ($i=0; $i < count($push); $i++) { 
					// 		# code...
					// 		$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					// 	}
					// }
					// ////////

					//if ($fetchValue[0]["eBuyerMoney"] > 0) { 
					// 入帳金額中若有仲介服務費且匯入金額大於仲介服務費時,因要只有賣方簡訊中要扣除服務費,所以把賣方單獨拉出來發送簡訊
					if (((($money - $fetchValue[0]["eBuyerMoney"]) > 0) && ($fetchValue[0]["eBuyerMoney"] > 0))||$fetchValue[0]['eExtraMoney']>0) {
						$money = $money - $fetchValue[0]["eBuyerMoney"] - $fetchValue[0]['eExtraMoney'];

						##//判斷是否有溢入款的文字
						$tmp = 	explode('+', $memo);

						for ($i=0; $i < count($tmp); $i++) { 
							if (preg_match("/^.*溢入款/",$tmp[$i])) {
							
									unset($tmp[$i]);
							}
							
							if (preg_match("/^買方仲介服務費/",$tmp[$i])) {
								unset($tmp[$i]);
							}

							if (preg_match("/^買方服務費/",$tmp[$i])) {
								unset($tmp[$i]);
							}

							if (preg_match("/^買方履保費/",$tmp[$i])) {
								unset($tmp[$i]);
							}
							if (preg_match("/^契稅/",$tmp[$i])) {
								unset($tmp[$i]);
							}
							if (preg_match("/^印花稅/",$tmp[$i])) {
								unset($tmp[$i]);
							}
							
						}
						$memo = implode('+', $tmp);
						unset($tmp);
						##

						//$sms_txt = "第一建經通知:買方".$buyer.$bCount.",賣方".$seller.$oCount."(保證號碼".substr($pid,5,9).")".$memo.",".$money."元於".$M.'/'.$D."存入履保專戶" ;
						$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo.$money.'元' ;
						if ($changeMoney > 0) {
							$changeMoney = $money - $fetchValue[0]["eBuyerMoney"] - $fetchValue[0]['eExtraMoney'];

							$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo.$changeMoney.'元' ;
						
						}
						

						for ($i = 0 ; $i < count($_special) ; $i ++) {
							$mobile_tel = $_special[$i]["mMobile"] ;
							$mobile_name = $_special[$i]["mName"] ;
						
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								

								$sms_id = $this->sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									$_special[$i]['mMobile'] .= '、'.$this->return_code('s') ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									$_special[$i]['mMobile'] .= '、'.$this->return_code('p') ;
								}
								else {											//發送失敗(f)
									$_special[$i]['mMobile'] .= '、'.$this->return_code('f') ;
								}
							}
							else {												//門號錯誤(n)
								$_special[$i]['mMobile'] .= '、'.$this->return_code('n') ;
							}
						}
					}


					
					
					//回覆簡訊發送結果
					if (count($_special) > 0) {

						if (count($_boss) > 0 && count($_all) > 0) {
							return array_merge($_all,$_special,$_boss) ;
						}elseif(count($_boss) > 0){
							return array_merge($_boss,$_special) ;
						}elseif(count($_all) > 0){
							return array_merge($_all,$_special) ;
						}
						
					}
					else {
						if (count($_boss) > 0 && count($_all) > 0) {
							return array_merge($_all,$_boss) ;
						}elseif(count($_boss) > 0){
							return $_boss;
						}elseif(count($_all) > 0){
							return $_all ;
						}
						
					}
					##
				}else {
					$_all[] = $sms_txt;

					
					// print_r($push);
					
						// $tmp = substr($pid,5,9);
						// $addr = $this->getProperty($tmp);
						// $memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
						// $tmp2 = explode('）', $sms_txt);
						// $sms_txt = $tmp2[0].';'.$addr.'）'.$tmp2[1];
						// echo $sms_txt;
						
						// // $_boss = $this->sendBossSms($boss,$sms_txt,$target,$pid,$tid,$sys); //******
						// unset($tmp);
					// for ($i = 0 ; $i < $_total ; $i ++) {	
					// 	if ($changeMoney > 0) {
					// 				if (($_all[$i]['iden'] == 'buyer' || $_all[$i]['iden'] == 'owner')) {
					// 					if (($money - $changeMoney) > 0) {
					// 						echo $_all[$i]["mName"].$sms_txtC."<br>";
					// 					}
										
					// 				}else{
					// 					echo $_all[$i]["mName"].$sms_txt."<br>";
					// 				}
					// 	}else{
					// 		echo $_all[$i]["mName"].$sms_txt."<br>";
					// 	}	
					// }

					// for ($i = 0 ; $i < count($_special) ; $i ++) {
					// 		$mobile_tel = $_special[$i]["mMobile"] ;
					// 		$mobile_name = $_special[$i]["mName"] ;
						
					// 		if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								

					// 			//買賣方不看調帳
					// 			if ($changeMoney > 0) {
					// 				if ($_all[$i]['iden'] == 'buyer' || $_all[$i]['iden'] == 'owner') {
										
					// 					if (($money - $changeMoney) > 0) {
					// 						echo $sms_txtC."<br>";
											
					// 					}
					// 				}else{
					// 					echo $sms_txt."<br>";
					// 				}
					// 			}else{
					// 				echo $sms_txt."<br>";
					// 			}
								
					// 		}
					// 		else {												//門號錯誤(n)
								
					// 		}
					// }
					

					return $_all ;
				}
				
			break ;
			case 'income2':
					$StartTime = date('Y-m-d H:i:s');
					$StartTime2 = microtime(true);
					$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
					$this->testLog($pid,$target,$timeTxt);
					$this->execSQL = "SELECT a.eLender,a.eDebit,a.eBuyerMoney,a.eExtraMoney,a.eDepAccount,a.eTradeStatus,b.sName AS _title,a.eRemarkContent,a.eTradeDate FROM tExpense AS a INNER JOIN tCategoryIncome AS b ON a.eStatusRemark = b.sId WHERE a.eTradeStatus = 0 AND a.eDepAccount = '00".$pid."' and a.id = $tid";
					// echo $this->execSQL;
					$getMD = $this->DB_link->prepare($this->execSQL);
					$getMD->execute();
					//echo $getMD->rowCount();
					$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
					
					//匯入款總金額
					$money = substr($fetchValue[0]["eLender"],0,-2) + 1 - 1 ;
					##
					$changeMoney = $this->getChangeMoney($tid,$pid);
					//
					$_data = $_data1 = $_data4 = array() ;
					
					$_data = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

					if ($push_test == 1) {
						for ($i=0; $i < count($_data); $i++) { 
							$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

							if ($check == 2) {
								$_data[$i]['push'] = 1;//sms
								// $push[] = $_data[$i];
							}elseif ($check == 3) {
								$_data[$i]['push'] = 2;//sms
								// $push[] = $_data[$i];
							}
							
						}
					}

					$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
					
					$_data = array_merge($_data,$_data1,$_data4) ;
					unset($_data1) ; unset($_data4) ;
				

					//增加第二組仲介簡訊發送
					$bid2 = $this->getSecBranchMobile($pid) ;
					$_data1 = $_data4 = array() ;
					
					if ($bid2 > 0) {
						$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					}
					
					$_data = array_merge($_data,$_data1,$_data4) ;
					unset($_data1) ; unset($_data4) ;
					##

					//增加第三組仲介簡訊發送
					$bid3 = $this->getThrBranchMobile($pid) ;
					$_data1 = $_data4 = array() ;
					
					if ($bid3 > 0) {
						$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					}
					
					$_data = array_merge($_data, $_data1, $_data4) ;
					unset($_data1) ; unset($_data4) ;

					//增加第四組仲介簡訊發送
					$bid4 = $this->getFourBranchMobile($pid) ;
					$_data1 = $_data4 = array() ;
					
					if ($bid3 > 0) {
						$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
					}
					$_all = array_merge($_data, $_data1, $_data4) ;
					unset($_data) ; unset($_data1) ; unset($_data4) ;
					##				
				
				
					// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
					$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
					$_t = 0 ;
				
					$bCount = 0 ;							//計算買方人數用
					//主買方
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
					$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
					$_all[$_start+$_t]["iden"] = 'buyer';
					$_all[$_start+$_t]["tTitle"] = '買方';
					$bCount ++ ;
					$_t ++ ;
					##

					//主買方其他電話
					$other_phone = $this->get_phone(1,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_all[$_start+$_t]["iden"] = 'buyer';
						$_all[$_start+$_t]["tTitle"] = '買方';
						$_t ++ ;
					}

					unset($other_phone);
					##

					//其他買方
					$_other_owners = $this->get_others($pid,'1') ;
					//print_r($_other_owners) ;
					for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
						$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
						$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
						$_all[$_start+$_t]["iden"] = 'buyer';
						$_all[$_start+$_t]["tTitle"] = '買方';
						$bCount ++ ;
						$_t ++ ;
					}
					unset($_other_owners);
					##

					//6買方代理人
					$_other_owners = $this->get_others($pid,'6') ;
					//print_r($_other_owners) ;
					for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
						$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//買方代理人姓名
						$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//買方代理人手機
						$_all[$_start+$_t]["iden"] = 'buyer';
						$_all[$_start+$_t]["tTitle"] = '買方代理人';
						$_t ++ ;
					}

					unset($_other_owners);
					##
					
					//買方經紀人
					$other_phone = $this->get_phone(3,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_all[$_start+$_t]["tTitle"] = '買方經紀人';
						$_t ++ ;
					}

					unset($other_phone);
					// if ($_contract_data[0]["b_agent_mobile"] != "") {
					// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ; 			//買方經紀人(1)姓名
					// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;		//買方經紀人(1)手機
					// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					// 	$_t ++ ;
					// }
				
					// if ($_contract_data[0]["b_agent_mobile2"] != "") {
					// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;		//買方經紀人(2)姓名
					// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;	//買方經紀人(2)手機
					// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					// 	$_t++;
					// } 
				
					// if ($_contract_data[0]["b_agent_mobile3"] != "") {
					// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;		//買方經紀人(3)姓名
					// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;	//買方經紀人(3)手機
					// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					// 	$_t++;
					// } 
				
					// if ($_contract_data[0]["b_agent_mobile4"] != "") {
					// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;		//買方經紀人(4)姓名
					// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;	//買方經紀人(4)手機
					// 	$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					// 	$_t++;
					// }
					##

					//賣方
					

					$memo = $this->getMemo($tid,$fetchValue[0]["eBuyerMoney"],$fetchValue[0]['eExtraMoney'],$money);
					// print_r($memo);
					if ($memo['owner'] != '') { //另外發送
						$_owner_no = 0 ;	//索引
						$oCount = 0 ;							//計算賣方人數用
						//主賣方
						$_special[$_owner_no]["mName"] = $_contract_data[0]["o_name"] ; 			//賣方姓名
						$_special[$_owner_no]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
						$_special[$_owner_no]["iden"] = 'owner';
						$_special[$_owner_no]["tTitle"] = '賣方';
						$oCount ++ ;
						$_owner_no ++ ;
						##

						//主賣方其他電話
						$other_phone = $this->get_phone(2,$pid);

						for ($i=0; $i < count($other_phone); $i++) { 
							$_special[$_owner_no]["mName"] = $_contract_data[0]["o_name"]	; 					
							$_special[$_owner_no]["mMobile"] = $other_phone[$i]['cMobileNum']	;
							$_special[$_owner_no]["iden"] = 'owner';
							$_special[$_owner_no]["tTitle"] = '賣方';
							$_owner_no ++ ;
						}

						unset($other_phone);
						##
						
						//其他賣方
						$_other_owners = $this->get_others($pid,'2') ;
						//print_r($_other_owners) ;
						for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
							$_special[$_owner_no]["mName"] = $_other_owners[$i]['cName'] ;			//其他賣方姓名
							$_special[$_owner_no]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;	//其他賣方手機
							$_special[$_owner_no]["iden"] = 'owner';
							$_special[$_owner_no]["tTitle"] = '賣方';
							$oCount ++ ;
							$_owner_no ++ ;
						}
						unset($_other_owners);
						##

						//7賣方代理人
							$_other_owners = $this->get_others($pid,'7') ;

							for ($i = 0 ; $i < count($_other_owners) ; $i ++) {

								$_special[$_owner_no]["mName"] = $_other_owners[$i]['cName'] ;			//賣方代理人姓名
								$_special[$_owner_no]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;	//賣方代理人手機
								$_special[$_owner_no]["iden"] = 'owner';
								$_special[$_owner_no]["tTitle"] = '賣方代理人';
								$_owner_no ++ ;
							}
							unset($_other_owners);
						##
						
						##

					}elseif ($memo['status'] == 1) {
						//不發送
						$oCount = 1 ;							//計算賣方人數用
						//其他賣方
						$_other_owners = $this->get_others($pid,'2') ;
						
						
							$oCount += count($_other_owners);
						
						unset($_other_owners);

					}else{
						$oCount = 0 ;							//計算賣方人數用
						//主賣方
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 				//賣方姓名
						$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
						$_all[$_start+$_t]["iden"] = 'owner';
						$_all[$_start+$_t]["tTitle"] = '賣方';
						$oCount ++ ;
						$_t ++ ;
						##

						//主賣方其他電話
						$other_phone = $this->get_phone(2,$pid);

						for ($i=0; $i < count($other_phone); $i++) { 
							$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
							$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
							$_all[$_start+$_t]["iden"] = 'owner';
							$_all[$_start+$_t]["tTitle"] = '賣方';
							$_t ++ ;
						}

						unset($other_phone);
						##
						
						//其他賣方
						$_other_owners = $this->get_others($pid,'2') ;
						//print_r($_other_owners) ;
						for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
							$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//其他賣方姓名
							$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//其他賣方手機
							$_all[$_start+$_t]["iden"] = 'owner';
							$_all[$_start+$_t]["tTitle"] = '賣方';
							$oCount ++ ;
							$_t ++ ;
						}
						unset($_other_owners);
						##

						//7賣方代理人
							$_other_owners = $this->get_others($pid,'7') ;
						// print_r($_other_owners) ;
						// die;
						for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
							$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//賣方代理人姓名
							$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//賣方代理人手機
							$_all[$_start+$_t]["iden"] = 'owner';
							$_all[$_start+$_t]["tTitle"] = '賣方代理人';
							$_t ++ ;
						}
						unset($_other_owners);
						##

							
					}

					//賣方經紀人

					$other_phone = $this->get_phone(4,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
						$_t ++ ;
					}

					unset($other_phone);
						// if ($_contract_data[0]["o_agent_mobile"] != "") {
						// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
						// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
						// 	$_all[$_start+$_t]["tTitle"] = '賣方經紀人' ;
						// 	$_t ++ ;
						// }
						
						// if ($_contract_data[0]["o_agent_mobile2"] != "") {
						// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
						// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
						// 	$_all[$_start+$_t]["tTitle"] = '賣方經紀人' ;
						// 	$_t++;	
						// } 
					
						// if ($_contract_data[0]["o_agent_mobile3"] != "") {
						// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
						// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
						// 	$_all[$_start+$_t]["tTitle"] = '賣方經紀人' ;
						// 	$_t++;
						// } 
					
						// if ($_contract_data[0]["o_agent_mobile4"] != "") {
						// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
						// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(3)手機
						// 	$_all[$_start+$_t]["tTitle"] = '賣方經紀人' ;
						// 	$_t++;	
						// } 
						##

					// print_r($_special);

					$M = substr($fetchValue[0]["eTradeDate"],3,2);
					$M = preg_replace("/^0/","",$M) ;
					$D = substr($fetchValue[0]["eTradeDate"],5,2);
					$D = preg_replace("/^0/","",$D) ;

					//簡訊內容重整
					$buyer = $_contract_data[0]["b_name"];
					$seller = $_contract_data[0]["o_name"];
					
					//是否多人買方
					$bCount = $this->getOhterBuyerOwner($bCount) ;
					##
					
					//是否多人賣方
					$oCount = $this->getOhterBuyerOwner($oCount) ;
					##	

					//正常版
					$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo['normal'].'元' ;
					
					$sms_txtC = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.($memo['normal']-$changeMoney).'元' ;
					

					//濾除重複簡訊對象並重新排序
					$_all = $this->filter_array($_all,$boss) ;
					
					$_total = count($_all);
					// $arr = array(0,1,2,3,4);
					//要寄送的對象(勾選)
					if ($arr) {
						// unset($boss);
						// $_all = $arr;
						for ($i=0; $i < $_total; $i++) {  //全部
							
							
							for ($j=0; $j < count($arr); $j++) { //有勾選到的陣列
								
								if ($arr[$j]==$i) { //勾選到的
									if ($push_test == 1) {
										if ($_all[$i]['boss'] == 1) {
											$boss[] = $_all[$i]; //店長店東
										}else if($_all[$i]['push'] == 1){
											$push[] = $_all[$i];
										}elseif($_all[$i]['push'] == 2){ //推播跟簡訊
											$push[] = $_all[$i];
											$tmp[] =$_all[$i];//寫入陣列
										}else{
											$tmp[] =$_all[$i];//寫入陣列
										}
									}else{
										if ($_all[$i]['boss'] == 1) {
											$boss[] = $_all[$i]; //店長店東
										}else{
											$tmp[] =$_all[$i];//寫入陣列
										}
									}
									
									// unset($_all[])
								}
							}
						}
						$_all = $tmp;

						$_total = count($_all);
						
						unset($tmp);

						$sms_txt = $stxt;
						// $ok = 'QQ';
					}
					// $owner_sptxt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo['owner'].'元' ;
							
					// echo $owner_sptxt;
					// print_r($_special);
					$EndTime = date('Y-m-d H:i:s');
					$EndTime2 = microtime(true);
					$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
					$this->testLog($pid,$target,$timeTxt);
					$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");

					if ($ok == 'y'){
						for ($i = 0 ; $i < $_total ; $i ++) {						
							if (trim($_all[$i]["mMobile"]) != "") {
								$check_word = array("-", "%", "_") ;
								$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;
								//if (substr($mobile_tel,0,2)=='09'){
								if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
									// $sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
									//買賣方不看調帳
									if ($changeMoney > 0) {
										if ($_all[$i]['iden'] == 'buyer' || $_all[$i]['iden'] == 'owner') {
											
											if (($money - $changeMoney) > 0) {
												// echo $sms_txtC."<br>";。
												$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txtC,$target,$pid,$tid,$sys) ;
											}
										}else{
											$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
										}
									}else{
										$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
									}

									
									if ($sms_id == 's') {							//發送成功(s)
										//$_all[$i]['mMobile'] .= '、簡訊已發出' ;
										$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									}
									else if ($sms_id == 'p') {						//部份成功(p)
										//$_all[$i]['mMobile'] .= '、單筆多封簡訊部分發出!!明細請至簡訊明細查詢' ;
										$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									}
									else {											//發送失敗(f)
										$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									}
								}
								else {												//門號錯誤(n)
									$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								}
							}
						}

						if ($push_test == 1) {
							//////推撥/////
							for ($i=0; $i < count($push); $i++) { 
								$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
							}
							////////
						}

						if (count($boss) > 0) {
							$tmp = substr($pid,5,9);
							$addr = $this->getProperty($tmp);
							// $memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
							
							$sms_txt = $sms_txt."(".$addr.")";
							
							$_boss = $this->sendBossSms($boss,$sms_txt,$target,$pid,$tid,$sys); //******
							unset($tmp);
							
						}
				
						//if ($fetchValue[0]["eBuyerMoney"] > 0) { 
						// 入帳金額中若有仲介服務費且匯入金額大於仲介服務費時,因要只有賣方簡訊中要扣除服務費,所以把賣方單獨拉出來發送簡訊
						
						if ($memo['owner'] != '' ) {
							
							$owner_sptxt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo['owner'].'元' ;
							
							$owner_sptxtC = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.($memo['owner']-$changeMoney).'元' ;
							
							for ($i = 0 ; $i < count($_special) ; $i ++) {
								$mobile_tel = $_special[$i]["mMobile"] ;
								$mobile_name = $_special[$i]["mName"] ;
							
								if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
									// $sms_id = $this->sms_send($mobile_tel,$mobile_name,$owner_sptxt,$target,$pid,$tid,$sys) ;

									//買賣方不看調帳
								if ($changeMoney > 0) {
									if ($_all[$i]['iden'] == 'buyer' || $_all[$i]['iden'] == 'owner') {
										
										if (($money - $changeMoney) > 0) {
											// echo $sms_txtC."<br>";。
											$sms_id = $this->sms_send($mobile_tel,$mobile_name,$owner_sptxtC,$target,$pid,$tid,$sys) ;
										}
									}else{
										$sms_id = $this->sms_send($mobile_tel,$mobile_name,$owner_sptxt,$target,$pid,$tid,$sys) ;
									}
								}else{
									$sms_id = $this->sms_send($mobile_tel,$mobile_name,$owner_sptxt,$target,$pid,$tid,$sys) ;
								}


									
									if ($sms_id == 's') {							//發送成功(s)
										$_special[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									}
									else if ($sms_id == 'p') {						//部份成功(p)
										$_special[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									}
									else {											//發送失敗(f)
										$_special[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									}
								}
								else {												//門號錯誤(n)
									$_special[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								}
							}
						}
						
						
						//回覆簡訊發送結果
						if (count($_special) > 0) {

							if (count($_boss) > 0 && count($_all) > 0) {
								return array_merge($_all,$_special,$_boss) ;
							}elseif(count($_boss) > 0){
								return array_merge($_boss,$_special) ;
							}elseif(count($_all) > 0){
								return array_merge($_all,$_special) ;
							}
							
						}
						else {
							if (count($_boss) > 0 && count($_all) > 0) {
								return array_merge($_all,$_boss) ;
							}elseif(count($_boss) > 0){
								return $_boss;
							}elseif(count($_all) > 0){
								return $_all ;
							}
							
						}
						##
					}else {
						$_all[] = $sms_txt;
						
						if ($memo['owner'] != '' && $_SESSION['member_id'] == 6 ) { // 
							// $owner_sptxtC = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.($memo['owner']-$changeMoney).'元' ;
							
							// $owner_sptxt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號' .substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入'.$memo['owner'].'元' ;
							// echo '賣方簡訊內容:'.$owner_sptxt."<br>";

							// for ($i=0; $i < count($_special); $i++) { 
							// 	echo $_special[$i]['mName']."_".$_special[$i]['mMobile']."<bR>";
							// }
						}
						//回覆簡訊發送結果
						
							// $tmp = substr($pid,5,9);
							// $addr = $this->getProperty($tmp);
							// $memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
							// $tmp2 = explode('）', $sms_txt);
							// $sms_txt = $tmp2[0].';'.$addr.'）'.$tmp2[1];
							// echo $sms_txt;
							
							// // $_boss = $this->sendBossSms($boss,$sms_txt,$target,$pid,$tid,$sys); //******
							// unset($tmp);
							
						

						return $_all ;
					}
					// if (condition) { //getMemo($tid,$money)
					// 	# code...
					// }

			break;	
			case "cheque" :
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = '
					SELECT 
						a.eLender,
						a.eDebit,
						a.eDepAccount,
						a.eTradeStatus,
						a.eRemarkContent,
						a.eTradeDate
					FROM
						tExpense_cheque AS a
					WHERE
						(a.eTradeStatus = 0 OR a.eTradeStatus = 10 OR a.eTradeStatus = 11 OR a.eTradeStatus = 12
						OR a.eTradeStatus = 20 OR a.eTradeStatus = 22 OR a.eTradeStatus = 30 OR a.eTradeStatus =31
						OR a.eTradeStatus = 40 OR a.eTradeStatus = 48 OR a.eTradeStatus = 49 OR a.eTradeStatus = 9)
						AND a.eDepAccount = "00'.$pid.'"
						AND a.id = "'.$tid.'";
				' ;
			
				// 10：入庫
				// 11：入庫-延期提示
				// 12：入庫-領回
				// 20：出庫
				// 22：出庫-領回
				// 30：退票
				// 31：本埠退票
				// 40：銷帳
				// 48：退票通知沖正
				// 49：即時銷入帳
				
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				
				//匯入款總金額
				$money = substr($fetchValue[0]["eLender"],0,-2) + 1 - 1 ;
				##
				
				//增加地政士與第一組仲介簡訊發送
				$_data = $_data1 = $_data4 = $manager = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid) ;		 // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							unset($_data[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							// unset($_data[$i]);
						}
						
					}
					sort($_data);
				}
				// print_r($push);
				


				$_data1 = $this->getsBranchMobile($pid,$bid,'店長') ;	 // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東') ;	 // 取得店發送簡訊對象
				
				// $_data = array_merge($_data,$_data1,$_data4) ;
				$manager = array_merge($manager,$_data1,$_data4);
				

				unset($_data1,$_data4) ;
				##
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
					
				}
				
				// $_data = array_merge($_data,$_data1,$_data4) ;
				unset($_data1,$_data4) ;
				##
			
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				unset($_data1,$_data4) ;

				//增加第四組仲介簡訊發送
				$bid4 = $this->getFourBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid4 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
					
				}

				// $_data = array_merge($_data,$_data1,$_data4) ;

				$_all = $_data;
				// $_all = array_merge($_data, $_data1, $_data4) ;
				unset($_data,$_data1,$_data4) ;

				##
				
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				//主買方
				$bCount = 0 ;																	//計算買方人數用
				
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				$_t ++ ;
				##
				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				##
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##
				##
				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//買方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
				
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["b_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ; 			//買方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;		//買方經紀人(1)手機
				// 	$_t ++ ;
				// }
			
				// if ($_contract_data[0]["b_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;		//買方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;	//買方經紀人(2)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;		//買方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;	//買方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;		//買方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;	//買方經紀人(4)手機
				// 	$_t++;
				// }
				##

				//主賣方
				$oCount = 0 ;																//計算賣方人數用
				
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 				//賣方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
				$oCount ++ ;
				$_t ++ ;
				##
				
				//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_t ++ ;
					}

					unset($other_phone);
				##

				//其他賣方
				$_other_owners = $this->get_others($pid,'2') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//其他賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//其他賣方手機
					$oCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//7賣方代理人
				$_other_owners = $this->get_others($pid,'7') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//7賣方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//7賣方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##

			
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(4)手機
				// 	$_t++;	
				// } 
				##
				
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
			
				$memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
				$M = substr($fetchValue[0]["eTradeDate"],3,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["eTradeDate"],5,2);
				$D = preg_replace("/^0/","",$D) ;
				##
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入票據金額'.$money.'元,待票據兌現後再另行簡訊通知' ;
				
				//濾除重複簡訊對象並重新排序
					
				$_all = $this->filter_array($_all,$manager) ;
				
				$_total = count($_all);

				//$_all = $tmp;
				if ($arr) {
					$_all = array_merge($_all,$manager);
					$_all = $this->filter_array($_all) ;
					$_total = count($_all);

					unset($manager);
					

					
					for ($i=0; $i < $_total; $i++) {  //全部
					
						for ($j=0; $j < count($arr); $j++) { //有勾選到的陣列
							// echo $arr[$j]."_".$_all[$i]['mMobile']."<br>";
							// echo $_all[$i]['mMobile']."<br>";
							
							if ($arr[$j]==$_all[$i]['mMobile']) { //勾選到的
								//PUSH
								if ($push_test == 1) {
									if ($_all[$i]['boss'] == 1) {
										$manager[] = $_all[$i]; //店長店東
									}elseif($_all[$i]['push'] == 1){
										$push[] = $_all[$i];//推撥
									}elseif($_all[$i]['push'] == 2){
										$push[] = $_all[$i];//推撥
										$tmp[] =$_all[$i];//寫入陣列
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}else{
									if ($_all[$i]['boss'] == 1) {
										$manager[] = $_all[$i]; //店長店東
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}
								
							}
						}
					}
					unset($_all);

					// echo "<pre>";
					// print_r($manager);
					// echo "</pre>";

					$_all = $tmp;

					
					
					
					$sms_txt = $stxt;
					
				}
				

				##
			
				
				// echo $sms_txt."\n";

				// $ok = "QQ";
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");

				if ($ok == 'y'){
					for ($i = 0 ; $i < $_total ; $i ++) {						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-", "%", "_") ;
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {

								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') $_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;			//發送成功(s)
								else if ($sms_id == 'p') $_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;		//部份成功(p)
								else $_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;							//發送失敗(f)
							}
							else $_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;								//門號錯誤(n)
						}
					}

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.';'.$addr.'）存入票據金額'.$money.'元,待票據兌現後再另行簡訊通知' ;
					if ($arr) {
						$sms_txt = $stxt.'('.$addr.')';
					}

				
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);
						
					
					//回覆簡訊發送結果
					if (count($_boss) > 0) return array_merge($_all,$_boss) ;
					else {return $_all ;}
					##
				}else {
					// echo "<pre>";
					// print_r($manager);
					// echo "</pre>";

					$show['txt'] = $sms_txt;
					if (count($manager) > 0) {
						if ($_all) {
							$show['sms'] = array_merge($_all,$manager);
						}else{
							$show['sms'] = $manager;
						}
						
					}else{
						$show['sms'] = $_all;
					}

					

					return $show;
					// if (count($manager) > 0) {
					// 	return array_merge($_all,$manager) ;
					// }else{
					// 	return $_all;
					// }
					
				} 
				
				break ;
			case "chequetaisin" :
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = '
					SELECT 
						a.eLender,
						a.eDebit,
						a.eDepAccount,
						a.eTradeDate
					FROM
						tExpense_cheque_taishin AS a
					WHERE
						a.eId = "'.$tid.'";
				' ;
				
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				
				//匯入款總金額
				$money = substr($fetchValue[0]["eLender"],0,-2) + 1 - 1 ;
				##
				
				//增加地政士與第一組仲介簡訊發送
				$_data = $_data1 = $_data4 = $manager = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid) ;		 // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							unset($_data[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							// unset($_data[$i]);
						}
						
					}
					sort($_data);
				}
				// print_r($push);
				


				$_data1 = $this->getsBranchMobile($pid,$bid,'店長') ;	 // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東') ;	 // 取得店發送簡訊對象
				
				// $_data = array_merge($_data,$_data1,$_data4) ;
				$manager = array_merge($manager,$_data1,$_data4);
				

				unset($_data1,$_data4) ;
				##
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
					
				}
				
				// $_data = array_merge($_data,$_data1,$_data4) ;
				unset($_data1,$_data4) ;
				##
			
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				unset($_data1,$_data4) ;

				//增加第四組仲介簡訊發送
				$bid4 = $this->getFourBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid4 > 0) {
					$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
					
				}

				// $_data = array_merge($_data,$_data1,$_data4) ;

				$_all = $_data;
				// $_all = array_merge($_data, $_data1, $_data4) ;
				unset($_data,$_data1,$_data4) ;

				##
				
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				//主買方
				$bCount = 0 ;																	//計算買方人數用
				
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				$_t ++ ;
				##
				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				##
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##
				##
				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//買方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
				
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["b_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ; 			//買方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;		//買方經紀人(1)手機
				// 	$_t ++ ;
				// }
			
				// if ($_contract_data[0]["b_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;		//買方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;	//買方經紀人(2)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;		//買方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;	//買方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;		//買方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;	//買方經紀人(4)手機
				// 	$_t++;
				// }
				##

				//主賣方
				$oCount = 0 ;																//計算賣方人數用
				
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 				//賣方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;			//賣方手機
				$oCount ++ ;
				$_t ++ ;
				##
				
				//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_t ++ ;
					}

					unset($other_phone);
				##

				//其他賣方
				$_other_owners = $this->get_others($pid,'2') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//其他賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//其他賣方手機
					$oCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//7賣方代理人
				$_other_owners = $this->get_others($pid,'7') ;
				
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;				//7賣方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;		//7賣方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##

			
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(4)手機
				// 	$_t++;	
				// } 
				##
				
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
			
				$memo = $fetchValue[0]["_title"].$fetchValue[0]["eRemarkContent"];
				$M = substr($fetchValue[0]["eTradeDate"],3,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["eTradeDate"],5,2);
				$D = preg_replace("/^0/","",$D) ;
				##
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.'）存入票據金額'.$money.'元,待票據兌現後再另行簡訊通知' ;
				
				//濾除重複簡訊對象並重新排序
					
				$_all = $this->filter_array($_all,$manager) ;
				
				$_total = count($_all);

				//$_all = $tmp;
				if ($arr) {
					$_all = array_merge($_all,$manager);
					$_all = $this->filter_array($_all) ;
					$_total = count($_all);

					unset($manager);
					

					
					for ($i=0; $i < $_total; $i++) {  //全部
					
						for ($j=0; $j < count($arr); $j++) { //有勾選到的陣列
							// echo $arr[$j]."_".$_all[$i]['mMobile']."<br>";
							// echo $_all[$i]['mMobile']."<br>";
							
							if ($arr[$j]==$_all[$i]['mMobile']) { //勾選到的
								//PUSH
								if ($push_test == 1) {
									if ($_all[$i]['boss'] == 1) {
										$manager[] = $_all[$i]; //店長店東
									}elseif($_all[$i]['push'] == 1){
										$push[] = $_all[$i];//推撥
									}elseif($_all[$i]['push'] == 2){
										$push[] = $_all[$i];//推撥
										$tmp[] =$_all[$i];//寫入陣列
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}else{
									if ($_all[$i]['boss'] == 1) {
										$manager[] = $_all[$i]; //店長店東
									}else{
										$tmp[] =$_all[$i];//寫入陣列
									}
								}
								
							}
						}
					}
					unset($_all);

					// echo "<pre>";
					// print_r($manager);
					// echo "</pre>";

					$_all = $tmp;

					
					
					
					$sms_txt = $stxt;
					
				}
				

				##
			
				
				// echo $sms_txt."\n";

				// $ok = "QQ";
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");

				if ($ok == 'y'){
					for ($i = 0 ; $i < $_total ; $i ++) {						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-", "%", "_") ;
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {

								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') $_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;			//發送成功(s)
								else if ($sms_id == 'p') $_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;		//部份成功(p)
								else $_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;							//發送失敗(f)
							}
							else $_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;								//門號錯誤(n)
						}
					}

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					$sms_txt = '第一建經信託履約保證專戶已於'.$M.'月'.$D.'日收到保證編號'.substr($pid,5,9).'（買方'.$buyer.$bCount.'賣方'.$seller.$oCount.';'.$addr.'）存入票據金額'.$money.'元,待票據兌現後再另行簡訊通知' ;

				
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);
						
					
					//回覆簡訊發送結果
					if (count($_boss) > 0) return array_merge($_all,$_boss) ;
					else {return $_all ;}
					##
				}else {
					// echo "<pre>";
					// print_r($manager);
					// echo "</pre>";

					$show['txt'] = $sms_txt;
					if (count($manager) > 0) {
						if ($_all) {
							$show['sms'] = array_merge($_all,$manager);
						}else{
							$show['sms'] = $manager;
						}
						
					}else{
						$show['sms'] = $_all;
					}

					

					return $show;
					// if (count($manager) > 0) {
					// 	return array_merge($_all,$manager) ;
					// }else{
					// 	return $_all;
					// }
					
				} 
				
				break ;	
			case "扣繳稅款" :

				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數

				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);
				
				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				//print_r($fetchValue);exit;
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
		
				//取得地政士發送簡訊對象
				$_all = $this->getsScrivenerMobile($pid,$sid) ;
				if ($push_test ==1) {
					for ($i=0; $i < count($_all); $i++) { 
						$check = $this->checkScrivenerSms($_all[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							unset($_all[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							// unset($_all[$i]);
						}
						
					}
					sort($_all);
				}
				// print_r($_all);
				// print_r($push);
				##
				//仲介一
				$manager = array();
				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
				$manager = array_merge($manager, $_data1, $_data4) ;
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ;unset($_data4) ;
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				##
				
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				unset($_data1) ; unset($_data4) ;
				##
				$_start = count($_all);
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);
				// print_r($other_phone);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				##
				
				//計算買方人數、是否多人買方
				$bCount = count($this->get_others($pid,'1')) + 1 ;
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//計算賣方人數、是否多人賣方
				$oCount = count($this->get_others($pid,'2')) + 1 ;
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;
				
				// $sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount."之稅款新台幣".$money."元已於".$M.'/'.$D."存入地政士指定帳戶" ; 
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount."之稅款已於".$M.'/'.$D."存入地政士指定帳戶" ; 
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				//濾除重複簡訊對象並重新排序
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;
				
				// $_all = $this->filter_array($_all,$noSend) ;
				##
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");

				$_total = count($_all);
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";
					
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
							
						}
					}

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					//店長店東
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					// $sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr."之稅款新台幣".$money."元已於".$M.'/'.$D."存入地政士指定帳戶" ; 
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr."之稅款已於".$M.'/'.$D."存入地政士指定帳戶" ; 
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					if ($manager) {
						// $tmp = substr($pid,5,9);
						// $addr = $this->getProperty($tmp);
						// $sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr."之稅款新台幣".$money."元已於".$M.'/'.$D."存入地政士指定帳戶" ; 
						// echo $sms_txt."<br>";
						$_all = array_merge($_all,$manager);
					}
					
					return $_all ;
					##
				}
				break ;
			
			case "點交(結案)":
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數
				
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				// echo $this->execSQL;
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				
				
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
				

				//加入地政士簡訊對象
				$_all = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_all); $i++) { 
						$check = $this->checkScrivenerSms($_all[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_all[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							unset($_all[$i]);
						}elseif ($check == 3) {
							// $_all[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							// unset($_all[$i]);
						}
						
					}
					sort($_all);
				}

				// print_r($push);
				// 仲介
				//有服務費
					
				
					$_data = $_data1 = $_data4 = $manager = array() ;
					$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
					unset($_data1) ; unset($_data4) ; 

					//增加第二組仲介簡訊發送
					$bid2 = $this->getSecBranchMobile($pid) ;
					$_data1 = $_data4 = array() ;
					
					if ($bid2) {
						$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
						$manager = array_merge($manager,$_data1,$_data4);
						
					}
					unset($_data1) ; unset($_data4) ;

					//增加第三組仲介簡訊發送
					$bid3 = $this->getThrBranchMobile($pid) ;
					$_data1 = $_data4 =  array() ;
					
					if ($bid3) {
						$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
						$manager = array_merge($manager,$_data1,$_data4);
						
					}
					
					unset($_data1,$_data4);

					//增加第四組仲介簡訊發送
					$bid4 = $this->getFourBranchMobile($pid) ;
					$_data1 = $_data4 =  array() ;
					
					if ($bid4) {
						$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
						$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
						$manager = array_merge($manager,$_data1,$_data4);
						
					}
					
					unset($_data1,$_data4);
				
				

				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				$bCount = 0 ;							//計算買方人數用
				//主買方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				$_t ++ ;
				##

				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				##
				
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//6買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//6買方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
						
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					// $_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				
				##

				$oCount = 0 ;							//計算賣方人數用
				//主賣方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 					//賣方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;				//賣方手機
				$oCount ++ ;
				$_t ++ ;
				##

				//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_t ++ ;
					}

					unset($other_phone);
				##
				
				//其他賣方$pid
				$_other_owners = $this->get_others($pid,'2') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他賣方手機
					$oCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//7賣方代理人
				$_other_owners = $this->get_others($pid,'7') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//7賣方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//7賣方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
							
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					// $_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);

				
				##

				//print_r($_all);exit;
			
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount."已完成點交作業,各應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				
				##
			
				//濾除重複簡訊對象並重新排序
				if (is_array($manager)) {
					
					
					$_all = $this->filter_array($_all,$manager) ;
					$manager = $this->filter_array($manager,$noSend);

					if ($realty > 0) {
						$bb = $this->sendCaseEndRealty($pid,$fetchValue[0]["tExport_nu"],"n",$noSend,$target,$tid,$sys,($buyer.$bCount),($seller.$oCount),$addr,$date);
						
						$manager = $this->filter_array($manager,$bb);
						
					}

					
					//
				}

				// if ($realty > 0) {
				// 	unset($manager);
				// }
				

				// $_data = $this->filter_array($_data,$manager) ;
				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;
				// $_data = $this->filter_array($_data,$noSend) ;

				##
				
				##		
				
				
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
				##
				
				$_total = count($_all) ;
				if ($ok == 'y') {
					//非仲介對象簡訊發送
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";
					
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					##
					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					
					
					##
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);

					if ($realty > 0)  {//服務費 另外發送

						// $sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：買方'.$buyer.$bCount.',賣方'.$seller.$oCount.';'.$addr.',已完成點交作業服務費'.$realty.'元已於'.$date.'匯入仲介指定帳戶';
						$this->sendCaseEndRealty($pid,$fetchValue[0]["tExport_nu"],$ok,$noSend,$target,$tid,$sys,($buyer.$bCount),($seller.$oCount),$addr,$date);
					}
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr.",已完成點交作業,各應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					
					// $sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr.",已完成點交作業,各應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
					
					
					unset($tmp);

					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##				
				}else {
					//回覆簡訊發送結果
					
					if (is_array($manager)) {
						$_all = array_merge($_all,$manager) ;
					}
					

					
					return $_all ;
					##
				}
				break ;
			
			case "解除契約":
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數
				
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				//print_r($fetchValue);exit;
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
		
				//
				$_data = $_data1 = $_data4 = $manager = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							unset($_data[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							// unset($_data[$i]);
						}
						
					}
					sort($_data);
				}
				// print_r($push);

				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象

				$manager = array_merge($manager,$_data1,$_data4);
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				##
				
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				unset($_data1) ; unset($_data4) ;
				##
				//增加第4組仲介簡訊發送
				$bid4 = $this->getFourBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid4) {
					$_data1 = $this->getsBranchMobile($pid,$bid4,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid4,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				unset($_data1) ; unset($_data4) ;
				
				// $_all = array_merge($_data, $_data1, $_data4) ;
				$_all = $_data;
				unset($_data) ; unset($_data1) ; unset($_data4) ;
				##				
				
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				$bCount = 0 ;							//計算買方人數用
				//主買方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				$_t ++ ;
				##
				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				##
				
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//6買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//6買方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
				
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);

				// if ($_contract_data[0]["b_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_agent_name"] ; 			//買方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_agent_mobile"] ;		//買方經紀人(1)手機
				// 	$_t ++ ;
				// }
			
				// if ($_contract_data[0]["b_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name2"] ;		//買方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile2"] ;	//買方經紀人(2)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name3"] ;		//買方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile3"] ;	//買方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["b_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["b_agent_name4"] ;		//買方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["b_agent_mobile4"] ;	//買方經紀人(4)手機
				// 	$_t++;
				// }
				##

				$oCount = 0 ;							//計算賣方人數用
				//主賣方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 					//賣方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;				//賣方手機
				$oCount ++ ;
				$_t ++ ;
				##

				//主賣方其他電話
					$other_phone = $this->get_phone(2,$pid);

					for ($i=0; $i < count($other_phone); $i++) { 
						$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
						$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
						$_t ++ ;
					}

					unset($other_phone);
				##
				
				//其他賣方$pid
				$_other_owners = $this->get_others($pid,'2') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他賣方手機
					$oCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//7賣方代理人
				$_other_owners = $this->get_others($pid,'7') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//7賣方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//7賣方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
					
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(3)手機
				// 	$_t++;	
				// } 
				##

				//print_r($_all);exit;
			
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				//$sms_txt = "第一建經通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount."已完成解除契約作業,應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount."已完成解除契約作業,應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all,$manager) ;

				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;
				$manager = $this->filter_array($manager,$noSend);
				##
				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
				
				$_total = count($_all) ;
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";
					
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////

					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount.",賣方".$seller.$oCount.";".$addr.",已完成解除契約作業,應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
				
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##				
				}else {
					//回覆簡訊發送結果
					if (count($manager) > 0) {
						return array_merge($_all,$manager) ;
					}else{
						return $_all;
					}
					##
				}
				break ;
				
			case "仲介服務費" :
			// echo 'II';
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數
				
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
		
				//qq
				$_data = $_data1 = $_data2 = $_data3 = $_data4 = array() ;
				
				

				$_data = $this->getsScrivenerMobile2($pid,$sid); //地政士的部分只撈取寄送服務費的人

				
				unset($tmp); unset($tmp2);

				$_data1 = $this->getsBranchMobile($pid,$fetchValue[0]['tStoreId'],'店長'); // 取得店發送簡訊對象			
				$_data4 = $this->getsBranchMobile($pid,$fetchValue[0]['tStoreId'],'店東'); // 取得店發送簡訊對象

				$_data2 = $this->getsBranchMobile($pid,$fetchValue[0]['tStoreId'],'會計'); // 取得店發送簡訊對象
				$_data3 = $this->getsBranchMobile($pid,$fetchValue[0]['tStoreId'],'秘書'); // 取得店發送簡訊對象


				// //第一組
				// $check = $this->checkBranch($pid,'1');

				// //check 1買賣方都可寄
				// // echo $check;
				// // die;
				// // echo $check."||".$fetchValue[0]["tSeller"]."||".$fetchValue[0]["tBuyer"]."<br>";

				//$this->getsBranchMobile($pid,$tmp2[$i]['tStoreId'],'店長'); // 取得店發送簡訊對象
				
				$_data = array_merge($_data, $_data1, $_data2, $_data3, $_data4) ;
					unset($_data1) ; unset($_data2) ; unset($_data3) ; unset($_data4) ;
				

				unset($check);
				##				
				$_all = $_data;
				//計算買方人數、是否多人買方
				$bCount = count($this->get_others($pid,'1')) + 1 ;
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//計算賣方人數、是否多人賣方
				$oCount = count($this->get_others($pid,'2')) + 1 ;
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				//$_all = array_merge($_data,$_data2); // 合併陣列		
				//$_all = array_merge($_all,$_data3); // 合併陣列
			
				$_start = count($_all);
			
				//print_r($fetchValue);exit;
				//
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$_buyer_money = $fetchValue[0]["tBuyer"]; // 買方服務費
				$_seller_money = $fetchValue[0]["tSeller"]; //賣方服務費
			
				//echo $_buyer_money;exit;
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;

				if ($_buyer_money > 0) {
					$_money = "買方服務費".$money;
				}elseif ($_seller_money > 0) {
					$_money = "賣方服務費".$money;
				}
			
				

				$tmp = substr($pid,5,9);

				$addr = $this->getProperty($tmp,'all');
					
				$date = $this->getBankDate($tid);
			
				// $sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：'.$_seller_txt.';服務費新台幣'.$_money.'元已於'.$M.'月'.$D.'日存入仲介指定帳戶' ;//20150804
				//第一建經信託履約保證專戶通知（保證號碼：006095118）：買方潘扶和,賣方何添順;基隆市中正區武昌街,賣方服務費120000元已於03月12日匯入仲介指定帳戶(218)
				$sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：買方'.$buyer.$bCount.',賣方'.$seller.$oCount.';'.$addr.','.$_money.'元已於'.$date.'匯入仲介指定帳戶';
					
				$sms_txt_big5 = mb_convert_encoding($sms_txt, 'BIG5', 'UTF-8');  
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all) ;
				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;

				##

				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
				
				$_total = count($_all);
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";
						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					return $_all ;
					##
				}
				
				break ;
				
			case "代清償" :
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數

				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
			
				//2012-12-07
				//$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				$tmp = $getMD->fetchALL(PDO::FETCH_ASSOC);
			
				$this->execSQL = "SELECT *,SUM(tMoney) tMoney FROM tBankTrans WHERE tVR_Code='".$pid."' AND tExport_nu='".$tmp[0]["tExport_nu"]."' AND tObjKind='".$target."'" ;
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
			
				$_all = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test) {
					for ($i=0; $i < count($_all); $i++) { 
						$check = $this->checkScrivenerSms($_all[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							unset($_all[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_all[$i];
							
						}
						
					}
					sort($_all);
				}
				####
				//仲介一
				$manager = array();
				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
				$manager = array_merge($manager, $_data1, $_data4) ;
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ;unset($_data4) ;
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				##
				
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				unset($_data1) ; unset($_data4) ;
				##
				$_start = count($_all);
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);
				// print_r($other_phone);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				###
				// print_r($push);
				//計算賣方人數
				$oCount = count($this->get_others($pid,'2')) + 1 ;
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				//簡訊內容重整
				$_seller_txt = '賣方'.$_contract_data[0]["o_name"].$oCount ;
				$_money = $fetchValue[0]["tMoney"];
			
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;				
			
				//$sms_txt = "第一建經通知（保證號碼：".substr($pid,5,9)."）：".$_seller_txt.";代償金額新台幣".$_money."元已於".$M.'/'.$D."匯入指定帳戶" ;
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：".$_seller_txt.";代償金額新台幣".$_money."元已於".$M.'/'.$D."匯入指定帳戶" ;
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all) ;
				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;
				##

				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
				
				$_total = count($_all);
				if ($ok == 'y'){
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";

						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					##
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					// $sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCount.";".$addr.",動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：".$_seller_txt.";".$addr."代償金額新台幣".$_money."元已於".$M.'/'.$D."匯入指定帳戶" ;
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					if ($manager) {
						$_all = array_merge($_all,$manager);
					}
					return $_all ;
					##
				}
				break ;
				
			case "賣方先動撥" : //20150917買賣方不收簡訊
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數
				
				$StartTime = date('Y-m-d H:i:s');
				$StartTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢開始'.$StartTime;
				$this->testLog($pid,$target,$timeTxt);

				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				//echo $this->execSQL;
				
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();

				//2012-12-13
				$tmp = $getMD->fetchALL(PDO::FETCH_ASSOC);
				
				$this->execSQL = "SELECT *,SUM(tMoney) tMoney FROM tBankTrans WHERE tVR_Code='".$pid."' AND tExport_nu='".$tmp[0]["tExport_nu"]."' AND tObjKind='".$target."'" ;
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
		
				//
				$_data = $_data1 = $_data4 = $manager = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							unset($_data[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							
						}
						
					}
					sort($_data);
				}
				// print_r($push);


				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象
				$manager = array_merge($manager, $_data1, $_data4) ;
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ;
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				##
				
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager, $_data1, $_data4) ;
				}
				$_all = $_data;
				// $_all = array_merge($_data, $_data1, $_data4) ;
				unset($_data) ; unset($_data1) ; unset($_data4) ;
				##				
							
				//$_all = array_merge($_data,$_data2); // 合併陣列
				
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				$bCount = 0 ;							//計算買方人數用
				//主買方
				// $_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				// $_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				// $_t ++ ;
				##

				//主買方其他電話
				// $other_phone = $this->get_phone(1,$pid);

				// for ($i=0; $i < count($other_phone); $i++) { 
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
				// 	$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
				// 	$_t ++ ;
				// }

				// unset($other_phone);
				##
				
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					// $_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					// $_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++;
					// $_t ++ ;
				}
				unset($_other_owners);
				##

				//6買方代理人
				// $_other_owners = $this->get_others($pid,'6') ;
				// //print_r($_other_owners) ;
				// for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
				// 	$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//6買方代理人姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//6買方代理人手機

				// 	$_t ++ ;
				// }
				// unset($_other_owners);
				##
				
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				##
				
				$oCount = 0 ;							//計算賣方人數用
				//主賣方
				// $_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 					//賣方姓名
				// $_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;				//賣方手機
				$oCount ++ ;
				// $_t ++ ;
				##

				//主賣方其他電話
					// $other_phone = $this->get_phone(2,$pid);

					// for ($i=0; $i < count($other_phone); $i++) { 
					// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
					// 	$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					// 	$_t ++ ;
					// }

					// unset($other_phone);
				##
				
				//其他賣方
				$_other_owners = $this->get_others($pid,'2') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					// $_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他賣方姓名
					// $_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他賣方手機
					$oCount ++ ;
					// $_t ++ ;
				}
				##

				//7賣方代理人
				// $_other_owners = $this->get_others($pid,'7') ;
				// //print_r($_other_owners) ;
				// for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
				// 	$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//7賣方代理人姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//7賣方代理人手機

				// 	$_t ++ ;
				// }
				##
				
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(3)手機
				// 	$_t++;	
				// } 
				##
				//print_r($_all);exit;
				
				//簡訊內容重整
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				//$sms_txt = "第一建經通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCount."動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCount."動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
				if ($ok == 'n') {
					echo $sms_txt;
				}
				
				
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all,$manager) ;
				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$_all = $this->filter_array($_all,$noSend) ;
				$manager = $this->filter_array($manager,$noSend);
				##

				$EndTime = date('Y-m-d H:i:s');
				$EndTime2 = microtime(true);
				$timeTxt = $pid."_".$target."_".'查詢結束'.$EndTime;
				$this->testLog($pid,$target,$timeTxt);
				$this->testLog($pid,$target,$pid."_".$target."_程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");

				$_total = count($_all);
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."<br>";
						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////
					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCount.";".$addr.",動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
				
					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);

					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					if (count($manager) > 0) {
						return array_merge($_all,$manager) ;
					}else{
						return $_all;
					}
					##
				}
				break ;

			case '回饋金':

				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數

			
				$tmp = explode(',', $bid);
				$a=0;
				for ($i=0; $i < count($tmp); $i++) { 

					$code = substr($tmp[$i], 0,2);
					$id = (int)substr($tmp[$i], 2);


					if ($code=='SC') {

						$tmp2 = $this->getfeedbackmobile2($id);
					}else{

						$tmp2 = $this->getfeedbackmobile($id);
					}

					// echo "<pre>";
					// print_r($tmp2);
					// echo "</pre>";
					unset($code);
					unset($id);
					// die;
					

					for ($j=0; $j < count($tmp2); $j++) { 
						
						$_all[$a]=$tmp2[$j];
						
						$a++;
					}
					unset($tmp2);
				}

				

				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all) ;
				##

				$sms_txt = $arr ;
				// echo $sms_txt;
				// echo "<pre>";
				// print_r($_all);
				// echo "</pre>";
				
				$_total = count($_all);
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."<br>";
						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$_all[$i]["bBranch"],$tid,$sys) ; //回饋金特殊
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					return $_all ;
					##
				}
			break;
			case '回饋金2':

				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數

			
				$tmp = explode(',', $bid);
				$a=0;
				for ($i=0; $i < count($tmp); $i++) { 

					$code = substr($tmp[$i], 0,2);
					$id = (int)substr($tmp[$i], 2);


					if ($code=='SC') {

						$tmp2 = $this->getfeedbackmobile2($id,1);
					}else{

						$tmp2 = $this->getfeedbackmobile($id,1);
					}

					// echo "<pre>";
					// print_r($tmp2);
					// echo "</pre>";
					unset($code);
					unset($id);
					// die;
					

					for ($j=0; $j < count($tmp2); $j++) { 
						
						
						// echo $tmp2[$j]['mMobile'];

						if ($tmp2[$j]['mMobile'] != '' ) {
							$_all[$a]=$tmp2[$j];

							$jsonArr['mobile'] = $_all[$a]['mMobile'];
							$jsonArr['code'] = $tmp[$i];
							$jsonArr['Time'] = date('Ymd');
							$url =  $this->getShortUrl('https://escrow.first1.com.tw/login/page-price1.php?v='.$this->enCrypt(json_encode($jsonArr)),$this->enCrypt(json_encode($jsonArr)),$ok);

							// $_all[$a]['smsTxt'] = '親愛的客戶您好:109年第2季之回饋金報表已結算完成,請點下列網址至第一建經官方網站確認報表,並依請款辦法作業,謝謝。新E化回饋金結算流程之操作手冊,請至第一建經官網下載。'.$url."";
							$_all[$a]['smsTxt'] = $arr.$url;


							$a++;
						}
						
						
						unset($json_Arr);
						
					}
					unset($tmp2);
				}

				

				//濾除重複簡訊對象並重新排序
				// $_all = $this->filter_array($_all) ;
				##

				


				
				
				$_total = count($_all);
				if ($ok == 'y') {
					// print_r($_all);
					// die;
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."<br>";
						
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$_all[$i]['smsTxt']."\r\n",$target,$_all[$i]["bBranch"],$tid,$sys) ; //回饋金特殊
								
								if ($sms_id == 's') {							//發送成功(s)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
									$_p ++ ;
								}
								else {											//發送失敗(f)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
								$_n ++ ;
							}
							##
						}
					}
					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##
				}
				else {
					//回覆簡訊發送結果
					return $_all ;
					##
				}
				break;
			case "保留款撥付":
				$_s = 0 ;		//簡訊成功次數
				$_p = 0 ;		//簡訊部分成功次數
				$_f = 0 ;		//簡訊失敗次數
				$_n = 0 ;		//簡訊號碼格式錯誤次數
				
				$this->execSQL = "select * from tBankTrans where tVR_Code='".$pid."' and tId=$tid";
				$getMD = $this->DB_link->prepare($this->execSQL);
				$getMD->execute();
				$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);
				//print_r($fetchValue);exit;
			
				$_m = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$_d = substr($fetchValue[0]["tBankLoansDate"],8,2);
		
				//
				$_data = $_data1 = $_data4 = $manager = array() ;
				
				$_data = $this->getsScrivenerMobile($pid,$sid); // 取得地政士發送簡訊對象

				if ($push_test == 1) {
					for ($i=0; $i < count($_data); $i++) { 
						$check = $this->checkScrivenerSms($_data[$i],$sid); //檢查寄送類型

						if ($check == 2) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							unset($_data[$i]);
						}elseif ($check == 3) {
							// $_data[$i]['push'] = 1;//sms
							$push[] = $_data[$i];
							
						}
						
					}
					sort($_data);
				}
				
				// print_r($push);

				$_data1 = $this->getsBranchMobile($pid,$bid,'店長'); // 取得店發送簡訊對象
				$_data4 = $this->getsBranchMobile($pid,$bid,'店東'); // 取得店發送簡訊對象

				$manager = array_merge($manager,$_data1,$_data4);
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				
				//增加第二組仲介簡訊發送
				$bid2 = $this->getSecBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid2) {
					$_data1 = $this->getsBranchMobile($pid,$bid2,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid2,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				
				// $_data = array_merge($_data, $_data1, $_data4) ;
				unset($_data1) ; unset($_data4) ;
				##
				
				//增加第三組仲介簡訊發送
				$bid3 = $this->getThrBranchMobile($pid) ;
				$_data1 = $_data4 = array() ;
				
				if ($bid3) {
					$_data1 = $this->getsBranchMobile($pid,$bid3,'店長'); // 取得店發送簡訊對象
					$_data4 = $this->getsBranchMobile($pid,$bid3,'店東'); // 取得店發送簡訊對象
					$manager = array_merge($manager,$_data1,$_data4);
				}
				
				// $_all = array_merge($_data, $_data1, $_data4) ;
				$_all = $_data;
				unset($_data) ; unset($_data1) ; unset($_data4) ;
				##				
			
				// 加入其他通訊資料 (買、賣方與買、賣方經紀人)
				$_start = count($_all) ;				//計算目前已存入之簡訊發送對象筆數
				$_t = 0 ;
				
				$bCount = 0 ;							//計算買方人數用
				//主買方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					//主買方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["b_mobile"]	;				//主買方手機
				$bCount ++ ;
				$_t ++ ;
				##
				//主買方其他電話
				$other_phone = $this->get_phone(1,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["b_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}

				unset($other_phone);
				##
				
				//其他買方
				$_other_owners = $this->get_others($pid,'1') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他買方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他買方手機
					$bCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//6買方代理人
				$_other_owners = $this->get_others($pid,'6') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//6買方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//6買方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
				
				//買方經紀人
				$other_phone = $this->get_phone(3,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '買方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				##

				$oCount = 0 ;							//計算賣方人數用
				//主賣方
				$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"] ; 					//賣方姓名
				$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_mobile"] ;				//賣方手機
				$oCount ++ ;
				$_t ++ ;
				##

				//主賣方其他電話
				$other_phone = $this->get_phone(2,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_name"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_t ++ ;
				}
				
				unset($other_phone);
				##
				
				//其他賣方$pid
				$_other_owners = $this->get_others($pid,'2') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//其他賣方姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//其他賣方手機
					$oCount ++ ;
					$_t ++ ;
				}
				unset($_other_owners);
				##

				//7賣方代理人
				$_other_owners = $this->get_others($pid,'7') ;
				//print_r($_other_owners) ;
				for ($i = 0 ; $i < count($_other_owners) ; $i ++) {
					$_all[$_start+$_t]["mName"] = $_other_owners[$i]['cName'] ;					//7賣方代理人姓名
					$_all[$_start+$_t]["mMobile"] = $_other_owners[$i]['cMobileNum'] ;			//7賣方代理人手機

					$_t ++ ;
				}
				unset($_other_owners);
				##
					
				//賣方經紀人
				$other_phone = $this->get_phone(4,$pid);

				for ($i=0; $i < count($other_phone); $i++) { 
					$_all[$_start+$_t]["mName"] = $other_phone[$i]["cName"]	; 					
					$_all[$_start+$_t]["mMobile"] = $other_phone[$i]['cMobileNum']	;
					$_all[$_start+$_t]["tTitle"] = '賣方經紀人';
					$_t ++ ;
				}

				unset($other_phone);
				// if ($_contract_data[0]["o_agent_mobile"] != "") {
				// 	$_all[$_start+$_t]["mName"] = $_contract_data[0]["o_agent_name"] ; 			//賣方經紀人(1)姓名
				// 	$_all[$_start+$_t]["mMobile"] = $_contract_data[0]["o_agent_mobile"] ;		//賣方經紀人(1)手機
				// 	$_t ++ ;
				// }
				
				// if ($_contract_data[0]["o_agent_mobile2"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name2"] ; 		//賣方經紀人(2)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile2"] ;	//賣方經紀人(2)手機
				// 	$_t++;	
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile3"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name3"] ;		//賣方經紀人(3)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile3"] ;	//賣方經紀人(3)手機
				// 	$_t++;
				// } 
			
				// if ($_contract_data[0]["o_agent_mobile4"] != "") {
				// 	$_all[$_start + $_t]["mName"] = $_contract_data[0]["o_agent_name4"] ;		//賣方經紀人(4)姓名
				// 	$_all[$_start + $_t]["mMobile"] = $_contract_data[0]["o_agent_mobile4"] ;	//賣方經紀人(3)手機
				// 	$_t++;	
				// } 
				##

				//print_r($_all);exit;
			
				//簡訊內容重整
				$buyer = $_contract_data[0]["b_name"];
				$seller = $_contract_data[0]["o_name"];
				$money = $fetchValue[0]["tMoney"];
				$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
				$M = preg_replace("/^0/","",$M) ;
				$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
				$D = preg_replace("/^0/","",$D) ;
				
				//是否多人買方
				$bCount = $this->getOhterBuyerOwner($bCount) ;
				##
				
				//是否多人賣方
				$oCount = $this->getOhterBuyerOwner($oCount) ;
				##
				
				
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount."賣方".$seller.$oCount."已完成保留款撥付作業,應收款項已於".$M.'/'.$D."匯入指定帳戶";

				echo $sms_txt;
				
				//濾除重複簡訊對象並重新排序
				$_all = $this->filter_array($_all,$manager) ;

				//不寄送的濾掉
				$noSend = $this->checkSend($pid,$target,$fetchValue[0]['tExport_nu'],$bid,$sid);
				$manager = $this->filter_array($manager,$noSend);
				$_all = $this->filter_array($_all,$noSend) ;
				##
					
				$_total = count($_all) ;
				if ($ok == 'y') {
					for ($i = 0 ; $i < $_total ; $i ++) {
						//echo $_all[$i]["mMobile"]."\n";
					
						if (trim($_all[$i]["mMobile"]) != "") {
							$check_word = array("-","%","_") ;									//分隔字元
							$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									
									$_p ++ ;
								}
								else {											//發送失敗(f)
									
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								
								$_n ++ ;
							}
							##
						}
					}

					//////推撥/////
					for ($i=0; $i < count($push); $i++) { 
						# code...
						$this->sendScrivenerPush($push[$i],$ok,$sid,$sms_txt,substr($pid,5,9),$target,$tid);
					}
					////////

					$tmp = substr($pid,5,9);
					$addr = $this->getProperty($tmp);
					$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCount."賣方".$seller.$oCount.";".$addr.",已完成保留款撥付作業,應收款項已於".$M.'/'.$D."匯入指定帳戶";

					$_boss = $this->sendBossSms($manager,$sms_txt,$target,$pid,$tid,$sys); //******
					unset($tmp);

					//回覆簡訊發送結果
					return $this->out_sms_code($_s,$_p,$_f,$_n) ;
					##				
				}else {
					//回覆簡訊發送結果
					if (count($manager) > 0) return array_merge($_all,$manager) ;
					else {return $_all ;}
					##
				}
			break ;
			
		}
	}

	//有自定寄送對象
	public function send2($pid,$target,$tId,$nu,$ok="n",$realty=0,$storeId=0){

		$_s = 0 ;		//簡訊成功次數
		$_p = 0 ;		//簡訊部分成功次數
		$_f = 0 ;		//簡訊失敗次數
		$_n = 0 ;		//簡訊號碼格式錯誤次數
		$sys = $this->getSmsSystem() ;//發送簡訊的系統
		$_all = $manager = array();
		
		// if ($storeId != 0 && $target =='仲介服務費') {
		// 	$str = "AND bStoreId = '".$storeId."'";
		// }
		//出款內容
		if ($target =='仲介服務費') {
			$this->execSQL = "SELECT * FROM tBankTrans WHERE tVR_Code='".$pid."' AND tObjKind='".$target."' AND tExport_nu='".$nu."' AND tStoreId = '".$storeId."'";
		}else{
			$this->execSQL = "SELECT tBankLoansDate,SUM(tMoney) tMoney,tId FROM tBankTrans WHERE tVR_Code='".$pid."' AND tExport_nu='".$nu."' AND tObjKind='".$target."'" ;
		
		}
		$getMD = $this->DB_link->prepare($this->execSQL);
		$getMD->execute();
		$fetchValue = $getMD->fetchALL(PDO::FETCH_ASSOC);

		
		//簡訊對象
		$this->execSQL = "SELECT bName AS mName,bMobile AS mMobile,bIden,bStoreId FROM tBankTranSms WHERE FIND_IN_SET (".$fetchValue[0]['tId'].",bBankTranId) AND bVR_Code='".$pid."' AND bObjKind = '".$target."' AND bDel = 0 ".$str;
		// echo $this->execSQL."\r\n";


		$getMD = $this->DB_link->prepare($this->execSQL);
		$getMD->execute();
		$BankTranSms = $getMD->fetchALL(PDO::FETCH_ASSOC);

		for ($i=0; $i < count($BankTranSms); $i++) { 
			if ($BankTranSms[$i]['bStoreId'] > 0) {
				if ($BankTranSms[$i]['bIden'] == '店東' || $BankTranSms[$i]['bIden'] == '店長') {
					$BankTranSms[$i]['boss'] = 1;
				}

				if ($target =='仲介服務費') {
					if ($BankTranSms[$i]['bStoreId'] == $storeId) {
						array_push($manager, $BankTranSms[$i]);
					}
				}else{
					array_push($manager, $BankTranSms[$i]);
				}
				
				

			}else{
				array_push($_all, $BankTranSms[$i]);
				// echo 'push';
			}
		}
		
		// print_r($manager);
		

		//案件內容
		$_contract_data = $this->getContractData($pid);
		//計算買方人數
		$bCount = 1 ; 
		$_other = $this->get_others($pid,'1') ;
		$bCount += count($_other);
		unset($_other);
		//計算賣方人數
		$oCount = 1;
		$_other = $this->get_others($pid,'2') ;
		$oCount += count($_other);

		//簡訊資訊
		$buyer = $_contract_data[0]["b_name"];
		$seller = $_contract_data[0]["o_name"];
		$money = $fetchValue[0]["tMoney"];
		$_buyer_money = $fetchValue[0]["tBuyer"]; // 買方服務費
		$_seller_money = $fetchValue[0]["tSeller"]; //賣方服務費
			

		$M = substr($fetchValue[0]["tBankLoansDate"],5,2);
		$M = preg_replace("/^0/","",$M) ;
		$D = substr($fetchValue[0]["tBankLoansDate"],8,2);
		$D = preg_replace("/^0/","",$D) ;

		$bCountTxt = $this->getOhterBuyerOwner($bCount) ;//是否多人買方等?人		
		$oCountTxt = $this->getOhterBuyerOwner($oCount) ;//是否多人賣方等?人
		$addr = $this->getProperty(substr($pid,-9)); //地址

		// echo $addr;
		// die;
		##
		//一般對象簡訊
		$sms_txt = "";
		//仲介簡訊
		$sms_txt_b="";

		//TEST
		// $target = '扣繳稅款';

		switch ($target) {
			case '扣繳稅款':
				//一般對象簡訊
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt."之稅款已於".$M.'/'.$D."存入地政士指定帳戶" ; 
				//仲介簡訊
				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt.";".$addr."之稅款已於".$M.'/'.$D."存入地政士指定帳戶" ; 

				
				break;
			case '點交(結案)':
				//一般對象簡訊
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt."已完成點交作業,各應收款項已於".$M.'/'.$D."匯入指定帳戶" ;

				//仲介簡訊
				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt.";".$addr.",已完成點交作業,各應收款項已於".$M.'/'.$D."匯入指定帳戶" ;

				
				//服務費另外發送
				if ($realty > 0) {
					$bb = $this->sendCaseEndRealty2($pid,$nu,'n',$target,$tid,$sys,($buyer.$bCountTxt),($seller.$oCountTxt),$addr,$M."月".$D."日",$manager);
					//發過服務費就不用再發點交簡訊	
					// print_r($manager);

					$manager = $this->filter_array($manager,$bb);
					
				}



				break;
			case '解除契約':
				//一般對象簡訊
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt."已完成解除契約作業,應收款項已於".$M.'/'.$D."匯入指定帳戶" ;
				//仲介簡訊
				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt.",賣方".$seller.$oCountTxt.";".$addr.",已完成解除契約作業,應收款項已於".$M.'/'.$D."匯入指定帳戶" ;

				$_all = $this->filter_array($_all);
				break;
			case '仲介服務費':
				$addr = $this->getProperty(substr($pid,-9),'all');
				if ($_buyer_money > 0) {
					$_money = "買方服務費".$money;
				}elseif ($_seller_money > 0) {
					$_money = "賣方服務費".$money;
				}

				$sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：買方'.$buyer.$bCountTxt.',賣方'.$seller.$oCountTxt.';'.$addr.','.$_money.'元已於'.$date.'匯入仲介指定帳戶';
				break;
			case '代清償':
				$_seller_txt = '賣方'.$_contract_data[0]["o_name"].$oCountTxt ;
				
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：".$_seller_txt.";代償金額新台幣".$money."元已於".$M.'/'.$D."匯入指定帳戶" ;
				
				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：".$_seller_txt.";".$addr."代償金額新台幣".$money."元已於".$M.'/'.$D."匯入指定帳戶" ;
					
				break;
			case '賣方先動撥':
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCountTxt."動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
				
				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）賣方".$seller.$oCountTxt.";".$addr.",動用買賣價金新台幣".$money."元已於".$M.'/'.$D."存入賣方指定帳戶" ;
				break;
			case '保留款撥付':
				$sms_txt = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt."賣方".$seller.$oCountTxt."已完成保留款撥付作業,應收款項已於".$M.'/'.$D."匯入指定帳戶";

				$sms_txt_b = "第一建經信託履約保證專戶通知（保證號碼：".substr($pid,5,9)."）：買方".$buyer.$bCountTxt."賣方".$seller.$oCountTxt.";".$addr.",已完成保留款撥付作業,應收款項已於".$M.'/'.$D."匯入指定帳戶";

				break;
			default:
				return false;
				break;
		}

		$_all = $this->filter_array($_all);

		
		// print_r($manager);
	
		if ($ok == 'y') {
			$_total = count($_all);
			//非仲介對象簡訊發送
			for ($i = 0 ; $i < $_total ; $i ++) {
					
				if (trim($_all[$i]["mMobile"]) != "") {
						$check_word = array("-","%","_") ;									//分隔字元
						$mobile_tel = str_replace($check_word,"",$_all[$i]["mMobile"]) ;	//濾除分隔字元
							
							//開始發送簡訊
							if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
								$sms_id = $this->sms_send($mobile_tel,$_all[$i]["mName"],$sms_txt,$target,$pid,$tid,$sys) ;
								
								if ($sms_id == 's') {							//發送成功(s)
									$_s ++ ;
								}
								else if ($sms_id == 'p') {						//部份成功(p)
									$_p ++ ;
								}
								else {											//發送失敗(f)
									$_f ++ ;
								}
							}
							else {												//門號錯誤(n)
								$_n ++ ;
							}
							##
				}
			}
			if ($realty > 0 && $target == '點交(結案)')  {//服務費 另外發送
				$this->sendCaseEndRealty2($pid,$nu,$ok,$target,$tid,$sys,($buyer.$bCountTxt),($seller.$oCountTxt),$addr,$M."月".$D."日",$BankTranSms);
			}
			
			if ($sms_txt_b) {
				$_boss = $this->sendBossSms($manager,$sms_txt_b,$target,$pid,$tid,$sys); //******
			}
			
					
			
			unset($tmp);

			//回覆簡訊發送結果
			return $this->out_sms_code($_s,$_p,$_f,$_n) ;
			##				
		}else {
			$BankTranSms = array();
			$BankTranSms = array_merge($BankTranSms,$manager,$_all);
			// $BankTranSms = $this->filter_array($BankTranSms);
			$BankTranSms['sms_txt'] = $sms_txt;
			if ($sms_txt_b) {
				$BankTranSms['sms_txt_b'] = $sms_txt_b;
			}
			// echo $sms_txt;
			// echo $sms_txt_b;
			return $BankTranSms ;
					##
		}

	}
	
	private function sendCaseEndRealty2($pid,$mediaCode,$ok,$target,$tid,$sys,$buyer,$seller,$addr,$date,$manager){

		$CertifiedId = substr($pid,5,9);
		$addr = $this->getProperty($CertifiedId,'all');

		$sql = 'SELECT
					tr.tMoney,
					tr.tStoreId
				FROM
					tBankTrans AS tr
				WHERE
					tObjKind="'.$target.'" AND tKind="仲介" AND tVR_Code="'.$pid.'" AND tExport_nu="'.$mediaCode.'";' ;


        $getMD = $this->DB_link->prepare($sql);
		$getMD->execute();
		$tmp2 = $getMD->fetchALL(PDO::FETCH_ASSOC);

		// print_r($BankTranSms);

		$_data = $store = array();
		//簡訊對象
		for ($i=0; $i < count($manager); $i++) { 
			if ($manager[$i]['bStoreId'] > 0) {
				// $arr[$manager[$i]['bStoreId']]['data'] = $manager[$i];
				$_data[$manager[$i]['bStoreId']][] = $manager[$i];

				array_push($store,$manager[$i]);

			}
		}

		
		for ($i=0; $i < count($tmp2); $i++) {
			$arr[$tmp2[$i]['tStoreId']]['money'] += $tmp2[$i]['tMoney'];
			$arr[$tmp2[$i]['tStoreId']]['data'] = $_data[$tmp2[$i]['tStoreId']];

			
		}

		if (is_array($arr)) {
			foreach ($arr as $key => $value) {
			
				$tmp = $this->filter_array($value['data']) ;
				unset($value['data']);
				$value['data'] = $tmp;
				$realty_sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：買方'.$buyer.',賣方'.$seller.';'.$addr.',已完成點交作業服務費'.$value['money'].'元已於'.$date.'匯入仲介指定帳戶';
					
				if ($ok == 'y') {
					for ($i=0; $i < count($tmp); $i++) { 
						if (trim($tmp[$i]["mMobile"]) != "") {
								$check_word = array("-","%","_") ;									//分隔字元
								$mobile_tel = str_replace($check_word,"",$tmp[$i]["mMobile"]) ;			//濾除分隔字元
								
								//開始發送簡訊
								if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
									$sms_id = $this->sms_send($mobile_tel,$tmp[$i]["mName"],$realty_sms_txt,$target,$pid,$tid,$sys) ;
									
									if ($sms_id == 's') {							//發送成功(s)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
										$_s ++ ;
									}
									else if ($sms_id == 'p') {						//部份成功(p)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
										$_p ++ ;
									}
									else {											//發送失敗(f)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
										$_f ++ ;
									}
								}
								else {												//門號錯誤(n)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
									$_n ++ ;
								}
						}
					}
				}else{
					for ($i=0; $i < count($tmp); $i++) { 
						$check_word = array("-","%","_") ;									//分隔字元
						$mobile_tel = str_replace($check_word,"",$tmp[$i]["mMobile"]) ;			//濾除分隔字元

						// echo "\r\n".$mobile_tel."_".$tmp[$i]["mName"]."_".$realty_sms_txt."\r\n";
					}
				}
				
			}
			$store = $this->filter_array($store) ;
		
		
			return $store;
		}else{
			return false;
		}

		
	}

	private function sendCaseEndRealty($pid,$mediaCode,$ok,$noSend,$target,$tid,$sys,$buyer,$seller,$addr,$date){
		//找到店的服務費總金額 (可能有N個店，要分開寄)
		$sql = 'SELECT
					tr.tMoney,
					tr.tStoreId
				FROM
					tBankTrans AS tr
				WHERE
					tObjKind="'.$target.'" AND tKind="仲介" AND tVR_Code="'.$pid.'" AND tExport_nu="'.$mediaCode.'";' ;
		
		
        $getMD = $this->DB_link->prepare($sql);
		$getMD->execute();
		$tmp2 = $getMD->fetchALL(PDO::FETCH_ASSOC);

		$store = array();
		
		for ($i=0; $i < count($tmp2); $i++) { 
			$_data = $_data1 = $_data1 = $_data2 = $_data3 = $_data4 =  array();
			$arr[$tmp2[$i]['tStoreId']]['money'] += $tmp2[$i]['tMoney'];
			
			$_data1 = $this->getsBranchMobile($pid,$tmp2[$i]['tStoreId'],'店長'); // 取得店發送簡訊對象			
			$_data4 = $this->getsBranchMobile($pid,$tmp2[$i]['tStoreId'],'店東'); // 取得店發送簡訊對象

			$_data2 = $this->getsBranchMobile($pid,$tmp2[$i]['tStoreId'],'會計'); // 取得店發送簡訊對象
			$_data3 = $this->getsBranchMobile($pid,$tmp2[$i]['tStoreId'],'秘書'); // 取得店發送簡訊對象
			$_data = array_merge($_data, $_data1, $_data2, $_data3, $_data4) ;
			unset($_data1) ; unset($_data2) ; unset($_data3) ; unset($_data4) ;

			$arr[$tmp2[$i]['tStoreId']]['data'] = $_data;

			

			if (is_array($_data)) {
				$store = array_merge($store,$_data);

			}
			

			
		}

		// print_r($store);

		// die;
		// echo "<pre>";
		// print_r($arr);
		// echo "</pre>";
		//SELECT `tMemo`,COUNT(`tVR_Code`)  FROM `tBankTrans` WHERE `tObjKind` ='點交(結案)' AND `tKind` ='仲介' AND `tStoreId` > 0 GROUP BY `tVR_Code` ORDER BY COUNT(`tVR_Code`) DESC

		// $_all = $this->filter_array($_all,$manager) ;
		// 			$manager = $this->filter_array($manager,$noSend);
		$tmp = substr($pid,5,9);
		$addr = $this->getProperty($tmp,'all');
			
		if (is_array($arr)) {
			foreach ($arr as $key => $value) {
			
				$tmp = $this->filter_array($value['data'],$noSend) ;
				unset($value['data']);
				$value['data'] = $tmp;
				$realty_sms_txt = '第一建經信託履約保證專戶通知（保證號碼：'.substr($pid,5,9).'）：買方'.$buyer.',賣方'.$seller.';'.$addr.',已完成點交作業服務費'.$value['money'].'元已於'.$date.'匯入仲介指定帳戶';
					
				if ($ok == 'y') {
					for ($i=0; $i < count($tmp); $i++) { 
						if (trim($tmp[$i]["mMobile"]) != "") {
								$check_word = array("-","%","_") ;									//分隔字元
								$mobile_tel = str_replace($check_word,"",$tmp[$i]["mMobile"]) ;			//濾除分隔字元
								
								//開始發送簡訊
								if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
									$sms_id = $this->sms_send($mobile_tel,$tmp[$i]["mName"],$realty_sms_txt,$target,$pid,$tid,$sys) ;
									
									if ($sms_id == 's') {							//發送成功(s)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('s') ;
										$_s ++ ;
									}
									else if ($sms_id == 'p') {						//部份成功(p)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('p') ;
										$_p ++ ;
									}
									else {											//發送失敗(f)
										//$_all[$i]['mMobile'] .= '、'.$this->return_code('f') ;
										$_f ++ ;
									}
								}
								else {												//門號錯誤(n)
									//$_all[$i]['mMobile'] .= '、'.$this->return_code('n') ;
									$_n ++ ;
								}
						}
					}
				}else{
					for ($i=0; $i < count($tmp); $i++) { 
						$check_word = array("-","%","_") ;									//分隔字元
						$mobile_tel = str_replace($check_word,"",$tmp[$i]["mMobile"]) ;			//濾除分隔字元

						// echo "<br>".$mobile_tel."_".$tmp[$i]["mName"]."_".$realty_sms_txt."<br>";
					}
				}
				
			}
			$store = $this->filter_array($store) ;
		
		
			return $store;
		}else{
			return false;
		}
		
	}

	private function getChangeMoney($id,$pid){
		 //取得對應調帳之出款檔金額
        $sql = 'SELECT tMoney FROM tBankTrans WHERE tChangeExpense='.$id ; //AND tVR_Code="'.$pid.'"

        $getMD = $this->DB_link->prepare($sql);
		$getMD->execute();
		$tmp2 = $getMD->fetch(PDO::FETCH_ASSOC);

		
		return $tmp2['tMoney'];
	}

	private function checkScrivenerSms($data,$sId){

		$tmp = 'SC'.str_pad($sId,'4','0',STR_PAD_LEFT);

		$sql = "SELECT
					aSmsOption
				FROM
					tAppAccount
				WHERE
					aParentPhone ='".$data['mMobile']."'
					AND aParentId = '".$tmp."'
					AND aIdentity = 1 LIMIT 1" ;
		// echo $sql;
		$getMD = $this->DB_link->prepare($sql);
		$getMD->execute();
		$tmp2 = $getMD->fetch(PDO::FETCH_ASSOC);

	
		return $tmp2['aSmsOption'];
	}

	private function sendScrivenerPush($data,$n,$sId,$msg,$cId,$target,$tId){
		// $n = 'ok';/,substr($pid,5,9),$target,$tid

		// $n = 'ok';
		$tmp = 'SC'.str_pad($sId,'4','0',STR_PAD_LEFT);

		
			$sql = "SELECT
					aId,
					aName,
					aPushToken
				FROM
					tAppAccount
				WHERE
					aParentPhone ='".$data['mMobile']."'
					AND aParentId = '".$tmp."'
					AND aStatus = 1
					AND aIdentity = 1" ;
			// echo $sql;

			$getMD = $this->DB_link->prepare($sql);
			$getMD->execute();
			$tmp2 = $getMD->fetchALL(PDO::FETCH_ASSOC);
			// print_r($tmp2);
			if ($n == 'y') {
				for ($i=0; $i < count($tmp2); $i++) { 
					
					if ($tmp2[$i]['aPushToken']) {
							// echo $tmp2[$i]['aPushToken']."<br>";
							pushMsg($tmp2[$i]['aPushToken'], $msg, '2', '第一建經通知');
							$this->pushLog($cId,$target,$msg,$data['mMobile'],$tmp2[$i]['aName'],$tId,$tmp2[$i]['aId']);
					}
					
					
				}
			}
			
		
	}

	private function pushLog($cId,$target,$msg,$mobile,$name,$tId,$aId){
		//$_SESSION['member_name']

		$sql = "INSERT INTO
					tSMS_Push_Log
					(
						tPID,
						tKind,
						tSMS,
						tTo,
						tName,
						tTransId,
						tAid,
						tSendName
					) VALUES(
						'".$cId."',
						'".$target."',
						'".$msg."',
						'".$mobile."',
						'".$name."',
						'".$tId."',
						'".$aId."',
						'".$_SESSION['member_name']."'
	
					)";
		// echo $sql."<bR>";
		$getMD = $this->DB_link->prepare($sql);
		$getMD->execute();
			
	}

	private function testLog($cId,$target,$msg){
		// $fw = fopen('/home/httpd/html/first2.twhg.com.tw/sms/log2/'.date('Y-m-d').'.log', 'a+');

		$url = dirname(__FILE__).'/log2/'.date('Y-m-d').".log";
		
		$fw = fopen($url, 'a+');
		fwrite($fw,$msg."\r\n");

		fclose($fw);


	}
	
  //
	private function checkSend($pid,$tg,$ex,$bid,$sid){
	
	  	$cCertifyId = substr($pid,5,9) ;
	  	// substr($rs->fields["tBankCode"],0,3)
	  	//仲介  地政士  買方 賣方 不寄送
	  	$sql = "SELECT tId,tKind,SUBSTR(tBankCode,1,3) AS BankMain,SUBSTR(tBankCode,4) AS BankBranch,tAccount,tAccountName FROM tBankTrans WHERE tVR_Code = '".$pid."' AND tObjKind='".$tg."' AND tExport_nu ='".$ex."' AND tSend = 1";
	  	
	  	$rs = $this->DB_link->prepare($sql);
		$rs->execute();
		$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
		// print_r($tmp);
		foreach ($tmp as $k => $v) {
			
			switch ($v['tKind']) {
				case '地政士':
						$s = $this->getNoSend('tScrivener',$cCertifyId,$v,$sid);
					break;
				case '仲介':
						$s = $this->getNoSend('tBranch',$cCertifyId,$v,$bid);
						$list = array_merge($s);
						unset($s);

						if ($this->getSecBranchMobile($pid) > 0) {
							$s = $this->getNoSend('tBranch',$cCertifiedId,$v,$this->getSecBranchMobile($pid));
							$list = array_merge($list,$s);
							unset($s);
						}
						
						if ($this->getThrBranchMobile($pid) > 0) {
							$s = $this->getNoSend('tBranch',$cCertifiedId,$v,$this->getThrBranchMobile($pid));
							$list = array_merge($list,$s);
							unset($s);
						}
					
					break;
				
				case '買方':
						$s = $this->getNoSend('tContractBuyer',$cCertifyId,$v,'');
						$s1 = $this->getNoSend('tContractOthers',$cCertifyId,$v,1);
						$s2 = $this->getNoSend('tContractCustomerBank',$cCertifyId,$v,1);
						$s3 = $this->getNoSend('tContractCustomerBank',$cCertifyId,$v,53);
						$list = array_merge($s,$s1,$s2,$s3);
						unset($s);unset($s1);unset($s2);unset($s3);
					break;
				case '賣方':
						$s = $this->getNoSend('tContractOwner',$cCertifyId,$v,'');
						$s1 = $this->getNoSend('tContractOthers',$cCertifyId,$v,2);
						$s2 = $this->getNoSend('tContractCustomerBank',$cCertifyId,$v,2);
						$s3 = $this->getNoSend('tContractCustomerBank',$cCertifyId,$v,52);
						$list = array_merge($s,$s1,$s2,$s3);
						unset($s);unset($s1);unset($s2);unset($s3);
					break;
				
			}
		}


		// print_r($list);
		// die;

		return $list;
	}
  	private function getNoSend($type,$cid,$arr,$iden)
  	{
	  	$list = array();

	  	if ($type == 'tContractOwner') {//比對銀行外再多比對人名※他們有時會在出款才增加帳戶
	  		$sql = 'SELECT cName AS mName,cMobileNum AS mMobile FROM tContractOwner WHERE cCertifiedId ="'.$cid.'" AND ((cBankKey2="'.$arr['BankMain'].'" AND cBankBranch2 ="'.$arr['BankBranch'].'" AND cBankAccNumber ="'.$arr['tAccount'].'" AND cBankAccName ="'.$arr['tAccountName'].'") OR (cName ="'.$arr['tAccountName'].'")) ';
	  		$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			
			$list = $tmp;

			$sql= "SELECT cMobileNum AS mMobile FROM tContractPhone WHERE cCertifiedId ='".$cid."' AND cIdentity = 2";

			$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);

			for ($i=0; $i < count($tmp); $i++) { 
				$list[($i+1)]['mName'] = $list[0]['mName'];
				$list[($i+1)]['mMobile'] = $tmp[$i]['mMobile'];
			}

		}elseif ($type == 'tContractOthers') { //比對銀行外再多比對人名※他們有時會在出款才增加帳戶
	  		$sql = 'SELECT cName AS mName,cMobileNum AS mMobile FROM tContractOthers WHERE cCertifiedId ="'.$cid.'" AND cIdentity = "'.$iden.'" AND ((cBankMain="'.$arr['BankMain'].'" AND cBankBranch ="'.$arr['BankBranch'].'" AND cBankAccName ="'.$arr['tAccount'].'" AND cBankAccNum ="'.$arr['tAccountName'].'") OR (cName ="'.$arr['tAccountName'].'")) ';
	  		$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			
			foreach ($tmp as $k => $v) {
				$list[] = $v;
			}
	  	}elseif ($type == 'tContractCustomerBank') {
	  		if ($iden == 1) {
	  			$sql = 'SELECT 
	  						(SELECT cName FROM tContractBuyer AS cb WHERE cb.cCertifiedId = cCertifiedId) AS mName,
	  						(SELECT cMobileNum FROM tContractBuyer AS cb WHERE cb.cCertifiedId = cCertifiedId) AS mMobile
	  					FROM
	  						tContractCustomerBank 
	  					WHERE
	  						cIdentity ="'.$iden.'" AND cCertifiedId ="'.$cid.'" AND cBankMain="'.$arr['BankMain'].'" AND cBankBranch ="'.$arr['BankBranch'].'" AND cBankAccountNo ="'.$arr['tAccount'].'" AND cBankAccountName ="'.$arr['tAccountName'].'"';
	  		
	  		}elseif ($iden == 2) {
	  			$sql = 'SELECT 
	  						(SELECT cName FROM tContractOwner AS cb WHERE cb.cCertifiedId = cCertifiedId) AS mName,
	  						(SELECT cMobileNum FROM tContractOwner AS cb WHERE cb.cCertifiedId = cCertifiedId) AS mMobile
	  					FROM
	  						tContractCustomerBank 
	  					WHERE
	  						cIdentity ="'.$iden.'" AND cCertifiedId ="'.$cid.'" AND cBankMain="'.$arr['BankMain'].'" AND cBankBranch ="'.$arr['BankBranch'].'" AND cBankAccountNo ="'.$arr['tAccount'].'" AND cBankAccountName ="'.$arr['tAccountName'].'"';
	  		
	  		}elseif ($iden == 52 || $iden == 53) {
	  			$sql = 'SELECT 
	  						(SELECT cName FROM tContractOthers AS cb WHERE cb.cId = cOtherId) AS mName,
	  						(SELECT cMobileNum FROM tContractOthers AS cb WHERE cb.cId = cOtherId) AS mMobile
	  					FROM
	  						tContractCustomerBank 
	  					WHERE
	  						cIdentity ="'.$iden.'" AND cCertifiedId ="'.$cid.'" AND cBankMain="'.$arr['BankMain'].'" AND cBankBranch ="'.$arr['BankBranch'].'" AND cBankAccountNo ="'.$arr['tAccount'].'" AND cBankAccountName ="'.$arr['tAccountName'].'"';
	  		
	  		
	  		}
	  		$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			//比對銀行外再多比對人名※他們有時會在出款才增加帳戶
			foreach ($tmp as $key => $value) { 
				if ($value['mName'] == $arr['tAccountName']) {
					$ck = 1;
				}
			}

			if ($ck != '1') {
				$list = $tmp;  
			}
			
	  	
	  	}elseif ($type == 'tContractBuyer') {//比對銀行外再多比對人名※他們有時會在出款才增加帳戶
	  		$sql = 'SELECT cName AS mName,cMobileNum AS mMobile FROM tContractBuyer WHERE cCertifiedId ="'.$cid.'" AND ((cBankKey2="'.$arr['BankMain'].'" AND cBankBranch2 ="'.$arr['BankBranch'].'" AND cBankAccNumber ="'.$arr['tAccount'].'" AND cBankAccName ="'.$arr['tAccountName'].'") OR (cName ="'.$arr['tAccountName'].'"))';
	  		// echo $sql;
	  		$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			
			$list = $tmp;
			unset($tmp);
			// print_r($list);

			$sql= "SELECT cMobileNum AS mMobile FROM tContractPhone WHERE cCertifiedId ='".$cid."' AND cIdentity = 1";

			$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);

			for ($i=0; $i < count($tmp); $i++) { 
				$list[($i+1)]['mName'] = $list[0]['mName'];
				$list[($i+1)]['mMobile'] = $tmp[$i]['mMobile'];
			}

			// print_r($list);

		}elseif ($type == 'tBranch') {
			$sql_s = '(b.bAccountUnused = 0 AND bAccountNum1 ="'.$arr['BankMain'].'" AND bAccountNum2 = "'.$arr['BankBranch'].'" AND bAccount3 ="'.$arr['tAccount'].'" AND bAccount4 = "'.$arr['tAccountName'].'")';
			$sql_s .= ' OR (b.bAccountUnused1 = 0 AND bAccountNum11 ="'.$arr['BankMain'].'" AND bAccountNum21 = "'.$arr['BankBranch'].'" AND bAccount31 ="'.$arr['tAccount'].'" AND bAccount41 = "'.$arr['tAccountName'].'")';
			$sql_s .= ' OR (b.bAccountUnused2 = 0 AND bAccountNum12 ="'.$arr['BankMain'].'" AND bAccountNum22 = "'.$arr['BankBranch'].'" AND bAccount32 ="'.$arr['tAccount'].'" AND bAccount42 = "'.$arr['tAccountName'].'")';
			
			$sql = "SELECT b.bId,bs.bName AS mName,bs.bMobile AS mMobile FROM tBranch AS b LEFT JOIN tBranchSms AS bs ON bs.bBranch = b.bId WHERE bs.bDel = 0 AND b.bId = '".$iden."' AND (".$sql_s.")";

			$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			
			$list = $tmp;
			
		}elseif ($type == 'tScrivener') {
			$sql = "SELECT ss.sName,ss.sMobile FROM tScrivener AS s LEFT JOIN tScrivenerSms AS ss ON ss.sScrivener = s.sId WHERE s.sId ='".$iden."' AND ss.sDel = 0 AND ss.sLock = 0";
			
			$rs = $this->DB_link->prepare($sql);
			$rs->execute();
			$tmp = $rs->fetchALL(PDO::FETCH_ASSOC);
			
			$list = $tmp;
		}

	  	return $list;
	}
  	private function writeLog($k,$smsTxt,$aryData,$pid,$tid,$_apol_id,$_tel=''){

		// $fs = '/home/httpd/html/first.twhg.com.tw/sms/log/'.date("Ymd").".log";
		$fs = $this->log_path.date("Ymd").".log";
	  	$fp = fopen($fs, 'a+');
		fwrite($fp, "============[".date("Y-m-d H:i:s")."]===========================\n");
		fwrite($fp, "TARGET:".$k."\n");
		fwrite($fp, "SMS:".$smsTxt."\n");
		fwrite($fp, "DATA:".$_tel."\n");
		$_total = count($aryData);
		
		for ($i = 0 ; $i <$_total ; $i ++) {
			$_tmp = $aryData[$i]["mMobile"] ;
			if (preg_match("/$_tmp/",$_tel)) {
				$mobile = $aryData[$i]["mMobile"] ;
				$mobile_name = $aryData[$i]["mName"] ;
				fwrite($fp,'Mobile:'.$aryData[$i]["mName"]."/".$aryData[$i]["mMobile"]."\n");
				break ;
			}
			unset($_tmp) ;
		}
		
		//$_new_str = implode(",", $mobile);
		//$_new_str2 = implode(",", $mobile_name);
		$_new_str = $_tel;
		$_new_str2 = $mobile_name;

		$sql ="INSERT INTO `tSMS_Log` (`tPID`, `tKind`, `tSMS`, `tTo`,`tName`,tTransId,tTID ) VALUES ('".substr($pid,5,9)."', '$k', '$smsTxt', '$_new_str','$_new_str2','$tid','$_apol_id')";
		fwrite($fp,$sql."\n") ;
		
		fwrite($fp, "=============================================================\n");
		fclose($fp);
		//

		$this->DB_link->exec($sql);
		//
  }
	
	//新增簡訊 Log 資料至資料表
	private function writeDB($target,$smsTxt,$pid,$tid,$msg_id,$_tel='',$mobile_name='') {
		$smsTxt = preg_replace("/\'+/","",$smsTxt) ;
		$smsTxt = preg_replace("/\"+/","",$smsTxt) ;

		$sql = 'INSERT INTO tSMS_Log (tPID,tKind,tSMS,tTo,tName,tTransId,tTID) VALUES ("'.substr($pid,5,9).'","'.$target.'","'.$smsTxt.'","'.$_tel.'","'.$mobile_name.'","'.$tid.'","'.$msg_id.'") ;' ;


		//echo "writeDB SQL=".$sql."<br>\n" ;
		$this->DB_link->exec($sql) ;
	}
	##
  
	//亞太電信版 sms_log 寫入
	private function writeLog_apol($k,$smsTxt,$pid,$tid,$_apol_id,$_tel='',$mobile_name=''){
		//echo "PID=".$pid ; exit ;
		$fs = $this->log_path . date("Ymd").'.log' ;
		$fp = fopen($fs, 'a+');
		
		fwrite($fp,'============['.date("Y-m-d H:i:s")."]===========================\n") ;
		fwrite($fp,'TARGET:'.$k."\n") ;
		fwrite($fp,'SMS:'.$smsTxt."\n") ;
		fwrite($fp,'Mobile:'.$_tel."\n") ;
		fwrite($fp,'DATA:'.$mobile_name.'/'.$_tel."\n");		
		fwrite($fp,"=============================================================\n") ;

		fclose($fp);
		//
	}
	##
	
	//中華電信版 sms_log 寫入
	private function writeLog_cht($target,$smsTxt,$pid,$tid,$msg_id,$_tel='',$mobile_name='') {
		//$fs = '/home/httpd/html/first.twhg.com.tw/sms/log/cht_'.date("Ymd").".log" ;
		//$fs = '/home/httpd/html/first2.twhg.com.tw/sms/log/cht_'.date("Ymd").".log" ;
		$fs = $this->log_path.'cht_'.date("Ymd").'.log' ;
	  	$fp = fopen($fs,'a+') ;
		
		fwrite($fp,'============['.date("Y-m-d H:i:s")."]===========================\n") ;
		fwrite($fp,'TARGET:'.$target."\n") ;
		fwrite($fp,'SMS:'.$smsTxt."\n") ;
		fwrite($fp,'Mobile:'.$_tel."\n") ;
		fwrite($fp,'DATA:'.$mobile_name.'/'.$_tel."\n");
		fwrite($fp,"=============================================================\n") ;
		
		fclose($fp) ;
		//
  }
  ##
	
	//遠傳電信版 sms_log 寫入
	private function writeLog_fet($target,$smsTxt,$pid,$tid,$msg_id,$_tel='',$mobile_name='') {
		$fs = $this->log_path.'fet_'.date("Ymd").'.log' ;
	  	$fp = fopen($fs,'a+') ;
		
		fwrite($fp,'============['.date("Y-m-d H:i:s")."]===========================\n") ;
		fwrite($fp,'TARGET:'.$target."\n") ;
		fwrite($fp,'SMS:'.$smsTxt."\n") ;
		fwrite($fp,'Mobile:'.$_tel."\n") ;
		fwrite($fp,'DATA:'.$mobile_name.'/'.$_tel."\n");
		fwrite($fp,"=============================================================\n") ;
		
		fclose($fp) ;
		//
  }
  ##

	 //取出回饋金簡訊對象
	private function getfeedbackmobile($bid,$cat='')
	{
	 	$tmp = array();
	 	if ($cat ==1 ) {
	 		$this->sql2 = "SELECT 
		 					fs.fName AS mName,
		 					fs.fMobile AS mMobile,
		 					(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand,
		 					(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
				            b.bStore
	 					FROM
	 						tFeedBackStoreSms AS fs
	 					LEFT JOIN
	 						tBranch AS b ON b.bId = fs.fStoreId
	 					WHERE
	 						fs.fType = 2 AND fs.fStoreId = '".$bid."' AND fs.fDelete = 0";
	 		// $this->sql2 = "SELECT
				// 		(SELECT bName FROM tBrand AS a WHERE a.bId=b.bBrand) AS brand,
				// 		b.bStore,
				// 		(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=bs.bNID ) AS title,
				// 		bs.bName AS mName,
				// 		bs.bMobile AS mMobile,
				// 		bs.bBranch
				// 	FROM 
				// 		tBranchSms  AS bs
				// 	LEFT JOIN 
				// 		tBranch AS b ON b.bId = bs.bBranch
		
				// 	WHERE 
				// 		bs.bBranch  ='".$bid."' AND bs.bDel = 0 AND bs.bCheck_id ='0' AND bs.bMobile !='' AND (bs.bNID = 12 OR bs.bNID = 13) AND bs.bMobile NOT IN(
				// 		'0932365997','0928143823',
				// 		'0932365997','0935385125',
				// 		'0926707761','0926333488',
				// 		'0935385125','0932500182',
				// 		'0921959123','0954019400',
				// 		'0915327980','0932162872',
				// 		'0910183393','0913023292',
				// 		'0983199343','0920516608',
				// 		'0939043983','0921659033',
				// 		'0933100981','0925402402',
				// 		'0918100361','0927999506',
				// 		'0936638838','0935217439',
				// 		'0916072288','0932074446',
				// 		'0938882781','0933996610',
				// 		'0983710295','0927311921',
				// 		'0955123862','0911207606',
				// 		'0935662700','0936365391',
				// 		'0939624321','0919112835',
				// 		'0910509321','0953909092',
				// 		'0955431391','0911498747',
				// 		'0922921423','0938922272',
				// 		'0988853887')";
						// if ($_SESSION['member_id'] == 6 ) {
							// echo $this->sql2;
						// }
	 	}else{
	 		$this->sql2 = "SELECT
						(SELECT bName FROM tBrand AS a WHERE a.bId=branch.bBrand) AS brand,
						branch.bStore,
						(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=b.bNID ) AS title,
						b.bName AS mName,
						b.bMobile AS mMobile,
						b.bBranch
					FROM 
						tBranchFeedback  AS b
					LEFT JOIN 
						tBranch AS branch ON branch.bId = b.bBranch
		
					WHERE 
						b.bBranch  ='".$bid."'";
	 	}

	 	// echo $this->sql2;
		$getMD2 = $this->DB_link->prepare($this->sql2);
		$getMD2->execute();
		$tmp = $getMD2->fetchALL(PDO::FETCH_ASSOC);
		// print_r($tmp);
		return $tmp;		

	}

	 //取出回饋金簡訊對象(scrivener)
	 private function getfeedbackmobile2($bid,$cat='')
	 {
	 	$tmp = array();
	 	// echo $cat;
	 	if ($cat == 1) {

	 		$this->sql2 = "SELECT
		 					fs.fName AS mName,
		 					fs.fMobile AS mMobile,
		 					(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=fs.fTitle ) AS title,
		 					s.sOffice AS bStore,
		 					s.sName
	 					FROM
	 						tFeedBackStoreSms AS fs
	 					LEFT JOIN
	 						tScrivener AS s ON s.sId=fs.fStoreId
	 					WHERE
	 						fs.fType = 1 AND fs.fStoreId = '".$bid."' AND fs.fDelete = 0";
	 		// echo $this->sql2;				
	 		// $this->sql2 = "SELECT 
	 		// 				sId AS id ,
	 		// 				sName AS mName,
	 		// 				sOffice AS bStore,
	 		// 				sMobileNum AS mMobile FROM tScrivener WHERE sStatus = 1 AND sId ='".$bid."'
	 		// 			";
	 					// echo $this->sql2;

	 		// $this->sql2 = "SELECT 
			 // 			s.mName,
			 // 			ss.sName AS mName,
			 // 			ss.sMobile  AS mMobile,
			 // 			s.sOffice AS bStore,
			 // 			(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=ss.sNID ) AS title
	 	 // 			FROM 
	 	 // 				tScrivenerSms AS ss
	 	 // 			LEFT JOIN
	 	 // 				tScrivener AS s ON s.sId=ss.sScrivener
	 	 // 			WHERE
			 // 			s.sId ='".$bid."' AND ss.sNID = 1 AND ss.sDel = 0 AND ss.sCheck_id ='';
	 	 // 			";



	 	}else{
	 		$this->sql2 = "SELECT 
			 			s.sId,
			 			sf.sName AS mName,
			 			sf.sMobile  AS mMobile,
			 			s.sOffice AS bStore,
			 			(SELECT tTitle FROM tTitle_SMS AS a WHERE a.id=sf.sNID ) AS title

	 	 			FROM 
	 	 				tScrivenerFeedSms AS sf
	 	 			LEFT JOIN
	 	 				tScrivener AS s ON s.sId=sf.sScrivener
	 	 			WHERE
			 			s.sId ='".$bid."';
	 	 			";
	 	}
	 	

		$getMD2 = $this->DB_link->prepare($this->sql2);
		$getMD2->execute();
		$tmp = $getMD2->fetchALL(PDO::FETCH_ASSOC);

		// echo "<pre>";
		// print_r($tmp);
		// echo "</pre>";

		return $tmp;		

	 }
  
	//取出地政士簡訊接收對象
	public function getsScrivenerMobile($pid,$sid){
		$aryTemp1 = array();
		$aryTemp2 = array();
		$_T = array() ;
		
		$pid = substr($pid,5,9) ;
		$this->sql2 = "SELECT  `cId`,  `cCertifiedId`,  `cScrivener`,  `cSmsTarget`, cSmsTargetName,  `cAssistant`,  `cBankAccount`,  `cZip`,  `cAddress` FROM tContractScrivener WHERE cScrivener=$sid and cCertifiedId='$pid'";
		// echo $this->sql2."<br>";
		$getMD2 = $this->DB_link->prepare($this->sql2);
		$getMD2->execute();
		$aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
		// print_r($aryTemp2);
		$_T = explode(",",$aryTemp2[0]['cSmsTarget']);
		if ($aryTemp2[0]['cSmsTargetName']) {
			$name = explode(',', $aryTemp2[0]['cSmsTargetName']);
		}
		
		$_sql_str = '' ;
		if (count($_T) > 0) {
			//$_sql_str = ' AND a.sNID IN ("'.join('","',$_T).'") ' ;
			$_sql_str = ' AND a.sMobile IN ("'.join('","',$_T).'") ' ;
		}
	
		$this->execSQL = '
			SELECT 
				a.sName as mName,
				a.sMobile as mMobile,
				c.tTitle 
			FROM 
				tScrivenerSms AS a 
			INNER JOIN 
				tScrivener AS b ON a.sScrivener = b.sId 
			INNER JOIN 
				tTitle_SMS AS c ON a.sNID = c.id 
			WHERE 
				((a.sScrivener = "'.$sid.'" AND a.sCheck_id = "") OR a.sCheck_id = "'.$pid.'") AND a.sDel = 0 AND a.sLock = 0 '.$_sql_str.';
		' ;
		// echo $this->execSQL."<br><br>";
		$getMD = $this->DB_link->prepare($this->execSQL);
		$getMD->execute();
		//echo $getMD->rowCount();
		$tmp  = $getMD->fetchALL(PDO::FETCH_ASSOC);
		// print_r($tmpArr);
		// print_r($name);
		
		if (is_array($name)) {
			for ($i=0; $i < count($tmp); $i++) { 
				for ($j=0; $j < count($name); $j++) { 
					// echo $tmp[$i]['mName']."=".$name[$j]."<bR>";
					if ($tmp[$i]['mName'] == $name[$j]) {
						$aryTemp1[]=$tmp[$i];
						// echo 'QQ';
					}
				}
				
			}
		}else{
			$aryTemp1 = $tmp;
		}
		
		return $aryTemp1 ;
	}
	##
	

	//取出地政士服務費簡訊接收對象
	public function getsScrivenerMobile2($pid,$sid){
		$aryTemp1 = array();
		$aryTemp2 = array();
		$_T = array() ;
		
		$pid = substr($pid,5,9) ;
		$this->sql2 = "SELECT  `cId`,  `cCertifiedId`,  `cScrivener`,  `cSmsTarget`,  `cAssistant`,  `cBankAccount`,  `cZip`,  `cAddress` ,`cSend2` FROM tContractScrivener WHERE cScrivener=$sid and cCertifiedId='$pid'";
		//echo $this->sql2."<br>";
		$getMD2 = $this->DB_link->prepare($this->sql2);
		$getMD2->execute();
		$aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
		$_T = explode(",",$aryTemp2[0]['cSend2']);
		$_sql_str = '' ;
		if (count($_T) > 0) {
			//$_sql_str = ' AND a.sNID IN ("'.join('","',$_T).'") ' ;
			$_sql_str = ' AND a.sMobile IN ("'.join('","',$_T).'") ' ;
		}
	
		$this->execSQL = '
			SELECT 
				a.sName as mName,
				a.sMobile as mMobile,
				c.tTitle
			FROM 
				tScrivenerSms AS a 
			INNER JOIN 
				tScrivener AS b ON a.sScrivener = b.sId 
			INNER JOIN 
				tTitle_SMS AS c ON a.sNID = c.id 
			WHERE 
				a.sScrivener = "'.$sid.'" AND a.sDel = 0 AND a.sLock = 0 '.$_sql_str.';
		' ;
		//echo $this->execSQL."<br>";
		$getMD = $this->DB_link->prepare($this->execSQL);
		$getMD->execute();
		//echo $getMD->rowCount();
		$aryTemp1 = $getMD->fetchALL(PDO::FETCH_ASSOC);
		
		return $aryTemp1 ;
	}
	##

	//取出店簡訊接收對象
	public function getsBranchMobile($pid,$bid,$title) {
		$aryTemp2 = array() ;
		$pid = substr($pid,5,9) ;
		
		if ($title != '') {
			//取得合約書仲介順序
			$smsTarget = array() ;
			$this->execSQL = 'SELECT * FROM tContractRealestate WHERE cCertifyId="'.$pid.'";' ;
			$getMD = $this->DB_link->prepare($this->execSQL) ;
			$getMD->execute() ;
			
			if ($getMD->rowCount() > 0) {
				$rs = $getMD->fetchALL(PDO::FETCH_ASSOC) ;
				
				$v = $rs[0] ;
				unset($rs) ;
				
				for ($i = 0 ; $i < 4 ; $i ++) {
					$index = '' ;
					if ($i > 0) {
						$index = $i ;
					}
					
					if ($v['cBranchNum'.$index] == $bid) {						//若符合仲介店家編號
						$smsTarget = explode(',',$v['cSmsTarget'.$index]) ;		//取出合約書的仲介簡訊號碼
						break ;
					}
				}
			}
			##
			if (($title == '會計') || ($title == '秘書')) {
				$str = " AND a.bDefault = 1"; //只要預設對象
			}
			
			$this->sql2 = '
				SELECT
					a.bName as mName,
					a.bMobile as mMobile,
					b.tTitle,
					(SELECT CONCAT((Select bName From `tBrand` c Where c.bId = bBrand ),bStore) FROM tBranch WHERE bId = a.bBranch) AS storeName
				FROM
					tBranchSms AS a
				JOIN
					tTitle_SMS AS b ON b.id=a.bNID
				WHERE
					a.bBranch="'.$bid.'"
					AND b.tKind="0"
					AND a.bDel = 0
					AND b.tTitle = "'.$title.'"
					AND a.bCheck_id = 0
					'.$str.'
			' ;
			// echo $this->sql2;

			$getMD2 = $this->DB_link->prepare($this->sql2) ;
			$getMD2->execute() ;
			

			$arr = $getMD2->fetchALL(PDO::FETCH_ASSOC) ;
			
			$x = 0 ; 
			for ($i = 0 ; $i < count($arr) ; $i ++) {
				foreach ($smsTarget as $k => $v) {
					if ($arr[$i]['mMobile'] == $v) {
						$aryTemp2[$x]['mName'] = $arr[$i]['mName'] ;
						$aryTemp2[$x]['mMobile'] = $arr[$i]['mMobile'] ;
						$aryTemp2[$x]['tTitle'] = $arr[$i]['tTitle'] ;
						$aryTemp2[$x]['storeId'] = $bid ;
						$aryTemp2[$x]['storeName'] = $arr[$i]['storeName'];
						if (($title == '店長') || ($title == '店東')) {
							$aryTemp2[$x]['boss'] = 1;//	
						}					
						$x ++ ;
					}
				}

				$arr[$i]['storeId'] = $bid ;

			}

				
			//若輸入對象為會計或秘書，則需從基本資料中強制加入該身分的簡訊號碼
			if (($title == '會計') || ($title == '秘書')) {
				
				$aryTemp2 = array_merge($aryTemp2,$arr) ;
				unset($arr2);
			}
		

			##
			//取得額外職稱
			if($title=='店長')
			{
				$sql= "SELECT a.bName as mName, a.bMobile as mMobile,b.tTitle,(SELECT CONCAT((Select bName From `tBrand` c Where c.bId = bBrand ),bStore) FROM tBranch WHERE bId = a.bBranch) AS storeName FROM tBranchSms AS a JOIN tTitle_SMS AS b ON b.id=a.bNID WHERE a.bDel = 0 AND a.bBranch=".$bid." AND b.tCheck =1 AND a.bCheck_id =".$pid ;

				$getMD3 = $this->DB_link->prepare($sql) ;
				$getMD3->execute() ;
				
				$arr3 = $getMD3->fetchALL(PDO::FETCH_ASSOC) ;

				$x = 0 ;
				for ($i = 0 ; $i < count($arr3) ; $i ++) {
					foreach ($smsTarget as $k => $v) {
						if ($arr3[$i]['mMobile'] == $v) {
							$Temp[$x]['mName'] = $arr3[$i]['mName'] ;
							$Temp[$x]['mMobile'] = $arr3[$i]['mMobile'] ;	//tTitle
							$Temp[$x]['tTitle'] = $arr3[$i]['tTitle'] ;
							$Temp[$x]['storeId'] = $bid ;
							$Temp[$x]['storeName'] = $arr3[$i]['storeName'];
							if (($title == '店長') || ($title == '店東')) {
								$Temp[$x]['boss'] = 1;//	
							}						
							$x ++ ;
						}
					}
				}
				 if(count($Temp)>0)
				 {
					$aryTemp2 = array_merge($aryTemp2,$Temp) ;

				 }
				
			}

			##
		}

		return $aryTemp2 ;

	}
	##
  private function checkBranch($pid,$no)
  {

  	$pid = substr($pid,5,9) ;

  	$sql = 'SELECT cBranchNum,cServiceTarget,cBranchNum1,cServiceTarget1,cBranchNum2,cServiceTarget2,cServiceTarget3 FROM tContractRealestate WHERE cCertifyId="'.$pid.'";' ;

  	$getMD = $this->DB_link->prepare($sql);
    $getMD->execute();
	$data = $getMD->fetchALL(PDO::FETCH_ASSOC);

	if ($no==1) {	
		return $data[0]['cServiceTarget'];
	}elseif ($no==2) {
		return $data[0]['cServiceTarget1'];
	}elseif ($no==3) {
		return $data[0]['cServiceTarget2'];
	}elseif ($no==4) {
		return $data[0]['cServiceTarget3'];
	}
  }
  //取得保證號碼之第二組仲介
  private function getSecBranchMobile($pid) {
	// 取得第二家仲介店編號
	$_no = '' ;
	$pid = substr($pid,5,9) ;	//取得保證號碼
	
	$this->sql2 = 'SELECT cBranchNum1 as bid FROM tContractRealestate WHERE cCertifyId="'.$pid.'";' ;
	
    $getMD2 = $this->DB_link->prepare($this->sql2);
    $getMD2->execute();
	$_no = $getMD2->fetchALL(PDO::FETCH_ASSOC);
	return $_no[0]['bid'] ;
  }
  ##
  	//取得主買賣其他電話 $_id 1買2賣3買方經紀人4賣方經紀人
	public function get_phone($_id,$pid)
	{
		$pid = substr($pid,5,9) ;	//取得保證號碼
	
			$sql = 'SELECT cMobileNum,cName FROM tContractPhone  WHERE cCertifiedId ="'.$pid.'" AND cIdentity = "'.$_id.'";' ;
		    $getMD2 = $this->DB_link->prepare($sql);
		    $getMD2->execute();
			$arr = $getMD2->fetchALL(PDO::FETCH_ASSOC);

			return $arr ;
	}
	##
  //取得保證號碼之第三組仲介
  private function getThrBranchMobile($pid) 
  {
	// 取得第三家仲介店編號
	$_no = '' ;
	$pid = substr($pid,5,9) ;	//取得保證號碼
	
	$this->sql3 = 'SELECT cBranchNum2 as bid FROM tContractRealestate WHERE cCertifyId="'.$pid.'";' ;
	
    $getMD3 = $this->DB_link->prepare($this->sql3);
    $getMD3->execute();
	$_no = $getMD3->fetchALL(PDO::FETCH_ASSOC);
	return $_no[0]['bid'] ;
  }


  ##
  //取得保證號碼之第四組仲介
  private function getFourBranchMobile($pid) 
  {
	// 取得第四家仲介店編號
	$_no = '' ;
	$pid = substr($pid,5,9) ;	//取得保證號碼
	
	$this->sql4 = 'SELECT cBranchNum3 as bid FROM tContractRealestate WHERE cCertifyId="'.$pid.'";' ;
	
    $getMD4 = $this->DB_link->prepare($this->sql4);
    $getMD4->execute();
	$_no = $getMD4->fetchALL(PDO::FETCH_ASSOC);
	return $_no[0]['bid'] ;
  }
  ##
    
  public function getContractData($pid){
	  /* 輸出內容
	  Array
		(
			[0] => Array
				(
					[b_name] => 石玉光
					[b_mobile] => 0937990947
					[o_name] => 廖淳凱
					[o_mobile] => 0937132940
					[b_agent_name] => 
					[b_agent_mobile] => 
					[o_agent_name] => 
					[o_agent_mobile] => 
				)
		
		)
	  */
	  $aryTemp2 = array();
	  $pid = substr($pid,5,9) ;
	  //$this->sql2 = "SELECT a.cName AS b_name, a.cMobileNum AS b_mobile, b.cName AS o_name,b.cMobileNum AS o_mobile,a.sAgentName1 AS b_agent_name,a.sAgentMobile1 AS b_agent_mobile,b.sAgentName1 AS o_agent_name,a.sAgentName2 AS b_agent_name2,a.sAgentMobile2 AS b_agent_mobile2,a.sAgentName3 AS b_agent_name3,a.sAgentMobile3 AS b_agent_mobile3,a.sAgentName4 AS b_agent_name4,a.sAgentMobile4 AS b_agent_mobile4,b.sAgentMobile1 AS o_agent_mobile,b.sAgentName2 AS o_agent_name2,b.sAgentMobile2 AS o_agent_mobile2,b.sAgentName3 AS o_agent_name3,b.sAgentMobile3 AS o_agent_mobile3,b.sAgentName4 AS o_agent_name4,b.sAgentMobile4 AS o_agent_mobile4 FROM tContractBuyer AS a INNER JOIN tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId WHERE a.cCertifiedId = '$pid'";
	  $this->sql2 = '
		SELECT 
			cs.cScrivener,
	 		cr.cBranchNum,
			cr.cBranchNum1,
			cr.cBranchNum2,
			cr.cBranchNum3,
			cr.cServiceTarget,
			cr.cServiceTarget1,
			cr.cServiceTarget2,
			cr.cServiceTarget3,
			a.cName AS b_name, 
			a.cMobileNum AS b_mobile, 
			a.sAgentName1 AS b_agent_name,
			a.sAgentMobile1 AS b_agent_mobile,
			a.sAgentName2 AS b_agent_name2,
			a.sAgentMobile2 AS b_agent_mobile2,
			a.sAgentName3 AS b_agent_name3,
			a.sAgentMobile3 AS b_agent_mobile3,
			a.sAgentName4 AS b_agent_name4,
			a.sAgentMobile4 AS b_agent_mobile4,
			b.cName AS o_name,
			b.cMobileNum AS o_mobile,
			b.sAgentName1 AS o_agent_name,
			b.sAgentMobile1 AS o_agent_mobile,
			b.sAgentName2 AS o_agent_name2,
			b.sAgentMobile2 AS o_agent_mobile2,
			b.sAgentName3 AS o_agent_name3,
			b.sAgentMobile3 AS o_agent_mobile3,
			b.sAgentName4 AS o_agent_name4,
			b.sAgentMobile4 AS o_agent_mobile4 
		FROM 
			tContractBuyer AS a 
		INNER JOIN 
			tContractOwner AS b ON a.cCertifiedId = b.cCertifiedId 
		LEFT JOIN
			tContractScrivener AS cs ON cs.cCertifiedId=a.cCertifiedId
		LEFT JOIN
			tContractRealestate AS cr ON cr.cCertifyId =a.cCertifiedId
		WHERE 
			a.cCertifiedId = "'.$pid.'";
		' ;
	  $getMD2 = $this->DB_link->prepare($this->sql2);
      $getMD2->execute();
	  $aryTemp2 = $getMD2->fetchALL(PDO::FETCH_ASSOC);
	  return $aryTemp2 ;
	  
  }
  
	//產生簡訊檢查碼
	private function genCheckCode($last_id,$mobile) {
		$sms_check_code = str_pad(($last_id + $mobile),13,'0',STR_PAD_LEFT) ;		//編碼原則：長度 最長 13 碼數字
		$n13 = substr($sms_check_code,0,1) ;										//欄位順序：由右至左
		$n12 = substr($sms_check_code,1,1) ;										//運算規則：
		$n11 = substr($sms_check_code,2,1) ;										//=================================================
		$n10 = substr($sms_check_code,3,1) ;										//奇數碼相加之總和(n1 + n3 + n5 + n7 + n9 + n11 + n13) = A
		$n9 = substr($sms_check_code,4,1) ;											//偶數碼相加之總和(n2 + n4 + n6 + n8 + n10 + n12) = B
		$n8 = substr($sms_check_code,5,1) ;											// A + (B x 6) = C
		$n7 = substr($sms_check_code,6,1) ;											//取末三碼即為檢查碼(未滿三碼時左補零)
		$n6 = substr($sms_check_code,7,1) ;
		$n5 = substr($sms_check_code,8,1) ;
		$n4 = substr($sms_check_code,9,1) ;
		$n3 = substr($sms_check_code,10,1) ;
		$n2 = substr($sms_check_code,11,1) ;
		$n1 = substr($sms_check_code,12,1) ;
		
		$_odd = $n1 + $n3 + $n5 + $n7 + $n9 + $n11 + $n13 ;
		$_even = ($n2 + $n4 + $n6 + $n8 + $n10 + $n12) * 6 ;
		
		$sms_check_code = $_odd + $_even ;
		$sms_check_code = str_pad(substr($sms_check_code,-3),3,'0',STR_PAD_LEFT) ;
		
		return $sms_check_code ;
	}
	##
	
	//遠傳電訊簡訊發送
	private function send_fet_sms($mobile,$mobile_name,$txt,$tg,$pid,$tid) {
		$StartTime = date('Y-m-d H:i:s');
		$StartTime2 = microtime(true);
		$timeTxt = $pid."_".$tg."_".$mobile_name.'寄送開始'.$StartTime;
		$this->testLog($pid,$tg,$timeTxt);

		$from_addr = '0936019428' ;									//顯示的發話方號碼
		$url = 'http://61.20.32.60:6600/mpushapi/smssubmit' ;		//遠傳API網址
		$fet_SysId = $this->fet_SysId;									//API帳號代號
		$fet_SrcAddress = $this->fet_SrcAddress;					//發送訊息的來源位址(20個數字)
		$sms_str = '' ;
		$_error_code = '' ;
		
		//預設簡訊 ID
		$messageid = $msgid = 'Fake_'.uniqid() ;
		##
		
		//登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
		$last_id = $this->sms_regist2DB($from_addr,$mobile) ;
		##
		
		//運算產生簡訊檢查碼
		$sms_check_code = $this->genCheckCode($last_id,$mobile) ;
		##
		
		//編輯傳送簡訊字串
		$txt .= '('.$sms_check_code.')' ;							//簡訊內容加上(簡訊檢查碼)
		$max_len = strlen(base64_encode($txt)) ;					//計算簡訊長度(Base64加密後)
		
		$sms_str = '<?xml version="1.0" encoding="UTF-8"?>'.
			'<SmsSubmitReq>'.
				'<SysId>'.$fet_SysId.'</SysId>'.
				'<SrcAddress>'.$fet_SrcAddress.'</SrcAddress>'.
				'<DestAddress>'.$mobile.'</DestAddress>'.
				'<SmsBody>'.base64_encode($txt).'</SmsBody>'.
				'<DrFlag>true</DrFlag>'.
			'</SmsSubmitReq>' ;
		##
		
		//開始傳送簡訊、透過curl發送
		$url .= '?xml='.urlencode($sms_str) ;						//透過GET方式，傳送愈發送的簡訊資料
		$ch = curl_init($url) ;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
		$output = curl_exec($ch) ;
		if (curl_errno($ch)) {
			echo 'Curl 錯誤!! Id:'.curl_errno($ch).', Error:'.curl_error($ch)."\n" ;
			exit ;
		}
		curl_close($ch) ;
		##
		
		/*
		//假簡訊解果回傳
		$output = '<?xml version="1.0" encoding="UTF-8"?>
		<SubmitRes><ResultCode>00000</ResultCode><ResultText>Request successfully processed.</ResultText><MessageId>'.$messageid.'</MessageId></SubmitRes>' ;
		
		//$eee = $output ;
		//$eee = $url ;
		##
		*/
		
		//取出需求資料
		$output = str_replace("\n","",$output) ;
		if (preg_match("/<SubmitRes><ResultCode>(.*)<\/ResultCode><ResultText>(.*)<\/ResultText>(.*)<\/SubmitRes>/",$output,$matches)) {
			$code = trim($matches[1]) ;				//結果代碼
			$description = trim($matches[2]) ;		//結果說明
			
			if ($code == '00000') {
				preg_match("/<MessageId>(.*)<\/MessageId>/",$matches[3],$_id) ;		//遠傳簡訊查詢ID
				$messageid = trim($_id[1]) ;
				$reason = $_res = '已發送' ;
				$_res .= ' ['.$messageid.']' ;
				$_error_code = 's' ;				//發送成功、代碼"s"
			}
			else {
				$messageid = $msgid ;				//若本筆產生錯誤，則重新指定 message id
				
				$reason = '發送失敗' ;
				$_res = $reason.' -[ '.$description.' ]'."\n" ;
				$_res .= '************ error messages ************'."\n" ;
				$_res .= $txt."\n" ;
				$_res .= '****************************************'."\n" ;
				$_error_code = $code ;
			}
		}
		else {
			//網路連線錯誤失敗
			$reason = '無法建立網路連線' ;
			$_error_code = $code = '99999' ;
			##
		}
		##
		
		//寫入資料庫
		$this->sms_update2DB($last_id,$messageid,$code,$mobile,$from_addr,'2',$sms_check_code,$reason,$this->fet_sms_code($code),'') ;
		$this->writeDB($tg,$txt,$pid,$tid,$messageid,$mobile,$mobile_name) ;
		##
		
		//寫入Log
		$this->writeLog_fet($tg,$txt,$pid,$tid,$messageid,$mobile,$mobile_name) ;
		$this->smsLog_fet($_res,$mobile,$tg,$pid,$max_len,$txt) ;
		##

		$EndTime = date('Y-m-d H:i:s');
		$EndTime2 = microtime(true);
		$timeTxt = $pid."_".$tg."_".$mobile_name.'寄送結束'.$EndTime;
		$this->testLog($pid,$tg,$timeTxt);
		$this->testLog($pid,$tg,$pid."_".$tg."_".$mobile_name."_寄送程式執行了 " . ($EndTime2 - $StartTime2) . "秒\r\n");
		
		return $_error_code ;
		//return 'A:'.$eee ;
	}
	##
	
	//中華電信簡訊特碼(txt 字串須為Big5)
	private function send_cht_sms($mobile,$mobile_name,$txt,$tg,$pid,$tid) {		
		$acc_china = $this->acc_china ;			//中華電信帳號
		$pwd_china = $this->pwd_china ;			//中華電信密碼
		$from_addr = '0911510792' ;		//發話方電話號碼
		$max_ch = 68 ;					//最大簡訊文字數量
		$sms_success = 0 ;				//發送成功簡訊數量
		$_error_code = '' ;				//簡訊錯誤碼
		
		//登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
		$last_id = $this->sms_regist2DB($from_addr,$mobile) ;
		##
		
		//運算產生簡訊檢查碼
		$sms_check_code = $this->genCheckCode($last_id,$mobile) ;
		##
		
		//修正簡訊文字與編碼格式
		$txt .= '('.$sms_check_code.')' ;							//於簡訊最後面加入簡訊檢核碼
		$txt_big5 = $this->n_to_w($txt) ;							//將訊息中的半型數字轉為全型數字
		$txt_big5 = mb_convert_encoding($txt_big5,'BIG5','UTF-8') ; //將簡訊內容轉成Big-5編碼 
		$max_len = mb_strlen($txt_big5,'big5') ;					//計算簡訊長度
		$_divid = 1 ;												//預設發送一則簡訊
		##
		
		//若單封簡訊長度超長
		if ($max_len > $max_ch) {
			$_divid = ceil($max_len / $max_ch) ;					//簡訊發送次數(單筆內容多筆發送)
		}
		##
		
		//分批發送簡訊
		for ($i = 0 ; $i < $_divid ; $i ++) {
			$_start = $i * $max_ch ;
			//echo "_start=$_start,i=$i,max_ch=$max_ch<br><br>\n" ;
			$_big5_str = mb_substr($txt_big5,$_start,$max_ch,'big5') ;
			$_utf8_str = mb_convert_encoding($_big5_str,'UTF-8','BIG5') ;
			//echo 'str='.mb_convert_encoding($_big5_str,'utf8','big5'),"<br><br>\n" ;
			$sms_success ++ ;
			
			//https 版本
			$url = 'https://imsp.emome.net:4443/imsp/sms/servlet/SubmitSM' ;		//網址
			$url .= '?account='.$acc_china.'&password='.$pwd_china ;				//帳號密碼
			$url .= '&from_addr_type=0&from_addr='.$from_addr ;						//發話方手機號碼
			$url .= '&to_addr_type=0&to_addr='.$mobile ;							//發送至手機號碼
			$url .= '&msg_expire_time=0&msg_type=0' ;								//設定資料格式
			$url .= '&msg='.urlencode($_big5_str) ;									//發送內容
			##
			
			//預設簡訊 ID
			$messageid = $msgid = 'Fake_'.uniqid() ;
			##
			
			//開始發送簡訊
			$res = $this->file_get_contents_curl($url,1) ;		// 1 : https 連線方式、2 : http 連線方式
			//echo "RES=".$res."<br>\n" ;
			##
					
			//假資料測試
			//$res = "<html>\n<header>\n</header>\n<body>\n".$mobile.'|0|'.$messageid."|Success<br>\n</body>\n</html>" ;
			##
			
			//取得發送簡訊回傳之相關訊息
			$res = str_replace("\n","",$res) ;
			if (preg_match("/<html><header><\/header><body>(.*)\|(.*)\|(.*)\|(.*)<br><\/body><\/html>/",$res,$_data)) {		//連線正常取得回傳訊息
				$_tel = trim($_data[1]) ;					//收訊端手機號碼
				$code = trim($_data[2]) ;					//回傳代碼
				$messageid = trim($_data[3]) ;				//中華電信簡訊 ID
				$description = trim($_data[4]) ;			//描述
				
				//if ($i == 1) { $code = 2 ; }	/////////////////////////////////////// 為了測試單筆多封簡訊 ////////////
				
				if ($code == '0') {
					//$reason = $_res = '發送成功' ;
					$reason = $_res = '已發送' ;
					$_res .= ' ['.$messageid.']' ;
				}
				else {
					$messageid = $msgid ;					//若本筆產生錯誤，則重新指定 message id

					$reason = '發送失敗' ;
					$_res = $reason.' -[ '.$url.' ]'."\n" ;
					$_res .= '************ error messages ************'."\n" ;
					$_res .= $res."\n" ;
					$_res .= '****************************************'."\n" ;
				
					$_error_code = $code ;					//記錄錯誤代碼(最後一次錯誤...)
					$sms_success -- ;
				}
			}
			else {		//網路發生錯誤時之處置
				$_tel = $mobile ;
				$reason = '發送失敗' ;
				$_res = $reason.' -[ '.$url." ] - [ 網路連線錯誤!! ] \n" ;
				
				$_error_code = $code = '77' ;					//記錄錯誤代碼(網路錯誤 77 )
				$sms_success -- ;
			}
			##
			
			//若中華電信加值簡訊遭拒則改用亞太發送簡訊
			//if ($code == '48') {	//確認亞太系統發送簡訊
			if (($code == '48') && (!preg_match("/^H/",$messageid))) {	//確認亞太系統發送簡訊
				unset($_data) ;
				
				$returnAns = $this->send_apol_sms($mobile,$_utf8_str,$tg,$pid,$tid) ;
				//$returnAns = '<Response><Reason>開始發送1</Reason><Code>0</Code><MDN>0980013768</MDN><TaskID>'.$messageid.'</TaskID><RtnDateTime>20130506132520</RtnDateTime></Response>' ;
				if (preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/",$returnAns,$_data)) {
					$apol_reason = $_data[1] ;		//代碼說明
					$apol_code = $_data[2] ;		//回傳代碼
					$apol_mdn = $_data[3] ;			//企業門號
					$apol_tid = $_data[4] ;			//交易代號
					$apol_RDT = $_data[5] ;			//平台回應時間
				}
				else {	
					$apol_code = '999999999' ;		//亞太系統發送失敗 code = 999999999(自行定義)
				}
				
				//若回傳成功(0)時，成功數量統計加 1
				if ($apol_code == '0') {
					$sms_success ++ ;
				}
				##
					
				//若無企業門號則手動指定企業門號
				if (!$apol_mdn) {
					$apol_mdn = '0980013768' ;
				}
				##
						
				//若無交易代號則產生唯一鍵值取代亞太回傳碼
				if (!$apol_tid) {
					$apol_tid = $messageid ;
				}
				##
						
				//若無平台回應時間則手動產生平台回應時間
				if (!$apol_RDT) {
					$apol_RDT = date("YmdHis") ;
				}
				##
					
				//依據代碼，取得亞太回傳結果
				$apol_reason = $this->apol_send_code($apol_code) ;
				##
				//echo "tid=$apol_tid,reason=$apol_reason,code=$apol_code,mdn=$apol_mdn,RDT=$apol_RDT,mobile=$mobile<br>\n" ; exit ;
				//將簡訊發送完成紀錄寫到資料庫中
				//$last_id = $this->apol_sms_check_register($apol_tid,$apol_reason,$apol_code,$apol_mdn,$apol_RDT,$mobile) ;
				if ($i <= 0) {
					$this->sms_update2DB($last_id,$apol_tid,$apol_code,$_tel,$apol_mdn,'3',$sms_check_code,$apol_reason,'',$apol_RDT) ;
				}
				else {
					$last_id = $this->sms_regist2DB($apol_mdn,$mobile) ;
					$this->sms_update2DB($last_id,$apol_tid,$apol_code,$_tel,$apol_mdn,'3','',$apol_reason,'',$apol_RDT) ;
				}
				$this->writeDB($tg,$_utf8_str,$pid,$tid,$apol_tid,$mobile,$mobile_name) ;
				##
					
				//寫入sms_log資料表
				$this->writeLog_apol($tg,$_utf8_str,$pid,$tid,$apol_tid,$mobile,$mobile_name) ;
				##
					
				//產生簡訊Log
				//($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$_utf8_str=>簡訊本文)
				$this->smsLog_apol($returnAns,$mobile,$tg,$pid,mb_strlen($_utf8_str,'utf-8'),$_utf8_str) ;
				##
			}
			##
			else {	//中華系統發送簡訊
				//將簡訊發送完成紀錄寫到資料庫中
				//($messageid=>簡訊回傳碼,$reason=>簡訊發送狀態(伺服器錯誤),$_code=>回傳狀態碼,$_desc=>狀態描述,$from_addr=>發送端手機號碼,$_tel=>接收端手機號碼)
				if ($i <= 0) {
					$this->sms_update2DB($last_id,$messageid,$code,$_tel,$from_addr,'1',$sms_check_code,$reason,$this->cht_sms_code($code),'') ;
				}
				else {
					$last_id = $this->sms_regist2DB($from_addr,$mobile) ;
					$this->sms_update2DB($last_id,$messageid,$code,$_tel,$from_addr,'1','',$reason,$this->cht_sms_code($code),'') ;
				}
				$this->writeDB($tg,mb_convert_encoding($_big5_str,'utf8','big5'),$pid,$tid,$messageid,$mobile,$mobile_name) ;
				##
			}
			##
			
			//寫入sms_log資料表
			//($target=>對象,$smsTxt=>簡訊內容,$pid=>保證號碼(tVR_Code),$tid=>Expense ID,$msg_id=>查詢用ID,$_tel=>接收端手機號碼,$mobile_name=>接收者姓名)
			$this->writeLog_cht($tg,mb_convert_encoding($_big5_str,'utf8','big5'),$pid,$tid,$messageid,$mobile,$mobile_name) ;
			##
				
			//產生簡訊Log
			//($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$txt=>簡訊本文)
			$this->smsLog_cht($_res,$mobile,$tg,$pid,$max_len,mb_convert_encoding($_big5_str,'utf8','big5')) ;
			##
			
		}
		##
		
		//回傳簡訊發送結果
		if ($sms_success <= 0) {
			return $_error_code ;						//失敗
		}
		else if ($_divid == $sms_success) {
			return 's' ;								//成功
		}
		else {
			return 'p' ;								//部份成功
		}
		##
	}
	##
  
	// ---- APOL SMS MODULE
	private function send_apol_sms($mobile,$txt,$tg,$pid,$tid,$sys=1) {
		$from_addr = '0980013768' ;
		$sms_success = 0 ;
		$msg_id = 'Fake_'.uniqid() ;
		
		//若為自主發送簡訊
		if ($sys != 1) {
			//登錄資料庫位置並取的 ID 以便進行簡訊驗證編碼運算
			$last_id = $this->sms_regist2DB($from_addr,$mobile) ;
			##
			
			//運算產生簡訊檢查碼
			$sms_check_code = $this->genCheckCode($last_id,$mobile) ;
			##
			
			//於簡訊最後面加入簡訊檢核碼並將半形文數字轉為全形
			$txt .= '('.$sms_check_code.')' ;
			$txt = $this->n_to_w($txt) ;
			##
		}
		##
		
		//$pid = substr($pid,5,9) ;
		$_len = mb_strlen($txt,"utf-8");
		$api_kind="APIRTRequest";
		$url = 'xsms.aptg.com.tw';
		
		$fp = fsockopen($url, 80, $errno, $errstr, 30);
		if (!$fp) {
			echo 'Could not open connection.';
			return 'error';
		}
		else {
			$xmlpacket ='<soap-env:Envelope xmlns:soap-env=\'http://schemas.xmlsoap.org/soap/envelope/\'> 
			    <soap-env:Header/> 
			    <soap-env:Body> 
			        <Request>
			            <MDN>'.$from_addr.'</MDN> 
			            <UID>' .$this->uid. '</UID> 
			            <UPASS>' .$this->upass. '</UPASS> 
			            <Subject>'.$tg."_".substr($pid,5,9).'</Subject> 
			            <Retry>Y</Retry>
			            <AutoSplit>Y</AutoSplit><Message>'.$txt.'</Message> 
			            <MDNList><MSISDN>'.$mobile.'</MSISDN></MDNList> 
			        </Request> 
			    </soap-env:Body> 
			</soap-env:Envelope>';
			$contentlength = strlen($xmlpacket);
			//echo "<pre>";
			//print_r($xmlpacket);
			
			$out = "POST /XSMSAP/api/".$api_kind." HTTP/1.1\r\n";
			$out .= "Host: 210.200.219.138\r\n";
			$out .= "Connection: close\r\n";
			$out .= "Content-type: text/xml;charset=utf-8\r\n";
			$out .= "Content-length: $contentlength\r\n\r\n";
			$out .= "$xmlpacket";
			
			fwrite($fp, $out);
			$theOutput='';
			while (!feof($fp)) {
				$theOutput .= fgets($fp, 128);
			}
			
			fclose($fp);
			//echo $theOutput."\n";
			$res = $theOutput;
			//$res = '<Response><Reason>開始發送</Reason><Code>0</Code><MDN>'.$from_addr.'</MDN><TaskID>'.$msg_id.'</TaskID><RtnDateTime>20130506132520</RtnDateTime></Response>' ;
			
			//若為自主發送簡訊
			if ($sys != 1) {
				//取得回傳代碼
				if (preg_match("/<Reason>(.*)<\/Reason><Code>(.*)<\/Code><MDN>(.*)<\/MDN><TaskID>(.*)<\/TaskID><RtnDateTime>(.*)<\/RtnDateTime>/",$res,$_data)) {
					$apol_reason = trim($_data[1]) ;		//代碼說明
					$apol_code = trim($_data[2]) ;			//回傳代碼
					$apol_mdn = trim($_data[3]) ;			//企業門號
					$apol_tid = trim($_data[4]) ;			//交易代號
					$apol_RDT = trim($_data[5]) ;			//平台回應時間
				}
				else {	
					$apol_code = '999999999' ;		//亞太系統發送失敗 code = 999999999(自行定義)
				}
				##
				
				//若回傳成功(0)時，成功數量統計加 1
				if ($apol_code == '0') {
					$sms_success ++ ;
				}
				##
					
				//若無企業門號則手動指定企業門號
				if (!$apol_mdn) {
					$apol_mdn = $from_addr ;
				}
				##
						
				//若無交易代號則產生唯一鍵值取代亞太回傳碼
				if (!$apol_tid) {
					$apol_tid = $msg_id ;
				}
				##
						
				//若無平台回應時間則手動產生平台回應時間
				if (!$apol_RDT) {
					$apol_RDT = date("YmdHis") ;
				}
				##
					
				//依據代碼，取得亞太回傳結果
				$apol_reason = $this->apol_send_code($apol_code) ;
				##
				//將簡訊發送完成紀錄寫到資料庫中
				//echo "tid=$apol_tid,reason=$apol_reason,code=$apol_code,mdn=$apol_mdn,RDT=$apol_RDT,mobile=$mobile<br>\n" ; exit ;				
				$this->sms_update2DB($last_id,$apol_tid,$apol_code,$_tel,$apol_mdn,'3',$sms_check_code,$apol_reason,'',$apol_RDT) ;
				$this->writeDB($tg,$txt,$pid,$tid,$apol_tid,$mobile,$mobile_name) ;
				##
					
				//寫入sms_log資料表
				$this->writeLog_apol($tg,$txt,$pid,$tid,$apol_tid,$mobile,$mobile_name) ;
				##
					
				//產生簡訊Log
				//($_res=>簡訊發出狀態,$mobile=>接收端手機號碼,$tg=>發送對象,$pid=>保證號碼,$max_len=>簡訊長度,$_utf8_str=>簡訊本文)
				$this->smsLog_apol($res,$mobile,$tg,$pid,$_len,$txt);
				##
				
				//回傳簡訊發送結果
				if ($sms_success <= 0) {
					return $_error_code ;						//失敗
				}
				else {
					return 's' ;								//成功
				}
				##
			}
			##
			else {
				return $res ;
			}
		}
	}
	##
	
  //
	private function send_sms($mobile,$name,$txt,$tg,$pid){
		$pid = substr($pid,5,9) ;
		if ($mobile <>""){ // 手機不為空值才會執行.
		$_len = mb_strlen($txt,"big5");
		if ($_len > 70) {
			$_t = ceil($_len / 70);
			for ($_i=0;$_i < $_t ;$_i++){
				$_start = $_i * 70;
				$_split_txt = mb_substr($txt,$_start,70,"big5");
				$url = 'https://smexpress.mitake.com.tw:8800/SmSendGet.asp?username=0921946427&password=first168&dstaddr='.$mobile.'&DestName='.$name.'&smbody='.$_split_txt;
		$_res = $this->file_get_contents_curl($url);
		$_res_utf8 = iconv("big5","utf-8",$_res);
		$_split_txt_u8 = iconv("big5","utf-8",$_split_txt);
		$this->smsLog($_res_utf8,$mobile,$name,$tg,$pid,$_len,$_split_txt_u8);
				
			}
		} else {
		$url = 'https://smexpress.mitake.com.tw:8800/SmSendGet.asp?username=0921946427&password=first168&dstaddr='.$mobile.'&DestName='.$name.'&smbody='.$txt;
		//$_res = $this->file_get_contents_curl($url);
		$_res="------------";
		$_res_utf8 = iconv("big5","utf-8",$_res);
		$_txt_utf8 = iconv("big5","utf-8",$txt);
		$this->smsLog($_res_utf8,$mobile,$name,$tg,$pid,$_len,$_txt_utf8);
		}
		//var_dump($_res_utf8);
		}
	
	}
	
	//遠傳電信 Log 紀錄
	private function smsLog_fet($txtlog,$mobile,$tg_txt,$pid,$len,$smstxt) {
		$fs = $this->log_path.'sms_fet_'.date("Ymd").'.log' ;
	  	$fp = fopen($fs, 'a+');
		fwrite($fp, "===[".$tg_txt."]=[".substr($pid,5)."]========[".date("Y-m-d H:i:s")."]===========[".$len."]=======CHT=========================\n");
		fwrite($fp,$mobile."\n");
		fwrite($fp,$smstxt."\n");
		fwrite($fp, $txtlog."\n");
		fwrite($fp, "===============================================================================================================\n");
		fclose($fp);
    }
	##
	
	
	//中華電信 Log 紀錄
	private function smsLog_cht($txtlog,$mobile,$tg_txt,$pid,$len,$smstxt) {
		$fs = $this->log_path.'sms_cht_'.date("Ymd").'.log' ;
	  	$fp = fopen($fs, 'a+');
		fwrite($fp, "===[".$tg_txt."]=[".substr($pid,5)."]========[".date("Y-m-d H:i:s")."]===========[".$len."]=======CHT=========================\n");
		fwrite($fp,$mobile."\n");
		fwrite($fp,$smstxt."\n");
		fwrite($fp, $txtlog."\n");
		fwrite($fp, "===============================================================================================================\n");
		fclose($fp);
    }
	##
	
	//亞太電信 Log 紀錄
	private function smsLog_apol($txtlog,$mobile,$tg_txt,$pid,$len,$smstxt){
		$fs = $this->log_path.'sms_'.date("Ymd").'.log' ;
	  	$fp = fopen($fs, 'a+');
		fwrite($fp, "===[".$tg_txt."]=[".substr($pid,5)."]========[".date("Y-m-d H:i:s")."]===========[".$len."]=======APOL========================\n");
		fwrite($fp,$mobile."\n");
		fwrite($fp,$smstxt."\n");
		fwrite($fp, $txtlog."\n");
		fwrite($fp, "===============================================================================================================\n");
		fclose($fp);
    }
	##
	
	//
	private function smsLog($txtlog,$mobile,$name,$tg_txt,$pid,$len,$smstxt){
	    $fs = '/home/httpd/html/first.twhg.com.tw/sms/log/sms_'.date("Ymd").".log";
	  	$fp = fopen($fs, 'a+');
		fwrite($fp, "===[".$tg_txt."]=[".$pid."]========[".date("Y-m-d H:i:s")."]=[".$mobile." ".$name."]==[".$len."]======\n");
		fwrite($fp,$smstxt."\n");
		fwrite($fp, $txtlog."\n");
		fwrite($fp, "===============================================================================================================\n");
		fclose($fp);
    }
	//
	private function file_get_contents_curl($url,$ver=1) {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//選擇使用的 http 連線方式 1:https、2:http
		if ($ver == 1) {
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		}
		##
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
		
		$data = curl_exec($ch);
		//var_dump($data);
		curl_close($ch);
		
		return $data;
	}
	//
	private function sms_check_register($_tid,$_reason='',$_code,$_mdn='',$_RDT='',$_telNo) {
		if (preg_match("/伺服器錯誤/",$_reason)) { $_checked = 'y' ; }
		else { $_checked = 'n' ; }
		
		$sql = 'INSERT INTO tSMS_Check (tTaskId,tChecked,tReason,tCode,tMDN,tMSISDN,tRtnDateTime) VALUES ("'.$_tid.'","'.$_checked.'","'.$_reason.'","'.$_code.'","'.$_mdn.'","'.$_telNo.'","'.$_RDT.'");' ;
		$insertData = $this->DB_link->prepare($sql) ;
		$insertData->execute() ;
	}
	
	//亞太電信簡訊發送紀錄
	private function apol_sms_check_register($_tid,$_reason='',$_code,$_mdn='',$_RDT='',$_telNo) {
		if ($_code == '0') { $_checked = 'n' ; }
		else { $_checked = 'y' ; }
		
		$sql = 'INSERT INTO tSMS_Check (tTaskID,tChecked,tReason,tCode,tMDN,tMSISDN,tRtnDateTime) VALUES ("'.$_tid.'","'.$_checked.'","'.$_reason.'","'.$_code.'","'.$_mdn.'","'.$_telNo.'","'.$_RDT.'");' ;
		$insertData = $this->DB_link->prepare($sql) ;
		$insertData->execute() ;
		return $this->DB_link->lastInsertId() ;
	}
	##
	
	//中華電信簡訊發送紀錄
	private function cht_sms_check_register($_tid,$_reason='',$_code,$_desc='',$_mdn='',$_telNo) {
		if (preg_match("/發送失敗/",$_reason)) { $_checked = 'y' ; }
		else { $_checked = 'n' ; }
		
		$sql = 'INSERT INTO tSMS_Check (tTaskId,tChecked,tReason,tCode,tMDN,tMSISDN,tSystem) VALUES ("'.$_tid.'","'.$_checked.'","'.$_desc.'","'.$_code.'","'.$_mdn.'","'.$_telNo.'","1");' ;
		$insertData = $this->DB_link->prepare($sql) ;
		$insertData->execute() ;
		return $this->DB_link->lastInsertId() ;
	}
	##
	
	//簡訊檢查登錄到資料庫中
	private function sms_regist2DB($_mdn='',$_msisdn='') {
		$sql = 'INSERT INTO tSMS_Check (tChecked,tMDN,tMSISDN,tRegistTime) VALUES ("n","'.$_mdn.'","'.$_msisdn.'","'.date("Y-m-d H:i:s").'");' ;
		$insertData = $this->DB_link->prepare($sql) ;
		$insertData->execute() ;
		
		return $this->DB_link->lastInsertId() ;
	}
	##
	
	//簡訊檢查更新到資料庫中
	private function sms_update2DB($_lastId,$_tid,$_code,$_telNo,$_mdn,$_system,$sms_check_code,$_reason='',$_desc='',$_RDT='') {
		if ($_code == '0') { $_checked = 'n' ; }
		else if ($_code == '00000') { $_checked = 'n' ; }
		else { $_checked = 'y' ; }
		
		$sql = 'UPDATE tSMS_Check SET tTaskID="'.$_tid.'",tChecked="'.$_checked.'",tReason="'.$_reason.'",tCode="'.$_code.'",tMDN="'.$_mdn.'",tMSISDN="'.$_telNo.'",tRtnDateTime="'.$_RDT.'",tSystem="'.$_system.'",tCheckCode="'.$sms_check_code.'",tRegistTime="'.date("Y-m-d H:i:s").'" WHERE id="'.$_lastId.'" ;' ;
		$insertData = $this->DB_link->prepare($sql) ;
		$insertData->execute() ;
	}
	##
	
	//中華電信錯誤代碼解析
	private function cht_sms_code($no=0) {
		// '77' 為網路錯誤所自行加入之錯誤碼
		$code_des = array(
			'0'=>'已發出、系統將開始發送簡訊',
			'2'=>'訊息傳送失敗',
			'3'=>'訊息預約時間超過48小時',
			'5'=>'訊息從Big-5轉碼到UCS失敗',
			'11'=>'參數錯誤',
			'12'=>'訊息的失效時間數值錯誤',
			'13'=>'SMS訊息的訊息種類不屬於合法的message type',
			'14'=>'用戶具備改發訊息權限，請填發訊號碼',
			'15'=>'簡訊號碼格式錯誤',
			'16'=>'系統無法執行msisdn<->subno，請稍後再試',
			'17'=>'系統無法找出對應此subno支電話號碼，請查明subno是否正確',
			'18'=>'請檢查受訊方號碼格式是否正確',
			'19'=>'受訊號碼數目超過系統限制(目前為20)',
			'20'=>'訊息長度不正確',
			'22'=>'帳號或是密碼錯誤',
			'23'=>'你登入的IP未在系統註冊',
			'24'=>'帳號已停用',
			'33'=>'企業預付帳號沒金額，請儲值',
			'34'=>'企業預付儲值系統發生介接錯誤，請洽服務人員',
			'35'=>'抱歉、企業預付系統扣款錯誤、請再試',
			'36'=>'抱歉、企業預付扣款系統鎖住，暫時無法使用、請再試',
			'37'=>'企業預付扣款帳號鎖住，暫時無法使用(可能多條連線同時發訊所產生、請再重試)',
			'41'=>'發訊內容含有系統不允許發送字集，請修改訊息內容再發訊',
			'43'=>'這個受訊號碼是空號(此錯誤碼只會發生在限發CHT的用戶發訊時產生)',
			'44'=>'無法判斷號碼是否屬於中華電信門號。無法決定費率，而停止發訊',
			'45'=>'放心講客戶餘額不足、無法發訊',
			'46'=>'無法決定計費客戶屬性、而停止服務',
			'47'=>'該特碼帳號無法提供預付式客戶使用',
			'48'=>'受訊客戶要求拒收加值簡訊、請不要重送',
			'49'=>'顯示於手機之發訊號碼格式不對',
			'50'=>'放心講系統扣款錯誤、請再試',
			'51'=>'預付客戶餘額不足、無法發訊',
			'52'=>'抱歉、預付式系統扣款錯誤、請再試',
			'77'=>'網路連線錯誤、請連絡相關人員'
		) ;
		
		if ($no < 0) {
			return '中華電信系統或是資料庫故障' ;
		}
		else {
			return $code_des[$no] ;
		}
	}
	##
	
	// 半形(narrow)、全形(wide)互換 -- 數字版
	private function n_to_w($strs, $types = '0') {
		$nt = array(
			"0", "1", "2", "3", "4", "5", "6", "7", "8", "9"
		) ;
		$wt = array(
			"０", "１", "２", "３", "４", "５", "６", "７", "８", "９"
		) ;
 
		if ($types == '0') {
			$strtmp = str_replace($nt,$wt,$strs) ;			// narrow to wide (半形轉全形)
		}
		else {
			$strtmp = str_replace($wt,$nt,$strs) ;			// wide to narrow (全形轉半形)
		}
		
		return $strtmp ;
	}
	##
	
	//簡訊發送結果碼
	private function return_code($ch) {
		switch ($ch) {
			case 's' :
						$ans = '簡訊已發出' ;
						break ;
			case 'p' :
						$ans = '單筆多封簡訊部分發出!!明細請至簡訊明細查詢' ;
						break ;
			case 'f' :
						$ans = '簡訊失敗!!詳細內容請至簡訊明細查詢' ;
						break ;
			case 'n' :
						$ans = '門號格式錯誤' ;
						break ;
			case 's1' :
						$ans = '系統開始發送簡訊' ;
						break ;
			case 'f1' :
						$ans = '系統發送簡訊失敗！詳情請查詢簡訊明細...' ;
						break ;
			case 'fn' :
						$ans = '系統發送簡訊失敗(fn)！詳情請查詢簡訊明細...' ;
						break ;
			case 'n1' :
						$ans = '簡訊門號格式錯誤！' ; 
						break ;
			case 'u1' :
						$ans = '系統開始發送部分簡訊！詳情請查詢簡訊明細...' ;
						break ;
			default ;
						$ans = '未知的錯誤' ;
						break ;
		}
		return $ans ;
	}
	##
	
	//回覆簡訊發送結果(出款項目專用)
	private function out_sms_code($ss=0,$pp=0,$ff=0,$nn=0) {
		$aa = $ss + $pp + $ff + $nn ;			//計算所有簡訊發送總和
		
		if ($aa == 0) {
			return '找不到發送簡訊號碼!!' ;
		}
		else if ($ss == $aa) {					//簡訊完全發送成功 (s1)
			return $this->return_code('s1') ;
		}
		else if ($ff == $aa) {					//簡訊完全發送失敗 1 (f1)
			return $this->return_code('f1') ;
		}
		else if ($nn == $aa) {					//簡訊號碼格式全部錯誤 (n1)
			return $this->return_code('n1') ;
		}
		else if (($ff + $nn) == $_a) {			//簡訊完全發送失敗 2 (fn)
			return $this->return_code('fn') ;
		}
		else {									//簡訊部分發送成功、部分失敗 (u1)
			return $this->return_code('u1') ;
		}
	}
	##
	
	//取得其他買賣方資料 $_ide 2: 賣方、1: 買方、6買方代理人、7賣方代理人
	public function get_others($_pid,$_ide) {
		$_sql = '
			SELECT
				*
			FROM
				tContractOthers
			WHERE
				cCertifiedId="'.substr($_pid,5).'"
				AND cIdentity="'.$_ide.'"
		' ;
		
		$_getData = $this->DB_link->prepare($_sql) ;
		$_getData->execute() ;
		$_myData = $_getData->fetchALL(PDO::FETCH_ASSOC) ;
		
		$_returnArr = array() ;
		
		for ($i = 0 ; $i < count($_myData) ; $i ++) {
			$_returnArr[$i]['cName'] = $_myData[$i]['cName'] ;
			$_returnArr[$i]['cMobileNum'] = $_myData[$i]['cMobileNum'] ;
		}
		
		//print_r($_returnArr) ;
		return $_returnArr ;
		//return $_sql ;
	}
	##
	private function getProperty($cid,$cat='') //建物地址
	{

		$_sql = '
			SELECT
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bCity,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=p.cZip) AS bArea,
				p.cAddr AS bAddr,
				(SELECT zCity FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lCity,
				(SELECT zArea FROM tZipArea AS z WHERE z.zZip=l.cZip) AS lArea,
				l.cLand1 AS lAddr
			FROM
				tContractProperty AS p
			LEFT JOIN 
				tContractLand AS l ON l.cCertifiedId=p.cCertifiedId 
			WHERE
				p.cCertifiedId="'.$cid.'"
		' ;
		// echo "<br>".$_sql."<br>";
		
		$_getData = $this->DB_link->prepare($_sql) ;
		$_getData->execute() ;
		$_myData = $_getData->fetchALL(PDO::FETCH_ASSOC) ;
	
		// echo $_myData[0]['bAddr']."<bR>";
		// echo $_myData[0]['bAddr']."_".$cat."<br>";
		if ($_myData[0]['bAddr']!='') {
			if ($cat != 'all') {
				preg_match("/(\D(.*)[路|街|段]{1})?(.*)?/isu",$_myData[0]['bAddr'],$arr) ;
				$addr = $_myData[0]['bCity'].$_myData[0]['bArea'].$arr[1];
			}else{
				$addr =$_myData[0]['bCity'].$_myData[0]['bArea'].$_myData[0]['bAddr'];
			}
			

			
			
		}else{

			$addr = $_myData[0]['lCity'].$_myData[0]['lArea'].$_myData[0]['lAddr'].'段';
		}
			
	
		//print_r($_returnArr) ;
		return $addr ;
		//return $_sql ;
	}
	private function getBankDate($tid)
	{
		$_sql = '
			SELECT
				tBankLoansDate
			FROM
				tBankTrans 
			WHERE
				tId="'.$tid.'"
		' ;
		
		$_getData = $this->DB_link->prepare($_sql) ;
		$_getData->execute() ;
		$data = $_getData->fetchALL(PDO::FETCH_ASSOC) ;

		$tmp = explode('-', $data[0]['tBankLoansDate']);

		$date = $tmp[1]."月".$tmp[2]."日";

		return $date;
	}
	private function sendBossSms($boss,$sms_txt,$target,$pid,$tid,$sys)
	{
		for ($i = 0 ; $i < count($boss) ; $i ++) {
			$mobile_tel = $boss[$i]["mMobile"] ;
			$mobile_name = $boss[$i]["mName"] ;

			

			if ($boss[$i]['boss'] == 1) { 
				// echo $mobile_tel.$mobile_name.$sms_txt.",".$target.",".$pid.",".$tid.",".$sys."\r\n";

						
				if (preg_match('/^09[0-9]{8}$/',$mobile_tel)) {
					$sms_id = $this->sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid,$sys) ;
									
					if ($sms_id == 's') {							//發送成功(s)
						$boss[$i]['mMobile'] .= '、'.$this->return_code('s') ;
					}
					else if ($sms_id == 'p') {						//部份成功(p)
						$boss[$i]['mMobile'] .= '、'.$this->return_code('p') ;
					}
					else {											//發送失敗(f)
						$boss[$i]['mMobile'] .= '、'.$this->return_code('f') ;
					}
				}
				else {												//門號錯誤(n)
					$boss[$i]['mMobile'] .= '、'.$this->return_code('n') ;
				}
			}
		}

		return $boss;
	}	
	//亞太簡訊傳送回傳代碼轉換表(發送)
	private function apol_send_code($code) {
		// '999999999' 為網路錯誤所自行加入之錯誤碼
		$code_list = array (
			'0' => '已發出、系統將開始發送簡訊' ,
			'16777217' => '認證失敗(用戶/企業代表號不存在或密碼錯誤)' ,
			'16777218' => '來源IP未授權使用' ,
			'16777219' => '指定帳號不存在(或空白)' ,
			'33554433' => '額度不足(或合約已開通未儲值)' ,
			'33554434' => '連線數超過上限' ,
			'33554435' => '回撥門號未申請授權使用' ,
			'33554436' => '國際簡訊未授權使用' ,
			'33554437' => '國內簡訊未授權使用' ,
			'33554438' => '國外簡訊未授權使用' ,
			'33554439' => '合約已終止，停止使用' ,
			'33554440' => '帳號已終止，停止使用' ,
			'33554441' => '帳號已鎖定(密碼錯誤超過三次以上)' ,
			'33554448' => '未授權此功能' ,
			'33554449' => '他網國際漫遊簡訊客戶不得發送Unicode字碼' ,
			'50331649' => '參數不足' ,
			'50331650' => '交易代號不存在' ,
			'50331651' => '門號格式錯誤' ,
			'50331652' => '日期格式錯誤' ,
			'50331653' => '其他格式錯誤' ,
			'50331654' => '接收門號數量超過上限' ,
			'50331655' => '簡訊本文含有非法關鍵字' ,
			'50331656' => '簡訊長度過長' ,
			'50331657' => '長簡訊則數已超過上限' ,
			'50331664' => '簡訊主旨不存在(或空白)' ,
			'50331665' => 'API簡訊發送起始時間需晚於API呼叫時間' ,
			'50331666' => 'API簡訊發送結束時間' ,
			'50331667' => '簡訊已全部送出，無法異動(刪除簡訊失敗/預約簡訊本文修改失敗)' ,
			'50331668' => '變更密碼失敗(長度不足或過長)' ,
			'50331669' => '異動點數長度不符' ,
			'50331670' => '異動點數格式錯誤' ,
			'51450129' => '系統維護時段，暫停使用' ,
			'286331153' => '例外錯誤' ,
			'999999999' => '網路連線錯誤、請連絡相關人員'
		) ;
		
		//print_r ($code_list[]) ; exit ;
		return $code_list[$code] ;
	}
	##
	
	//亞太簡訊查詢代碼轉換表(查詢、TaskStatus)
	private function apol_return_code_TS($code) {
		$code_list = array (	
			'00' => '上傳成功' ,
			'01' => '預約中' ,
			'11' => '系統正在處裡(展開明細中)' ,
			'12' => '系統已將簡訊送至簡訊中心' ,
			'21' => '使用者取消' ,
			'22' => '非法簡訊' ,
			'23' => '點數不足' ,
			'24' => '上傳失敗' ,
			'25' => '傳送失敗' ,
			'30' => '傳送失敗(展開明細失效)' ,
			'99' => '傳送完成' 
		) ;
		
		return $code_list[$code] ;
	}
	##
	
	//亞太簡訊查詢代碼轉換表(查詢、Status)
	private function apol_return_code_S($code) {
		$code_list = array (
			'99' => '成功' ,
			'21' => '使用者取消' ,
			'25' => '傳送失敗' ,
			'26' => '逾時失敗' ,
			'27' => '空號失敗' ,
			'28' => '傳送失敗(UNKNOWN)' ,
			'29' => '傳送失敗(REJECTD)' ,
			'30' => '傳送中(尚未得到簡訊狀態)' 
		) ;
		
		return $code_list[$code] ;
	}
	##
	
	//濾除重複簡訊對象並重新排序
	public function filter_array($a,$boss=null) {
		
		$count=count($a);

		
		for ($i = 0 ; $i < $count ; $i ++) {

			if ($a[$i]['mMobile']!='') { //20150414 代理人空的太多 為了顯示出名子加上此判斷
				$b[$a[$i]['mMobile']] ++ ;
			}

			if ($b[$a[$i]['mMobile']] > 1) {

				
					unset($a[$i]) ;
				
				
			}

		}
	
		$b = array_merge($a) ;

		if (is_array($boss)) {
			$b = $this->filter_array2($b,$boss);
		}

		

		return $b ;
	}

	//
	private function filter_array2($a,$b) {
		
		$count = count($a);

		
		for ($i = 0 ; $i < $count ; $i ++) {

			for ($j=0; $j < count($b); $j++) { 
				// echo $a[$i]['mMobile']." == ".$b[$j]['mMobile']."<bR>";
				if ($a[$i]['mMobile'] == $b[$j]['mMobile']) {
					if ($a[$i]['mMobile']!='') {
						$tmp[$a[$i]['mMobile']] ++ ;
					}
				}

				if ($tmp[$a[$i]['mMobile']] > 0 || $a[$i]['mMobile'] == '') {

				
						unset($a[$i]) ;
					
					
				}
			}

			

		}
		// print_r($tmp );
		$arr = array_merge($a) ;

		return $arr;
	}
	##
	
	//判定買or賣方人數
	private function getOhterBuyerOwner($no) {
		if ($no > 1) {
			return '等'.$no.'人' ; 
		}
		else {
			return '' ;
		}
		
	}
	##
	
	//遠傳API發送回傳代碼
	private function fet_sms_code($code) {
		include_once 'sms_return_code_fet.php' ;
		
		return $return_code[$code] ;
	}
	##
	
	//決定發送系統
	private function sms_send($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid,$sys=0) {
		if ($sys == 3) {				//其他(亞太電信)
			$sms_id = $this->send_apol_sms($mobile_tel,$sms_txt,$target,$pid,$tid,$sys);
		}
		else if ($sys == 1) {			//中華電信
			$sms_id = $this->send_cht_sms($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;
		}
		else if ($sys == 2) {			//遠傳電信
			$sms_id = $this->send_fet_sms($mobile_tel,$mobile_name,$sms_txt,$target,$pid,$tid) ;
		}
		else {
			exit ;
		}
		
		return $sms_id ;
	}
	##
	
	//取得指定之電信業者
	private function getSmsSystem() {
		$_sql = 'SELECT * FROM tSmsSystem WHERE sUsed="1" ORDER BY sId ASC LIMIT 1;' ;
		
		$_getData = $this->DB_link->prepare($_sql) ;
		$_getData->execute() ;
		$_myData = $_getData->fetchALL(PDO::FETCH_ASSOC) ;
		
		return ($_myData[0]['sSystemVendorCode']) ;
	}
	##金額明細
	private function getMemo($tid,$buyerMoney,$extraMoney,$total){
		$txt = array();
		$sql = '
			SELECT
				*
			FROM
				tExpenseDetailSms 
			WHERE
				eExpenseId ="'.$tid.'"
		' ;
		
		$rs = $this->DB_link->prepare($sql) ;
		$rs->execute() ;
		$data = $rs->fetchALL(PDO::FETCH_ASSOC) ;

		
		if ($data[0]['eSignMoney'] > 0) {$txt[] = "簽約款".$data[0]['eSignMoney'];}
		if ($data[0]['eAffixMoney'] > 0) {$txt[] = "用印款".$data[0]['eAffixMoney'];}
		if ($data[0]['eDutyMoney'] > 0) {$txt[] = "完稅款".$data[0]['eDutyMoney'];}
		if ($data[0]['eEstimatedMoney'] > 0) {$txt[] = "尾款".$data[0]['eEstimatedMoney'];}
		if ($data[0]['eEstimatedMoney2'] > 0) {$txt[] = "尾款差額".$data[0]['eEstimatedMoney2'];}
		if ($data[0]['eCompensationMoney'] > 0) {$txt[] = "代償後餘額".$data[0]['eCompensationMoney'];}

		$msg['owner'] =  @implode('+', $txt);//賣方不收服務費跟溢入款也不收買方履保費、買方應付款、契稅、印花稅
		#####它項####
		$sql = '
			SELECT
				eTitle,
				eMoney
			FROM
				tExpenseDetailSmsOther  
			WHERE
				eExpenseId ="'.$tid.'" AND eDel = 0
		' ;
		
		$rs = $this->DB_link->prepare($sql) ;
		$rs->execute() ;
		$data2 = $rs->fetchALL(PDO::FETCH_ASSOC) ;

		foreach ($data2 as $k => $v) {
			
			$txt[] = $v['eTitle'].$v['eMoney'];
			
			$txtb[] = $v['eTitle'].$v['eMoney'];
			$cc += $v['eMoney'];
		}
		
		#########################

		

		if ($data[0]['eServiceFee'] > 0) {
			$txt[] ="買方仲介服務費".$data[0]['eServiceFee'];
		}

		if ($data[0]['eExtraMoney'] > 0) {
			$txt[] ="買方溢入款".$data[0]['eExtraMoney'];
		}

		if ($data[0]['eServiceFee'] == 0 && $data[0]['eExtraMoney'] == 0 && !is_array($txtb)) { //沒有買方仲介服務費跟買方溢入款 賣方不用單獨發送
			// $msg['owner'] = '';
			unset($msg['owner']);
		}

		
		
		$msg['normal'] = @implode('+', $txt);

		$money = $total - $buyerMoney - $extraMoney;

		//賣方不發送情況 只匯入買方服務費或溢入款
		if ($money == 0 || $cc == $money) {
			$msg['status'] = 1;
		}


		
		// if (($buyerMoney > 0 || $extraMoney > 0) || ($data[0]['eOtherTitle'] == '買方服務費' || $data[0]['eOtherTitle'] == '買方溢入款')) {
		// 	$money = $data[0]['eOtherMoney'] - $buyerMoney - $extraMoney;
			
		// 	if ($money > 0 && ($data[0]['eOtherTitle'] != '買方服務費' && $data[0]['eOtherTitle'] != '買方溢入款')) {
				
		// 		$msg['owner'] =  @implode('+', $txt)."+".$data[0]['eOtherTitle'].$money;
		// 	}else if (is_array($txt)) {
		// 		$msg['owner'] = @implode('+', $txt); //有XXX款項+服務費但服務費為0所以只留下前面的款項
		// 	}else{
		// 		$msg['status'] = 1; //只有服務費ˇ或溢入款狀況
		// 	}

		// }

		// if ($data[0]['eOtherMoney'] > 0) {$txt[] = "".$data[0]['eOtherTitle'].$data[0]['eOtherMoney'];}

		// if (is_array($txt)) {
		// 	$msg['normal'] = implode('+', $txt);
		// }


		if (is_array($msg)) {
			return $msg;
		}else{
			return false;
		}
		
		
	}

	private function enCrypt($str, $seed='firstfeedSms') {
		global $psiArr ;
		
		$encode = '' ;
		$rc = new Crypt_RC4 ;
		$rc->setKey($seed) ;
		$encode = $rc->encrypt($str) ;
		
		return $encode ;
	}

	private function getShortUrl($url,$key,$ok='n'){

		$sql = "SELECT * FROM tShortUrl WHERE sCategory = '0' AND sKey = '".$key."'";
		$rs = $this->DB_link->prepare($sql) ;
		$rs->execute() ;
		$ShortUrlData = $rs->fetch(PDO::FETCH_ASSOC) ;

		if ($ShortUrlData['sShortUrl'] != '') {
			return $ShortUrlData['sShortUrl'];
		}else{
			$target = "https://escrow.first1.com.tw/url/url.php";
			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $target);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("url"=>$url))); 
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch); 
				curl_close($ch);
				$data = json_decode($result,true);
				
				if ($data['code'] == 200) { //成功
					$sql = "INSERT INTO tShortUrl SET sCategory = '0',sKey = '".$key."',sUrl ='".$url."',sShortUrl = '".$data['url']."'";
					$rs = $this->DB_link->prepare($sql) ;
					$rs->execute() ;

					return $data['url'];
				}else{ //失敗就走原本的
					return $url;
				}

			

		}


		
		

		
	}
}



?>