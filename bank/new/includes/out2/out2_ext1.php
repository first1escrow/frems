<?php
//20231122 當選擇相關出款、且有隨案付款者，將保證費帳號改為中繼帳號
if (in_array($radiokind, ['點交', '解除契約', '建經發函終止', '預售屋']) && !empty($pay_by_case) && $pay_by_case['detail']['total'] != 0) {
    $sql = 'SELECT cBankMain, cBankBranch, cBankAccount, cAccountName FROM tContractRelayBank';
    $rs  = $conn->Execute($sql);

    //第一建經活儲
    $_account_name = $rs->fields['cAccountName']; //帳戶
    $_account_no   = $rs->fields['cBankAccount']; //帳號
    $main_bank     = $rs->fields['cBankMain']; //總行代碼
    $branch_bank   = $rs->fields['cBankBranch']; //分行代碼

    $rs = null;unset($rs);
}

switch ($radiokind) {
    case "賣方先動撥":
        //主賣方
        $i     = 1;
        $index = 0;
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $index++;
        }

        $i = count($_a);
        ##
        break;
    case "點交":
        $index = 0;

        //賣方
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $_ab5[$index] = $ownerBank[$c]['bankMoney'];
            $index++;
        }
        ##

        //買方
        for ($c = 0; $c < count($buyerBank); $c++) {
            $_a[$index]   = '買方';
            $_an[$index]  = $buyerBank[$c]['bankAccName'];
            $_ac[$index]  = $buyerBank[$c]['bankAccNum'];
            $_ab3[$index] = $buyerBank[$c]['bank'];
            $_ab4[$index] = $buyerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }
        ##

        //第一建經活儲
        $_an[$index] = $_account_name; //帳戶
        $_ac[$index] = $_account_no; //帳號

        $_ab3[$index] = $main_bank; //總行代碼
        $_ab4[$index] = $branch_bank; //分行代碼
        $_af[$index]  = ''; //傳真電話
        $_ae[$index]  = ''; //E-Mail
        $_a[$index]   = '保證費';

        $index++;
        ##

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }
        ##

        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);

        break;

    case "解除契約":
        $index = 0;
        //賣方
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //買方
        for ($c = 0; $c < count($buyerBank); $c++) {
            $_a[$index]   = '買方';
            $_an[$index]  = $buyerBank[$c]['bankAccName'];
            $_ac[$index]  = $buyerBank[$c]['bankAccNum'];
            $_ab3[$index] = $buyerBank[$c]['bank'];
            $_ab4[$index] = $buyerBank[$c]['bankBranch'];
            $index++;
        }

        //代書
        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }
        ##

        //第一建經活儲帳戶、帳號
        $_an[$index]  = $_account_name; //帳戶
        $_ac[$index]  = $_account_no; //帳號
        $_ab3[$index] = $main_bank; //銀行總行代碼
        $_ab4[$index] = $branch_bank; //銀行分行代碼
        $_af[$index]  = ''; //傳真電話
        $_ae[$index]  = ''; //email
        $_a[$index]   = '保證費';
        $index++;
        ##

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }
        ##

        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);

        break;

    case "扣繳稅款":
        $index = 0;

        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }

        $i = count($_a);
        break;
    case "仲介服務費":
        $index = 0;
        //$_a = 身分集合

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }

        ##
        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);
        break;
    case "代清償":
        $_a[0]   = "";
        $_an[0]  = "";
        $_ac[0]  = "";
        $_ab3[0] = "";
        $_ab4[0] = "";
        $i       = 1;
        break;

    case '保留款撥付':
        $index = 0;

        //賣方
        //賣方
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $index++;
        }

        ##
        //買方
        for ($c = 0; $c < count($buyerBank); $c++) {
            $_a[$index]   = '買方';
            $_an[$index]  = $buyerBank[$c]['bankAccName'];
            $_ac[$index]  = $buyerBank[$c]['bankAccNum'];
            $_ab3[$index] = $buyerBank[$c]['bank'];
            $_ab4[$index] = $buyerBank[$c]['bankBranch'];
            $index++;
        }

        ##
        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }
        ##

        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);
        ##

        break;
    case '建經發函終止':
        $index = 0;

        //賣方
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //買方
        for ($c = 0; $c < count($buyerBank); $c++) {
            $_a[$index]   = '買方';
            $_an[$index]  = $buyerBank[$c]['bankAccName'];
            $_ac[$index]  = $buyerBank[$c]['bankAccNum'];
            $_ab3[$index] = $buyerBank[$c]['bank'];
            $_ab4[$index] = $buyerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //代書
        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }
        ##

        //第一建經活儲帳戶、帳號
        $_an[$index]  = $_account_name; //帳戶
        $_ac[$index]  = $_account_no; //帳號
        $_ab3[$index] = $main_bank; //銀行總行代碼
        $_ab4[$index] = $branch_bank; //銀行分行代碼
        $_af[$index]  = ''; //傳真電話
        $_ae[$index]  = ''; //email
        $_a[$index]   = '保證費';

        $index++;
        ##

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }
        ##

        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }
        ##

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }
        ##

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);

        break;
    case '預售屋':
        $index = 0;

        //賣方
        for ($c = 0; $c < count($ownerBank); $c++) {
            $_a[$index]   = '賣方';
            $_an[$index]  = $ownerBank[$c]['bankAccName'];
            $_ac[$index]  = $ownerBank[$c]['bankAccNum'];
            $_ab3[$index] = $ownerBank[$c]['bank'];
            $_ab4[$index] = $ownerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //買方
        for ($c = 0; $c < count($buyerBank); $c++) {
            $_a[$index]   = '買方';
            $_an[$index]  = $buyerBank[$c]['bankAccName'];
            $_ac[$index]  = $buyerBank[$c]['bankAccNum'];
            $_ab3[$index] = $buyerBank[$c]['bank'];
            $_ab4[$index] = $buyerBank[$c]['bankBranch'];
            $index++;
        }
        ##

        //代書帳戶名稱
        for ($c = 0; $c < count($scrivenerBank); $c++) {
            $_an[$index]  = $scrivenerBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $scrivenerBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $scrivenerBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $scrivenerBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = ''; //代書傳真電話
            $_ae[$index]  = $_s_email; //代書E-Mail
            $_a[$index]   = '地政士'; //身分title
            $index++;
        }
        ##

        //第一建經活儲、
        $_an[$index] = $_account_name; //帳戶
        $_ac[$index] = $_account_no; //帳號

        $_ab3[$index] = $main_bank; //總行代碼
        $_ab4[$index] = $branch_bank; //分行代碼
        $_af[$index]  = ''; //傳真電話
        $_ae[$index]  = ''; //E-Mail
        $_a[$index]   = '保證費';

        $index++;
        ##

        //第一家仲介
        for ($c = 0; $c < count($branchBank); $c++) {
            $_sn[$index]  = $storeName; //店家名稱
            $_si[$index]  = $storeId; //店家編號
            $_st[$index]  = $_store_target; //店家服務對象
            $_an[$index]  = $branchBank[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_fax; //傳真電話
            $_ae[$index]  = $_store_email; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target]; //身分title
            $index++;
        }
        ##

        //第二間仲介
        for ($c = 0; $c < count($branchBank1); $c++) {
            $_sn[$index]  = $storeName1; //店家名稱
            $_si[$index]  = $storeId1; //店家編號
            $_st[$index]  = $_store_target1; //店家服務對象
            $_an[$index]  = $branchBank1[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank1[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank1[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank1[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxA; //傳真電話
            $_ae[$index]  = $_store_emailA; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target1]; //身分title
            $index++;
        }
        ##

        //第三家仲介
        for ($c = 0; $c < count($branchBank2); $c++) {
            $_sn[$index]  = $storeName2; //店家名稱
            $_si[$index]  = $storeId2; //店家編號
            $_st[$index]  = $_store_target2; //店家服務對象
            $_an[$index]  = $branchBank2[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank2[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank2[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank2[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxB; //傳真電話
            $_ae[$index]  = $_store_emailB; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target2]; //身分title
            $index++;
        }
        ##

        //第四家仲介
        for ($c = 0; $c < count($branchBank3); $c++) {
            $_sn[$index]  = $storeName3; //店家名稱
            $_si[$index]  = $storeId3; //店家編號
            $_st[$index]  = $_store_target3; //店家服務對象
            $_an[$index]  = $branchBank3[$c]['bankAccName']; //帳戶
            $_ac[$index]  = $branchBank3[$c]['bankAccNum']; //帳號
            $_ab3[$index] = $branchBank3[$c]['bank']; //銀行總行代碼
            $_ab4[$index] = $branchBank3[$c]['bankBranch']; //分行代碼
            $_af[$index]  = $_store_faxC; //傳真電話
            $_ae[$index]  = $_store_emailC; //E-Mail
            $_a[$index]   = '仲介' . $realtyTarget[$_store_target3]; //身分title
            $index++;
        }
        ##

        $i = count($_a);

        break;
    case '履保費先收(結案回饋)':

        $index = 0;

        //第一建經活儲
        $_an[$index] = $_account_name; //帳戶
        $_ac[$index] = $_account_no; //帳號

        $_ab3[$index] = $main_bank; //總行代碼
        $_ab4[$index] = $branch_bank; //分行代碼
        $_af[$index]  = ''; //傳真電話
        $_ae[$index]  = ''; //E-Mail
        $_a[$index]   = '保證費';

        $index++;
        ##

        $i = count($_a);

        break;
}

if ($i == 0) {
    $i = 1;
}