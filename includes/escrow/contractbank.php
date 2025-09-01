<?php
//取得買或賣銀行資料
//身分(1:買 2:賣 31:賣方點交單的買方 3:仲介 32:仲介賣 33:仲介買 4:代書 42:代書賣 43:代書買 5:創世 52:其他賣 53:其他買)
function getBankData($conn, $id, $type)
{
    $sql = "SELECT
			*
		FROM
			tContractCustomerBank
		WHERE
			cCertifiedId ='" . $id . "' AND cIdentity ='" . $type . "' ORDER BY cId ASC";
    // echo $sql;
    $rs   = $conn->Execute($sql);
    $j    = 2;
    $bank = []; // 初始化陣列
    while (! $rs->EOF) {

        //要排出買賣表裡的銀行資料

        $rs->fields['menu_branch'] = getBankBranch($conn, $rs->fields['cBankMain'], $rs->fields['cBankBranch']);
        $rs->fields['num']         = $j;
        $bank[]                    = $rs->fields;
        $j++;

        $rs->MoveNext();
    }

    return $bank;
}

function updateBankData($conn, $data, $id, $type)
{

    if ($type == 1) {
        if (is_array($data['buyer_bankid2'])) {
            for ($i = 0; $i < count($data['buyer_bankid2']); $i++) {
                if ($i == 0 && $data['buyer_bankid2'][$i] == '') { //(2)有可能是新增也有可能是有的
                    if ($data['buyer_bankkey2'][$i] != '0' && $data['buyer_bankbranch2'][$i] != null && $data['buyer_bankaccnumber2'][$i] != '' && $data['buyer_bankaccname2'][$i] != '') {
                        $sql = "INSERT INTO
							tContractCustomerBank
							(
								cCertifiedId,
								cIdentity,
								cBankMain,
								cBankBranch,
								cBankAccountNo,
								cBankAccountName,
								cChecklistBank,
								cBankMoney
							) VALUES (
								'" . $id . "',
								'1',
								'" . $data['buyer_bankkey2'][$i] . "',
								'" . $data['buyer_bankbranch2'][$i] . "',
								'" . $data['buyer_bankaccnumber2'][$i] . "',
								'" . $data['buyer_bankaccname2'][$i] . "',
								'" . $data['buyer_cklist2'][$i] . "',
								'" . $data['buyer_bankMoney2'][$i] . "'
							)";
                        // echo $sql.";<bR>";
                        $conn->Execute($sql);

                    }

                } else {
                    $sql = "UPDATE
							tContractCustomerBank
						SET
							cBankMain = '" . $data['buyer_bankkey2'][$i] . "',
							cBankBranch = '" . $data['buyer_bankbranch2'][$i] . "',
							cBankAccountNo = '" . $data['buyer_bankaccnumber2'][$i] . "',
							cBankAccountName = '" . $data['buyer_bankaccname2'][$i] . "',
							cChecklistBank = '" . $data['buyer_cklist2'][$i] . "',
							cBankMoney = '" . $data['buyer_bankMoney2'][$i] . "'
						WHERE
							cId ='" . $data['buyer_bankid2'][$i] . "'";
                    // echo $sql.";<bR>";
                    $conn->Execute($sql);
                }

            }
        }

    } else {

        if (is_array($data['owner_bankid2'])) {
            for ($i = 0; $i < count($data['owner_bankid2']); $i++) {
                if ($i == 0 && $data['owner_bankid2'][$i] == '') { //(2)有可能是新增也有可能是有資料的
                                                                       //判斷是否為空值
                    if (($data['owner_bankkey2'][$i] != '0' && $data['owner_bankbranch2'][$i] != null && $data['owner_bankaccnumber2'][$i] != '' && $data['owner_bankaccname2'][$i] != '')) {
                        $sql = "INSERT INTO
							tContractCustomerBank
							(
								cCertifiedId,
								cIdentity,
								cBankMain,
								cBankBranch,
								cBankAccountNo,
								cBankAccountName,
								cChecklistBank,
								cBankMoney
							) VALUES (
								'" . $id . "',
								'2',
								'" . $data['owner_bankkey2'][$i] . "',
								'" . $data['owner_bankbranch2'][$i] . "',
								'" . $data['owner_bankaccnumber2'][$i] . "',
								'" . $data['owner_bankaccname2'][$i] . "',
								'" . $data['owner_cklist2'][$i] . "',
								'" . $data['owner_bankMoney2'][$i] . "'
							)";
                        // echo $sql.";<br>";
                        $conn->Execute($sql);
                    }

                } else {
                    $sql = "UPDATE
							tContractCustomerBank
						SET
							cBankMain = '" . $data['owner_bankkey2'][$i] . "',
							cBankBranch = '" . $data['owner_bankbranch2'][$i] . "',
							cBankAccountNo = '" . $data['owner_bankaccnumber2'][$i] . "',
							cBankAccountName = '" . $data['owner_bankaccname2'][$i] . "',
							cChecklistBank = '" . $data['owner_cklist2'][$i] . "',
							cBankMoney = '" . $data['owner_bankMoney2'][$i] . "'
						WHERE
							cId ='" . $data['owner_bankid2'][$i] . "'";
                    // echo $sql.";<bR>";
                    $conn->Execute($sql);
                }

            }
        }

    }

}

