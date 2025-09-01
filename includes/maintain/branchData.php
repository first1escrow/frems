<?php
require_once dirname(dirname(__DIR__)).'/first1DB.php';

class branchData
{
    private $conn;

    public function __construct()
    {
        $this->conn = new First1DB;
    }

    //取得仲介店業務資訊
    public function getBranchSales($bId)
    {
        $sql = '
            SELECT
                b.Pid   as id,
                b.pName as name
            FROM
                tBranchSales AS a
            JOIN
                tPeopleInfo AS b ON a.bSales = b.pId
            WHERE
                a.bBranch   = :bId
                AND bSales  > 0
                AND bBranch > 0
            ORDER BY
                bId
            ASC
        ;';

        $data = $this->conn->all($sql, ['bId' => $bId]);
        return empty($data) ? [] : $data;
    }
    ##
}

?>
