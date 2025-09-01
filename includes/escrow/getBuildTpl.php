<?php
    require_once dirname(dirname(__DIR__)) . '/configs/config.class.php';
    require_once dirname(dirname(__DIR__)) . '/class/contract.class.php';
    require_once dirname(dirname(__DIR__)) . '/class/getAddress.php';
    require_once dirname(dirname(__DIR__)) . '/session_check.php';
    require_once dirname(dirname(__DIR__)) . '/openadodb.php';

    // 輔助函數：安全地訪問陣列索引
    function safe_value($array, $key, $default = '')
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    $contract = new Contract();

    // 檢查必要的 POST 變數是否存在
    $item       = isset($_POST['item']) ? $_POST['item'] : '';
    $limit_show = isset($_POST['limit']) ? $_POST['limit'] : '';

    // 安全處理 data 參數
    $data_property = [];
    if (isset($_POST['data']) && ! empty($_POST['data'])) {
        $data_property = json_decode($_POST['data'], true) ?: [];
    }

    // 設置默認值以避免 undefined array key 警告
    $cZip = safe_value($data_property, 'cZip');

    $property_country = listCity($conn, $cZip);
    $property_area    = ($cZip == '') ? '' : listArea($conn, $cZip); //建物區域
    $list_ObjUse      = $contract->GetObjUse();
    $list_material    = $contract->GetMaterialsList();
    $menu_material    = $contract->ConvertOption($list_material, 'bTypeId', 'bTypeName');
    $list_objkind     = $contract->GetObjKind();
    $menu_objkind     = $contract->ConvertOption($list_objkind, 'oTypeId', 'oTypeName');

?>