function addBankData($conn, $data, $id, $type)
{ //

    if ($type == 1) {
        if (is_array($data['newbuyer_bankid2'])) {
            for ($i = 0; $i < count($data['newbuyer_bankid2']); $i++) {

                if (($data['newbuyer_bankkey2'][$i] != '0' && $data['newbuyer_bankbranch2'][$i] != null && $data['newbuyer_bankaccnumber2'][$i] != '' && $data['newbuyer_bankaccname2'][$i] != '')) {

                    $sql = "INSERT INTO
						tContractCustomerBank
						(
							cCertifiedId,
							cIdentity,
							cBankMain,
							cBankBranch,
							cBankAccountNo,
							cBankAccountName,
							cChecklistBank,
							cBankMoney

						) VALUES (
							'" . $id . "',
							'1',
							'" . $data['newbuyer_bankkey2'][$i] . "',
							'" . $data['newbuyer_bankbranch2'][$i] . "',
							'" . $data['newbuyer_bankaccnumber2'][$i] . "',
							'" . $data['newbuyer_bankaccname2'][$i] . "',
							'" . $data['newbuyer_cklist2'][$i] . "',
							'" . $data['newbuyer_bankMoney2'][$i] . "'

						)";
                    // echo $sql.";<br>";
                    $conn->Execute($sql);
                }

            }
        }

    } elseif ($type == 2) {
        if (is_array($data['newowner_bankid2'])) {
            for ($i = 0; $i < count($data['newowner_bankid2']); $i++) {

                if (($data['newowner_bankkey2'][$i] != '0' && $data['newowner_bankbranch2'][$i] != null && $data['newowner_bankaccnumber2'][$i] != '' && $data['newowner_bankaccname2'][$i] != '')) {

                    $sql = "INSERT INTO
						tContractCustomerBank
						(
							cCertifiedId,
							cIdentity,
							cBankMain,
							cBankBranch,
							cBankAccountNo,
							cBankAccountName,
							cChecklistBank,
							cBankMoney
						) VALUES (
							'" . $id . "',
							'2',
							'" . $data['newowner_bankkey2'][$i] . "',
							'" . $data['newowner_bankbranch2'][$i] . "',
							'" . $data['newowner_bankaccnumber2'][$i] . "',
							'" . $data['newowner_bankaccname2'][$i] . "',
							'" . $data['newowner_cklist2'][$i] . "',
							'" . $data['newowner_bankMoney2'][$i] . "'

						)";
                    // echo $sql.";<br>";
                    $conn->Execute($sql);
                }

            }
        }

    }

}
