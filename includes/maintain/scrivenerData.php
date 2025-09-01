<?php
require_once dirname(dirname(__DIR__)) . '/first1DB.php';

class scrivenerData
{
    private $conn;

    public function __construct()
    {
        $this->conn = new First1DB;
    }

    //取得地政士業務資訊
    public function getScrivenerSales($sId)
    {
        $sql = '
            SELECT
                b.Pid   as id,
                b.pName as name
            FROM
                tScrivenerSales AS a
            JOIN
                tPeopleInfo AS b ON a.sSales = b.pId
            WHERE
                a.sScrivener   = :sId
                AND sSales     > 0
                AND sScrivener > 0
            ORDER BY
                sId
            ASC
        ;';

        $data = $this->conn->all($sql, ['sId' => $sId]);
        return empty($data) ? [] : $data;
    }
    ##

    //取得地政士績效業務資訊
    public function getScrivenerSalesForPerformance($sId)
    {
        $sql = '
            SELECT
                b.pId   as id,
                b.pName as name
            FROM
                tScrivenerSalesForPerformance AS a
            JOIN
                tPeopleInfo AS b ON a.sSales = b.pId
            WHERE
                a.sScrivener   = :sId
                AND a.sSales     > 0
                AND a.sScrivener > 0
            ORDER BY
                a.sId
            ASC
        ;';

        $data = $this->conn->all($sql, ['sId' => $sId]);
        return empty($data) ? [] : $data;
    }
    ##
}
