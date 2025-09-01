<?php
require_once dirname(dirname(__DIR__)) . '/session_check.php';
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

$action = empty($_GET['action']) ? '' : $_GET['action'];
if (!in_array($action, ['mailing', 'register'])) {
    exit('<center>無法判斷資料</center>');
}

$conn = new first1DB;

$id = $_SESSION['member_id'];

$alert = '';
if ($_POST['save'] == 'ok') {
    // echo '<pre>';
    // print_r($_POST);exit;

    $zip     = $_POST['selectDistrict'];
    $address = $_POST['address'];

    $sql = 'INSERT INTO
                tPeopleInfoDetail
                (pStaffId, p' . ucfirst($action) . 'Zip, p' . ucfirst($action) . 'Address)
            VALUES
                (:staff, :zip, :address)
            ON DUPLICATE KEY UPDATE
                p' . ucfirst($action) . 'Zip = :zip,
                p' . ucfirst($action) . 'Address = :address;';
    $bind = [
        'staff'   => $_SESSION['member_id'],
        'zip'     => $zip,
        'address' => $address,
    ];

    $alert = $conn->exeSql($sql, $bind) ? 'alert("儲存成功");' : 'alert("儲存失敗");';
}

$selected = '';

$sql  = 'SELECT p' . ucfirst($action) . 'Zip AS zip, p' . ucfirst($action) . 'Address AS address FROM tPeopleInfoDetail WHERE pStaffId = :staff;';
$bind = ['staff' => $_SESSION['member_id']];
$rs   = $conn->one($sql, $bind);

$zip     = empty($rs['zip']) ? '' : $rs['zip'];
$address = empty($rs['address']) ? '' : $rs['address'];

if (!empty($zip)) {
    $sql      = 'SELECT zCity FROM tZipArea WHERE zZip = :zip;';
    $rs       = $conn->one($sql, ['zip' => $zip]);
    $selected = $rs['zCity'];
}

$sql = 'SELECT zCity FROM tZipArea GROUP BY zCity;';
$rs  = $conn->all($sql);

$city_options = '<option value="">請選擇</option>';
foreach ($rs as $row) {
    $city_options .= '<option value="' . $row['zCity'] . '"';
    $city_options .= ($selected == $row['zCity']) ? ' selected' : '';
    $city_options .= '>' . $row['zCity'] . '</option>';
}

$title = ($action == 'mailing') ? '通訊' : '戶籍';
?>

<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>第一建經人員基本資料</title>
    <!------------------------- RWD open ------------------------->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta http-equiv="X-UA-Compatible" content="IE=11; IE=10; IE=9; IE=8; IE=7" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <!--Google icon-->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <style>
    .item {
        padding: 5px;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    </style>
</head>

<body>
    <div style="height:30px;"></div>
    <div id="area" class="container">
        <div class="card">
            <div class="card-header"><?=$title?>地址</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" id="action" name="action" value="<?=$action?>">

                    <div class="item">
                        <span>
                            <select id="selectCity" name="selectCity" onchange="getDistrict()">
                                <?=$city_options?>
                            </select>
                            <select id="selectDistrict" name="selectDistrict">
                                <option value="">請選擇</option>
                            </select>
                        </span>
                        <div style="margin-top:20px;">
                            <input type="text" id="address" name="address" style="width: 300px;" placeholder="請輸入地址"
                                value="<?=$address?>">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer" style="text-align:center;">
                <button type="button" class="btn btn-primary" style="width:100px;" onclick="store()">儲存</button>
                <button type="button" class="btn btn-info" style="width:100px;" onclick="cancel()">返回</button>
            </div>
        </div>
    </div>
</body>

</html>
<script type="text/javascript">
<?php echo $alert; ?>

$(document).ready(function() {
    <?php if (!empty($selected)) {?>
    getDistrict("<?=$zip?>");
    <?php }?>
});

function getDistrict(zip = '') {
    let city = $('#selectCity').val();

    let url = '/includes/getDistrict.php';
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            city: city
        },
        success: function(res) {
            if (res.status == 200) {
                let data = res.data;
                let district = '<option value="">請選擇</option>';
                $.each(data, function(index, value) {
                    district += '<option value="' + index + '">' + value + '</option>';
                });

                $('#selectDistrict').empty().html(district);

                if (zip) {
                    $('#selectDistrict').val(zip);
                }
            } else {
                alert(res.message);
            }
        },
        error: function(err) {
            console.log(err);
            alert('系統錯誤，請稍後再試');
        }
    });
}

function store() {
    let selectCity = $('#selectCity').val();
    let selectDistrict = $('#selectDistrict').val();
    let address = $('#address').val();

    if (selectCity == '') {
        alert('請選擇縣市');
        $('#selectCity').focus();
        return;
    }

    if (selectDistrict == '') {
        alert('請選擇區域');
        $('#selectDistrict').focus();
        return;
    }

    if (address == '') {
        alert('請輸入地址');
        $('#address').focus();
        return;
    }

    let el = '<input type="hidden" name="save" value="ok">';
    $('form').append(el).submit();
}

function cancel() {
    parent.location.reload();
}
</script>