<table border="0" width="100%"  class="newP">
    <tr>
        <td colspan="6" class="tb-title">
            產品資料 > 建物標示
        </td>
    </tr>
    <tr>
        <th>建物門牌︰</th>
        <td colspan="5">
            <input type="hidden" name="new_property_Item[]" value="<?php echo $item ?>">
            <input type="hidden" name="new_property_zip<?php echo $item ?>" id="new_property_zip<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cZip') ?>"/>
            <input type="text" maxlength="6" name="new_property_zip<?php echo $item ?>F"  class="input-text-sml text-center pZip" readonly="readonly" value="<?php echo safe_value($data_property, 'cZip') ?>"/>
            <select class="input-text-big" name="new_property_country<?php echo $item ?>"  onchange="getArea('new_property_country<?php echo $item ?>','new_property_area<?php echo $item ?>','new_property_zip<?php echo $item ?>')">
            <?php echo $property_country ?>
            </select>
            <span id="new_property_area<?php echo $item ?>R">
            <select class="input-text-big" name="new_property_area<?php echo $item ?>" onchange="getZip('new_property_area<?php echo $item ?>','new_property_zip<?php echo $item ?>')">
            <?php echo $property_area ?>
            </select>
            </span>
            <input style="width:330px;" class="pAddr" name="new_property_addr<?php echo $item ?>" id="new_property_addr<?php echo $item ?>" onkeyup="checkAddr('new')" value="<?php echo safe_value($data_property, 'cAddr') ?>"/>
        </td>
    </tr>
    <tr>
        <th valign="top">建物座落土地︰</th>

        <td colspan="5" id="new_building_land_<?php echo $item ?>">
            <div style="text-align:right;">
                <a href="Javascript:void(0);" style="font-size:10pt;padding:right:10px;" onclick="cloneNewBuildingLand(<?php echo $item ?>)">新增建物座落地號</a>
            </div>
            <div class="new_building_land_<?php echo $item ?>" style="padding-bottom:5px;">
                <span>段︰</span>
                <span><input type="text" style="width:130px;padding-right:20px;" name="new_buildingLandSession_<?php echo $item ?>[]" id="" value=""></span>
                <span>小段︰</span>
                <span><input type="text" style="width:130px;padding-right:20px;" name="new_buildingLandSessionExt_<?php echo $item ?>[]" id="" value=""></span>
                <span>建物座落地號︰</span>
                <span><input type="text" style="width:130px;" name="new_buildingLandNo_<?php echo $item ?>[]" id="" value=""></span>
                <span>
                    <a href="Javascript:void(0);" style="font-size:10pt;" onclick="deleteBuildingLand(this)">刪除</a>
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <th>主要用途︰</th>
        <td colspan="5">
            <?php
                foreach ($list_ObjUse as $key => $value) {
                    $checked = "";
                    $cObjUse = safe_value($data_property, 'cObjUse', []);
                    if (is_array($cObjUse)) {
                        $checked = (in_array($value['uId'], $cObjUse)) ? "checked=checked" : "";
                    }

                    echo "<input type=\"checkbox\" name=\"new_property_objuse" . $item . "[]\" value=\"" . $value['uId'] . "\" " . $checked . ">" . $value['uName'];
                }

                $cOther  = safe_value($data_property, 'cOther');
                $checked = ($cOther != '') ? "checked=checked" : "";
            ?>

            <input type="checkbox" name="new_property_cIsOther<?php echo $item ?>" value="1"<?php echo $checked ?>/>
            其它
            <input type="text" name="new_property_cOther<?php echo $item ?>" class="input-text-big"  value="<?php echo $cOther ?>"/>
        </td>
    </tr>
    <tr>
        <th>建號︰</th>
        <td>
            <input type="text" name="new_property_buildno<?php echo $item ?>" maxlength="16" class="input-text-big" value="<?php echo safe_value($data_property, 'cBuildNo') ?>"/>
        </td>
        <th><span class="th_title_sml">建築完成日期︰</span></th>
        <td>
            <input type="text" name="new_property_builddate<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cBuildDate') ?>" onclick="showdate(form_case.new_property_builddate<?php echo $item ?>,form_case.new_property_buildage<?php echo $item ?>)" maxlength="10" class="calender input-text-big" onchange="build_age('new_property_builddate<?php echo $item ?>','new_property_buildage<?php echo $item ?>')" />
        </td>
        <th>主要建材︰</th>
        <td>
            <select name="new_property_budmaterial<?php echo $item ?>" id="">
            <option value=""></option>
            <?php
                foreach ($menu_material as $key => $value) {
                    $checked = (safe_value($data_property, 'cBudMaterial') == $key) ? "selected=selected" : "";
                    echo "<option value=\"" . $key . "\" " . $checked . " >" . $value . "</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>樓層/總樓層︰</th>
        <td colspan="3">
            <input type="text" name="new_property_levelnow<?php echo $item ?>" maxlength="6" class="input-text-mid" value="<?php echo safe_value($data_property, 'cLevelNow') ?>" /> /
            <input type="text" name="new_property_levelhighter<?php echo $item ?>" maxlength="3" class="input-text-sml" value="<?php echo safe_value($data_property, 'cLevelHighter') ?>"/>
            &nbsp;&nbsp;
            <?php
                $checked = (safe_value($data_property, 'cTownHouse') == 1) ? "checked=checked" : "";
            ?>
            <input type="checkbox" name='new_property_housetown<?php echo $item ?>' value='1'<?php echo $checked ?>/> 透天厝
        </td>
        <th>出賣權利範圍</th>
        <td>
        <input type="text" name="new_property_power1<?php echo $item ?>"  size="8" value="<?php echo safe_value($data_property, 'cPower1') ?>">&nbsp;/&nbsp;<input type="text" value="<?php echo safe_value($data_property, 'cPower2') ?>" name="new_property_power2<?php echo $item ?>"size="8" >
        </td>
    </tr>
    <tr>
        <th>產品面積︰</th>
        <td>
            <input type="text" name="new_property_measuretotal<?php echo $item ?>" maxlength="10" value="<?php echo safe_value($data_property, 'cMeasureTotal') ?>" size="10" class="input-text-big text-right" readonly/>M<sup>2</sup>
        </td>
        <th><span class="th_title_sml">隨同主建物轉移<br>共同使用部分︰</span></th>
        <td colspan="3">面積：<input type="text" name="new_property_publicmeasuretotal<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cPublicMeasureTotal') ?>" size="8">&nbsp;持分<input type="text" name="new_property_publicmeasuremain" size="8" value="<?php echo safe_value($data_property, 'cPublicMeasureMain') ?>"></td>
    </tr>
    <tr>
        <th>主要類型︰</th>
        <td>
            <select name="new_property_objkind<?php echo $item ?>" id="">
            <?php
                foreach ($menu_objkind as $key => $value) {
                    $checked = (safe_value($data_property, 'cObjKind') == $key) ? "selected=selected" : "";
                    echo "<option value=\"" . $key . "\" " . $checked . ">" . $value . "</option>";
                }
            ?>
            </select>
        </td>
        <td></td>
        <td></td>
        <th>房/廳/衛︰</th>
        <td>
            <input type="text" name="new_property_room<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cRoom') ?>" maxlength="2" size="3" class="input-text-sml text-right"  /> /
            <input type="text" name="new_property_parlor<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cParlor') ?>" maxlength="2" size="3" class="input-text-sml text-right" /> /
            <input type="text" name="new_property_toilet<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cToilet') ?>" maxlength="2" size="3" class="input-text-sml text-right" />
        </td>
    </tr>
    <tr>
        <th>屋齡︰</th>
        <td>
            <input type="text" name="new_property_buildage<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cBuildAge') ?>" maxlength="3" size="3" class="input-text-sml text-right" />年
        </td>
        <th>交屋日︰</th>
        <td>
            <input type="text" name="new_property_closingday<?php echo $item ?>" value="<?php echo safe_value($data_property, 'cClosingDay') ?>" onclick="showdate(form_case.new_property_closingday<?php echo $item ?>)" maxlength="10" class="calender date-field input-text-big" id="new_property_closingday<?php echo $item ?>" style="width:100px" />
            <a href="#new_property_closingday<?php echo $item ?>" onclick="closingday('new_property_closingday<?php echo $item ?>')">
                <img src="/images/ng.png" title="清除">
            </a>
        </td>
        <td colspan="2">

        </td>
    </tr>
</table>