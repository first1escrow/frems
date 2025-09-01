<?php
require_once dirname(__DIR__) . '/first1DB.php';
require_once dirname(__DIR__) . '/session_check.php';
require_once dirname(__DIR__) . '/class/scrivener.class.php';

$alert = '';

$id = empty($_GET['id']) ? '' : $_GET['id'];

$conn = new first1DB;

$sql = 'SELECT a.* ,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.fSales) AS salesName,
            (SELECT pName FROM tPeopleInfo WHERE pId = a.fSalesConfirmId) AS ConfirmedSalesName
        FROM tFeedBackMoneyPayByCaseLog as a WHERE a.fCertifiedId = "'.$id.'" AND a.fSalesConfirmDate IS NOT NULL AND fStatus <> 2 ORDER BY a.fLogCreated_at DESC';
$rs  = $conn->all($sql);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
    <title>Log</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>
    <script src="/js/1.13.3/combobox.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <style>
    body {
        font-family: '微軟正黑體', sans-serif;
        font-size: 16px;
    }

    .div-css {
        margin-top: 50px;
    }

    .custom-combobox {
        position: relative;
        display: inline-block;
    }

    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }

    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
    }

    /* 表格整體樣式 */
    table {
        border-collapse: collapse;
        width: 100%;
        /*max-width: 800px;*/
        margin: 20px auto;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    /* 表格標題和單元格共用樣式 */
    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    /* 表格標題特殊樣式 */
    th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9em;
        letter-spacing: 0.5px;
    }

    /* 表格內容列樣式 */
    tr:last-child td {
        border-bottom: none;
    }

    /* 滑鼠移過效果 */
    tbody tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.2s ease;
    }

    /* 隔行變色 */
    tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    /* 響應式設計 */
    @media (max-width: 768px) {
        table {
            box-shadow: none;
        }

        th, td {
            padding: 8px 10px;
        }
    }
    </style>
</head>

<body>
    <div class="div-css">
        <div class="ui-widget" style="border: 1px solid #CCC;padding:10px;border-radius:10px;">
            <h3>異動紀錄說明</h3>
            <table cellpadding="10" cellspacing="0" border="0">
                <thead>
                <tr>
                    <th>保證號碼</th>
                    <th>負責業務</th>
                    <th>確認業務</th>
                    <th>確認時間</th>
                    <th>回饋對象</th>
                    <th>異動說明</th>
                    <th>紀錄時間</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($rs as $v) {
                        $scrivener = new Scrivener();
                        $scrivenerInfo = $scrivener->GetScrivenerInfo($v['fTargetId']);
                ?>
                    <tr>
                        <td><?php echo $v['fCertifiedId'];?></td>
                        <td><?php echo $v['salesName'];?></td>
                        <td><?php echo $v['ConfirmedSalesName'];?></td>
                        <td><?php echo $v['fSalesConfirmDate'];?></td>
                        <td><?php echo $scrivenerInfo['sName'];?></td>
                        <td><?php echo $v['fMemo'];?></td>
                        <td><?php echo $v['fLogCreated_at'];?></td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>