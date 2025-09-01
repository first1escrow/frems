<?php
$ext        = json_decode($v['ext'], true);
$extRecords = getActivityRecordExt($v['aId'], $act_identity, $_POST['id']);

if (empty($extRecords)) {
    //新增Ext資訊，從回饋金資訊複製過來
    if (empty($data_feedData)) {
        $_records[] = '
            <div style="padding-bottom: 10px;">
                <table>
                    <tr>
                        <th>' . $ext['data']['identity'] . '：</th>
                        <td>
                            <select name="act_identity_' . $v['aId'] . '[]">
                                <option value="1">------</option>
                                <option value="2">身份證編號</option>
                                <option value="3">統一編號</option>
                                <option value="4">居留證號碼</option>
                            </select>
                        <th>' . $ext['data']['idNo'] . '：</th>
                        <td><input type="text" name="act_idNo_' . $v['aId'] . '[]" value=""></td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['mailingAddr'] . '：</th>
                        <td colspan=3>
                            <input type="text" name="act_mailingAddrZip_' . $v['aId'] . '[]" value="">
                            <input type="text" name="act_mailingAddr_' . $v['aId'] . '[]" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['residenceAddr'] . '：</th>
                        <td colspan=3>
                            <input type="text" name="act_residenceAddrZip_' . $v['aId'] . '[]" value="">
                            <input type="text" name="act_residenceAddr_' . $v['aId'] . '[]" value="">
                        </td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['bankMain'] . '：</th>
                        <td><input type="text" name="act_bankMain_' . $v['aId'] . '[]" value=""></td>
                        <th>' . $ext['data']['bankBranch'] . '：</th>
                        <td><input type="text" name="act_bankBranch_' . $v['aId'] . '[]" value=""></td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['bankAccount'] . '：</th>
                        <td><input type="text" name="act_bankAccount_' . $v['aId'] . '[]" value=""></td>
                        <th>' . $ext['data']['bankAccountName'] . '：</th>
                        <td><input type="text" name="act_bankAccountName_' . $v['aId'] . '[]" value=""></td>
                    </tr>
                </table>
            </div>
        ';
    } else {
        foreach ($data_feedData as $idx => $feedback) {
            if (($feedback['fStatus'] == 1) || ($feedback['fStop'] == 1)) {
                continue;
            }

            $_bankMain = '';
            foreach ($menu_bank as $ka => $va) {
                $_bankMain .= '<option value="' . $ka . '"';
                $_bankMain .= ($ka == $feedback['fAccountNum']) ? ' selected="selected">' : '>';
                $_bankMain .= $va . '</option>' . "\n";
            }

            $_records[] = '
                <div style="padding-bottom: 10px;">
                    <table>
                        <tr>
                            <th>' . $ext['data']['identity'] . '：</th>
                            <td>
                                <select name="act_identity_' . $v['aId'] . '[]">
                                    <option value="1"' . (($feedback['fIdentity'] == 1) ? ' selected="selected"' : '') . '>------</option>
                                    <option value="2"' . (($feedback['fIdentity'] == 2) ? ' selected="selected"' : '') . '>身份證編號</option>
                                    <option value="3"' . (($feedback['fIdentity'] == 3) ? ' selected="selected"' : '') . '>統一編號</option>
                                    <option value="4"' . (($feedback['fIdentity'] == 4) ? ' selected="selected"' : '') . '>居留證號碼</option>
                                </select>
                            </td>
                            <th>' . $ext['data']['idNo'] . '：</th>
                            <td><input type="text" name="act_idNo_' . $v['aId'] . '[]" value="' . $feedback['fIdentityNumber'] . '"></td>
                        </tr>
                        <tr>
                            <th>' . $ext['data']['mailingAddr'] . '：</th>
                            <td colspan=3>
                                <input type="hidden" name="act_mailingAddrZip_' . $v['aId'] . '[]" id="act_mailingAddrZip_' . $v['aId'] . $idx . '" value="' . $feedback['fZipC'] . '"/>
                                <input type="text" maxlength="6" id="act_mailingAddrZip_' . $v['aId'] . $idx . 'F" class="input-text-sml text-center" readonly="readonly" value="' . $feedback['fZipC'] . '"/>

                                <select class="input-text-big" name="act_feedBackCountry_' . $v['aId'] . '' . $idx . '" id="act_feedBackCountry_' . $v['aId'] . '' . $idx . '" onchange="getArea2(\'act_feedBackCountry_' . $v['aId'] . '' . $idx . '\',\'act_feedbackArea_' . $v['aId'] . '' . $idx . '\',\'act_mailingAddrZip_' . $v['aId'] . '' . $idx . '\')" >
                                    ' . $feedback['countryC'] . '
                                </select>

                                <select class="input-text-big" name="act_feedbackArea_' . $v['aId'] . '' . $idx . '" id="act_feedbackArea_' . $v['aId'] . '' . $idx . '" onchange="getZip2(\'act_feedbackArea_' . $v['aId'] . '' . $idx . '\',\'act_mailingAddrZip_' . $v['aId'] . '' . $idx . '\')">
                                    ' . $feedback['areaC'] . '
                                </select>
                                </span>
                                <input type="text" name="act_mailingAddr_' . $v['aId'] . '[]" value="' . $feedback['fAddrC'] . '">
                            </td>
                        </tr>
                        <tr>
                            <th>' . $ext['data']['residenceAddr'] . '：</th>
                            <td colspan=3>
                                <input type="hidden" name="act_residenceAddrZip_' . $v['aId'] . '[]" id="act_residenceAddrZip_' . $v['aId'] . '' . $idx . '" value="' . $feedback['fZipR'] . '"/>
                                <input type="text" maxlength="6" id="act_residenceAddrZip_' . $v['aId'] . '' . $idx . 'F" class="input-text-sml text-center" readonly="readonly" value="' . $feedback['fZipR'] . '"/>

                                <select class="input-text-big" name="act_feedBackCountryR_' . $v['aId'] . '' . $idx . '" id="act_feedBackCountryR_' . $v['aId'] . '' . $idx . '" onchange="getArea2(\'act_feedBackCountryR_' . $v['aId'] . '' . $idx . '\',\'act_feedbackAreaR_' . $v['aId'] . '' . $idx . '\',\'act_residenceAddrZip_' . $v['aId'] . '' . $idx . '\')" >
                                    ' . $feedback['countryR'] . '
                                </select>

                                <select class="input-text-big" name="act_feedbackAreaR_' . $v['aId'] . '' . $idx . '" id="act_feedbackAreaR_' . $v['aId'] . '' . $idx . '" onchange="getZip2(\'act_feedbackAreaR_' . $v['aId'] . '' . $idx . '\',\'act_residenceAddrZip_' . $v['aId'] . '' . $idx . '\')">
                                    ' . $feedback['areaR'] . '
                                </select>
                                </span>
                                <input type="text" name="act_residenceAddr_' . $v['aId'] . '[]" value="' . $feedback['fAddrR'] . '">
                            </td>
                        </tr>
                        <tr>
                            <th>' . $ext['data']['bankMain'] . '：</th>
                            <td>
                                <select name="act_bankMain_' . $v['aId'] . '[]" id="act_bankMain_' . $v['aId'] . '' . $idx . '" onchange="Bankchange(\'act_bankMain_' . $v['aId'] . '' . $idx . '\',\'act_bankBranch_' . $v['aId'] . '' . $idx . '\')" class="input-text-per">
                                    ' . $_bankMain . '
                                </select>
                            </td>
                            <th>' . $ext['data']['bankBranch'] . '：</th>
                            <td>
                                <select name="act_bankBranch_' . $v['aId'] . '[]" id="act_bankBranch_' . $v['aId'] . '' . $idx . '" class="input-text-per">
                                    ' . $feedback['bank_branch'] . '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>' . $ext['data']['bankAccount'] . '：</th>
                            <td><input type="text" name="act_bankAccount_' . $v['aId'] . '[]" value="' . $feedback['fAccount'] . '"></td>
                            <th>' . $ext['data']['bankAccountName'] . '：</th>
                            <td><input type="text" name="act_bankAccountName_' . $v['aId'] . '[]" value="' . $feedback['fAccountName'] . '"></td>
                        </tr>
                    </table>
                </div>
            ';
        }
    }
} else {
    //有資料
    // print_r($extRecords);exit;
    $_records = [];
    foreach ($extRecords as $idx => $feedback) {
        $_bankMain = '';
        foreach ($menu_bank as $ka => $va) {
            $_bankMain .= '<option value="' . $ka . '"';
            $_bankMain .= ($ka == $feedback['bankMain']) ? ' selected="selected">' : '>';
            $_bankMain .= $va . '</option>' . "\n";
        }

        $_countryC    = listCity($conn, $feedback['mailingAddrZip']);
        $_areaC       = listArea($conn, $feedback['mailingAddrZip']);
        $_countryR    = listCity($conn, $feedback['residenceAddrZip']);
        $_areaR       = listArea($conn, $feedback['residenceAddrZip']);
        $_bank_branch = getBankBranch($conn, $feedback['bankMain'], $feedback['bankBranch']);

        $_records[] = '
            <div style="padding-bottom: 10px;">
                <table>
                    <tr>
                        <th>' . $ext['data']['identity'] . '：</th>
                        <td>
                            <select name="act_identity_' . $v['aId'] . '[]">
                                <option value="1"' . (($feedback['identity'] == 1) ? ' selected="selected"' : '') . '>------</option>
                                <option value="2"' . (($feedback['identity'] == 2) ? ' selected="selected"' : '') . '>身份證編號</option>
                                <option value="3"' . (($feedback['identity'] == 3) ? ' selected="selected"' : '') . '>統一編號</option>
                                <option value="4"' . (($feedback['identity'] == 4) ? ' selected="selected"' : '') . '>居留證號碼</option>
                            </select>
                        </td>
                        <th>' . $ext['data']['idNo'] . '：</th>
                        <td><input type="text" name="act_idNo_' . $v['aId'] . '[]" value="' . $feedback['idNo'] . '"></td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['mailingAddr'] . '：</th>
                        <td colspan=3>
                            <input type="hidden" name="act_mailingAddrZip_' . $v['aId'] . '[]" id="act_mailingAddrZip_' . $v['aId'] . $idx . '" value="' . $feedback['mailingAddrZip'] . '"/>
                            <input type="text" maxlength="6" id="act_mailingAddrZip_' . $v['aId'] . $idx . 'F" class="input-text-sml text-center" readonly="readonly" value="' . $feedback['mailingAddrZip'] . '"/>

                            <select class="input-text-big" name="act_feedBackCountry_' . $v['aId'] . '' . $idx . '" id="act_feedBackCountry_' . $v['aId'] . '' . $idx . '" onchange="getArea2(\'act_feedBackCountry_' . $v['aId'] . '' . $idx . '\',\'act_feedbackArea_' . $v['aId'] . '' . $idx . '\',\'act_mailingAddrZip_' . $v['aId'] . '' . $idx . '\')" >
                                ' . $_countryC . '
                            </select>

                            <select class="input-text-big" name="act_feedbackArea_' . $v['aId'] . '' . $idx . '" id="act_feedbackArea_' . $v['aId'] . '' . $idx . '" onchange="getZip2(\'act_feedbackArea_' . $v['aId'] . '' . $idx . '\',\'act_mailingAddrZip_' . $v['aId'] . '' . $idx . '\')">
                                ' . $_areaC . '
                            </select>
                            </span>
                            <input type="text" name="act_mailingAddr_' . $v['aId'] . '[]" value="' . $feedback['mailingAddr'] . '">
                        </td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['residenceAddr'] . '：</th>
                        <td colspan=3>
                            <input type="hidden" name="act_residenceAddrZip_' . $v['aId'] . '[]" id="act_residenceAddrZip_' . $v['aId'] . '' . $idx . '" value="' . $feedback['residenceAddrZip'] . '"/>
                            <input type="text" maxlength="6" id="act_residenceAddrZip_' . $v['aId'] . '' . $idx . 'F" class="input-text-sml text-center" readonly="readonly" value="' . $feedback['residenceAddrZip'] . '"/>

                            <select class="input-text-big" name="act_feedBackCountryR_' . $v['aId'] . '' . $idx . '" id="act_feedBackCountryR_' . $v['aId'] . '' . $idx . '" onchange="getArea2(\'act_feedBackCountryR_' . $v['aId'] . '' . $idx . '\',\'act_feedbackAreaR_' . $v['aId'] . '' . $idx . '\',\'act_residenceAddrZip_' . $v['aId'] . '' . $idx . '\')" >
                                ' . $_countryR . '
                            </select>

                            <select class="input-text-big" name="act_feedbackAreaR_' . $v['aId'] . '' . $idx . '" id="act_feedbackAreaR_' . $v['aId'] . '' . $idx . '" onchange="getZip2(\'act_feedbackAreaR_' . $v['aId'] . '' . $idx . '\',\'act_residenceAddrZip_' . $v['aId'] . '' . $idx . '\')">
                                ' . $_areaR . '
                            </select>
                            </span>
                            <input type="text" name="act_residenceAddr_' . $v['aId'] . '[]" value="' . $feedback['residenceAddr'] . '">
                        </td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['bankMain'] . '：</th>
                        <td>
                            <select name="act_bankMain_' . $v['aId'] . '[]" id="act_bankMain_' . $v['aId'] . '' . $idx . '" onchange="Bankchange(\'act_bankMain_' . $v['aId'] . '' . $idx . '\',\'act_bankBranch_' . $v['aId'] . '' . $idx . '\')" class="input-text-per">
                                ' . $_bankMain . '
                            </select>
                        </td>
                        <th>' . $ext['data']['bankBranch'] . '：</th>
                        <td>
                            <select name="act_bankBranch_' . $v['aId'] . '[]" id="act_bankBranch_' . $v['aId'] . '' . $idx . '" class="input-text-per">
                                ' . $_bank_branch . '
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>' . $ext['data']['bankAccount'] . '：</th>
                        <td><input type="text" name="act_bankAccount_' . $v['aId'] . '[]" value="' . $feedback['bankAccount'] . '"></td>
                        <th>' . $ext['data']['bankAccountName'] . '：</th>
                        <td><input type="text" name="act_bankAccountName_' . $v['aId'] . '[]" value="' . $feedback['bankAccountName'] . '"></td>
                    </tr>
                </table>
            </div>
        ';
    }
}

$activities[$v['aId']]['extRecords'] = $_records;

$ext = $extRecords = $_records = null;
unset($ext, $extRecords);
