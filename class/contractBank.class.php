<?php
require_once dirname(__DIR__) . '/class/base.class.php' ;

class contractBank extends Base
{
    //所有合約銀行資料
    public function getContractBanks()
    {
        $sql = '
            SELECT 
                * 
            FROM 
                tContractBank 
            WHERE 
                cShow="1" 
            ORDER BY 
                cOrder 
            ASC;' ;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

}