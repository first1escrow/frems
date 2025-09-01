<?php
    include_once '../../configs/config.class.php';
    include_once 'class/SmartyMain.class.php';
    include_once 'class/brand.class.php';
    include_once '../../openadodb.php';

    $_POST = escapeStr($_POST);
    $no    = $_POST['val'];

    $brand = new Brand();

    $menu_bank = $brand->GetBankMenuList();
?>
<tr class="newBankDelete<?php echo $no ?>">
    <th>總行(<?php echo $no ?>)︰</th>
    <td>
        <select name="NewBankMain[]" id="NewBankMain<?php echo $no ?>" onchange="Bankchange('NewBankMain<?php echo $no ?>','NewBankBranch<?php echo $no ?>')">
            <?php
                foreach ($menu_bank as $key => $value) {
                    echo "<option value=\"" . $key . "\">" . $value . "</option>";
                }
            ?>
        </select>

    </td>
    <th>分行(<?php echo $no ?>)︰</th>
    <td>
        <select name="NewBankBranch[]" id="NewBankBranch<?php echo $no ?>" class="input-text-per acc_disabled3">
            <option>分行</option>
        </select>
    </td>
    <th>指定帳號(<?php echo $no ?>)︰</th>
    <td>
        <input type="text" name="NewBankAccountNo[]" id="NewBankAccountNo<?php echo $no ?>" maxlength="14" class="input-text-per " value="" />
    </td>
</tr>
<tr class="newBankDelete<?php echo $no ?>">
    <th>戶名(<?php echo $no ?>)︰</th>
    <td colspan="2">
        <input type="text" name="NewBankAccountName[]" id="NewBankAccountName<?php echo $no ?>" class="input-text-per " value="" />
    </td>
    <td>
        <th><!-- 停用(<?php echo $no ?>) --></th>
    <td>
        <!-- <input type="checkbox" name="NewUnUsed[]" id="NewUnUsed<?php echo $no ?>"> -->
        <span style="float:right;">
            <a href="Javascript:void(0);" style="font-size:0.8em" onclick="newBankDelete('<?php echo $no ?>')">刪除紀錄</a>
        </span>
    </td>
</tr>