<?php
require_once dirname(__DIR__) . '/configs/config.class.php';
require_once dirname(__DIR__) . '/openadodb.php';

$action = $_GET['action']; //contract , banktrans
$bid = $_GET['bid']; //banktrans 流水編號
$certifiedId = $_GET['cid']; // 保證號碼
$today = date("Y-m-d");

$bids = null;
if($action == 'contract'){
    $bids = explode(',',$bid);
}


$kindItem = [1 => '買方', 2 => '賣方', 3 => '副總同意'];
$list = [];
$bankTrans = [];

if(!empty($_POST['id'])){
    if($_POST['save'] == 'ok'){
        $bankTransId = (int)$_POST['bankTransId'];

        if($_POST['id'] == 'new'){
            $sql = "INSERT INTO tBankTransConfirmCall (bCertifiedId,bBankTransId,bKind,bName,bPhone,bCalledAt,bMemo,bCreatedAt) VALUES ";
            $sql .= "('".$_POST['cid']."',".$bankTransId.",".$_POST['kind'].",'".$_POST['name']."','".$_POST['phone']."','".$_POST['calltime']."','','".date("Y-m-d H:i:s")."')";
            $conn->Execute($sql);
        } else {
            $sql = "UPDATE tBankTransConfirmCall SET bBankTransId = ".$bankTransId.",bKind = ".$_POST['kind']."
        ,bName = '".$_POST['name']."',bPhone = '".$_POST['phone']."',bCalledAt = '".$_POST['calltime']."' WHERE bId = ".$_POST['id'];
            $conn->Execute($sql);
        }
    } else if($_POST['delete'] == 'ok'){
        $sql = "UPDATE tBankTransConfirmCall SET bDeletedAt = '".date("Y-m-d H:i:s")."' WHERE bId = ".$_POST['id'];
        $conn->Execute($sql);
    }
}

//check
if(!in_array($action, ['contract', 'banktrans']) || strlen($certifiedId) != 9){
    exit();
}

$sql_check = "SELECT cId FROM tContractCase WHERE cCertifiedId = '".$certifiedId."' LIMIT 1";
$rs = $conn->Execute($sql_check);
if($rs->EOF){
    exit();
}

//出款
$sql = "SELECT * FROM tBankTrans WHERE tMemo = '".$certifiedId."' AND tObjKind = '賣方先動撥'";
$rs = $conn->Execute($sql);
while(!$rs->EOF)
{
    $bankTrans[] = $rs->fields;
    $rs->MoveNext();
}

//照會資料
$check_ids = null;
$sql = "SELECT a.*,b.tOk,b.tLegalAllow FROM tBankTransConfirmCall AS a LEFT JOIN tBankTrans AS b ON a.bBankTransId=b.tId WHERE a.bCertifiedId = '".$certifiedId."' AND a.bDeletedAt IS NULL ";
if($action == 'banktrans'){
    foreach ($bankTrans as $k=>$v){
        if($v['tOk'] != "1" && $v['tLegalAllow'] != "1"){
            $check_ids[] = $v['tId'];
        }
    }

    if($check_ids){
        $sql .= " AND bBankTransId in (0, ".implode(',',$check_ids).")";
    }
}
$sql .= " ORDER BY a.bCalledAt ASC,a.bId ASC";

