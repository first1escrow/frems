<?php
/* 銀行相關資料取得 */
require_once dirname(__DIR__) . '/first1DB.php';

//取得銀行總行資料
function getBankMain($_conn, $main = '')
{
    $str = '<option value=""';
    if ($main == '') {
        $str .= ' selected="selected"';
    }
    $str .= '>總行' . "</option>\n";

    $_conn = new first1DB();

    $sql = 'SELECT * FROM tBank WHERE bBank4="" ORDER BY bBank3 ASC;';
    $rs  = $_conn->all($sql);
    if (! empty($rs)) {
        foreach ($rs as $row) {
            $str .= '<option value="' . $row['bBank3'] . '"';
            if ($row['bBank3'] == $main) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $row['bBank3_name'] . '(' . $row['bBank3'] . ')' . "</option>\n";
        }
    }

    return $str;
}
##

//取得銀行分行資料
function getBankBranch($_conn, $main = '', $branch = '')
{
    $str = '<option value=""';
    if ($branch == '') {
        $str .= ' selected="selected"';
    }
    $str .= '>分行' . "</option>\n";

    $_conn = new first1DB();
    $sql   = 'SELECT * FROM tBank WHERE bBank3="' . $main . '" AND bBank4<>"" AND bOK =0 ORDER BY bBank4 ASC;';
    $rs    = $_conn->all($sql);
    if (! empty($rs)) {
        foreach ($rs as $row) {
            $str .= '<option value="' . $row['bBank4'] . '"';
            if ($row['bBank4'] == $branch) {
                $str .= ' selected="selected"';
            }
            $str .= '>' . $row['bBank4_name'] . '(' . $row['bBank4'] . ')' . "</option>\n";
        }
    }

    return $str;

}
##

function getBankBranchName($_conn, $main = '', $branch = '')
{
    if (empty($main) || empty($branch)) {
        return '';
    }

    $_conn = new first1DB();

    $sql = 'SELECT * FROM tBank WHERE bBank3="' . $main . '" AND bBank4<>"" AND bBank4="' . $branch . '" ORDER BY bBank4 ASC;';
    $rs  = $_conn->one($sql);

    if (! empty($rs) && isset($rs['bBank4_name']) && isset($rs['bBank4'])) {
        $str = $rs['bBank4_name'] . '(' . $rs['bBank4'] . ')';
        return $str;
    }

    return '';
}
