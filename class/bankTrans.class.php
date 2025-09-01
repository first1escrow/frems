<?php
require_once dirname(__DIR__) . '/class/base.class.php' ;

class bankTrans extends Base
{
    //合約銀行案件列表
    public function getNotPaidsList($contractBank, $export, $condition, $group)
    {
        $sql = '
            SELECT 
                tId,
                tVR_Code,
                SUM(tMoney) as Total,
                COUNT(tVR_Code) as C, 
                tDate,
                tBank_kind,
                tExport_time,
                tCode2,
                tCode,
                tKind,
                tObjKind,
                tObjKind2,
                (SELECT cEndDate FROM tContractCase WHERE cEscrowBankAccount = tVR_Code) AS endDate,
                tOwner
            FROM 
                tBankTrans 
            WHERE 
                tOK="1" 
                AND tBank_kind="'.$contractBank['cBankName'].'"
                AND tVR_Code LIKE "'.$contractBank['cBankVR'].'%"
                ' .$condition. '
                AND (tExport = "'. $export .'") 
                AND tPayOk = "2" 
            GROUP BY 
                ' .$group. '
            ORDER BY 
                tDate 
            DESC ;
        ' ;;

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

}