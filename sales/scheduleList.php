<?php
    require_once dirname(__DIR__) . '/web_addr.php';
    require_once dirname(__DIR__) . '/session_check.php';
    require_once dirname(__DIR__) . '/openadodb.php';

    $years = $_REQUEST['yy'];
    if (! preg_match("/^\d{4}$/uis", $years)) {
        $years = date("Y");
    }

    $months = $_REQUEST['mm'];
    if (! preg_match("/^\d{1,2}$/uis", $months)) {
        $months = str_pad(date("m"), 2, '0', STR_PAD_LEFT);
    }

    $pId = $_REQUEST['pp'];

    $staff = [];
    $sql   = 'SELECT * FROM tPeopleInfo WHERE pDep = "7" AND pJob = "1" AND pId = "' . $pId . '" ORDER BY pId ASC;';
    $rs    = $conn->Execute($sql);

    if ($rs->EOF) {
        echo '<h2>資料錯誤!!</h2>';
        exit;
    }

    $staff = $rs->fields;

    $sql = 'SELECT
                *
            FROM
                tCalendar
            WHERE
                YEAR(cStartDateTime) = "' . $years . '"
                AND MONTH(cStartDateTime) = "' . $months . '"
                AND cCreator = "' . $pId . '"
                AND cErease = "1"
            ORDER BY
                cStartDateTime
            ASC;';
    $rs = $conn->Execute($sql);

    $list = [];
    while (! $rs->EOF) {
        $sub = $rs->fields['cSubject']; //目的：1=例行拜訪、2=開發拜訪、3=案件處理討論、4=其他

        if ($sub == 1) {
            $sub = '例行拜訪';
        } else if ($sub == 2) {
            $sub = '開發拜訪';
        } else if ($sub == 3) {
            $sub = '案件處理討論';
        } else {
            $sub = '其他';
        }

        if ($rs->fields['cClass'] == 1) { //拜訪店家
            $brand   = $rs->fields['cBrand'];
            $catName = $rs->fields['cStore']; //店名

            if (($brand == 2) || empty($brand)) {
                $brand = '';
            } else { //2=非仲介成交
                $sql   = 'SELECT * FROM tBrand WHERE bId = "' . $brand . '";';
                $rel   = $conn->Execute($sql);
                $brand = $rel->fields['bName'];
                if (preg_match("/^自有品牌\(*/isu", $brand)) {
                    $brand = '自有品牌';
                }
            }

            $list[] = [
                'from'    => $rs->fields['cStartDateTime'],
                'to'      => $rs->fields['cEndDateTime'],
                'class'   => '拜訪店家',
                'subject' => $sub,
                'target'  => $brand . '/' . $catName,
                'desc'    => $rs->fields['cDescription'],
            ];
        } else if ($rs->fields['cClass'] == 2) { //拜訪代書
            $list[] = [
                'from'    => $rs->fields['cStartDateTime'],
                'to'      => $rs->fields['cEndDateTime'],
                'class'   => '拜訪代書',
                'subject' => $sub,
                'target'  => $rs->fields['cScrivener'],
                'desc'    => $rs->fields['cDescription'],
            ];
        } else { //其他
            $list[] = [
                'from'    => $rs->fields['cStartDateTime'],
                'to'      => $rs->fields['cEndDateTime'],
                'class'   => '其他',
                'subject' => $sub,
                'target'  => '',
                'desc'    => $rs->fields['cDescription'],
            ];
        }

        $rs->MoveNext();
    }

    $tables = '<tr style="background-color:#F8ECE9;">
                    <th style="width:80px;">&nbsp;</th>
                    <th style="width:80px;">期間</th>
                    <th style="width:80px;">分類</th>
                    <th style="width:80px;">目的</th>
                    <th>對象</th>
                    <th>內容</th>
                </tr>
                ';

    if (! empty($list)) {
        foreach ($list as $k => $v) {
            $fromto = '';
            $fromto .= (int) substr($v['from'], 5, 2);
            $fromto .= '/' . (int) substr($v['from'], 8, 2);
            $fromto .= ' ' . substr($v['from'], 11, 2);
            $fromto .= ':' . substr($v['from'], 14, 2);
            $fromto .= '<br>' . (int) substr($v['to'], 5, 2);
            $fromto .= '/' . (int) substr($v['to'], 8, 2);
            $fromto .= ' ' . substr($v['to'], 11, 2);
            $fromto .= ':' . substr($v['to'], 14, 2);

            if ($k == 0) {
                $tables .= '<tr style="background-color:#FFB6C1;">' . "\n";
                $tables .= '<td style="font-weight:bold;font-size:12pt;">' . $staff['pName'] . '&nbsp;</td>';
            } else {
                $cindex = ($k % 2 == 0) ? '#FFF0F5' : '';
                $tables .= '<tr style="background-color:' . $cindex . '">' . "\n";
                $tables .= '<td>&nbsp;</td>';
            }

            $tables .= '<td style="font-size:10pt;">' . $fromto . '&nbsp;</td>';
            $tables .= '<td>' . $v['class'] . '&nbsp;</td>';
            $tables .= '<td>' . $v['subject'] . '&nbsp;</td>';
            $tables .= '<td>' . $v['target'] . '&nbsp;</td>';
            $tables .= '<td style="width:300px;">' . $v['desc'] . '&nbsp;</td>';
            $tables .= '</tr>' . "\n";
        }
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=9" />

<head>
    <script type="text/javascript" src="/libs/jquery/js/jquery-1.7.1.min.js"></script>
    <style>
    th {
        padding: 10px;
    }

    td {
        padding: 5px;
    }

    table.YourClass tr:hover td {
        font-size: 14pt;
        font-weight: bold;
        background-color: #DAA520;
    }
    </style>
</head>

<body>
    <center>
        <table class="YourClass">
            <?php echo $tables ?>
        </table>
    </center>
</body>

</html>