$rs = $conn->Execute($sql);
while(!$rs->EOF)
{
    if($action == 'banktrans' && (int)$rs->fields['bBankTransId'] > 0){
        $bids[] = $rs->fields['bBankTransId'];
    }
    $list[] = $rs->fields;
    $rs->MoveNext();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>出款照會紀錄</title>
    <style>
        body {
            background-color: #eef1f5;
            font-family: "Segoe UI", sans-serif;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 25px;
            /*max-width: 900px;*/
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 150px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #9e2925;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #b11111;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f5f5f5;
        }

        .select_class,.input_class,.datetime_class {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .btn-small {
            padding: 4px 10px;
            font-size: 14px;
            background-color: #9e2925;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-small.gray {
            background-color: #ccc;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo $certifiedId;?> 出款照會紀錄</h2>

    <table>
        <thead>
        <tr>
            <th>出款項目<br/>賣方先動撥</th>
            <th><font color="red">*</font> 買/賣方</th>
            <th><font color="red">*</font> 姓名</th>
            <th><font color="red">*</font> 照會電話</th>
            <th><font color="red">*</font> 照會日期</th>
            <th>功能</th>
        </tr>
        </thead>
        <tbody id="dataTableBody">
            <?php
            if(!$list || count($list) == 0){
                echo "<tr><td colspan='6' style='color: red;'>此案件無照會資料</td></tr>";
            } else {
                foreach($list as $row){
                    $flag = false;
                    $disabled = 'disabled';
                    $trBgColor = '';
                    $bCalledAt = substr($row['bCalledAt'],0 ,10);

                    if($row['tOk'] != 1){
                        $flag = true;
                        $disabled = '';
                    }

                    if(in_array($row['bBankTransId'],$bids)){
                        $trBgColor = 'background-color: #E4BEB1';
                    }

                    $bankTransSelect = '<select class="select_class" id="bankTrans_'.$row['bId'].'" '.$disabled.'><option value="">請選擇</option>';
                    foreach ($bankTrans as $k => $v) {
                        if($flag){
                            if($v['tOk'] == 1){
                                continue;
                            }
                        }

                        if($v['tId'] == $row['bBankTransId']){
                            $bankTransSelect .= '<option value="'.$v['tId'].'" selected>'.$v['tAccountName'].'_$'.$v['tMoney'].'</option>';
                        } else {
                            $bankTransSelect .= '<option value="'.$v['tId'].'">'.$v['tAccountName'].'_$'.$v['tMoney'].'</option>';
                        }

                    }
                    $bankTransSelect .= '</select>';

                    $kindSelect = '<select class="select_class" id="kind_'.$row['bId'].'" '.$disabled.' onchange="change_data(this.value, \''.$row['bId'].'\')">';
                    foreach ($kindItem as $k => $v) {
                        $kindSelect .= ($k == $row['bKind']) ? '<option value="'.$k.'" selected>'.$v.'</option>' : '<option value="'.$k.'">'.$v.'</option>';
                    }
                    $kindSelect .= '</select>';

                    echo "<tr style='".$trBgColor."'>";
                    echo "<td>".$bankTransSelect."</td>";
                    echo "<td>".$kindSelect."</td>";
                    echo "<td><input type='text' id='name_".$row['bId']."' value='".$row['bName']."' class='input_class' ".$disabled."/></td>";
                    echo "<td><input type='text' id='phone_".$row['bId']."' value='".$row['bPhone']."' class='input_class' ".$disabled."/></td>";
                    echo "<td><input type='date' id='calltime_".$row['bId']."' placeholder='YYYY-MM-DD' max='".$today."' value='".$bCalledAt."' class='datetime_class' ".$disabled."></td>";
                    if($flag){
                        echo "<td><button class='btn-small' onclick='save(\"".$row['bId']."\")'>儲存</button>&nbsp;&nbsp;<button class='btn-small gray' onclick='delete_item(\"".$row['bId']."\")'>刪除</button></td>";
                    } else {
                        echo "<td><span style='color: red'>已審核出款</span></td>";
                    }
                    echo "<tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
<div>&nbsp;</div>
<div class="container">
    <!--<h3>新增出款照會紀錄</h3>-->
    <div class="form-row">
        <div class="form-group">
            <label for="bankTrans_new">出款項目</label>
            <select id="bankTrans_new">
                <option value="">請選擇</option>
                <?php
                foreach ($bankTrans as $k => $v) {
                    if($v['tOk'] != 1 && $v['tId'] != 1324399){
                        if($v['tId'] == $bids[0]){
                            echo '<option value="'.$v['tId'].'" selected>'.$v['tAccountName'].'_$'.$v['tMoney'].'</option>';
                        } else {
                            echo '<option value="'.$v['tId'].'">'.$v['tAccountName'].'_$'.$v['tMoney'].'</option>';
                        }
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="kind_new"><font color="red">*</font> 買賣方</label>
            <select id="kind_new" onchange="change_data(this.value, 'new')">
                <option value="">請選擇</option>
                <option value="1">買方</option>
                <option value="2">賣方</option>
                <option value="3">副總同意</option>
            </select>
        </div>
        <div class="form-group">
            <label for="name_new"><font color="red">*</font> 姓名</label>
            <input type="text" id="name_new" placeholder="輸入姓名">
        </div>
        <div class="form-group">
            <label for="phone_new"><font color="red">*</font> 照會電話</label>
            <input type="text" id="phone_new" placeholder="0987654321">
        </div>
        <div class="form-group">
            <label for="calltime_new"><font color="red">*</font> 照會時間</label>
            <input type="date" id="calltime_new" name="date" placeholder="YYYY-MM-DD" max="<?php echo $today;?>" value="<?php echo $today;?>">
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button id="addBtn" onclick="save('new')">新增紀錄</button>
        </div>
    </div>
</div>
<?php if($action == 'banktrans'){?>
    <div class="form-group">
        <label>&nbsp;</label>
        <button id="addBtn" onclick="reload_page()">關閉並更新頁面</button>
    </div>
<?php } ?>
<script>
    var action = "<?php echo $_SERVER['PHP_SELF'];?>?action=<?php echo $action;?>&cid=<?php echo $certifiedId;?>&bid=<?php echo $bid;?>";
    function save(id){
        var bankTransId = $("#bankTrans_" + id).val();
        var kind = $("#kind_" + id).val();
        var name = $("#name_" + id).val().trim();
        var phone = $("#phone_" + id).val().trim();
        var calltime = $("#calltime_" + id).val();

        if (!kind || !name || !phone || !calltime) {
            alert("資料需填寫完整！");
            return;
        } else {
            var confirm_str = (id === "new") ? "確定新增資料？" : "確定修改資料？";
            if(confirm(confirm_str)){
                var form = document.createElement("form");
                form.method = "POST";
                form.action = action;
                form.style.display = "none";

                function appendInput(name, value) {
                    var input = document.createElement("input");
                    input.type = "hidden";
                    input.name = name;
                    input.value = value;
                    form.appendChild(input);
                }

                appendInput("id", id);
                appendInput("save", "ok");
                appendInput("cid", "<?php echo $certifiedId;?>");
                appendInput("bankTransId", bankTransId);
                appendInput("kind", kind);
                appendInput("name", name);
                appendInput("phone", phone);
                appendInput("calltime", calltime);

                document.body.appendChild(form);
                form.submit();
            }
        }
    }

    function delete_item(id){
        if (id && confirm("確定刪除資料？")) {
            var form = document.createElement("form");
            form.method = "POST";
            form.action = action;
            form.style.display = "none";

            function appendInput(name, value) {
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = name;
                input.value = value;
                form.appendChild(input);
            }

            appendInput("id", id);
            appendInput("delete", "ok");

            document.body.appendChild(form);
            form.submit();
        }
    }

    function reload_page(){
        window.parent.$.colorbox.close();
        window.parent.location.reload();
    }

    function change_data(v, id){
        if(v == '3'){
            $("#name_" + id).val("曾政耀");
            $("#phone_" + id).val("0930945670");
        }
    }
</script>
<script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
</body>
</html>
